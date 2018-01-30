<?php
class Plugg_HyperEstraier_Plugin extends Plugg_Plugin implements Plugg_Search_Engine
{
    public function onSearchEnginePluginOptions($options)
    {
        $options[$this->getName()] = $this->getNicename();
    }

    public function searchEngineGetFeatures()
    {
        return Plugg_Search_Plugin::ENGINE_FEATURE_BOOLEAN |
            Plugg_Search_Plugin::ENGINE_FEATURE_FIND_BY_PLUGINS |
            Plugg_Search_Plugin::ENGINE_FEATURE_ORDER_BY_SCORE |
            Plugg_Search_Plugin::ENGINE_FEATURE_ORDER_BY_DATE;
    }

    private function _init()
    {
        set_include_path(dirname(__FILE__) . '/lib' . PATH_SEPARATOR . get_include_path());
        require_once 'Services/HyperEstraier.php';
    }

    public function searchEngineFind($searchableIds, $keywords, $keywordsType, $keywordsNot, $limit, $offset, $order, $userId)
    {
        $this->_init();

        $condition = $this->_getCondition($keywords, $keywordsType, $keywordsNot, $userId, $searchableIds);
        $condition->setMax($limit);
        $condition->setSkip($offset);
        switch ($order) {
            case Plugg_Search_Plugin::ORDER_DATE_ASC:
                $condition->setOrder('created NUMA');
                break;
            case Plugg_Search_Plugin::ORDER_DATE_DESC:
                $condition->setOrder('created NUMD');
                break;
        }

        return $this->_search($condition, $keywords);
    }

    public function searchEngineCount($searchableIds, $keywords, $keywordsType, $keywordsNot, $userId)
    {
        $this->_init();

        $condition = $this->_getCondition($keywords, $keywordsType, $keywordsNot, $userId, $searchableIds);
        return $this->_count($condition);
    }

    public function searchEngineFindByPlugins($plugins, $keywords, $keywordsType, $keywordsNot, $limit, $offset, $order, $userId)
    {
        $this->_init();

        $condition = $this->_getCondition($keywords, $keywordsType, $keywordsNot, $userId, array(), $plugins);
        $condition->setMax($limit);
        $condition->setSkip($offset);
        switch ($order) {
            case Plugg_Search_Plugin::ORDER_DATE_ASC:
                $condition->setOrder('created NUMA');
                break;
            case Plugg_Search_Plugin::ORDER_DATE_DESC:
                $condition->setOrder('created NUMD');
                break;
        }

        return $this->_search($condition, $keywords);
    }

    public function searchEngineCountByPlugins($plugins, $keywords, $keywordsType, $keywordsNot, $userId)
    {
        $this->_init();

        $condition = $this->_getCondition($keywords, $keywordsType, $keywordsNot, $userId, array(), $plugins);
        return $this->_count($condition);
    }

    private function _search($condition, $keywords)
    {
        $node = $this->_getNode();
        if (!$nres = $node->search($condition, 0)) return array();

        $ret = array();
        foreach ($nres as $rdoc) {
            $ret[] = array(
                'content_id' => $rdoc->getAttribute('content_id'),
                'created' => $rdoc->getAttribute('created'),
                'modified' => $rdoc->getAttribute('modified'),
                'title_html' => $this->_application->getPlugin('search')->highlightKeywords(h($rdoc->getAttribute('@title')), $keywords),
                'author_id' => $rdoc->getAttribute('author_id'),
                'searchable_id' => $rdoc->getAttribute('searchable_id'),
                'score' => $rdoc->getAttribute('#nodescore'),
                'snippet_html' => $this->_htmlizeSnippet($rdoc->getSnippet(), $keywords),
            );
        }
        return $ret;
    }

    private function _count($condition)
    {
        $node = $this->_getNode();
        if (!$nres = $node->search($condition, 0)) {
            return false;
        }

        return $nres->getHint('HIT');
    }

    public function searchEngineListBySearchContentIds($searchableId, $contentIds, $order)
    {
        $this->_init();

        $condition = $this->_createCondition();
        // Fetch documents that match the searhcable id and content ids
        $condition->addAttribute('searchable_id NUMEQ ' . $searchableId);
        sort($contentIds, SORT_NUMERIC);
        $content_id_first = array_shift($contentIds);
        $content_id_last = array_pop($contentIds);
        $condition->addAttribute(sprintf('content_id NUMBT %d %d', $content_id_first, $content_id_last));
        $node = $this->_getNode();
        if (!$nres = $node->search($condition, 0)) return array();

        $ret = array();
        foreach ($nres as $rdoc) {
            $content_id = $rdoc->getAttribute('content_id');
            $ret[$content_id] = $rdoc->getAttribute('@title');
        }
        return $ret;
    }

