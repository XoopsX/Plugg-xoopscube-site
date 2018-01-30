<?php
class Plugg_User_Plugin extends Plugg_Plugin implements Plugg_Widget_Widget, Plugg_User_Widget, Plugg_User_Menu
{
    const FIELD_TYPE_REGISTERABLE = 1;
    const FIELD_TYPE_EDITABLE = 2;
    const FIELD_TYPE_VIEWABLE = 4;
    const FIELD_TYPE_ALL = 7;
    const FIELD_TYPE_REGISTERABLE_REQUIRED = 9;
    const FIELD_TYPE_EDITABLE_REQUIRED = 18;
    const FIELD_TYPE_VIEWABLE_REQUIRED = 36;
    const FIELD_TYPE_ALL_REQUIRED = 63;
    const FIELD_VIEWER_CONFIGURABLE = 64;
    const FIELD_CONFIGURABLE = 64; // For compat, deprecated

    const WIDGET_TYPE_PUBLIC = 1;
    const WIDGET_TYPE_PRIVATE = 2;
    const WIDGET_POSITION_LEFT = 1;
    const WIDGET_POSITION_RIGHT = 2;

    const MENU_TYPE_EDITABLE = 1;
    const MENU_TYPE_NONEDITABLE = 2;

    const AUTOLOGIN_COOKIE = 'plugg_user_autologin';

    const QUEUE_TYPE_REGISTER = 0;
    const QUEUE_TYPE_REGISTERAUTH = 1;
    const QUEUE_TYPE_EDITEMAIL = 2;
    const QUEUE_TYPE_REQUESTPASSWORD = 3;

    const FRIENDREQUEST_STATUS_PENDING = 0;
    const FRIENDREQUEST_STATUS_REJECTED = 1;
    const FRIENDREQUEST_STATUS_ACCEPTED = 2;
    const FRIENDREQUEST_STATUS_CONFIRMED = 3;

    private $_userPermissions = array();

    public function hasCurrentUser()
    {
        if ($this->getParam('enableAutologin')) {
            if (isset($_COOKIE[self::AUTOLOGIN_COOKIE])) {
                $manager = $this->getManagerPlugin();
                if ($manager instanceof Plugg_User_Manager_Application &&
                    ($cookie = explode(':', $_COOKIE[self::AUTOLOGIN_COOKIE])) &&
                    ($user_id = $cookie[0]) &&
                    ($password = $cookie[1]) &&
                    ($session = $this->_getAutologinSession($user_id)) &&
                    $session->expires > time() &&
                    ($identity = $session->User) &&
                    sha1($session->salt . $manager->userGetIdentityPasswordById($identity->getId())) == $password
                ) {
                    $session->last_ip = getip();
                    $session->last_ua = $_SERVER['HTTP_USER_AGENT'];
                    $session->commit();
                    $user = new Sabai_User($identity, true, $this->_application->getId());
                    $user->startSession();
                    return $user;
                }
                // Invalidate cookie
                $this->_setAutologinCookie('', time() - 3600);
            }
        }
        return false;
    }

    private function _getAutologinSession($userId)
    {
        return $this->getModel()->Autologin->criteria()->userid_is($userId)->fetch(1, 0)->getNext();
    }

    public function createAutologinSession($user)
    {
        $manager = $this->getManagerPlugin();
        if (!$manager instanceof Plugg_User_Manager_Application) return false;

        $user_id = $user->getId();
        if (!$session = $this->_getAutologinSession($user_id)) {
            $session = $this->getModel()->create('Autologin');
            $session->salt = md5(uniqid(mt_rand(), true));
            $session->assignUser($user);
            $session->markNew();
        } else {
            if ($this->getParam('limitSingleAutologinSession')) {
                // Update salt so that only the requested PC holds a valid autologin cookie
                $session->salt = md5(uniqid(mt_rand(), true));
            }
        }
        $expires = time() + 86400 * intval($this->getParam('autologinSessionLifetime'));
        $session->expires = $expires;
        $session->last_ip = getip();
        $session->last_ua = $_SERVER['HTTP_USER_AGENT'];
        if (!$session->commit()) {
            return false;
        }
        $password = sha1($session->salt . $manager->userGetIdentityPasswordById($user->getId()));
        $this->_setAutologinCookie("$user_id:$password", $expires);
        return true;
    }

    private function _setAutologinCookie($value, $expires)
    {
        $path = '/';
        if ($parsed = parse_url($this->_application->getConfig('siteUrl'))) {
            $path = $parsed['path'];
        }
        setcookie(self::AUTOLOGIN_COOKIE, $value, $expires, $path, '', false, true);
    }

