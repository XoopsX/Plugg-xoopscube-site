<?php
require_once 'Plugg/ModelEntityController/Create.php';

class Plugg_Xigg_Admin_Tag_Create extends Plugg_ModelEntityController_Create
{
    public function __construct()
    {
         parent::__construct('Tag');
    }

    protected function _onEntityCreated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/tag/' . $entity->getId()));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->addSubmitButtons($context->plugin->_('Create'));
        return $form;
    }

    protected function _onCreateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Add tag'));
        return true;
    }
}