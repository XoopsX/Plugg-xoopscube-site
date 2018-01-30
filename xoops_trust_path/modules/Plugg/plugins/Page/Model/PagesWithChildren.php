<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ChildEntities.php';

class Plugg_Page_Model_PagesWithChildren extends Sabai_Model_EntityCollection_Decorator_ChildEntities
{
    function __construct($collection)
    {
        parent::__construct('Page', 'page_parent', $collection);
    }
}