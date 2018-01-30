<?php
require_once 'Plugg/ModelEntityController/Delete.php';

class Plugg_Xigg_Admin_Tag_Tag_Delete extends Plugg_ModelEntityController_Delete
{
    public function __construct()
    {
        $url = array('path' => '/tag');
        $options = array('successUrl' => $url, 'errorUrl' => $url);
        parent::__construct('Tag', 'tag_id', $options);
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Name'), $entity->name);
        $form->addSubmitButtons($context->plugin->_('Delete'));
        return $form;
    }

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Delete tag'));
        return true;
    }
}