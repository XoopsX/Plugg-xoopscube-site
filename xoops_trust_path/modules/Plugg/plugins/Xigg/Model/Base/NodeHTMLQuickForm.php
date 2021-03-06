<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
pluginsy/Xigg/Model/NodeHTMLQuickForm.php
*/
abstract class Plugg_Xigg_Model_Base_NodeHTMLQuickForm extends Sabai_Model_EntityHTMLQuickForm
{
    public function onInit(array $params)
    {
        $this->addElement('text', 'title', $this->_model->_('Title'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('text', 'source', $this->_model->_('Source'), array('size' => 80, 'maxlength' => 255));
        $this->addRule('source', $this->_model->_('Invalid URI'), 'uri', null, 'client');
        $this->addElement('text', 'source_title', $this->_model->_('Source title'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('textarea', 'body', $this->_model->_('Body'), array('rows' => 20, 'cols' => 70));
        $this->addElement('textarea', 'teaser', $this->_model->_('Teaser'), array('rows' => 10, 'cols' => 70));
        $this->addElement('altselect', 'allow_comments', $this->_model->_('Allow comments'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('altselect', 'allow_trackbacks', $this->_model->_('Allow trackbacks'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('altselect', 'allow_edit', $this->_model->_('Allow edit'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('altselect', 'hidden', $this->_model->_('Hidden'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('text', 'priority', $this->_model->_('Priority'), array('size' => 6, 'maxlength' => 255));
        $this->addElement('text', 'views', $this->_model->_('Views'), array('size' => 6, 'maxlength' => 255));
        $this->addElement('selectmodeltreeentity', $this->_model, 'Category', ' - ', 'Category', $this->_model->_('category'), null, array('size' => 1));
        $this->addElement('selectmodelentity', $this->_model, 'Tag', 'Tags', $this->_model->_('Tag'), null, array('size' => 10, 'multiple' => 'multiple'));
        $this->addElement('text', 'userid', $this->_model->_('User ID'), array('size' => 30, 'maxlength' => 255));
        $this->_onInit($params);
    }

    public function onEntity(Sabai_Model_Entity $entity)
    {
        $defaults = array();
        foreach (array('title', 'source', 'source_title', 'body', 'teaser', 'allow_comments', 'allow_trackbacks', 'allow_edit', 'hidden', 'priority', 'views') as $key) {
            if ($this->elementExists($key) || $this->isInGroup($key)) {
                $defaults[$key] = $entity->getVar($key);
            }
        }
        if ($this->elementExists('Category')) {
            $defaults['Category'] = $entity->getVar('category_id');
        }
        if ($this->elementExists('Tags')) {
            $defaults['Tags'] = $entity->get('Tags')->getAllIds();
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
        foreach (array('title' => 'title', 'source' => 'source', 'source_title' => 'source_title', 'body' => 'body', 'teaser' => 'teaser', 'allow_comments' => 'allow_comments', 'allow_trackbacks' => 'allow_trackbacks', 'allow_edit' => 'allow_edit', 'hidden' => 'hidden', 'priority' => 'priority', 'views' => 'views', 'category_id' => 'Category', 'userid' => 'userid') as $var_name => $form_name) {
            if ($this->elementExists($form_name) || $this->isInGroup($form_name)) {
                if ($this->getElementType($form_name) == 'static') continue;

                $value = $this->getSubmitValue($form_name);
                $vars[$var_name] = is_array($value) ? array_shift($value) : $value;
            }
        }
        $entity->setVars($vars);
        if ($this->elementExists('Tags')) {
            $entity->set('Tags', (array)$this->getElementValue('Tags'));
        } elseif ($group = $this->isInGroup('Tags')) {
            $value = $this->getElementValue($group);
            $entity->set('Tags', (array)$value['Tags']);
        }
        $this->_onFillEntity($entity);
    }

    abstract protected function _onInit(array $params);
    abstract protected function _onEntity(Sabai_Model_Entity $entity);
    abstract protected function _onFillEntity(Sabai_Model_Entity $entity);
}