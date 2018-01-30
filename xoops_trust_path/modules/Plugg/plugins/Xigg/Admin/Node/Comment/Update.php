<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Xigg_Admin_Node_Comment_Update extends Plugg_ModelEntityController_Update
{
    public function __construct()
    {
        parent::__construct('Comment', 'comment_id');
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElements(array('Node', 'body_filter_id', 'body'));
        $form->addSubmitButtons($context->plugin->_('Submit'));
        return $form;
    }

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Edit comment'));
        return true;
    }
}