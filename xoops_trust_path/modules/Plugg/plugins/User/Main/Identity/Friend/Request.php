<?php
class Plugg_User_Main_Identity_Friend_Request extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('View', 'Plugg_User_Main_Identity_Friend_Request_', dirname(__FILE__) . '/Request');
        $this->addFilters(array('_isValidFriendRequestRequested'));
    }

    protected function _isValidFriendRequestRequestedBeforeFilter(Sabai_Application_Context $context)
    {
        // Make sure the requested request exists
        if (!$request = $this->_getRequestedFriendRequest($context)) {
            $context->response->setError($context->plugin->_('Invalid request'))
                ->send($this->_application);
        }

        $this->_application->friendrequest = $request;
    }

    protected function _isValidFriendRequestRequestedAfterFilter(Sabai_Application_Context $context){}

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        return array(
            'accept'   => array(
                'controller' => 'Accept',
            ),
            'reject'   => array(
                'controller' => 'Reject',
            ),
            'cancel'   => array(
                'controller' => 'Cancel',
            ),
            'confirm'   => array(
                'controller' => 'Confirm',
            ),
        );
    }

    private function _getRequestedFriendRequest(Sabai_Application_Context $context, $clearCache = false)
    {
        if ($id = $context->request->getAsInt('request_id')) {
            if ($entity = $context->plugin->getModel()->Friendrequest->fetchById($id, $clearCache)) {
                $entity->cache();
                return $entity;
            }
        }
        return false;
    }

    function clearMenuInSession(Sabai_Application_Context $context)
    {
        $this->_application->getPlugin('user')->clearMenuInSession($context->plugin->getName(), 'friendrequest');
    }
}