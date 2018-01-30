<?php
require_once 'Sabai.php';
require_once 'Sabai/Application/URL.php';
require_once 'Sabai/Application/Context.php';

abstract class Sabai_Application
{
    private $_id;
    private $_name;
    private $_path;
    protected $_url;
    private $_data = array();

    private static $_initialized = false;

    protected function Sabai_Application($id, $name, $path, Sabai_Application_URL $url)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_path = $path;
        $this->_url = $url;
        $this->_initEnv();
    }

    private function _initEnv()
    {
        if (!self::$_initialized) {
            if (empty($_SERVER['REQUEST_URI'])) {
                $parsed = parse_url($this->_url->getScriptUrl());
                $_SERVER['REQUEST_URI'] = $parsed['path'];
                if (!empty($_SERVER['QUERY_STRING'])) $_SERVER['REQUEST_URI'] .= '?' . $_SERVER[ 'QUERY_STRING' ];
            }
            self::$_initialized = true;
        }
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData($data, $value = null)
    {
        if (is_array($data)) {
            $this->_data = array_merge($this->_data, $data);
        } else {
            $this->_data[$data] = $value;
        }
    }

    public function createUrl($options = array())
    {
        return $this->_url->create($options);
    }

    public function run(Sabai_Application_Controller $controller, Sabai_Request $request, Sabai_Response $response, Sabai_User $user)
    {
        $response->pushContentName(strtolower(get_class($controller)));
        $controller->setApplication($this);
        $context = new Sabai_Application_Context(array(
            'request' => $request,
            'response' => $response,
            'user' => $user,
            'route' => $request->getAsStr($this->_url->getRouteParam()),
            'routesMatched' => array()
        ));
        $controller->execute($context);
        $response->send($this);
    }

    public function __get($name)
    {
        return $this->_data[$name];
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }
}
