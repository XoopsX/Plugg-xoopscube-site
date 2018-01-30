<?php
class Plugg_XOOPSCubeUserAPI_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('This plugin enables using the default user account management system provided by XOOPSCube to manage user accounts');
        $this->_nicename = $this->_('XOOPSCube User API');
        $this->_cloneable = false;
        $this->_uninstallable = false;
        $this->_requiredPlugins = array('User');
        $this->_supportedAppType = Plugg::XOOPSCUBE_LEGACY;
    }
}