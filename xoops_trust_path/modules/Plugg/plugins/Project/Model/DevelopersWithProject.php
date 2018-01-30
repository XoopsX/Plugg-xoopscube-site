<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntity.php';

class Plugg_Project_Model_DevelopersWithProject extends Sabai_Model_EntityCollection_Decorator_ForeignEntity
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('project_id', 'Project', 'project_id', $collection);
    }
}