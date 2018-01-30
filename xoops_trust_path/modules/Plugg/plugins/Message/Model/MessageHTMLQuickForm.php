<?php
class Plugg_Message_Model_MessageHTMLQuickForm extends Plugg_Message_Model_Base_MessageHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    var $_bodyFilterId;
    var $_bodyFiltered;
    
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)

        // remove user id form element by default
        $this->removeElements(array('userid', 'body_html', 'body_filter_id', 'from_to', 'read', 'star', 'deleted', 'type', 'key'));
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
        $this->_bodyFilterId = $entity->body_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
        $entity->body_html = $this->_bodyFiltered;
        $entity->body_filter_id = $this->_bodyFilterId;
    }
    
    public function getFilterableElementNames()
    {
        return array('body' => $this->_bodyFilterId);
    }
    
    public function setFilteredValue($elementName, $filteredText, $filterId)
    {
        switch ($elementName) {
            case 'body':
                $this->_bodyFiltered = $filteredText;
                $this->_bodyFilterId = $filterId;
        }
    }
}