<?php
class Plugg_System_Admin_UpgradePlugin extends Sabai_Application_Controller
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
        if (!version_compare($plugin->get('version'), $plugin_data['version'], '<')) {
            $context->response->setError($context->plugin->_('Plugin is up to date'), array('base' => '/system/plugin'));
            return;
        }
        $form = $this->_getForm($context, $plugin, $plugin_data);
        if ($form->validate()) {
            if ($context->request->getAsStr('form_submit_confirm')) {
                $form->addSubmitButtons(array($context->plugin->_('Back'), 'form_submit_submit' => $context->plugin->_('Upgrade')));
                $form->freeze();
            } elseif ($context->request->getAsStr('form_submit_submit')) {
                /*$params = array();
                foreach (array_keys($plugin_data['params']) as $param_name) {
                    $params[$param_name] = $form->getSubmitValue($param_name);
                    if ($plugin_data['params'][$param_name]['type'] == 'input_multi') {
                        $separator = !isset($plugin_data['params'][$param_name]['separator']) ? "\n" : (string)$plugin_data['params'][$param_name]['separator'];
                        $params[$param_name] = explode($separator, str_replace("\r", '', $params[$param_name]));
                    }
                }
                $plugin->setParams($params);*/
                $previous_version = $plugin->get('version');
                $plugin->set('version', $plugin_data['version']);
                $plugin->set('priority', $form->getSubmitValue('_priority'));
                if (!$form->elementExists('_nicename')) {
                    $nicename = $plugin_name;
                } else {
                    if (!$nicename = trim($form->getSubmitValue('_nicename'))) {
                        $nicename = $plugin_name;
                    }
                }
                $plugin->set('nicename', $nicename);
                $message = '';
                $plugin_object = $this->_application->getPlugin($plugin_name, false);
                if (!$plugin_object->upgrade($previous_version, $message)) {
                    $context->response->setError($message, array('base' => '/system/plugin'));
                    return;
                }
                if (!empty($message)) $context->response->addMessage($message);
                if ($plugin->commit()) {
                    $context->response->setSuccess('Plugin upgraded successfully', array('base' => '/system/plugin'));

                    // Reload plugins
                    $this->_application->getPluginManager()->reloadPlugins();

                        // Load messages
                    $this->_application->getGettext()->loadMessages($plugin->get('library'), $plugin->get('name'));

                    $this->_application->dispatchEvent('SystemAdminPluginUpgraded', array($plugin));
                    $this->_application->dispatchEvent($plugin->get('library') . 'PluginUpgraded', array($plugin));
                    return;
                }
            }
        }

        $context->response->setPageInfo($context->plugin->_('Upgrade plugin'));
        $this->_application->setData(array(
            'plugin_params_form' => &$form,
        ));
    }

    private function _getForm(Sabai_Application_Context $context, $plugin, $data)
    {
        $form = $this->getForm($context, $data, $plugin->get('active'), $plugin->get('priority'), $plugin->getParams(), $plugin->get('nicename'));
        // remove active/options selections for now, need to check plugin dependency if not removing
        $form->removeElements(array('_active', '_options'));
        $insert_before = $form->elementExists('_nicename') ? '_nicename' : '_options';
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Name'), h($plugin->get('library'))), $insert_before);
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Version'), h($data['version'])), $insert_before);
        $form->insertElementBefore($form->createElement('static', '', $context->plugin->_('Summary'), h($data['summary'])), $insert_before);
        $form->addSubmitButtons(array('form_submit_confirm' => $context->plugin->_('Confirm')));
        return $form;
    }
}