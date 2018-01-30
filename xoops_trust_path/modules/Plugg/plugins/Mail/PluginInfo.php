<?php
class Plugg_Mail_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Provides basic mail sending functionalities');
        $this->_nicename = $this->_('Mail');
        $this->_cloneable = false;
        $this->_uninstallable = false;
        $this->_params = array(
            'mailSenderPlugin' => array(
                'label' => array($this->_('Mail sender plugin'), null, $this->_('Select library to use for sending emails')),
                'required' => false,
                'type' => 'radio',
                'options_event' => 'MailSenderPluginOptions',
            )
        );
    }
}