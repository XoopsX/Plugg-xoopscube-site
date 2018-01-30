<?php
class Plugg_User_Main_Register extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if already registered
        if ($context->user->isAuthenticated()) {
            $context->response->setError(null, array('base' => '/user'));
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
            $manager->userRegister($context);
            return;
        }


        // Validate form and queue if valid
        $action = $this->_application->createUrl(array('path' => '/register'));
        $form = $this->getRegisterForm($context, $manager, $action);
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
                    $queue->set('type', Plugg_User_Plugin::QUEUE_TYPE_REGISTER);

                    if ('auto' == $activation_type = $context->plugin->getParam('userActivation')) {
                        // Activate user now
                        if ($identity = $manager->userRegisterSubmit($queue)) {
                            // Save extra data if any
                            if ($extra_data = $queue->getExtraData()) {
                                $context->plugin->createExtra($identity, $extra_data);
                            }
                            $context->response->setSuccess(
                                $context->plugin->_('You have been registered successfully. Please login using the username/password pair submitted during registration.'),
                                array('path' => '/login')
                            );
                            // Dispatch UserRegisterSuccess event
                            $this->_application->dispatchEvent('UserRegisterSuccess', array($identity));

                            return;
                        }
                    } else {
                        // Save registration data into queue
                        $queue->markNew();
                        if ($model->commit()) {
                            // Confirm by admin?
                            $confirm_by_admin = 'admin' == $activation_type;

                            // Send confirmation email
                            $context->plugin->sendRegisterConfirmEmail($queue, $manager, $confirm_by_admin);

                            if ($confirm_by_admin) {
                                $msg = $context->plugin->_('Registration data has been submitted successfully. Your account will be activated after confirmation by the administrator.');
                            } else {
                                $msg = $context->plugin->_('Registration data has been submitted successfully. Please check your email for further instruction.');
                            }
                            $context->response
                                //->setVar('content', $msg)
                                ->popContentName();
                            $context->response->pushContentName('plugg_user_content');

        $context->response->setPageInfo($context->plugin->_('Create account'));
        $this->_application->setData(array(
            'content' => $msg,
        ));
                            return;
                        }
                    }
                }
            }
        }

        // View
        $context->response->setPageInfo($context->plugin->_('Create account'));
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $this->renderRegisterForm($form, $manager)
        ));
    }
}
