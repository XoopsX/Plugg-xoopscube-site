<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ParentEntitiesCount.php';

class Plugg_Xigg_Model_CategoriesWithParentsCount extends Sabai_Model_EntityCollection_Decorator_ParentEntitiesCount
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Category', $collection);
    }
}