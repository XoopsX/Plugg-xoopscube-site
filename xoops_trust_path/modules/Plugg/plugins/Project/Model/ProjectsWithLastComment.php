<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntitiesLast.php';

class Plugg_Project_Model_ProjectsWithLastComment extends Sabai_Model_EntityCollection_Decorator_ForeignEntitiesLast
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('comment_last', 'Comment', 'comment_id', $collection);
    }
}