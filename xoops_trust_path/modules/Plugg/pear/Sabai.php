<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai
 * @copyright  Copyright (c) 2008 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @link
 * @since      File available since Release 0.1.1
*/

require_once 'Sabai/Log.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai
 * @copyright  Copyright (c) 2008 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @link
 * @version    0.1.9a2
 * @since      Class available since Release 0.1.1
 */
final class Sabai
{
    private static $_started = false;

    /**
     * Initializes session and other required libraries
     *
     * @param int $logLevel
     * @param string $charset
     * @static
     */
    public static function start($logLevel = Sabai_Log::ERROR, $charset = 'UTF-8', $lang = 'en', $startSession = true)
    {
        if (!self::$_started) {
            // Some startup initializations
            define('SABAI_CHARSET', $charset);
            define('SABAI_LANG', $lang);
            //set_magic_quotes_runtime(0);

            // Start session if required
            if ($startSession && !session_id() && PHP_SAPI != 'cli') {
                @ini_set('session.use_only_cookies', 1);
                @ini_set('session.use_trans_sid', 0);
                @ini_set( 'session.hash_function', 1);
                @session_start();
            }

            // Set the global log level
            Sabai_Log::level($logLevel);

            // Initialize the default error handler
            require_once 'Sabai/ErrorHandler.php';
            Sabai_ErrorHandler::initDefault();

            self::$_started = true;
        }
    }

    /**
     * Enables the HTML log writer for easier debugging
     *
     * @param int $level
     */
    public static function debug($level = Sabai_Log::ALL)
    {
        Sabai_Log::level($level);
        require_once 'Sabai/Log/Writer/HTML.php';
        Sabai_Log::writer(new Sabai_Log_Writer_HTML());
    }
}

/**
 * Basic(and not complete) pluralization of a string
 *
 * @param string $str
 * @return string
 */
function pluralize($str)
{
    switch (strtolower(substr($str, -1))) {
    case 'y':
        return substr($str, 0, -1) . 'ies';
    case 's':
        return $str . 'es';
    default:
        return $str . 's';
    }
}

/**
 * Alias for htmlspecialchars()
 *
 * @param string $str
 * @param int $quoteStyle
 * @return string
 */
function h($str, $quoteStyle = ENT_QUOTES)
{
    return htmlspecialchars($str, $quoteStyle, SABAI_CHARSET);
}

/**
 * Echos out the result of h()
 *
 * @param string $str
 * @param int $quoteStyle
 * @return string
 */
function _h($str, $quoteStyle = ENT_QUOTES)
{
    echo htmlspecialchars($str, $quoteStyle, SABAI_CHARSET);
}

/**
 * HTML friendly var_dump()
 *
 * @param mixed $var
 */
function var_dump_html($var)
{
    echo '<pre>';
    _h(var_dump($var));
    echo '</pre>';
}

/**
 * Checks whether a file can be included with include()/require()
 *
 * @param string $filename
 * @return bool
 */
function is_includable($filename)
{
    $ret = false;
    if (false !== $fp = fopen($filename, 'r', true)) {
        $ret = true;
        fclose($fp);
    } else {
        if (!in_array('.', explode(PATH_SEPARATOR, get_include_path()))) {
            $ret = file_exists($filename);
        }
    }
    return $ret;
}

function getip($default = '')
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $key) {
        if (!empty($_SERVER[$key]) && ($_SERVER[$key] != 'unknown')) {
            return $_SERVER[$key];
        }
    }
    return $default;
}
