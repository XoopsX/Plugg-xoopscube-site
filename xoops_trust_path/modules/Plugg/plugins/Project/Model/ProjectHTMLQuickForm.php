<?php
class Plugg_Project_Model_ProjectHTMLQuickForm extends Plugg_Project_Model_Base_ProjectHTMLQuickForm implements Plugg_Filter_FilterableForm
{
    private $_customElementNames = array();
    private $_summaryFilterId;
    private $_summaryFiltered;

    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $this->removeElements(array('userid'));
        $this->setElementLabel('name', $this->_model->_('Project name'));

        if (!empty($params['elements'])) {
            $defaults = $data = $data_rules = array();
            foreach ($params['elements'] as $element_name => $element_def) {
                switch ($element_def['type']) {
                    case 'select':
                        $data[] = $this->createElement('select', $element_name, $element_def['label'], $element_def['options'], $element_def['attributes']);
                        break;
                    case 'select_multi':
                        $data[] = $this->createElement('select', $element_name, $element_def['label'], $element_def['options'], array_merge(array('multiple' => 'multiple', 'size' => count($element_def['options'])), $element_def['attributes']));
                        break;
                    case 'text':
                        $data[] = $this->createElement('text', $element_name, $element_def['label'], $element_def['attributes']);
                        break;
                    case 'url':
                        $data[] = $this->createElement('text', $element_name, $element_def['label'], $element_def['attributes']);
                        $data_rules[$element_name][] = array($this->_model->_('Invalid URL'), 'uri', null, 'client');
                        break;
                    case 'email':
                        $data[] = $this->createElement('text', $element_name, $element_def['label'], $element_def['attributes']);
                        $data_rules[$element_name][] = array($this->_model->_('Invalid mail address'), 'email', null, 'client');
                        break;
                    case 'textarea':
                        $data[] = $this->createElement('textarea', $element_name, $element_def['label'], $element_def['attributes']);
                        break;
                    case 'radio':
                        break;
                    case 'checkbox':
                    default:
                        continue;
                }
                if (isset($element_def['default'])) $defaults[$element_name] = $element_def['default'];
                if (!empty($element_def['attributes']['required'])) {
                    $data_rules[$element_name] = !isset($data_rules[$element_name]) ? array() : $data_rules[$element_name];
                    // Always prepend required rule
                    array_unshift($data_rules[$element_name], array(sprintf($this->_model->_('%s is required'), $element_def['label']), 'required', null, 'client'));
                }
                $this->_customElementNames[] = $element_name;
            }
            if (!empty($data)) {
                $this->addGroup($data, 'data', $this->_model->_('Project details'), '');
                $this->insertElementAfter($this->removeElement('data'), 'summary');
                if (!empty($data_rules)) $this->addGroupRule('data', $data_rules);
                $this->setDefaults(array('data' => $defaults));
            }
        }
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        if ($data = $entity->getData()) {
            $this->setDefaults(array('data' => $data));
        }

        $this->_summaryFilterId = $entity->summary_filter_id;
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        $entity->setData($this->getSubmitValue('data'));
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