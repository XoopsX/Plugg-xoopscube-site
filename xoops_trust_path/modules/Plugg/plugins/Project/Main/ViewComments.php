<?php
class Plugg_Project_Main_ViewComments extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $context->plugin->getModel();

        // projects
        $comment_view = $context->request->getAsStr('comment_view');
        switch ($comment_view) {
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
                $comment_view = 'newest';
                break;
        }

        $pages = $model->Comment
            ->criteria()
            ->status_is(Plugg_Project_Plugin::COMMENT_STATUS_APPROVED)
            ->paginate($context->plugin->getParam('numberOfCommentsOnTopPage'), $sort, $order);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        $this->_application->setData(array(
            'comment_pages' => $pages,
            'comment_page' => $page,
            'comments' => $page->getElements()->with('Project'),
            'comment_view' => $comment_view,
            'comment_projects_dev' => $model->getGateway('Developer')->getProjectsAsDevByUserId($context->user->getId()),
            'comment_sorts' => array(
                'oldest' => $context->plugin->_('Oldest first'),
                'newest' => $context->plugin->_('Newest first'),
                'rating' => $context->plugin->_('Higher rating first')
            )
        ));
    }
}