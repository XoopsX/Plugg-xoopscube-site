<?php
class Plugg_User_Model_Activewidget extends Plugg_User_Model_Base_Activewidget{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}

class Plugg_User_Model_ActivewidgetRepository extends Plugg_User_Model_Base_ActivewidgetRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}