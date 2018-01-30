<?php
class Plugg_Signature_Model_Signature extends Plugg_Signature_Model_Base_Signature{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}

class Plugg_Signature_Model_SignatureRepository extends Plugg_Signature_Model_Base_SignatureRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct($model);
    }
}