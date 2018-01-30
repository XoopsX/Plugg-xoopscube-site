<?php
class Plugg_Page_Model_PageHTMLQuickForm extends Plugg_Page_Model_Base_PageHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_entityId;
    private $_contentFilterId;
    private $_contentFiltered;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)

        // set lock options
        $lock = $this->getElement('lock');
        $lock->addOption($this->_model->_('Inherit'), Plugg_Page_Plugin::PAGE_LOCK_INHERIT);
        $lock->addOption($this->_model->_('Lock'), Plugg_Page_Plugin::PAGE_LOCK_ENABLE);
        $lock->addOption($this->_model->_('Unlock'), Plugg_Page_Plugin::PAGE_LOCK_DISABLE);
        $lock->setValue(Plugg_Page_Plugin::PAGE_LOCK_INHERIT);

        // add some validators
        $this->setRequired('title', $this->_model->_('You must enter the title'), true, $this->_model->_(' '));
        $this->addFormRule(array($this, 'validateForm'));

        // following columns should not be changed via form
        $this->removeElements(array('userid', 'Parent'));

        $this->setElementLabel('lock', array($this->_model->_('Lock mode for this page')));
        $this->setElementLabel('nav', array($this->_model->_('Show navigation on this page and descendants')));
    }

    public function validateForm($values, $files)
    {
        if ($slug = $values['slug']) {
            if ($slug_value = mb_trim($slug, $this->_model->_(' '))) {
                if (!empty($this->_entityId)) {
                    $criteria = Sabai_Model_Criteria::createComposite();
                    $criteria->addAnd(Sabai_Model_Criteria::createValue('page_slug', $slug_value))
                        ->addAnd(Sabai_Model_Criteria::createValue('page_id', $this->_entityId, '!='));
                } else {
                    $criteria = Sabai_Model_Criteria::createValue('page_slug', $slug_value);
                }
                if (0 < $count = $this->_model->getRepository('Page')->countByCriteria($criteria)) {
                    return array('slug' => $this->_model->_('Page with the slug name already exists'));
                }
            }
        }
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
        $this->_entityId = $entity->getId();

        $this->_contentFilterId = $entity->content_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here

        $entity->content_html = $this->_contentFiltered;
        $entity->content_filter_id = $this->_contentFilterId;
    }

    public function getFilterableElementNames()
    {
        return array('content' => $this->_contentFilterId);
    }

    public function setFilteredValue($elementName, $filteredText, $filterId)
    {
        switch ($elementName) {
            case 'content':
                $this->_contentFiltered = $filteredText;
                $this->_contentFilterId = $filterId;
        }
    }
}