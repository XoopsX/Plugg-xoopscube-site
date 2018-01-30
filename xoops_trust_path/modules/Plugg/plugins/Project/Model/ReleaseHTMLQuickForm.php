<?php
class Plugg_Project_Model_ReleaseHTMLQuickForm extends Plugg_Project_Model_Base_ReleaseHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_summaryFilterId;
    private $_summaryFiltered;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $stability = $this->getElement('stability');
        foreach ($this->_model->getPlugin()->getReleaseStabilities() as $value => $label) {
            $stability->addOption($label, $value);
        }
        $this->removeElements(array('userid', 'Project'));
        $this->addRule('stability', $this->_model->_('Stability must be selected'), 'required', null, 'client');
        $this->addRule('version', $this->_model->_('Version number must start with a number followed by zero or more digit/alphanumerical values. A digit may not be used after using an alphabet and must always be followed by a number.'), 'regex', '/^\d+(?:\.\d+)*(?:[a-zA-Z]+\d*)?$/', 'client');
        $this->setElementLabel('date', $this->_model->_('Release date'));
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        if (!$entity->getId()) {
            $this->setDefaults(array('version' => '0.0.0', 'date' => time()));
        }

        $this->_summaryFilterId = $entity->summary_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
        $date = $this->getSubmitValue('date');
        foreach ($date as $format => $value) {
            switch ($format) {
                case 'Y':
                case 'y':
                    $year = $value;
                    break;
                case 'm':
                case 'M':
                case 'F':
                case 'n':
                    $month = $value;
                    break;
                case 'd':
                case 'j':
                    $day = $value;
                    break;
                default:
            }
        }
        $entity->set('date', mktime(0, 0, 0, $month, $day, $year));

        $entity->summary_html = $this->_summaryFiltered;
        $entity->summary_filter_id = $this->_summaryFilterId;
    }

    public function getFilterableElementNames()
    {
        return array('summary' => $this->_summaryFilterId);
    }

    public function setFilteredValue($elementName, $filteredText, $filterId)
    {
        switch ($elementName) {
            case 'summary':
                $this->_summaryFiltered = $filteredText;
                $this->_summaryFilterId = $filterId;
        }
    }
}