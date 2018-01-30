<?php
class Plugg_Cron_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = sprintf($this->_('The cron plugin will allow administrators to manually run the cron at any time.'));
        $this->_nicename = $this->_('Cron');
    }
}