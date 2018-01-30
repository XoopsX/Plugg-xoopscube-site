<?php
class Plugg_OpenID_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Enables the OpenID user authentication');
        $this->_nicename = $this->_('OpenID');
        $this->_uninstallable = true;
        $this->_cloneable = false;
        $this->_requiredPlugins = array('User');
        $this->_params = array(
            'openidRandSource' => array(
                'label'   => array($this->_('Path to random number generator'), null, $this->_('Enter the path to a random number generator such as /dev/urandom or leave it blank to continue with an insecure random number generator.')),
                'default' => '/dev/urandom'
            ),
            'yadisCurlOptionCaInfo' => array(
                'label'   => array($this->_('Path to PEM encoded cert file'), sprintf($this->_('Enter a relative path from %s/ or an absolute path starting with a %s'), $this->_path, DIRECTORY_SEPARATOR), $this->_('Enter the path to a PEM encoded certificate file which is required by some OpenID providers such as mixi.jp. Leave it as-is if you are unsure about this option.')),
                'default' => 'certs/cacert.pem'
            ),
        );
    }
}