<?php
require_once 'SabaiPlugin/Plugin.php';

class Plugg_Plugin extends SabaiPlugin_Plugin
{
    protected $_hasSchema, $_hasModel, $_hasCSS, $_hasLocale, $_nicename, $_application;

    public function Plugg_Plugin($name, $path, $version, array $params, $library, $extra, Plugg $application)
    {
        parent::__construct($name, $path, $version, $params, $library);
        $this->_nicename = @$extra['nicename'];
        $this->_hasSchema = $extra['hasSchema'];
        $this->_hasModel = $extra['hasModel'];
        $this->_hasMainCSS = $extra['hasMainCSS'];
        $this->_hasAdminCSS = $extra['hasAdminCSS'];
        $this->_hasLocale = $extra['hasLocale'];
        $this->_application = $application;
        $this->_application->getGettext()->loadMessages($this->_name, $this->_library . '.mo');
    }

    public function getNicename()
    {
        return $this->_nicename;
    }

    public function getApplication()
    {
        return $this->_application;
    }

    public function _($msgId)
    {
        if ($this->_application->getGettext()->dhastext($this->_name, $msgId)) {
            return $this->_application->getGettext()->dgettext($this->_name, $msgId);
        }

        // If message cannt be found in the domain, find it from the global domain
        return $this->_application->getGettext()->gettext($msgId);
    }

    public function _e($msgId)
    {
        if ($this->_application->getGettext()->dhastext($this->_name, $msgId)) {
            echo $this->_application->getGettext()->dgettext($this->_name, $msgId);
        } else {
            // If message cannt be found in the domain, find it from the global domain
            echo $this->_application->getGettext()->gettext($msgId);
        }
    }

    public function ngettext($msgId, $msgId2, $num)
    {
        if ($this->_application->getGettext()->dhastext($this->_name, $msgId)) {
            return $this->_application->getGettext()->dngettext($this->_name, $msgId, $msgId2, $num);
        }

        // If message cannt be found in the domain, find it from the global domain
        return $this->_application->getGettext()->ngettext($msgId, $msgId2, $num);
    }

    protected function _onPluggMainRoutes(&$routes, $noBreadcrumb = false)
    {
        $routes['/' . $this->_name] = array(
            'controller' => sprintf('Plugg_%s_Main', $this->_library),
            'controller_file' => $this->_path . '/Main.php',
            'context' => array('plugin' => $this),
            'title' => $this->getNicename(),
            'no_breadcrumb' => $noBreadcrumb,
        );
    }

    protected function _onPluggAdminRoutes(&$routes, $tabAjax = true)
    {
        $routes['/' . $this->_name] = array(
            'controller' => sprintf('Plugg_%s_Admin', $this->_library),
            'controller_file' => $this->_path . '/Admin.php',
            'context' => array('plugin' => $this),
            'title' => $this->getNicename(),
            'tab' => true,
            'tab_ajax' => $tabAjax
        );
    }

    protected function _onUserMainIdentityRoutes(&$routes, $private = false)
    {
        $routes[$this->_name] = array(
            'controller' => sprintf('Plugg_%s_User', $this->_library),
            'controller_file' => $this->_path . '/User.php',
            'context'=> array('plugin' => $this),
            'title' => $this->getNicename(),
            'tab' => true,
            'tab_ajax' => false,
            'access_callback' => $private ? 'isValidOwnerAccess' : null
        );
    }

    protected function _onGroupMainGroupRoutes(&$routes)
    {
        $routes[$this->_name] = array(
            'controller' => sprintf('Plugg_%s_Group', $this->_library),
            'controller_file' => $this->_path . '/Group.php',
            'context'=> array('plugin' => $this),
            'title' => $this->getNicename(),
            'tab' => true,
            'tab_ajax' => false,
        );
    }

    protected function _onUserAdminRolePermissions(&$permissions, $pluginPermissions)
    {
        $permissions[$this->_library] = $pluginPermissions;
    }

    private function _getSchemaList($schemaDir)
    {

        if (!$dh = opendir($schemaDir)) {
            return false;
        }

        $old = $new = array();
        while ($file = readdir($dh)) {
            if (preg_match('/^\d+(?:\.\d+)*(?:[a-zA-Z]+\d*)?\.xml$/', $file)) {
                $file_version = basename($file, '.xml');
                $found = false;
                if (version_compare($file_version, $this->_version, '<=')) {
                    if (!empty($old)) {
                        $old2 = array();
                        foreach ($old as $old_version => $old_file) {
                            if (!$found && version_compare($file_version, $old_version, '<')) {
                                $old2[$file_version] = $schemaDir . $file;
                                $found = true;
                            } else {
                                $old2[$old_version] = $old_file;
                            }
                        }
                        if (!$found) {
                            $old2[$file_version] = $schemaDir . $file;
                        }
                        $old = $old2;
                    } else {
                        $old = array($file_version => $schemaDir . $file);
                    }
                } else {
                    if (!empty($new)) {
                        $new2 = array();
                        foreach ($new as $new_version => $new_file) {
                            if (!$found && version_compare($file_version, $new_version, '<')) {
                                $new2[$file_version] = $schemaDir . $file;
                                $found = true;
                            } else {
                                $new2[$new_version] = $new_file;
                            }
                        }
                        if (!$found) {
                            $new2[$file_version] = $schemaDir . $file;
                        }
                        $new = $new2;
                    } else {
                        $new = array($file_version => $schemaDir . $file);
                    }
                }
            }
        }

        return array($old, $new);
    }

