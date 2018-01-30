<?php
class Plugg_Message_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Messaging plugin');
        $this->_uninstallable = true;
        $this->_cloneable = false;
        $this->_requiredPlugins = array('User');
        $this->_nicename = $this->_('Messages');
        $this->_params = array(
            'deleteOlderThanDays' => array(
                'type'     => 'radio',
                'numeric'  => true,
                'label'    => $this->_('Periodically delete messages without a star and older than following days:'),
                'default'  => 100,
                'options'  => array(0 => $this->_('Never'), 5 => 5, 10 => 10, 30 => 30, 50 => 50, 100 => 100, 365 => 365),
                'required' => true,
                'delimiter' => '&nbsp;'
            ),
        );
    }
}