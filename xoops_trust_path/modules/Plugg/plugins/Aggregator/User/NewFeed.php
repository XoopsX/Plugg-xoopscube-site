<?php
require_once 'Plugg/FormController.php';

class Plugg_Aggregator_User_NewFeed extends Plugg_FormController
{
    private $_feed;

    protected function _init(Sabai_Application_Context $context)
    {
        // Check if submitting for another user and have permission to do so
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission(array('aggregator feed add any', 'aggregator feed add any approved'))) return false;
        } else {
            if (!$context->user->hasPermission(array('aggregator feed add own', 'aggregator feed add own approved'))) return false;
        }

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
            if ($this->_application->identity->getId() == $context->user->getId()) {
                if (!$context->user->hasPermission(array('aggregator feed allow own img', 'aggregator feed allow any img'))) {
                    $remove_options[] = 'allow_image';
                }
                if (!$context->user->hasPermission(array('aggregator feed allow own ex resources', 'aggregator feed allow any ex resources'))) {
                    $remove_options[] = 'allow_external_resources';
                }
            } else {
                if (!$context->user->hasPermission(array('aggregator feed allow any img'))) {
                    $remove_options[] = 'allow_image';
                }
                if (!$context->user->hasPermission(array('aggregator feed allow any ex resources'))) {
                    $remove_options[] = 'allow_external_resources';
                }
            }
        }
        $form->removeElements($remove);
        $form->removeGroupedElements($remove_options, 'options');
        $form->insertElementAfter(
            $form->createStatic(
                $this->_application->identity->getUsername(),
                null,
                $context->plugin->_('Feed owner')
            ),
            'feed_url'
        );

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
            } else {
                if ($this->_application->identity->getId() == $context->user->getId() &&
                    $context->user->hasPermission('aggregator feed add own approved')
                ) {
                    $this->_feed->status = Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED;
                }
            }
        } else {
            $this->_feed->status = Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED;
            $success_msg = $context->plugin->_('Feed data submitted successfully.');
        }
        $this->_feed->assignUser($this->_application->identity);

        if (!$context->plugin->getModel()->commit()) return false;

        if ($this->_feed->isApproved()) {
            if ($this->_application->identity->getId() != $context->user->getId() &&
                $context->plugin->getParam('sendAddededNotifyEmail')
            ) {
                $this->_feed->reload();
                $context->plugin->sendFeedAddedEmail($this->_feed);
            }
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