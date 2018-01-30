<?php
class Plugg_Xigg_Model_CommentHTMLQuickForm extends Plugg_Xigg_Model_Base_CommentHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_bodyFiltered;
    private $_bodyFilterId;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $this->removeElements(array('Parent', 'Node', 'userid', 'body_filter_id', 'body_html'));
        $this->setRequired('title', $this->_model->_('You must enter title for the comment'), true, $this->_model->_(' '));
        $this->setRequired('body', $this->_model->_('You must enter something to comment'), true, $this->_model->_(' '));
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
        $this->_bodyFilterId = $entity->body_filter_id;
        $this->_bodyFiltered = $entity->body_html;
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