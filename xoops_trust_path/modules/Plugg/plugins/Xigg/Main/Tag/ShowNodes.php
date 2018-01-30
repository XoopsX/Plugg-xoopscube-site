<?php
class Plugg_Xigg_Main_Tag_ShowNodes extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $tag = $this->_application->tag;

        $criteria = $model->createCriteria('Node')
            ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED)
            ->hidden_is(0);
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
        $perpage = $context->plugin->getParam('numberOfNodesOnTop');
        $pages = $model->Node->paginateByTagAndCriteria($tag->getId(), $criteria, $perpage, $sort, $order);
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
            'pages'            => $pages,
            'page'             => $page,
            'nodes'            => $nodes,
            'requested_period' => $period,
            'upcoming_count'   => $model->Node
                ->criteria()
                ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING)
                ->hidden_is(0)
                ->countByTag($tag->getId()),
            'route'            => '/tag/' . rawurlencode($tag->name),
            'vote_allowed'     => $vote_allowed,
            'nodes_voted'      => $nodes_voted,
            'node_lastviews'   => $node_lastviews,
            'sorts'            => $context->plugin->getParam('useVotingFeature') ?
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