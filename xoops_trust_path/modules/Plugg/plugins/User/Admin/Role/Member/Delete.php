<?php
require_once 'Sabai/Application/ModelEntityController/Delete.php';

class Plugg_User_Admin_Role_Member_Delete extends Sabai_Application_ModelEntityController_Delete
{
    function __construct()
    {
        $url = array('base' => '/user/role');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::__construct('Member', 'member_id', $options);
    }

    function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        // prevent removing yourself from the admin role
        if ($entity->getUserId() == $context->user->getId()) {
            $role = $entity->get('Role');
            if ($role->get('system')) {
                $context->response->setError(
                    $context->plugin->_('You may not remove yourself from the system defined role'),
                    array('base' => '/system/role/' . $role->getId())
                );
                return false;
            }
        }
        $context->response->setPageInfo($context->plugin->_('Remove member'));

        return true;
    }

    function _onEntityDeleted(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('base' => '/user/role/' . $entity->getVar('role_id')));
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }

    function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = parent::_getEntityForm($entity, $context);
        $form->freeze();
        $form->addSubmitButtons($context->plugin->_('Delete'));
        return $form;
    }
}