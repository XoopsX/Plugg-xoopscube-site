<?php
require_once 'Sabai/Application/Controller.php';

class Plugg_Install extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if ($context->request->isPost()) {
            $logs = array();
            if ($this->_install($context, $logs)) {
                $this->_application->success = true;
            }
            $this->_application->logs = $logs;
        }
    }

    private function _install($context, &$logs = array())
    {
        // Init cache directories
        $log = 'Initializing cache and media directories...';
        foreach (array(
            $this->_application->getConfig('cacheDir'),
            $this->_application->getConfig('mediaDir')
        ) as $dir) {
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
        $logs[] = $log;

        // Install required plugins
        $log = 'Installing required plugins...';

        // Install the System plugin
        if (!$system = $this->_application->getPluginManager()->fetchPlugin('System')) {
            $log .= 'failed fetching the System plugin.';
            $logs[] = $log;
            return false;
        }
        $message = '';
        if (!$system->install($message)) {
            $log .= sprintf('failed installing the System plugin. Error: %s', $message);
            $logs[] = $log;
            return false;
        }
        $log .= 'System installed...';

        // Install other required plugins
        $plugins_required = array(
            'HTMLPurifier' => array(),
            'Filter' => array(),
            'Mail' => array('mailSenderPlugin' => 'swiftmailer'),
            'Search' => array('searchEnginePlugin' => 'simplesearch'),
            'Widget' => array(),
            'User' => array('userManagerPlugin' => 'account'),
            'Account' => array(),
            'jQuery' => array(),
            'Profile' => array(),
            'SimpleSearch' => array(),
            'SwiftMailer' => array(),
        );
        $plugins_installed = array('system');
        $install_failed = false;
        foreach ($plugins_required as $plugin_lib => $plugin_params) {
            $error = '';
            if (!$plugin_data = $system->isPluginInstallable($plugin_lib, $error)) {
                $install_failed = true;
                $log .= sprintf('failed installing required plugin %s. Error: %s', $plugin_lib, $error);
                break;
            }

            $result = $system->installPlugin($plugin_lib, $plugin_data, $plugin_data['nicename'], $plugin_params);
            if (!is_object($result)) {
                $install_failed = true;
                $log .= sprintf('failed installing required plugin %s. Error: %s', $plugin_lib, $result);
                break;
            }

            $log .= sprintf('%s installed...', $plugin_lib);
            $plugins_installed[] = strtolower($plugin_lib);
            $this->_application->dispatchEvent('SystemAdminPluginInstalled', array($result));
            $this->_application->dispatchEvent($plugin_lib . 'PluginInstalled', array($result));
        }

        if (!$install_failed) {
            $logs[] = $log;
            $log = 'Creating system roles...';

            // Create default administrator role
            $role = $this->_application->getPlugin('user')
                ->getModel()
                ->create('Role')
                ->markNew()
                ->set('system', 1)
                ->set('name', $this->_application->getGettext()->_('Administrator'));
            if ($role->commit()) {
                $log .= 'Administrator role created...';

                // Create a new user account
                $user = $this->_application->getPlugin('account')
                    ->getModel()
                    ->create('Account')
                    ->markNew()
                    ->set('login', 'admin')
                    ->set('password', md5('admin'))
                    ->set('email', '')
                    ->set('name', $this->_application->getGettext()->_('Administrator'));
                if ($user->commit()) {

                    // Add user to the default administrator role
                    $member = $role->createMember()
                        ->markNew()
                        ->setVar('userid', $user->getId());
                    if ($member->commit()) {
                        $log .= 'a default admin user created...';
                    } else {
                        $log .= 'failed creating a default admin user...';
                        $install_failed = true;
                    }
                } else {
                    $log .= 'failed creating a default admin user...';
                    $install_failed = true;
                }
            } else {
                $log .= 'failed creating Administrator role...';
                $install_failed = true;
            }
        }

        // Uninstall all plugins if requierd plugins were not installed
        if ($install_failed) {
            $logs[] = $log;
            if (!empty($plugins_installed)) {
                $log = 'Uninstalling installed plugins...';
                foreach ($plugins_installed as $plugin_name) {
                    $message = '';
                    if ((!$plugin = $this->_application->getPlugin($plugin_name, false)) || !$plugin->uninstall($message)) {
                        $log .= sprintf('failed uninstalling the %s plugin! You must manually uninstall the plugin. Error: %s..', $plugin_name, $message);
                        continue;
                    }
                    $log .= sprintf('%s uninstalled...', $plugin_name);
                }
            }
        }
        $log .= 'done.';
        $logs[] = $log;

        return !$install_failed;
    }
}