<?php
require_once 'SabaiPlugin/PluginInfo.php';

class Plugg_PluginInfo extends SabaiPlugin_PluginInfo
{
    private $_gettextDomain;
    protected $_application, $_supportedAppType, $_supportedDbSchemes;

    function __construct($library, $path, Plugg $application)
    {
        parent::__construct($library, $path);
        $extra = array(
            'hasModel' => is_dir($this->_path . '/Model'),
            'hasSchema' => file_exists($this->_path . '/schema/latest.xml'),
            'hasMainCSS' => file_exists($this->_path . '/css/Main.css'),
            'hasAdminCSS' => file_exists($this->_path . '/css/Admin.css'),
            'hasLocale' => file_exists($this->_path . '/' . $this->_library . '.pot'),
            'hasInfoLocale' => file_exists($this->_path . '/' . $this->_library . 'Info.pot'),
        );
        $this->setExtra($extra);
        $this->_application = $application;
        $this->_gettextDomain = strtolower($this->_library . '-info');
        $this->_application->getGettext()->loadMessages($this->_gettextDomain, $this->_library . 'Info.mo');
    }

    public function _($msgId)
    {
        if ($this->_application->getGettext()->dhastext($this->_gettextDomain, $msgId)) {
            return $this->_application->getGettext()->dgettext($this->_gettextDomain, $msgId);
        }

        // If message cannt be found in the domain, find it from the global domain
        return $this->_application->getGettext()->gettext($msgId);
    }

    public function ngettext($msgId, $msgId2, $num)
    {
        if ($this->_application->getGettext()->dhastext($this->_gettextDomain, $msgId)) {
            return $this->_application->getGettext()->dngettext($this->_gettextDomain, $msgId, $msgId2, $num);
        }

        // If message cannt be found in the domain, find it from the global domain
        return $this->_application->getGettext()->ngettext($msgId, $msgId2, $num);
    }

    public function getDependencies()
    {
        return array_merge(parent::getDependencies(), array(
            'app' => $this->_supportedAppType,
            'db' => (array)$this->_supportedDbSchemes
        ));
    }
}