<?php
require_once 'HTML/QuickForm/Rule/Required.php';

class Sabai_HTMLQuickForm_Rule_Required extends HTML_QuickForm_Rule_Required
{
    /**
     * Overrides the default Required rule to cope with a warning error when element type is multiple select 
     *
     * @param mixed $value
     * @param array $options
     * @return bool
     */
    function validate($value, $options = null)
    {
        if (is_array($value)) {
            return empty($value) ? false : true;
        }
        return parent::validate($value, $options);
    }
}