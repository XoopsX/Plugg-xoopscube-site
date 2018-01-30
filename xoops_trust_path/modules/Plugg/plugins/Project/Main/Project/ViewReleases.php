<?php
class Plugg_Project_Main_Project_ViewReleases extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) || !$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $view_req = $context->request->getAsStr('release_view');
        switch ($view_req) {
            case 'oldest':
                $sort = array('release_date', 'release_created');
                $order = array('ASC', 'ASC');
                break;
            case 'stability':
                $sort = array('release_stability', 'release_date', 'release_created');
                $order = array('DESC', 'DESC', 'DESC');
                break;
            case 'reported':
                $sort = array('release_report_last', 'release_date', 'release_created');
                $order = array('DESC', 'DESC', 'DESC');
                break;
            default:
                $sort = array('release_date', 'release_created');
                $order = array('DESC', 'DESC');
                $view_req = 'newest';
                break;
        }
        $perpage = $context->plugin->getParam('numberOfReleasesOnPage');
        $is_developer = $project->isDeveloper($context->user);
        if (!$context->user->hasPermission('project release approve') && !$is_developer) {
            $pages = $context->plugin->getModel()->Release
                ->criteria()
                ->status_is(Plugg_Project_Plugin::RELEASE_STATUS_APPROVED)
                ->paginateByProject($project->getId(), $perpage, $sort, $order);
        } else {
            $pages = $context->plugin->getModel()->Release->paginateByProject($project->getId(), $perpage, $sort, $order);
        }

        $page = $pages->getValidPage($context->request->getAsInt('release_page', 1));
        $this->_application->setData(array(
            'project' => $project,
            'release_pages' => $pages,
            'release_page' => $page,
            'releases' => $page->getElements(),
            'release_view' => $view_req,
            'release_sorts' => array(
                'newest' => $context->plugin->_('Newest first'),
                'oldest' => $context->plugin->_('Oldest first'),
                'stability' => $context->plugin->_('More stable first'),
                'reported' => $context->plugin->_('Recently reported')
            ),
            'release_stabilities' => $context->plugin->getReleaseStabilities(),
            'is_developer' => $is_developer,
        ));
    }
}