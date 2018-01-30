<?php
abstract class SabaiPlugin_PluginInfo
{
    protected $_library;
    protected $_path;
    protected $_version = '0.0.1dev';
    protected $_summary = '';
    protected $_uninstallable = true;
    protected $_params = array();
    protected $_requiredPHP = '';
    protected $_requiredExtensions = array();
    protected $_requiredLibs = array();
    protected $_requiredPlugins = array();
    protected $_cloneable = false;
    protected $_extra;
    protected $_nicename = '';

    protected function SabaiPlugin_PluginInfo($library, $path)
    {
        $this->_library = $library;
        $this->_path = $path;
    }

    public function getVersion()
    {
        return $this->_version;
    }

    public function getSummary()
    {
        return $this->_summary;
    }
    
    public function getNicename()
    {
        return $this->_nicename;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function setExtra($extra)
    {
        $this->_extra = $extra;
    }

    public function getExtra()
    {
        return $this->_extra;
    }

    public function getDependencies()
    {
        $plugins = array();
        foreach ($this->_getRequiredPlugins() as $plugin) {
            settype($plugin, 'array');
            $plugins[] = array(
              'library' => $plugin[0],
              'version' => isset($plugin[1]) ? $plugin[1] : null,
              'active' => isset($plugin[2]) ? (bool)$plugin[2] : true,
            );
        }
        return array(
            'php' => $this->_getRequiredPHP(),
            'extensions' => $this->_getRequiredExtensions(),
            'plugins' => $plugins,
            'libs' => $this->_getRequiredLibs(),
        );
    }

    public function isUninstallable()
    {
        return $this->_uninstallable;
    }

    public function isCloneable()
    {
        return $this->_cloneable;
    }

    protected function _getRequiredPHP()
    {
        return $this->_requiredPHP;
    }

    protected function _getRequiredExtensions()
    {
        return $this->_requiredExtensions;
    }
    
    protected function _getRequiredLibs()
    {
        return $this->_requiredLibs;
    }

    protected function _getRequiredPlugins()
    {
        return $this->_requiredPlugins;
    }
}