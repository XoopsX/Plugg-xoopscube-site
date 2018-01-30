<?php
require_once 'Sabai/Model/EntityCollection/Decorator/AssocEntities.php';

class Plugg_Xigg_Model_TagsWithNodes extends Sabai_Model_EntityCollection_Decorator_AssocEntities
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Node2tag', 'node2tag_tag_id', 'node', 'Node', $collection);
    }
}