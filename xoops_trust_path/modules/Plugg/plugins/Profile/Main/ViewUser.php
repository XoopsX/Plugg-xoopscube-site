<?php
class Plugg_Profile_Main_ViewUser extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check user_name parameter
        if (!$user_name = $context->request->getAsStr('user_name')) {
            if (!$context->user->isAuthenticated()) {
                $context->response->setError($context->plugin->_('Invalid request'), array('base' => '/'));
            } else {
                $this->forward('/user/' . $context->user->getId(), $context);
            }
            return;
        }

        // Fetch identity
        $identity = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentityByUsername($user_name);

        // User with the requested user name does not exist
        if ($identity->isAnonymous()) {
            $context->response->setError($context->plugin->_('Invalid request'), array('base' => '/'));
            return;
        }

        // Forward to the user plugin profile page
        $route = sprintf('/user/%d/%s', $identity->getId(), $context->route);
        $this->forward($route, $context);
    }
}