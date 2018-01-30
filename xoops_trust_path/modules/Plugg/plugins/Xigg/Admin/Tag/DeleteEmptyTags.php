<?php
class Plugg_Xigg_Admin_Tag_DeleteEmptyTags extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/tag'));
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/tag'));
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'Admin_tag_delete_empty_tags')) {
            $context->response->setError($context->plugin->_('Invalid request'), array('path' => '/tag'));
            return;
        }
        if (false === $affected = $context->plugin->getModel()->getGateway('Tag')->deleteEmptyTags()) {
            $context->response->setError($context->plugin->_('An error occurred while deleting empty tags'), array('path' => '/tag/list'));
        } else {
            $context->response->setSuccess(sprintf($context->plugin->_('Deleted %s empty tag(s)'), $affected), array('path' => '/tag/list'));
        }
    }
}