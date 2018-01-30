<?php
class Plugg_Message_User_Submit extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $messages_type = $context->request->getAsInt(
            'messages_type',
            Plugg_Message_Plugin::MESSAGE_TYPE_INCOMING,
            array(Plugg_Message_Plugin::MESSAGE_TYPE_INCOMING, Plugg_Message_Plugin::MESSAGE_TYPE_OUTGOING)
        );
        $url = array(
            'base' => '/user/' . $this->_application->identity->getId() . '/message',
            'params' => array('messages_type' => $messages_type)
        );

        if (!$context->request->isPost()) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        if (!$messages = $context->request->getAsArray('messages')) {
            $context->response->setError(null, $url);
            return;
        }
        if (!$token_value = $context->request->getAsStr('_TOKEN', false)) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, 'message_messages_submit')) {
            $context->response->setError($context->plugin->_('Invalid request'), $url);
            return;
        }

        if ($context->request->getAsStr('submit_delete')) {
            $action = 'delete';
        } else {
            $action = $context->request->getAsStr('submit_action');
            $actions_allowed = array('read', 'unread', 'star', 'unstar');
            if (!in_array($action, $actions_allowed)) {
                $context->response->setError($context->plugin->_('Invalid request'), $url);
                return;
            }
        }

        $model = $context->plugin->getModel();
        $messages_current = $model->Message
            ->criteria()
            ->userid_is($this->_application->identity->getId())
            ->type_is($messages_type)
            ->id_in($messages)
            ->fetch();

        switch ($action) {
            case 'delete':
                foreach ($messages_current as $message) {
                    $message->markRemoved();
                }
                break;
            case 'read':
                foreach ($messages_current as $message) {
                    $message->set('read', 1);
                }
                break;
            case 'unread':
                foreach ($messages_current as $message) {
                    $message->set('read', 0);
                }
                break;
            case 'star':
                foreach ($messages_current as $message) {
                    $message->set('star', 1);
                }
                break;
            case 'unstar':
                foreach ($messages_current as $message) {
                    $message->set('star', 0);
                }
                break;
            default:
        }

        if (false === $num = $model->commit()) {
            $context->response->setError($context->plugin->_('An error occurred while updating messages.'), $url);
        } else {
            // Clear inbox user menu in session if read or delete action
            if (in_array($action, array('delete', 'read', 'unread'))) {
                $this->_application->getPlugin('user')->clearMenuInSession($context->plugin->getName(), 'inbox');
            }
            $context->response->setSuccess(sprintf($context->plugin->_('%d message(s) updated successfully.'), $num), $url);
        }
    }
}