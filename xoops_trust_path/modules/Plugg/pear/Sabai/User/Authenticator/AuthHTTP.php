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
 * @since      File available since Release 0.2.0
*/

/**
 * Sabai_User_Authenticator
 */
require_once 'Sabai/User/Authenticator.php';
/**
 * Auth_HTTP
 */
require_once 'Auth/HTTP.php';

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
 * @since      Class available since Release 0.2.0
 * @abstract
 */
class Sabai_User_Authenticator_AuthHTTP extends Sabai_User_Authenticator
{
    /**
     * @var Auth_HTTP
     * @access protected
     */
    var $_authHTTP;

    /**
     * Constructor
     *
     * @param Auth_HTTP $authHTTP An instance of the PEAR Auth_HTTP class
     * @return Sabai_User_Authenticator_AuthHTTP
     */
    function Sabai_User_Authenticator_AuthHTTP(&$authHTTP)
    {
        $this->_authHTTP =& $authHTTP;
    }

    /**
     * Authenticates a user
     *
     * @access protected
     * @param Sabai_Request $request
     * @return Sabai_User on success, false on failure
     */
    function &authenticate(&$request)
    {
        $user = false;
        if (false !== $this->_authHTTP->getAuth()) {
            $user = new Sabai_User($this->_getUserIdentity($request, $auth), true);
        }
        return $user;
    }

    /**
     * Deauthenticates a user
     *
     */
    function deauthenticate()
    {
        unset($_SESSION[$this->_authHTTP->_sessionName]);
    }

    /**
     * Gets an authentication error message
     *
     * @return string
     */
    function getAuthError()
    {
        return $this->_authHTTP->getStatus();
    }

    /**
     * Creates a Sabai_User_Authenticated instance
     *
     * @abstract
     * @return Sabai_User_Authenticated
     * @param Sabai_Request $request
     * @param Auth $auth
     */
    function &_getUserIdentity(&$request, &$auth){}
}