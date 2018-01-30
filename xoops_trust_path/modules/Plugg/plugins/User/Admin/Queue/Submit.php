<?php
class Plugg_User_Admin_Queue_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/queue');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$queues = $context->request->getAsArray('queues')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_queue_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        $model = $context->plugin->getModel();
        $queues_current = $model->Queue
            ->criteria()
            ->id_in($queues)
            ->fetch();
        foreach ($queues_current as $queue) {
            $queue->markRemoved();
        }
        if (false === $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }
    }
}