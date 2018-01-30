<?php
class Plugg_System_Admin_ClonePlugin extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$plugin_library = $context->request->getAsStr('plugin_library')) {
            $context->response->setError($context->plugin->_('No plugin library specified'), array('base' => '/system/plugin'));
            return;
        }
        $error = '';
        if (false === $plugin_data = $context->plugin->isPluginInstallable($plugin_library, $error, true)) {
            $context->response->setError($error, array('base' => '/system/plugin'));
            return;
        }
        $form = $this->_getForm($context, $plugin_library, $plugin_data);
        if ($form->validate()) {
            if ($context->request->getAsStr('form_submit_confirm')) {
                $form->addSubmitButtons(array($context->plugin->_('Back'), 'form_submit_submit' => $context->plugin->_('Install')));
                $form->freeze();
            } elseif ($context->request->getAsStr('form_submit_submit')) {
                if (!$form->elementExists('_nicename')) {
                    $nicename = $form->getSubmitValue('_name');
                } else {
                    if (!$nicename = mb_trim($form->getSubmitValue('_nicename'), $context->plugin->_(' '))) {
                        $nicename = $form->getSubmitValue('_name');
                    }
                }
                $params = array();
                foreach (array_keys($plugin_data['params']) as $param_name) {
                    $params[$param_name] = $form->getSubmitValue($param_name);
                    if (@$plugin_data['params'][$param_name]['type'] == 'input_multi') {
                        $separator = !isset($plugin_data['params'][$param_name]['separator']) ? "\n" : (string)$plugin_data['params'][$param_name]['separator'];
                        $params[$param_name] = explode($separator, str_replace("\r", '', $params[$param_name]));
                    }
                }
                $result = $context->plugin->installPlugin($plugin_library, $plugin_data, $nicename, $params, $form->getSubmitValue('_priority'), $form->getSubmitValue('_active'), strtolower($form->getSubmitValue('_name')));
                if (is_object($result)) {
                    $context->response->setSuccess($context->plugin->_('Plugin installed successfully'), array('base' => '/system/plugin'));
                    $this->_application->dispatchEvent('SystemAdminPluginInstalled', array($result));
                    $this->_application->dispatchEvent($plugin_library . 'PluginInstalled', array($result));
                } else {
                    $context->response->setError(sprintf($context->plugin->_('Plugin installation failure. Please check the plugin %s and try again. Error: %s'), $plugin_library, $result), array('base' => '/system/plugin'));
                }
                return;
            }
        }

        $context->response->setPageInfo($context->plugin->_('Clone plugin'));
        $this->_application->setData(array(
            'plugin_params_form' => $form,
        ));
    }

    private function _getForm(Sabai_Application_Context $context, $library, $data)
    {
        $form = $this->getForm($context, $data, 1, 0, array(), $data['nicename']);
        $insert_before = $form->elementExists('_nicename') ? '_nicename' : '_options';
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Name'), h($library)), $insert_before);
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Version'), h($data['version'])), $insert_before);
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Summary'), h($data['summary'])), $insert_before);
        $form->insertElementBefore($form->createElement('text', '_name', array($context->plugin->_('Clone name'), $context->plugin->_('Only lowercase alphabet and numerical values are allowed, and must start with an alphabet.')), array('size' => 30, 'maxlength' => 255)), $insert_before);
        $form->setRequired('_name', sprintf($context->plugin->_('%s is required'), $context->plugin->_('Clone name')), true, $context->plugin->_(' '));
        $form->addRule('_name', $context->plugin->_('Only lowercase alphabet and numerical values are allowed starting with an alphabet.'), 'regex', SabaiPlugin::PLUGIN_NAME_REGEX);
        $form->setCallback('_name', $context->plugin->_('There is another plugin installed using the specified plugin name'), array($this, 'validateInstallNameUnique'), array($context));
        $form->setCallback('_name', $context->plugin->_('There is another plugin library using the specified plugin name'), array($this, 'validateLibraryNameUnique'), array($context));
        $form->addSubmitButtons(array('form_submit_confirm' => $context->plugin->_('Confirm')));
        return $form;
    }

    public function validateInstallNameUnique($name, Sabai_Application_Context $context)
    {
        if ($this->isPluginInstalled($context, $name)) {
            return false;
        }
        return true;
    }

    public function validateLibraryNameUnique($name, Sabai_Application_Context $context)
    {
        if ($this->_application->getPluginManager()->getLocalPlugin($name)) {
            return false;
        }
        return true;
    }
}