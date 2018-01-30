<?php
require_once 'HTML/QuickForm/select.php';

class Sabai_HTMLQuickForm_Element_SelectModelTreeEntity extends HTML_QuickForm_select
{
    var $_model;
    var $_entityName;
    var $_prefix;

    function __construct($model = null, $entityName = null, $prefix = null, $elementName = null, $elementLabel = null, $options = null, $attributes = null)
    {
        $this->setModel($model);
        $this->setEntityName($entityName);
        $this->setPrefix($prefix);
        parent::HTML_QuickForm_select($elementName, $elementLabel, $options, $attributes);
    }
    
    /**
     * PHP4 style constructor required for compat with the HTMLQuickForm library
     */
    function Sabai_HTMLQuickForm_Element_SelectModelTreeEntity($model = null, $entityName = null, $prefix = null, $elementName = null, $elementLabel = null, $options = null, $attributes = null)
    {
        $this->__construct($model, $entityName, $prefix, $elementName, $elementLabel, $options, $attributes);
    }

    function setModel($model)
    {
        $this->_model = $model;
    }

    function setEntityName($entityName)
    {
        $this->_entityName = $entityName;
    }

    function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }

    function accept($renderer)
    {
        // option for no parent
        $this->addOption('', 0);
        $entities = array();
        foreach ($this->_model->getRepository($this->_entityName)->fetch() as $option) {
            $entities[$option->getParentId()][] = $option;
        }
        $prefix = !isset($this->_prefix) ? ' - ' : $this->_prefix;
        if (!empty($entities[0])) {
            foreach (array_keys($entities[0]) as $i) {
                $this->_fillTreeEntityOption($entities, $entities[0][$i], $prefix);
            }
        }
        parent::accept($renderer);
    }

    function _fillTreeEntityOption($entities, $entity, $prefix)
    {
        $id = $entity->getId();
        $this->addOption($prefix . $entity->getLabel(), $id);
        if (!empty($entities[$id])) {
            foreach (array_keys($entities[$id]) as $i) {
                $this->_fillTreeEntityOption($entities, $entities[$id][$i], $prefix . $this->_prefix);
            }
        }
    }
}