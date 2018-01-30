<?php
require_once 'Plugg/ModelEntityController.php';

abstract class Plugg_ModelEntityController_Create extends Plugg_ModelEntityController
{
    /**
     * @var Sabai_Model_Entity
     */
    private $_entity;

    /**
     * Constructor
     *
     * @param string $entityName
     * @param array $options
     * @return Plugg_ModelEntityController_Create
     */
    public function __construct($entityName, array $options = array())
    {
        parent::__construct($entityName, array_merge(array(
            'autoAssignUser' => true
        ), $options));
    }

    protected function _init(Sabai_Application_Context $context)
    {
        $this->_entity = $context->plugin->getModel()->create($this->_entityName);

        if (!$this->_onCreateEntity($this->_entity, $context)) {
            $context->response->setError('Invalid request', $this->_getErrorUrl());
            return false;
        }

        $this->_submitPhrase = $context->plugin->_('Create');

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        return $this->_getEntityForm($this->_entity, $context);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_entity->applyForm($form);
        $this->_entity->markNew();

        if ($this->_getOption('autoAssignUser') &&
            is_callable(array($this->_entity, 'assignUser'))
        ) {
            $this->_entity->assignUser($context->user);
        }

        if (!$this->_onCreateEntityCommit($this->_entity, $context, $form)) return false;

        if (!$context->plugin->getModel()->commit()) return false;

        $this->_onEntityCreated($this->_entity, $context);
        $context->response->setSuccess(
            sprintf('%s created successfully', $this->_entityName),
            $this->_getSuccessUrl()
        );

        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_application->setData(array(
            'entity' => $this->_entity,
            'entity_name' => $this->_entityName,
            'entity_name_lc' => strtolower($this->_entityName),
        ));
        if ($view_name = $this->_getOption('viewName')) {
            $context->response->popContentName();
            $context->response->pushContentName($view_name);
        }
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
     * @param Sabai_HTMLQuickForm $form
     */
    protected function _onCreateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
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

    /**
     * Gets a form object for the target entity
     *
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     * @return Sabai_Model_EntityForm
     */
    abstract protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context);
}