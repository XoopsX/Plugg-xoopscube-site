<?php
class Plugg_Xigg_Main_Node_ShowComments extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $comment_view = $context->request->getAsStr('comment_view');
        $comment_perpage = $context->plugin->getParam('numberOfCommentsOnPage');
        switch ($comment_view) {
            case 'nested':
                $pages = $this->_application->node->paginateCommentsByParentComment('NULL', $comment_perpage, false);
                break;
            case 'newest':
                $pages = $this->_application->node->paginateComments($comment_perpage, 'comment_created', 'DESC');
                break;
            case 'oldest':
            default:
                $pages = $this->_application->node->paginateComments($comment_perpage, 'comment_created', 'ASC');
                $comment_view = 'oldest';
                break;
        }
        $page = $pages->getValidPage($context->request->getAsInt('comment_page', 1));
        $comment_form_show = false;
        if ($this->_application->node->get('allow_comments')) {
            if ($context->user->isAuthenticated() ||
                $context->plugin->getParam('guestCommentsAllowed')
            ) {
                $comment_form_show = true;
            }
        }
        $this->_application->setData(array(
            'comment_pages'     => $pages,
            'comment_page'      => $page,
            'comment_form_show' => $comment_form_show,
            'comment_view'      => $comment_view,
            'comments'          => $comments = $page->getElements()->with('UserWithData'),
            'comment_ids'       => $comments->getAllIds()
        ));
        $context->response->setPageInfo($context->plugin->_('Listing comments'));
    }
}