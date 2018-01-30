<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ParentEntitiesCount.php';

class Plugg_Xigg_Model_CommentsWithParentsCount extends Sabai_Model_EntityCollection_Decorator_ParentEntitiesCount
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Comment', $collection);
    }
}