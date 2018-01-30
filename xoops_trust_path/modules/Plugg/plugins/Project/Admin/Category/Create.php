<?php
require_once 'Sabai/Application/ModelEntityController/Create.php';

class Plugg_Project_Admin_Category_Create extends Sabai_Application_ModelEntityController_Create
{
    function __construct()
    {
        parent::__construct('Category');
    }

    function _onEntityCreated($entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/category/' . $entity->getId()));
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }

    function _getEntityForm($entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->addSubmitButtons($context->plugin->_('Submit'));
        return $form;
    }

    protected function _onCreateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Add category'));
        return true;
    }
}