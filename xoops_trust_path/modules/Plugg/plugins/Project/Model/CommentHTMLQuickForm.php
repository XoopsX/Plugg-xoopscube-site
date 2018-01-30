<?php
class Plugg_Project_Model_CommentHTMLQuickForm extends Plugg_Project_Model_Base_CommentHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_bodyFilterId;
    private $_bodyFiltered;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)

        $this->removeElements(array('userid', 'Project'));
        $rating = $this->getElement('rating');
        foreach (array(0 => '-', 5 => $this->_model->_('5 stars'), 4 => $this->_model->_('4 stars'), 3 => $this->_model->_('3 stars'), 2 => $this->_model->_('2 stars'), 1 => $this->_model->_('1 star') ) as $value => $label) {
            $rating->addOption($label, $value);
        }
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