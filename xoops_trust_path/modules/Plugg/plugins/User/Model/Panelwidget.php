<?php
class Plugg_User_Model_Panelwidget extends Plugg_User_Model_Base_Panelwidget{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}

class Plugg_User_Model_PanelwidgetRepository extends Plugg_User_Model_Base_PanelwidgetRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}