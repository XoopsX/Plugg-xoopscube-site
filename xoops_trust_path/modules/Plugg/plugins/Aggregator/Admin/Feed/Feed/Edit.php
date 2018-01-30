<?php
require_once 'Plugg/ModelEntityController/Update.php';

class Plugg_Aggregator_Admin_Feed_Feed_Edit extends Plugg_ModelEntityController_Update
{
    public function __construct()
    {
        parent::__construct('Feed', 'feed_id');
    }

    protected function _onUpdateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        // Load feed info if feed and/or favicon URL is empty
        if (!$entity->feed_url || !$entity->favicon_url) {
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
        }

        return true;
    }

    function _onEntityUpdated($entity, Sabai_Application_Context $context)
    {
        $this->_setOption('successUrl', array('path' => '/' . $entity->getId()));
    }

    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $form = $entity->toHTMLQuickForm();
        //$form->freezeElements(array('site_url', 'feed_url'));
        //$form->getElement('favicon')->getElement('favicon_url')->freeze();

        return $form;
    }

    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        $context->response->setPageInfo($context->plugin->_('Edit feed'));
        return true;
    }
}