    public function install(&$message)
    {
        if ($this->_hasSchema) {
            // create database tables
            $schema = $this->getDBSchema();
            if (!$schema->create($this->_path . '/schema/latest.xml')) {
                $message = sprintf('Failed creating database tables using schema. Error: %s', implode(', ', $schema->getErrors()));
                return false;
            }
            $message = 'Database tables created.';
        }
        return true;
    }

    public function uninstall(&$message)
    {
        if ($this->_hasSchema) {
            $schema_dir = $this->_path . '/schema/';
            if (false === $schema_list = $this->_getSchemaList($schema_dir)) {
                $message = 'Failed opening schema directory.';
                return false;
            }

            list($schema_old, $schema_new) = $schema_list;
            if (!empty($schema_old)) {
                // get the last schema file
                $previous_schema = array_pop($schema_old);
            } else {
                $previous_schema = $schema_dir . 'latest.xml';
            }
            $schema = $this->getDBSchema();
            if (!$schema->drop($previous_schema)) {
                $message = sprintf(
                    'Failed deleting database tables using schema %s. Error: %s',
                    str_replace($schema_dir, '', $previous_schema),
                    implode(', ', $schema->getErrors())
                );
                return false;
            }
            $message = 'Database tables deleted.';
        }
        if (!$this->getCache()->clean()) {
            $message .= 'Failed removing cache files.';
        } else {
            $message .= 'Removed cache files.';
        }
        return true;
    }

    public function upgrade($previousVersion, &$message)
    {
        if ($this->_hasSchema) {
            $schema_dir = $this->_path . '/schema/';
            if (false === $schema_list = $this->_getSchemaList($schema_dir)) {
                $message = 'Failed opening schema directory.';
                return false;
            }

            list($schema_old, $schema_new) = $schema_list;

            if (!empty($schema_new)) {
                $schema = $this->getDBSchema();
                $messages = array();
                if (!empty($schema_old)) {
                    // get the last schema file
                    $previous_schema = array_pop($schema_old);
                } else {
                    // No old schema, so get one from the new schema list
                    $previous_schema = array_shift($schema_new);
                    if (!$schema->create($previous_schema)) {
                        $message = sprintf('Failed creating database tables using schema. Error: %s', implode(', ', $schema->getErrors()));
                        return false;
                    }
                    $messages[] = sprintf('Created database using schema %s.', str_replace($schema_dir, '', $previous_schema));
                }
                // update schema incrementally

                foreach ($schema_new as $new_schema) {
                    if (!$result = $schema->update($new_schema, $previous_schema)) {
                        $message = sprintf(
                            'Failed updating database schema from %s to %s. Error: %s',
                            str_replace($schema_dir, '', $previous_schema),
                            str_replace($schema_dir, '', $new_schema),
                            implode(', ', $schema->getErrors())
                        );
                        return false;
                    }
                    $messages[] = sprintf(
                        'Updated database schema from %s to %s.',
                        str_replace($schema_dir, '', $previous_schema),
                        str_replace($schema_dir, '', $new_schema)
                    );
                    $previous_schema = $new_schema;
                }
                $message = implode('<br />', $messages);
            }
        }
        return true;
    }

    public function getDB()
    {
        $default_table_prefix = $this->_application->getLocator()->getDefaultParam('DB', 'tablePrefix');
        return $this->_application->getService(
            'DB',
            $this->_name,
            array(
                'tablePrefix' => $default_table_prefix . $this->_name . '_'
            )
        );
    }

    public function getDBSchema()
    {
        require_once 'Sabai/DB/Schema.php';
        return Sabai_DB_Schema::factory($this->getDB());
    }

    public function getModel()
    {
        if (!$this->_hasModel) {
            trigger_error(sprintf('No model available for plugin %s', $this->_name), E_USER_WARNING);
            return;
        }
        return $this->_application->getService('PluginModel', $this->_name, array('plugin' => $this));
    }

    public function getCache($cacheLifetime = null)
    {
        // Create new cache object if cache lifetime set, otherwise reuse the last object
        if (!empty($cacheLifetime)) {
            return $this->_application->getLocator()->createService(
                'Cache',
                array(
                    'cacheName' => $this->_name,
                    'lifeTime' => $cacheLifetime,
                )
            );
        } else {
            return $this->_application->getService(
                'Cache',
                $this->_name,
                array(
                    'cacheName' => $this->_name,
                )
            );
        }
    }

    public function getTemplatePath()
    {
        return $this->_path . '/templates';
    }

    public function hasMainCSS()
    {
        return $this->_hasMainCSS;
    }

    public function hasAdminCSS()
    {
        return $this->_hasAdminCSS;
    }

    public function hasLocale()
    {
        return $this->_hasLocale;
    }

    public function loadParams()
    {
        if ($plugin = $this->_application->getPlugin('system')
                ->getModel()
                ->Plugin
                ->fetchByName($this->_name)
        ) {
            $this->_params = $plugin->getParams();
            return true;
        }
        return false;
    }

    public function updateParam($name, $value)
    {
        if (array_key_exists($name, $this->_params)) {
            if ($plugin = $this->_application->getPlugin('system')
                    ->getModel()
                    ->Plugin
                    ->fetchByName($this->_name)
            ) {
                $this->_params[$name] = $value;
                $plugin->setParams($this->_params);
                return $plugin->commit();
            }
        }
        return false;
    }
}