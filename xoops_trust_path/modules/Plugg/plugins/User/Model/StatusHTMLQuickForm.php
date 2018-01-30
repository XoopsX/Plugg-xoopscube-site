<?php
class Plugg_User_Model_StatusHTMLQuickForm extends Plugg_User_Model_Base_StatusHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    var $_textFilterId;
    var $_textFiltered;
    
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)

        // remove user id form element by default
        $this->removeElement('userid');
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
        $this->_textFilterId = $entity->text_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
        $entity->text_filtered = $this->_textFiltered;
        $entity->text_filter_id = $this->_textFilterId;
    }
    
    public function getFilterableElementNames()
    {
        return array('text' => $this->_textFilterId);
    }
    
    public function setFilteredValue($elementName, $filteredText, $filterId)
    {
        switch ($elementName) {
            case 'text':
                $this->_textFiltered = $filteredText;
                $this->_textFilterId = $filterId;
        }
    }
}