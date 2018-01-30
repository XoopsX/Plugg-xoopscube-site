<?php
class Plugg_Locale_Plugin extends Plugg_Plugin
{
    function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }

    function onSystemAdminPluginConfigured($plugin)
    {
        $this->updatePluginGettextMessages($plugin->name);
    }

    function onSystemAdminPluginUpgraded($plugin)
    {
        $this->updatePluginGettextMessages($plugin->name);
    }

    function updatePluginGettextMessages($pluginName)
    {
        $messages = $this->getModel()->Message
            ->criteria()
            ->plugin_is($pluginName)
            ->lang_is(SABAI_LANG)
            ->fetch();
        $custom = array();
        foreach ($messages as $message) {
            $custom[$message->key] = $message->localized;
        }
        if (!empty($custom)) {
            $original = $this->_application->getGettext()->getMessages($pluginName);
            $this->_application->cacheMessages(array_merge($original, $custom), $pluginName);
        }
    }
}