<?php
require_once 'Sabai/Application/ModelEntityController/Delete.php';

class Plugg_User_Admin_Auth_Authdata_Delete extends Sabai_Application_ModelEntityController_Delete
{
    function __construct()
    {
        $url = array('base' => '/user/auth');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::__construct('Authdata', 'authdata_id', $options);
    }

    function _onEntityDeleted($entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('base' => '/user/auth/' . $entity->getVar('auth_id')));
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }

    function _getEntityForm($entity, Sabai_Application_Context $context)
    {
        $form = parent::_getEntityForm($entity, $context);
        $form->freeze();
        $form->addSubmitButtons($context->plugin->_('Delete'));
        return $form;
    }

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Remove auth data'));
        return true;
    }
}