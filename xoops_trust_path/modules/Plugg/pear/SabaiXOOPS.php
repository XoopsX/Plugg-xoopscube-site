<?php
/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   SabaiXOOPS
 * @package    SabaiXOOPS
 * @copyright  Copyright (c) 2008 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/gpl-license.php GNU GPL
 * @link       http://sourceforge.net/projects/sabai
 * @version    0.1.9a2
 * @since      Class available since Release 0.1.0
 */
final class SabaiXOOPS
{
    private static $_configs;
    private static $_currentUsers;
    private static $_requestedPlugins;

    /**
     * Runs the module version of a Sabai application
     *
     * @static
     * @param Sabai_Application $application
     * @param Sabai_Application_Controller $controller
     * @param string $layoutFile
     */
    public static function run(Sabai_Application $application, Sabai_Application_Controller $controller, Sabai_Request $request, Sabai_Response $response)
    {
        $module_dir = $application->getId();

        // Customize response for XOOPS
        $response->setLayout(
            $layout_url = XOOPS_URL . '/modules/' . $module_dir . '/layouts/default',
            XOOPS_ROOT_PATH . '/modules/' . $module_dir . '/layouts/default'
        )
            ->addCSSFile($layout_url . '/css/screen.css')
            ->addCSSFile($layout_url . '/css/print.css', 'print')
            ->addCSSFile($layout_url . '/css/handheld.css', 'handheld');

        // css file of the module provided by a theme will override other css files
        if (file_exists($css_file = XOOPS_ROOT_PATH . '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/modules/' . $module_dir . '/css/screen.css')) {
            $response->addCSSFile(str_replace(XOOPS_ROOT_PATH, XOOPS_URL, $css_file), 'screen', null, 99);
        }

        // Initialize template directories
        self::initTemplate($application, $response->getTemplate());

        $application->run($controller, $request, $response, self::getCurrentUser($module_dir));
    }

    /**
     * Gets module specific configuration object
     *
     * @static
     * @staticvar array $configs
     * @param string $moduleName
     * @param string $moduleDir
     * @param array $default
     * @return Sabai_Config_Array
     */
    public static function getConfig($moduleName, $moduleDir, $default = array())
    {
        if (!isset(self::$_configs[$moduleDir])) {
            self::$_configs[$moduleDir] = array_merge(
                $default,
                self::getModuleConfig($moduleDir),
                array(
                    'localeDir' => XOOPS_TRUST_PATH . '/modules/' . $moduleName . '/locales',
                    'DB' => array(
                        'connection' => array(
                            'scheme' => XOOPS_DB_TYPE,
                            'options' => array(
                                'host' => XOOPS_DB_HOST,
                                'dbname' => XOOPS_DB_NAME,
                                'user' => XOOPS_DB_USER,
                                'pass' => XOOPS_DB_PASS,
                                'clientEncoding' => (strpos(XOOPS_VERSION, '2.0.', 1) || (defined('LEGACY_JAPANESE_ANTI_CHARSETMYSQL') && LEGACY_JAPANESE_ANTI_CHARSETMYSQL)) ? null : _CHARSET,
                            )
                        ),
                        'tablePrefix' => XOOPS_DB_PREFIX . '_' . strtolower($moduleDir) . '_' ,
                    ),
                )
            );
        }
        return self::$_configs[$moduleDir];
    }

    /**
     * Gets module specific configuration variables
     *
     * @static
     * @param string $moduleDir
     * @return array
     */
    public static function getModuleConfig($moduleDir)
    {
        if (self::isInModule($moduleDir) && isset($GLOBALS['xoopsModuleConfig'])) {
            return $GLOBALS['xoopsModuleConfig'];
        }
        // if not, load the module configuration variables
        if (!$module = xoops_gethandler('module')->getByDirname($moduleDir)) {
            trigger_error(sprintf('Requested module %s does not exist', $moduleDir), E_USER_NOTICE);
            return array();
        }
        return xoops_gethandler('config')->getConfigsByCat(0, $module->getVar('mid'));
    }

