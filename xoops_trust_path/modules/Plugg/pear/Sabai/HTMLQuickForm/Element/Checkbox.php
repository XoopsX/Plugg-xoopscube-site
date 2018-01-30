<?php
require_once 'HTML/QuickForm/checkbox.php';

class Sabai_HTMLQuickForm_Element_Checkbox extends HTML_QuickForm_checkbox
{   
    /*
     * Overrides the parent method to cope with the bug below
     * http://pear.php.net/bugs/bug.php?id=15298
     */
    function getValue()
    {
        return $this->getAttribute('value');
    }
}