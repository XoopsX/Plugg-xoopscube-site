<?php
class Plugg_PluginModel extends Sabai_Model
{
    private $_plugin;

    public function __construct(Plugg_Plugin $plugin, Sabai_User_IdentityFetcher $userIdentityFetcher = null)
    {
        parent::__construct(
            $plugin->getDB(),
            $plugin->getPath() . '/Model',
            'Plugg_' . $plugin->getLibrary() . '_Model_',
            $userIdentityFetcher
        );
        $this->_plugin = $plugin;
    }

    public function getPlugin()
    {
        return $this->_plugin;
    }

    public function _($message)
    {
        return $this->_plugin->_($message);
    }

    public function ngettext($msgId, $msgId2, $num)
    {
        return $this->_plugin->ngettext($msgId, $msgId2, $num);
    }
}