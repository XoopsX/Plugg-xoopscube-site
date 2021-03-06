<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
pluginsy/Xigg/Model/TrackbackHTMLQuickForm.php
*/
abstract class Plugg_Xigg_Model_Base_TrackbackHTMLQuickForm extends Sabai_Model_EntityHTMLQuickForm
{
    public function onInit(array $params)
    {
        $this->addElement('text', 'title', $this->_model->_('Title'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('textarea', 'excerpt', $this->_model->_('Excerpt'), array('rows' => 10, 'cols' => 60));
        $this->addElement('text', 'url', $this->_model->_('Url'), array('size' => 80, 'maxlength' => 255));
        $this->addRule('url', $this->_model->_('Invalid URI'), 'uri', null, 'client');
        $this->addElement('text', 'blog_name', $this->_model->_('Blog name'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('selectmodelentity', $this->_model, 'Node', 'Node', $this->_model->_('node'), null, array('size' => 1));
        $this->_onInit($params);
    }

    public function onEntity(Sabai_Model_Entity $entity)
    {
        $defaults = array();
        foreach (array('title', 'excerpt', 'url', 'blog_name') as $key) {
            if ($this->elementExists($key) || $this->isInGroup($key)) {
                $defaults[$key] = $entity->getVar($key);
            }
        }
        if ($this->elementExists('Node')) {
            $defaults['Node'] = $entity->getVar('node_id');
        }
        if (!empty($defaults)) $this->setDefaults($defaults);
        $this->_onEntity($entity);
    }

    public function onFillEntity(Sabai_Model_Entity $entity)
    {
        $vars = array();
        foreach (array('title' => 'title', 'excerpt' => 'excerpt', 'url' => 'url', 'blog_name' => 'blog_name', 'node_id' => 'Node') as $var_name => $form_name) {
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