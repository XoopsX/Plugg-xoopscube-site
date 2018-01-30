<?php
class Plugg_Message_User_Message_View extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $message = $this->_application->message;

        // Mark message as read if still unread
        if (!$message->isRead()) {
            $message->markRead();
            $message->commit();

            // Clear inbox user menu in session
            $this->_application->getPlugin('user')->clearMenuInSession($context->plugin->getName(), 'inbox');
        }

        $context->response->setPageTitle('');
        $this->_application->setData(array(
            'message' => $message,
            'message_from_to_user' => $this->_application->getService('UserIdentityFetcher')
                ->fetchUserIdentity($message->get('from_to'), true),
        ));
    }
}