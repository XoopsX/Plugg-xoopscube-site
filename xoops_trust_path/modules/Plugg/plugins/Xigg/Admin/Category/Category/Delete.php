<?php
require_once 'Plugg/ModelEntityController/Delete.php';

class Plugg_Xigg_Admin_Category_Category_Delete extends Plugg_ModelEntityController_Delete
{
    public function __construct()
    {
        $url = array('path' => '/category');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::__construct('Category', 'category_id', $options);
    }

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        if ($entity->descendantsCount() > 0) {
            $context->response->setError('Category with child categories may not be deleted', array('path' => '/category'));
            return false;
        }

        return true;
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Name'), $entity->name);
        $form->addSubmitButtons($context->plugin->_('Delete'));
        return $form;
    }

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Delete category'));
        return true;
    }
}