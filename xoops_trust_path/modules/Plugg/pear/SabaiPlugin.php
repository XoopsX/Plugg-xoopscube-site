<?php
require_once 'Sabai/Application.php';
require_once 'Sabai/Event/Dispatcher.php';
require_once 'Sabai/Handle/Decorator/Cache.php';
require_once 'Sabai/Handle/Decorator/Autoload.php';
require_once 'Sabai/Handle/Class.php';

abstract class SabaiPlugin /*extends Sabai_Application*/
{
    protected $_pluginDir;
    protected $_pluginPrefix;
    protected $_eventDispatcher;
    private $_plugins = array();
    private $_pluginsInstalled;
    private $_pluginsActive;

    const PLUGIN_LIBRARY_REGEX = '/^[a-zA-Z]+[a-zA-Z0-9]*[a-zA-Z0-9]+$/';
    const PLUGIN_NAME_REGEX = '/^[a-z]+[a-z0-9]*[a-z0-9]+$/';

    protected function __construct(/*$id, $name, $path, $url, $script, */$pluginDir, $pluginPrefix)
    {
        //parent::Sabai_Application($id, $name, $path, $url, $script);
        $this->_pluginDir = $pluginDir;
        $this->_pluginPrefix = $pluginPrefix;
        $this->_eventDispatcher = new Sabai_Event_Dispatcher();
    }

    public function getPluginDir()
    {
        return $this->_pluginDir;
    }

    /**
     * Gets an already installed plugin
     *
     * @param string $pluginName
     * @param bool $mustBeActive
     * @return mixed SabaiPlugin_Plugin if plugin found, false otherwise
     */
    public function getPlugin($pluginName, $mustBeActive = true)
    {
        if (isset($this->_plugins[$pluginName])) return $this->_plugins[$pluginName];

        if ($handle = $this->getPluginHandle($pluginName, $mustBeActive)) {
            $this->_plugins[$pluginName] = $handle->instantiate();
            return $this->_plugins[$pluginName];
        }

        return false;
    }

    /**
     * Gets a handle object of already installed plugin
     *
     * @param string $pluginName
     * @param bool $mustBeActive
     * @ret Sabai_Handle
     */
    public function getPluginHandle($pluginName, $mustBeActive = true)
    {
        // Is the plugin subscribed to any events?
        if ($this->_eventDispatcher->listenerExists($pluginName)) {
            // Retrieve from registered listenters
            return $this->_eventDispatcher->getListenerHandle($pluginName);
        } elseif ($mustBeActive) {

            if ($active = $this->isPluginActive($pluginName)) {
                // The plugin is active, but without any subscribed events, so load the plugin manually
                return $this->_getPluginHandle($pluginName, $active['library'], $active);
            }
        } else {
            if ($installed = $this->isPluginInstalled($pluginName)) {
                // The plugin is installed but inactive, so load the plugin manually
                return $this->_getPluginHandle($pluginName, $installed['library'], $installed);
            }
        }
        return false;
    }

    /**
     * Gets a plugin which is not yet installed
     *
     * @param string $library
     * @param string $name
     * @return SabaiPlugin_Plugin
     */
    public function fetchPlugin($library, $name = null)
    {
        if (!$local_data = $this->getLocalPlugin($library)) {
            return false;
        }
        if (!isset($name)) $name = strtolower($library);
        return $this->_getPluginHandle($name, $library, array(), $local_data)->instantiate();
    }

    public function callPlugin($name, $method, $params = array())
    {
        $plugin = $this->getPlugin($name);
        return call_user_func_array(array($plugin, $method), $params);
    }

    public function dispatch($eventType, array $eventArgs = array(), $pluginName = null, $force = false)
    {
        Sabai_Log::info(sprintf('Event "%s" dispatched', $eventType));
        $this->_eventDispatcher->dispatch($eventType, $eventArgs, $pluginName, $force);
    }

    protected function _createPluginHandle($name, $library, $version, array $params = array(), array $extra = array())
    {
        $plugin_path = $this->_pluginDir . '/' . $library;
        return new Sabai_Handle_Decorator_Cache(
            new Sabai_Handle_Decorator_Autoload(
                new Sabai_Handle_Class($this->_pluginPrefix . $library . '_Plugin', array($name, $plugin_path, $version, $params, $library, $extra)),
                $plugin_path . '/Plugin.php'
            )
        );
    }

