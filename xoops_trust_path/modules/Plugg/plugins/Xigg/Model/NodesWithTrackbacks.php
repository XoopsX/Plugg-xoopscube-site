<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntities.php';

class Plugg_Xigg_Model_NodesWithTrackbacks extends Sabai_Model_EntityCollection_Decorator_ForeignEntities
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('trackback_node_id', 'Trackback', $collection);
    }
}