    public function getManagerPlugin()
    {
        if (!$manager_name = $this->getParam('userManagerPlugin')) {
            throw new Plugg_Exception('User manager plugin is not defined.');
        }

        return $this->_application->getPlugin($manager_name);
    }

    public function onPluggInit()
    {
        // Use alternate user account plugin?
        if ($plugin_name = $this->getParam('userManagerPlugin')) {
            if ($plugin_handle = $this->_application->getPluginManager()->getPluginHandle($plugin_name)) {
                $db = $this->getDB();
                $this->_application->getLocator()->addProviderClass(
                    'UserIdentityFetcher',
                    array('pluginHandle' => $plugin_handle, 'DB' => $db),
                    'Plugg_User_IdentityFetcher',
                    $this->_path . '/IdentityFetcher.php'
                );
            }
        }
    }

    public function onPluggRun($controller)
    {
        require $this->_path . '/Filter.php';
        require_once 'Sabai/Handle/Instance.php';
        $controller->prependFilter(new Sabai_Handle_Instance(new Plugg_User_Filter()));
    }

    public function onPluggMainRoutes($routes)
    {
        $this->_onPluggMainRoutes($routes, true);
    }

    public function onPluggAdminRoutes($routes)
    {
        $this->_onPluggAdminRoutes($routes);
    }

    public function onUserPluginConfigured($pluginEntity, $originalParams)
    {
        $params = $pluginEntity->getParams();
        if ($params['userManagerPlugin'] !== $originalParams['userManagerPlugin']) {
            $this->_application->dispatchEvent(
                'UserManagerPluginChanged',
                array($originalParams['userManagerPlugin'], $params['userManagerPlugin'])
            );
        }
    }

    public function onUserFieldInstalled($pluginEntity, $plugin)
    {
        if ($fields = $plugin->userFieldGetNames()) {
            $model = $this->getModel();
            $this->_createPluginUserFields($model->create('Field'), $pluginEntity->name, $fields);
            $model->commit();
            $this->clearAllCache();
        }
    }

    public function onUserFieldUninstalled($pluginEntity, $plugin)
    {
        $this->_deletePluginUserFeature($pluginEntity->name, 'Field');
        $this->clearAllCache();
    }

    public function onUserFieldUpgraded($pluginEntity, $plugin)
    {
        if (!$fields = $plugin->userFieldGetNames()) {
            $this->_deletePluginUserFeature($pluginEntity->name, 'Field');
        } else {
            $model = $this->getModel();
            $fields_already_installed = array();
            foreach ($model->Field->criteria()->plugin_is($pluginEntity->name)->fetch() as $current_field) {
                if (in_array($current_field->name, $fields)) {
                    $fields_already_installed[] = $current_field->name;
                    $current_field->type = $fields[$current_field->name]['type']; // Always update the field type
                } else {
                    $current_field->markRemoved();
                }
            }
            $this->_createPluginUserFields(
                $model->create('Field'), // pass in a prototype
                $pluginEntity->name,
                array_diff($fields, $fields_already_installed)
            );
            $model->commit();
        }

        $this->clearAllCache();
    }

    public function onUserAuthenticatorInstalled($pluginEntity, $plugin)
    {
        if ($auths = $plugin->userAuthGetName()) {
            $this->_createPluginUserAuths($pluginEntity->name, $auths);
            $this->clearAllCache();
        }
    }

    public function onUserAuthenticatorUninstalled($pluginEntity, $plugin)
    {
        $this->_deletePluginUserFeature($pluginEntity->name, 'Auth');
        $this->clearAllCache();
    }

    public function onUserAuthenticatorUpgraded($pluginEntity, $plugin)
    {
        if (!$auths = $plugin->userAuthGetName()) {
            $this->_deletePluginUserFeature($pluginEntity->name, 'Auth');
        } else {
            $auths_already_installed = array();
            foreach ($this->getModel()->Auth->criteria()->plugin_is($pluginEntity->name)->fetch() as $current_auth) {
                if (in_array($current_auth->name, $auths)) {
                    $auths_already_installed[] = $current_auth->name;
                } else {
                    $current_auth->markRemoved();
                }
            }
            $this->_createPluginUserAuths($pluginEntity->name, array_diff($auths, $auths_already_installed));
        }

        $this->clearAllCache();
    }

    public function onUserWidgetInstalled($pluginEntity, $plugin)
    {
        if ($widgets = $plugin->userWidgetGetNames()) {
            $model = $this->getModel();
            $this->_createPluginUserWidgets($model->create('Widget'), $pluginEntity->name, $widgets);
            $model->commit();
            $this->clearAllCache();
        }
    }

