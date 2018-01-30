<?php
class Plugg_HyperEstraier_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Fulltext search engine plugin using the HyperEstraier library');
        $this->_nicename = $this->_('HyperEstraier fulltext search engine');
        $this->_requiredPlugins = array('Search');
        //$this->_requiredLibs = array('Services_HyperEstraier');
        $this->_params = array(
            'nodeServerUrl' => array(
                'type'     => 'url',
                'label'    => $this->_('HyperEstraier node server URL'),
                'default'  => 'http://localhost:1978/node/test',
                'required' => true
            ),
            'nodeServerUser' => array(
                'label'    => $this->_('HyperEstraier node server user name'),
                'default'  => '',
                'required' => true
            ),
            'nodeServerPassword' => array(
                'label'    => $this->_('HyperEstraier node server user password'),
                'default'  => '',
                'required' => true
            ),
            'snippetWidth' => array(
                'label'    => $this->_('Whole width of a snippet text in bytes'),
                'default'  => 480,
                'required' => true,
                'numeric' => true
            ),
        );
    }
}