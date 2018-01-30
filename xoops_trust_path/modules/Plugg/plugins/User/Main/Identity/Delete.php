<?php
class Plugg_User_Main_Identity_Delete extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $manager = $context->plugin->getManagerPlugin();

        if ($manager instanceof Plugg_User_Manager_API) {
            $manager->userDelete($context, $this->_application->identity);
            return;
        }

        // Check permissions
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission('user profile delete any')) {
                $context->response->setError($context->plugin->_('You are not allowed to delete other user profile.'));
                return;
            }
        } else {
            if (!$context->user->hasPermission('user profile delete own') ||
                $context->user->isSuperUser() // Not allowed to delete oneself if super user
            ) {
                $context->response->setError($context->plugin->_('You are not allowed to delete your own profile.'));
                return;
            }
        }

        // Validate form and submit
        $form = $this->_getForm($context, $this->_application->identity);
        if ($form->validate()) {
            if ($manager->userDeleteSubmit($this->_application->identity)) {
                $context->response->setSuccess($context->plugin->_('User data removed successfully'));
                $this->_application->dispatchEvent('UserIdentityDeleteSuccess', array($this->_application->identity));
                return;
            }
        }

        // view
        $context->response->setPageInfo($context->plugin->_('Delete account'));
        $this->_application->setData(array(
            'form' => $form,
        ));
    }

    function _getForm(Sabai_Application_Context $context)
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm();
        $form->addElement(
            'static',
            '',
            $context->plugin->_('Name'),
            h($this->_application->identity->getUsername())
        );
        $form->addSubmitButtons($context->plugin->_('Delete account'));
        $form->useToken(get_class($this));
        return $form;
    }
}