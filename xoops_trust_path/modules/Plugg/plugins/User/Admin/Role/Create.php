<?php
require_once 'Sabai/Application/ModelEntityController/Create.php';

class Plugg_User_Admin_Role_Create extends Sabai_Application_ModelEntityController_Create
{
    function __construct()
    {
        $options = array('successUrl' => array('base' => '/user/role'));
        parent::__construct('Role', $options);
    }

    function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        require dirname(__FILE__) . '/permissions.php';
        $entity->setPermissions($permissions_default);
        $form = $entity->toHTMLQuickForm(
            '',
            $this->_application->createUrl(array('path' => '/role/add')),
            'post',
            array(
                'permissions' => $permissions,
            )
        );
        $form->addSubmitButtons($context->plugin->_('Submit'));
        return $form;
    }

    function _onEntityCreated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('base' => '/user/role/' . $entity->getId()));
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }

    protected function _onCreateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Add role'));

        return true;
    }
}