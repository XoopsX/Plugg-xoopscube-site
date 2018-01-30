<?php
require_once 'Sabai/Model/EntityCollection/Decorator/AssocEntities.php';

class Plugg_Project_Model_ProjectsWithCategories extends Sabai_Model_EntityCollection_Decorator_AssocEntities
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Project2category', 'project2category_project_id', 'category', 'Category', $collection);
    }
}