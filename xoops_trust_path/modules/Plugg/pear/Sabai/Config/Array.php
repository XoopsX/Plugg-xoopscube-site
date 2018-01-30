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
class Sabai_Config_Array extends Sabai_Config
{
    /**
     * @var array
     */
    protected $_configs;

    /**
     * Cosntructor
     *
     * @param array $configs
     * @return Sabai_Config_Array
     */
    public function __construct(array $configs = array())
    {
        $this->_configs = $configs;
    }

    /**
     * Checks if a config variable is present
     *
     * @param string $name
     * @return bool
     */
    protected function _hasConfig($name)
    {
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
     * Gets all config variables
     *
     * @return array
     */
    protected function _getAll()
    {
        return $this->_configs;
    }
}