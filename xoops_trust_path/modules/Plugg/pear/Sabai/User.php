<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_User
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Sabai_User_Identity
 */
require_once 'Sabai/User/Identity.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_User
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
class Sabai_User
{
    /**
     * @var Sabai_User_Identity
     */
    protected $_identity;
    /**
     * @var bool
     */
    protected $_authenticated;
    /**
     * @var string
     */
    protected $_key;
    /**
     * @var bool
     */
    protected $_superUser = false;
    /**
     * @var array
     */
    protected $_permissions = array();
    /**
     * @var bool
     */
    protected $_finalized = false;
    /**
     * @var bool
     */
    protected $_finalize = false;

    /**
     * Constructor
     *
     * @param Sabai_User_Identity $identity
     * @param bool $authenticated
     * @param string $key
     * @return Sabai_User
     */
    public function __construct(Sabai_User_AbstractIdentity $identity, $authenticated = false, $key = 'default')
    {
        $this->_identity = $identity;
        $this->_authenticated = $authenticated;
        $this->_key = $key;
    }

    /**
     * Returns the current logged in user
     * @param string $anonymousName
     * @param string $key
     * @return Sabai_User
     */
    public static function getCurrentUser($anonymousName, $key = 'default')
    {
        if (!$user = self::hasCurrentUser($key)) {
            $user = self::createAnonymousUser($anonymousName, $key);
        }
        return $user;
    }

    /**
     * Checks if an user object already exists in current session
     * @param string $key
     * @return mixed Sabai_User if exists, false if not
     */
    public static function hasCurrentUser($key = 'default')
    {
        if (!isset($_SESSION['Sabai_User_current'][$key])) return false;

        if (!$user = unserialize($_SESSION['Sabai_User_current'][$key])) {
            unset($_SESSION['Sabai_User_current'][$key]);
            return false;
        }

        register_shutdown_function(array($user, 'shutdown'));

        return $user;
    }

    /**
     * Creates a user object with anonymoous identity
     * @param string $anonymousName
     * @param string $key
     */
    public static function createAnonymousUser($anonymousName, $key = 'default')
    {
        if (!class_exists('Sabai_User_AnonymousIdentity', false)) {
            require 'Sabai/User/AnonymousIdentity.php';
        }
        return new Sabai_User(new Sabai_User_AnonymousIdentity($anonymousName), false, $key);
    }

    /**
     * Returns a string identifier of this user
     *
     * @return string
     */
    public function getId()
    {
        return $this->_identity->getId();
    }

    /**
     * Returns an identy object for the user
     *
     * @return Sabai_User_Identity
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Sets the user as authenticated
     *
     * @param bool $flag
     */
    public function setAuthenticated($flag = true)
    {
        $this->_authenticated = $flag;
    }

    /**
     * Checks whether this user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->_authenticated;
    }

    /**
     * Starts a user session
     *
     */
    public function startSession($regenerateId = true)
    {
        if (empty($_SESSION['Sabai_User_keepalive'][$this->_key])) {
            if ($regenerateId) session_regenerate_id();
            register_shutdown_function(array($this, 'shutdown'));
            $_SESSION['Sabai_User_keepalive'][$this->_key] = true;
        }
    }

    /**
     * Ends the current user session
     *
     */
    public function endSession($clear = true)
    {
        if ($clear) {
            $_SESSION = array();
        } else {
            unset($_SESSION['Sabai_User_keepalive'][$this->_key]);
        }
    }

    /**
     * Saves the current user object to the session
     *
     */
    public function shutdown()
    {
        if ($this->isAuthenticated() && !empty($_SESSION['Sabai_User_keepalive'][$this->_key])) {

            // Finalize?
            if ($this->_finalize) {
            	$this->_finalized = true;
            	$this->_finalize = false;
            }

            $_SESSION['Sabai_User_current'][$this->_key] = serialize($this);
        } else {
            unset($_SESSION['Sabai_User_current'][$this->_key], $_SESSION['Sabai_User_keepalive'][$this->_key]);
        }
    }

    /**
     * Sets the user identity as a super user
     *
     * @param bool $flag
     */
    public function setSuperUser($flag = true)
    {
        $this->_superUser = $flag;
    }

    /**
     * Checks whether this user is a super user or not
     *
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->_superUser;
    }

    /**
     * Gets the string name for this user
     *
     * @return string
     */
    public function getName()
    {
        return $name = $this->_identity->getName() ? $name : $this->_identity->getUsername();
    }

    /**
     * Adds a permission
     *
     * @param string $perm
     */
    public function addPermission($perm)
    {
        $this->_permissions[$perm] = 1;
    }

    /**
     * Checks whether the user has a certain permission
     *
     * @param mixed $perm string or array
     * @return bool
     */
    public function hasPermission($perm)
    {
        if ($this->isSuperUser()) {
            return true;
        }

        foreach ((array)$perm as $_perm) {
            if (isset($this->_permissions[$_perm])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether the user has certain permissions
     *
     * @param array $perms
     * @return bool
     */
    public function hasPermissions($perms)
    {
        if ($this->isSuperUser()) {
            return true;
        }

        foreach ($perms as $_perm) {
            if (!isset($this->_permissions[$_perm])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the permissions array
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->_permissions;
    }

    /**
     * Checks if the user object is finalized
     *
     * @return bool
     */
    public function isFinalized()
    {
    	return $this->_finalized;
    }

    /**
     * Finalize the user object
     *
     * @param bool $bool
     */
    public function finalize($bool = true)
    {
    	$this->_finalize = $bool;
    }
}