<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntitiesLast.php';

class Plugg_Project_Model_ProjectsWithLastLink extends Sabai_Model_EntityCollection_Decorator_ForeignEntitiesLast
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('link_last', 'Link', 'link_id', $collection);
    }
}