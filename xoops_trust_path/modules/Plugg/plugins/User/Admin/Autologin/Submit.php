<?php
class Plugg_User_Admin_Autologin_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $url = array('path' => '/autologin');
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$autologins = $context->request->getAsArray('autologins')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_autologin_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        $model = $context->plugin->getModel();
        $autologins_current = $model->Autologin
            ->criteria()
            ->id_in($autologins)
            ->fetch();
        foreach ($autologins_current as $autologin) {
            $autologin->markRemoved();
        }
        if (false === $num = $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating data.'), $url);
        } else {
            $context->response->setSuccess(
                sprintf($context->plugin->_('%d auto-login session(s) deleted successfully.'), $num),
                $url
            );
        }
    }
}