<?php
class Plugg_SimpleSearch_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('A simple search engine plugin using database as backend');
        $this->_nicename = $this->_('Simple search engine');
        $this->_requiredPlugins = array('Search');
        $this->_params = array(
            'snippetWidth' => array(
                'label'    => $this->_('Whole width of a snippet text in bytes'),
                'default'  => 480,
                'required' => true,
                'numeric' => true
            ),
        );
    }
}