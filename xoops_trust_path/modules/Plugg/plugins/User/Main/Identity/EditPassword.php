<?php
class Plugg_User_Main_Identity_EditPassword extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $manager = $context->plugin->getManagerPlugin();

        if ($manager instanceof Plugg_User_Manager_API) {
            $manager->userEditPassword($context, $this->_application->identity);
            return;
        }

        // Check permission if other user's profile
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission('user profile edit any')) {
                $context->response->setError($context->plugin->_('Permission denied'));
                return;
            }
        }

        // Validate form and submit
        $form = $this->_getForm($context, $manager, $this->_application->identity);
        if ($form->validate()) {
            if ($manager->userEditPasswordSubmitForm($this->_application->identity, $form)) {
                $context->response->setSuccess(
                    $context->plugin->_('User data updated successfully')
                );
                $this->_application->dispatchEvent('UserIdentityEditPasswordSuccess', array($this->_application->identity));
                return;
            }
        }

        // View
        $context->response->setPageInfo($context->plugin->_('Edit password'));
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $manager->userEditPasswordRenderForm($form),
        ));
    }

    private function _getForm(Sabai_Application_Context $context, $manager)
    {
        $action = $this->_application->createUrl(array(
            'path' => '/edit_password'
        ));
        if (!$form = $manager->userEditPasswordGetForm($this->_application->identity, $action)) {
            require_once 'Sabai/HTMLQuickForm.php';
            $form = new Sabai_HTMLQuickForm();
            $passwords[] = $form->createElement(
                'password',
                'password',
                array(
                    $context->plugin->_('New password'),
                    $context->plugin->_('Enter your new password')
                ),
                array('size' => 50, 'maxlength' => 255, 'tabindex' => 1)
            );
            $passwords[] = $form->createElement(
                'password',
                'password_confirm',
                array(
                    $context->plugin->_('Confirm password'),
                    $context->plugin->_('Enter again for confirmation')
                ),
                array('size' => 50, 'maxlength' => 255, 'tabindex' => 2)
            );
            $form->addGroup(
                $passwords,
                'passwords',
                array(
                    $context->plugin->_('Edit password'),
                    null,
                    $context->plugin->_('Enter passwords two times to change your password.')
                ),
                '',
                false
            );
            $form->addGroupRule('passwords', array(
                'password' => array(
                    array($context->plugin->_('Password is required'), 'required', null, 'client'),
                ),
                'password_confirm' => array(
                    array($context->plugin->_('Please enter password two times'), 'required', null, 'client'),
                ),
            ));
            $form->addFormRule(array($this, '_validateForm'));
            $form->useToken(get_class($this));
        }
        $form->addSubmitButtons($context->plugin->_('Submit'));
        return $form;
    }

    function _validateForm($values, $files)
    {
        if (!empty($values['password']) &&
            !empty($values['password_confirm']) &&
            $values['password'] != $values['password_confirm']
        ) {
            $ret['passwords'] = _('The passwords do not match');
        }
        return empty($ret) ? true : $ret;
    }
}