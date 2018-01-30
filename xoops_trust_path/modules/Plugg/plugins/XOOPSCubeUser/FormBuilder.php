<?php
abstract class Plugg_XOOPSCubeUser_FormBuilder extends Sabai_HTMLQuickForm
{
    protected $_plugin;

    public function __construct(Plugg_XOOPSCubeUser_Plugin $plugin)
    {
        $this->_plugin = $plugin;
    }

    public function buildForm(Sabai_HTMLQuickForm $form)
    {
        // name
        $this->_addNameField($form);
        // url
        $this->_addUrlField($form);
        // time zone
        $this->_addTimeZoneField($form);
        // IM accounts
        $this->_addImAccountsField($form);
        // location
        $this->_addLocationField($form);
        // occupation
        $this->_addOccupationField($form);
        // interests
        $this->_addInterestsField($form);
        // site preferences
        $this->_addSitePreferencesField($form);
        // other
        $this->_addExtraInfoField($form);
    }

    protected function _addNameField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_isFieldEnabled($this->_plugin->getParam('nameField'))) return;

        $form->addElement('text', 'name', array($this->_plugin->_('Full name'), null, $this->_plugin->_('Enter your full name')), array('size' => 50, 'maxlength' => 60));
    }

    protected function _addUrlField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_isFieldEnabled($this->_plugin->getParam('urlField'))) return;

        $form->addElement('text', 'url', array($this->_plugin->_('Website URL'), null, $this->_plugin->_('Enter the URL of your website if any')), array('size' => 80, 'maxlength' => 100));
        $form->addRule('url', $this->_plugin->_('Invalid URL'), 'uri');
    }

    protected function _addTimeZoneField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_plugin->getApplication()->isType(Plugg::XOOPSCUBE_LEGACY) ||
            !$this->_isFieldEnabled($this->_plugin->getParam('timezoneField'))
        ) {
            return;
        }

        $xcube_root = XCube_Root::getSingleton();
        $xcube_root->mLanguageManager->loadModuleMessageCatalog('user');
        $xcube_root->mLanguageManager->loadPageTypeMessageCatalog('timezone');

        $options = array(
            '-12.0' => $this->_plugin->_(_TZ_GMTM12),
            '-11.0' => $this->_plugin->_(_TZ_GMTM11),
            '-10.0' => $this->_plugin->_(_TZ_GMTM10),
            '-9.0' => $this->_plugin->_(_TZ_GMTM9),
            '-8.0' => $this->_plugin->_(_TZ_GMTM8),
            '-7.0' => $this->_plugin->_(_TZ_GMTM7),
            '-6.0' => $this->_plugin->_(_TZ_GMTM6),
            '-5.0' => $this->_plugin->_(_TZ_GMTM5),
            '-4.0' => $this->_plugin->_(_TZ_GMTM4),
            '-3.5' => $this->_plugin->_(_TZ_GMTM35),
            '-3.0' => $this->_plugin->_(_TZ_GMTM3),
            '-2.0' => $this->_plugin->_(_TZ_GMTM2),
            '-1.0' => $this->_plugin->_(_TZ_GMTM1),
            '0.0' => $this->_plugin->_(_TZ_GMT0),
            '1.0' => $this->_plugin->_(_TZ_GMTP1),
            '2.0' => $this->_plugin->_(_TZ_GMTP2),
            '3.0' => $this->_plugin->_(_TZ_GMTP3),
            '3.5' => $this->_plugin->_(_TZ_GMTP35),
            '4.0' => $this->_plugin->_(_TZ_GMTP4),
            '4.5' => $this->_plugin->_(_TZ_GMTP45),
            '5.0' => $this->_plugin->_(_TZ_GMTP5),
            '5.5' => $this->_plugin->_(_TZ_GMTP55),
            '6.0' => $this->_plugin->_(_TZ_GMTP6),
            '7.0' => $this->_plugin->_(_TZ_GMTP7),
            '8.0' => $this->_plugin->_(_TZ_GMTP8),
            '9.0' => $this->_plugin->_(_TZ_GMTP9),
            '9.5' => $this->_plugin->_(_TZ_GMTP95),
            '10.0' => $this->_plugin->_(_TZ_GMTP10),
            '11.0' => $this->_plugin->_(_TZ_GMTP11),
            '12.0' => $this->_plugin->_(_TZ_GMTP12)
        );
        $element = $form->createElement('select', 'timezone_offset', array($this->_plugin->_('Time zone'), null, $this->_plugin->_('Select the appropriate time zone for your location from the list below.')), $options);
        $default = $GLOBALS['xoopsConfig']['default_TZ'] % 1 ? (string)$GLOBALS['xoopsConfig']['default_TZ'] : intval($GLOBALS['xoopsConfig']['default_TZ']) . '.0';
        $element->setSelected($default);
        $form->addElement($element);
    }

    protected function _addIMAccountsField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_isFieldEnabled($this->_plugin->getParam('imAccountsField'))) return;

        $im_accounts[] = $form->createElement('text', 'user_icq', array($this->_plugin->_('ICQ')), array('size' => 50, 'maxlength' => 15));
        $im_accounts[] = $form->createElement('text', 'user_aim', array($this->_plugin->_('AOL Instant Messenger')), array('size' => 50, 'maxlength' => 18));
        $im_accounts[] = $form->createElement('text', 'user_yim', array($this->_plugin->_('Yahoo! Messenger')), array('size' => 50, 'maxlength' => 25));
        $im_accounts[] = $form->createElement('text', 'user_msnm', array($this->_plugin->_('MSN Messenger')), array('size' => 50, 'maxlength' => 100));
        $form->addGroup($im_accounts, 'im_accounts', array($this->_plugin->_('IM accounts'), null, $this->_plugin->_('Enter your IM accounts below if any to let other users contact you easier.')), '', false);
    }

    protected function _addLocationField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_isFieldEnabled($this->_plugin->getParam('locationField'))) return;

        $form->addElement('text', 'user_from', array($this->_plugin->_('Location')), array('size' => 80, 'maxlength' => 100));
    }

    protected function _addOccupationField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_isFieldEnabled($this->_plugin->getParam('occupationField'))) return;

        $form->addElement('text', 'user_occ', array($this->_plugin->_('Occupation')), array('size' => 80, 'maxlength' => 100));
    }

    protected function _addInterestsField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_isFieldEnabled($this->_plugin->getParam('interestsField'))) return;

        $form->addElement('text', 'user_intrest', array($this->_plugin->_('Interests'), null, $this->_plugin->_('Enter things you are interested in')), array('size' => 80, 'maxlength' => 150));
    }

    protected function _addSitePreferencesField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_plugin->getApplication()->isType(Plugg::XOOPSCUBE_LEGACY) ||
            !$this->_isFieldEnabled($this->_plugin->getParam('sitePreferencesField'))
        ) {
            return;
        }

        $xcube_root = XCube_Root::getSingleton();
        $xcube_root->mLanguageManager->loadModuleMessageCatalog('user');
        $xcube_root->mLanguageManager->loadPageTypeMessageCatalog('notification');
        $umodes = array('nest' => h(_NESTED), 'flat' => h(_FLAT), 'thread' => h(_THREADED));
        $uorders = array(0 => h(_OLDESTFIRST), 1 => h(_NEWESTFIRST));
        $notify_methods = array(0 => h(_NOT_METHOD_DISABLE), 1 => h(_NOT_METHOD_PM), 2 => h(_NOT_METHOD_EMAIL));
        $notify_modes = array(0 => h(_NOT_MODE_SENDALWAYS), 1 => h(_NOT_MODE_SENDONCE), 2 => h(_NOT_MODE_SENDONCEPERLOGIN));
        $site_options[] = $form->createElement('select', 'umode', array(_MD_USER_LANG_UMODE), $umodes);
        $site_options[] = $form->createElement('select', 'uorder', array(_MD_USER_LANG_UORDER), $uorders);
        $site_options[] = $form->createElement('select', 'notify_method', array(_MD_USER_LANG_NOTIFY_METHOD, null, $this->_plugin->_('Select how you would like to receive notification messeges.')), $notify_methods);
        $site_options[] = $form->createElement('select', 'notify_mode', array(_MD_USER_LANG_NOTIFY_MODE), $notify_modes);
        $site_options[] = $form->createElement('textarea', 'user_sig', array(_MD_USER_LANG_USER_SIG, null, $this->_plugin->_('Enter your signature that may be attached to the end of your posted content.')), array('rows' => 10, 'cols' => 60));
        $site_options[] = $form->createElement('altselect', 'attachsig', _MD_USER_LANG_ATTACHSIG, array(1 => $this->_plugin->_('Yes'), 0 => $this->_plugin->_('No')));
        $form->addGroup($site_options, 'site_options', array($this->_plugin->_('Site preferences'), null, $this->_plugin->_('Select some options to make this website more useful to you. Note that not all contents support these features.') ), '', false);
    }

    protected function _addExtraInfoField(Sabai_HTMLQuickForm $form)
    {
        if (!$this->_isFieldEnabled($this->_plugin->getParam('extraInfoField'))) return;

        $form->addElement('textarea', 'bio', array($this->_plugin->_('About me'), null, $this->_plugin->_('Enter any extra information you would like other users to see on your profile page.')), array('rows' => 10, 'cols' => 60));
    }

    abstract protected function _isFieldEnabled($value);
}