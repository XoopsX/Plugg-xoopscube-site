<?php
class Plugg_Search_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Search plugin');
        $this->_nicename = $this->_('Search');
        $this->_uninstallable = false;
        $this->_params = array(
            'searchEnginePlugin' => array(
                'label' => array($this->_('Search engine plugin'), null, $this->_('IMPORTANT! If you switch to another search engine plugin, searchable contents must be imported manually to the new search engine data source.')),
                'required' => false,
                'type' => 'radio',
                'options_event' => 'SearchEnginePluginOptions',
            ),
            'keywordMinLength' => array(
                'label'    => $this->_('Minimum length of search keyword allowed'),
                'default'  => 5,
                'required' => true,
                'type' => 'radio',
                'numeric' => true,
                'options' => array(1 => 1, 2 => 2, 3 => 3, 5 => 5, 10 => 10, 20 => 20),
                'delimiter' => '&nbsp;'
            ),
            'numResultsPage' => array(
                'label'    => $this->_('Number of search results to display on each page'),
                'default'  => 20,
                'required' => true,
                'type' => 'radio',
                'numeric' => true,
                'options' => array(1 => 1, 5 => 5, 10 => 10, 20 => 20, 30 => 30, 50 => 50),
                'delimiter' => '&nbsp;'
            ),
            'rebuildSearchEngine' => array(
                'label'    => array($this->_('Rebuild search engine contents'), null, $this->_('Select yes to rebuild search engine contents upon next cron update. IMPORTANT! Turning on this option could impose a very large load on the server.') ),
                'default'  => false,
                'required' => true,
                'type' => 'yesno',
            ),
        );
    }
}