    public function onUserWidgetUninstalled($pluginEntity, $plugin)
    {
        $this->_deletePluginUserFeature($pluginEntity->name, 'Widget');
        $this->clearAllCache();
    }

    public function onUserWidgetUpgraded($pluginEntity, $plugin)
    {
        if (!$widgets = $plugin->userWidgetGetNames()) {
            $this->_deletePluginUserFeature($pluginEntity->name, 'Widget');
        } else {
            $model = $this->getModel();
            $widgets_already_installed = array();
            foreach ($model->Widget->criteria()->plugin_is($pluginEntity->name)->fetch() as $current_widget) {
                if (in_array($current_widget->name, $widgets)) {
                    $widgets_already_installed[] = $current_widget->name;
                    if ($type = @$widgets[$current_widget->name]) {
                        $current_widget->type = $type; // Update the widget type if configured explicitly
                    }
                } else {
                    $current_widget->markRemoved();
                }
            }
            $this->_createPluginUserWidgets(
                $model->create('Widget'),
                $pluginEntity->name,
                array_diff($widgets, $widgets_already_installed)
            );
            $model->commit();
        }

        $this->clearAllCache();
    }

    public function onUserMenuInstalled($pluginEntity, $plugin)
    {
        if ($menus = $plugin->userMenuGetNames()) {
            $model = $this->getModel();
            $this->_createPluginUserMenus($model->create('Menu'), $pluginEntity->name, $menus);
            $model->commit();
            $this->clearAllCache();
        }
    }

    public function onUserMenuUninstalled($pluginEntity, $plugin)
    {
        $this->_deletePluginUserFeature($pluginEntity->name, 'Menu');
        $this->clearAllCache();
    }

    public function onUserMenuUpgraded($pluginEntity, $plugin)
    {
        if (!$menus = $plugin->userMenuGetNames()) {
            $this->_deletePluginUserFeature($pluginEntity->name, 'Menu');
        } else {
            $model = $this->getModel();
            $menus_already_installed = array();
            foreach ($model->Menu->criteria()->plugin_is($pluginEntity->name)->fetch() as $current_menu) {
                if (in_array($current_menu->name, $menus)) {
                    $menus_already_installed[] = $current_menu->name;
                    if ($type = @$menus[$current_menu->name]['type']) {
                        $current_menu->type = $type; // Update the menu type if configured explicitly
                    }
                } else {
                    $current_menu->markRemoved();
                }
            }
            $this->_createPluginUserMenus(
                $model->create('Menu'), // pass in the prototype
                $pluginEntity->name,
                array_diff($menus, $menus_already_installed)
            );
            $model->commit();
        }

        $this->clearAllCache();
    }

    private function _clearRolePermissionsCache()
    {
        $this->getCache()->remove('permissions');
    }

    public function clearMenuDataCache()
    {
        $this->getCache()->remove('menu_data');
    }

    public function clearAllCache()
    {
        $this->getCache()->clean();
    }

    private function _createPluginUserFields($prototype, $pluginName, $fields)
    {
        foreach ($fields as $field_name => $field_meta) {
            if (empty($field_name)) continue;
            $field = clone $prototype;
            $field->name = $field_name;
            $field->title = $field_meta['title'];
            $field->type = $field_meta['type'];

            if ($field->isType(self::FIELD_TYPE_EDITABLE)) {
                $field->editable = 1;
            }
            if ($field->isType(self::FIELD_TYPE_VIEWABLE)) {
                $field->viewable = 1;
            }
            if ($field->isType(self::FIELD_VIEWER_CONFIGURABLE)) {
                $field->configurable = 1;
            }
            $field->plugin = $pluginName;
            $field->active = 1;
            $field->markNew();
        }
    }

    /**
     * Plugins that are installed prior to the User plugin can call this method upon
     * the UserPluginInstalled event to manually register user fields.
     *
     * @param string $pluginName
     * @param array $fields
     * @return bool
     */
    public function createPluginUserFields($pluginName, $fields)
    {
        $model = $this->getModel();
        $this->_createPluginUserFields($model->create('Field'), $pluginName, $fields);
        return $model->commit();
    }

    private function _createPluginUserAuths($pluginName, $auths)
    {
        $model = $this->getModel();
        foreach ($auths as $auth_name => $auth_title) {
            if (empty($auth_name)) continue;
            $auth = $model->create('Auth');
            $auth->name = $auth_name;
            $auth->title = $auth_title;
            $auth->plugin = $pluginName;
            $auth->active = 1;
            $auth->markNew();
        }
        return $model->commit();
    }

