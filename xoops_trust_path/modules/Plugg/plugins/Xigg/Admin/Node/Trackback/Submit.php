<?php
class Plugg_Xigg_Admin_Node_Trackback_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$trackback_ids = $context->request->getAsArray('trackbacks')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'Admin_node_trackback_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        $model = $context->plugin->getModel();
        $trackbacks = $model->Trackback
            ->criteria()
            ->id_in($trackback_ids)
            ->fetch();
        foreach ($trackbacks as $trackback) {
            $trackback->markRemoved();
        }
        if (false === $deleted = $model->commit()) {
            $context->response->setError($context->plugin->_('Could not delete selected trackbacks'), $url);
        } else {
            $context->response->setSuccess(sprintf($context->plugin->_('%d trackback(s) deleted successfully'), $deleted), $url);
        }
    }
}