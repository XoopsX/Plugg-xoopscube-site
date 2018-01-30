<?php
class Plugg_User_Admin_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$users = $context->request->getAsArray('users')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$action = $context->request->getAsStr('action')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'user_admin_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if ((!$action = explode(',', $action)) ||
            count($action) != 2 ||
            !in_array($action[0], array('assign', 'remove'))
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if ((!$role_id = intval($action[1])) ||
            (!$role = $context->plugin->getModel()->Role->fetchById($role_id))
        ) {
            $context->response->setError($context->plugin->_('Invalid role'));
            return;
        }
        $success_url = $error_url = array(
            'params' => array(
                'sortby' => $context->request->getAsStr('sortby'),
                'page' => $context->request->getAsInt('page')
            )
        );
        switch ($action[0]) {
            case 'assign':
                if (false === $num = $this->_assign($context, $role, $users)) {
                    $context->response->setError(
                        $context->plugin->_('Could not assign role to selected users'),
                        $error_url
                    );
                } else {
                    $context->response->setSuccess(
                        sprintf($context->plugin->_('%d users(s) assigned role successfully'), $num),
                        $success_url
                    );
                }
                break;
            case 'remove':
                if (false === $num = $this->_remove($context, $role, $users)) {
                    $context->response->setError(
                        $context->plugin->_('Could not remove role from selected users'),
                        $error_url
                    );
                } else {
                    $context->response->setSuccess(
                        sprintf($context->plugin->_('%d users(s) removed role successfully'), $num),
                        $success_url
                    );
                }
                break;
        }
    }

    function _assign(Sabai_Application_Context $context, $role, $userIds)
    {
        $model = $context->plugin->getModel();
        $members = $model->Member
            ->criteria()
            ->userid_in($userIds)
            ->fetchByRole($role->getId());
        foreach ($members as $member) {
            // already assigned
            unset($userIds[$member->getUserId()]);
        }
        foreach ($userIds as $user_id) {
            $new_member[$user_id] = $model->create('Member');
            $new_member[$user_id]->assignRole($role);
            $new_member[$user_id]->setVar('userid', $user_id);
            $new_member[$user_id]->markNew();
        }
        return $model->commit();
    }

    function _remove(Sabai_Application_Context $context, $role, $userIds)
    {
        $model = $context->plugin->getModel();
        $members = $model->Member
            ->criteria()
            ->userid_in($userIds)
            ->fetchByRole($role->getId());
        foreach ($members as $member) {
            $member->markRemoved();
        }
        return $model->commit();
    }
}