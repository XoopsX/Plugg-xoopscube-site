<?php
class Plugg_Project_Model_Release extends Plugg_Project_Model_Base_Release
{
    function getStabilityStr()
    {
        $stabilities = $this->_model->getPlugin()->getReleaseStabilities();
        return $stabilities[$this->get('stability')];
    }

    function getDateStr()
    {
        return ($date = $this->get('date')) ? date($this->_model->_('F jS, Y'), $date) : $this->_model->_('Unknown');
    }

    function getDateStrShort()
    {
        return ($date = $this->get('date')) ? date($this->_model->_('M jS, Y'), $date) : $this->_model->_('N/A');
    }

    function isApproved()
    {
        return $this->get('status') == Plugg_Project_Plugin::RELEASE_STATUS_APPROVED;
    }

    function setApproved()
    {
        $this->set('status', Plugg_Project_Plugin::RELEASE_STATUS_APPROVED);
    }

    function setPending()
    {
        $this->set('status', Plugg_Project_Plugin::RELEASE_STATUS_PENDING);
    }

    function getVersionStr()
    {
        return h($this->get('version'));
    }
}

class Plugg_Project_Model_ReleaseRepository extends Plugg_Project_Model_Base_ReleaseRepository
{
}