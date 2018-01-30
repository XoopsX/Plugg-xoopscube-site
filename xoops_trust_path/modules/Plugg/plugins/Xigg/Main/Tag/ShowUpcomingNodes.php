<?php
class Plugg_Xigg_Main_Tag_ShowUpcomingNodes extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();
        $tag = $this->_application->tag;

        $vars = array();
        $vars['route'] = '/tag/' . rawurlencode($tag->name);
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
        $perpage = $context->plugin->getParam('numberOfNodesOnTop');
        $pages = $model->Node
            ->criteria()
            ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_UPCOMING)
            ->hidden_is(0)
            ->paginateByTag($tag->getId(), $perpage, $sort, $order);
        $vars['vote_allowed'] = false;
        $vars['nodes_voted'] = array();
        $vars['page'] = $pages->getValidPage($context->request->getAsInt('page', 1, null, 0));
        if ($pages->getElementCount() > 0) {
            $vars['nodes'] = $vars['page']->getElements();
            if ($context->user->isAuthenticated()) {
                if ($context->user->hasPermission('xigg vote')) {
                    $vars['vote_allowed'] = true;
                    $vars['nodes_voted'] = $model->Vote->checkByNodesAndUser($vars['nodes']->getAllIds(), $context->user);
                }
            } elseif ($context->plugin->getParam('guestVotesAllowed')) {
                if ($user_ip = getip()) {
                    $vars['vote_allowed'] = true;
                    $vars['nodes_voted'] = $model->Vote->checkByNodesAndUser($vars['nodes']->getAllIds(), null, $user_ip);
                }
            }
        }

        $vars['popular_count'] = $model->Node
            ->criteria()
            ->status_is(Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED)
            ->hidden_is(0)
            ->countByTag($vars['tag']->getId());
        $vars['pages'] = $pages;
        $vars['requested_sort'] = $sort_req;
        $vars['sorts'] = $context->plugin->getParam('useVotingFeature') ?
            array(
                'new'     => $context->plugin->_('Newest first'),
                'old'     => $context->plugin->_('Oldest first'),
                'vote'    => $context->plugin->_('Most voted'),
                'voteup'  => $context->plugin->_('Least voted'),
                'comment' => $context->plugin->_('Most commented')
            ) :
            array(
                'new'     => $context->plugin->_('Newest first'),
                'old'     => $context->plugin->_('Oldest first'),
                'comment' => $context->plugin->_('Most commented')
            );
        $this->_application->setData($vars);
    }
}