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
 * @copyright  Copyright (c) 2008 myWeb Japan (http://www.myweb.ne.jp/)
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
 * @copyright  Copyright (c) 2008 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.8
 */
abstract class Sabai_Application_Controller
{
    /**
     * @var Sabai_Application
     */
    protected $_application;
    /**
     * @var array
     */
    private $_filters = array();
    /**
     * @var array
     */
    private $_activeFilters = array();

    /**
     * @var Sabai_Application_RoutingController
     */
    private $_parent;

    /**
     * Sets the parent controller
     *
     * @param Sabai_Application_RoutingController $controller
     */
    public function setParent(Sabai_Application_RoutingController $controller)
    {
        $this->_parent = $controller;
    }

    /**
     * Gets the parent controller
     *
     * @return Sabai_Application_RoutingController $controller
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Adds a filter for all actions in the controller
     *
     * @param mixed $filter Sabai_Handle object or string
     */
    public function addFilter($filter)
    {
        $this->_filters[] = $filter;
    }

    /**
     * Adds filters for all actions in the controller
     *
     * @param array $filters array of Sabai_Handle object or string
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * Adds a filter to the first index for all actions in the controller
     *
     * @param mixed $filter Sabai_Handle object or string
     */
    public function prependFilter($filter)
    {
        array_unshift($this->_filters, $filter);
    }

    /**
     * Sets filters for all actions in the controller
     *
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->_filters = $filters;
    }

    /**
     * Executes the controller
     *
     * @param Sabai_Application_Context $context
     */
    public function execute(Sabai_Application_Context $context)
    {
        $this->_filterBefore($context);
        $this->_doExecute($context);
        $this->_filterAfter($context);
    }

    /**
     * Executes the controller
     *
     * @param Sabai_Application_Context $context
     */
    abstract protected function _doExecute(Sabai_Application_Context $context);

    /**
     * Executes pre-filters
     *
     * @param Sabai_Application_Context $context
     */
    protected function _filterBefore($context)
    {
        foreach (array_keys($this->_filters) as $i) {
            $this->_executeBeforeFilter($this->_filters[$i], $context);

            // Add the filter to the active filters stack
            $this->_activeFilters[$i] = $this->_filters[$i];
        }
    }

    /**
     * Executes a before filter
     *
     * @param mixed $filter
     * @param Sabai_Application_Context $context
     */
    protected function _executeBeforeFilter($filter, $context)
    {
        if (is_object($filter)) {
            $filter->instantiate()->before($context, $this->_application);
        } else {
            $method = $filter . 'BeforeFilter';
            $this->$method($context);
        }
    }

    /**
     * Executes after filters
     *
     * @param Sabai_Application_Context $context
     */
    protected function _filterAfter($context)
    {
        foreach (array_keys($this->_activeFilters) as $i) {
            $this->_executeAfterFilter($this->_activeFilters[$i], $context);

            // Remove the filter from the active filters stack
            unset($this->_activeFilters[$i]);
        }
    }

    /**
     * Executes a before filter
     *
     * @param mixed $filter
     * @param Sabai_Application_Context $context
     */
    protected function _executeAfterFilter($filter, $context)
    {
        if (is_object($filter)) {
            $filter->instantiate()->after($context, $this->_application);
        } else {
            $method = $filter . 'AfterFilter';
            $this->$method($context);
        }
    }

    /**
     * Recursively call parent method until the method is found and executed.
     */
    public function __call($method, $args)
    {
        if (isset($this->_parent)) {
            return call_user_func_array(array($this->_parent, $method), $args);
        }
        trigger_error('Call to undefined function: ' . $method, E_USER_WARNING);
    }

    /**
     * Sets an application instance
     *
     * @param Sabai_Application $application
     */
    public function setApplication(Sabai_Application $application)
    {
        $this->_application = $application;
    }

    /**
     * Magic method
     */
    public function __get($name)
    {
        return $this->_application->$name;
    }

    /*throw new Exception*
     * Forwards to another route
     *
     * @param string $route
     * @param Sabai_Application_Context $context
     * @param bool $stackContentName
     */
    public function forward($route, Sabai_Application_Context $context, $stackContentName = false)
    {
        // Remove the global filters that have been activated by this controller
        $this->_activeFilters = array();

        $this->_parent->forward($route, $context, $stackContentName);
    }
}
