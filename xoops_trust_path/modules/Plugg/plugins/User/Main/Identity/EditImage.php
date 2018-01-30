<?php
class Plugg_User_Main_Identity_EditImage extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $manager = $context->plugin->getManagerPlugin();

        if ($manager instanceof Plugg_User_Manager_API) {
            $manager->userEditImage($context, $this->_application->identity);
            return;
        }

        // Check permission
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission('user image edit any')) {
                $context->response->setError($context->plugin->_('Permission denied'));
                return;
            }
        } else {
            if (!$context->user->hasPermission('user image edit own')) {
                $context->response->setError($context->plugin->_('Permission denied'));
                return;
            }
        }

        // Validate form and submit
        $form = $this->_getForm($context, $manager, $this->_application->identity);
        if ($form->validate()) {
            if ($manager->userEditImageSubmitForm($this->_application->identity, $form)) {
                $context->response->setSuccess(
                    $context->plugin->_('User data updated successfully')
                );
                $this->_application->dispatchEvent('UserIdentityEditImageSuccess', array($this->_application->identity));
                return;
            }
        }

        // View
        $context->response->setPageInfo($context->plugin->_('Edit image'));
        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $manager->userEditImageRenderForm($form),
        ));
    }

    private function _getForm(Sabai_Application_Context $context, $manager)
    {
        $action = $this->_application->createUrl(array(
            'path' => '/edit_image'
        ));
        $form = $manager->userEditImageGetForm($this->_application->identity, $action);
        $form->addSubmitButtons($context->plugin->_('Submit'));
        return $form;
    }
}