<?php
class Plugg_User_Model_Panel extends Plugg_User_Model_Base_Panel{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}

class Plugg_User_Model_PanelRepository extends Plugg_User_Model_Base_PanelRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}