    /**
     * Plugins that are installed prior to the User plugin can call this method upon
     * the UserPluginInstalled event to manually register user widgets.
     *
     * @param string $pluginName
     * @param array $widgets
     * @return bool
     */
    public function createPluginUserWidgets($pluginName, $widgets)
    {
        $model = $this->getModel();
        $this->_createPluginUserWidgets($model->create('Widget'), $pluginName, $widgets);
        return $model->commit();
    }

    private function _createPluginUserWidgets($prototype, $pluginName, $widgets)
    {
        foreach ($widgets as $widget_name => $widget_type) {
            if (empty($widget_name)) continue;
            $widget = clone $prototype;
            $widget->name = $widget_name;
            $widget->plugin = $pluginName;
            if (!$widget_type & self::WIDGET_TYPE_PRIVATE) {
                // Make sure the widget is of type public
                $widget_type = $widget_type | self::WIDGET_TYPE_PUBLIC;
            }
            $widget->type = $widget_type;

            $widget->markNew();
        }
    }

    private function _createPluginUserMenus($prototype, $pluginName, $menus)
    {
        foreach ($menus as $menu_name => $menu_meta) {
            $menu = clone $prototype;
            $menu->name = $menu_name;
            $menu->plugin = $pluginName;
            $menu->active = 1;
            $menu->title = isset($menu_meta['title']) ? $menu_meta['title'] : '';
            $menu->type = isset($menu_meta['type']) ? $menu_meta['type'] : self::MENU_TYPE_EDITABLE;
            $menu->markNew();
        }
    }

    private function _deletePluginUserFeature($pluginName, $featureName)
    {
        $model = $this->getModel();
        foreach ($model->$featureName->criteria()->plugin_is($pluginName)->fetch() as $entity) {
            $entity->markRemoved();
        }
        return $model->commit();
    }

    public function onUserAdminRolePermissions($permissions)
    {
        $this->_onUserAdminRolePermissions($permissions, array(
            'user profile view any' => $this->_("View other user's profile"),
            'user profile edit any' => $this->_("Edit other user's profile"),
            'user profile delete own' => $this->_('Delete own user profile'),
            'user profile delete any' => $this->_("Delete other user's profile"),
            'user email edit own' => $this->_('Edit own user email'),
            'user email edit any' => $this->_("Edit other user's email"),
            'user image edit own' => $this->_('Edit own user image'),
            'user image edit any' => $this->_("Edit other user's image"),
            'user widget view any private' => $this->_("View other user's private widget contents"),
            'user friend manage any' => $this->_("Manage other user's friends data"),
        ));
        if (!empty($this->_param['allowViewAnyUser'])) unset($permissions[$this->_library]['user profile view any']);
    }

    public function onUserAdminRolePermissionsDefault($permissions)
    {
        $permissions = array_merge($permissions, array(
            'user email edit own',
            'user image edit own',
            'user profile view any'
        ));
    }

    public function widgetGetNames()
    {
        return array('account', 'login');
    }

