<?php
class Plugg_Message_Plugin extends Plugg_Plugin implements Plugg_User_Menu
{
    const MESSAGE_TYPE_INCOMING = 0;
    const MESSAGE_TYPE_OUTGOING = 1;

    public function onUserMainIdentityRoutes($routes)
    {
        $this->_onUserMainIdentityRoutes($routes, /*$private*/ true);
    }

    public function onPluggCron($lastrun)
    {
        // Allow run this cron 1 time per day at most
        if (!empty($lastrun) && time() - $lastrun < 86400) return;

        if (!$delete_days = intval($this->getParam('deleteOlderThanDays'))) return;

        $model = $this->getModel();

        // Remove messages if no star and older than X days
        $criteria = $model->createCriteria('Message')
            ->star_is(0)
            ->created_isSmallerThan($delete_days * 86400);

        $model->getGateway('Message')->deleteByCriteria($criteria);
    }

    public function onUserIdentityDeleteSuccess($identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();

        // Remove stat data if any
        $model->getGateway('Message')
            ->deleteByCriteria($model->createCriteria('Message')->userid_is($id));
    }

    public function onUserProfileButtons($user, $identity, $buttons)
    {
        if ($user->isAuthenticated() && $user->getId() != $identity->getId()) {
            $buttons[] = array(
                'url' => $this->_application->createUrl(array(
                             'base' => '/user',
                             'path' => '/' . $user->getId() . '/message/new',
                             'params' => array('to' => $identity->getUsername())
                         )),
                'text' => $this->_('Send message'),
                'icon' => $this->_application->getUrl()->getImageUrl($this->getLibrary(), 'message.gif')
            );
        }
    }

    /* Start implementation of Plugg_User_Menu */

    public function userMenuGetNames()
    {
        return array(
            'inbox' => array(
                'title' => '',
                'type' => Plugg_User_Plugin::MENU_TYPE_NONEDITABLE
            )
        );
    }

    public function userMenuGetNicename($menuName)
    {
        return $this->_('New messages');
    }

    public function userMenuGetLinkText($menuName, $menuTitle, Sabai_User $user)
    {
        $model = $this->getModel();
        $criteria = $model->createCriteria('Message')
            ->type_is(self::MESSAGE_TYPE_INCOMING)
            ->read_is(0);
        if ($count = $model->Message->countByUserAndCriteria($user->getId(), $criteria)) {
            return sprintf($this->_('Inbox (<strong>%d</strong>)'), $count);
        } else {
            return $this->_('Inbox');
        }
    }

    public function userMenuGetLinkUrl($menuName, Sabai_User $user)
    {
        return $this->_application->createUrl(array(
            'base' => '/user/' . $user->getId(),
            'path' => '/message'
        ));
    }
    /* End implementation of Plugg_User_Menu */
}