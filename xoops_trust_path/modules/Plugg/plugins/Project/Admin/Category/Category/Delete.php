<?php
require_once 'Sabai/Application/ModelEntityController/Delete.php';

class Plugg_Project_Admin_Category_Category_Delete extends Sabai_Application_ModelEntityController_Delete
{
    function __construct()
    {
        $url = array('path' => '/category');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::__construct('Category', 'category_id', $options);
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

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Delete category'));
        return true;
    }
}