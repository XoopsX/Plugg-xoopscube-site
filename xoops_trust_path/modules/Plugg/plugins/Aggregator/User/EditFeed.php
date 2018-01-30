<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Aggregator_User_EditFeed extends Plugg_ModelEntityController_Update
{
    public function __construct()
    {
        parent::__construct('Feed', 'feed_id');
    }

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        if (!$context->user->hasPermission('aggregator feed edit any')) {
            if ($entity->isOwnedBy($context->user)) {
                if (!$context->user->hasPermission('aggregator feed edit own')) {
                    return false;
                }
            } else {
                return false;
            }
        }

        $context->response->setPageInfo($context->plugin->_('Edit feed'));
        return true;
    }

    function _onEntityUpdated($entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/feeds'));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        if (!$context->user->isSuperUser()) {
            $remove = array('site_url', 'feed_url', 'favicon', 'language', 'owner');
            $remove_options = array();
            if ($entity->isOwnedBy($context->user)) {
                if (!$context->user->hasPermission(array('aggregator feed allow own img', 'aggregator feed allow any img'))) {
                    $remove_options[] = 'allow_image';
                }
                if (!$context->user->hasPermission(array('aggregator feed allow own ex resources', 'aggregator feed allow any ex resources'))) {
                    $remove_options[] = 'allow_external_resources';
                }
                if (!$context->user->hasPermission(array('aggregator feed edit own host', 'aggregator feed edit any host'))) {
                    $remove_options[] = 'host';
                }
            } else {
                if (!$context->user->hasPermission(array('aggregator feed allow any img'))) {
                    $remove_options[] = 'allow_image';
                }
                if (!$context->user->hasPermission(array('aggregator feed allow any ex resources'))) {
                    $remove_options[] = 'allow_external_resources';
                }
                if (!$context->user->hasPermission(array('aggregator feed edit any host'))) {
                    $remove_options[] = 'host';
                }
            }
            if (!empty($remove_options)) {
                $form->removeGroupedElements($remove_options, 'options');
            }
        } else {
            $remove = array('site_url', 'feed_url');
        }
        $form->removeElements($remove);

        return $form;
    }
}