<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntitiesLast.php';

class Plugg_Xigg_Model_NodesWithLastVote extends Sabai_Model_EntityCollection_Decorator_ForeignEntitiesLast
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('vote_last', 'Vote', 'vote_id', $collection);
    }
}