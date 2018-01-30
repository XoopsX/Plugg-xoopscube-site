<?php
class Plugg_Aggregator_Admin_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ($feed_id = $context->request->getAsInt('feed_id', false)) {
            $url = array(
                'path' => '/feed/' . $feed_id,
                'params' => array(
                    'page' => $context->request->getAsInt('page', 1)
                )
            );
        } else {
            $url = array('params' => array('page' => $context->request->getAsInt('page', 1)));
        }


        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$items = $context->request->getAsArray('items')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'aggregator_admin_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        foreach (array('hide', 'unhide', 'delete') as $action) {
            if ($context->request->getAsBool($action, false)) {
                break;
            }
        }
        switch ($action) {
            case 'hide':
                if (false === $num = $this->_hide($context, $items)) {
                    $context->response->setError($context->plugin->_('Could not hide selected items'), $url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d items hidden successfully'), $num), $url);
                }
                break;
            case 'unhide':
                if (false === $num = $this->_unhide($context, $items)) {
                    $context->response->setError($context->plugin->_('Could not unhide selected items'), $url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d items unhidden successfully'), $num), $url);
                }
                break;
            case 'delete':
                if (false === $num = $this->_delete($context, $items)) {
                    $context->response->setError($context->plugin->_('Could not delete selected items'), $url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d items deleted successfully'), $num), $url);
                }
                break;
            default:
                $context->response->setError($context->plugin->_('Invalid request'), $url);
        }
    }

    private function _hide($context, $itemIds)
    {
        $model = $context->plugin->getModel();
        $items = $model->Item
            ->criteria()
            ->hidden_is(0)
            ->id_in($itemIds)
            ->fetch()
            ->with('Feed');
        $feeds = array();
        foreach ($items as $item) {
            $item->hidden = 1;
            $feeds[$item->Feed->getId()] = $item->Feed;
        }

        // We need to commit items first to update item data
        if (!$ret = $model->commit()) return false;

        foreach ($feeds as $feed) {
            $feed->updateLastPublished(false);
        }
        $model->commit();

        return $ret;
    }

    private function _unhide($context, $itemIds)
    {
        $model = $context->plugin->getModel();
        $items = $model->Item
            ->criteria()
            ->hidden_is(1)
            ->id_in($itemIds)
            ->fetch()
            ->with('Feed');
        $feeds = array();
        foreach ($items as $item) {
            $item->hidden = 0;
            $feeds[$item->Feed->getId()] = $item->Feed;
        }

        // We need to commit items first to update item data
        if (!$ret = $model->commit()) return false;

        foreach ($feeds as $feed) {
            $feed->updateLastPublished(false);
        }
        $model->commit();

        return $ret;
    }

    private function _delete($context, $itemIds)
    {
        $model = $context->plugin->getModel();
        $items = $model->Item
            ->criteria()
            ->id_in($itemIds)
            ->fetch();
        $feed_ids = array();
        foreach ($items as $item) {
            $item->markRemoved();
            $feed_ids[$item->getVar('feed_id')] = true;
        }

        // We need to commit items first to update feed counter data
        if (!$ret = $model->commit()) return false;

        $feeds = $model->Feed->criteria()->id_in(array_keys($feed_ids))->fetch();
        foreach ($feeds as $feed) {
            $feed->updateLastPublished(false);
        }
        $model->commit();

        return $ret;
    }
}