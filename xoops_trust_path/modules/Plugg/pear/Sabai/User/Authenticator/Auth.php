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
 * @subpackage Authenticator
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Sabai_User_Authenticator
 */
require_once 'Sabai/User/Authenticator.php';
/**
 * Auth
 */
require_once 'Auth/Auth.php';

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_User
 * @subpackage Authenticator
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 * @abstract
 */
class Sabai_User_Authenticator_Auth extends Sabai_User_Authenticator
{
    /**
     * @var Auth
     * @access private
     */
    var $_auth;

    /**
     * Constructor
     *
     * @param Auth $auth An instance of the PEAR Auth class
     * @return Sabai_User_Authenticator_Auth
     */
    function Sabai_User_Authenticator_Auth(&$auth)
    {
        $this->_auth =& $auth;
    }

    /**
     * Authenticates a user
     *
     * @param Sabai_Request $request
     * @return Sabai_User on success, false on failure
     */
    function &authenticate(&$request)
    {
        $user = false;
        $this->_auth->setShowLogin(false);
        $this->_auth->assignData();
        $this->_auth->login();
        if (false !== $this->_auth->checkAuth()) {
            $user = new Sabai_User($this->_getUserIdentity($request, $this->_auth), true);
        }
        return $user;
    }

    /**
     * Deauthenticates a user
     *
     */
    function deauthenticate()
    {
        unset($_SESSION[$this->_auth->_sessionName]);
    }

    /**
     * Checks whether the authenticator has a view of its own
     *
     * @return bool
     */
    function hasView()
    {
        return false;
    }

    /**
     * Gets an authentication error message
     *
     * @return string
     */
    function getAuthError()
    {
        return $this->_auth->getStatus();
    }

    /**
     * Gets the username field name used in authentication
     *
     * @return string
     */
    function getAuthUsernameField()
    {
        return $this->_auth->getPostUsernameField();
    }

    /**
     * Gets the password field name used in authentication
     *
     * @return string
     */
    function getAuthPasswordField()
    {
        return $this->_auth->getPostPasswordField();
    }

    /**
     * Creates a user identity object
     *
     * @abstract
     * @return Sabai_User_Identity
     * @param string $username
     */
    function &_getUserIdentity(&$request, &$auth){}
}