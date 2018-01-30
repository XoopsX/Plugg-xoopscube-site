<?php
class Plugg_Message_User_Message_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $message = $this->_application->message;

        $url = array(
            'base' => '/user/' . $this->_application->identity->getId() . '/message',
            'params' => array(
                'messages_type' => $message->get('type'),
            )
        );

        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        $actions_allowed = array('star', 'delete', 'read');
        if (!$action = $context->request->getAsStr('submit_action', false, $actions_allowed)) {
            foreach ($actions_allowed as $action_name) {
                if ($context->request->getAsStr('submit_action_' . $action_name)) {
                    $action = $action_name;
                    break;
                }
            }
            if (empty($action)) {
                $context->response->setError($context->plugin->_('Invalid request'), $url);
                return;
            }
        }

        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'message_message_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        switch ($action) {
            case 'star':
                $message->markStarred(!$message->isStarred());
                break;
            case 'delete':
                $message->markRemoved();
                break;
            case 'read':
                $message->markRead(!$message->isRead());
                break;
        }

        if (!$message->commit()) {
            $context->response->setError($context->plugin->_('Message could not be updated.'), $url);
        } else {
            // Clear inbox user menu in session if read or delete action
            if (in_array($action, array('delete', 'read'))) {
                $this->_application->getPlugin('user')->clearMenuInSession($context->plugin->getName(), 'inbox');
            }
            $context->response->setSuccess($context->plugin->_('Message updated successfully.'), $url);
        }
    }
}