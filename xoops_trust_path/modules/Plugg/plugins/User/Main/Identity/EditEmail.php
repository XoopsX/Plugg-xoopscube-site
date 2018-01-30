<?php
class Plugg_User_Main_Identity_EditEmail extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $manager = $context->plugin->getManagerPlugin();

        if ($manager instanceof Plugg_User_Manager_API) {
            $manager->userEditEmail($context, $this->_application->identity);
            return;
        }

        // Check permission
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission('user email edit any')) {
                $context->response->setError($context->plugin->_('Permission denied'));
                return;
            }
        } else {
            if (!$context->user->hasPermission('user email edit own')) {
                $context->response->setError($context->plugin->_('Permission denied'));
                return;
            }
        }

        // Validate form and submit
        $form = $this->_getForm($context, $manager, $this->_application->identity);
        $form->filterAll('trim');
        if ($form->validate()) {
            if ($context->request->getAsStr('form_submit_preview')) {
                $form->freeze();
                $form->addSubmitButtons(array(
                    $context->plugin->_('Back'),
                    'form_submit_submit' => $context->plugin->_('Submit')
                ));
            } elseif ($context->request->getAsStr('form_submit_submit')) {
                $model = $context->plugin->getModel();
                $queue = $model->create('Queue');
                if ($manager->userEditEmailQueueForm($queue, $form, $this->_application->identity)) {
                    $queue->set('key', md5(uniqid(mt_rand(), true)));
                    $queue->set('type', Plugg_User_Plugin::QUEUE_TYPE_EDITEMAIL);
                    $queue->set('identity_id', $this->_application->identity->getId());
                    $queue->markNew();
                    if ($model->commit()) {
                        // Process the queue right now if email address has not been modified
                        if ($this->_application->identity->getEmail() == $queue->get('notify_email')) {
                            $context->request->set('key', $queue->get('key'));
                            $this->forward('/user/confirm/' . $queue->getId(), $context);
                            return;
                        }

                        // Send confirmation email
                        $context->plugin->sendEditEmailConfirmEmail($queue, $manager);

                        $this->_application->content = $context->plugin->_('Email modification request has been submitted successfully. Please check your email for further instruction.');

                        $context->response->popContentName();
                        $context->response->pushContentName('plugg_user_content');

                        return;
                    }
                }
            }
        }

        // View
        $context->response->setPageInfo($context->plugin->_('Edit email address'));
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $manager->userEditEmailRenderForm($form),
        ));
    }

    private function _getForm(Sabai_Application_Context $context, $manager)
    {
        $action = $this->_application->createUrl(array(
            'path' => '/edit_email'
        ));
        if (!$form = $manager->userEditEmailGetForm($this->_application->identity, $action)) {
            require_once 'Sabai/HTMLQuickForm.php';
            $form = new Sabai_HTMLQuickForm();
            $emails[] = $form->createElement(
                'text',
                'email',
                array(
                    $context->plugin->_('New email address'),
                    $context->plugin->_('Enter your new email address')
                ),
                array('size' => 50, 'maxlength' => 255, 'tabindex' => 1)
            );
            $emails[] = $form->createElement(
                'text',
                'email_confirm',
                array($context->plugin->_('Confirm email address'), $context->plugin->_('Enter again for confirmation')),
                array('size' => 50, 'maxlength' => 255, 'tabindex' => 2)
            );
            $form->addGroup(
                $emails,
                'emails',
                array(
                    $context->plugin->_('Email address'),
                    null,
                    $context->plugin->_('Please enter a valid email address for yourself. We will send you an email shortly that you must confirm to complete the process.')
                ),
                '',
                false
            );
            $form->addGroupRule('emails', array(
                'email' => array(
                    array($context->plugin->_('Email is required'), 'required', null, 'client'),
                    array($context->plugin->_('Invalid email address'), 'email', false, 'client'),
                ),
                'email_confirm' => array(
                    array($context->plugin->_('Please enter email address two times'), 'required', null, 'client'),
                    array($context->plugin->_('Invalid email address'), 'email', false, 'client'),
                ),
            ));
            $form->addFormRule(array($this, '_validateForm'), array($context));
            $form->setDefaults(array(
                'email' => $email = $this->_application->identity->getEmail(),
                'email_confirm' => $email,
            ));
            $form->useToken(get_class($this));
        }
        $form->addSubmitButtons(array(
            'form_submit_preview' => $context->plugin->_('Confirm'),
            'form_submit_submit' => $context->plugin->_('Submit')
        ));
        return $form;
    }

    function _validateForm($values, $files, $context)
    {
        if (!empty($values['email']) &&
            !empty($values['email_confirm']) &&
            $values['email'] != $values['email_confirm']
        ) {
            $ret['emails'] = $context->plugin->_('The email addresses do not match');
        }
        return empty($ret) ? true : $ret;
    }
}