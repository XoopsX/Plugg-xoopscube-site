<?php
require_once 'HTML/QuickForm/group.php';

class Sabai_HTMLQuickForm_Element_Group extends HTML_QuickForm_group
{
    var $_groupType;
    var $_elementIndex = array();

    function setGroupType($type)
    {
        $this->_groupType = $type;
    }

    function getGroupType()
    {
        return isset($this->_groupType) ? $this->_groupType : parent::getGroupType();
    }

    function setAppendName($flag = true)
    {
        $this->_appendName = (bool)$flag;
    }

    /*
     * Overrides the parent method to allow setting an error message for each element
     * inside the group element
     * http://pear.php.net/bugs/bug.php?id=14997
     */
    function accept($renderer, $required = false, $error = null)
    {
        $this->_createElementsIfNotExist();
        $element_errors = array();
        if (is_array($error)) {
            $element_errors = $error;
            $error = null;
        }
        $renderer->startGroup($this, $required, $error);
        $name = $this->getName();
        foreach (array_keys($this->_elements) as $key) {
            $element = $this->_elements[$key];
            $elementName = $element->getName();
            $element_error = null;
            if ($this->_appendName) {
                if (isset($elementName)) {
                    if (strlen($elementName)) {
                        $element_name = $name . '['. $elementName .']';
                        $element->setName($element_name);
                        if (isset($element_errors[$element_name])) $element_error = $element_errors[$element_name];
                    } else {
                        $element->setName($name . '['. $key .']');
                    }
                } else {
                    $element->setName($name);
                }
            } else {
                if (isset($element_errors[$elementName])) $element_error = $element_errors[$elementName];
            }
            $required = !$element->isFrozen() && in_array($element->getName(), $this->_required);
            $element->accept($renderer, $required, $element_error);
            // restore the element's name
            if ($this->_appendName) {
                $element->setName($elementName);
            }
        }
        $renderer->finishGroup($this);
    }

    function addElement($element)
    {
        $elements = $this->getElements();
        $elements[] = $element;
        $this->setElements($elements);
    }

    function setElements($elements)
    {
        parent::setElements($elements);

        $this->_elementIndex = array();
        foreach (array_keys($this->_elements) as $i) {
        	$this->_elementIndex[$this->_elements[$i]->getName()] = $i;
        }
    }

    function getElement($elementName)
    {
    	if (!isset($this->_elementIndex[$elementName])) return;

    	return $this->_elements[$this->_elementIndex[$elementName]];
    }
}