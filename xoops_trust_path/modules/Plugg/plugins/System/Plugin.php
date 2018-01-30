<?php
class Plugg_System_Plugin extends Plugg_Plugin
{
    public function onPluggAdminRoutes($routes)
    {
        $routes['/' . $this->_name] = array(
            'controller' => sprintf('Plugg_%s_Admin', $this->_library),
            'controller_file' => $this->_path . '/Admin.php',
            'context' => array('plugin' => $this),
            'title' => $this->_('Plugins'),
            'tab' => true,
            'tab_ajax' => true,
        );
    }

    public function isPluginInstallable($library, &$error, $clone = false)
    {
        $library_lc = strtolower($library);
        $reserved_plugin_names = array('admin', 'main', 'system', 'core', 'kernel', 'application', 'model', 'db', 'cache', 'controller', 'role', strtolower($this->_application->getName()));
        if (in_array($library_lc, $reserved_plugin_names)) {
            $error = sprintf($this->_('Plugin name %s is reserved by the system'), $library);
            return false;
        }
        if ($this->_application->getPluginManager()->isPluginInstalled($library_lc, true)) {
            if (!$clone) {
                $error = sprintf($this->_('Plugin with the name %s is installed already'), $library_lc);
                return false;
            }
        } else {
            if ($clone) {
                // library(original) must be installed before cloning
                $error = $this->_('Invalid plugin library');
                return false;
            }
        }
        if (!$data = $this->_application->getPluginManager()->getLocalPlugin($library)) {
            $error = $this->_('Invalid plugin');
            return false;
        }
        if ($clone && !$data['cloneable']) {
            $error = $this->_('The selected plugin may not be cloned');
            return false;
        }
        if ($app_type_supported = $data['dependencies']['app']) {
            if (!($this->_application->getType() & $app_type_supported)) {
                $error = $this->_('The selected plugin is not compatible with this application');
                return false;
            }
        }
        if (($php_required = $data['dependencies']['php']) && version_compare(phpversion(), $php_required, '<')) {
            $error = sprintf($this->_('The selected plugin requires PHP %s or higher'), $php_required);
            return false;
        }
        if ($extensions_required = $data['dependencies']['extensions']) {
            foreach ($extensions_required as $extension) {
                if (!extension_loaded($extension)) {
                    $error = sprintf($this->_('The selected plugin requires the PHP %s extension to be enabled on your server'), $extension);
                    return false;
                }
            }
        }
        if ($libs_required = $data['dependencies']['libs']) {
            foreach ($libs_required as $lib) {
                $lib_file = str_replace('_', DIRECTORY_SEPARATOR, $lib) . '.php';
                if (!is_includable($lib_file)) {
                    $error = sprintf($this->_('The selected plugin requires the %s library file(s) to be available under include_path: %s'), $lib, get_include_path());
                    return false;
                }
            }
        }
        if ($db_scheme_supported = $data['dependencies']['db']) {
            $db = $this->getDB();
            $db_scheme = strtolower($db->getScheme());
            if (!in_array($db_scheme, array_map('strtolower', $db_scheme_supported))) {
                $error = $this->_('The selected plugin requires a %s database', implode('/', $db_scheme_supported));
                return false;
            }
        }
        if ($plugins_required = $data['dependencies']['plugins']) {
            $plugins_installed = $this->_application->getPluginManager()->getInstalledPlugins();
            foreach ($plugins_required as $plugin_required) {
                $plugin_required_name = strtolower($plugin_required['library']);
                if (!array_key_exists($plugin_required_name, $plugins_installed)) {
                    $error = sprintf($this->_('The selected plugin requires plugin %s to be installed'), $plugin_required['library']);
                    return false;
                }
                // Does the required plugin must be active?
                if (!empty($plugin_required['active']) && empty($plugins_installed[$plugin_required_name]['active'])) {
                    $error = sprintf($this->_('The selected plugin requires plugin %s to be installed and active'), $plugin_required['library']);
                    return false;
                }
                if (isset($plugin_required['version'])) {
                    if (version_compare($plugins_installed[$plugin_required_name]['version'], $plugin_required['version'], '<')) {
                        $error = sprintf($this->_('The selected plugin requires plugin %s version %s or higher to be installed'), $plugin_required['library'], $plugin_required['version']);
                        return false;
                    }
                }
            }
        }
        return $data;
    }

