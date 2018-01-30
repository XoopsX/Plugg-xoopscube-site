<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntity.php';

class Plugg_Project_Model_LinkvotesWithLink extends Sabai_Model_EntityCollection_Decorator_ForeignEntity
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('link_id', 'Link', 'link_id', $collection);
    }
}