    protected function _getPluginHandle($pluginName, $pluginLibrary, array $pluginData, array $localData = null)
    {
        if (!isset($localData)) {
            if (!$localData = $this->getLocalPlugin($pluginLibrary)) {
                return false;
            }
        }
        if (!empty($pluginData['params'])) {
            $plugin_params = $pluginData['params'];
            foreach ($localData['params'] as $local_param_name => $local_param_data) {
                if (!array_key_exists($local_param_name, $plugin_params)) {
                    $plugin_params[$local_param_name] = $local_param_data['default'];
                }
            }
        } else {
            $plugin_params = $localData['params'];
        }
        $plugin_extra = !empty($pluginData['extra']) ? array_merge($pluginData['extra'], $localData['extra']) : $localData['extra'];
        $plugin_version = isset($pluginData['version']) ? $pluginData['version'] : $localData['version'];
        return $this->_createPluginHandle($pluginName, $pluginLibrary, $plugin_version, $plugin_params, $plugin_extra);
    }

    public function reloadPlugins()
    {
        $this->_pluginHandles = array();
        $this->getLocalPlugins(true);
        $this->loadPlugins(true);
        $this->getPluginsDependency(true);
    }

    public function loadPlugins($force = false, $forceLocal = false)
    {
        if ($force || (!$data = $this->_isPluginDataCached('plugins'))) {
            $data = array();
            $this->_eventDispatcher->clear();
            $local = $this->getLocalPlugins($forceLocal);
            $active_plugins = $this->getActivePlugins($force);
            foreach (array_keys($active_plugins) as $plugin_name) {
                // Always use the library name as key
                $plugin_lib = $active_plugins[$plugin_name]['library'];
                if ($plugin_data = @$local[$plugin_lib]) {
                    $plugin_params = $active_plugins[$plugin_name]['params'];
                    foreach ($plugin_data['params'] as $plugin_data_param_name => $plugin_data_param_data) {
                        if (isset($plugin_data_param_data['cacheable']) && !$plugin_data_param_data['cacheable']) {
                            unset($plugin_params[$plugin_data_param_name]);
                        } elseif (!array_key_exists($plugin_data_param_name, $plugin_params)) {
                            $plugin_params[$plugin_data_param_name] = isset($plugin_data_param_data['default']) ? $plugin_data_param_data['default'] : null;
                        }
                    }
                    $plugin_extra = array_merge($active_plugins[$plugin_name]['extra'], $plugin_data['extra']);
                    $data[$plugin_name] = array('library' => $plugin_lib, 'version' => $active_plugins[$plugin_name]['version'], 'params' => $plugin_params, 'extra' => $plugin_extra, 'events' => $plugin_data['events']);
                }
            }
            $this->_cachePluginData($data, 'plugins');
        }
        foreach (array_keys($data) as $plugin_name) {
            $plugin_data = $data[$plugin_name];
            $this->_eventDispatcher->addListener($plugin_data['events'], $this->_createPluginHandle($plugin_name, $plugin_data['library'], $plugin_data['version'], $plugin_data['params'], $plugin_data['extra']), strtolower($plugin_name));
        }
    }

    public function getPluginsDependency($force = false, $forceLocal = false)
    {
        if ($force || (!$data = $this->_isPluginDataCached('plugins_dependency'))) {
            $local = $this->getLocalPlugins($forceLocal);
            $active_plugins = $this->getActivePlugins($force);
            $data = array();
            foreach (array_keys($active_plugins) as $plugin_name) {
                $plugin_lib = $active_plugins[$plugin_name]['library'];
                // is it a clone?
                if (strtolower($plugin_lib) != $plugin_name) {
                    // Add dependency with the original plugin. The original need not be active.
                    $data[$plugin_lib][$plugin_name] = array('version' => null, 'active' => false);
                }
                if ($plugin_data = @$local[$plugin_lib]) {
                    foreach ($plugin_data['dependencies']['plugins'] as $dependant_plugin) {
                        $data[$dependant_plugin['library']][$plugin_name] = array('version' => $dependant_plugin['version'], 'active' => $dependant_plugin['active']);
                    }
                }
            }
            $this->_cachePluginData($data, 'plugins_dependency');
        }
        return $data;
    }

