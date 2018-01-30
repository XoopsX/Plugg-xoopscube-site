<?php
class Plugg_XOOPSCubeUserAPI_Plugin extends Plugg_Plugin implements Plugg_User_Manager_API
{
    public function onUserManagerPluginOptions($options)
    {
        $options[$this->getName()] = $this->getNicename();
    }

    public function userLogin(Sabai_Application_Context $context, $returnTo)
    {
        $parsed = parse_url(XOOPS_URL);
        //$server = sprintf('%s://%s%s', $parsed['scheme'], $parsed['host'], isset($parsed['port']) ? ':' . $parsed['port'] : '');
        $server = isset($parsed['path']) ? str_replace($parsed['path'], '', XOOPS_URL) : XOOPS_URL;
        $url = XOOPS_URL . '/user.php?xoops_redirect=' . str_replace(array($server, '&', '/'), array('', urlencode('&'), urlencode('/')), $returnTo);
        header('Location: ' . $url);
        exit;
    }

    public function userLogout(Sabai_Application_Context $context)
    {
        header('Location: ' . XOOPS_URL . '/user.php?op=logout');
        exit;
    }

    public function userView(Sabai_Application_Context $context, Sabai_User_Identity $identity)
    {
        header('Location: ' . XOOPS_URL . '/userinfo.php?uid=' . $identity->getId());
        exit;
    }

    public function userRegister(Sabai_Application_Context $context)
    {
        header('Location: ' . XOOPS_URL . '/register.php');
        exit;
    }

    public function userEdit(Sabai_Application_Context $context, Sabai_User_Identity $identity)
    {
        if ($identity->getId() != $context->user->getId()) {
            // Cannot edit other user's profile in XC
            $context->response->setError($this->_('Invalid request'));
            $context->response->send($this->_application);
        }
        header('Location: ' . XOOPS_URL . '/edituser.php');
        exit;
    }

    public function userEditPassword(Sabai_Application_Context $context, Sabai_User_Identity $identity)
    {
        $this->userEdit($context, $identity);
    }

    public function userEditEmail(Sabai_Application_Context $context, Sabai_User_Identity $identity)
    {
        $this->userEdit($context, $identity);
    }

    public function userEditImage(Sabai_Application_Context $context, Sabai_User_Identity $identity)
    {
        if ($identity->getId() != $context->user->getId()) {
            // Cannot edit other user's profile in XC
            $context->response->setError($this->_('Invalid request'));
            $context->response->send($this->_application);
        }
        header('Location: ' . XOOPS_URL . '/edituser.php?op=avatarform&uid=' . $identity->getId());
        exit;
    }

    public function userDelete(Sabai_Application_Context $context, Sabai_User_Identity $identity)
    {
        if ($identity->getId() != $context->user->getId()) {
            // Cannot delete other user's profile in XC
            $context->response->setError($this->_('Invalid request'));
            $context->response->send($this->_application);
        }
        header('Location: ' . XOOPS_URL . '/user.php?op=delete');
        exit;
    }

    public function userRequestPassword(Sabai_Application_Context $context)
    {
        header('Location: ' . XOOPS_URL . '/lostpass.php');
        exit;
    }

    public function userFetchIdentitiesByIds($userIds)
    {
        $ret = array();
        $criteria = new Criteria('uid', '(' . implode(',', array_map('intval', $userIds)) . ')', 'IN');
        $xoops_users = xoops_gethandler('member')->getUsers($criteria, true);
        foreach ($userIds as $uid) {
            if (isset($xoops_users[$uid])) {
                $ret[$uid] = SabaiXOOPS::getUserIdentity($xoops_users[$uid]);
            } else {
                $ret[$uid] = SabaiXOOPS::getGuestIdentity();
            }
        }
        return $ret;
    }

    public function userFetchIdentitiesSortbyId($limit, $offset, $order)
    {
        return $this->_fetchIdentities($limit, $offset, 'uid', $order);
    }

    public function userFetchIdentitiesSortbyUsername($limit, $offset, $order)
    {
        return $this->_fetchIdentities($limit, $offset, 'uname', $order);
    }

    public function userFetchIdentitiesSortbyName($limit, $offset, $order)
    {
        return $this->_fetchIdentities($limit, $offset, 'name', $order);
    }

    public function userFetchIdentitiesSortbyEmail($limit, $offset, $order)
    {
        return $this->_fetchIdentities($limit, $offset, 'email', $order);
    }

    public function userFetchIdentitiesSortbyUrl($limit, $offset, $order)
    {
        return $this->_fetchIdentities($limit, $offset, 'url', $order);
    }

    public function userFetchIdentityByUsername($userName)
    {
        $criteria = new CriteriaCompo();
        $criteria->setLimit(1);
        $criteria->setStart(0);
        $xoops_users = xoops_gethandler('member')->getUsers($criteria, false);
        if (count($xoops_users) > 0) {
            return SabaiXOOPS::getUserIdentity($xoops_users[0]);
        }
        return SabaiXOOPS::getGuestIdentity();
    }

    public function userFetchIdentityByEmail($email)
    {
        if ($user = xoops_gethandler('member')->getUserByEmail($email)) {
            return SabaiXOOPS::getUserIdentity($user);
        }
        return SabaiXOOPS::getGuestIdentity();
    }

    private function _fetchIdentities($limit, $offset, $sort, $order)
    {
        $ret = array();
        $criteria = new CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($limit);
        $criteria->setStart($offset);
        $xoops_users = xoops_gethandler('member')->getUsers($criteria, false);
        foreach (array_keys($xoops_users) as $i) {
            $ret[] = SabaiXOOPS::getUserIdentity($xoops_users[$i]);
        }
        return $ret;
    }

    public function userCountIdentities()
    {
        return xoops_gethandler('member')->getUserCount();
    }

    public function userGetIdentityPasswordById($userId)
    {
        if (!$user = xoops_gethandler('member')->getUser($userId)) return false;

        return $user->getVar('pass');
    }

    public function userGetRoleIdsById($userId)
    {
        if (!$user = xoops_gethandler('member')->getUser($userId)) return array();

        $groups = $user->getGroups();
        $module_dir = $this->_application->getId();
        $module_id = xoops_gethandler('module')->getByDirname($module_dir)->getVar('mid');

        // Return as all roles (true) if belongs to the admin group or has the module admin permission
        if (in_array(XOOPS_GROUP_ADMIN, $groups) ||
            xoops_gethandler('groupperm')->checkRight('module_admin', $module_id, $groups)
        ) {
            return true;
        }

        return xoops_gethandler('groupperm')->getItemIds($module_dir . '_role', $groups, $module_id);
    }
}