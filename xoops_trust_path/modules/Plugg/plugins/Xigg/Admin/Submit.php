<?php
class Plugg_Xigg_Admin_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ($category_id = $context->request->getAsInt('category_id', false)) {
            $error_url = $success_url = array('path' => '/category/' . $category_id);
        } elseif ($tag_id = $context->request->getAsInt('tag_id', false)) {
            $error_url = $success_url = array('path' => '/tag/' . $tag_id);
        } else {
            $error_url = $success_url = array('path' => '/node');
        }
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (!$nodes = $context->request->getAsArray('nodes')) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'Admin_node_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $error_url);
            return;
        }
        foreach (array('publish', 'hide', 'unhide', 'delete') as $action) {
            if ($context->request->getAsBool($action, false)) {
                break;
            }
        }
        switch ($action) {
            case 'publish':
                if (false === $num = $this->_publish($context, $nodes)) {
                    $context->response->setError($context->plugin->_('Could not publish selected nodes'), $error_url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d node(s) published successfully'), $num), $success_url);
                }
                break;
            case 'hide':
                if (false === $num = $this->_hide($context, $nodes)) {
                    $context->response->setError($context->plugin->_('Could not hide selected nodes'), $error_url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d node(s) hidden successfully'), $num), $success_url);
                }
                break;
            case 'unhide':
                if (false === $num = $this->_unhide($context, $nodes)) {
                    $context->response->setError($context->plugin->_('Could not unhide selected nodes'), $error_url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d node(s) unhidden successfully'), $num), $success_url);
                }
                break;
            case 'delete':
                if (false === $num = $this->_delete($context, $nodes)) {
                    $context->response->setError($context->plugin->_('Could not delete selected nodes'), $error_url);
                } else {
                    $context->response->setSuccess(sprintf($context->plugin->_('%d node(s) deleted successfully'), $num), $success_url);
                }
                break;
            default:
                $context->response->setError($context->plugin->_('Invalid request'), $error_url);
        }
    }

    private function _publish(Sabai_Application_Context $context, $nodeIds)
    {
        $model = $context->plugin->getModel();
        $nodes = $model->Node
            ->criteria()
            ->status_isNot(Plugg_Xigg_Plugin::NODE_STATUS_PUBLISHED)
            ->id_in($nodeIds)
            ->fetch();
        foreach ($nodes as $node) {
            $node->publish();
        }
        return $model->commit();
    }

    private function _hide(Sabai_Application_Context $context, $nodeIds)
    {
        $model = $context->plugin->getModel();
        $nodes = $model->Node
            ->criteria()
            ->hidden_is(0)
            ->id_in($nodeIds)
            ->fetch();
        foreach ($nodes as $node) {
            $node->hide();
        }
        return $model->commit();
    }

    private function _unhide(Sabai_Application_Context $context, $nodeIds)
    {
        $model = $context->plugin->getModel();
        $nodes = $model->Node
            ->criteria()
            ->hidden_is(1)
            ->id_in($nodeIds)
            ->fetch();
        foreach ($nodes as $node) {
            $node->unhide();
        }
        return $model->commit();
    }

    private function _delete(Sabai_Application_Context $context, $nodeIds)
    {
        $model = $context->plugin->getModel();
        $nodes = $model->Node
            ->criteria()
            ->id_in($nodeIds)
            ->fetch();
        foreach ($nodes as $node) {
            $node->markRemoved();
        }
        return $model->commit();
    }
}