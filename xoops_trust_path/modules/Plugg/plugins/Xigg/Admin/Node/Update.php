<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Xigg_Admin_Node_Update extends Plugg_ModelEntityController_Update
{
    public function __construct()
    {
        parent::__construct('Node', 'node_id');
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->addSubmitButtons($context->plugin->_('Submit'));
        return $form;
    }

    protected function _onUpdateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        if (!$entity->isPublished()) {
            // make sure published time is reset
            $entity->setVar('published', 0);
        }
        return true;
    }

    protected function _onEntityUpdated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $entity->unlinkTags();
        if ($tagging = $context->request->getAsStr('tagging', false)) {
            $entity->linkTagsByStr($tagging);
        }
        $this->_application->dispatchEvent('XiggSubmitNodeSuccess', array($context, $entity, /*$isEdit*/ true));
    }

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Edit'));
        return true;
    }
}