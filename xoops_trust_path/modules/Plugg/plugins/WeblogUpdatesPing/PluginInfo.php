<?php
class Plugg_WeblogUpdatesPing_PluginInfo extends Plugg_PluginInfo
{
    public function __construct($library, $path, $application)
    {
        parent::__construct($library, $path, $application);
        $this->_version = '1.0.0';
        $this->_summary = $this->_('Sends update notification ping to ping service servers');
        $this->_requiredLibs = array('XML_RPC');
        $this->_params = array(
            'blogName' => array(
                'label' => array($this->_('Blog name')),
                'default' => $application->getConfig('siteName'),
                'required' => true
            ),
            'blogUrl'  => array(
                'label' => array($this->_('Blog URL')),
                'default' => $application->getConfig('siteUrl'),
                'required' => true,
                'type' => 'url'
            ),
            'pingServers' => array(
                'label' => array(
                    $this->_('weblogUpdates.ping servers'),
                    $this->_('Enter the URL of weblogUpdates.ping servers, one each line.')
                ),
                'default' => array(
                    'http://rpc.weblogs.com/RPC2',
                    'http://ping.blo.gs',
                    'http://rpc.technorati.com/rpc/ping'
                ),
                'required' => false,
                'type' => 'input_multi'
            ),
            'extendedPingServers'  => array(
                'label' => array(
                    $this->_('weblogUpdates.extendedPing servers'),
                    $this->_('Enter the URL of weblogUpdates.extenededPing servers, one each line.')
                ),
                'default' => array(
                    'http://blogsearch.google.co.jp/ping/RPC2',
                ),
                'required' => false,
                'type' => 'input_multi'
            ),
        );
    }
}