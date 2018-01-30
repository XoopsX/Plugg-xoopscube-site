<?php
class Plugg_Project_Model_Abuse extends Plugg_Project_Model_Base_Abuse
{
    function setEntity($entity)
    {
        $this->set('entity', $entity->getName());
        $this->set('entity_id', $entity->getId());
    }

    function isConfirmed()
    {
        return $this->get('status') == Plugg_Project_Plugin::ABUSE_STATUS_CONFIRMED;
    }

    function setConfirmed()
    {
        $this->set('status', Plugg_Project_Plugin::ABUSE_STATUS_CONFIRMED);
    }

    function setPending()
    {
        $this->set('status', Plugg_Project_Plugin::ABUSE_STATUS_PENDING);
    }
    
    function getReasonStr()
    {
        $reasons = $this->_model->getPlugin()->getAbuseReasons();
        return $reasons[$this->get('reason')];
    }
}

class Plugg_Project_Model_AbuseRepository extends Plugg_Project_Model_Base_AbuseRepository
{
}