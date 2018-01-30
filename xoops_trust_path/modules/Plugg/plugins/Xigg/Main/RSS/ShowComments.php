<?php
class Plugg_Xigg_Main_RSS_ShowComments extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$node = $this->getNodeById($context, 'node_id')) {
            $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/rss'));
            return;
        }
        $comment_view = $context->request->getAsStr('comment_view', 'newest');
        $comment_perpage = $context->plugin->getParam('numberOfCommentsOnPage');
        switch ($comment_view) {
            case 'oldest':
                $pages = $node->paginateComments($comment_perpage, 'comment_created', 'ASC');
                break;
            case 'newest':
            default:
                $pages = $node->paginateComments($comment_perpage, 'comment_created', 'DESC');
                $comment_view = 'newest';
            break;
        }
        $this->_application->setData(array(
            'node' => $node,
            'comments' => $pages->getValidPage($context->request->getAsInt('comment_page', 1))->getElements()
        ));
    }
}