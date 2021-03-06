<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntities.php';

class Plugg_Project_Model_ProjectsWithDevelopers extends Sabai_Model_EntityCollection_Decorator_ForeignEntities
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('developer_project_id', 'Developer', $collection);
    }
}