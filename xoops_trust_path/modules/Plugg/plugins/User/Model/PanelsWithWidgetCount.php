<?php
require_once 'Sabai/Model/EntityCollection/Decorator/AssocEntitiesCount.php';

class Plugg_User_Model_PanelsWithWidgetCount extends Sabai_Model_EntityCollection_Decorator_AssocEntitiesCount
{
    public function __construct(Sabai_Model_EntityCollection $collection)
    {
        parent::__construct('Panelwidget', 'panelwidget_panel_id', 'Widget', $collection);
    }
}