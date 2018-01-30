<?php
class Plugg_Xigg_Main_ShowNodes extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $category_options = $categories = array();
        $category_options[0] = $context->plugin->_('All News');
        foreach ($model->Category->fetchAsTree() as $category) {
            $category_options[$category->getId()] = str_repeat('--', $category->parentsCount() + 1) . $category->name;
            $categories[$category->getId()] = $category;
        }
        $criteria = $model->createCriteria('Node');
        $criteria->hidden_is(0);

        $user_req = null;
        if ($user_id = $context->request->getAsInt('user_id')) {
            if ($users = $model->fetchUserIdentities(array($user_id))) {
                $user_req = $users[$user_id];
                $criteria->userid_is($user_id);
            }
        }

        if ($keyword_req = $context->request->getAsStr('keyword', '')) {
            // need to convert encoding since AJAX with Form.serialize() comes with UTF-8
            if (SABAI_CHARSET != 'UTF-8') {
                $keyword_req = mb_convert_encoding($keyword_req, SABAI_CHARSET, array(SABAI_CHARSET, 'UTF-8'));
            }
            $keyword_req = trim(preg_replace(array('/\s\s+/'), array(' '), str_replace($context->plugin->_(' '), ' ', $keyword_req)));
        }

        $criteria2 = clone $criteria;
        $criteria->status_is(Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED);
        $criteria2->status_is(Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING);

        $sort = array('node_vote_count', 'node_published');
        $order = array('DESC', 'DESC');
        $period = $context->request->getAsStr('period', $context->plugin->getParam('defaultNodesPeriod'));
        switch ($period) {
            case 'all':
                break;
            case 'day':
                $criteria->published_isOrGreaterThan(time() - 86400);
                break;
            case 'week':
                $criteria->published_isOrGreaterThan(time() - 604800);
                break;
            case 'month':
                $criteria->published_isOrGreaterThan(time() - 2592000);
                break;
            case 'comments':
                $sort = array('node_comment_last', 'node_published');
                $order = array('DESC', 'DESC');
                break;
            case 'active':
                $sort = array('node_comment_lasttime', 'node_published');
                $order = array('DESC', 'DESC');
                break;
            default:
                $sort = array('node_priority', 'node_published');
                $order = array('DESC', 'DESC');
                $period = 'new';
                break;
        }
        $requested_category = null;
        $perpage = $context->plugin->getParam('numberOfNodesOnTop');
        if (($category_id = $context->request->getAsInt('category_id')) &&
            ($requested_category = $model->Category->fetchById($category_id))
        ) {
            $this->_application->requested_category = $requested_category;
            $descendants = $requested_category->descendants();
            $cat_ids = array_merge(array($category_id), $descendants->getAllIds());
            if (!empty($keyword_req)) {
                $pages = $model->Node->paginateByCriteriaKeywordAndCategory($criteria, $keyword_req, $cat_ids, $perpage, $sort, $order);
                $upcoming_count = $model->Node->countByCriteriaKeywordAndCategory($criteria2, $keyword_req, $cat_ids);
                $context->response->setPageInfo(sprintf($context->plugin->_('Search: %s'), $keyword_req));
            } else {
                $pages = $model->Node->paginateByCategoryAndCriteria($cat_ids, $criteria, $perpage, $sort, $order);
                $upcoming_count = $model->Node->countByCategoryAndCriteria($cat_ids, $criteria2);
            }

            // Set page info for all parent categories
            $parent_ids = array($category_id);
            $parent_id = $requested_category->getParentId();
            while (isset($categories[$parent_id])) {
                $parent_ids[] = $parent_id;
                $parent_id = $categories[$parent_id]->getParentId();
            }
            foreach (array_reverse($parent_ids) as $parent_id) {
                $context->response->setPageInfo(
                    $categories[$parent_id]->name,
                    array('params' => array('category_id' => $parent_id))
                );
            }

        } else {
            if (!empty($keyword_req)) {
                $pages = $model->Node->paginateByCriteriaKeywordAndCategory($criteria, $keyword_req, null, $perpage, $sort, $order);
                $upcoming_count = $model->Node->countByCriteriaKeywordAndCategory($criteria2, $keyword_req, null);
                $context->response->setPageInfo(sprintf($context->plugin->_('Search: %s'), $keyword_req));
            } else {
                $pages = $model->Node->paginateByCriteria($criteria, $perpage, $sort, $order);
                $upcoming_count = $model->Node->countByCriteria($criteria2);
            }
        }

        $page = $pages->getValidPage($context->request->getAsInt('page', 1, null, 0));
        $nodes = null;
        $vote_allowed = false;
        $nodes_voted = $node_lastviews = array();
        if ($pages->getElementCount() > 0) {
            $nodes = $page->getElements();
            $node_ids = $nodes->getAllIds();
            if ($context->user->isAuthenticated()) {
                if ($context->user->hasPermission('xigg vote')) {
                    $vote_allowed = true;
                    $nodes_voted = $model->Vote->checkByNodesAndUser($node_ids, $context->user);
                }
                $node_lastviews = $model->View->checkByNodesAndUser($node_ids, $context->user);
            } elseif ($context->plugin->getParam('guestVotesAllowed')) {
                if ($user_ip = getip()) {
                    $vote_allowed = true;
                    $nodes_voted = $model->Vote->checkByNodesAndUser($node_ids, null, $user_ip);
                }
            }
        }
        $this->_application->setData(array(
            'requested_category' => $requested_category,
            'requested_user'     => $user_req,
            'requested_user_id'  => $user_req ? $user_req->getId() : '',
            'requested_keyword'  => $keyword_req,
            'pages'              => $pages,
            'page'               => $page,
            'nodes'              => $nodes,
            'requested_period'   => $period,
            'upcoming_count'     => $upcoming_count,
            'vote_allowed'       => $vote_allowed,
            'nodes_voted'        => $nodes_voted,
            'node_lastviews'     => $node_lastviews,
            'categories'         => $categories,
            'category_list'      => $category_options,
            'sorts'              => $context->plugin->getParam('useVotingFeature') ?
                array(
                    'new'      => $context->plugin->_('Newly popular'),
                    'comments' => $context->plugin->_('Newly commented'),
                    'active'   => $context->plugin->_('Last active'),
                    'day'      => $context->plugin->_('Top in 24 hours'),
                    'week'     => $context->plugin->_('Top in 7 days'),
                    'month'    => $context->plugin->_('Top in 30 days'),
                    'all'      => $context->plugin->_('Top in all period')
                ) :
                array(
                    'new'      => $context->plugin->_('Date posted'),
                    'comments' => $context->plugin->_('Date commented'),
                    'active'   => $context->plugin->_('Last active')
                )
            )
        );
    }
}