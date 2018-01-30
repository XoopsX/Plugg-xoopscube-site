<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
pluginsy/Project/Model/DeveloperHTMLQuickForm.php
*/
abstract class Plugg_Project_Model_Base_DeveloperHTMLQuickForm extends Sabai_Model_EntityHTMLQuickForm
{
    public function onInit(array $params)
    {
        $this->addElement('select', 'role', $this->_model->_('Role'), null, array('size' => 1));
        $this->addElement('text', 'tasks', $this->_model->_('Tasks'), array('size' => 80, 'maxlength' => 255));
        $this->addElement('selectmodelentity', $this->_model, 'Project', 'Project', $this->_model->_('project'), null, array('size' => 1));
        $this->addElement('text', 'userid', $this->_model->_('User ID'), array('size' => 30, 'maxlength' => 255));
        $this->_onInit($params);
    }

    public function onEntity(Sabai_Model_Entity $entity)
    {
        $defaults = array();
        foreach (array('role', 'tasks') as $key) {
            if ($this->elementExists($key) || $this->isInGroup($key)) {
                $defaults[$key] = $entity->getVar($key);
            }
        }
        if ($this->elementExists('Project')) {
            $defaults['Project'] = $entity->getVar('project_id');
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
        foreach (array('role' => 'role', 'tasks' => 'tasks', 'project_id' => 'Project', 'userid' => 'userid') as $var_name => $form_name) {
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