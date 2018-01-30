<?php
require_once 'Plugg/ModelEntityController/Create.php';

class Plugg_Xigg_Admin_Category_Create extends Plugg_ModelEntityController_Create
{
    public function __construct()
    {
        $options = array('successUrl' => array('path' => '/category'));
        parent::__construct('Category', $options);
    }

    protected function _onEntityCreated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/category/' . $entity->getId()));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->addSubmitButtons($context->plugin->_('Create'));
        $form->setDefaults(array(
            'Parent' => $context->request->getAsInt('category_id')
        ));
        return $form;
    }

    protected function _onCreateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Add category'));
        return true;
    }
}