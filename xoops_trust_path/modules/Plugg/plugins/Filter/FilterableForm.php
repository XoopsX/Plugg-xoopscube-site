<?php
interface Plugg_Filter_FilterableForm
{
    function getFilterableElementNames();
    function setFilteredValue($elementName, $filteredText, $filterId);
}