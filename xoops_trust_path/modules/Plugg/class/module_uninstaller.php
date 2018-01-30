<?php
require_once 'SabaiXOOPS/ModuleInstaller.php';

class plugg_xoops_module_uninstaller extends SabaiXOOPS_ModuleInstaller
{
    private $_app;
    private $_lastVersion;

    public function __construct(Sabai_Application $app, $lastVersion)
    {
        parent::__construct('Legacy.Admin.Event.ModuleUninstall.%s.Success', 'Legacy.Admin.Event.ModuleUninstall.%s.Fail');
        $this->_app = $app;
        $this->_lastVersion = $lastVersion;
    }

    protected function _doExecute($module)
    {
        // Uninstall the plugins
        if ($plugins_installed = @$this->_app->getPluginManager()->getInstalledPlugins(true)) {
            $log = 'Uninstalling installed plugins...';
            foreach (array_keys($plugins_installed) as $plugin_name) {
                 if ($plugin = $this->_app->getPlugin($plugin_name, false)) {
                     $message = '';
                     if (!$plugin->uninstall($message)) {
                         $log .= sprintf('failed uninstalling the %s plugin. You must manually uninstall the plugin. Error: %s...', $plugin_name, $message);
                         continue;
                     } else {
                         $log .= '...';
                     }
                 }
                 $log .= sprintf('%s uninstalled...', $plugin_name);
            }
            $log .= 'done.';
            $this->addLog($log);
        }

        // Remove media files
        $log = 'Removing media files...';
        $dir = $this->_app->getConfig('mediaDir');
        if ($dh = opendir($dir)) {
            while (false !== $file = readdir($dh)) {
                if (!in_array($file, array('index.html', '.htaccess')) &&
                    is_file($cache_file = $dir . '/' . $file)
                ) {
                    if (!@unlink($cache_file)) {
                        $log .= sprintf('failed removing file %s...', $cache_file);
                    } else {
                        $log .= '...';
                    }
                }
            }
            closedir($dh);
        }
        $log .= 'done.';
        $this->addLog($log);

        $log = 'Removing cache files...';
        if (!$this->_app->getService('Cache')->clean($this->_app->getId())) {
            $this->addLog('failed...');
        } else {
            $log .= '...';
        }
        $log .= 'done.';
        $this->addLog($log);

        return true;
    }
}