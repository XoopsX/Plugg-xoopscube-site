<?php
class Plugg_User_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.2';
        $this->_summary = $this->_('The user plugin adds user security system to the application');
        $this->_nicename = $this->_('User');
        $this->_requiredPlugins = array('Mail', 'Filter');
        $this->_uninstallable = false;
        $this->_cloneable = false;

        switch ($this->_application->getType()) {
            case Plugg::XOOPSCUBE_LEGACY:
                $userManagerPlugin_options = array('xoopscubeuser', 'xoopscubeuserapi');
                break;
            case Plugg::XOOPS:
                $userManagerPlugin_options = array('xoopsuser', 'xoopsuserapi');
                break;
            default:
                $userManagerPlugin_options = array();
                break;
        }

        $this->_params = array(
            'userManagerPlugin' => array(
                'label' => array($this->_('User management plugin'), null, $this->_('IMPORTANT! If you are switching to another plugin, make sure that the new plugin already has valid user accounts. Otherwise, you may not be able to login and/or may result in corruputed user system on your site.')),
                'required' => false,
                'type' => 'radio',
                'options_event' => 'UserManagerPluginOptions',
                'options_allowed' => $userManagerPlugin_options,
            ),
            'allowViewAnyUser' => array(
                'label' => array($this->_('Allow anyone includeing guest users to view any user profile'), null, $this->_('Applicable if user management plugin is not the API type plugin.')),
                'default' => 0,
                'required' => true,
                'type' => 'yesno'
            ),
            'allowRegistration' => array(
                'label' => array($this->_('Allow new user registration'), null, $this->_('Applicable if user management plugin is not the API type plugin.')),
                'required' => true,
                'type' => 'yesno',
                'default' => true,
            ),
            'useFriendsFeature' => array(
                'label' => array($this->_('Community features - Friends'), $this->_('Select yes to allow users to add each other as a friend'), $this->_('Applicable if user management plugin is not the API type plugin.')),
                'required' => true,
                'type' => 'yesno',
                'default' => true,
            ),
            'enableAutologin' => array(
                'label' => array($this->_('AutoLogin - Allow users to keep logged in for specific range of time'), null, $this->_('Applicable if user management plugin is not the API type plugin.')),
                'default' => true,
                'required' => true,
                'type' => 'yesno'
            ),
            'autologinSessionLifetime' => array(
                'label' => array($this->_('AutoLogin - Number of days users can keep logged in'), null, $this->_('Applicable if user management plugin is not the API type plugin.')),
                'default' => 3,
                'required' => false,
                'type' => 'text',
                'numeric' => true,
            ),
            'limitSingleAutologinSession' => array(
                'label' => array($this->_('AutoLogin - Limit only one autologin session to be created per user'), null, $this->_('Applicable if user management plugin is not the API type plugin.')),
                'default' => true,
                'required' => true,
                'type' => 'yesno'
            ),
            'userActivation' => array(
                'label' => array($this->_('Select activation type of newly registered users'), null, $this->_('Applicable if user management plugin is not the API type plugin.')),
                'required' => true,
                'type' => 'radio',
                'default' => 'user',
                'options' => array(
                    'user' => $this->_('Require activation by user'),
                    'auto' => $this->_('Activate automatically'),
                    'admin' => $this->_('Activation by administrators'),
                )
            ),
            'registerConfirmEmail' => array(
                'label' => array($this->_('Confirmation email sent to user upon new registration'), null, 'Valid only when "Require activation by user" is selected for user activation type.'),
                'type' => 'textarea',
                'required' => true,
                'rows' => 11,
                'default' => implode("\n\n", array(
                    $this->_('Hello {USER_NAME},'),
                    $this->_('The email address ({USER_EMAIL}) has been used to register an account at {SITE_NAME}.'),
                    $this->_('To become a member of {SITE_NAME}, please confirm your request by clicking on the link below:'),
                    '{CONFIRM_LINK}',
                    "-----------\n{SITE_NAME}\n{SITE_URL}",
                ))
            ),
            'registerConfirmedEmail' => array(
                'label' => array($this->_('Notification email sent to user upon user account activation')),
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
            'editEmailConfirmEmail' => array(
                'label' => array($this->_('Confirmation email to send upon user email address modification')),
                'type' => 'textarea',
                'required' => true,
                'rows' => 11,
                'default' => implode("\n\n", array(
                    $this->_('Hello {USER_NAME},'),
                    $this->_('The email address ({USER_EMAIL}) has been used for a user account at {SITE_NAME}.'),
                    $this->_('Please click on the link below to confirm the email address:'),
                    '{CONFIRM_LINK}',
                    "-----------\n{SITE_NAME}\n{SITE_URL}"
                ))
            ),
            'newPasswordConfirmEmail' => array(
                'label' => array($this->_('Confirmation email to send upon new user password request')),
                'type' => 'textarea',
                'required' => true,
                'rows' => 13,
                'default' => implode("\n\n", array(
                    $this->_('Hello {USER_NAME},'),
                    $this->_('A web user from {IP} has just requested a new password for your user account at {SITE_NAME}.'),
                    $this->_('Please click on the link below to confirm the request and receive a new password:'),
                    '{CONFIRM_LINK}',
                    $this->_('If you did not ask for this, you can just ignore this email.'),
                    "-----------\n{SITE_NAME}\n{SITE_URL}"
                ))
            ),
            'newPasswordEmail' => array(
                'label' => array($this->_('Email containing new user password')),
                'type' => 'textarea',
                'required' => true,
                'rows' => 16,
                'default' => implode("\n\n", array(
                    $this->_('Hello {USER_NAME},'),
                    $this->_('A web user from {IP} has just requested a new password for your user account at {SITE_NAME}.'),
                    $this->_('Here are your login details:'),
                    $this->_('Username: {USER_NAME}') . "\n" . $this->_('New Password: {USER_PASSWORD}'),
                    $this->_('You can change the password after you login from the following URL:'),
                    '{LOGIN_LINK}',
                    "-----------\n{SITE_NAME}\n{SITE_URL}"
                ))
            ),
            'registerConfirmByAdminEmail' => array(
                'label' => array($this->_('Confirmation email sent to admin email address upon new user registration'), null, 'Valid only when "Activation by administrators" is selected for user activation type.'),
                'type' => 'textarea',
                'required' => true,
                'rows' => 11,
                'default' => implode("\n\n", array(
                    $this->_('Hello admin,'),
                    $this->_('A new user {USER_NAME} ({USER_EMAIL}) has just registered an account at {SITE_NAME}.'),
                    $this->_('Clicking on the link below will activate the user account:'),
                    '{CONFIRM_LINK}',
                    "-----------\n{SITE_NAME}\n{SITE_URL}",
                ))
            ),
            'registerCompleteEmail' => array(
                'label' => array($this->_('Confirmation email sent to admin email address upon completion of new user registration')),
                'type' => 'textarea',
                'required' => true,
                'rows' => 11,
                'default' => implode("\n\n", array(
                    $this->_('Hello admin,'),
                    $this->_('A new user {USER_NAME} ({USER_EMAIL}) has completed user registration at {SITE_NAME}.'),
                    $this->_('Click the link below to view the user profile:'),
                    '{USER_LINK}',
                    "-----------\n{SITE_NAME}\n{SITE_URL}",
                ))
            ),
        );
    }
}