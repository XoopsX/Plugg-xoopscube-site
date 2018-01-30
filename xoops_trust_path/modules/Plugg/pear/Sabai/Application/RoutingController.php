<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Application
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.8
*/

/**
 * Sabai_Application_Controller
 */
require_once 'Sabai/Application/Controller.php';

/**
 * Front Controller
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Application
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.8
 */
abstract class Sabai_Application_RoutingController extends Sabai_Application_Controller
{
    /**
     * Class prefix for controllers
     *
     * @var string
     */
    protected $_controllerPrefix;
    /**
     * Path to directory where controller class files are located
     *
     * @var string
     */
    protected $_controllerDir;
    /**
     * Front router
     *
     * @var Sabai_Application_RoutingControllerRouter
     */
    private $_router;
    /**
     * @var array
     */
    protected $_controllers = array();

    /**
     * Constructor
     *
     * @param string $controllerPrefix
     * @param string $controllerDir
     * @return Sabai_Application_RoutingController
     */
    protected function __construct($controllerPrefix, $controllerDir)
    {
        $this->_controllerPrefix = $controllerPrefix;
        $this->_controllerDir = $controllerDir;
    }

    /**
     * Runs the controller
     *
     * @param Sabai_Application_Context $context
     */
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $router = $this->_getRouter($context);
        if ($router->isRoutable($context->route)) {
            $this->_executeRoutableController($context, $router);
        } else {
            $this->_executeDefaultController($context, $router);
        }
    }

    /**
     * Forwards request to another route
     *
     * @param string $route
     * @param Sabai_Application_Context $context
     * @param bool $stackContentName
     */
    public function forward($route, Sabai_Application_Context $context, $stackContentName = false)
    {
        Sabai_Log::info(sprintf('Forwarding request to route "%s" by %s', $route, get_class($this)), __FILE__, __LINE__);

        if (!$stackContentName) {
            // Remove the last content name from the stack
            $context->response->popContentName();
        }

        // Update route data
        $context->pop('routesMatched');
        $context->route = $route;

        $router = $this->_getRouter($context);

        // Default controller requested explicitly?
        if ($route == '') {
            $this->_executeDefaultController($context, $router);
            return;
        }


        if ($router->isRoutable($context->route)) {
            $this->_executeRoutableController($context, $router);
        } else {
            // Forward to the parent controller if any
            if ($parent = $this->getParent()) {
                // Remove the global filters that have been activated by this controller
                $this->_activeFilters = array();

                $parent->forward($route, $context);
            } else {
                // use the default route if no parent
                $this->_executeDefaultController($context, $router);
            }
        }
    }

    /**
     * Runs the controller if any
     *
     * @param Sabai_Application_Context $context
     * @param Sabai_Application_RoutingControllerRouter $router
     */
    protected function _executeRoutableController($context, $router)
    {
        // Update context for executing the controller
        $this->_updateContext($context, $router);

        $this->_doExecuteController($context, $router->getController(), $router->getControllerArgs(), $router->getControllerFile());
    }

    /**
     * Updates context for the controller
     *
     * @param Sabai_Application_Context $context
     * @param Sabai_Application_RoutingControllerRouter $router
     */
    protected function _updateContext(Sabai_Application_Context $context, $router)
    {
        // Set request parameters if any
        foreach ($router->getParams() as $key => $value) {
            $context->request->set($key, $value);
        }

        // Set extra context variables if any
        foreach ($router->getContext() as $key => $value) {
            $context->$key = $value;
        }

        // Add matched route
        $route_matched = $context->push('routesMatched', $router->getRouteMatched());
        // Set the next route
        if (!$context->route = substr($context->route, strlen($route_matched))) {
            $context->route = '';
        }
    }

    /**
     * Executes the default controller
     *
     * @param Sabai_Application_Context $context
     * @param Sabai_Application_RoutingControllerRouter $router
     */
    protected function _executeDefaultController($context, $router)
    {
        // Add matched route
        $context->push('routesMatched', $context->route);
        // Set the next route
        $context->route = ''; // there should be no more route to match

        $this->_doExecuteController(
            $context,
            $router->getDefaultController(),
            $router->getDefaultControllerFile(),
            $router->getDefaultControllerArgs()
        );
    }

    /**
     * Runs the controller if any
     *
     * @param Sabai_Application_Context $context
     * @param string $controllerName
     * @param array $controllerArgs
     * @param string $controllerFile
     */
    protected function _doExecuteController($context, $controllerName, $controllerArgs = array(), $controllerFile = null)
    {
        if (!empty($controllerFile)) {
            $controller_class = $controllerName;
            $controller_class_file = $controllerFile;
        } else {
            $controller_class = $this->_controllerPrefix . $controllerName;
            $controller_class_file = $this->_controllerDir . '/' . $controllerName . '.php';
        }
        $context->response->pushContentName(strtolower($controller_class));
        if (file_exists($controller_class_file)) {
            Sabai_Log::info(sprintf('Executing controller %s(%s)', $controller_class, $controller_class_file), __FILE__, __LINE__);
            require_once $controller_class_file;
            $this->_getControllerInstance($controllerName, $controller_class, $controllerArgs)->execute($context);
            Sabai_Log::info(sprintf('Controller %s(%s) executed', $controllerName, $controller_class), __FILE__, __LINE__);
        }
    }

    protected function _getControllerInstance($controllerName, $controllerClass, $controllerArgs)
    {
        if (!isset($this->_controllers[$controllerName])) {
            if (!empty($controllerArgs)) {
                $reflection = new ReflectionClass($controllerClass);
                $controller = $reflection->newInstanceArgs($controllerArgs);
            } else {
                $controller = new $controllerClass();
            }
            $controller->setParent($this);
            $controller->setApplication($this->_application);
            $this->_controllers[$controllerName] = $controller;
        }
        return $this->_controllers[$controllerName];
    }

    /**
     * Returns a Sabai_Application_RoutingControllerRouter
     *
     * @return Sabai_Application_RoutingControllerRouter
     * @param Sabai_Application_Context $context
     */
    final protected function _getRouter(Sabai_Application_Context $context)
    {
        if (!isset($this->_router)) {
            $this->_router = $this->_doGetRouter($context);
        }
        return $this->_router;
    }

    /**
     * Returns a Sabai_Application_RoutingControllerRouter instance
     *
     * @return Sabai_Application_RoutingControllerRouter
     * @param Sabai_Application_Context $context
     */
    abstract protected function _doGetRouter($context);
}
