<?php
class Plugg_Project_Main_Comment_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ((!$comment = $this->getRequestedComment($context))) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $comment_id = $comment->getId();
        header('Location: ' . $this->_application->createUrl(array(
            'path' => '/' . $comment->getVar('project_id'),
            'params' => array('view' => 'comments', 'comment_id' => $comment_id),
            'fragment' => 'comment' . $comment_id,
            'separator' => '&'
        )));
        exit;
    }
}