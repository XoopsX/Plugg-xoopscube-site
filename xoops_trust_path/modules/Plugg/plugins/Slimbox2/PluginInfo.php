<?php
class Plugg_Slimbox2_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Enables the Slimbox2 javascript library from http://www.digitalia.be/software/slimbox2');
        $this->_nicename = $this->_('Slimbox2');
        $this->_requiredPlugins = array('jQuery');
    }
}