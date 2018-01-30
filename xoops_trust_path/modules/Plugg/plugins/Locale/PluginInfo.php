<?php
class Plugg_Locale_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Enables online administration of locale message catalogues.');
        $this->_nicename = $this->_('Locale');
    }
}