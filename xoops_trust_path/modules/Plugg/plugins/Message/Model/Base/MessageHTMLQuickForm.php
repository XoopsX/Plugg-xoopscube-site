<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
pluginsy/Message/Model/MessageHTMLQuickForm.php
*/
abstract class Plugg_Message_Model_Base_MessageHTMLQuickForm extends Sabai_Model_EntityHTMLQuickForm
{
    public function onInit(array $params)
    {
        $this->addElement('text', 'title', $this->_model->_('Title'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('textarea', 'body', $this->_model->_('Body'), array('rows' => 10, 'cols' => 60));
        $this->addElement('textarea', 'body_html', $this->_model->_('Body html'), array('rows' => 10, 'cols' => 60));
        $this->addElement('text', 'userid', $this->_model->_('User ID'), array('size' => 30, 'maxlength' => 255));
        $this->_onInit($params);
    }

    public function onEntity(Sabai_Model_Entity $entity)
    {
        $defaults = array();
        foreach (array('title', 'body', 'body_html') as $key) {
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
        foreach (array('title' => 'title', 'body' => 'body', 'body_html' => 'body_html', 'userid' => 'userid') as $var_name => $form_name) {
            if ($this->elementExists($form_name) || $this->isInGroup($form_name)) {
                if ($this->getElementType($form_name) == 'static') continue;

                $value = $this->getSubmitValue($form_name);
                $vars[$var_name] = is_array($value) ? array_shift($value) : $value;
            }
        }
        $entity->setVars($vars);
        $this->_onFillEntity($entity);
    }

    abstract protected function _onInit(array $params);
    abstract protected function _onEntity(Sabai_Model_Entity $entity);
    abstract protected function _onFillEntity(Sabai_Model_Entity $entity);
}