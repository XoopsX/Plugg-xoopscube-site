<?php
class Plugg_XOOPSCubeUserAuth_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Enables user authentication using the user data on another XOOPSCube website.');
        $this->_nicename = $this->_('XOOPSCubeUserAuth');
        $this->_requiredPlugins = array('User');
        $this->_uninstallable = true;
        $this->_cloneable = true;
        $this->_params = array(
            'siteUrl' => array(
                'label' => array($this->_('XOOPSCube website URL'), null, $this->_('Enter the value of XOOPS_URL defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'url',
                'size' => 80,
                'default' => 'http://',
            ),
            'siteName' => array(
                'label' => array($this->_('XOOPSCube website name'), null, $this->_('Enter the name of the target XOOPSCube website.')),
                'required' => true,
                'type' => 'input',
                'size' => 80,
                'default' => 'XOOPSCube website',
            ),
            'dbHost' => array(
                'label' => array($this->_('Database hostname'), null, $this->_('Enter the value of XOOPS_DB_HOST defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'input',
                'size' => 50,
                'default' => 'localhost',
                'cacheable' => true,
            ),
            'dbSecure' => array(
                'label' => array($this->_('Use SSL encryption'), null, $this->_('Use SSL encryption during connection.')),
                'required' => true,
                'type' => 'yesno',
                'default' => 0,
                'cacheable' => false,
            ),
            'dbScheme' => array(
                'label' => array($this->_('Database scheme'), null, $this->_('Enter the value of XOOPS_DB_SCHEME defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'input',
                'size' => 50,
                'default' => 'mysql',
                'alphanumerical' => true,
                'cacheable' => false,
            ),
            'dbName' => array(
                'label' => array($this->_('Database name'), null, $this->_('Enter the value of XOOPS_DB_NAME defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'input',
                'size' => 50,
                'default' => 'xoopscube',
                'cacheable' => false,
            ),
            'dbUser' => array(
                'label' => array($this->_('Database user name'), null, $this->_('Enter the value of XOOPS_DB_USER defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'input',
                'size' => 50,
                'default' => 'root',
                'cacheable' => false,
            ),
            'dbPass' => array(
                'label' => array($this->_('Database user password'), null, $this->_('Enter the value of XOOPS_DB_PASS defined in the target XOOPSCube system.')),
                'required' => false,
                'type' => 'input',
                'size' => 50,
                'default' => '',
                'cacheable' => false,
            ),
            'dbPrefix' => array(
                'label' => array($this->_('Database table prefix'), null, $this->_('Enter the value of XOOPS_DB_PREFIX defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'input',
                'default' => 'xoops',
                'cacheable' => false,
            ),
        );
    }
}