<?php
require_once 'Sabai/Application/ModelEntityController/Delete.php';

class Plugg_User_Admin_Role_Delete extends Sabai_Application_ModelEntityController_Delete
{
    function __construct()
    {
        parent::__construct('Role', 'role_id');
    }

    function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        require dirname(__FILE__) . '/permissions.php';
        $form = $entity->toHTMLQuickForm(
            '',
            $this->_application->createUrl(array('path' => '/role/' . $entity->getId() . '/delete'))
        );
        $form->freeze();
        $form->addSubmitButtons($context->plugin->_('Delete'));
        return $form;
    }

    function _onDeleteEntity($entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/role'));
        $context->response->setPageInfo($context->plugin->_('Delete role'));
        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}