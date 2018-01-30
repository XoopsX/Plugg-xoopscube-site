<?php
class Plugg_Xigg_Model_Node extends Plugg_Xigg_Model_Base_Node
{
    function isPublished()
    {
        return $this->get('status') == Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED;
    }

    function publish($time = null)
    {
        $this->set('status', Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED);
        $this->set('published', isset($time) ? $time : time());
    }

    function hide()
    {
        $this->set('hidden', 1);
    }

    function unhide()
    {
        $this->set('hidden', 0);
    }

    function isHidden()
    {
        return (bool)$this->get('hidden');
    }

    function isReadable($user)
    {
        if ($this->isHidden() && !$user->hasPermission('xigg view hidden')) {
            return false;
        }
        return true;
    }

    function paginateCommentsByParentComment($parentCommentId = 'NULL', $perpage = 10, $sort = null, $order = null)
    {
        return $this->_model->Comment
            ->criteria()
            ->parent_is($parentCommentId)
            ->paginateByNode($this->getId(), $perpage, $sort, $order);
    }

    function getSourceHTMLLink($length = 100, $target = '_blank')
    {
        if ($source = $this->get('source')) {
            if ($source_title = $this->get('source_title')) {
                return sprintf('<a href="%s" target="%s">%s</a>', h($source), h($target), h(mb_strimlength($source_title, 0, $length)));
            }
            return sprintf('<a href="%s" target="%s">%s</a>', h($source), h($target), h(mb_strimlength($source, 0, $length)));
        }
        return '';
    }

    function getScreenshot()
    {
        return sprintf('<img src="http://mozshot.nemui.org/shot?img_x=120:img_y=120;effect=true;uri=%1$s" width="120" height="120" alt="%1$s" />', urlencode($this->get('source')));
    }

    function linkTagsByStr($tagsStr)
    {
        $tag_names = array();
        foreach (explode(',', $tagsStr) as $tag_name) {
            // convert encoding specific chars and then trim
            $tag_name = trim(mb_convert_kana($tag_name, 'as', SABAI_CHARSET));
            // remove redundant spaces and invalid characters
            $tag_name = preg_replace(array('/\s\s+/', "[\r\n\t]", '/[\/~]/'), array(' ', ' ', ''), $tag_name);
            if (!empty($tag_name)) {
                $tag_names[strtolower($tag_name)] = $tag_name;
            }
        }
        $tags_existing = $this->_model->Tag->getExistingTags(array_keys($tag_names));
        foreach ($tags_existing as $tag_existing) {
            $tag_existing->linkNode($this);
            unset($tag_names[strtolower($tag_existing->name)]);
        }
        foreach ($tag_names as $tag_name) {
            $tag = $this->_model->create('Tag');
            $tag->set('name', $tag_name);
            $tag->linkNode($this);
        }
        return $this->_model->commit();
    }
}

class Plugg_Xigg_Model_NodeRepository extends Plugg_Xigg_Model_Base_NodeRepository
{
    function paginateByCriteriaKeywordAndCategory($criteria, $keyword, $categoryId, $perpage, $sort = null, $order = null)
    {
        require_once 'Sabai/Page/Collection/Custom.php';
        return new Sabai_Page_Collection_Custom(
            array($this, 'countByCriteriaKeywordAndCategory'),
            array($this, 'fetchByCriteriaKeywordAndCategory'),
            $perpage,
            array($criteria, $keyword, $categoryId, $sort, $order)
        );
    }

    function fetchByCriteriaKeywordAndCategory($limit, $offset, $criteria, $keywords, $categoryId, $sort = null, $order = null)
    {
        $criterion = $this->_createKeywordAndCategoryCriteria($criteria, $keywords, $categoryId);
        $gateway = $this->_model->getGateway($this->getName());
        return $this->_getCollection($gateway->selectByCriteriaWithComment($criterion, array(), $limit, $offset, $sort, $order));
    }

    function countByCriteriaKeywordAndCategory($criteria, $keywords, $categoryId)
    {
        $criterion = $this->_createKeywordAndCategoryCriteria($criteria, $keywords, $categoryId);
        $gateway = $this->_model->getGateway($this->getName());
        return $gateway->countByCriteriaWithComment($criterion);
    }

    function _createKeywordAndCategoryCriteria($criteria, $keywords, $categoryId)
    {
        $criterion = Sabai_Model_Criteria::createComposite(array($criteria));
        if (!empty($categoryId)) {
            if (is_array($categoryId)) {
                $criterion->addAnd(Sabai_Model_Criteria::createIn('node_category_id', $categoryId));
            } else {
                $criterion->addAnd(Sabai_Model_Criteria::createValue('node_category_id', $categoryId));
            }
        }
        foreach (explode(' ', $keywords) as $keyword) {
            $keyword_criteria = Sabai_Model_Criteria::createComposite(array(Sabai_Model_Criteria::createString('node_teaser_html', $keyword)));
            $keyword_criteria->addOr(Sabai_Model_Criteria::createString('node_body_html', $keyword))
                ->addOr(Sabai_Model_Criteria::createString('node_title', $keyword))
                ->addOr(Sabai_Model_Criteria::createString('comment_title', $keyword))
                ->addOr(Sabai_Model_Criteria::createString('comment_body_html', $keyword));
            $criterion->addAnd($keyword_criteria);
            unset($keyword_criteria);
        }
        return $criterion;
    }
}