<?php
class Plugg_Profile_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_cloneable = true;
        $this->_summary = $this->_('Allows access to user profile page using user names');
        $this->_nicename = $this->_('Profile');
        $this->_requiredPlugins = array('User');
        $this->_params = array(
            'usernameRegex' => array(
                'type'     => 'input',
                'label'    => $this->_('Request user name must match the following regex:'),
                'default'  => '[a-zA-Z0-9_\-]+',
                'required' => true
            ),
        );
    }
}