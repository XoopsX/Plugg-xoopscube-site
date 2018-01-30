<?php
class Plugg_XOOPSCube_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.2';
        $this->_summary = $this->_('This plugin is required to run Plugg as a XOOPSCube module.');
        $this->_nicename = $this->_('XOOPSCube');
        $this->_cloneable = false;
        $this->_uninstallable = false;
        $this->_supportedAppType = Plugg::XOOPSCUBE_LEGACY;
    }
}