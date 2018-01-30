<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Aggregator_Admin_Item_Edit extends Plugg_ModelEntityController_Update
{
    public function __construct()
    {
        parent::__construct('Item', 'item_id');
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        return $entity->toHTMLQuickForm();
    }

    protected function _onUpdateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        // Purify item body if body element is active
        if ($form->elementExists('body')) {
            $entity->body = $context->plugin->getHTMLPurifier($entity->Feed)->purify($entity->body);
        }

        return true;
    }

    protected function _onEntityUpdated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $entity->Feed->updateLastPublished();
        $this->_setOption('successUrl', array('path' => '/feed/' . $entity->getVar('feed_id')));
    }

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Edit feed item'));
        return true;
    }
}