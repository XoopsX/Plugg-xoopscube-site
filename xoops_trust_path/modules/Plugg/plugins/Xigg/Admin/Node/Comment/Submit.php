<?php
class Plugg_Xigg_Admin_Node_Comment_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$comment_ids = $context->request->getAsArray('comments')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'Admin_node_comment_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        $model = $context->plugin->getModel();
        $comments = $model->Comment
            ->criteria()
            ->id_in($comment_ids)
            ->fetch();
        foreach ($comments as $comment) {
            $comment->markRemoved();
        }
        if (false === $deleted = $model->commit()) {
            $context->response->setError($context->plugin->_('Could not delete selected comments'), $url);
        } else {
            $context->response->setSuccess(sprintf($context->plugin->_('%d comment(s) deleted successfully'), $deleted), $url);
        }
    }
}