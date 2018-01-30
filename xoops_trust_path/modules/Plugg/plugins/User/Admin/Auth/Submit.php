<?php
class Plugg_User_Admin_Auth_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/auth');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$auths = $context->request->getAsArray('auths')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_auth_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        $model = $context->plugin->getModel();
        $auths_current = $model->Auth
            ->criteria()
            ->id_in(array_keys($auths))
            ->fetch();
        foreach ($auths_current as $auth) {
            $auth_id = $auth->getId();
            if ($auth->order != $auth_order = intval($auths[$auth_id]['order'])) {
                $auth->order = $auth_order;
            }
            foreach (array('active') as $key) {
                if ($auth->$key) {
                    if (empty($auths[$auth_id][$key])) $auth->$key = 0;
                } else {
                    if (!empty($auths[$auth_id][$key])) $auth->$key = 1;
                }
            }

            $auth_title = trim($auths[$auth_id]['title']);
            if ($auth_title != $auth->title) {
                $auth->title = $auth_title;
            }

        }
        if (false === $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess($context->plugin->_('Data updated successfully.'), $url);
        }
    }
}