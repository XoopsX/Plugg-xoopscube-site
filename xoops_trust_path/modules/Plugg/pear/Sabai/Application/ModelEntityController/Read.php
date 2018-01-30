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
abstract class Sabai_Application_ModelEntityController_Read extends Sabai_Application_ModelEntityController
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
     * @return Sabai_Application_ModelEntityController_Read
     */
    public function __construct($entityName, $entityIdKey, array $options = array())
    {
        $optins = array_merge(array('autoAssignUser' => true), $options);
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
        $entity_r = $this->_getModel($context)->getRepository($this->_entityName);
        if (!$entity = $entity_r->fetchById($id)) {
            $context->response->setError('Invalid entity ID', $this->_getErrorUrl());
            return;
        }
        if (!$this->_onReadEntity($entity, $context)) {
            return;
        }
        $this->_application->setData(array(
            'entity'         => $entity,
            'entity_id'      => $entity->getId(),
            'entity_name'    => $this->_entityName,
            'entity_name_lc' => strtolower($this->_entityName),
        ));
        if ($view_name = $this->_getOption('viewName')) {
            $context->response->popContentName();
            $context->response->pushContentName($view_name);
        }
    }

    /**
     * Callback method called just before viewing the entity
     *
     * @return bool
     * @param Sabai_Model_Entity $entity
     * @param Sabai_Controller_Context $context
     */
    protected function _onReadEntity(Sabai_Model_Entity $entity, Sabai_Application_Context $context)
    {
        return true;
    }
}