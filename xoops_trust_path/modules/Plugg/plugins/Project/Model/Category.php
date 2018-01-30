<?php
class Plugg_Project_Model_Category extends Plugg_Project_Model_Base_Category{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}

class Plugg_Project_Model_CategoryRepository extends Plugg_Project_Model_Base_CategoryRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}