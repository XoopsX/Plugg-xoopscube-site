<?php
class Plugg_System_Admin_InstallPlugin extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$plugin_library = $context->request->getAsStr('plugin_library')) {
            $context->response->setError($context->plugin->_('No plugin specified'), array('base' => '/system/plugin'));
            return;
        }
        $error = '';
        if (false === $plugin_data = $context->plugin->isPluginInstallable($plugin_library, $error)) {
            $context->response->setError($error, array('base' => '/system/plugin'));
            return;
        }
        $form = $this->_getForm($context, $plugin_library, $plugin_data);
        if ($form->validate()) {
            if ($context->request->getAsStr('form_submit_confirm')) {
                $form->addSubmitButtons(array($context->plugin->_('Back'), 'form_submit_submit' => $context->plugin->_('Install')));
                $form->freeze();
            } elseif ($context->request->getAsStr('form_submit_submit')) {
                $params = array();
                foreach (array_keys($plugin_data['params']) as $param_name) {
                    $params[$param_name] = $form->getSubmitValue($param_name);
                    if (@$plugin_data['params'][$param_name]['type'] == 'input_multi') {
                        $separator = !isset($plugin_data['params'][$param_name]['separator']) ? "\n" : (string)$plugin_data['params'][$param_name]['separator'];
                        $params[$param_name] = explode($separator, str_replace("\r", '', $params[$param_name]));
                    }
                }
                if (!$form->elementExists('_nicename')) {
                    $nicename = $plugin_library;
                } else {
                    if (!$nicename = mb_trim($form->getSubmitValue('_nicename'), $context->plugin->_(' '))) {
                        $nicename = $plugin_library;
                    }
                }
                $result = $context->plugin->installPlugin($plugin_library, $plugin_data, $nicename, $params, $form->getSubmitValue('_priority'), $form->getSubmitValue('_active'));
                if (is_object($result)) {
                    $context->response->setSuccess($context->plugin->_('Plugin installed successfully'), array('base' => '/system/plugin'));
                    $this->_application->dispatchEvent('SystemAdminPluginInstalled', array($result));
                    $this->_application->dispatchEvent($plugin_library . 'Installed', array($result));
                } else {
                    $context->response->setError(sprintf($context->plugin->_('Plugin installation failure. Please check the plugin %s and try again. Error: %s'), $plugin_library, $result), array('base' => '/system/plugin'));
                }
                return;
            }
        }

        $context->response->setPageInfo($context->plugin->_('Install plugin'));
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
        $form->addSubmitButtons(array('form_submit_confirm' => $context->plugin->_('Confirm')));
        // Always activate the plugin upon install
        $form->removeElement('_active');
        $form->addElement('hidden', '_active', 1);
        return $form;
    }
}