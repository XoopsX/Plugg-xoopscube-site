<?php
class Plugg_Filter_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.1';
        $this->_summary = $this->_('Filters content');
        $this->_uninstallable = false;
        $this->_cloneable = false;
        $this->_requiredPlugins = array('HTMLPurifier');
        $this->_nicename = $this->_('Filter');
    }
}