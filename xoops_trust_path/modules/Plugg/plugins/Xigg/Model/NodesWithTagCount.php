<?php
require_once 'Sabai/Model/EntityCollection/Decorator/AssocEntitiesCount.php';

class Plugg_Xigg_Model_NodesWithTagCount extends Sabai_Model_EntityCollection_Decorator_AssocEntitiesCount
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Node2tag', 'node2tag_node_id', 'Tag', $collection);
    }
}