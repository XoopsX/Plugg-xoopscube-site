<?php
class Plugg_System_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.1';
        $this->_summary = $this->_('Handles general site configuration for administrators.');
        $this->_nicename = $this->_('System');
        $this->_uninstallable = false;
        $this->_cloneable = false;
    }
}