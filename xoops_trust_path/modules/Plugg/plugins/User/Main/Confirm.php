<?php
class Plugg_User_Main_Confirm extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Redirected from the admin page?
        $fromAdmin = $context->user->isSuperUser() && $context->request->getAsBool('admin');

        // Check if user account plugin is valid
        if ((!$manager_name = $context->plugin->getParam('userManagerPlugin')) ||
            (!$manager = $this->_application->getPlugin($manager_name)) ||
            $manager instanceof Plugg_User_Manager_API
        ) {
            $context->response->setError(
                $context->plugin->_('Invalid request'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('base' => '/user')
            );

            return;
        }

        // Check if confirmation request
        if ((!$queue = $this->isValidQueueRequested($context))) {
            $context->response->setError(
                $context->plugin->_('Invalid request'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('base' => '/user')
            );

            return;
        }

        // Remove queue
        $queue->markRemoved();
        if (!$queue->commit()) {
            $context->response
                ->setVar(
                    'content',
                    $context->plugin->_('An error occurred. Please click on the email link again to complete the process.')
                )
                ->popContentName();
            $context->response->pushContentName('plugg_user_content');

            return;
        }

        switch ($queue->get('type')) {
            case Plugg_User_Plugin::QUEUE_TYPE_REGISTER:
                $this->_processRegisterQueue($context, $queue, $manager, $fromAdmin);
                break;
            case Plugg_User_Plugin::QUEUE_TYPE_REQUESTPASSWORD:
                $this->_processRequestPasswordQueue($context, $queue, $manager, $fromAdmin);
                break;
            case Plugg_User_Plugin::QUEUE_TYPE_EDITEMAIL:
                $this->_processEditEmailQueue($context, $queue, $manager, $fromAdmin);
                break;
            case Plugg_User_Plugin::QUEUE_TYPE_REGISTERAUTH:
                $this->_processRegisterAuthQueue($context, $queue, $manager, $fromAdmin);
                break;
            default:
                $context->response->setError(
                    $context->plugin->_('Invalid request'),
                    $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('base' => '/user')
                );
        }
    }

    private function _processRegisterQueue(Sabai_Application_Context $context, $queue, $manager, $fromAdmin)
    {
        if ($identity = $manager->userRegisterSubmit($queue)) {
            // Save extra data if any
            if ($extra_data = $queue->getExtraData()) {
                $context->plugin->createExtra($identity, $extra_data);
            }
            $context->response->setSuccess(
                $context->plugin->_('You have been registered successfully. Please login using the username/password pair submitted during registration.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('path' => '/login')
            );
            // Dispatch UserRegisterSuccess event
            $this->_application->dispatchEvent('UserRegisterSuccess', array($identity));
        } else {
            $context->response->setError(
                $context->plugin->_('An error occurred. Please fill in the registration form again to register.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('base' => '/user/register')
            );
        }
    }

    private function _processRequestPasswordQueue(Sabai_Application_Context $context, $queue, $manager, $fromAdmin)
    {
        $identity = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($queue->get('identity_id'));
        if (!$identity->isAnonymous() && ($password = $manager->userRequestPasswordSubmit($queue))) {
            $this->_sendNewPasswordEmail($context, $identity, $password, $manager);
            $context->response->setSuccess(
                $context->plugin->_('Your new password has been sent to your registered email address.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('path' => '/login')
            );
            // Dispatch UserRequestPasswordSuccess event
            $this->_application->dispatchEvent('UserRequestPasswordSuccess', array($identity));
        } else {
            $context->response->setError(
                $context->plugin->_('An error occurred. Please submit the password request form again.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('base' => '/user/request_password')
            );
        }
    }

    private function _processEditEmailQueue(Sabai_Application_Context $context, $queue, $manager, $fromAdmin)
    {
        $identity = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($queue->get('identity_id'));
        if (!$identity->isAnonymous() && $manager->userEditEmailSubmit($queue, $identity)) {
            $context->response->setSuccess(
                $context->plugin->_('Your email address has been updated successfully.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : null
            );
            // Dispatch UserEditEmailSuccess event
            $this->_application->dispatchEvent('UserEditEmailSuccess', array($identity));
        } else {
            $context->response->setError(
                $context->plugin->_('An error occurred. Please submit the form again to change your email address.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('base' => '/user/edit_email')
            );
        }
    }

    private function _processRegisterAuthQueue(Sabai_Application_Context $context, $queue, $manager, $fromAdmin)
    {
        if ($identity = $manager->userRegisterSubmit($queue)) {
            // Save extra data if any
            if ($extra_data = $queue->getExtraData()) {
                $context->plugin->createExtra($identity, $extra_data);
            }
            $context->response->setSuccess(
                $context->plugin->_('You have been registered successfully. Please login using the username/password pair submitted during registration.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('path' => '/login')
            );
            $auth_data = $queue->getAuthData();
            if ($context->plugin->createAuthdata($auth_data, $identity->getId())) {
                $context->response->addMessage(
                    sprintf(
                        $context->plugin->_('Additionally, your external authentication data using %s has been associated with the created account. You can also use that authentication data to login.'),
                        $auth_data['type']
                    ),
                    Sabai_Response::MESSAGE_SUCCESS
                );
            } else {
                $url = $this->_application->createUrl(array(
                    'base' => '/user',
                    'path' => '/login',
                    'params' => array('_auth' => $auth_data['type']))
                );
                $context->response->addMessage(
                    sprintf(
                        $context->plugin->_('An error has occurred while associating your external authentication data with the created account. Please <a href="%s">login again using %s</a> if you need to associate the authentication data.'),
                        $url,
                        $auth_data['type']
                    ),
                    Sabai_Response::MESSAGE_ERROR
                );
            }
            // Dispatch UserRegisterSuccess event
            $this->_application->dispatchEvent('UserRegisterSuccess', array($identity));
        } else {
            $context->response->setError(
                $context->plugin->_('An error occurred. Please fill in the registration form again to register.'),
                $fromAdmin ? array('script_alias' => 'admin', 'base' => '/user/queue') : array('base' => '/user/register')
            );
        }
    }

    private function _sendNewPasswordEmail($context, $identity, $newPassword, $manager)
    {
        $replacements = array(
            '{SITE_NAME}' => $this->_application->getConfig('siteName'),
            '{SITE_URL}' => $this->_application->getConfig('siteUrl'),
            '{USER_NAME}' => $identity->getUsername(),
            '{USER_PASSWORD}' => $newPassword,
            '{LOGIN_LINK}' => $this->_application->createUrl(array('path' => '/login')),
            '{IP}' => getip()
        );
        $subject = sprintf($context->plugin->_('New password request at %s'), $this->_application->getConfig('siteName'));
        $body = strtr($context->plugin->getParam('newPasswordEmail'), $replacements);

        return $this->_application->getPlugin('mail')
            ->getSender()
            ->mailSend($identity->getEmail(), $subject, $body);
    }
}
