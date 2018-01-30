<?php
eval('
class ' . ucfirst($module_dirname) . '_xoopscube_preload extends XCube_ActionFilter
{
    private $_preloader;

    public function __construct($controller)
    {
        parent::XCube_ActionFilter($controller);
        $this->_preloader = new plugg_xoopscube_module_preloader("' . $module_dirname . '");
    }

    public function preBlockFilter()
    {
        $this->_preloader->preFilter($this->mRoot);
    }

    public function postFilter()
    {
        $this->_preloader->postFilter($this->mRoot);
    }
}
');

if(!class_exists('plugg_xoopscube_module_preloader', false)) {

    class plugg_xoopscube_module_preloader
    {
        private $_app;
        private $_moduleDirname;
        private static $_done = false;

        public function __construct($moduleDirname)
        {
            $this->_moduleDirname = $moduleDirname;
        }

        public function preFilter($xcubeRoot)
        {
            // Allow only one module to setup delegates
            if (self::$_done) {
                return;
            }

            if ($xcubeRoot->mController->_mStrategy->mStatusFlag == LEGACY_CONTROLLER_STATE_ADMIN) {
                $in_admin = true;
                $module_script = 'admin/index.php';
            } else {
                $in_admin = false;
                $module_script = 'index.php';
            }
            $module_dirname = $this->_moduleDirname; // common.php expects variable $module_dirname
            require dirname(__FILE__) . '/../common.php';
            $this->_app = $plugg;

            if ($in_admin) {
                $xcubeRoot->mDelegateManager->add(
                    'Legacy_ModuleInstallAction.InstallSuccess',
                    array($this, 'adminModuleInstallSuccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
                $xcubeRoot->mDelegateManager->add(
                    'Legacy_ModuleUninstallAction.UninstallSuccess',
                    array($this, 'adminModuleUninstallSuccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
                $xcubeRoot->mDelegateManager->add(
                    'Legacy_ModuleUpdateAction.UpdateSuccess',
                    array($this, 'adminModuleUpdateSuccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
            }

            // Check plugin availability
            if ((!$user_plugin = $this->_app->getPlugin('user')) || // User plugin must be installed and active
                (!$userman_plugin_name = $user_plugin->getParam('userManagerPlugin')) || // User manager plugin must be defined
                $userman_plugin_name == 'xoopscubeuserapi' || // User manager plugin should be other than XOOPSCubeUserAPI
                (!$userman_plugin = $this->_app->getPlugin($userman_plugin_name)) // User manager plugin must be installed and active
            ) {
                return;
            }

            // Make sure all users are allowed to access this module
            $module_id = xoops_gethandler('module')->getByDirname($module_dirname)->getVar('mid');
            $gperm_handler = xoops_gethandler('groupperm');
            if (!$gperm_handler->checkRight('module_read', $module_id, XOOPS_GROUP_ANONYMOUS) ||
                !$gperm_handler->checkRight('module_read', $module_id, XOOPS_GROUP_USERS)
            ) {
                trigger_error(sprintf(
                    'Module %s needs to be accessible by all user groups to enable the user management plugin %s.',
                    $module_dirname, $userman_plugin_name),
                    E_USER_WARNING
                );
                return;
            }

            // Set delegates
            $xcubeRoot->mController->mSetupUser->reset();
            $xcubeRoot->mController->mSetupUser->add(array($this, 'setupUser'), XCUBE_DELEGATE_PRIORITY_FIRST);
            if (!$in_admin) {
                $xcubeRoot->mDelegateManager->add(
                    'Legacypage.Edituser.Access',
                    array($this, 'edituserAccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
                $xcubeRoot->mDelegateManager->add(
                    'Legacypage.Lostpass.Access',
                    array($this, 'lostpassAccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
                $xcubeRoot->mDelegateManager->add(
                    'Legacypage.Register.Access',
                    array($this, 'registerAccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
                $xcubeRoot->mDelegateManager->add(
                    'Legacypage.User.Access',
                    array($this, 'userAccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
                $xcubeRoot->mDelegateManager->add(
                    'Legacypage.Userinfo.Access',
                    array($this, 'userinfoAccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
                $xcubeRoot->mDelegateManager->add(
                    'Legacypage.Search.Access',
                    array($this, 'searchAccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
            } else {
                $xcubeRoot->mDelegateManager->add(
                    'Legacy.Admin.Event.UserDelete.Success',
                    array($this, 'adminUserDeleteSuccess'),
                    XCUBE_DELEGATE_PRIORITY_FIRST
                );
            }

            self::$_done = true;
        }

        public function postFilter($xcubeRoot)
        {
            // Clear some session variables when pages outside the module
            if (!isset($GLOBALS['xoopsModule'])) {
                unset($_SESSION['Sabai_Request_uri']);
                $_SESSION['Sabai_Response_flash'] = array();
            }
        }

        public function setupUser($principal, $controller, $context)
        {
            if ($user = $this->_app->hasCurrentUser()) {
                if ($context->mXoopsUser = xoops_gethandler('member')->getUser($user->getId())) {
                    if (!isset($_SESSION['xoopsUserGroups'])) {
                        $_SESSION['xoopsUserGroups'] = $context->mXoopsUser->getGroups();
                    } else {
                        $context->mXoopsUser->setGroups($_SESSION['xoopsUserGroups']);
                    }
                    $roles = array('Site.RegisteredUser');
                    if ($context->mXoopsUser->isAdmin(-1)) {
                        $roles[] = 'Site.Administrator';
                    }
                    if (in_array(XOOPS_GROUP_ADMIN, $_SESSION['xoopsUserGroups'])) {
                        $roles[] = 'Site.Owner';
                    }
                    $principal = new Legacy_GenericPrincipal(new Legacy_Identity($context->mXoopsUser), $roles);
                    return;
                } else {
                    $user->endSession();
                }
            }
            $context->mXoopsUser = null;
            $principal = new Legacy_GenericPrincipal(new Legacy_AnonymousIdentity(), array('Site.GuestUser'));
        }

        public function edituserAccess()
        {
            switch (@$_GET['op']) {
                case 'avatarform':
                case 'avatarupload':
                case 'avatarchoose':
                    header('Location: ' . $this->_createUrl('/user/edit_image'));
                    exit;
                default:
                    header('Location: ' . $this->_createUrl('/user/edit'));
                    exit;
            }
        }

        public function lostpassAccess()
        {
            header('Location: ' . $this->_createUrl('/user/request_password'));
            exit;
        }

        public function registerAccess()
        {
            header('Location: ' . $this->_createUrl('/user/register'));
            exit;
        }

        public function userAccess()
        {
            $params = array();
            if (!$op = @$_GET['op']) {
                if (!is_object(@$GLOBALS['xoopsUser'])) {
                    $op = 'login';
                }
            }
            switch ($op) {
                case 'login':
                    $route = '/user/login';
                    if ($xoops_redirect = trim((string)@$_GET['xoops_redirect'])) {
                        $parsed = parse_url(XOOPS_URL);
                        $redirect = $parsed['scheme'] . '://' . $parsed['host'];
                        if (isset($parsed['port'])) $redirect .= ':' . $parsed['port'];
                        $redirect .= $xoops_redirect;
                        $params = array('return' => 1, 'return_to' => $redirect);
                    }
                    break;
                case 'logout':
                    unset($_SESSION['xoopsUserGroups']);
                    $route = '/user/logout';
                    break;
                case 'delete':
                    $route = '/user/delete';
                    break;
                default:
                    $route = '/user';
            }
            header('Location: ' . $this->_createUrl($route, $params));
            exit;
        }

        public function userInfoAccess()
        {
            $route = ($user_id = (int)@$_GET['uid']) ? '/user/' . $user_id : '/user';
            header('Location: ' . $this->_createUrl($route));
            exit;
        }

        public function searchAccess()
        {
            header('Location: ' . $this->_createUrl('/search'));
            exit;
        }

        public function adminUserDeleteSuccess($xoopsUser)
        {
            require_once 'Sabai/User/Identity.php';
            $identity = SabaiXOOPS::getUserIdentity($xoopsUser);
            $this->_app->dispatchEvent('UserIdentityDeleteSuccess', array($identity));
        }

        public function adminModuleInstallSuccess($module, &$log)
        {
            $this->_app->dispatchEvent('XOOPSCubeModuleInstallSuccess', array($module));
        }

        public function adminModuleUninstallSuccess($module, &$log)
        {
            $this->_app->dispatchEvent('XOOPSCubeModuleUninstallSuccess', array($module));
        }

        public function adminModuleUpdateSuccess($module, &$log)
        {
            $this->_app->dispatchEvent('XOOPSCubeModuleUpdateSuccess', array($module));
        }

        private function _createUrl($route, $params = array())
        {
            return $this->_app->createUrl(array('base' => $route, 'params' => $params, 'separator' => '&'));
        }
    }
}