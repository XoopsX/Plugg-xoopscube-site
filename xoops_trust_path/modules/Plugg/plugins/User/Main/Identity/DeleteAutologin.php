<?php
class Plugg_User_Main_Identity_DeleteAutologin extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check request
        if (!$autologin_id = $context->request->getAsInt('autologin_id')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        // Check token
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($context->request->getAsStr(SABAI_TOKEN_NAME), 'user_main_deleteautologin')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        // Check data exists
        if (!$autologin = $context->plugin->getModel()->Autologin->fetchById($autologin_id)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        // Check permission
        if (!$autologin->isOwnedBy($this->_application->identity) &&
            !$context->user->hasPermission('user autologin delete any')
        ) {
            $context->response->setError($context->plugin->_('Permission denied'));
            return;
        }

        // Remove
        $autologin->markRemoved();
        if (!$autologin->commit()) {
            $context->response->setError($context->plugin->_('Failed removing active autologin session.'));
        } else {
            $context->response->setSuccess($context->plugin->_('Autologin session removed successfully.'));
        }
    }
}