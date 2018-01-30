<?php
abstract class Sabai_Application_ModelEntityController extends Sabai_Application_Controller
{
    /**
     * @var string
     * @access protected
     */
    protected $_entityName;
    /**
     * @var string
     * @access protected
     */
    protected $_entityNameLc;
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
     * @return Sabai_Application_ModelEntityController
     */
    protected function __construct($entityName, array $options = array())
    {
        $this->_entityName = $entityName;
        $this->_entityNameLc = strtolower($entityName);
        $this->_options = array_merge(array(
            'viewName'   => null,
            'successUrl' => null,
            'errorUrl'   => null
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

    /**
     * Returns the model object
     *
     * @return Sabai_Model
     * @param Sabai_Application_Context
     */
    abstract protected function _getModel(Sabai_Application_Context $context);
}