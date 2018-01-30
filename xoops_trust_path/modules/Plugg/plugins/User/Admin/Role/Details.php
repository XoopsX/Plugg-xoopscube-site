<?php
require_once 'Sabai/Application/ModelEntityController/Read.php';

class Plugg_User_Admin_Role_Details extends Sabai_Application_ModelEntityController_Read
{
    function __construct()
    {
        parent::__construct('Role', 'role_id', array('errorUrl' => array('base' => '/user/role')));
    }

    function _onReadEntity($entity, Sabai_Application_Context $context)
    {
        $sort = 'userid';
        $order = 'ASC';
        if (($sortby = explode(',', $context->request->getAsStr('sortby', ''))) && (count($sortby) == 2)) {
            list($sort, $order) = $sortby;
        }
        $pages = $entity->paginateMembers(20, 'member_' . $sort, $order);
        $page_num = $context->request->getAsInt('page', 1, null, 0);
        $page = $pages->getValidPage($page_num);
        require dirname(__FILE__) . '/permissions.php';
        $this->_application->setData(array(
            'member_entities'       => $page->getElements()->with('User'),
            'member_sortby'         => "$sort,$order",
            'member_pages'          => $pages,
            'member_page_requested' => $page_num,
            'permissions'           => $permissions,
        ));
        $context->response->setPageInfo($this->_application->role->name);

        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}