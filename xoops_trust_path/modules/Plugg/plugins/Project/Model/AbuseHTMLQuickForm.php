<?php
class Plugg_Project_Model_AbuseHTMLQuickForm extends Plugg_Project_Model_Base_AbuseHTMLQuickForm
{
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)

        // remove user id form element by default
        $this->removeElements(array('userid', 'Project'));
        $reason = $this->getElement('reason');
        foreach ($this->_model->getPlugin()->getAbuseReasons() as $value => $label) {
            $reason->addOption($label, $value);
        }
        $this->setRequired('reason', $this->_model->_('Please select the reason for this report'));
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
    }
}