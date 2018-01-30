<?php
class Plugg_Search_Plugin extends Plugg_Plugin
{
    const ENGINE_FEATURE_BOOLEAN_AND = 1;
    const ENGINE_FEATURE_BOOLEAN_OR = 2;
    const ENGINE_FEATURE_BOOLEAN_NOT = 4;
    const ENGINE_FEATURE_BOOLEAN = 7;
    const ENGINE_FEATURE_FIND_BY_SEARCHABLES = 8;
    const ENGINE_FEATURE_FIND_BY_PLUGINS = 16;
    const ENGINE_FEATURE_ORDER_BY_SCORE = 32;
    const ENGINE_FEATURE_ORDER_BY_DATE_ASC = 64;
    const ENGINE_FEATURE_ORDER_BY_DATE_DESC = 128;
    const ENGINE_FEATURE_ORDER_BY_DATE = 192;

    const ORDER_DATE_ASC = 1;
    const ORDER_DATE_DESC = 2;
    const ORDER_SCORE = 3;

    const KEYWORDS_OR = 1;
    const KEYWORDS_AND = 2;

    private $_searchables;

    public function onPluggMainRoutes($routes)
    {
        $this->_onPluggMainRoutes($routes);
    }

    public function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }

    public function onPluggCron($lastrun)
    {
        // Allow run this cron 1 time per day at most
        if (!empty($lastrun) && time() - $lastrun < 86400) return;

        $engine = $this->getEnginePlugin();
        if ($this->getParam('rebuildSearchEngine')) {
            $searchables = $this->getActiveSearchables();
            foreach (array_keys($searchables) as $id) {
                $plugin_name = $searchables[$id]['plugin'];
                $searchable = $this->_application->getPlugin($plugin_name);
                $count_func = array($searchable, 'searchCountContents');
                $fetch_func = array($searchable, 'searchFetchContents');
                require_once 'Sabai/Page/Collection/Custom.php';
                $pages = new Sabai_Page_Collection_Custom($count_func, $fetch_func, 500, array(), array($searchables[$id]['name']));
                foreach ($pages as $page) {
                    foreach ($page->getElements() as $content) {
                        $engine->searchEnginePut(
                            $plugin_name,
                            $id,
                            $content['id'],
                            $content['title'],
                            $content['body'],
                            $content['user_id'],
                            $content['created'],
                            $content['modified'],
                            $content['keywords'],
                            $content['group']
                        );
                    }
                }
            }
            $this->updateParam('rebuildSearchEngine', false);
        } else {
            $engine->searchEngineUpdateIndex();
        }
    }

    public function getEnginePlugin()
    {
        if (!$engine_name = $this->getParam('searchEnginePlugin')) {
            throw new Plugg_Exception('Search engine plugin must be defined');
        }

        return $this->_application->getPlugin($engine_name);
    }

    public function onSearchSearchableInstalled($pluginEntity, $plugin)
    {
        if ($searches = $plugin->searchGetNames()) {
            $this->_createSearchables($pluginEntity->name, $searches);
        }
    }

    public function onSearchSearchableUninstalled($pluginEntity, $plugin)
    {
        if ($deleted = $this->_deleteSearchables($pluginEntity->name)) {
            $this->_purge($deleted);
        }
    }

    public function onSearchSearchableUpgraded($pluginEntity, $plugin)
    {
        $deleted = array();

        // Update searches if any
        if (!$searches = $plugin->searchGetNames()) {
            $deleted = $this->_deleteSearchables($pluginEntity->name);
        } else {
            $model = $this->getModel();
            $searches_already_installed = array();
            foreach ($model->Searchable->criteria()->plugin_is($pluginEntity->name)->fetch() as $current_search) {
                if (in_array($current_search->name, $searches)) {
                    $searches_already_installed[] = $current_search->name;
                } else {
                    $current_search->markRemoved();
                    $deleted[] = $current_search->getId();
                }
            }
            if (!$model->commit()) {
                return;
            }
            $this->_createSearchables($plugin_name, array_diff($searches, $searches_already_installed));
        }

        // Purge the search engine contents
        if (!empty($deleted)) {
            $this->_purge($deleted);
        }
    }

    public function _purge($searchableIds)
    {
        if ($engine = $this->getEnginePlugin()) {
            foreach ($searchableIds as $searchable_id) {
                $engine->searchEnginePurge($searchable_id);
            }
        }
    }

    private function _createSearchables($pluginName, $searchables)
    {
        $model = $this->getModel();
        foreach ($searchables as $search_name => $search_title) {
            if (empty($search_name)) continue;
            $search = $model->create('Searchable');
            $search->name = $search_name;
            $search->title = $search_title;
            $search->plugin = $pluginName;
            $search->default = 1;
            $search->markNew();
        }
        return $model->commit();
    }

    public function createOrUpdateSearchable($pluginName, $searchName, $searchTitle)
    {
        $model = $this->getModel();
        $searches = $model->Searchable
            ->criteria()
            ->plugin_is($pluginName)
            ->name_is($searchName)
            ->fetch();
        if ($searches->count() > 0) {
            $search = $searches->getNext();
        } else {
            $search = $model->create('Searchable');
            $search->plugin = $pluginName;
            $search->name = $searchName;
            $search->default = 1;
            $search->markNew();
        }
        $search->title = $searchTitle;
        return $model->commit();
    }

    private function _deleteSearchables($pluginName, $searchableName = null)
    {
        $ids = array();
        $model = $this->getModel();
        $criteria = $model->createCriteria('Searchable')->plugin_is($pluginName);
        if (isset($searchableName)) $criteria->name_is($searchableName);
        foreach ($model->Searchable->fetchByCriteria($criteria) as $entity) {
            $entity->markRemoved();
            $ids[] = $entity->getId();
        }
        if (!$model->commit()) {
            return false;
        }
        return $ids;
    }

    public function deleteSearchable($pluginName, $searchableName)
    {
        if ($ids = $this->_deleteSearchables($pluginName, $searchableName)) {
            $this->_purge($ids);
        }
    }

    public function createSnippet($text, $keywords, $length = 255)
    {
        if ($length >= $text_len = strlen($text)) return $text;

        $regex = implode('|', array_map('preg_quote', $keywords));
        if (!preg_match('/' . $regex . '/i', $text, $matches, PREG_OFFSET_CAPTURE)) {
            return mb_strimlength($text, $start, $length);
        }

        list($matched, $matched_pos) = $matches[0];
        if (0 >= $start = $matched_pos - intval($length/2)) {
            return mb_strimlength($text, 0, $length);
        }

        $length = $length - 3; // subtract the prefix part: "..."
        if ($start + $length > $text_len) {
            $start = $text_len - $length;
        }
        return '...' . mb_strimlength($text, $start, $length);
    }

    public function highlightKeywords($text, $keywords, $tag = 'strong', $class = '')
    {
        $regex = implode('|', array_map('preg_quote', $keywords));
        $replacement = '<' . $tag . ' class="' . $class . '">$1</' . $tag . '>';
        return preg_replace('/(' . $regex . ')/i', $replacement, $text);
    }

    private function _getPluginSearchables($pluginName, $searchableName = null)
    {
        $model = $this->getModel();
        $criteria = $model->createCriteria('Searchable')->plugin_is($pluginName);
        if (isset($searchableName)) $criteria->name_is($searchableName);
        return $model->Searchable->fetchByCriteria($criteria);
    }

    /**
     * Registers a searchable content to the search engine
     *
     * @param string $pluginName
     * @param string $searchableName
     * @param int $contentId
     * @param string $title
     * @param string $content
     * @param int $userId
     * @param int $ctime
     * @param int $mtime
     * @param array $keywords
     * @param string $contentGroup
     * @return mixed 0 if no active searchable content or no search engine registered, true if success, and false otherwise
     */
    public function putContent($pluginName, $searchableName, $contentId, $title, $content, $userId, $ctime, $mtime, $keywords = array(), $contentGroup = '')
    {
        if (!$engine = $this->getEnginePlugin()) {
            return 0;
        }

        if (!$searchable = $this->_getPluginSearchables($pluginName, $searchableName)->getNext()) return 0;

        return $engine->searchEnginePut($pluginName, $searchable->getId(), $contentId, $title, $content, $userId, $ctime, $mtime, $keywords, $contentGroup);
    }

    /**
     * Removes a registered content from the search engine
     *
     * @param string $pluginName
     * @param string $searchableName
     * @param int $contentId
     * @param string $contentGroup
     * @return mixed 0 if no active searchable content or no search engine registered, true if success, and false otherwise
     */
    public function purgeContent($pluginName, $searchableName, $contentId)
    {
        // content id cannot be empty
        if (empty($contentId)) return 0;

        if (!$engine = $this->getEnginePlugin()) {
            return 0;
        }

        if (!$searchable = $this->_getPluginSearchables($pluginName, $searchableName)->getNext()) return 0;

        return $engine->searchEnginePurgeContent($searchable->getId(), $contentId);
    }

    /**
     * Removes contents by content group
     *
     * @param string $pluginName
     * @param string $contentGroup
     * @param string $searchableName
     * @return mixed 0 if no active searchable content or no search engine registered, true if success, and false otherwise
     */
    public function purgeContentGroup($pluginName, $contentGroup, $searchableName = null)
    {
        // content group cannot be empty
        if (empty($contentGroup)) return 0;

        if (!$engine = $this->getEnginePlugin()) {
            return 0;
        }

        $searchables = $this->_getPluginSearchables($pluginName, $searchableName);
        if ($searchables->count() == 0) return 0;

        foreach ($searchables as $searchable) {
            $engine->searchEnginePurgeContentGroup($searchable->getId(), $contentGroup);
        }
        return true;
    }

    public function getSearchables()
    {
        if (!isset($this->_searchables)) {
            $this->_searchables = $this->getModel()->Searchable
                ->fetch(0, 0, array('searchable_order', 'searchable_plugin'));
        }
        return $this->_searchables;
    }

    public function getActiveSearchables()
    {
        $ret = array();
        foreach ($this->getSearchables() as $searchable) {
            if ($plugin = $this->_application->getPlugin($searchable->plugin)) {
                $ret[$searchable->getId()] = array(
                    'name' => $searchable->name,
                    'title' => sprintf($searchable->title, $plugin->getNicename()),
                    'default' => $searchable->default,
                    'plugin' => $searchable->plugin
                );
            }
        }
        return $ret;
    }

    public function getActiveSearchablePlugins()
    {
        $ret = array();
        foreach ($this->getSearchables() as $searchable) {
            if ($plugin = $this->_application->getPlugin($searchable->plugin)) {
                $ret[$plugin->getName()] = $plugin->getNicename();
            }
        }
        return $ret;
    }

    public function getForm(array $plugins = array())
    {
        if (!$engine = $this->getEnginePlugin()) return;

        $features = $engine->searchEngineGetFeatures();

        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm('', 'get', $this->_application->createUrl(array(
            'base' => '/search',
            'path' => '/',
            'fragment' => 'plugg-search-results')), '', null, false);
        $defaults = array();

        // Keyword field
        $keywords[] = $form->createElement('text', 'keyword', array($this->_('Find contents that have...')), array('size' => 60));

        // Keyword type options
        $keywords_type_options = array();
        if ($features & self::ENGINE_FEATURE_BOOLEAN_AND) {
            $keywords_type_options[self::KEYWORDS_AND] = $this->_('all of the above words');
        }
        if ($features & self::ENGINE_FEATURE_BOOLEAN_OR) {
            $keywords_type_options[self::KEYWORDS_OR] = $this->_('one of the above words');
        }
        if (!empty($keywords_type_options)) {
            $keywords_type = $form->createElement('altselect', 'keyword_type', null, $keywords_type_options);
            $keywords_type->setDelimiter('&nbsp;');
            $defaults['keyword_type'] = array_shift(array_keys($keywords_type_options));
            $keywords[] = $keywords_type;
        }

        // Keyword NOT field
        if ($features & self::ENGINE_FEATURE_BOOLEAN_NOT) {
            $keywords[] = $form->createElement('text', 'keyword_not', $this->_("But don't show contents that have any of these unwanted words:"), array('size' => 60));
        } else {
            $form->createElement('hidden', 'keyword_not');
        }

        $form->addGroup($keywords, 'keywords', array($this->_('Keywords:')), '', false);

        if (($features & self::ENGINE_FEATURE_FIND_BY_PLUGINS) &&
            ($searchable_plugins = $this->getActiveSearchablePlugins())
        ) {
            // Searchable content selection
            $searchable_options = array();
            foreach (array_keys($searchable_plugins) as $plugin_name) {
                $searchable_options[$plugin_name] = h($searchable_plugins[$plugin_name]);
            }
            if (!empty($searchable_options)) {
                $searchable_select = $form->addElement('altselect', 'p', $this->_('Only of the type(s):'), $searchable_options);
                $searchable_select->setDelimiter('&nbsp;');
                $searchable_select->setMultiple(true);
                if (!empty($plugins)) {
                    $searchable_select->setSelected($plugins);
                } else {
                    $searchable_select->setSelected(array_keys($searchable_options));
                }
            }
        } elseif (($features & self::ENGINE_FEATURE_FIND_BY_SEARCHABLES) &&
            ($searchables = $this->getActiveSearchables())
        ) {
            // Searchable content selection
            $searchable_defaults = $searchable_options = array();
            foreach (array_keys($searchables) as $searchable_id) {
                $searchable = $searchables[$searchable_id];
                $searchable_options[$searchable_id] = h($searchable['title']);
                if (!empty($plugins)) {
                    if (in_array($searchable['plugin'], $plugins)) {
                        $searchable_defaults[] = $searchable_id;
                    }
                } elseif (!empty($searchable['default'])) {
                    $searchable_defaults[] = $searchable_id;
                }
            }
            if (!empty($searchable_options)) {
                $searchable_select = $form->addElement('altselect', 's', $this->_('Only of the type(s):'), $searchable_options);
                $searchable_select->setDelimiter('&nbsp;');
                $searchable_select->setMultiple(true);
                $searchable_select->setSelected($searchable_defaults);
            }
        }

        // Order options
        $search_order_options = array();
        if ($features & self::ENGINE_FEATURE_ORDER_BY_SCORE) {
            $search_order_options[self::ORDER_SCORE] = $this->_('Score');
        }
        if ($features & self::ENGINE_FEATURE_ORDER_BY_DATE_DESC) {
            $search_order_options[self::ORDER_DATE_DESC] = $this->_('Newest first');
        }
        if ($features & self::ENGINE_FEATURE_ORDER_BY_DATE_ASC) {
            $search_order_options[self::ORDER_DATE_ASC] = $this->_('Oldest first');
        }
        if (!empty($search_order_options)) {
            $search_order = $form->addElement('altselect', 'order', $this->_('Order results by:'), $search_order_options);
            $search_order->setDelimiter('&nbsp;');
            $defaults['order'] = array_shift(array_keys($search_order_options));
        } else {
            $form->addElement('hidden', 'order');
        }

        $form->addSubmitButtons($this->_('Search'));
        $form->addElement('t', time());
        $form->addElement('hidden', $this->_application->getUrl()->getRouteParam());
        $form->setConstants(array($this->_application->getUrl()->getRouteParam() => '/' . $this->_name));
        $form->setDefaults($defaults);

        // Rules
        $form->addFormRule(array($this, 'validateForm'));
        return $form;
    }

    public function validateForm($values, $files)
    {
        if (empty($values['keyword'])) {
            $error['keywords']['keyword'] = $this->_('Please enter keywords to search for');
        } else {
            $keywords = trim(mb_convert_kana($values['keyword'], 'as', SABAI_CHARSET));
            if (strlen($keywords) == 0) {
                $error['keywords']['keyword'] = $this->_('Please enter keywords to search for');
            }
        }
        return empty($error) ? true : $error;
    }

    public function getMiniForm($pluginName = null)
    {
        // Do not diaplay on the search page
        if ($pluginName == $this->_name) return;

        // Create plugin select options if the search engine supports the find by plugin feature
        if ($this->getEnginePlugin()->searchEngineGetFeatures() & self::ENGINE_FEATURE_FIND_BY_PLUGINS) {
            $options = array(sprintf('<option value="">%s</option>', $this->_('Everything')));
            foreach ($this->getSearchables() as $searchable) {
                if ($plugin = $this->_application->getPlugin($searchable->plugin)) {
                    $plugin_name = $plugin->getName();
                    if (isset($options[$plugin_name])) continue;
                    if (isset($pluginName) && $plugin_name == $pluginName) {
                        $options[$plugin_name] = sprintf('<option value="%s" selected="selected">%s</option>', h($plugin_name), h($plugin->getNicename()));
                    } else {
                        $options[$plugin_name] = sprintf('<option value="%s">%s</option>', h($plugin_name), h($plugin->getNicename()));
                    }
                }
            }
            $select = sprintf('<select name="p">%s</select>', implode("\n", $options));
        } else {
            $select = '';
        }

        return sprintf(
            '<form action="%s" method="get">
  <input type="text" name="keyword" />
  %s
  <input type="submit" value="%s" />
  <input type="hidden" name="%s" value="/search" />
</form>',
            $this->_application->createUrl(array(
                'base' => '/search',
                'fragment' => 'plugg-search-results'
            )),
            $select,
            $this->_('Search'),
            $this->_application->getUrl()->getRouteParam()
        );
    }
}