    public function getPluginDependency($pluginLibrary, $force = false, $forceLocal = false)
    {
        $dependency = $this->getPluginsDependency($force, $forceLocal);
        return isset($dependency[$pluginLibrary]) ? $dependency[$pluginLibrary] : false;
    }

    public function getLocalPlugins($force = false)
    {
        if ($force || (!$data = $this->_isPluginDataCached('plugins_local'))) {
            $data = array();
            if ($dh = opendir($this->_pluginDir)) {
                while (false !== $file = readdir($dh)) {
                    if (preg_match(self::PLUGIN_LIBRARY_REGEX, $file) && empty($data[$file])) {
                        $plugin_dir = $this->_pluginDir . '/' . $file;
                        if (is_dir($plugin_dir)) {
                            $plugin_file_info = $plugin_dir . '/PluginInfo.php';
                            $plugin_file_main = $plugin_dir . '/Plugin.php';
                            if (file_exists($plugin_file_info) && file_exists($plugin_file_main)) {
                                require_once $plugin_file_info;
                                require_once $plugin_file_main;
                                $plugin_class_info = $this->_pluginPrefix . $file . '_PluginInfo';
                                $plugin_class_main = $this->_pluginPrefix . $file . '_Plugin';
                                if (class_exists($plugin_class_info, false) && class_exists($plugin_class_main, false)) {
                                    $plugin_events = array_map(array($this, '_mapPluginEvent'), array_filter(get_class_methods($plugin_class_main), array($this, '_filterPluginEvent')));
                                    $plugin_info = $this->_getPluginInfo($plugin_class_info, $file, $plugin_dir);
                                    // param names starting with an underscore(_) is reserverd for internal use
                                    $plugin_params = $plugin_info->getParams();
                                    foreach (array_keys($plugin_params) as $plugin_param_name) {
                                        if (strpos($plugin_param_name, '_') === 0) {
                                            unset($plugin_params[$plugin_param_name]);
                                        }
                                    }
                                    $data[$file] = array(
                                        'params'        => $plugin_params,
                                        'version'       => $plugin_info->getVersion(),
                                        'summary'       => $plugin_info->getSummary(),
                                        'nicename'      => $plugin_info->getNicename(),
                                        'uninstallable' => $plugin_info->isUninstallable(),
                                        'events'        => $plugin_events,
                                        'dependencies'  => $plugin_info->getDependencies(),
                                        'cloneable'     => $plugin_info->isCloneable(),
                                        'extra'         => $plugin_info->getExtra(),
                                    );
                                }
                            }
                        }
                    }
                }
                closedir($dh);
                ksort($data);
                $this->_cachePluginData($data, 'plugins_local');
            }
        }
        return $data;
    }

    public function getInstalledPlugins($force = false)
    {
        if ($force || !isset($this->_pluginsInstalled)) {
            $this->_pluginsInstalled = $this->_doGetInstalledPlugins();
        }
        return $this->_pluginsInstalled;
    }

    public function getActivePlugins($force = false)
    {
        if ($force || !isset($this->_pluginsActive)) {
            $this->_pluginsActive = $this->_doGetActivePlugins();
        }
        return $this->_pluginsActive;
    }

    public function getLocalPlugin($pluginLibrary, $force = false)
    {
        $local_plugins = $this->getLocalPlugins($force);
        return isset($local_plugins[$pluginLibrary]) ? $local_plugins[$pluginLibrary] : false;
    }

    public function isPluginInstalled($pluginName, $force = false)
    {
        $plugins = $this->getInstalledPlugins($force);
        return isset($plugins[$pluginName]) ? $plugins[$pluginName] : false;
    }

    public function isPluginActive($pluginName, $force = false)
    {
        $plugins = $this->getActivePlugins($force);
        return isset($plugins[$pluginName]) ? $plugins[$pluginName] : false;
    }

    protected function _getPluginInfo($class, $file, $dir)
    {
        return new $class($file, $dir);
    }

    private function _filterPluginEvent($method)
    {
        return strpos(strtolower($method), 'on') === 0;
    }

    private function _mapPluginEvent($method)
    {
        return strtolower(substr($method, 2));
    }

    abstract protected function _isPluginDataCached($id);
    abstract protected function _cachePluginData($data, $id);
    abstract protected function _doGetInstalledPlugins();
    abstract protected function _doGetActivePlugins();
}