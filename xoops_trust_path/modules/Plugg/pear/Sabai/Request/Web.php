<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Request
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Sabai_Request
 */
require 'Sabai/Request.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Request
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
class Sabai_Request_Web extends Sabai_Request
{
    protected $_request;
    protected $_cookie;
    protected $_server;

    private static $_filtered = false;

    /**
     * Constructor
     *
     * @return Sabai_Request_Web
     * @param bool $filterGlobals
     */
    public function __construct($useGlobals = true, $filterGlobals = true)
    {
        if ($filterGlobals) {
            self::filterGlobals();
        }
        if ($useGlobals) {
            $this->_request =& $_REQUEST;
            $this->_cookie =& $_COOKIE;
        } else {
            $this->_request = $_REQUEST;
            $this->_cookie = $_COOKIE;
        }
        $scheme = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $this->_server = $scheme . $host;
        parent::__construct();
    }

    public static function filterGlobals()
    {
        if (!self::$_filtered) {
            if (get_magic_quotes_gpc()) {
                $_GET = self::stripSlashes($_GET);
                $_POST = self::stripSlashes($_POST);
                $_COOKIE = self::stripSlashes($_COOKIE);
            }
            // Filter malicious user inputs
            $list = array('GLOBALS', '_GET', '_POST', '_REQUEST', '_COOKIE', '_ENV', '_FILES', '_SERVER', '_SESSION');
            self::filterUserData($_GET, $list);
            self::filterUserData($_POST, $list);
            self::filterUserData($_COOKIE, $list);
            $_REQUEST = array_merge($_GET, $_POST);
            self::$_filtered = true;
        }
    }

    /**
     * @param mixed $var
     */
    public static function stripSlashes($var)
    {
        if (is_array($var)) {
            return array_map(array(__CLASS__, __FUNCTION__), $var);
        } else {
            return stripslashes($var);
        }
    }

    /**
     * @param mixed $var
     * @param array $globalKeys
     */
    public static function filterUserData(&$var, $globalKeys = array())
    {
        if (is_array($var)) {
            $var_keys = array_keys($var);
            if (array_intersect($globalKeys, $var_keys)) {
                $var = array();
            } else {
                foreach ($var_keys as $key) {
                    self::filterUserData($var[$key], $globalKeys);
                }
            }
        } else {
            $var = str_replace("\x00", '', $var);
        }
    }

    public function getAll()
    {
        return $this->_request;
    }

    protected function _has($name)
    {
        return array_key_exists($name, $this->_request);
    }

    protected function _get($name)
    {
        return $this->_request[$name];
    }

    protected function _set($name, $value)
    {
        $this->_request[$name] = $value;
    }

    public function hasCookie($name)
    {
        return isset($this->_cookie[$name]);
    }

    public function getCookie($name)
    {
        return $this->_cookie[$name];
    }

    public function isPost()
    {
        return strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0;
    }

    protected function _getUri()
    {
        return $this->_server . $_SERVER['REQUEST_URI'];
    }
}