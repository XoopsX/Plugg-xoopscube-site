<?php
class Plugg_XOOPSCubeUser_Plugin extends Plugg_Plugin implements Plugg_User_Manager_Application
{
    const FIELD_VIEWABLE = 1;
    const FIELD_EDITABLE = 2;
    const FIELD_REGISTERABLE = 4;

    private $_xoopsDB;
    private $_xoopsUrl;

    public function onUserManagerPluginOptions($options)
    {
        $options[$this->getName()] = $this->getNicename();
    }

    public function userLoginGetForm($action)
    {
        return false;
    }

    public function userLoginSubmitForm(Sabai_HTMLQuickForm $form)
    {
        $values = $form->getSubmitValues();
        $mb_whitespace = $this->_(' ');
        $username = mb_trim($values['username'], $mb_whitespace);
        $password = mb_trim($values['password'], $mb_whitespace);
        if (!empty($username) && !empty($password)) {
            $db = $this->getXoopsDB();
            $sql = sprintf('SELECT * FROM %susers WHERE uname = %s AND pass = %s', $db->getResourcePrefix(), $db->escapeString($username), $db->escapeString(md5($password)));
            if (($rs = $db->query($sql, 1, 0)) && ($row = $rs->fetchAssoc())) {
                require_once 'Sabai/User/Identity.php';
                return $this->_buildIdentity($row);
            }
        }
        $error = $this->_('Invalid username or password');
        $form->setElementError('username', $error);
        $form->setElementError('password', $error);
        $form->getElement('password')->updateAttributes(array('value' => ''));
        return false;
    }

    public function userLoginRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    public function userLogoutUser(Sabai_User_Identity $identity)
    {
        return true;
    }

    public function userRegisterInitForm(Sabai_HTMLQuickForm $form, $username = null, $email = null, $name = null)
    {
        require_once $this->_path . '/RegisterFormBuilder.php';
        $builder = new Plugg_XOOPSCubeUser_RegisterFormBuilder($this);
        $builder->buildForm($form);

        $defaults = array();
        if (isset($username)) $defaults['uname'] = $username;
        if (isset($email)) {
            $defaults['email'] = $defaults['email_confirm'] = $email;
        }
        if (isset($name)) $defaults['name'] = $name;
        $form->setDefaults($defaults);
    }

    public function userRegisterRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    public function userRegisterQueueForm(Plugg_User_Model_Queue $queue, Sabai_HTMLQuickForm $form)
    {
        $uname = $form->getSubmitValue('uname');
        if ($this->_isUnameRegistered($uname)) {
            $error = $this->_('The username is already registered.');
            $form->setElementError('uname', $error);
        }
        $email = $form->getSubmitValue('email');
        if ($this->_isEmailRegistered($email)) {
            $error = $this->_('The email address is already registered.');
            $form->setElementError('emails', array('email' => $error, 'email_confirm' => $error));
        }

        if (isset($error)) return false;

        $data = array('uname' => $uname, 'email' => $email, 'pass' => md5($form->getSubmitValue('pass')));
        foreach (array('name', 'url', 'user_avatar', 'user_icq', 'user_from', 'user_sig',
                       'user_viewemail', 'user_aim', 'user_yim', 'user_msnm', 'attachsig', 'rank', 'theme',
                       'timezone_offset', 'umode', 'uorder', 'notify_method', 'notify_mode', 'user_occ',
                       'bio', 'user_intrest', 'user_mailok') as $var_name) {
            if ($form->elementExists($var_name) || $form->isInGroup($var_name)) $data[$var_name] = $form->getSubmitValue($var_name);
        }
        $queue->setData($data);
        $queue->set('notify_email', $email);
        $queue->set('register_username', $uname);
        return true;
    }

    public function userRegisterSubmit(Plugg_User_Model_Queue $queue)
    {
        $db = $this->getXoopsDB();
        $values = $this->_toSqlData($db, $queue->getData());
        ksort($values);
        $columns = array_keys($values);
        sort($columns);
        $db->beginTransaction();
        $sql = sprintf('INSERT INTO %susers (%s) VALUES (%s)',
                       $db->getResourcePrefix(), implode(',', $columns), implode(',', $values));
        if ($db->exec($sql)) {
            $user_id = $db->lastInsertId($db->getResourcePrefix() . 'users', 'uid');
            $sql = sprintf('INSERT INTO %sgroups_users_link (groupid, uid) VALUES (%d, %d)',
                           $db->getResourcePrefix(), $this->getXoopsUsersGroupId(), $user_id);
            if ($db->exec($sql)) {
                $db->commit();
                $sql = sprintf('SELECT * FROM %susers WHERE uid = %d', $db->getResourcePrefix(), $user_id);
                if (($rs = $db->query($sql, 1, 0)) && ($row = $rs->fetchAssoc())) {
                    require_once 'Sabai/User/Identity.php';
                    return $this->_buildIdentity($row);
                }
            }
        }
        $db->rollback();
        return false;
    }

