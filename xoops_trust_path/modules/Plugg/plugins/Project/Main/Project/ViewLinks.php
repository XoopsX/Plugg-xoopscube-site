<?php
class Plugg_Project_Main_Project_ViewLinks extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) || !$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
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
        $model = $context->plugin->getModel();
        $link_type = $context->request->getAsStr('link_type');
        $link_types = $context->plugin->getLinkTypes();
        $perpage = $context->plugin->getParam('numberOfLinksOnPage');
        if (!empty($link_type) && isset($link_types[$link_type])) {
            $pages = $model->Link
                ->criteria()
                ->type_is($link_type)
                ->paginateByProject($project->getId(), $perpage, $sort, $order);
        } else {
            $pages = $model->Link->paginateByProject($project->getId(), $perpage, $sort, $order);
        }
        $page = $pages->getValidPage($context->request->getAsInt('link_page', 1));
        $links = $page->getElements();

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
            'project' => $project,
            'is_developer' => $project->isDeveloper($context->user),
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
        ));
    }
}