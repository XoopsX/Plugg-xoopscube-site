<?php
require_once 'Sabai/Model/EntityCollection/Decorator/DescendantEntitiesCount.php';

class Plugg_Page_Model_PagesWithDescendantsCount extends Sabai_Model_EntityCollection_Decorator_DescendantEntitiesCount
{
    function __construct($collection)
    {
        parent::__construct('Page', $collection);
    }
}