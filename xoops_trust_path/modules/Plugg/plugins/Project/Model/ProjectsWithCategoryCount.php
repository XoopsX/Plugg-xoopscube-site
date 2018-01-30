<?php
require_once 'Sabai/Model/EntityCollection/Decorator/AssocEntitiesCount.php';

class Plugg_Project_Model_ProjectsWithCategoryCount extends Sabai_Model_EntityCollection_Decorator_AssocEntitiesCount
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Project2category', 'project2category_project_id', 'Category', $collection);
    }
}