    public function widgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'account':
                return $this->_('Your Account');
            case 'login':
                return $this->_('Login');
        }
    }

    public function widgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'account':
                return $this->_('Displays user account menu.');
            case 'login':
                return $this->_('Displays user login form.');
        }
    }

    public function widgetGetSettings($widgetName)
    {
        return array();
    }

    public function widgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template)
    {
        switch ($widgetName) {
            case 'account':
                if ($user->isAuthenticated()) {
                    return $this->_renderAccountWidget($user, $template);
                }
                break;
            case 'login':
                if (!$user->isAuthenticated()) {
                    return $template->render('plugg_user_widget_login.tpl', array());
                }
                break;
        }
    }

    public function _renderAccountWidget($user, $template)
    {
        $menu_data = $this->_getMenuData();
        $menus = array();
        foreach ($menu_data as $_menu) {
            $plugin_name = $_menu['plugin'];
            $menu_name = $_menu['name'];
            if (!isset($_SESSION['Plugg_User_Plugin'][$plugin_name][$menu_name])) {
                if (($plugin = $this->_application->getPlugin($plugin_name)) &&
                    ($link_text = $plugin->userMenuGetLinkText($menu_name, $_menu['title'], $user))
                ) {
                    $_SESSION['Plugg_User_Plugin'][$plugin_name][$menu_name] = array(
                        'text' => $link_text,
                        'url' => $plugin->userMenuGetLinkUrl($menu_name, $user),
                        'plugin' => $plugin->getNicename()
                    );
                } else {
                    $_SESSION['Plugg_User_Plugin'][$plugin_name][$menu_name] = false;
                }
            }
            $menus[] = $_SESSION['Plugg_User_Plugin'][$plugin_name][$menu_name];
        }
        return $template->render('plugg_user_widget_account.tpl', array('menus' => $menus));
    }

    public function clearMenuInSession($pluginName, $menuName)
    {
        unset($_SESSION['Plugg_User_Plugin'][$pluginName][$menuName]);
    }

    public function onUserLoginSuccess($user)
    {
        $stat = $this->_getStatByIdentity($user->getIdentity());
        $stat->last_login = time();
        $stat->commit();
    }

    public function onUserLogoutSuccess($user)
    {
        // Invalidate autologin cookie
        $this->_setAutologinCookie('', time() - 3600);
    }

    public function onUserRegisterSuccess($identity)
    {
        // Init stat data for the user
        $stat = $this->_getStatByIdentity($identity);
        foreach (array('last_edit', 'last_edit_email', 'last_edit_password', 'last_edit_image') as $stat_type) {
            $stat->$stat_type = time();
        }
        $stat->commit();

        // Send user registration complete email to site admin
        $replacements = array(
            '{SITE_NAME}' => $site_name = $this->_application->getConfig('siteName'),
            '{SITE_URL}' => $this->_application->getConfig('siteUrl'),
            '{USER_NAME}' => $identity->getUsername(),
            '{USER_EMAIL}'=> $identity->getEmail(),
            '{USER_LINK}' => $this->_application->createUrl(array('base' => '/user', 'path' => '/' . $identity->getId())),
        );
        $subject = sprintf($this->_('New user registration at %s'), $site_name);
        $body = strtr($this->getParam('registerCompleteEmail'), $replacements);

        $this->_application->getPlugin('mail')
            ->getSender()
            ->mailSend($this->_application->getConfig('siteEmail'), $subject, $body);
    }

    public function onUserIdentityEditSuccess($identity)
    {
        $stat = $this->_getStatByIdentity($identity);
        $stat->last_edit = time();
        $stat->commit();
    }

    public function onUserIdentityEditEmailSuccess($identity)
    {
        $stat = $this->_getStatByIdentity($identity);
        $stat->last_edit_email = time();
        $stat->commit();
    }

    public function onUserIdentityEditPasswordSuccess($identity)
    {
        $stat = $this->_getStatByIdentity($identity);
        $stat->last_edit_password = time();
        $stat->commit();
    }

    public function onUserIdentityEditImageSuccess($identity)
    {
        $stat = $this->_getStatByIdentity($identity);
        $stat->last_edit_image = time();
        $stat->commit();
    }

    private function _getStatByIdentity(&$identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();
        if (!$stat = $model->Stat->fetchByUser($id)->getNext()) {
            $stat = $model->create('Stat');
            $stat->userid = $id;
            $stat->markNew();
        }
        return $stat;
    }

    public function onUserIdentityDeleteSuccess($identity)
    {
        $id = $identity->getId();
        $model = $this->getModel();
        $model->getGateway('Stat')->deleteByCriteria($model->createCriteria('Stat')->userid_is($id));
        $model->getGateway('Extra')->deleteByCriteria($model->createCriteria('Extra')->userid_is($id));
        $model->getGateway('Authdata')->deleteByCriteria($model->createCriteria('Authdata')->userid_is($id));
        $model->getGateway('Autologin')->deleteByCriteria($model->createCriteria('Autologin')->userid_is($id));
        $model->getGateway('Queue')->deleteByCriteria($model->createCriteria('Queue')->identity_id_is($id));
        $model->getGateway('Friendrequest')->deleteByCriteria($model->createCriteria('Freindrequest')->userid_is($id));
        $model->getGateway('Friend')->deleteByCriteria($model->createCriteria('Friend')->userid_is($id)->or_()->with_is($id));
    }

    public function userWidgetGetNames()
    {
        return array(
            'autologin' => self::WIDGET_TYPE_PRIVATE,
            'friends' => self::WIDGET_TYPE_PUBLIC
        );
    }

    public function userWidgetGetTitle($widgetName)
    {
        switch ($widgetName) {
            case 'autologin':
                return $this->_('AutoLogin');
            case 'friends':
                return $this->_('Friends');
        }
    }

    public function userWidgetGetSummary($widgetName)
    {
        switch ($widgetName) {
            case 'autologin':
                return $this->_('Displays a list of autologin sessions activated by the user.');
            case 'friends':
                return $this->_("Displays a list of the user's friends.");
        }
    }

    public function userWidgetGetSettings($widgetName)
    {
        switch ($widgetName) {
            case 'autologin':
                return array();
            case 'friends':
                return array(
                    'limit' => array(
                        'type'     => 'radio',
                        'label'    => $this->_('Number of friends to display'),
                        'default'  => 10,
                        'options'  => array(1 => 1, 3 => 3, 5 => 5, 7 => 7, 10 => 10, 15 => 15, 20 => 20),
                        'delimiter' => '&nbsp;'
                    ),
                );
        }
    }

    public function userWidgetGetContent($widgetName, $widgetSettings, Sabai_User $user, Sabai_Template_PHP $template, Sabai_User_Identity $identity)
    {
        switch ($widgetName) {
            case 'autologin':
                return $this->_renderAutologinUserWidget($widgetSettings, $user, $template, $identity);
            case 'friends':
                if ($this->getParam('useFriendsFeature')) {
                    return $this->_renderFriendsUserWidget($widgetSettings, $user, $template, $identity);
                }
                break;
        }
    }

    private function _renderAutologinUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $autologins = $this->getModel()->Autologin->criteria()->userid_is($id)->fetch();
        if ($autologins->count()) {
            return $template->render('plugg_user_user_widget_autologin.tpl', array(
                'identity' => $identity,
                'autologins' => $autologins,
            ));
        }
    }

    private function _renderFriendsUserWidget($widgetSettings, $user, $template, $identity)
    {
        $id = $identity->getId();
        $friends = $this->getModel()->Friend
            ->fetchByUser($id, $widgetSettings['limit'], 0, 'friend_created', 'DESC');
        if ($count = $friends->count()) {
            return $template->render('plugg_user_user_widget_friends.tpl', array(
                'is_owner' => $is_owner = $user->getId() == $id,
                'identity' => $identity,
                'can_manage' => $can_manage =  $is_owner ? true : $user->hasPermission('user friend manage any'),
                'friends' => $friends,
                'friends_count' => $count,
            ));
        }
    }

    public function onPluggCron($lastrun)
    {
        // Allow run this cron 1 time per day at most
        if (!empty($lastrun) && time() - $lastrun < 86400) return;

        $model = $this->getModel();

        // Remove expired autologin sessions
        $criteria = $model->createCriteria('Autologin')
            ->expires_isSmallerThan(time());
        $model->getGateway('Autologin')->deleteByCriteria($criteria);

        // Remove queues older than 3 days
        $criteria = $model->createCriteria('Queue')
            ->created_isSmallerThan(time() - 259200);
        $model->getGateway('Queue')->deleteByCriteria($criteria);

        // Remove confirmed friend requests
        $criteria = $model->createCriteria('Friendrequest')
            ->status_is(self::FRIENDREQUEST_STATUS_CONFIRMED);
        $model->getGateway('Friendrequest')->deleteByCriteria($criteria);

        // Remove unconfirmed but rejected/accepted friend requests that are more than 100 days old
        $criteria = $model->createCriteria('Friendrequest')
            ->status_is(self::FRIENDREQUEST_STATUS_ACCEPTED)
            ->created_isSmallerThan(time() - 8640000);
        $model->getGateway('Friendrequest')->deleteByCriteria($criteria);
        $criteria = $model->createCriteria('Friendrequest')
            ->status_is(self::FRIENDREQUEST_STATUS_REJECTED)
            ->created_isSmallerThan(time() - 8640000);
        $model->getGateway('Friendrequest')->deleteByCriteria($criteria);
    }

    private function _getMenuData()
    {
        $ret = array();
        $cache = $this->getCache();
        if (false === $data = $cache->get('menu_data')) {
            $menus = $this->getModel()->Menu->criteria()->active_is(1)->fetch(0, 0, 'menu_order', 'ASC');
            foreach ($menus as $menu) {
                $ret[] = array(
                    'plugin' => $menu->plugin,
                    'name' => $menu->name,
                    'title' => $menu->title,
                );
            }
            $cache->save(serialize($ret), 'menu_data');
        } else {
            $ret = unserialize($data);
        }
        return $ret;
    }

    public function getRelationships($from, $to)
    {
        $friend = $this->getModel()->Friend
            ->criteria()
            ->with_is(is_object($to) ? $to->getId() : $to)
            ->fetchByUser(is_object($from) ? $from->getId() : $from, 1, 0)
            ->getNext();
        return $friend ? $friend->getRelationships() : array();
    }

    public function onUserProfileButtons($user, $identity, $buttons)
    {
        if ($this->getParam('useFriendsFeature')
            && $user->isAuthenticated()
            && $user->getId() != $identity->getId()
        ) {
            $buttons[] = array(
                'url' => $this->_application->createUrl(array(
                             'base' => '/user',
                             'path' => '/request_friend',
                             'params' => array('to' => $identity->getId())
                         )),
                'text' => $this->_('Add as friend'),
                'icon' => $this->_application->getUrl()->getImageUrl($this->_library, 'friend_add.gif')
            );
        }
    }

    public function userMenuGetNames()
    {
        return array(
            'friendrequest' => array(
                'title' => '',
                'type' => Plugg_User_Plugin::MENU_TYPE_NONEDITABLE
            )
        );
    }

    public function userMenuGetNicename($menuName)
    {
        switch ($menuName) {
            case 'friendrequest':
                return $this->_('New friend requests');
        }
    }

    public function userMenuGetLinkText($menuName, $menuTitle, Sabai_User $user)
    {
        switch ($menuName) {
            case 'friendrequest':
                if (!$this->getParam('useFriendsFeature')) return;

                $count = $this->getModel()->Friendrequest
                    ->criteria()
                    ->to_is($user->getId())
                    ->status_is(self::FRIENDREQUEST_STATUS_PENDING)
                    ->count();
                if ($count == 0) return;

                return sprintf($this->_('Friend requests (<strong>%d</strong>)'), $count);
        }
    }

    public function userMenuGetLinkUrl($menuName, Sabai_User $user)
    {
        switch ($menuName) {
            case 'friendrequest':
                if ($this->getParam('useFriendsFeature')) {
                    return $this->_application->createUrl(array(
                        'base' => '/user',
                        'path' => '/' . $user->getId() . '/friend')
                    );
                }
                break;
        }
    }

    public function getXFNMetaDataList($categorize = true)
    {
        $list = array(
            'Friendship' => array(
                $this->_('contact'),
                $this->_('acquaintance'),
                $this->_('friend')
            ),
            'Physical' => array($this->_('met')),
            'Professional' => array(
                $this->_('co-worker'),
                $this->_('colleague')
            ),
            'Geographical' => array(
                $this->_('co-resident'),
                $this->_('neighbor')
            ),
            'Family' => array(
                $this->_('child'),
                $this->_('parent'),
                $this->_('sibling'),
                $this->_('spouse'),
                $this->_('kin')
            ),
            'Romantic' => array(
                $this->_('muse'),
                $this->_('crush'),
                $this->_('date'),
                $this->_('sweetheart')
            ),
            'Identity' => array($this->_('me'))
        );
        if ($categorize) {
            return $list;
        }
        $ret = array();
        foreach ($list as $k => $v) {
            $ret = array_merge($ret, $v);
        }
        return $ret;
    }

    public function sendRegisterConfirmEmail($queue, $manager, $confirmByAdmin = false)
    {
        $confirm_link = $this->_application->createUrl(array(
            'base' => '/user',
            'path' => '/confirm/' . $queue->getId(),
            'params' => array('key' => $queue->get('key')),
            'separator' => '&'
        ));
        $confirm_recipient = $queue->get('notify_email');
        $data = $queue->getData();
        $replacements = array(
            '{SITE_NAME}' => $this->_application->getConfig('siteName'),
            '{SITE_URL}' => $this->_application->getConfig('siteUrl'),
            '{USER_NAME}' => $queue->get('register_username'),
            '{USER_EMAIL}' => $confirm_recipient,
            '{CONFIRM_LINK}' => $confirm_link,
            '{IP}' => getip()
        );
        $subject = sprintf($this->_('User activation key for %s'), $queue->get('register_username'));
        if ($confirmByAdmin) {
            // Send confirmation mail to admin
            $body_template = $this->getParam('registerConfirmByAdminEmail');
            $to = $this->_application->getConfig('siteEmail');
        } else {
            $body_template = $this->getParam('registerConfirmEmail');
            $to = $confirm_recipient;
        }
        $body = strtr($body_template, $replacements);

        return $this->_application->getPlugin('mail')
            ->getSender()
            ->mailSend($to, $subject, $body);
    }

    public function sendEditEmailConfirmEmail($queue, $manager)
    {
        $identity = $this->_application->getService('UserIdentityFetcher')
            ->fetchUserIdentity($queue->get('identity_id'));
        if ($identity->isAnonymous()) {
            return false;
        }

        $confirm_link = $this->_application->createUrl(array(
            'base' => '/user',
            'path' => '/confirm/' . $queue->getId(),
            'params' => array('key' => $queue->get('key')),
            'separator' => '&'
        ));
        $confirm_email = $queue->get('notify_email');
        $replacements = array(
            '{SITE_NAME}' => $this->_application->getConfig('siteName'),
            '{SITE_URL}' => $this->_application->getConfig('siteUrl'),
            '{USER_NAME}' => $identity->getUsername(),
            '{USER_EMAIL}' => $confirm_email,
            '{CONFIRM_LINK}' => $confirm_link
        );
        $subject = sprintf($this->_('User activation key for %s'), $identity->getUsername());
        $body = strtr($this->getParam('editEmailConfirmEmail'), $replacements);

        return $this->_application->getPlugin('mail')
            ->getSender()
            ->mailSend($confirm_email, $subject, $body);
    }

    public function sendRequestPasswordConfirmEmail($queue, $identity, $manager)
    {
        $confirm_link = $this->_application->createUrl(array(
            'base' => '/user',
            'path' => '/confirm/' . $queue->getId(),
            'params' => array('key' => $queue->get('key')),
            'separator' => '&'
        ));
        $replacements = array(
            '{SITE_NAME}' => $site_name = $this->_application->getConfig('siteName'),
            '{SITE_URL}' => $this->_application->getConfig('siteUrl'),
            '{USER_NAME}' => $identity->getUsername(),
            '{USER_EMAIL}'=> $identity->getEmail(),
            '{CONFIRM_LINK}' => $confirm_link,
            '{IP}' => getip()
        );
        $subject = sprintf($this->_('New password request at %s'), $site_name);
        $body = strtr($this->getParam('newPasswordConfirmEmail'), $replacements);

        return $this->_application->getPlugin('mail')
            ->getSender()
            ->mailSend($identity->getEmail(), $subject, $body);
    }

    public function createAuthdata($authData, $userId)
    {
        $auth_data = $this->getModel()->create('Authdata');
        $auth_data->claimed_id = $authData['claimed_id'];
        $auth_data->display_id = $authData['display_id'];
        //$auth_data->type = $authData['type'];
        $auth_data->lastused = time();
        $auth_data->setVar('auth_id', $authData['auth_id']);
        $auth_data->setVar('userid', $userId);
        $auth_data->markNew();
        return $auth_data->commit();
    }

    public function createExtra($identity, $extraData)
    {
        $model = $this->getModel();
        $data = array();
        foreach ($model->Field->criteria()->active_is(1)->fetch() as $field) {
            if (!$field_plugin = $this->_application->getPlugin($field->plugin)) {
                continue;
            }
            if ($field_data = @$extraData[$field->plugin][$field->name]) {
                $plugin_lib = $field_plugin->getLibrary();
                list($filtered_value, $filter_id) = $field_data['filter'];
                $data[$plugin_lib][$field->plugin][$field->name] = array(
                    'value' => $field_plugin->userFieldSubmit($field->name, $field_data['value'], $identity, $filtered_value, $filter_id),
                    'visibility' => $field_data['visibility']
                );
            }
        }
        $extra = $model->create('Extra');
        $extra->setData($data);
        $extra->setVar('userid', $identity->getId());
        $extra->markNew();
        return $extra->commit();
    }

    public function getPermissionsByIdentity(Sabai_User_Identity $identity)
    {
        $id = $identity->getId();

        // Check if cached
        if (isset($this->_userPermissions[$id])) return $this->_userPermissions[$id];

        if (true === $role_ids = $this->getManagerPlugin()->userGetRoleIdsById($id)) {
            // Returning true means all roles and permissions
            $this->_userPermissions[$id] = true;
            return true;
        }

        $model = $this->getModel();
        foreach ($model->Member->fetchByUser($id) as $member) {
            $role_ids [] = $member->getVar('role_id');
        }

        $permissions = array();
        $roles = $model->Role->criteria()->id_in($role_ids)->fetch();
        foreach ($roles as $role) {
            if ($role->system) {
                $this->_userPermissions[$id] = true;
                return true; // Super user
            }
            foreach ($role->getPermissions() as $perm_name) {
                $permissions[] = $perm_name;
            }
        }
        $this->_userPermissions[$id] = array_unique($permissions);

        return $this->_userPermissions[$id];
    }

    public function checkPermissionByIdentity(Sabai_User_Identity $identity, $permission)
    {
        if (true == $permissions = $this->getPermissionsByIdentity($identity)) return true;

        foreach ((array)$permission as $perm) {
            if (in_array($perm, $permissions)) return true;
        }

        return false;
    }
}
