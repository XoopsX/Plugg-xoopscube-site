<?php
require_once 'Sabai/Model/EntityCollection/Decorator/DescendantEntitiesCount.php';

class Plugg_Xigg_Model_CategoriesWithDescendantsCount extends Sabai_Model_EntityCollection_Decorator_DescendantEntitiesCount
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Category', $collection);
    }
}