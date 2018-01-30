<?php
require_once 'Plugg/ModelEntityController/Delete.php';

class Plugg_Xigg_Admin_Node_Delete extends Plugg_ModelEntityController_Delete
{
    public function __construct()
    {
        $url = array('path' => '/node');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::__construct('Node', 'node_id', $options);
    }

    protected function _onEntityDeleted(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $this->_application->dispatchEvent('XiggDeleteNodeSuccess', array($context, $entity));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Title'), $entity->title);
        $form->addSubmitButtons($context->plugin->_('Delete'));
        return $form;
    }

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Delete'));
        return true;
    }
}