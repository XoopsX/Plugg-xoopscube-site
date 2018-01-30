<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntity.php';

class Plugg_Project_Model_ProjectsWithLatestRelease extends Sabai_Model_EntityCollection_Decorator_ForeignEntity
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('release_latest', 'Release', 'release_id', $collection, 'LatestRelease');
    }
}