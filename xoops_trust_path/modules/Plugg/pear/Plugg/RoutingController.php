<?php
require_once 'Sabai/Application/RoutingController.php';

abstract class Plugg_RoutingController extends Sabai_Application_RoutingController
{
    /**
     * Name of the default controller
     *
     * @var string
     */
    protected $_defaultController;
    /**
     * Arguments passed to the default controller if any
     *
     * @var array
     */
    protected $_defaultControllerArgs;
    /**
     * Path to the the default controller class file
     *
     * @var string
     */
    protected $_defaultControllerFile;
    protected $_defaultTabAjax = true;
    protected $_eventNamePrefix;

    private $_tabs = array();
    private $_tabSetAdded = false;
    private $_pageInfoAdded = 0;

    protected function __construct($defaultController, $controllerPrefix, $controllerDir, array $defaultControllerArgs = array(), $defaultControllerFile = null)
    {
        parent::__construct($controllerPrefix, $controllerDir);
        $this->_defaultController = $defaultController;
        $this->_defaultControllerArgs = $defaultControllerArgs;
        $this->_defaultControllerFile = $defaultControllerFile;
        $this->setFilters(array('_global'));
        $this->_eventNamePrefix = str_replace('_', '', substr(get_class($this), 6)); // Remove plugg_
    }

    protected function _globalBeforeFilter($context)
    {
        $this->_application->dispatchEvent($this->_eventNamePrefix . 'Enter', array($context));
    }

    protected function _globalAfterFilter($context)
    {
        $this->_application->dispatchEvent($this->_eventNamePrefix . 'Exit', array($context));
    }

    final public function forward($route, Sabai_Application_Context $context, $stackContentName = false)
    {
        if ($this->_tabSetAdded) {
            // Remove tabs added by previous controller request
            $context->response->removeTabSet();
        } elseif ($this->_pageInfoAdded > 0) {
            // Remove page info added previously
            do {
                $context->response->popPageInfo();
            } while (--$this->_pageInfoAdded);
        }

        parent::forward($route, $context, $stackContentName);
    }

    final protected function _updateContext($context, $router)
    {
        parent::_updateContext($context, $router);

        $routes = $router->getRoutes();
        $route = $router->getRouteSelected();

        // Any access callback defined?
        if (isset($routes[$route]['access_callback'])) {
            foreach ((array)$routes[$route]['access_callback'] as $method) {
                if (!call_user_func_array(array($this, $method), array($context, $routes[$route]['controller']))) {
                    // Access denied. Set error if not already set
                    if (!$context->response->isError()) {
                        $context->response->setError($context->plugin->_('Permission denied'));
                    }
                    $context->response->send($this->_application);
                }
            }
        }

        if (empty($routes[$route]['no_breadcrumb'])) {
            $breadcrumbs = array();

            if (!empty($routes[$route]['title'])) {
                $breadcrumbs[] = array(
                    'title' => $routes[$route]['title'],
                    'url' => array(
                        'base' => implode('', $context->routesMatched)
                    ),
                    'ajax' => !empty($this->_tabs[$route]['ajax'])
                );
            }

            if ($this->_addTabsIfExist($context)) {

                // Add link to the parent tab if requested route is not a tab
                if (empty($routes[$route]['tab'])) {
                    if (isset($routes[$route]['parent_tab'])) {
                        $route = $routes[$route]['parent_tab'];
                    } else {
                        // No parent set, so set the default tab as parent
                        $route = '';
                    }
                    $breadcrumbs[] = array(
                        'title' => $this->_tabs[$route]['title'],
                        'url' => $this->_tabs[$route]['url'],
                        'ajax' => $this->_tabs[$route]['ajax']
                    );
                }

                $context->response->setCurrentTab($route);
            }

            foreach (array_reverse($breadcrumbs) as $breadcrumb) {
                $context->response->setPageInfo($breadcrumb['title'], $breadcrumb['url'], $breadcrumb['ajax']);
                ++$this->_pageInfoAdded;
            }
        } else {
            if ($this->_addTabsIfExist($context)) {
                $context->response->setCurrentTab($route);
            }
        }
    }

    final protected function _executeDefaultController($context, $router)
    {
        if ($this->_addTabsIfExist($context)) {
            $context->response->setCurrentTab('');
        }

        parent::_executeDefaultController($context, $router);
    }

    protected function _doGetRouter($context)
    {
        $default_route = array(
            '' => array(
                'controller' => $this->_defaultController,
                'controller_args' => $this->_defaultControllerArgs,
                'controller_file' => $this->_defaultControllerFile,
                'tab' => true,
                'tab_ajax' => $this->_defaultTabAjax,
                'title' => $this->_getDefaultTabTitle($context),
            )
        );
        $routes = array();
        $this->_application->dispatchEvent($this->_eventNamePrefix . 'Routes', array(&$routes));
        $routes = array_merge(array_reverse($routes), $this->_getRoutes($context), $default_route);

        $route_prefix = implode('', $context->routesMatched);

        foreach ($routes as $route => $route_data) {
            // Check if the route is accessible by the user
            if (isset($route_data['access']) && !$route_data['access']) {
                unset($routes[$route]);
                continue;
            }

            if (!empty($routes[$route]['tab'])) {
                // Any access callback defined?
                if (isset($routes[$route]['access_callback'])) {
                    foreach ((array)$routes[$route]['access_callback'] as $method) {
                        if (!call_user_func_array(array($this, $method), array($context, $routes[$route]['controller']))) {
                            unset($routes[$route]);
                            continue 2;
                        }

                        // Access allowed
                        // Unset access callbacks to prevent multiple calls
                        unset($routes[$route]['access_callback']);
                        $this->_tabs[$route] = array(
                            'title' => $routes[$route]['title'],
                            'url' => array(
                                'base' => $route_prefix . $route,
                            ),
                            'ajax' => !empty($routes[$route]['tab_ajax']),
                        );
                    }
                } else {
                    // No access callbacks, so add the route to the tabs list immediately
                    $this->_tabs[$route] = array(
                        'title' => $routes[$route]['title'],
                        'url' => array(
                            'base' => $route_prefix . $route,
                        ),
                        'ajax' => !empty($routes[$route]['tab_ajax']),
                    );
                }
            }
        }

        require_once 'Plugg/RoutingControllerRouter.php';
        return new Plugg_RoutingControllerRouter($routes);
    }

    private function _addTabsIfExist($context)
    {
        // No need to make tabs if not more than 1 tab
        if (count($this->_tabs) > 1) {
            // Add tabs
            $this->_tabSetAdded = $context->response->addTabSet(array_reverse($this->_tabs))->getCurrentTabSet();
        }

        return $this->_tabSetAdded;
    }

    /**
     * Returns all route data for the default router as an associative array
     *
     * @access protected
     * @return array
     * @param Sabai_Application_Context $context
     */
    protected function _getRoutes($context)
    {
        return array();
    }

    protected function _getDefaultTabTitle($context)
    {
        return $this->_application->getGettext()->_('Top');
    }
}