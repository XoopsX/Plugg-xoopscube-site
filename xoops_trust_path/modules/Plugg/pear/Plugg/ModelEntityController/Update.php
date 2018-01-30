<?php
require_once 'Plugg/ModelEntityController.php';

abstract class Plugg_ModelEntityController_Update extends Plugg_ModelEntityController
{
    /**
     * @var Sabai_Model_Entity
     */
    private $_entity;
    /**
     * @var string
     */
    protected $_entityIdKey;

    /**
     * Constructor
     *
     * @param string $entityName
     * @param string $entityIdKey
     * @param array $options
     * @return Plugg_ModelEntityController_Update
     */
    public function __construct($entityName, $entityIdKey, array $options = array())
    {
        parent::__construct($entityName, $options);
        $this->_entityIdKey = $entityIdKey;
    }

    protected function _init(Sabai_Application_Context $context)
    {
        if (0 >= $id = $context->request->getAsInt($this->_entityIdKey, 0)) {
            $context->response->setError('Invalid request', $this->_getErrorUrl());
            return false;
        }

        // retrieve from cache if exists so that the update time key is preserved
        $model = $context->plugin->getModel();
        if (!$this->_entity = $model->isCached($this->_entityName, $id)) {
            if (!$this->_entity = $model->getRepository($this->_entityName)->fetchById($id)) {
                $context->response->setError('Invalid request', $this->_getErrorUrl());
                return false;
            }
        }
        $model->cache($this->_entity);

        if (!$this->_onUpdateEntity($this->_entity, $context)) {
            $context->response->setError('Invalid request', $this->_getErrorUrl());
            return false;
        }

        return true;
    }

    protected function _getForm(Sabai_Application_Context $context)
    {
        return $this->_getEntityForm($this->_entity, $context);
    }

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        $this->_entity->applyForm($form);

        if (!$this->_onUpdateEntityCommit($this->_entity, $context, $form)) return false;

        if (!$context->plugin->getModel()->commit()) return false;

        $this->_onEntityUpdated($this->_entity, $context);
        $context->response->setSuccess(
            sprintf('%s updated successfully', $this->_entityName),
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
     * Callback method called just before updating the entity
     *
     * @return bool
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     * @access protected
     */
    protected function _onUpdateEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
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
     * @access protected
     */
    protected function _onUpdateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        return true;
    }

    /**
     * Callback method called just after the update of entity is commited to the datasource
     *
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     * @access protected
     */
    protected function _onEntityUpdated(Sabai_Model_Entity $entity, Sabai_Application_Context $context){}

    /**
     * Gets a form object for the target entity
     *
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     * @return Sabai_Model_EntityForm
     */
    abstract protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context);
}