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
abstract class Sabai_Application_ModelEntityController_Paginate extends Sabai_Application_ModelEntityController
{
	/**
     * @var string
     */
	protected $_defaultSort = 'id';
	/**
     * @var string
     */
	protected $_defaultOrder = 'ASC';

    /**
     * Constructor
     *
     * @param string $entityName
     * @param array $options
     * @return Sabai_Application_ModelEntityController_Paginate
     */
    public function __construct($entityName, array $options = array())
    {
        $default = array(
            'viewName' => null,
            'tplVarPages' => 'entity_pages',
            'tplVarSortKey' => 'entity_sort_key',
            'tplVarSortOrder' => 'entity_sort_order',
            'tplVarSort' => 'entity_sort',
            'tplVarPageRequested' => 'entity_page_requested',
            'tplVarName' => 'entity_name',
            'tplVarNameLC' => 'entity_name_lc',
            'tplVarNamePlural' => 'entity_name_plural',
            'tplVarNamePluralLC' => 'entity_name_plural_lc',
            'tplVarEntities' => 'entity_objects',
            'perpage' => 10,
        );
        parent::__construct($entityName, array_merge($default, $options));
    }

    /**
     * Executes the action
     *
     * @param Sabai_Controller_Context $context
     */
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $repository = $this->_getModel($context)->getRepository($this->_entityName);
        $page_num = intval($this->_getRequestedPage($context->request));
        $perpage = $this->_getOption('perpage');

        // Fetch sort key
        $sort_keys = array($this->_defaultSort);
        if ($sort_key_requested = $this->_getRequestedSort($context->request)) {
        	if ($this->_defaultSort != $sort_key_requested) {
        		$sort_keys = array($sort_key_requested, $this->_defaultSort);
        	}
        }
        $sort_key = array();
        foreach ($sort_keys as $_sort_key) {
            $sort_key[] = $this->_entityNameLc . '_' . $_sort_key;
        }

        // Fetch sort order
        $sort_order = array($this->_defaultOrder);
        if ($sort_order_requested = $this->_getRequestedOrder($context->request)) {
            $sort_order = array($sort_order_requested, $this->_defaultOrder);
        }

        // Fetch entities
        if ($criteria = $this->_getCriteria($context)) {
            $pages = $repository->paginateByCriteria($criteria, $perpage, $sort_key, $sort_order);
        } else {
            $pages = $repository->paginate($perpage, $sort_key, $sort_order);
        }
        $entities = $pages->getValidPage($page_num)->getElements();

        $this->_application->setData(array(
            $this->_getOption('tplVarPages') => $pages,
            $this->_getOption('tplVarSortKey') => $sort_keys[0],
            $this->_getOption('tplVarSortOrder') => $sort_order[0],
            $this->_getOption('tplVarSort') => implode(',', array($sort_keys[0], $sort_order[0])),
            $this->_getOption('tplVarPageRequested') => $page_num,
            $this->_getOption('tplVarName') => $this->_entityName,
            $this->_getOption('tplVarNameLC') => $this->_entityNameLc,
            $this->_getOption('tplVarNamePlural') => pluralize($this->_entityName),
            $this->_getOption('tplVarNamePluralLC') => strtolower(pluralize($this->_entityName)),
            $this->_getOption('tplVarEntities') => $this->_onPaginateEntities($entities, $context)
        ));

        if ($view_name = $this->_getOption('viewName')) {
            $context->response->popContentName();
            $context->response->pushContentName($view_name);
        }
    }

    protected function _getRequestedPage(Sabai_Request $request)
    {
        return $request->getAsInt('page', 1, null, 0);
    }

    protected function _getRequestedSort(Sabai_Request $request)
    {
        return $request->getAsStr('sort', '');
    }

    protected function _getRequestedOrder(Sabai_Request $request)
    {
        return $request->getAsStr('order', 'ASC', array('ASC', 'DESC'));
    }

    protected function _getCriteria(Sabai_Application_Context $context)
    {
        return false;
    }

    /**
     * Callback method called just before viewing the entity page
     *
     * @return Sabai_Model_EntityCollection
     * @param Sabai_Model_EntityCollection_Rowset $entities
     * @param Sabai_Controller_Context $context
     */
    protected function _onPaginateEntities($entities, Sabai_Application_Context $context)
    {
        return $entities;
    }
}