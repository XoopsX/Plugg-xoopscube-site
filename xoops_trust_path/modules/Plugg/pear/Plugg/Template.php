<?php
require_once 'Sabai/Template/PHP.php';

class Plugg_Template extends Sabai_Template_PHP
{
    public function __construct()
    {
        parent::__construct(dirname(__FILE__) . '/templates');
    }

    public function _($message)
    {
        if (isset($this->Plugin)) {
            return $this->Plugin->_($message);
        }
        return $this->Gettext->_($message);
    }

    public function _e($message)
    {
        if (isset($this->Plugin)) {
            return $this->Plugin->_e($message);
        }
        return $this->Gettext->_e($message);
    }

    public function ngettext($message1, $message2, $num)
    {
        if (isset($this->Plugin)) {
            return $this->Plugin->ngettext($message1, $message1, $num);
        }
        return $this->Gettext->ngettext($message1, $message2, $num);
    }

    public function setPluggObjects(Plugg $plugg)
    {
        $this->setObject('URL', $plugg->getUrl());
        $this->setObject('Config', $plugg->getConfig());
        $this->setObject('Locator', $plugg->getLocator());
        $this->setObject('Gettext', $plugg->getGettext());
        $this->setObject('PluginManager', $plugg->getPluginManager());

        return $this;
    }
}