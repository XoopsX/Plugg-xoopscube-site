<?php
class Plugg_Widget_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('This plugin enables adding plugin widgets to the website.');
        $this->_nicename = $this->_('Widget');
        $this->_cloneable = false;
        $this->_uninstallable = false;
        //$this->_supportedAppType = Plugg::STANDALONE;
    }
}