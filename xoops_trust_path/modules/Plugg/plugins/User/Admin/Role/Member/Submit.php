<?php
class Plugg_User_Admin_Role_Member_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $error_url = $success_url = array('base' => '/user/role/' . $context->request->getAsInt('role_id'));
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (!$members = $context->request->getAsArray('members')) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'Admin_role_member_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (false === $num = $this->_remove($context, $members)) {
            $context->response->setError(
                $context->plugin->_('Could not remove selected members'),
                $error_url
            );
        } else {
            $context->response->setSuccess(
                sprintf($context->plugin->_('%d members(s) removed successfully'), $num),
                $success_url
            );
        }
    }

    function _remove(Sabai_Application_Context $context, $memberIds)
    {
        $model = $context->plugin->getModel();
        $members = $model->Member
            ->criteria()
            ->id_in($memberIds)
            ->fetch();
        foreach ($members as $member) {
            $member->markRemoved();
        }
        return $model->commit();
    }
}