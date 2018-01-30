<?php
class Plugg_Gender_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Adds a gender field to user profiles.');
        $this->_nicename = $this->_('Gender');
        $this->_cloneable = true;
        $this->_uninstallable = true;
        $this->_requiredPlugins = array('User');
        $this->_params = array();
    }
}