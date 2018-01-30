<?php
class Plugg_Project_Main_ViewLinks extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();

        $view_req = $context->request->getAsStr('link_view');
        switch ($view_req) {
            case 'oldest':
                $sort = array('link_created');
                $order = array('ASC');
                break;
            case 'newest':
                $sort = array('link_created');
                $order = array('DESC');
                break;
            default:
                $sort = array('link_score', 'link_linkvote_lasttime');
                $order = array('DESC', 'DESC');
                $view_req = 'rating';
                break;
        }

        $link_type = $context->request->getAsStr('link_type');
        $link_types = $context->plugin->getLinkTypes();
        $perpage = $context->plugin->getParam('numberOfLinksOnTopPage');
        $criteria = $model->createCriteria('Link')->status_is(Plugg_Project_Plugin::LINK_STATUS_APPROVED);
        if (!empty($link_type) && isset($link_types[$link_type])) {
            $criteria->type_is($link_type);
        } else {
            $link_type = '';
        }
        $pages = $model->Link->paginateByCriteria($criteria, $perpage, $sort, $order);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));
        $links = $page->getElements()->with('Project');

        $links_voted = array();
        $link_vote_allowed = false;
        if ($links->count() > 0) {
            if ($context->plugin->getParam('guestLinkvotesAllowed')) {
                $user_ip = getip();
                if ($context->user->isAuthenticated() || $user_ip) {
                    $link_vote_allowed = true;
                    $links_voted = $model->Linkvote->checkByLinksAndUser($links->getAllIds(), $context->user, $user_ip);
                }
            } elseif ($context->user->isAuthenticated() && $context->user->hasPermission('project link vote')) {
                $link_vote_allowed = true;
                $links_voted = $model->Linkvote->checkByLinksAndUser($links->getAllIds(), $context->user, getip());
            }
        }

        $this->_application->setData(array(
            'link_pages' => $pages,
            'link_page' => $page,
            'links' => $links,
            'link_view' => $view_req,
            'link_type_requested' => $link_type,
            'link_sorts' => array(
                'rating' => $context->plugin->_('Rating'),
                'newest' => $context->plugin->_('Newest first'),
                'oldest' => $context->plugin->_('Oldest first'),
            ),
            'link_types' => $link_types,
            'links_voted' => $links_voted,
            'link_vote_allowed' => $link_vote_allowed,
            'link_projects_dev' => $model->getGateway('Developer')->getProjectsAsDevByUserId($context->user->getId())
        ));
    }
}