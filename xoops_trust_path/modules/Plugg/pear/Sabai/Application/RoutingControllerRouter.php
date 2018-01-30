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
 * Short description for class
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
class Sabai_Application_RoutingControllerRouter
{
    /**
     * @var string
     */
    protected $_controllerRegex;
    /**
     * @var array
     */
    protected $_routes = array();
    /**
     * @var string
     */
    protected $_controller;
    /**
     * @var string
     */
    protected $_params = array();
    /**
     * @var string
     */
    protected $_routeMatched = '';
    /**
     * @var string
     */
    protected $_forward;
    /**
     * @var string
     */
    protected $_file;
    /**
     * @var array
     */
    protected $_args;
    /**
     * @var array
     */
    protected $_context;
    /**
     * @var array
     */
    protected $_routeData;

    /**
     * Constructor
     *
     * @param string $controllerRegex
     * @return Sabai_Application_RoutingControllerRouter
     */
    function __construct($controllerRegex = '[a-z][a-z0-9_]*')
    {
        $this->_controllerRegex = $controllerRegex;
    }

    /**
     * Gets the name of requested controller found in route
     *
     * @return string
     */
    function getController()
    {
        return $this->_controller;
    }

    /**
     * Gets the values of extra parameters
     *
     * @return array
     */
    function getParams()
    {
        return $this->_params;
    }

    /**
     * Returns the route matched for the request
     *
     * @return string
     */
    function getRouteMatched()
    {
        return $this->_routeMatched;
    }

    /**
     * Returns a route to forward request to
     *
     * @return string
     */
    function getForward()
    {
        return $this->_forward;
    }

    /**
     * Returns controller file path
     *
     * @return string
     */
    function getFile()
    {
        return $this->_file;
    }

    /**
     * Returns controller constructor paramters
     *
     * @return array
     */
    function getArgs()
    {
        return $this->_args;
    }
    
    /**
     * Returns controller constructor paramters
     *
     * @return array
     */
    function getContext()
    {
        return $this->_context;
    }
    
    function getRouteData($key = null)
    {
        return isset($key) ? @$this->_routeData[$key] : $this->_routeData;
    }

    /**
     * Adds a valid route
     *
     * @param string $routeStr
     * @param array $routeData
     */
    function addRoute($routeStr, $routeData = array())
    {
        $this->_routes[$routeStr] = $routeData;
    }
    
    /**
     * Sets all routes
     *
     * @param array $routes
     */
    function setRoutes($routes)
    {
        $this->_routes = $routes;
    }

    /**
     * Finds the best matched route available for the requested path
     *
     * @param string $route
     * @return bool true if route found, false otherwise
     */
    function isRoutable($route)
    {
        $route = rtrim($route, '/');
        $path_parts_count = count(explode('/', $route));
        if ($path_parts_count < 1) {
            // no requested route
            return false;
        }
        krsort($this->_routes, SORT_STRING);
        foreach ($this->_routes as $route_key => $route_data) {
            $route_str = rtrim($route_key, '/');
            $route_parts = explode('/', $route_str);
            $route_parts_count = count($route_parts);
            if ($route_parts_count > $path_parts_count) {
                // defined route string is longer than pathinfo
                continue;
            }
            if ($route_parts[0] == '') {
                // Route is a full path
                $regex_parts = array('');
                unset($route_parts[0]);
            } else {
                $regex_parts = array();
            }
            foreach (array_keys($route_parts) as $i) {
                if (0 === strpos($route_parts[$i], ':')) {
                    if (!empty($route_data['requirements'][$route_parts[$i]])) {
                        $regex_parts[$i] = '(' . str_replace('#', '\#', $route_data['requirements'][$route_parts[$i]]) . ')';
                    } elseif ($route_parts[$i] == ':controller') {
                        $regex_parts[$i] = '(' . $this->_controllerRegex . ')';
                    } else {
                        $regex_parts[$i] = '([a-z0-9~\s\.:_\-]+)';
                    }
                } else {
                    $regex_parts[$i] = '(' . $route_parts[$i] . ')';
                }
            }
            $regex = implode('/', $regex_parts);
            if (preg_match('#^' . $regex . '/#i', $route . '/', $matches)) {
                // unset the route string that matched to prevent circular routing 
                unset($this->_routes[$route_key]);                
                // get the route matched
                $this->_routeMatched = array_shift($matches);
                $this->_forward = isset($route_data['forward']) ? $route_data['forward'] : '';
                Sabai_Log::info(sprintf('Route %s matched with requested path %s', $regex, $route), __FILE__, __LINE__);

                foreach (array_keys($route_parts) as $i) {
                    $match = array_shift($matches);
                    // :controller is handled differently
                    if ($route_parts[$i] == ':controller') {
                        $this->_controller = $match;
                    } elseif (0 === strpos($route_parts[$i], ':')) {
                        $this->_params[str_replace(':', '', $route_parts[$i])] = $match;
                        $this->_forward = str_replace($route_parts[$i], $match, $this->_forward);
                    }
                }
                if (isset($route_data['controller'])) $this->_controller = $route_data['controller'];
                if (!empty($route_data['params'])) {
                    foreach ($route_data['params'] as $name => $value) {
                        $this->_params[$name] = $value;
                    }
                }
                $this->_file = isset($route_data['file']) ? $route_data['file'] : null;
                $this->_args = !empty($route_data['args']) ? $route_data['args'] : array();
                $this->_context = !empty($route_data['context']) ? $route_data['context'] : array();
                $this->_routeData = $route_data;
                return true;
            }
        }
        return false;
    }
}