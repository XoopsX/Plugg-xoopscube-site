<?php
class Plugg_Page_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Page plugin');
        $this->_nicename = $this->_('Page');
        $this->_requiredPlugins = array('User', 'Filter');
        $this->_cloneable = true;
        $this->_params = array(
            'showNavigation' => array(
                'label'    => $this->_('Show navigation at the bottom'),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
            'lock' => array(
                'label'    => $this->_('Lock all pages'),
                'default'  => 0,
                'required' => true,
                'type'     => 'yesno'
            ),
            'showIndexPageLink' => array(
                'label'    => $this->_('Show link to index page in breadcrumbs'),
                'default'  => 1,
                'required' => true,
                'type'     => 'yesno'
            ),
        );
    }
}