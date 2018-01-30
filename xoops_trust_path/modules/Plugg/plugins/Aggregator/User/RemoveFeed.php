<?php
require_once 'Plugg/ModelEntityController/Delete.php';

class Plugg_Aggregator_User_RemoveFeed extends Plugg_ModelEntityController_Delete
{
    public function __construct()
    {
        parent::__construct('Feed', 'feed_id');
    }

    protected function _onEntityDeleted(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/feeds'));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElementsAll();
        $form->addElement('static', '', $context->plugin->_('Title'), $entity->title);
        $this->_submitPhrase = $context->plugin->_('Remove feed');

        return $form;
    }

    protected function _onDeleteEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        if (!$context->user->hasPermission('aggregator feed delete any')) {
            if ($entity->isOwnedBy($context->user)) {
                if (!$context->user->hasPermission('aggregator feed delete own')) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }
}