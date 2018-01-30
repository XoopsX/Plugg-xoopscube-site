<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Application
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.8
*/

require_once 'Sabai/Application/ModelEntityController.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Application
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.8
 */
abstract class Sabai_Application_ModelEntityController_Create extends Sabai_Application_ModelEntityController
{
    /**
     * Constructor
     *
     * @param string $entityName
     * @param array $options
     * @return Sabai_Application_ModelEntityController_Create
     */
    public function __construct($entityName, array $options = array())
    {
        $options = array_merge(array('autoAssignUser' => true), $options);
        parent::__construct($entityName, $options);
    }

    /**
     * Executes the action
     *
     * @param Sabai_Application_Context $context
     */
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $model = $this->_getModel($context);
        $entity = $model->create($this->_entityName);
        if (!$this->_onCreateEntity($entity, $context)) {
            return;
        }
        $form = $this->_getEntityForm($entity, $context);
        $form->useToken(get_class($this));
        if ($form->validate()) {
            $entity->applyForm($form);
            if ($this->_getOption('autoAssignUser') && is_callable(array($entity, 'assignUser'))) {
                $entity->assignUser($context->user);
            }
            $entity->markNew();
            if (!$this->_onCreateEntityCommit($entity, $context)) {
                return;
            }
            if ($model->commit()) {
                $this->_onEntityCreated($entity, $context);
                $context->response->setSuccess(sprintf('%s created successfully', $this->_entityName), $this->_getSuccessUrl());
                return;
            }
        }
        $this->_application->setData(array(
            'entity_form'    => $form,
            'entity_name'    => $this->_entityName,
            'entity_name_lc' => strtolower($this->_entityName),
        ));
        if ($view_name = $this->_getOption('viewName')) {
            $context->response->popContentName();
            $context->response->pushContentName($view_name);
        }
    }

    /**
     * Gets a entity form object
     *
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     * @return Sabai_Model_EntityForm
     */
    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        return $entity->toHTMLQuickForm();
    }

    /**
     * Callback method called just before creating the entity
     *
     * @return bool
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Application_Context $context
     */
    protected function _onCreateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        return true;
    }

    /**
     * Callback method called just before commit
     *
     * @return bool
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     */
    protected function _onCreateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        return true;
    }

    /**
     * Callback method called just after the creation of entity is commited to the datasource
     *
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     */
    protected function _onEntityCreated(Sabai_Model_Entity $entity, Sabai_Application_Context $context){}
}