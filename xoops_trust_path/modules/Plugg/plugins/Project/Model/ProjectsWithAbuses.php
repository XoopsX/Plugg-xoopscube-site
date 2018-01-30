<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntities.php';

class Plugg_Project_Model_ProjectsWithAbuses extends Sabai_Model_EntityCollection_Decorator_ForeignEntities
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('abuse_project_id', 'Abuse', $collection);
    }
}