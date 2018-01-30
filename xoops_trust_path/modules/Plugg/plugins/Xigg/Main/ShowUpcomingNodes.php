<?php
class Plugg_Xigg_Main_ShowUpcomingNodes extends Sabai_Application_Controller
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
        $criteria = $model->createCriteria('Node')
            ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING)
            ->hidden_is(0);
        $criteria2 = $model->createCriteria('Node')
            ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED)
            ->hidden_is(0);

        $user_req = null;
        if ($user_id = $context->request->getAsInt('user_id')) {
            if ($users = $model->fetchUserIdentities(array($user_id))) {
                $user_req = $users[$user_id];
                $criteria->userid_is($user_id);
                $criteria2->userid_is($user_id);
            }
        }

        if ($keyword_req = $context->request->getAsStr('keyword', '')) {
            // need to convert encoding since AJAX with Form.serialize() comes with UTF-8
            if (SABAI_CHARSET != 'UTF-8') {
                $keyword_req = mb_convert_encoding($keyword_req, SABAI_CHARSET, array(SABAI_CHARSET, 'UTF-8'));
            }
            $keyword_req = trim(preg_replace(array('/\s\s+/'), array(' '), str_replace($context->plugin->_(' '), ' ', $keyword_req)));
        }
        $sort_req = $context->request->getAsStr('sort');
        switch ($sort_req) {
            case 'vote':
                $sort = array('node_vote_count', 'node_created');
                $order = array('DESC', 'DESC');
                break;
            case 'voteup':
                $sort = array('node_vote_count', 'node_created');
                $order = array('ASC', 'DESC');
                break;
            case 'comment':
                $sort = array('node_comment_count', 'node_created');
                $order = array('DESC', 'DESC');
                break;
            case 'old':
                $sort = 'node_created';
                $order = 'ASC';
                break;
            case 'new':
            default:
                $sort_req = 'new';
                $sort = 'node_created';
                $order = 'DESC';
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
                $popular_count = $model->Node->countByCriteriaKeywordAndCategory($criteria2, $keyword_req, $cat_ids);
                $context->response->setPageInfo(sprintf($context->plugin->_('Search: %s'), $keyword_req));
            } else {
                $pages = $model->Node->paginateByCategoryAndCriteria($cat_ids, $criteria, $perpage, $sort, $order);
                $popular_count = $model->Node->countByCategoryAndCriteria($cat_ids, $criteria2);
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
                $popular_count = $model->Node->countByCriteriaKeywordAndCategory($criteria2, $keyword_req, null);
                $context->response->setPageInfo(sprintf($context->plugin->_('Search: %s'), $keyword_req));
            } else {
                $pages = $model->Node->paginateByCriteria($criteria, $perpage, $sort, $order);
                $popular_count = $model->Node->countByCriteria($criteria2);
            }
        }
        $page = $pages->getValidPage($context->request->getAsInt('page', 1, null, 0));
        $nodes = null;
        $vote_allowed = false;
        $nodes_voted = array();
        if ($pages->getElementCount() > 0) {
            $nodes = $page->getElements();
            if ($context->user->isAuthenticated()) {
                if ($context->user->hasPermission('xigg vote')) {
                    $vote_allowed = true;
                    $nodes_voted = $model->Vote->checkByNodesAndUser($nodes->getAllIds(), $context->user);
                }
            } elseif ($context->plugin->getParam('guestVotesAllowed')) {
                if ($user_ip = getip()) {
                    $vote_allowed = true;
                    $nodes_voted = $model->Vote->checkByNodesAndUser($nodes->getAllIds(), null, $user_ip);
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
            'requested_sort'     => $sort_req,
            'popular_count'      => $popular_count,
            'vote_allowed'       => $vote_allowed,
            'nodes_voted'        => $nodes_voted,
            'categories'         => $categories,
            'category_list'      => $category_options,
            'sorts'              => $context->plugin->getParam('useVotingFeature') ?
                array(
                    'new'     => $context->plugin->_('Newest first'),
                    'old'     => $context->plugin->_('Oldest first'),
                    'vote'    => $context->plugin->_('Most voted'),
                    'voteup'  => $context->plugin->_('Least voted'),
                    'comment' => $context->plugin->_('Most commented')) :
                array(
                    'new'     => $context->plugin->_('Newest first'),
                    'old'     => $context->plugin->_('Oldest first'),
                    'comment' => $context->plugin->_('Most commented')
                )
            )
        );
    }
}