<?php
class Plugg_Project_Main_Release_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$release = $this->getRequestedRelease($context)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $project = $release->get('Project');
        if (!$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$release->isApproved() && !$context->user->hasPermission('project release approve')) {
            // only developers are allowed to view pending releases
            if (!$project->isDeveloper($context->user)) {
                $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/' . $project->getId()));
                return;
            }
        }
        $project->addViewCount();
        $report_page_requested = $context->request->getAsInt('report_page', 1);
        $report_perpage = $context->plugin->getParam('numberOfReportsOnPage');
        $report_view = $context->request->getAsStr('report_view', 'newest');
        $model = $context->plugin->getModel();
        if ($report_id = $context->request->getAsInt('report_id', false)) {
            // make sure report exists and that it belongs to the requested release
            if (($report = $model->Report->fetchById($report_id)) && ($report->getVar('release_id') == $release->getId())) {
                $criteria = $model->createCriteria('Report');
                switch ($report_view) {
                    case 'oldest':
                        $criteria->created_isSmallerThan($report->getTimeCreated());
                    break;
                    default:
                        $criteria->created_isGreaterThan($report->getTimeCreated());
                    break;
                }
                if ($report_count = $model->Report->countByCriteria($criteria)) {
                    $report_page_requested = ceil(($report_count + 1) / $report_perpage);
                }
            }
        }
        switch ($report_view) {
            case 'oldest':
                $order = 'ASC';
                $sort = 'report_created';
                break;
            default:
                $order = 'DESC';
                $sort = 'report_created';
                $report_view = 'newest';
                break;
        }
        $report_pages = $release->paginateReports($report_perpage, $sort, $order);
        $report_page = $report_pages->getValidPage($report_page_requested);
        $this->_application->setData(array(
            'report_page_requested' => $report_page_requested,
            'report_view' => $report_view,
            'report_pages' => $report_pages,
            'report_page' => $report_page,
            'reports' => $report_page->getElements(),
            'release' => $release,
            'project' => $project,
            'report_sorts' => array(
                'newest' => $context->plugin->_('Newest first'),
                'oldest' => $context->plugin->_('Oldest first'),
            ),
            'is_developer' => $project->isDeveloper($context->user),
            'report_elements' => $context->plugin->getReportFormElementDefinitions(),
            'report_types' => $context->plugin->getReportTypes()
        ));
    }
}