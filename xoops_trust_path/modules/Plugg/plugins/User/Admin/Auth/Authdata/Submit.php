<?php
class Plugg_User_Admin_Auth_Authdata_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $error_url = $success_url = array('base' => '/user/auth/' . $context->request->getAsInt('auth_id'));
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (!$authdatas = $context->request->getAsArray('authdatas')) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_auth_authdata_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (false === $num = $this->_remove($context, $authdatas)) {
            $context->response->setError(
                $context->plugin->_('Could not remove selected authentication data'),
                $error_url
            );
        } else {
            $context->response->setSuccess(
                sprintf($context->plugin->_('%d authentication data removed successfully'), $num),
                $success_url
            );
        }
    }

    function _remove(Sabai_Application_Context $context, $authdatas)
    {
        $model = $context->plugin->getModel();
        $authdatas = $model->Authdata
            ->criteria()
            ->id_in($authdatas)
            ->fetch();
        foreach ($authdatas as $authdata) {
            $authdata->markRemoved();
        }
        return $model->commit();
    }
}