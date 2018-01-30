<?php
class Plugg_Xigg_Model_CategoryHTMLQuickForm extends Plugg_Xigg_Model_Base_CategoryHTMLQuickForm
{
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $this->setRequired('name', $this->_model->_('You must enter a name for the category'), true, $this->_model->_(' '));
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