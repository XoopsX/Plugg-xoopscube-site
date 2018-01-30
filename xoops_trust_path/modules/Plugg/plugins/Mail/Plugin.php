<?php
class Plugg_Mail_Plugin extends Plugg_Plugin
{
    public function getSenderPlugin()
    {
        if ($plugin_name = $this->getParam('mailSenderPlugin')) {
            if ($plugin = $this->_application->getPlugin($plugin_name)) {
                return $plugin;
            }
        }
        throw new Plugg_Exception(sprintf('Mailer plugin %s could not be found', $plugin_name));
    }

    public function getSender()
    {
        return $this->getSenderPlugin()->mailGetSender();
    }
}