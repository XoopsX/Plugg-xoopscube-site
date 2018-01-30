<?php
require_once 'Plugg/PluginAdmin.php';

class Plugg_Locale_Admin extends Plugg_PluginAdmin
{
    public function __construct()
    {
        parent::__construct(__CLASS__ . '_', dirname(__FILE__) . '/Admin');
    }

    function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'plugg' => array(
                'controller' => 'EditMessagesForm',
            ),
            'plugin/:plugin_name' => array(
                'controller' => 'EditPluginMessagesForm',
                /*'requirements' => array(':plugin_name' => SabaiPlugin::PLUGIN_NAME_REGEX)*/
                'access_callback' => '_onAccess',
            )
        );
    }

    protected function _onAccess($context, $controller)
    {
        if ($plugin_name = $context->request->getAsStr('plugin_name')) {
            if (($plugin = $this->_application->getPlugin($plugin_name)) &&
                $plugin->hasLocale()
            ) {
                $this->_application->plugin = $plugin;
                return true;
            }
        }

        return false;
    }

    function saveMessages($submitted, $original, $pluginName, $model, $lang = SABAI_LANG)
    {
        $messages = array_filter(array_intersect_key($submitted, $original), array($this, '_filterMessage'));
        foreach (array_keys($messages) as $k) {
            $this->_savePluginCustomMessage($pluginName, $k, $messages[$k], $model, $lang);
        }
        return $model->commit() ? $messages : false;
    }

    function _filterMessage($message)
    {
        return $message != '';
    }

    function _savePluginCustomMessage($pluginName, $key, $localized, $model, $lang)
    {
        $message = $model->create('Message')
            ->set('key', $key)
            ->set('localized', $localized)
            ->set('lang', $lang)
            ->set('plugin', $pluginName)
            ->markNew();
    }

    function validateToken($tokenParam, $tokenName, Sabai_Application_Context $context)
    {
        if (!$token_value = $context->request->getAsStr($tokenParam, false)) {
            return false;
        }
        require_once 'Sabai/Token.php';
        if (!Sabai_Token::validate($token_value, $tokenName)) {
            return false;
        }
        return true;
    }
}