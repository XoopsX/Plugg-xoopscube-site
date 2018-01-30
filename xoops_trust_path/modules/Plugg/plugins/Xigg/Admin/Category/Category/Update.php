<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Xigg_Admin_Category_Category_Update extends Plugg_ModelEntityController_Update
{
    public function __construct()
    {
        parent::__construct('Category', 'category_id');
    }

    protected function _onEntityUpdated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/category/' . $entity->getId()));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
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