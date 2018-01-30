<?php
require_once 'SabaiXOOPS/ModuleInstaller.php';

class plugg_xoops_module_installer extends SabaiXOOPS_ModuleInstaller
{
    private $_app;

    public function __construct(Sabai_Application $app)
    {
        parent::__construct();
        $this->_app = $app;
    }

    protected function _doExecute($module)
    {
        $this->_app->debug();

        // Init cache directories
        $log = 'Initializing cache and media directories...';
        foreach (array($this->_app->getConfig('cacheDir'), $this->_app->getConfig('mediaDir')) as $dir) {
            if (!is_writable($dir)) {
                if (!@chmod($dir, 0777)) {
                    $log .= sprintf('failed setting the permission of %s to 0777. Please set the permission manually...', $dir);
                } else {
                    $log .= sprintf('permission of %s set to 0777...', $dir);
                }
            } else {
                $log .= sprintf('%s is already writable...', $dir);
            }
        }
        $log .= 'done.';
        $this->addLog($log);

        // Install required plugins
        $log = 'Installing required plugins...';

        // Install the System plugin
        if (!$system = $this->_app->getPluginManager()->fetchPlugin('System')) {
            $log .= 'failed fetching the System plugin.';
            $this->addLog($log);
            return false;
        }
        $message = '';
        if (!$system->install($message)) {
            $log .= sprintf('failed installing the System plugin. Error: %s', $message);
            $this->addLog($log);
            return false;
        }
        $log .= 'System installed...';

        // Install other required plugins
        $plugins_required = array(
            'HTMLPurifier' => array(),
            'Filter' => array(),
            'Mail' => array('mailSenderPlugin' => 'xoopscube'),
            'Search' => array('searchEnginePlugin' => 'simplesearch'),
            'XOOPSCube' => array(),
            'User' => array('userManagerPlugin' => 'xoopscubeuserapi'),
            'XOOPSCubeUserAPI' => array(),
            'jQuery' => array(),
            'Profile' => array(),
            'SimpleSearch' => array(),
            'Widget' => array(),
            'XOOPSCodeFilter' => array()
        );
        $plugins_installed = array('system');
        $install_failed = false;
        foreach ($plugins_required as $plugin_lib => $plugin_params) {
            $error = '';
            if (!$plugin_data = $system->isPluginInstallable($plugin_lib, $error)) {
                $install_failed = true;
                $log .= sprintf('failed installing required plugin %s. Error: %s', $plugin_lib, $error);
                break;
            } else {
                $result = $system->installPlugin($plugin_lib, $plugin_data, $plugin_data['nicename'], $plugin_params, 5);
                if (!is_object($result)) {
                    $install_failed = true;
                    $log .= sprintf('failed installing required plugin %s. Error: %s', $plugin_lib, $result);
                    break;
                } else {
                    $log .= sprintf('%s installed...', $plugin_lib);
                    $plugins_installed[] = strtolower($plugin_lib);
                    $this->_app->dispatchEvent('SystemAdminPluginInstalled', array($result));
                    $this->_app->dispatchEvent($plugin_lib . 'PluginInstalled', array($result));
                }
            }
        }

        // Uninstall all plugins if requierd plugins were not installed
        if ($install_failed) {
            $this->addLog($log);
            if (!empty($plugins_installed)) {
                $log = 'Uninstalling installed plugins...';
                foreach ($plugins_installed as $plugin_name) {
                    $message = '';
                    if ((!$plugin = $this->_app->getPlugin($plugin_name, false)) || !$plugin->uninstall($message)) {
                        $log .= sprintf('failed uninstalling the %s plugin! You must manually uninstall the plugin. Error: %s..', $plugin_name, $message);
                        continue;
                    }
                    $log .= sprintf('%s uninstalled...', $plugin_name);
                }
            }
        }
        $log .= 'done.';
        $this->addLog($log);
        return !$install_failed;
    }
}