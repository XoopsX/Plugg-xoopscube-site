<?php
class Plugg_User_Main_Identity_Friend_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $identity_id = $this->_application->identity->getId();
        $model = $context->plugin->getModel();

        $friends_pages = $model->Friend->paginateByUser($identity_id, 30);
        $friends_page = $friends_pages->getValidPage($context->request->getAsInt('friends_page', 1));
        $this->_application->setData(array(
            'can_manage' => $context->user->getId() == $identity_id ? true : $context->user->hasPermission('user friend manage any'),
            'friends' => $friends_page->getElements(),
            'friends_pages' => $friends_pages,
            'friends_page' => $friends_page,
        ));
    }
}