    public function searchEnginePut($pluginName, $searchableId, $contentId, $title, $bodyHtml, $userId, $created, $modified, $keywords, $contentGroup)
    {
        $this->_init();

        // create a document object
        $doc = new Services_HyperEstraier_Document();

        // add system attributes
        $doc->addAttribute('@uri', $this->_getContentUri($searchableId, $contentId));
        $doc->addAttribute('@title', $title);

        // add custom attributes
        $doc->addAttribute('plugin', $pluginName);
        $doc->addAttribute('content_id', $contentId);
        $doc->addAttribute('created', $created);
        $doc->addAttribute('modified', $modified);
        $doc->addAttribute('author_id', $userId);
        $doc->addAttribute('searchable_id', $searchableId);
        $doc->addAttribute('content_group', $contentGroup);

        // Keywords
        $doc->setKeywords((array)@$keywords);

        // add text
        $doc->addText(strip_tags($bodyHtml));
        $doc->addHiddenText(h($title)); // add title to text but do not display in snippet

        // register the document object to the node
        $node = $this->_getNode(true);
        if (!$node->putDocument($doc)) {
            //$this->_dumpError($node);
            return false;
        }

        return true;
    }

    public function searchEnginePurge($searchableId)
    {
        $this->_init();

        $node = $this->_getNode(true);

        $condition = $this->_createCondition();
        // Fetch documents with searhcable_id $searchableId
        $condition->addAttribute('searchable_id NUMEQ ' . $searchableId);
        if (null === $nres = $node->search($condition, 0)) {
            return false;
        }
        foreach ($nres as $rdoc) {
            $node->outDocumentByUri($rdoc->getUri());
        }

        return true;
    }

    public function searchEnginePurgeContent($searchableId, $contentId)
    {
        $this->_init();

        $node = $this->_getNode(true);
        return $node->outDocumentByUri($this->_getContentUri($searchableId, $contentId));
    }

    public function searchEnginePurgeContentGroup($searchableId, $contentGroup)
    {
        $this->_init();

        $node = $this->_getNode(true);

        $condition = $this->_createCondition();
        // Fetch documents with searhcable_id $searchableId
        $condition->addAttribute('searchable_id NUMEQ ' . $searchableId);
        // Fetch documents with content group name starting from $contentGroup
        $condition->addAttribute('content_group STRBW ' . $contentGroup);
        if (null === $nres = $node->search($condition, 0)) {
            return false;
        }
        foreach ($nres as $rdoc) {
            $node->outDocumentByUri($rdoc->getUri());
        }

        return true;
    }

    public function searchEngineUpdateIndex()
    {
        $this->_init();

        $node = $this->_getNode(true);

        // Optimize index
        $node->optimize();
    }

    private function _getNode($withAuth = false)
    {
        // create and configure the node connecton object
        $node = new Services_HyperEstraier_Node();
        $node->setUrl($this->getParam('nodeServerUrl'));
        $node->setSnippetWidth($this->getParam('snippetWidth'), 96, 96);
        if ($withAuth) {
            $this->loadParams(); // username and password are not cached, so need to load them manually
            $node->setAuth($this->getParam('nodeServerUser'), $this->getParam('nodeServerPassword'));
        }
        return $node;
    }

    private function _createCondition()
    {
        return new Services_HyperEstraier_Condition();
    }

    private function _getCondition($keywords, $keywordsType, $keywordsNot, $userId, $searchIds = array(), $plugins = array())
    {
        $cond = $this->_createCondition();
        $keywords = array_map(array(__CLASS__, 'escapeKeyword'), $keywords);
        $delimiter = Plugg_Search_Plugin::KEYWORDS_OR == $keywordsType ? ' OR ' : ' AND ';
        $phrase = implode($delimiter, $keywords);
        if (!empty($keywordsNot)) {
            $keywordsNot = array_map(array(__CLASS__, 'escapeKeyword'), $keywordsNot);
            $phrase = implode(' ANDNOT ', array_merge(array($phrase), $keywordsNot));
        }
        $cond->setPhrase($phrase);
        if ($user_id = intval($userId)) $cond->addAttribute('author_id NUMEQ ' . $user_id);
        if (!empty($plugins)) $cond->addAttribute('plugin STROREQ ' . implode(' ', $plugins));

        return $cond;
    }

    private function _getContentUri($searchableId, $contentId)
    {
        return implode(':', array($this->_application->getId(), $searchableId, $contentId));
    }

    private function _htmlizeSnippet($snippet, $keywords, $separator = ' ... ')
    {
        $lines = explode("\n", trim($snippet));
        foreach ($lines as $line) {
            if (strpos($line, "\t")) {
                $pair = explode("\t", $line);
                $html[] = $pair[0];
            } else {
                $html[] = $line == '' ? $separator : $line;
            }
        }
        // Always append the separator. There should be a way to know if the snippet is at the end of the doc..
        $html[] = $separator;

        return $this->_application->getPlugin('search')->highlightKeywords(implode('', $html), $keywords);
    }

    public static function escapeKeyword($keyword)
    {
        // Because search contents are htmlized, keywords must be htmlized as well to match contents.
        // Also, a keyword may not be OR/AND/ANDNOT, so convert it to lower case.
        return h(strtolower($keyword));
    }

    private function _dumpError($node)
    {
        echo '<pre>';
        print_r('Status: ' . $node->status);
        if (Services_HyperEstraier_Error::hasErrors()) {
            print_r(Services_HyperEstraier_Error::pop());
        }
        echo '</pre>';
    }
}