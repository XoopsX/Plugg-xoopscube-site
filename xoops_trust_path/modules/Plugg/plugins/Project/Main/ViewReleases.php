<?php
class Plugg_Project_Main_ViewReleases extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();

        $release_view = $context->request->getAsStr('release_view');
        switch ($release_view) {
            case 'oldest':
                $sort = array('release_date', 'release_created');
                $order = array('ASC', 'ASC');
                break;
            case 'stability':
                $sort = array('release_stability', 'release_date', 'release_created');
                $order = array('DESC', 'DESC', 'DESC');
                break;
            case 'report':
                $sort = array('release_report_last', 'release_date', 'release_created');
                $order = array('DESC', 'DESC', 'DESC');
                break;
            default:
                $sort = array('release_date', 'release_created');
                $order = array('DESC', 'DESC');
                $release_view = 'newest';
                break;
        }

        $perpage = $context->plugin->getParam('numberOfReleasesOnTopPage');
        $pending_only = false;
        $projects_dev = array();
        if ($context->user->isAuthenticated()) {
            $projects_dev = $model->getGateway('Developer')->getProjectsAsDevByUserId($context->user->getId());
        }
        if (!$context->user->hasPermission('project release approve')) {
            $criteria = $model->createCriteria('Release')->status_is(Plugg_Project_Plugin::RELEASE_STATUS_APPROVED);
            if (!empty($projects_dev)) {
                $criteria->or_();
                $criteria->projectId_in(array_keys($projects_dev));
            }
            $pages = $model->Release->paginateByCriteria($criteria, $perpage, $sort, $order);
        } else {
            if ($pending_only = $context->request->getAsBool('pending', false)) {
                $pages = $model->Release
                    ->criteria()
                    ->status_is(Plugg_Project_Plugin::RELEASE_STATUS_PENDING)
                    ->paginate($perpage, $sort, $order);
            } else {
                $pages = $model->Release->paginate($perpage, $sort, $order);
            }
        }
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        $this->_application->setData(array(
            'release_pages' => $pages,
            'release_page' => $page,
            'releases' => $page->getElements()->with('Project'),
            'release_view' => $release_view,
            'release_stabilities' => $context->plugin->getReleaseStabilities(),
            'release_pending_only' => $pending_only,
            'release_projects_dev' => $projects_dev,
            'release_sorts' => array(
                'oldest' => $context->plugin->_('Oldest first'),
                'newest' => $context->plugin->_('Newest first'),
                'stability' => $context->plugin->_('More stable first'),
                'report' => $context->plugin->_('Newly reported first'),
            )));
    }
}