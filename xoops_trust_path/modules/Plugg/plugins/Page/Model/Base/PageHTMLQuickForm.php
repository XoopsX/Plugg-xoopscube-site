<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
pluginsy/Page/Model/PageHTMLQuickForm.php
*/
abstract class Plugg_Page_Model_Base_PageHTMLQuickForm extends Sabai_Model_EntityHTMLQuickForm
{
    public function onInit(array $params)
    {
        $this->addElement('text', 'title', $this->_model->_('Title'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('text', 'slug', $this->_model->_('Slug'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('textarea', 'htmlhead', $this->_model->_('Htmlhead'), array('rows' => 10, 'cols' => 70));
        $this->addElement('textarea', 'content', $this->_model->_('Content'), array('rows' => 20, 'cols' => 70));
        $this->addElement('textarea', 'content_html', $this->_model->_('Content html'), array('rows' => 20, 'cols' => 70));
        $this->addElement('altselect', 'allow_edit', $this->_model->_('Allow edit'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('altselect', 'allow_comment', $this->_model->_('Allow comment'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('altselect', 'lock', $this->_model->_('Lock'), array());
        $this->addElement('altselect', 'nav', $this->_model->_('Nav'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('text', 'views', $this->_model->_('Views'), array('size' => 6, 'maxlength' => 255));
        $this->addElement('altselect', 'hidden', $this->_model->_('Hidden'), array(
            1 => $this->_model->_('Yes'),
            0 => $this->_model->_('No'),
        ))->setDelimiter('&nbsp;');
        $this->addElement('selectmodeltreeentity', $this->_model, 'Page', ' - ', 'Parent', $this->_model->_('Parent page'), null, array('size' => 1));
        $this->addElement('text', 'userid', $this->_model->_('User ID'), array('size' => 30, 'maxlength' => 255));
        $this->_onInit($params);
    }

    public function onEntity(Sabai_Model_Entity $entity)
    {
        $defaults = array();
        foreach (array('title', 'slug', 'htmlhead', 'content', 'content_html', 'allow_edit', 'allow_comment', 'lock', 'nav', 'views', 'hidden') as $key) {
            if ($this->elementExists($key) || $this->isInGroup($key)) {
                $defaults[$key] = $entity->getVar($key);
            }
        }
        if ($this->elementExists('Parent')) {
            $defaults['Parent'] = $entity->getVar('parent');
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
        foreach (array('title' => 'title', 'slug' => 'slug', 'htmlhead' => 'htmlhead', 'content' => 'content', 'content_html' => 'content_html', 'allow_edit' => 'allow_edit', 'allow_comment' => 'allow_comment', 'lock' => 'lock', 'nav' => 'nav', 'views' => 'views', 'hidden' => 'hidden', 'parent' => 'Parent', 'userid' => 'userid') as $var_name => $form_name) {
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