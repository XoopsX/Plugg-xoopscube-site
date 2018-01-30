<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ParentEntitiesCount.php';

class Plugg_Page_Model_PagesWithParentsCount extends Sabai_Model_EntityCollection_Decorator_ParentEntitiesCount
{
    function __construct($collection)
    {
        parent::__construct('Page', $collection);
    }
}