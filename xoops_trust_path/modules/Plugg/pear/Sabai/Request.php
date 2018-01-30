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
abstract class Sabai_Request
{
    /**
     * @var string
     * @access protected
     */
    protected $_uri;
    /**
     * @var string
     * @access protected
     */
    protected $_previousUri;

    /**
     * Constructor
     *
     * @return Sabai_Request
     */
    protected function __construct()
    {
        $this->_previousUri = @$_SESSION['Sabai_Request_uri'];
        $_SESSION['Sabai_Request_uri'] = $this->_uri = $this->_getUri();
    }

    /**
     * Gets the previously requested URI
     *
     * @return string
     */
    public function getPreviousUri()
    {
        return $this->_previousUri;
    }

    /**
     * Sets the previously requested URI manually
     *
     * @return string
     */
    public function setPreviousUri($previousUri)
    {
        $this->_previousUri = $previousUri;
    }

    /**
     * Gets the requested URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * Gets a request variable
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->_get($name);
    }

    /**
     * Gets a request variable as a certain PHP type variable
     *
     * @access protected
     * @param string $type
     * @param string $name
     * @param mixed $default
     * @param array $include
     * @param array $exclude
     * @return mixed
     */
    protected function _getAs($type, $name, $default, $include = array(), $exclude = array())
    {
        $ret = $default;
        if ($this->_has($name)) {
            $ret = $this->_get($name);
            settype($ret, $type);
            if (!empty($exclude)) {
                if (in_array($ret, $exclude)) {
                    $ret = $default;
                }
            } elseif (!empty($include)) {
                if (!in_array($ret, $include)) {
                    $ret = $default;
                }
            }
        }
        return $ret;
    }

    /**
     * Gets a certain request variable as array
     *
     * @param string $name
     * @param array $default
     * @param array $include
     * @param array $exclude
     * @return array
     */
    public function getAsArray($name, $default = array(), $include = array(), $exclude = array())
    {
        return $this->_getAs('array', $name, $default, $include, $exclude);
    }

    /**
     * Gets a certain request variable as string
     *
     * @param string $name
     * @param string $default
     * @param mixed $include
     * @param mixed $exclude
     * @return string
     */
    public function getAsStr($name, $default = '', $include = null, $exclude = null)
    {
        return $this->_getAs('string', $name, $default, (array)$include, (array)$exclude);
    }

    /**
     * Gets a certain request variable as integer
     *
     * @param string $name
     * @param int $default
     * @param mixed $include
     * @param mixed $exclude
     * @return int
     */
    public function getAsInt($name, $default = 0, $include = null, $exclude = null)
    {
        return $this->_getAs('integer', $name, $default, (array)$include, (array)$exclude);
    }

    /**
     * Gets a certain request variable as bool
     *
     * @param string $name
     * @param bool $default
     * @return bool
     */
    public function getAsBool($name, $default = false)
    {
        return $this->_getAs('boolean', $name, $default);
    }

    /**
     * Gets a certain request variable as float
     *
     * @param string $name
     * @param float $default
     * @param mixed $include
     * @param mixed $exclude
     * @return float
     */
    public function getAsFloat($name, $default = 0.0, $include = null, $exclude = null)
    {
        return $this->_getAs('float', $name, $default, (array)$include, (array)$exclude);
    }

    /**
     * Sets the value of a request parameter
     *
     * @final
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value){
        $this->_set($name, $value);
    }

    /**
     * Checks if a cookie is set
     *
     * @param string $name
     * @return bool
     */
    public function hasCookie($name)
    {
        return false;
    }

    /**
     * Gets a cookie variable
     *
     * @param string $name
     * @return mixed
     */
    public function getCookie($name)
    {
        return null;
    }

    /**
     * Checks the request method used
     *
     * @return bool
     */
    public function isPost()
    {
        return false;
    }

    /**
     * Gets all the request parameters
     *
     * @return array
     */
    abstract public function getAll();
    /**
     * Checks if a request parameter is present
     *
     * @return bool
     */
    abstract protected function _has($name);
    /**
     * Gets the value of a request parameter
     *
     * @return mixed
     * @param string $name
     */
    abstract protected function _get($name);
    /**
     * Sets the value of a request parameter
     *
     * @param string $name
     * @param mixed $value
     */
    abstract protected function _set($name, $value);
    /**
     * Gets the requested URI
     *
     * @return string
     */
    abstract protected function _getUri();
}