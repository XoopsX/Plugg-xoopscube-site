<?php
class Plugg_User_Main_RequestPassword extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Check if already registered and logged in
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
            $manager->userRequestPassword($context);
            return;
        }

        // Validate form and queue if valid
        $form = $this->_getForm($context, $manager);
        $form->filterAll('trim');
        if ($form->validate()) {
            $model = $context->plugin->getModel();
            $queue = $model->create('Queue');
            if ($manager->userRequestPasswordQueueForm($queue, $form) &&
                ($identity_id = $queue->get('identity_id')) // make sure identity id is set by the manager
            ) {
                $identity = $this->_application->getService('UserIdentityFetcher')
                    ->fetchUserIdentity($identity_id);
                if (!$identity->isAnonymous()) {
                    $queue->set('key', md5(uniqid(mt_rand(), true)));
                    $queue->set('type', Plugg_User_Plugin::QUEUE_TYPE_REQUESTPASSWORD);
                    $queue->markNew();
                    if ($queue->commit()) {
                        // Send confirmation email
                        $context->plugin->sendRequestPasswordConfirmEmail($queue, $identity, $manager);

                        $context->response
                            ->setVar(
                                'content',
                                $context->plugin->_('Password request has been submitted successfully. Please check your email for further instruction.')
                            )
                            ->popContentName();
                        $context->response->pushContentName('plugg_user_content');

                        return;
                    }
                }
            }
        }

        // View
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $manager->userRequestPasswordRenderForm($form),
        ));
        $context->response->setPageInfo($context->plugin->_('Request password'));
    }

    private function _getForm(Sabai_Application_Context $context, $manager)
    {
        $action = $this->_application->createUrl(array('path' => '/request_password'));
        if (!$form = $manager->userRequestPasswordGetForm($action)) {
            require_once 'Sabai/HTMLQuickForm.php';
            $form = new Sabai_HTMLQuickForm();
            $form->addHeader(
                $context->plugin->_('If you have forgotten your username or password, you can request to have your username emailed to you and to reset your password. When you fill in your registered email address, you will be sent instructions on how to reset your password.')
            );
            $form->addElement(
                'text',
                'email',
                array(
                    $context->plugin->_('Email address'),
                    $context->plugin->_('Enter your registered email address')
                ),
                array('size' => 50, 'maxlength' => 255)
            );
            $form->setRequired('email', $context->plugin->_('Email address is required'), true, $context->plugin->_(' '));
            $form->addRule('email', $context->plugin->_('Invalid email address'), 'email', false, 'client');
            $form->useToken(get_class($this));
        }
        $form->addSubmitButtons($context->plugin->_('Request password'));
        return $form;
    }
}