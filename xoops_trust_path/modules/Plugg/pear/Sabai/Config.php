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
abstract class Sabai_Config
{
    /**
     * Gets a config value
     *
     * @param string $name
     * @return mixed
     */
    public function get($section = null)
    {
        if (!isset($section)) {
            return $this->_getAll();
        }
        if (!$this->_hasConfig($section)) {
            trigger_error(sprintf('Request to non-existent config key "%s"', $section), E_USER_WARNING);
            return;
        }
        $config = $this->_getConfig($section);
        if (func_num_args() > 1) {
            $names = array_slice(func_get_args(), 1);
            foreach ($names as $name) {
                if (is_array($config) && array_key_exists($name, $config)) {
                    $config = $config[$name];
                } else {
                    trigger_error(sprintf('Request to non-existent config key "%s"', $name), E_USER_WARNING);
                    break;
                }
            }
        }
        return $config;
    }

    /**
     * Magic method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Checks if config variable is available
     *
     * @param string $name
     * @return bool
     */
    abstract protected function _hasConfig($name);

    /**
     * Gets a config variable
     *
     * @param string $name
     * @return mixed
     */
    abstract protected function _getConfig($name);

    /**
     * Gets all config variables
     *
     * @return array
     */
    abstract protected function _getAll();
}