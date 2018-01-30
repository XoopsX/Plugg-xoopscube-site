<?php
class Plugg_Birthday_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('This plugin enables each user to add his or her birthday to the profile page.');
        $this->_nicename = $this->_('Birthday');
        $this->_cloneable = true;
        $this->_uninstallable = true;
        $this->_requiredPlugins = array('User');
        $this->_params = array(
            'happyBirthdayEmail' => array(
                'label' => array($this->_('Happy birthday mail sent to users on user birthday')),
                'type' => 'textarea',
                'required' => true,
                'rows' => 11,
                'default' => implode("\n\n", array(
                    $this->_('Hello {USER_NAME},'),
                    $this->_('Your user account registration at {SITE_NAME} is complete.'),
                    $this->_('You can now login from the following URL with the password you have provided upon registration:'),
                    '{LOGIN_LINK}',
                    "-----------\n{SITE_NAME}\n{SITE_URL}",
                ))
            ),
        );
    }
}