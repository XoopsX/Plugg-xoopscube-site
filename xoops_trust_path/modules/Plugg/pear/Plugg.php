<?php
require_once 'Sabai/Application.php';
require_once 'SabaiPlugin.php';
require_once 'SabaiMbstring.php';
require_once 'Plugg/URL.php';
require_once 'Plugg/Plugin.php';
require_once 'Plugg/Exception.php';

final class Plugg extends Sabai_Application
{
    private static $_instance;
    protected $_config, $_locator, $_pluginManager, $_gettext, $_type;

    // Request parameter constants
    const AJAX = '__ajax';
    const STACK_LEVEL = '__stacklevel';
    const REGION = '__region';
    const TOKEN = '__t';
    const ROUTE = 'q';

    // Plugg type constants
    const XOOPS = 1;
    const XOOPSCUBE_LEGACY = 2;
    const IMPRESSCMS = 4;
    const WORDPRESS = 8;
    const MODULE = 15;
    const STANDALONE = 16;

    private function __construct($id, $baseUrl, $baseScript, $type)
    {
        parent::__construct(
            $id,
            'Plugg',
            dirname(__FILE__) . '/Plugg',
            new Plugg_URL($baseUrl, $baseScript, self::ROUTE)
        );

        // Register autoload functions
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
        spl_autoload_register(array(__CLASS__, 'autoload'));

        $this->_type = $type;
    }

    private function _init($config)
    {
        // Build config object
        require_once 'Sabai/Config/Array.php';
        $this->_config = new Sabai_Config_Array($config);

        // Build service locator
        require_once 'Sabai/Service/Locator.php';
        $this->_locator = new Sabai_Service_Locator();
        $this->_locator->addProviderFactoryMethod(
            'DBConnection',
            array('Sabai_DB_Connection', 'factory'),
            $config['DB']['connection'],
            'Sabai/DB/Connection.php'
        );
        $this->_locator->addProviderFactoryMethod(
            'DB',
            array('Sabai_DB', 'factory'),
            array(
                'DBConnection' => new stdClass,
                'tablePrefix' => $config['DB']['tablePrefix']
            ),
            'Sabai/DB.php'
        );
        $this->_locator->addProviderClass(
            'Model',
            array(
                'DB' => new stdClass,
                'modelDir' => dirname(__FILE__) . '/Model',
                'modelPrefix' => 'Plugg_Model_',
                'UserIdentityFetcher' => new stdClass
            ),
            'Sabai_Model',
            'Sabai/Model.php'
        );
        $this->_locator->addProviderClass(
            'PluginModel',
            array(
                'plugin' => null,
                'UserIdentityFetcher' => new stdClass
            ),
            'Plugg_PluginModel',
            'Plugg/PluginModel.php'
        );
        $this->_locator->addProviderClass(
            'UserIdentityFetcher',
            array(),
            'Sabai_User_IdentityFetcher_Default',
            'Sabai/User/IdentityFetcher/Default.php'
        );
        $this->_locator->addProviderClass(
            'Cache',
            array(
                'cacheName' => $this->getId(),
                'cacheDir' => $config['cacheDir'] . '/',
                'automaticSerialization' => false,
                'lifeTime' => null
            ),
            'Plugg_Cache',
            'Plugg/Cache.php'
        );

        // Load global message catalogue
        require_once 'Plugg/Gettext.php';
        $this->_gettext = new Plugg_Gettext($this);
        $this->_gettext->loadMessages($this->getId(), 'Plugg.mo');

        // Load plugins and envoke PluggInit event
        require_once 'Plugg/PluginManager.php';
        $this->_pluginManager = new Plugg_PluginManager($this);
        $this->_pluginManager->loadPlugins(false);
        $this->_pluginManager->dispatch('PluggInit');

        // Set mod_rewrite options if enabled
        if (@$this->_config->get('modRewrite') &&
            ($format = $this->_config->get('modRewriteFormat'))
        ) {
            $this->_url->useModRewrite(true)
                ->setModRewrite($format, $this->_url->getBaseUrl() . '/' . $this->_url->getScriptAlias('main'));
        }
    }

