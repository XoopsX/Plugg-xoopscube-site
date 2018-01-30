<?php
class Plugg_User_Main_Logout extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if logged in
        if (!$context->user->isAuthenticated()) {
            $context->response->setError();
            return;
        }

        // Check if user account plugin is valid
        if ((!$manager_name = $context->plugin->getParam('userManagerPlugin')) ||
            (!$manager = $this->_application->getPlugin($manager_name))
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }

        // Is it an API type plugin?
        if ($manager instanceof Plugg_User_Manager_API) {
            $manager->userLogout($context);
            return;
        }

        if ($manager->userLogoutUser($context->user->getIdentity())) {
            $context->user->endSession();
            $context->response->setSuccess(
                $context->plugin->_('You have logged out successfully.'),
                array('base' => '/')
            );
            $this->_application->dispatchEvent('UserLogoutSuccess', array($context->user));
        } else {
            $context->response->setError(
                $context->plugin->_('An error occurred'),
                array('base' => '/user')
            );
        }
    }
}