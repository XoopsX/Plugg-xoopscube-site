<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntitiesLast.php';

class Plugg_Project_Model_ReleasesWithLastReport extends Sabai_Model_EntityCollection_Decorator_ForeignEntitiesLast
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('report_last', 'Report', 'report_id', $collection);
    }
}