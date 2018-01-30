<?php
class Plugg_Akismet_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Anti SPAM plugin using Akismet or other compatible spam-filtering services');
        $this->_nicename = $this->_('Akismet');
        $this->_requiredLibs = array('Services_Akismet2');
        $this->_params = array(
            'apiKey' => array(
                'label'    => $this->_('API key'),
                'default'  => '',
                'required' => true
            ),
            'apiServer' => array(
                'label'    => array(
                    $this->_('Alternate API server'),
                    $this->_('Enter the hostname of Akismet compatible service provider, e.g. api.antispam.typepad.com'),
                    $this->_('If you want to use a spam-filtering service provider other than Akismet, enter the server hostname of the provider below.'),
                ),
                'default'  => '',
                'required' => false,
            ),
            'apiPort' => array(
                'label'    => array(
                    $this->_('Alternate API port'),
                    null,
                    $this->_('Enter the port number of the spam-filtering service provider if the provider uses a non-standard port.'),
                ),
                'default'  => '',
                'required' => false
            )
        );
    }
}