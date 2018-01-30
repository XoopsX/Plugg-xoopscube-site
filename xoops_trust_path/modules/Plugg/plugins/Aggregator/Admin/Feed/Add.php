<?php
require_once 'Plugg/ModelEntityController/Create.php';

class Plugg_Aggregator_Admin_Feed_Add extends Plugg_ModelEntityController_Create
{
    public function __construct()
    {
        parent::__construct('Feed', array('autoAssignUser' => false));
    }

    protected function _onCreateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        try {
            $entity->loadFeedInfo();
        } catch (Plugg_Aggregator_Exception_InvalidSiteUrl $e) {
            $form->setElementError('site_url', $context->plugin->_('Invalid site URL.'));

            return false;
        } catch (Plugg_Aggregator_Exception $e) {
            if (!$entity->feed_url) {
                $form->setElementError(
                    'feed_url',
                    $context->plugin->_('Failed fetching feed data. Make sure that the feed URL is dicoverable or manually enter the feed URL.')
                );
            } else {
                $form->setElementError(
                    'feed_url',
                    $context->plugin->_('Failed fetching feed data from the supplied URL.')
                );
            }

            return false;
        }

        $entity->setApproved();

        return true;
    }

    protected function _onEntityCreated(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        if (($feed_owner = $entity->getUserId()) &&
            $context->user->getId() != $feed_owner
        ) {
            $entity->reload();
            $context->plugin->sendFeedAddedEmail($entity);
        }
        $this->_setOption('successUrl', array('path' => '/' . $entity->getId()));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        $form->removeElements(array('title', 'description', 'language'));
        $form->removeGroupedElements(array('host'), 'options');

        return $form;
    }

    protected function _onCreateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Add feed'));

        return true;
    }
}