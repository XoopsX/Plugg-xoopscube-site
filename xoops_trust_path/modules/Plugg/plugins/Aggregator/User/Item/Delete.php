<?php
require_once 'Plugg/ModelEntityController/Delete.php';

class Plugg_Aggregator_User_Item_Delete extends Plugg_ModelEntityController_Delete
{
    public function __construct()
    {
        parent::__construct('Item', 'item_id');
    }

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        if (!$context->user->hasPermission('aggregator item delete any')) {
            if ($entity->isOwnedBy($context->user)) {
                if (!$context->user->hasPermission('aggregator item delete own')) {
                    return false;
                }
            } else {
                return false;
            }
        }

        $context->response->setPageInfo($context->plugin->_('Delete feed item'));
        return true;
    }

    protected function _onEntityDeleted(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $entity->Feed->updateLastPublished();
        $this->_setOption('successUrl', array('path' => '/' . $entity->Feed->getId()));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Title'), $entity->title);

        return $form;
    }
}