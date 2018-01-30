<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntities.php';

class Plugg_Project_Model_ProjectsWithImages extends Sabai_Model_EntityCollection_Decorator_ForeignEntities
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('image_project_id', 'Image', $collection);
    }
}