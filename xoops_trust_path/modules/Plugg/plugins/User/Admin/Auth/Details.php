<?php
require_once 'Sabai/Application/ModelEntityController/Read.php';

class Plugg_User_Admin_Auth_Details extends Sabai_Application_ModelEntityController_Read
{
    function __construct()
    {
        parent::__construct('Auth', 'auth_id', array('errorUrl' => array('base' => '/user/auth')));
    }

    function _onReadEntity($entity, Sabai_Application_Context $context)
    {
        $sort = 'lastused';
        $order = 'DESC';
        if (($sortby = explode(',', $context->request->getAsStr('sortby', ''))) && (count($sortby) == 2)) {
            list($sort, $order) = $sortby;
        }
        $pages = $entity->paginateAuthdatas(20, 'authdata_' . $sort, $order);
        $page_num = $context->request->getAsInt('page', 1, null, 0);
        $page = $pages->getValidPage($page_num);
        $this->_application->setData(array(
            'authdata_entities'       => $page->getElements()->with('User'),
            'authdata_sortby'         => "$sort,$order",
            'authdata_pages'          => $pages,
            'authdata_page_requested' => $page_num,
        ));
        $context->response->setPageInfo($this->_application->auth->name);

        return true;
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}