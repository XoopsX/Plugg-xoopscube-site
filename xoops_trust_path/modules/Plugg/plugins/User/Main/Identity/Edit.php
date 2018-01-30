<?php
require_once 'Plugg/FormController.php';

class Plugg_User_Main_Identity_Edit extends Plugg_FormController
{
    private $_manager;

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_manager = $context->plugin->getManagerPlugin();

        // Is it an API type plugin?
        if ($this->_manager instanceof Plugg_User_Manager_API) {
            $this->_manager->userEdit($context, $this->_application->identity);
            return;
        }

        // Check permission if other user's profile
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission('user profile edit any')) {
                $context->response->setError($context->plugin->_('Permission denied'));
                return false;
            }
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        // Craete base form
        require_once $context->plugin->getPath() . '/ProfileForm.php';
        $action = $this->_application->createUrl(array(
            'path' => '/edit'
        ));
        $form = new Plugg_User_ProfileForm('UserIdentityEdit', 'post', $action);
        $this->_manager->userEditInitForm($this->_application->identity, $form);

        // Any extra user fields?
        if ($extra = $this->getExtraByIdentity($context, $this->_application->identity)) {
            $extra_data = $extra->getData();
        } else {
            $extra_data = array();
        }

        $this->addExtraFormFields($context, $form, $this->_application->identity, $extra_data);

        return $form;
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $extra_values = $this->extractExtraFormFieldValues($context, $form);
        if ($identity = $this->_manager->userEditSubmitForm($this->_application->identity, $form)) {
            $this->_updateExtra($context, $identity, $extra_values);
            $context->response->setSuccess($context->plugin->_('User data updated successfully'));
            $this->_application->dispatchEvent('UserIdentityEditSuccess', array($identity));
            return true;
        }
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $context->response->setPageInfo($context->plugin->_('Edit profile'));
        $this->_application->setData(array(
            'form' => $form,
            'form_rendered' => $this->_manager->userEditRenderForm($form),
        ));
    }

    private function _updateExtra(Sabai_Application_Context $context, $identity, $extraData)
    {
        $model = $context->plugin->getModel();
        if (!$extra = $this->getExtraByIdentity($context, $identity)) {
            $extra = $model->create('Extra');
            $extra->setVar('userid', $identity->getId());
            $extra->markNew();
        }
        $data = array();
        foreach ($model->Field->criteria()->active_is(1)->fetch('field_plugin') as $field) {
            $plugin_name = $field->get('plugin');
            if (!$field_plugin = $this->_application->getPlugin($plugin_name)) {
                continue;
            }
            $field_name = $field->get('name');
            if ($field_data = @$extraData[$plugin_name][$field_name]) {
                $plugin_lib = $field_plugin->getLibrary();
                list($filtered_value, $filter_id) = $field_data['filter'];
                $data[$plugin_lib][$plugin_name][$field_name] = array(
                    'value' => $field_plugin->userFieldSubmit(
                        $field_name,
                        $field_data['value'],
                        $identity,
                        $filtered_value,
                        $filter_id
                    ),
                    'visibility' => $field_data['visibility']
                );
            }
        }
        $extra->setData($data);
        return $extra->commit();
    }
}