<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ChildEntities.php';

class Plugg_Xigg_Model_CategoriesWithChildren extends Sabai_Model_EntityCollection_Decorator_ChildEntities
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Category', 'category_parent', $collection);
    }
}