    public function userEditInitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form)
    {
        require_once $this->_path . '/EditFormBuilder.php';
        $builder = new Plugg_XOOPSCubeUser_EditFormBuilder($this);
        $builder->buildForm($form);

        // Retrieve current data for this identity
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT * FROM %susers WHERE uid = %d', $db->getResourcePrefix(), $identity->getId());
        if (($rs = $db->query($sql, 1, 0)) && $row = $rs->fetchAssoc()) {
            $form->setDefaults($row);
        }
    }

    public function userEditSubmitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form)
    {
        $db = $this->getXoopsDB();
        $data = $this->_toSqlData($db, $form->getSubmitValues());

        // Some values should never be edited here
        unset($data['uname'], $data['pass'], $data['email'], $data['user_avatar'], $data['user_regdate']);

        // Values returned by $form->getSubmitValues() come from $_POST!
        // Make sure the fields exist in form.
        foreach (array_keys($data) as $field) {
            if (!$form->elementExists($field) && !$form->isInGroup($field)) {
                unset($data[$field]);
            }
        }

        // Return current identity if no data to update
        if (empty($data)) return $identity;

        $sets = array();
        foreach (array_keys($data) as $column) {
            $sets[] = $column . '=' . $data[$column];
        }
        $sql = sprintf('UPDATE %susers SET %s WHERE uid = %d', $db->getResourcePrefix(), implode(',', $sets), $identity->getId());
        if (!$db->exec($sql)) {
            $ret = false; return $ret;
        }
        if (isset($data['name'])) $identity->setName($data['name']);
        if (isset($data['url'])) $identity->setUrl($data['url']);
        return $identity;
    }

    public function userEditRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    private function _toSqlData($db, $values)
    {
        $int_fields = array(
            'user_regdate' => time(), 'user_viewemail' => 0, 'attachsig' => 0,
            'rank' => 0, 'level' => 1, 'uorder' => 0, 'notify_method' => 1,
            'notify_mode' => 0, 'user_mailok' => 0
        );
        $string_fields = array(
            'name' => '', 'uname' => '', 'email' => '', 'url' => '', 'user_avatar' => 'blank.gif',
            'user_icq' => '', 'user_from' => '', 'user_sig' => '', 'user_aim' => '',
            'user_yim' => '', 'user_msnm' => '', 'pass' => '', 'theme' => '', 'umode' => '',
            'user_occ' => '', 'bio' => '', 'user_intrest' => ''
        );
        $float_fields = array('timezone_offset' => 0.0);
        $data= array();
        foreach ($int_fields as $field => $default) {
            $data[$field] = isset($values[$field]) ? intval($values[$field]) : $default;
        }
        foreach ($string_fields as $field => $default) {
            $data[$field] = isset($values[$field]) ? $db->escapeString($values[$field]) : $db->escapeString($default);
        }
        foreach ($float_fields as $field => $default) {
            $data[$field] = isset($values[$field]) ? floatval($values[$field]) : $default;
        }
        return $data;
    }

    public function userDeleteSubmit(Sabai_User_Identity $identity)
    {
        $db = $this->getXoopsDB();
        $db->beginTransaction();
        $sql = sprintf('DELETE FROM %susers WHERE uid = %d', $db->getResourcePrefix(), $identity->getId());
        if (!$db->exec($sql)) {
            $db->rollback();
            return false;
        }
        $sql = sprintf('DELETE FROM %sgroups_users_link WHERE uid = %d', $db->getResourcePrefix(), $identity->getId());
        if (!$db->exec($sql)) {
            $db->rollback();
            return false;
        }
        return $db->commit();
    }

    public function userRequestPasswordGetForm($action)
    {
        return false;
    }

    public function userRequestPasswordRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    public function userRequestPasswordQueueForm(Plugg_User_Model_Queue $queue, Sabai_HTMLQuickForm $form)
    {
        $email = $form->getSubmitValue('email');
        if (!$identity_id = $this->_isEmailRegistered($email)) {
            $form->setElementError('email', $this->_('The email address is not registered'));
            return false;
        }
        $queue->set('identity_id', $identity_id);
        $queue->set('notify_email', $email);
        return true;
    }

    public function userRequestPasswordSubmit(Plugg_User_Model_Queue $queue)
    {
        $db = $this->getXoopsDB();
        $new_password = substr(md5(uniqid(mt_rand(), true)), 5, 8);
        $sql = sprintf('UPDATE %susers SET pass = %s WHERE uid = %d', $db->getResourcePrefix(), $db->escapeString(md5($new_password)), $queue->get('identity_id'));
        return $db->exec($sql) ? $new_password : false;
    }

    public function userEditEmailGetForm(Sabai_User_Identity $identity, $action)
    {
        return false;
    }

    public function userEditEmailRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    public function userEditEmailQueueForm(Plugg_User_Model_Queue $queue, Sabai_HTMLQuickForm $form, Sabai_User_Identity $identity)
    {
        $email = $form->getSubmitValue('email');
        if ($identity_id = $this->_isEmailRegistered($email)) {
            if ($identity_id != $identity->getId()) {
                $form->setElementError('emails', $this->_('The email address is already registered by another user'));
                return false;
            }
        }
        $queue->set('notify_email', $email);
        return true;
    }

    public function userEditEmailSubmit(Plugg_User_Model_Queue $queue, Sabai_User_Identity $identity)
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('UPDATE %susers SET email = %s WHERE uid = %d', $db->getResourcePrefix(), $db->escapeString($queue->get('notify_email')), $queue->get('identity_id'));
        return $db->exec($sql);
    }

    public function userEditPasswordGetForm(Sabai_User_Identity $identity, $action)
    {
        return false;
    }

    public function userEditPasswordSubmitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form)
    {
        $db = $this->getXoopsDB();
        $new_password = $form->getSubmitValue('password');
        $sql = sprintf('UPDATE %susers SET pass = %s WHERE uid = %d', $db->getResourcePrefix(), $db->escapeString(md5($new_password)), $identity->getId());
        $ret = $db->exec($sql);
        return $ret;
    }

    public function userEditPasswordRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    public function userEditImageGetForm(Sabai_User_Identity $identity, $action)
    {
        require_once dirname(__FILE__) . '/EditImageForm.php';
        return new Plugg_XOOPSCubeUser_EditImageForm($this, $identity, $action);
    }

    public function userEditImageSubmitForm(Sabai_User_Identity $identity, Sabai_HTMLQuickForm $form)
    {
        return $form->submit($this, $identity);
    }

    public function userEditImageRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    public function userViewRenderIdentity(Sabai_User $user, Sabai_Template_PHP $template, Sabai_User_Identity $identity, $extraFields)
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT * FROM %susers WHERE uid = %d', $db->getResourcePrefix(), $identity->getId());
        $vars = array(
            'extra_fields' => $extraFields,
            'identity' => $identity,
        );
        if ($rs = $db->query($sql)) {
            $vars['fields'] = $rs->fetchAssoc();
        }
        return $template->render('plugg_xoopscubeuser_user_identity.tpl', $vars);
    }

    public function userFetchIdentitiesByIds($userIds)
    {
        $ret = array();
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT * FROM %susers WHERE uid IN (%s)', $db->getResourcePrefix(), implode(',', array_map('intval', $userIds)));
        if ($rs = $db->query($sql)) {
            while ($row = $rs->fetchAssoc()) {
                $ret[$row['uid']] = $this->_buildIdentity($row);
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

    private function _fetchIdentities($limit, $offset, $sort, $order)
    {
        $ret = array();
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT * FROM %susers ORDER BY %s %s', $db->getResourcePrefix(), $sort, $order);
        if ($rs = $db->query($sql, $limit, $offset)) {
            while ($row = $rs->fetchAssoc()) {
                $ret[] = $this->_buildIdentity($row);
            }
        }

        return $ret;
    }

    public function userFetchIdentityByUsername($userName)
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT * FROM %susers WHERE uname = %s', $db->getResourcePrefix(), $db->escapeString($userName));
        if (($rs = $db->query($sql, 1, 0)) &&
            $row = $rs->fetchAssoc()
        ) {
            return $this->_buildIdentity($row);
        }
        return false;
    }

    public function userFetchIdentityByEmail($email)
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT * FROM %susers WHERE email = %s', $db->getResourcePrefix(), $db->escapeString($email));
        if (($rs = $db->query($sql, 1, 0)) &&
            $row = $rs->fetchAssoc()
        ) {
            return $this->_buildIdentity($row);
        }
        return false;
    }

    public function userCountIdentities()
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT COUNT(*) FROM %susers', $db->getResourcePrefix());
        if ($rs = $this->_xoopsDB->query($sql)) {
            return $rs->fetchSingle();
        }
        return 0;
    }

    public function userGetIdentityPasswordById($userId)
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT pass FROM %susers WHERE uid = %d', $db->getResourcePrefix(), $userId);
        if ($rs = $this->_xoopsDB->query($sql)) {
            return $rs->fetchSingle();
        }
        return false;
    }

    public function userGetRoleIdsById($userId)
    {
        if (!$this->_application->isType(Plugg::XOOPSCUBE_LEGACY)) return array();

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

    private function _buildIdentity($rowData)
    {
        $identity = new Sabai_User_Identity($rowData['uid'], $rowData['uname']);
        $identity->setName($rowData['name']);
        $identity->setEmail($rowData['email']);
        $identity->setUrl($rowData['url']);
        $identity->setImage($this->getXoopsUrl() . '/uploads/' . $rowData['user_avatar']);
        $identity->setTimeCreated($rowData['user_regdate']);
        return $identity;
    }

    private function _getIdentityById($id)
    {
        if ($identities = $this->userFetchIdentitiesByIds(array($id))) {
            return $identities[$id];
        }
        return false;
    }

    private function _isUnameRegistered($uname)
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT uid FROM %susers WHERE uname = %s', $db->getResourcePrefix(), $db->escapeString($uname));
        if (($rs = $db->query($sql, 1, 0)) && ($row = $rs->fetchRow())) {
            return $row[0];
        }
        return false;
    }

    private function _isEmailRegistered($email)
    {
        $db = $this->getXoopsDB();
        $sql = sprintf('SELECT uid FROM %susers WHERE email = %s', $db->getResourcePrefix(), $db->escapeString($email));
        if (($rs = $db->query($sql, 1, 0)) && ($row = $rs->fetchRow())) {
            return $row[0];
        }
        return false;
    }

    function getXoopsDB()
    {
        if (!isset($this->_xoopsDB)) {
            if ($this->_application->isType(Plugg::XOOPSCUBE_LEGACY)) {
                $params = array('tablePrefix' => XOOPS_DB_PREFIX . '_');
            } else {
                $this->loadParams(); // Load params manually so that non-cacheable ones (db* params) become accessible
                $params = array(
                    'scheme' => $this->_params['dbScheme'],
                    'tablePrefix' => $this->_params['dbPrefix'] . '_',
                    'clientEncoding' => SABAI_CHARSET,
                    'options' => array(
                        'host' => $this->_params['dbHost'],
                        'dbname' => $this->_params['dbName'],
                        'user' => $this->_params['dbUser'],
                        'pass' => $this->_params['dbPass']
                    )
                );
            }
            $this->_xoopsDB = $this->_application->getLocator()->createService('DB', $params);
        }
        return $this->_xoopsDB;
    }

    function getXoopsUrl()
    {
        if (!isset($this->_xoopsUrl)) {
            if ($this->_application->isType(Plugg::XOOPSCUBE_LEGACY)) {
                $this->_xoopsUrl = XOOPS_URL;
            } else {
                $this->_xoopsUrl = $this->_params['xoopsUrl'];
            }
        }
        return $this->_xoopsUrl;
    }

    function getXoopsUsersGroupId()
    {
        return $this->_application->isType(Plugg::XOOPSCUBE_LEGACY) ? XOOPS_GROUP_USERS : $this->_params['usersGroupId'];
    }

    function getXoopsUploadsPath()
    {
        return $this->_application->isType(Plugg::XOOPSCUBE_LEGACY) ? XOOPS_ROOT_PATH . '/uploads' : false;
    }

    public function onUserLoginSuccess($user)
    {
        if (!$this->_application->isType(Plugg::XOOPSCUBE_LEGACY)) return;

        $db = $this->getXoopsDB();
        $sql = sprintf('UPDATE %susers SET last_login = %d WHERE uid = %d', $db->getResourcePrefix(), time(), $user->getId());
        $db->exec($sql);
    }
}
