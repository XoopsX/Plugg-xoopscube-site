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
abstract class Sabai_Application_ModelEntityController_Update extends Sabai_Application_ModelEntityController
{
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
     * @return Sabai_Application_ModelEntityController_Update
     */
    public function __construct($entityName, $entityIdKey, array $options = array())
    {
        parent::__construct($entityName, $options);
        $this->_entityIdKey = $entityIdKey;
    }

    /**
     * Executes the action
     *
     * @param Sabai_Controller_Context $context
     */
    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (0 >= $id = $context->request->getAsInt($this->_entityIdKey, 0)) {
            $context->response->setError('Invalid entity ID', $this->_getErrorUrl());
            return;
        }
        $model = $this->_getModel($context);
        // retrieve from cache if exists so that the update time key is preserved
        if (!$entity = $model->isCached($this->_entityName, $id)) {
            $repository = $model->getRepository($this->_entityName);
            if (!$entity = $repository->fetchById($id)) {
                $context->response->setError('Requested entity does not exist', $this->_getErrorUrl());
                return;
            }
            $model->cache($entity);
        }
        if (!$this->_onUpdateEntity($entity, $context)) {
            return;
        }
        $form = $this->_getEntityForm($entity, $context);
        $form->useToken(get_class($this));
        if ($form->validate()) {
            $entity->applyForm($form);
            if (!$this->_onUpdateEntityCommit($entity, $context)) {
                return;
            }
            if ($model->commit()) {
                $this->_onEntityUpdated($entity, $context);
                $context->response->setSuccess(sprintf('%s updated successfully', $this->_entityName), $this->_getSuccessUrl());
                return;
            }
        }
        $this->_application->setData(array(
            'entity_form'    => $form,
            'entity_id'      => $entity->getId(),
            'entity_name'    => $this->_entityName,
            'entity_name_lc' => strtolower($this->_entityName)
        ));
        if ($view_name = $this->_getOption('viewName')) {
            $context->response->popContentName();
            $context->response->pushContentName($view_name);
        }
    }

    /**
     * Gets an entity form object
     *
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     * @return Sabai_Model_EntityHTMLQuickForm
     */
    protected function _getEntityForm(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        return $entity->toHTMLQuickForm();
    }

    /**
     * Callback method called just before updating the entity
     *
     * @return bool
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
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
     */
    protected function _onUpdateEntityCommit(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        return true;
    }

    /**
     * Callback method called just after the update of entity is commited to the datasource
     *
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     */
    protected function _onEntityUpdated(Sabai_Model_Entity $entity, Sabai_Application_Context $context){}
}