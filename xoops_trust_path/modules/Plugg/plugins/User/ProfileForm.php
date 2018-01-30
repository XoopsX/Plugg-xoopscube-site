<?php
require_once 'Sabai/HTMLQuickForm.php';

class Plugg_User_ProfileForm extends Sabai_HTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_filterableElementNames = array();
    private $_filteredValues = array();
    
    public function getFilterableElementNames()
    {
        return $this->_filterableElementNames;
    }
    
    public function setFilteredValue($elementName, $filteredText, $filterId)
    {
        $this->_filteredValues[$elementName] = array($filteredText, $filterId);
    }
    
    public function addFilterableElement($elementName, $filterId = null)
    {
         $this->_filterableElementNames[$elementName] = $filterId;
    }
    
    public function hasFilteredValue($elementName)
    {
        return isset($this->_filteredValues[$elementName]);
    }
    
    public function getFilteredValue($elementName)
    {
        return $this->_filteredValues[$elementName];
    }
}