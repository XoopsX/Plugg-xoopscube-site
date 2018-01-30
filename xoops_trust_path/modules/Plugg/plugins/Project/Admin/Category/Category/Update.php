<?php
require_once 'Sabai/Application/ModelEntityController/Update.php';

class Plugg_Project_Admin_Category_Category_Update extends Sabai_Application_ModelEntityController_Update
{
    function __construct()
    {
        parent::__construct('Category', 'category_id');
    }

    function _onEntityUpdated($entity, Sabai_Application_Context $context)
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

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Edit category'));
        return true;
    }
}