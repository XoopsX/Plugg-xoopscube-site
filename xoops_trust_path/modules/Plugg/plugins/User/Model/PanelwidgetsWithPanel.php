<?php
require_once 'Sabai/Model/EntityCollection/Decorator/ForeignEntity.php';

class Plugg_User_Model_PanelwidgetsWithPanel extends Sabai_Model_EntityCollection_Decorator_ForeignEntity
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('panel_id', 'Panel', 'panel_id', $collection);
    }
}