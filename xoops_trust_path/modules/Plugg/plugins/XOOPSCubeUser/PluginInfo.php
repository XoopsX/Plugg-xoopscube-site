<?php
class Plugg_XOOPSCubeUser_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('This plugin enables using user accounts of any XOOPS Cube installation as the user data. If you install this plugin on a XOOPSCube system and select this plugin as the default user account management plugin from the User plugin configuration page, this plugin will then replace the original user account management system provided by XOOPSCube with a more enhanced, feature rich user account management system.');
        $this->_nicename = $this->_('XOOPSCube User');
        $this->_cloneable = false;
        $this->_uninstallable = true;
        $this->_requiredPlugins = array('User');
        $this->_supportedAppType = Plugg::STANDALONE | Plugg::XOOPSCUBE_LEGACY;
        $params = $this->_application->isType(Plugg::XOOPSCUBE_LEGACY) ? array() : array(
            'xoopsUrl' => array(
                'label' => array($this->_('XOOPS URL'), null, $this->_('Enter the value of XOOPS_URL defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'url',
                'size' => 80,
                'default' => 'http://',
            ),
            'dbHost' => array(
                'label' => array($this->_('Database hostname'), null, $this->_('Enter the value of XOOPS_DB_HOST defined in the target XOOPSCube system.')),
                'required' => true,
                'type' => 'input',
                'size' => 50,
                'default' => 'localhost',
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
        $field_options = array(
            Plugg_XOOPSCubeUser_Plugin::FIELD_VIEWABLE => $this->_('Display on profile page'),
            Plugg_XOOPSCubeUser_Plugin::FIELD_EDITABLE => $this->_('Display on edit profile page'),
            Plugg_XOOPSCubeUser_Plugin::FIELD_REGISTERABLE => $this->_('Display on registration page')
        );
        $field_options2 = array(
            Plugg_XOOPSCubeUser_Plugin::FIELD_EDITABLE => $this->_('Display on edit profile page'),
            Plugg_XOOPSCubeUser_Plugin::FIELD_REGISTERABLE => $this->_('Display on registration page')
        );
        
        $this->_params = array_merge($params, array(
            'passwordMinLength' => array(
                'label' => array($this->_('Password minimum length')),
                'required' => true,
                'type' => 'text',
                'default' => 8,
            ),
            'usernameMinLength' => array(
                'label' => array($this->_('User name minimum length')),
                'required' => true,
                'type' => 'text',
                'default' => 3,
            ),
            'usernameMaxLength' => array(
                'label' => array($this->_('User name maximum length')),
                'required' => true,
                'type' => 'text',
                'default' => 10,
            ),
            'usernameRestriction' => array(
                'label' => array($this->_('User name restriction'), null, $this->_('Select the restriction level of allowed characters in a user name')),
                'required' => true,
                'type' => 'radio',
                'default' => 'strict',
                'options' => array(
                    'strict' => $this->_('Strict (Only alphabets, numbers, underscores, and dashes, RECOMMENDED)'),
                    'medium' => $this->_('Medium (Strict + some punctuation characters)'),
                    'light' => $this->_('Light (Medium + multi-byte characters)'),
                )
            ),
            'usernamesNotAllowed' => array(
                'label'    => array($this->_('Restricted user names'), null, $this->_('Enter user names that are not allowed to be used, each per line. Regular expressions may be used.')),
                'default'  => array('webmaster', '^xoops', '^admin'),
                'required' => false,
                'type'     => 'input_multi',
            ),
            'emailsNotAllowed' => array(
                'label'    => array($this->_('Restricted email addresses'), null, $this->_('Enter email addresses that are not allowed to be used, each per line. Regular expressions may be used.')),
                'default'  => array(),
                'required' => false,
                'type'     => 'input_multi',
            ),
            'nameField' => array(
                'label' => array($this->_('"Full name" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(1, 2),
                'options' => $field_options
            ),
            'urlField' => array(
                'label' => array($this->_('"URL" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(1, 2, 4),
                'options' => $field_options
            ),
            'timezoneField' => array(
                'label' => array($this->_('"Time zone" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(2, 4),
                'options' => $field_options2,
                'dependency' => array('app' => Plugg::XOOPSCUBE_LEGACY)
            ),
            'imAccountsField' => array(
                'label' => array($this->_('"IM accounts" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(1, 2),
                'options' => $field_options
            ),
            'locationField' => array(
                'label' => array($this->_('"Location" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(1, 2),
                'options' => $field_options
            ),
            'occupationField' => array(
                'label' => array($this->_('"Occupation" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(1, 2),
                'options' => $field_options
            ),
            'interestsField' => array(
                'label' => array($this->_('"Interests" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(1, 2),
                'options' => $field_options
            ),
            'sitePreferencesField' => array(
                'label' => array($this->_('"Site preferences" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(2),
                'options' => $field_options2,
                'dependency' => array('app' => Plugg::XOOPSCUBE_LEGACY)
            ),
            'extraInfoField' => array(
                'label' => array($this->_('"Extra info" user field')),
                'required' => false,
                'type' => 'checkbox',
                'default' => array(1, 2),
                'options' => $field_options
            ),
            'enableStatFields' => array(
                'label' => array($this->_('Display statistic user fields')),
                'required' => true,
                'type' => 'yesno',
                'default' => false,
            ),
            'allowImageUpload' => array(
                'label' => array($this->_('Allow avatar image upload')),
                'required' => true,
                'type' => 'yesno',
                'default' => false,
            ),
            'imageMaxSizeKB' => array(
                'label'    => array($this->_('Avatar image max file size'), $this->_('Enter a numeric value in kilo bytes')),
                'default'  => 100,
                'required' => true,
                'type'     => 'input',
                'numeric'  => true,
            ),
            'imageMaxWidth' => array(
                'label'    => array($this->_('Avatar image max file width'), $this->_('Enter a numeric value in pixels, 0 for unlimited')),
                'default'  => 200,
                'required' => true,
                'type'     => 'input',
                'numeric'  => true,
            ),
            'imageMaxHeight' => array(
                'label'    => array($this->_('Avatar image max file height'), $this->_('Enter a numeric value in pixels, 0 for unlimited')),
                'default'  => 200,
                'required' => true,
                'type'     => 'input',
                'numeric'  => true,
            )
        ));
    }
}