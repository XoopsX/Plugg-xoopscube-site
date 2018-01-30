<?php
class Plugg_Project_Model_ReportHTMLQuickForm extends Plugg_Project_Model_Base_ReportHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_customElementNames = array();
    private $_commentFilterId;
    private $_commentFiltered;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)

        // remove user id form element by default
        $this->removeElements(array('userid', 'Release'));

        $type = $this->getElement('type');
        foreach ($this->_model->getPlugin()->getReportTypes() as $value => $label) {
            $type->addOption($label, $value);
        }

        $defaults = array();
        foreach ((array)@$params['elements'] as $element_name => $element_def) {
            switch ($element_def['type']) {
                case 'select':
                    $this->addElement('select', $element_name, $element_def['label'], $element_def['options'], $element_def['attributes']);
                    break;
                case 'text':
                    $this->addElement('text', $element_name, $element_def['label'], $element_def['attributes']);
                    break;
                case 'textarea':
                    $this->addElement('textarea', $element_name, $element_def['label'], $element_def['attributes']);
                    break;
                case 'radio':
                    break;
                case 'checkbox':
                default:
                    continue;
            }
            $this->_customElementNames[] = $element_name;
            if (isset($element_def['default'])) $defaults[$element_name] = $element_def['default'];
            if (!empty($element_def['attributes']['required'])) $this->setRequired($element_name, sprintf($this->_model->_('%s is required'), $element_def['label']));
        }
        $this->setDefaults($defaults);

        // move comment elements to the botoom
        $comments = $this->removeElements(array('comment', 'comment_html'));
        foreach (array_keys($comments) as $i) {
            $this->addElement($comments[$i]);
        }
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
        if ($data = $entity->getData()) {
            $this->setDefaults($data);
        }

        $this->_commentFilterId = $entity->comment_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
        foreach ($this->_customElementNames as $element_name) {
            $data[$element_name] = $this->getSubmitValue($element_name);
        }
        $entity->setData($data);

        $entity->comment_html = $this->_commentFiltered;
        $entity->comment_filter_id = $this->_commentFilterId;
    }

    public function getFilterableElementNames()
    {
        return array('comment' => $this->_commentFilterId);
    }

    public function setFilteredValue($elementName, $filteredText, $filterId)
    {
        switch ($elementName) {
            case 'comment':
                $this->_commentFiltered = $filteredText;
                $this->_commentFilterId = $filterId;
        }
    }
}