<?php
class Plugg_Signature2_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Adds a signature field to user profile. Signature is an HTML fragment that can be attached to user contents.');
        $this->_nicename = $this->_('Signature2');
        $this->_cloneable = true;
        $this->_uninstallable = true;
        $this->_requiredPlugins = array('User');
        $this->_params = array();
    }
}