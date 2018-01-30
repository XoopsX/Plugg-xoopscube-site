<?php
class Plugg_jQuery_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Enables the jQuery javascript library');
        $this->_nicename = $this->_('jQuery');
    }
}