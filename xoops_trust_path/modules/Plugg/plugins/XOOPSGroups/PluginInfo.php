<?php
class Plugg_XOOPSGroups_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Adds a user profile field that displays groups to which the user belongs.');
        $this->_nicename = $this->_('XOOPS Groups');
        $this->_requiredPlugins = array('User');
        $this->_cloneable = true;
        $this->_uninstallable = true;
        $this->_requiredPlugins = array('User');
        $this->_supportedAppType = Plugg::MODULE;
        $this->_params = array();
    }
}