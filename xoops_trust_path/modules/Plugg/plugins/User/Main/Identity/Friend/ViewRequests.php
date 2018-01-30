<?php
class Plugg_User_Main_Identity_Friend_ViewRequests extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $identity_id = $this->_application->identity->getId();
        $model = $context->plugin->getModel();

        $requests_pending_pages = $model->Friendrequest
            ->criteria()
            ->status_is(Plugg_User_Plugin::FRIENDREQUEST_STATUS_PENDING)
            ->paginateByUser($identity_id, 10, 'request_created', 'DESC');
        $requests_pending_page = $requests_pending_pages->getValidPage($context->request->getAsInt('request_pending_page', 1));

        $requests_accepted_pages = $model->Friendrequest
            ->criteria()
            ->status_is(Plugg_User_Plugin::FRIENDREQUEST_STATUS_ACCEPTED)
            ->paginateByUser($identity_id, 10, 'request_updated', 'DESC');
        $requests_accepted_page = $requests_accepted_pages->getValidPage($context->request->getAsInt('request_accepted_page', 1));

        $requests_rejected_pages = $model->Friendrequest
            ->criteria()
            ->status_is(Plugg_User_Plugin::FRIENDREQUEST_STATUS_REJECTED)
            ->paginateByUser($identity_id, 10, 'request_updated', 'DESC');
        $requests_rejected_page = $requests_rejected_pages->getValidPage($context->request->getAsInt('request_rejected_page', 1));

        $requests_received_pages = $model->Friendrequest
            ->criteria()
            ->status_is(Plugg_User_Plugin::FRIENDREQUEST_STATUS_PENDING)
            ->to_is($identity_id)
            ->paginate(10, 'request_created', 'DESC');
        $requests_received_page = $requests_received_pages->getValidPage($context->request->getAsInt('request_received_page', 1));

        $this->_application->setData(array(
            'requests_pending_pages' => $requests_pending_pages,
            'requests_pending_page' => $requests_pending_page,
            'requests_pending' => $requests_pending_page->getElements(),
            'requests_accepted_pages' => $requests_accepted_pages,
            'requests_accepted_page' => $requests_accepted_page,
            'requests_accepted' => $requests_accepted_page->getElements(),
            'requests_rejected_pages' => $requests_rejected_pages,
            'requests_rejected_page' => $requests_rejected_page,
            'requests_rejected' => $requests_rejected_page->getElements(),
            'requests_received_pages' => $requests_received_pages,
            'requests_received_page' => $requests_received_page,
            'requests_received' => $requests_received_page->getElements(),
        ));
    }
}