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
 * @since      File available since Release 0.2.0
*/

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
 * @since      Class available since Release 0.2.0
 */
abstract class Sabai_Application_UserIdentityController_Paginate extends Sabai_Application_Controller
{
    /**
     * @var array
     */
    private $_options;
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
     * @param array $options
     * @return Sabai_Application_UserIdentityController_Paginate
     */
    public function __construct(array $options = array())
    {
        $default = array(
            'viewName' => null,
            'tplVarPages' => 'identity_pages',
            'tplVarSortKey' => 'identity_sort_key',
            'tplVarSortOrder' => 'identity_sort_order',
            'tplVarSort' => 'identity_sort',
            'tplVarPageRequested' => 'identity_page_requested',
            'tplVarIdentities' => 'identity_objects',
            'perpage' => 20,
        );
        $this->_options = array_merge($default, $options);
    }

    /**
     * Executes the action
     *
     * @param Sabai_Application_Context $context
     */
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Fetch sort key
        if ($sort_key_requested = $this->_getRequestedSort($context->request)) {
            $sort_key = $sort_key_requested;
        } else {
        	$sort_key = $this->_defaultSort;
        }

        // Fetch sort order
        if ($sort_order_requested = $this->_getRequestedOrder($context->request)) {
            $sort_order = $sort_order_requested;
        } else {
            $sort_order = $this->_defaultOrder;
        }

        $page_num = intval($this->_getRequestedPage($context->request));

        $identity_fetcher = $this->_getUserIdentityFetcher($context);
        $pages = $identity_fetcher->paginateIdentities(intval($this->_options['perpage']), $sort_key, $sort_order);
        $page = $pages->getValidPage($page_num);
        $identities = $this->_onPaginateIdentities($page->getElements(), $context);

        $this->_application->setData(array(
            $this->_options['tplVarPages'] => $pages,
            $this->_options['tplVarSortKey'] => $sort_key,
            $this->_options['tplVarSortOrder'] => $sort_order,
            $this->_options['tplVarSort'] => implode(',', array($sort_key, $sort_order)),
            $this->_options['tplVarPageRequested'] => $page_num,
            $this->_options['tplVarIdentities'] => $identities)
        );

        if (!empty($this->_options['viewName'])) {
            $context->response->popContentName();
            $context->response->pushContentName($this->_options['viewName']);
        }
    }

    protected function _getRequestedPage(Sabai_Request $request)
    {
        return $request->getAsInt('page', 1, null, 0);
    }

    protected function _getRequestedSort(Sabai_Request $request)
    {
        return $request->getAsStr('sort', 'id', array('id', 'name'));
    }

    protected function _getRequestedOrder(Sabai_Request $request)
    {
        return $request->getAsStr('order', 'ASC', array('ASC', 'DESC'));
    }

    /**
     * Callback method called just before viewing the list of identities
     *
     * @return ArrayObject
     * @param ArrayObject $identities
     * @param Sabai_Application_Context $context
     */
    protected function _onPaginateIdentities($identities, Sabai_Application_Context $context)
    {
        return $identities;
    }

    /**
     * Returns the user identity fetcher object
     *
     * @return Sabai_User_IdentityFetcher
     * @param Sabai_Application_Context
     */
    abstract protected function _getUserIdentityFetcher(Sabai_Application_Context $context);
}