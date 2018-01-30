<?php
class Plugg_Project_Main_Project_ViewComments extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) || !$project->isReadable($context->user)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $view_req = $context->request->getAsStr('comment_view');
        switch ($view_req) {
            case 'oldest':
                $sort = array('comment_created');
                $order = array('ASC');
                break;
            case 'rating':
                $sort = array('comment_rating', 'comment_created');
                $order = array('DESC', 'DESC');
                break;
            default:
                $sort = array('comment_created');
                $order = array('DESC');
                $view_req = 'newest';
                break;
        }
        $perpage = $context->plugin->getParam('numberOfCommentsOnPage');
        $pages = $context->plugin->getModel()->Comment->paginateByProject($project->getId(), $perpage, $sort, $order);
        $page = $pages->getValidPage($context->request->getAsInt('comment_page', 1));
        $this->_application->setData(array(
            'project' => $project,
            'is_developer' => $project->isDeveloper($context->user),
            'comment_pages' => $pages,
            'comment_page' => $page,
            'comments' => $page->getElements(),
            'comment_view' => $view_req,
            'comment_sorts' => array(
                'newest' => $context->plugin->_('Newest first'),
                'oldest' => $context->plugin->_('Oldest first'),
                'rating' => $context->plugin->_('Higher rating first')
            )
        ));
    }
}