<?php
require_once 'Plugg/FormController.php';

abstract class Plugg_ModelEntityController extends Plugg_FormController
{
    /**
     * @var string
     * @access protected
     */
    protected $_entityName;
    /**
     * @var array
     * @access protected
     */
    protected $_options = array();

    /**
     * Constructor
     *
     * @param string $entityName
     * @param array $options
     * @return Plugg_ModelEntityController
     */
    protected function __construct($entityName, array $options = array())
    {
        $this->_entityName = $entityName;
        $this->_options = array_merge(array(
            'viewName'   => null,
            'successUrl' => array(),
            'errorUrl'   => array()
        ), $options);
    }

    final protected function _getErrorUrl()
    {
        return $this->_options['errorUrl'];
    }

    final protected function _getSuccessUrl()
    {
        return $this->_options['successUrl'];
    }

    final protected function _getOption($name)
    {
        return $this->_options[$name];
    }

    final protected function _setOption($name, $value)
    {
        $this->_options[$name] = $value;
    }
}