    /**
     * Checks if the current page is within the specified module
     *
     * @param string $moduleDir
     * @return bool
     */
    public static function isInModule($moduleDir)
    {
        return isset($GLOBALS['xoopsModule']) && ($GLOBALS['xoopsModule']->getVar('dirname') == $moduleDir);
    }

    public static function getModule($moduleDir)
    {
        return xoops_gethandler('module')->getByDirname($moduleDir);
    }

    /**
     * Gets a template object for use within the module context
     *
     * @static
     * @param Sabai_Application
     * @param Sabai_Template $template
     */
    public static function initTemplate(Sabai_Application $application, Sabai_Template $template)
    {
        $template->addTemplateDir(XOOPS_TRUST_PATH . '/modules/' . $application->getName() . '/templates', 99);

        // Any module installation specific template files?
        if (is_dir($custom_tpldir = XOOPS_ROOT_PATH . '/modules/' . $application->getId() . '/templates')) {
            $template->addTemplateDir($custom_tpldir, 99);
        }
    }

    /**
     * Gets the current user object for the specified module
     *
     * @static
     * @staticvar array $users
     * @param string $moduleDir
     * @return Sabai_User
     */
    public static function getCurrentUser($moduleDir)
    {
        if (!isset(self::$_currentUsers[$moduleDir])) {
            require_once 'Sabai/User.php';
            if (isset($GLOBALS['xoopsUser']) && is_object($GLOBALS['xoopsUser'])) {
            	if (!$user = Sabai_User::hasCurrentUser($moduleDir)) {
                    $user = new Sabai_User(self::getUserIdentity($GLOBALS['xoopsUser']), true, $moduleDir);
                    $user->startSession(false);
            	}
            	self::$_currentUsers[$moduleDir] = $user;

                //$xoops_groups = $GLOBALS['xoopsUser']->getGroups();
                // Set as super user if belongs to the default admin group
                //if (in_array(XOOPS_GROUP_ADMIN, $xoops_groups)) {
                //    self::$_currentUsers[$moduleDir]->setSuperUser(true);
                //} else {
                    // Set as super user if module admin
               //     if ($module = self::getModule($moduleDir)) {
                //        if (xoops_gethandler('groupperm')->checkRight('module_admin', $module->getVar('mid'), $xoops_groups)) {
                //            self::$_currentUsers[$moduleDir]->setSuperUser(true);
                //        }
                //    }
                //}
            } else {
                self::$_currentUsers[$moduleDir] = new Sabai_User(self::getGuestIdentity(), false, $moduleDir);
            }
        }

        return self::$_currentUsers[$moduleDir];
    }

    /**
     * Gets a registered user identity
     *
     * @param XoopsUser $xoopsUser
     * @return Sabai_User_IDentity
     * @static
     */
    public static function getUserIdentity($xoopsUser)
    {
        $uid = $xoopsUser->getVar('uid');
        $identity = new Sabai_User_Identity($uid, $xoopsUser->getVar('uname'));
        $identity->setName($xoopsUser->getVar('name'));
        $identity->setEmail($xoopsUser->getVar('email'));
        $identity->setUrl($xoopsUser->getVar('url'));
        $identity->setTimeCreated($xoopsUser->getVar('user_regdate'));
        $identity->setImage(XOOPS_URL . '/uploads/' . $xoopsUser->getVar('user_avatar'));
        return $identity;
    }

    /**
     * Gets a guest user identity
     *
     * @return Sabai_User_Identity
     * @static
     */
    public static function getGuestIdentity()
    {
        require_once 'Sabai/User/AnonymousIdentity.php';
        return new Sabai_User_AnonymousIdentity($GLOBALS['xoopsConfig']['anonymous']);
    }

    public static function getRequestedPlugin($moduleDir, $routeParam)
    {
        if (!isset(self::$_requestedPlugins[$moduleDir])) {
            if (!isset($_REQUEST[$routeParam]) ||
                (!$route = trim($_REQUEST[$routeParam], '/')) ||
                !self::isInModule($moduleDir)
            ) {
                self::$_requestedPlugins[$moduleDir] = '';
            } else {
                $route_arr = explode('/', $route);
                self::$_requestedPlugins[$moduleDir] = $route_arr[0];
            }
        }

        return self::$_requestedPlugins[$moduleDir];
    }
}
