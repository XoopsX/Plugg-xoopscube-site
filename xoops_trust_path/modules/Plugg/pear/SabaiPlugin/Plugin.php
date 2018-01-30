<?php
require_once 'Sabai/Event/Dispatchable.php';

abstract class SabaiPlugin_Plugin implements Sabai_Event_Dispatchable
{
    /**
     * @var string
     * @access protected
     */
    protected $_name;
    /**
     * @var string
     * @access protected
     */
    protected $_path;
    /**
     * @var string
     * @access protected
     */
    protected $_version;
    /**
     * @var array
     * @access protected
     */
    protected $_params;
    /**
     * @var string
     * @access protected
     */
    protected $_library;

    protected function __construct($name, $path, $version, array $params, $library)
    {
        $this->_name = $name;
        $this->_path = $path;
        $this->_version = $version;
        $this->_params = $params;
        $this->_library = $library;
    }
    
    public function dispatchEvent(Sabai_Event $event)
    {
        $method = 'on' . $event->getType();
        return call_user_func_array(array($this, $method), $event->getVars());
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getParam($key)
    {
        return $this->_params[$key];
    }
    
    public function getVersion()
    {
        return $this->_version;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getLibrary()
    {
        return $this->_library;
    }

    public function isClone()
    {
        return $this->_name != strtolower($this->_library);
    }
}