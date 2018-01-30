<?php
class Plugg_System_Admin_ConfigurePlugin extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$plugin_name = $context->request->getAsStr('plugin_name')) {
            $context->response->setError($context->plugin->_('No plugin specified'), array('base' => '/system/plugin'));
            return;
        }
        if (!$plugin = $this->isPluginInstalled($context, $plugin_name)) {
            $context->response->setError($context->plugin->_('Plugin not installed'), array('base' => '/system/plugin'));
            return;
        }
        if (!$plugin_data = $this->_application->getPluginManager()->getLocalPlugin($plugin->get('library'), true)) {
            $context->response->setError($context->plugin->_('Invalid plugin'), array('base' => '/system/plugin'));
            return;
        }
        $form = $this->_getForm($context, $plugin, $plugin_data);
        if ($form->validate()) {
            if ($context->request->getAsStr('form_submit_confirm')) {
                $form->addSubmitButtons(array($context->plugin->_('Back'), 'form_submit_submit' => $context->plugin->_('Submit')));
                $form->freeze();
            } elseif ($context->request->getAsStr('form_submit_submit')) {
                $error = false;
                // check plugin dependency if the plugin is deactivated and that the plugin is not a clone
                if (!$active = $form->getSubmitValue('_active')) {
                    if (!$plugin->isClone()) {
                        if (!$plugin_data['uninstallable']) {
                            $error = sprintf('Plugin %s may not be deactivated', $plugin->get('library'));
                        } else {
                            if ($dependency = $this->_application->getPluginManager()->getPluginDependency($plugin->get('library'), true, true)) {
                                $dependency_plugins = array();
                                // Get plugins that are dependent and that requires the configured plugin to be active
                                foreach ($dependency as $dependency_plugin => $dependency_info) {
                                    if ($dependency_info['active']) $dependency_plugins[] =  $dependency_plugin;
                                }
                                if (!empty($dependency_plugins)) {
                                    $error = sprintf('Plugin %s is required by %s and must be active', $plugin->get('library'), implode(', ', $dependency_plugins));
                                }
                            }
                        }
                    }
                } else {
                    if (($php_required = $plugin_data['dependencies']['php']) && version_compare(phpversion(), $php_required, '<')) {
                        $context->response->setError(sprintf($context->plugin->_('The selected plugin requires PHP %s or higher'), $php_required), array('base' => '/system/plugin'));
                        return;
                    }
                    if ($plugins_required = $plugin_data['dependencies']['plugins']) {
                        $plugins_installed = $this->_application->getPluginManager()->getInstalledPlugins();
                        foreach ($plugins_required as $plugin_required) {
                            $plugin_required_name = strtolower($plugin_required['library']);
                            if (!array_key_exists($plugin_required_name, $plugins_installed)) {
                                $context->response->setError(sprintf($context->plugin->_('The selected plugin requires plugin %s to be installed'), $plugin_required['library']), array('base' => '/system/plugin'));
                                return;
                            }
                            // Does the required plugin must be active?
                            if (!empty($plugin_required['active']) && empty($plugins_installed[$plugin_required_name]['active'])) {
                                $context->response->setError(sprintf($context->plugin->_('The selected plugin requires plugin %s to be installed and active'), $plugin_required['library']), array('base' => '/system/plugin'));
                                return;
                            }
                            if (isset($plugin_required['version'])) {
                                if (version_compare($plugins_installed[$plugin_required_name]['version'], $plugin_required['version'], '<')) {
                                    $context->response->setError(sprintf($context->plugin->_('The selected plugin requires plugin %s version %s or higher to be installed'), $plugin_required['library'], $plugin_required['version']), array('base' => '/system/plugin'));
                                    return;
                                }
                            }
                        }
                    }
                }
                if (!$error) {
                    // Copy plugin params so that it can be passed upon dispatch
                    $plugin_params_original = $plugin->getParams();
                    $params = array();
                    foreach (array_keys($plugin_data['params']) as $param_name) {
                        $params[$param_name] = $form->getSubmitValue($param_name);
                        if (@$plugin_data['params'][$param_name]['type'] == 'input_multi') {
                            $separator = !isset($plugin_data['params'][$param_name]['separator']) ? "\n" : (string)$plugin_data['params'][$param_name]['separator'];
                            $params[$param_name] = explode($separator, str_replace("\r", '', $params[$param_name]));
                        }
                    }
                    $plugin->setParams($params);
                    $plugin->set('active', $active);
                    $plugin->set('priority', $form->getSubmitValue('_priority'));
                    if (!$form->elementExists('_nicename')) {
                        $nicename = $plugin_name;
                    } else {
                        if (!$nicename = trim($form->getSubmitValue('_nicename'))) {
                            $nicename = $plugin_name;
                        }
                    }
                    $plugin->set('nicename', $nicename);
                    if ($plugin->commit()) {
                        $context->response->setSuccess($context->plugin->_('Plugin configured successfully'), array('base' => '/system/plugin'));

                        // Reload plugins
                        $this->_application->getPluginManager()->reloadPlugins();

                        // Load messages
                        $this->_application->getGettext()->loadMessages($plugin->get('library'), $plugin->get('name'));

                        // Dispatch plugin configured events
                        $this->_application->dispatchEvent('SystemAdminPluginConfigured', array($plugin, $plugin_params_original));
                        $this->_application->dispatchEvent($plugin->get('library') . 'PluginConfigured', array($plugin, $plugin_params_original));

                        return;
                    }
                }
            }
        }

        $context->response->setPageInfo($context->plugin->_('Configure plugin'));
        $this->_application->setData(array(
            'plugin_params_form' => $form,
        ));
    }

    private function _getForm(Sabai_Application_Context $context, $plugin, $data)
    {
        $form = $this->getForm($context, $data, $plugin->get('active'), $plugin->get('priority'), $plugin->getParams(), $plugin->get('nicename'));
        $insert_before = $form->elementExists('_nicename') ? '_nicename' : '_options';
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Name'), h($plugin->get('library'))), $insert_before);
        if ($plugin->isClone()) {
            $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Clone name'), h($plugin->get('name'))), '_nicename');
        }
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Summary'), h($data['summary'])), $insert_before);
        $form->addSubmitButtons(array('form_submit_confirm' => $context->plugin->_('Confirm')));
        return $form;
    }
}