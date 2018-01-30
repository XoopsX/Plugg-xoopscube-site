<?php
class Plugg_User_Main_RegisterAuth extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if properly coming from authentication
        if (empty($_SESSION['Plugg_User_Main_Login_auth']['timestamp']) ||
            $_SESSION['Plugg_User_Main_Login_auth']['timestamp'] < time() - 300
        ) {
            $context->response->setError($context->plugin->_('Invalid request'), array('base' => '/user'));
            return;
        }

        // Check if already registered
        if ($context->user->isAuthenticated()) {
            $context->response->setError($context->plugin->_('Invalid request'), array('base' => '/user'));
            unset($_SESSION['Plugg_User_Main_Login_auth']);
            return;
        }

        // Check if user account plugin is valid
        if ((!$manager_name = $context->plugin->getParam('userManagerPlugin')) ||
            (!$manager = $this->_application->getPlugin($manager_name)) ||
            ($manager instanceof Plugg_User_Manager_API)
        ) {
            $context->response->setError($context->plugin->_('Invalid request'));
            unset($_SESSION['Plugg_User_Main_Login_auth']);
            return;
        }

        // Update auth data timestamp in session
        $_SESSION['Plugg_User_Main_Login_auth']['timestamp'] = time();

        $context->response->setPageInfo($context->plugin->_('Create account'));

        // Validate form and queue if valid
        $action = $this->_application->createUrl(array('path' => '/register_auth'));
        $form = $this->getRegisterForm($context, $manager, $action);
        $form->addHeader(sprintf(
            $context->plugin->_('If you already have a user account, you can <a href="%s">associate the submitted authentication with that account</a>.'),
            $this->_application->createUrl(array('base' => '/user', 'path' => '/associate_auth'))
        ));
        if ($form->validate()) {
            if ($context->request->getAsStr('form_submit_preview')) {
                $form->freeze();
                $form->addSubmitButtons(array(
                    $context->plugin->_('Back'),
                    'form_submit_submit' => $context->plugin->_('Register')
                ));
            } elseif ($context->request->getAsStr('form_submit_submit')) {
                $extra_values = $this->extractExtraFormFieldValues($context, $form);
                $model = $context->plugin->getModel();
                $queue = $model->create('Queue');
                if ($manager->userRegisterQueueForm($queue, $form)) {
                    $queue->setExtraData($extra_values);
                    $queue->set('key', md5(uniqid(mt_rand(), true)));
                    $queue->set('type', Plugg_User_Plugin::QUEUE_TYPE_REGISTERAUTH);
                    $queue->setAuthData($_SESSION['Plugg_User_Main_Login_auth']);

                    if ('auto' == $activation_type = $context->plugin->getParam('userActivation')) {
                        // Activate user now
                        if ($identity = $manager->userRegisterSubmit($queue)) {
                            unset($_SESSION['Plugg_User_Main_Login_auth']);

                            // Save extra data if any
                            if ($extra_data = $queue->getExtraData()) {
                                $context->plugin->createExtra($identity, $extra_data);
                            }

                            $context->response->setSuccess(
                                $context->plugin->_('You have been registered successfully. Please login using the username/password pair submitted during registration.'),
                                array('path' => '/login')
                            );

                            // Save associated authentication data
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
                                    'params' => array('_auth' => $auth_data['type'])
                                ));
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

                            return;
                        }
                    } else {
                        // Save registration data into queue
                        $queue->markNew();
                        if ($model->commit()) {
                            unset($_SESSION['Plugg_User_Main_Login_auth']);

                            // Confirm by admin?
                            $confirm_by_admin = 'admin' == $context->plugin->getParam('userActivation');

                            // Send confirmation email
                            $context->plugin->sendRegisterConfirmEmail($queue, $manager, $confirm_by_admin);

                            if ($confirm_by_admin) {
                                $msg = $context->plugin->_('Registration data has been submitted successfully. Your account will be activated after confirmation by the administrator.');
                            } else {
                                $msg = $context->plugin->_('Registration data has been submitted successfully. Please check your email for further instruction.');
                            }
                            $context->response
                                ->setVar('content', $msg)
                                ->popContentName();
                            $context->response->pushContentName('plugg_user_content');

                            return;
                        }
                    }
                }
            }
        }

        // View
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $this->renderRegisterForm($form, $manager)
        ));
    }
}