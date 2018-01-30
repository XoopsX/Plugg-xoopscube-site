<?php
class Plugg_TextareaResizer_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('This plugin allows the user to extend the textarea element/area within the web page whenever they feel.');
        $this->_nicename = $this->_('TextareaResizer');
        $this->_params = array();
        $this->_requiredPlugins = array('jQuery');
    }
}