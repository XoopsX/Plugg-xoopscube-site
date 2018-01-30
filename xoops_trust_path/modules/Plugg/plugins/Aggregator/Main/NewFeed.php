<?php
require_once 'Plugg/FormController.php';

class Plugg_Aggregator_Main_NewFeed extends Plugg_FormController
{
    private $_feed;

    protected function _init(Sabai_Application_Context $context)
    {
        // Make sure the user has permission to add a system owned feed
        if (!$context->user->hasPermission(array('aggregator feed add any', 'aggregator feed add any approved'))) return false;

        // No confirmation
        $this->_confirmable = false;

        // Init feed
        $this->_feed = $context->plugin->getModel()->create('Feed');

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        $form = $this->_feed->toHTMLQuickForm();
        $remove = array('owner', 'title', 'description', 'language', 'favicon');
        $remove_options = array('host');
        if (!$context->user->isSuperUser()) {
            if (!$context->user->hasPermission(array('aggregator feed allow any img'))) {
                $remove_options[] = 'allow_image';
            }
            if (!$context->user->hasPermission(array('aggregator feed allow any ex resources'))) {
                $remove_options[] = 'allow_external_resources';
            }
        }
        $form->removeElements($remove);
        $form->removeGroupedElements($remove_options, 'options');

        return $form;
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        // Create feed
        $this->_feed->applyForm($form);
        $this->_feed->markNew();

        // Load feed info
        try {
            $this->_feed->loadFeedInfo();
        } catch (Plugg_Aggregator_Exception_InvalidSiteUrl $e) {
            $form->setElementError('site_url', $context->plugin->_('Invalid site URL.'));

            return false;
        } catch (Plugg_Aggregator_Exception $e) {
            if (!$this->_feed->feed_url) {
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

        // Set feed status
        if ($context->plugin->getParam('feedsRequireApproval')) {
            $this->_feed->status = Plugg_Aggregator_Plugin::FEED_STATUS_PENDING;
            if ($context->user->hasPermission('aggregator feed add any approved')) {
                $this->_feed->status = Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED;
            }
        } else {
            $this->_feed->status = Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED;
            $success_msg = $context->plugin->_('Feed data submitted successfully.');
        }

        if (!$context->plugin->getModel()->commit()) return false;

        if ($this->_feed->isApproved()) {
            $msg = $context->plugin->_('Feed data submitted successfully.');
        } else {
            $msg = $context->plugin->_('Feed data submitted successfully. The submitted feed will be listed on the feed list page once approved by the administrators.');
        }
        $context->response->setSuccess($msg);

        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Add feed'));
    }
}