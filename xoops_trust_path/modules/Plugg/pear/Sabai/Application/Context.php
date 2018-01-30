<?php
/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Application
 * @copyright  Copyright (c) 2008 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @link
 * @since      Class available since Release 0.1.9a3
 */
class Sabai_Application_Context
{
    /**
     * @var array
     */
    private $_context = array();

    /**
     * Constructor for PHP4 only
     *
     * @param array $context
     * @return Sabai_Application_Context
     */
    public function __construct(array $context = array())
    {
        $this->_context = $context;
    }


    /**
     * PHP magic __get() method
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_context[$name];
    }

    /**
     * PHP magic method
     *
     * @param string $name
     * @param bool
     */
    public function __isset($name)
    {
        return isset($this->_context[$name]);
    }

    /**
     * PHP magic method
     *
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->_context[$name]);
    }

    /**
     * PHP magic method
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_context[$name] = $value;
    }

    /**
     * Push a value to a stack
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function push($name, $value)
    {
        settype($this->_context[$name], 'array');
        array_push($this->_context[$name], $value);
        return $value;
    }

    /**
     * Pop a value from a stack
     *
     * @param string $name
     * @return mixed
     */
    public function pop($name)
    {
        return array_pop($this->_context[$name]);
    }
}