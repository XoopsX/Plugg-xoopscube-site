<?php
class Plugg_ExternalLink_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Adds a small link icon to external links. Opens external links in a new window when clicked.');
        $this->_nicename = $this->_('ExternalLink');
        $this->_params = array(
            'localhost'  => array(
                'label'   => $this->_('URL will be considered local if contains the following text'),
                'default' => $_SERVER['HTTP_HOST']
            )
        );
        $this->_requiredPlugins = array('jQuery');
    }
}