<?php
class Plugg_Aggregator_Admin_Feed_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        if (!$feeds = $context->request->getAsArray('feeds')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        // Redirect to feed details page if a single feed is requested
        if (count($feeds) == 1) {
            $url = array(
                'path' => '/' . $feeds[0]
            );
        } else {
            $url = array();
        }

        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'aggregator_admin_feed_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        $action = '';
        foreach (array('approve', 'delete', 'update', 'empty') as $_action) {
            if ($context->request->getAsBool($_action, false)) {
                $action = $_action;
                break;
            }
        }
        switch ($action) {
            case 'approve':
                if (false === $this->_approve($context, $feeds)) {
                    $context->response->setError($context->plugin->_('Could not approve selected feeds'), $url);
                } else {
                    $context->response->setSuccess($context->plugin->_('Selected feeds approved successfully'), $url);
                }
                break;
            case 'delete':
                if (false === $this->_delete($context, $feeds)) {
                    $context->response->setError($context->plugin->_('Could not delete selected feeds'), $url);
                } else {
                    $context->response->setSuccess($context->plugin->_('Selected feeds deleted successfully'));
                }
                break;
            case 'update':
                if (false === $count = $this->_update($context, $feeds)) {
                    $context->response->setError($context->plugin->_('Could not update items for selected feeds'), $url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d out of %d feed(s) updated successfully'), $count, count($feeds)), $url);
                }
                break;
            case 'empty':
                if (false === $this->_empty($context, $feeds)) {
                    $context->response->setError($context->plugin->_('Could not remove feed items for selected feeds'), $url);
                } else {
                    $context->response->setSuccess($context->plugin->_('Selected feeds have been emptied successfully'), $url);
                }
                break;
            default:
                $context->response->setError($context->plugin->_('Invalid request'), $url);
        }
    }

    private function _approve($context, $feedIds)
    {
        $model = $context->plugin->getModel();
        $feeds = $model->Feed
            ->criteria()
            ->status_is(Plugg_Aggregator_Plugin::FEED_STATUS_PENDING)
            ->id_in($feedIds)
            ->fetch();
        foreach ($feeds as $feed) {
            $feed->setApproved();
        }

        if (!$ret = $model->commit()) {
            return false;
        }

        foreach ($feeds as $feed) {
            $context->plugin->sendFeedApprovedEmail($feed);
        }

        return $ret;
    }

    private function _delete($context, $feedIds)
    {
        $model = $context->plugin->getModel();
        $feeds = $model->Feed
            ->criteria()
            ->id_in($feedIds)
            ->fetch();
        foreach ($feeds as $feed) {
            $feed->markRemoved();
        }

        return $model->commit();
    }

    private function _update($context, $feedIds)
    {
        $count = false;
        $model = $context->plugin->getModel();
        $feeds = $model->Feed
            ->criteria()
            ->status_is(Plugg_Aggregator_Plugin::FEED_STATUS_APPROVED)
            ->id_in($feedIds)
            ->fetch();
        foreach ($feeds as $feed) {
            if (false !== $feed->updateItems()) {
                $count = $count + 1;
            }
        }

        return $count;
    }

    private function _empty($context, $feedIds)
    {
        $model = $context->plugin->getModel();

        // Delete all items associated with the selected feeds
        $criteria = $model->createCriteria('Item')->feedId_in($feedIds);
        if (false === $model->getGateway('Item')->deleteByCriteria($criteria)) {
            return false;
        }

        // Now, reset all statistic data for the feeds
        $feeds = $model->Feed
            ->criteria()
            ->id_in($feedIds)
            ->fetch();
        foreach ($feeds as $feed) {
            $feed->setVars(array(
                'last_fetch' => 0,
                'last_ping' => 0,
                'last_publish' => 0,
                'item_last' => 0,
                'item_count' => 0,
                'item_lasttime' => $feed->getTimeCreated()
            ));
        }

        return $model->commit();
    }
}