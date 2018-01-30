<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntity.php';

class Plugg_Xigg_Model_NodesWithCategory extends Sabai_Model_EntityCollection_Decorator_ForeignEntity
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('category_id', 'Category', 'category_id', $collection);
    }
}