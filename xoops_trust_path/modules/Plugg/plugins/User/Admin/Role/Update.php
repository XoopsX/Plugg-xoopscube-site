<?php
require_once 'Sabai/Application/ModelEntityController/Update.php';

class Plugg_User_Admin_Role_Update extends Sabai_Application_ModelEntityController_Update
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
            $this->_application->createUrl(array('path' => '/role/' . $entity->getId() . '/edit')),
            'post',
            array('permissions' => $permissions)
        );
        $form->addSubmitButtons($context->plugin->_('Submit'));
        return $form;
    }

    function _onUpdateEntity($entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/role/' . $entity->getId()));
        $context->response->setPageInfo($context->plugin->_('Edit role'));

        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}