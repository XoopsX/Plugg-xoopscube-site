<?php
class Plugg_User_Main_Identity_Friend extends Plugg_RoutingController
{
    public function __construct()
    {
        parent::__construct('Index', 'Plugg_User_Main_Identity_Friend_', dirname(__FILE__) . '/Friend');
    }

    protected function _isValidFriendRequest($context)
    {
        // Make sure the requested message exists and belongs to the user
        if ((!$friend = $this->_getRequestedFriend($context)) ||
            !$friend->isOwnedBy($this->_application->identity)
        ) {
            return false;
        }
        $this->_application->friend = $friend;

        return true;
    }

    protected function _isValidUserAccess(Sabai_Application_Context $context, $controller)
    {
        if (!$context->plugin->getParam('useFriendsFeature')) {
            return false;
        }

        // Check permission if trying to access other user's messages
        if ($this->_application->identity->getId() != $context->user->getId()) {
            if (!$context->user->hasPermission('user friend view any')) return false;

            if ($controller == 'ViewRequests' &&
                !$context->user->hasPermission('user friend manage any')
            ) {
                return false;
            }
        }

        return true;
    }

    protected function _getRoutes(Sabai_Application_Context $context)
    {
        $default_base = '/user/' . $this->_application->identity->getId() . '/friend';
        $this->_application->getUrl()->setRouteBase($default_base);
        $context->response->setDefaultSuccessUri(array('base' => $default_base))
            ->setDefaultErrorUri(array('base' => $default_base));

        return array(
            'requests' => array(
                'controller' => 'ViewRequests',
                'access_callback' => array('_isValidUserAccess'),
                'tab' => true,
                'tab_ajax' => true,
                'title' => $context->plugin->_('Friend requests')
            ),
            'request/:request_id' => array(
                'controller' => 'Request',
                'requirements' => array(':request_id' => '\d+'),
                'access_callback' => array('_isValidUserAccess'),
            ),
            ':friend_id/remove'   => array(
                'controller' => 'Remove',
                'requirements' => array(':friend_id' => '\d+'),
                'access_callback' => array('_isValidUserAccess', '_isValidFriendRequest'),
            ),
            ':friend_id/edit'   => array(
                'controller' => 'Edit',
                'requirements' => array(':friend_id' => '\d+'),
                'access_callback' => array('_isValidUserAccess', '_isValidFriendRequest'),
            ),
        );
    }

    protected function _getDefaultTabTitle($context)
    {
        return $context->plugin->_('Friends');
    }

    public function _getRequestedFriend(Sabai_Application_Context $context, $clearCache = false)
    {
        if ($id = $context->request->getAsInt('friend_id')) {
            if ($friend = $context->plugin->getModel()->Friend->fetchById($id, $clearCache)) {
                $friend->cache();
                return $friend;
            }
        }
        return false;
    }
}