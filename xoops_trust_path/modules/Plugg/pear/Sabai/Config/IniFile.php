<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Config
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Sabai_Config
 */
require_once 'Sabai/Config.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Config
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
class Sabai_Config_IniFile extends Sabai_Config
{
    /**
     * @var string
     */
    protected $_file;
    /**
     * @var array
     */
    protected $_configs;

    /**
     * Constructor
     *
     * @param string $file
     * @return Sabai_Config_IniFile
     */
    public function __construct($file = './config.ini')
    {
        $this->_file = $file;
    }

    /**
     * Checks if a config variable exists
     *
     * @param string $name
     * @return bool
     */
    protected function _hasConfig($name)
    {
        if (!$this->_loadConfig()) {
            trigger_error(sprintf('Failed loading config values from config file "%s"', $this->_file), E_USER_WARNING);
            return false;
        }
        return array_key_exists($name, $this->_configs);
    }

    /**
     * Gets the value of a config variable
     *
     * @param string $name
     * @return mixed
     */
    protected function _getConfig($name)
    {
        return $this->_configs[$name];
    }

    /**
     * Loads all the config variables from a file
     *
     * @return bool
     */
    protected function _loadConfig()
    {
        if (isset($this->_configs)) {
            return true;
        }
        if (!file_exists($this->_file)) {
            return false;
        }
        $this->_configs = parse_ini_file($this->_file);
        return true;
    }

    /**
     * Gets all config variables
     *
     * @return array
     */
    protected function _getAll()
    {
        if (!$this->_loadConfig()) {
            return array();
        }
        return $this->_configs;
    }
}