<?php
abstract class Plugg_Signature2_Model_Base_SignatureHTMLQuickForm extends Sabai_Model_EntityHTMLQuickForm
{
    public function onInit(array $params, Sabai_Gettext $gettext)
    {
        $this->addElement('static', 'text', $gettext->_('Text'));
        $this->addElement('static', 'text_filtered', $gettext->_('Text filtered'));
        $this->addElement('static', 'text_filter_id', $gettext->_('Text filter id'));
        $this->addElement('text', 'userid', $gettext->_('User ID'), array('size' => 30, 'maxlength' => 255));
        $this->_onInit($params, $gettext);
    }

    public function onEntity(Sabai_Model_Entity $entity)
    {
        $defaults = array();
        foreach (array('text', 'text_filtered', 'text_filter_id') as $key) {
            if ($this->elementExists($key) || $this->isInGroup($key)) {
                $defaults[$key] = $entity->getVar($key);
            }
        }
        if ($this->elementExists('userid')) {
            $defaults['userid'] = $entity->getVar('userid');
        }
        if (!empty($defaults)) $this->setDefaults($defaults);
        $this->_onEntity($entity);
    }

    public function onFillEntity(Sabai_Model_Entity $entity)
    {
        $vars = array();
        foreach (array('text' => 'text', 'text_filtered' => 'text_filtered', 'text_filter_id' => 'text_filter_id', 'userid' => 'userid') as $var_name => $form_name) {
            if ($this->elementExists($form_name) || $this->isInGroup($form_name)) {
                $value = $this->getSubmitValue($form_name);
                $vars[$var_name] = is_array($value) ? array_shift($value) : $value;
            }
        }
        $entity->setVars($vars);
        $this->_onFillEntity($entity);
    }

    abstract protected function _onInit(array $params, Sabai_Gettext $gettext);
    abstract protected function _onEntity(Sabai_Model_Entity $entity);
    abstract protected function _onFillEntity(Sabai_Model_Entity $entity);
}