    public function installPlugin($library, $data, $nicename, $params = array(), $priority = 0, $active = 1, $name = null)
    {
        $entity = $this->getModel()->create('Plugin');
        $entity->name = empty($name) ? strtolower($library) : strtolower($name);
        $entity->library = $library;
        $entity->locked = !$data['uninstallable'];
        $plugin_params = array();
        foreach (array_keys($data['params']) as $param_name) {
            if (isset($params[$param_name])) {
                $plugin_params[$param_name] = $params[$param_name];
            } elseif (isset($data['params'][$param_name]['default'])) {
                $plugin_params[$param_name] = $data['params'][$param_name]['default'];
            }
        }
        $entity->setParams($plugin_params);
        $entity->version = $data['version'];
        $entity->active = $active;
        $entity->priority = $priority;
        $entity->nicename = $nicename;
        $entity->markNew();
        if ($entity->commit()) {
            $this->_application->getPluginManager()->reloadPlugins();
            $message = '';
            if ($this->_application->getPlugin($entity->name)->install($message)) {
                return $entity;
            } else {
                $entity->markRemoved();
                if (!$entity->commit()) {
                    $message .= ' ' . $this->_('Additionally, failed deleting plugin data from the database. Please uninstall the plugin manually.');
                }
                $this->_application->getPluginManager()->reloadPlugins();
            }
        } else {
            $message = $this->_('Failed inserting plugin data into the database. ' . $this->getModel()->getCommitError());
        }
        return $message;
    }

    public function onSystemAdminPluginInstalled($pluginEntity)
    {
        if (($plugin = $this->_application->getPlugin($pluginEntity->name)) &&
            ($interfaces = class_implements($plugin)) // get interfaces implemented by the plugin
        ) {
            foreach ($interfaces as $interface) {
                if (stripos($interface, 'plugg_') == 0) {
                    $event = str_replace('_', '', substr($interface, 6))  . 'Installed'; // Remove the plugg_ prefix
                    $this->_application->dispatchEvent($event, array($pluginEntity, $plugin));
                }
            }
        }
    }

    public function onSystemAdminPluginUninstalled($pluginEntity, $plugin)
    {
        // get interfaces implemented by the plugin
        if ($interfaces = class_implements($plugin, false)) {
            foreach ($interfaces as $interface) {
                if (stripos($interface, 'plugg_') == 0) {
                    $event = str_replace('_', '', substr($interface, 6))  . 'Uninstalled'; // Remove the plugg_ prefix
                    $this->_application->dispatchEvent($event, array($pluginEntity, $plugin));
                }
            }
        }
    }

    public function onSystemAdminPluginUpgraded($pluginEntity)
    {
        if (($plugin = $this->_application->getPlugin($pluginEntity->name)) &&
            ($interfaces = class_implements($plugin, false)) // get interfaces implemented by the plugin
        ) {
            foreach ($interfaces as $interface) {
                if (stripos($interface, 'plugg_') == 0) {
                    $event = str_replace('_', '', substr($interface, 6))  . 'Upgraded'; // Remove the plugg_ prefix and underscores
                    $this->_application->dispatchEvent($event, array($pluginEntity, $plugin));
                }
            }
        }
    }

    public function onSystemAdminPluginConfigured($pluginEntity, $paramsOld)
    {
        if (($plugin = $this->_application->getPlugin($pluginEntity->name)) &&
            ($interfaces = class_implements($plugin, false)) // get interfaces implemented by the plugin
        ) {
            foreach ($interfaces as $interface) {
                if (stripos($interface, 'plugg_') == 0) {
                    $event = str_replace('_', '', substr($interface, 6))  . 'Configured'; // Remove the plugg_ prefix
                    $this->_application->dispatchEvent($event, array($pluginEntity, $plugin, $paramsOld));
                }
            }
        }
    }

    public function onSystemPlugins($plugins, $library = null, $escape = false)
    {
        if (!empty($library)) {
            $plugins_installed = $this->getModel()->Plugin
                ->criteria()
                ->library_is($library)
                ->fetch();
        } else {
            $plugins_installed = $this->getModel()->Plugin->fetch();
        }

        foreach ($plugins_installed as $plugin) {
            $plugins[$plugin->name] = $escape ? h($plugin->nicename) : $plugin->nicename;
        }
    }
}