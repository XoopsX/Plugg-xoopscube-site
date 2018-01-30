<?php
class Plugg_Project_Model_LinkHTMLQuickForm extends Plugg_Project_Model_Base_LinkHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_summaryFilterId;
    private $_summaryFiltered;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $this->removeElements(array('userid', 'Project'));
        $type = $this->getElement('type');
        foreach ($this->_model->getPlugin()->getLinkTypes() as $value => $label) {
            $type->addOption($label, $value);
        }
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
        $this->_summaryFilterId = $entity->summary_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
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