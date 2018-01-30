<?php
require_once 'SabaiPlugin.php';
require_once 'Plugg/Plugin.php';

class Plugg_PluginManager extends SabaiPlugin
{
    protected $_plugg;
    private $_pluginRepository, $_cache;

    public function __construct(Plugg $plugg)
    {
        parent::__construct($plugg->getConfig('pluginDir'), 'Plugg_');
        $this->_pluginRepository = $plugg->getLocator()->createService(
            'Model',
            array(
                'DB' => $plugg->getLocator()->createService('DB', array(
                    'tablePrefix' => $plugg->getLocator()->getDefaultParam('DB', 'tablePrefix') . 'system_'
                )),
                'modelDir' => $this->_pluginDir . '/System/Model',
                'modelPrefix' => $this->_pluginPrefix . 'System_Model_',
                'UserIdentityFetcher' => null,
            )
        )->getRepository('Plugin');
        $this->_cache = $plugg->getLocator()->createService(
            'Cache',
            array(
                // Change the cache name slightly different from the default to prevent collisions
                'cacheName' => $plugg->getLocator()->getDefaultParam('Cache', 'cacheName') . '-plugins'
            )
        );
        $this->_plugg = $plugg;
    }

    public function dispatchEvent(Plugg_Event $event, $force = false)
    {
        // This event should be forced to allow multiple occurrences
        $this->dispatch('PluggEventDispatched', array($event), null, true);

        $this->_eventDispatcher->dispatchEvent($event, $force);
    }

    public function dispatchPluginEvent(Plugg_Event $event, $pluginName = null, $force = false)
    {
        // This event should be forced to allow multiple occurrences
        $this->dispatch('PluggEventDispatched', array($event, $pluginName), null, true);

        $this->_eventDispatcher->dispatchListenerEvent($pluginName, $event, $force);
    }

    public function reloadPlugins()
    {
        // Clear cache
        $this->_cache->clean();

        parent::reloadPlugins();
    }

    public function getLocalPlugins($force = false)
    {
        require_once 'Plugg/PluginInfo.php';
        return parent::getLocalPlugins($force);
    }

    protected function _isPluginDataCached($id)
    {
        if (!$cached = $this->_cache->get($id)) {
            return false;
        }
        return unserialize($cached);
    }

    protected function _cachePluginData($data, $id)
    {
        $this->_cache->save(serialize($data), $id);
    }

    protected function _doGetInstalledPlugins()
    {
        $ret = array();
        foreach ($this->_pluginRepository->fetch() as $plugin) {
            $ret[$plugin->name] = array(
                'params'  => $plugin->getParams(),
                'version' => $plugin->version,
                'library' => $plugin->library,
                'active'  => $plugin->active,
                'extra'   => array('nicename' => $plugin->nicename),
            );
        }
        return $ret;
    }

    protected function _doGetActivePlugins()
    {
        $ret = array();
        $plugins = $this->_pluginRepository
            ->criteria()
            ->active_is(1)
            ->fetch(0, 0, 'plugin_priority', 'DESC');
        foreach ($plugins as $plugin) {
            $ret[$plugin->name] = array(
                'params'  => $plugin->getParams(),
                'version' => $plugin->version,
                'library' => $plugin->library,
                'extra'   => array('nicename' => $plugin->nicename),
            );
        }
        return $ret;
    }

    /**
     * Overrides the parent method to pass in the Plugg instance
     */
    protected function _getPluginInfo($class, $file, $dir)
    {
        return new $class($file, $dir, $this->_plugg);
    }

    /**
     * Overrides the parent method to pass in the Plugg instance
     */
    protected function _createPluginHandle($name, $library, $version, array $params = array(), array $extra = array())
    {
        $plugin_path = $this->_pluginDir . '/' . $library;
        $handle = new Sabai_Handle_Decorator_Cache(
            new Sabai_Handle_Decorator_Autoload(
                new Sabai_Handle_Class($this->_pluginPrefix . $library . '_Plugin',
                    array($name, $plugin_path, $version, $params, $library, $extra, $this->_plugg)),
                $plugin_path . '/Plugin.php'
            )
        );
        return $handle;
    }
}