    public static function getInstance($id = null, $url = null, $script = null, array $config = array(), $type = self::STANDALONE)
    {
        if (!isset(self::$_instance)) {
            // Initialize if there is no instance yet
            self::$_instance = new self($id, $url, $script, $type);
            self::$_instance->_init($config);
        }
        return self::$_instance;
    }

    public function debug()
    {
        Sabai_Log::level(Sabai_Log::ALL);
        require_once 'Sabai/Log/Writer/HTML.php';
        Sabai_Log::writer(new Sabai_Log_Writer_HTML());
    }

    public static function autoload($class)
    {
        if (strpos($class, 'Plugg_') === 0 && // Class starts with Plugg_
            ($partial = substr($class, 6)) &&
            strpos($partial, '_') // Depth is more than 1 level
        ) {
            // Plugin file
            // ToDo: Any way to pass in the $id parameter to getInstance()?
            require self::getInstance()->getPluginManager()->getPluginDir() . '/' . str_replace('_', '/', $partial) . '.php';
        }
    }

    public function run(Sabai_Application_Controller $controller, Sabai_Request $request, Sabai_Response $response, Sabai_User $user = null)
    {
        if (is_null($user)) $user = $this->getCurrentUser();


        try {
            // Invoke PluggRun event
            $this->_pluginManager->dispatch('PluggRun', array($controller));

            parent::run($controller, $request, $response, $user);
        } catch (Plugg_Exception $e) {
            // Render the error page

            // Make sure required template objects are set properly
            $response->getTemplate()->setPluggObjects($this)
                ->setObject('User', $user)
                ->setObject('Request', $request);

            $this->ERROR = $e;
            $response->setLayoutFile('error.html')->send($this, false);
        }
    }

    public function cron($key, &$logs = null)
    {
        // Check if the right key is passed
        if ($key != $this->_config->get('cronKey')) {
            exit;
        }

        // Cache the last run timestamp
        $cache = $this->_locator->getService('Cache');
        if (!$cron_lastrun = $cache->get('cron_lastrun', $this->getId())) {
            $cron_lastrun = 0;
        }
        $cache->save(time(), 'cron_lastrun', $this->getId());

        // Invoke plguins
        $this->_pluginManager->dispatch('PluggCron', array($cron_lastrun, &$logs));
    }

    public function isType($type)
    {
        return ($this->_type & $type) == $type;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getLocator()
    {
        return $this->_locator;
    }

    public function getGettext()
    {
        return $this->_gettext;
    }

    public function getPluginManager()
    {
        return $this->_pluginManager;
    }

    public function getConfig($name = null)
    {
        return isset($name) ? $this->_config->get($name) : $this->_config;
    }

    /**
     * A shortcut method for fetching a service provider object
     * @param string $serviceName
     * @param string $id
     * @param array $params
     * @return Sabai_Service_Provider
     */
    public function getService($serviceName, $id = 'default', array $params = array())
    {
        return $this->_locator->getService($serviceName, $id, $params);
    }

    /**
     * A shortcut method for fetching a plugin object
     * @param string $pluginName
     * @return Plugg_Plugin
     */
    public function getPlugin($pluginName)
    {
        return $this->_pluginManager->getPlugin($pluginName);
    }

    /**
     * A shortcut method for dispatching a plugin event
     * @param string $eventName
     * @param array $eventParams
     * @param string $pluginName
     * @param bool $force
     */
    public function dispatchEvent($eventName, $eventParams = array(), $pluginName = null, $force = false)
    {
        $this->_pluginManager->dispatch($eventName, $eventParams, $pluginName, $force);
    }

    public function getCurrentUser()
    {
        if ($user = $this->hasCurrentUser()) return $user;

        return Sabai_User::createAnonymousUser($this->_gettext->_('Guest'), $this->getId());
    }

    public function hasCurrentUser()
    {
        require_once 'Sabai/User.php';
        // User data in session?
        if ($user = Sabai_User::hasCurrentUser($this->getId())) return $user;

        // User plugin available?
        if ($user_plugin = $this->_pluginManager->getPlugin('user')) {
            return $user_plugin->hasCurrentUser();
        }

        return false;
    }
}
