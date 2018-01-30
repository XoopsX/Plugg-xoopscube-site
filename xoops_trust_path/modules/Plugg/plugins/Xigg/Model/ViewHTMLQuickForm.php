<?php
class Plugg_Xigg_Model_ViewHTMLQuickForm extends Plugg_Xigg_Model_Base_ViewHTMLQuickForm
{
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)

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