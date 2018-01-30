<?php
class Plugg_Locale_Admin_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $localizable_plugins = array();
        $installed_plugins = $this->_application->getPluginManager()->getInstalledPlugins();
        foreach (array_keys($installed_plugins) as $plugin_name) {
            if (($plugin = $this->_application->getPlugin($plugin_name)) && $plugin->hasLocale()) {
                $localizable_plugins[$plugin_name] = array(
                    'library' => $plugin->getLibrary(),
                    'nicename' => $plugin->getNicename(),
                    'clone' => $plugin->isClone(),
                );
            }
        }
        $this->_application->setData(array(
            'plugins' => $localizable_plugins,
            'plugin_message_count' => $context->plugin
                ->getModel()
                ->getGateway('Message')
                ->getPluginMessageCount(),
        ));
    }
}