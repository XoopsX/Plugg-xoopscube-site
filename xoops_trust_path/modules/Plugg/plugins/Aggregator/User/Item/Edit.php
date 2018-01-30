<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Aggregator_User_Item_Edit extends Plugg_ModelEntityController_Update
{
    public function __construct()
    {
        parent::__construct('Item', 'item_id');
    }

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        if (!$context->user->hasPermission('aggregator item edit any')) {
            if ($entity->isOwnedBy($context->user)) {
                if (!$context->user->hasPermission('aggregator item edit own')) {
                    return false;
                }
            } else {
                return false;
            }
        }

        $context->response->setPageInfo($context->plugin->_('Edit feed item'));
        return true;
    }

    protected function _onUpdateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        // Purify item body if body element is active
        if ($form->elementExists('body')) {
            $entity->body = $context->plugin->getHTMLPurifier($entity->Feed)->purify($entity->body);
        }

        return true;
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $remove = array('url');
        if (!$context->user->isSuperUser()) {
            if ($entity->Feed->isOwnedBy($context->user)) {
                if (!$context->user->hasPermission(array('aggregator item edit own body', 'aggregator item edit any body'))) {
                    $remove[] = 'body';
                }
                if (!$context->user->hasPermission(array('aggregator item edit own author', 'aggregator item edit any author'))) {
                    $remove[] = 'author';
                }
                if (!$context->user->hasPermission(array('aggregator item edit own author link', 'aggregator item edit any author link'))) {
                    $remove[] = 'author_link';
                }
                if (!$context->user->hasPermission(array('aggregator item hide own', 'aggregator item hide any'))) {
                    $remove[] = 'hidden';
                }
            } else {
                if (!$context->user->hasPermission(array('aggregator item edit any body'))) {
                    $remove[] = 'body';
                }
                if (!$context->user->hasPermission(array('aggregator item edit any author'))) {
                    $remove[] = 'author';
                }
                if (!$context->user->hasPermission(array('aggregator item edit any author link'))) {
                    $remove[] = 'author_link';
                }
                if (!$context->user->hasPermission(array('aggregator item hide any'))) {
                    $remove[] = 'hidden';
                }
            }
        }
        $form->removeElements($remove);

        return $form;
    }

    protected function _onEntityUpdated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $entity->Feed->updateLastPublished();
        $this->_setOption('successUrl', array('path' => '/item/' . $entity->getId()));
    }
}