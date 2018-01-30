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
 * Sabai_User_Authenticator_Auth
 */
require_once 'Sabai/User/Authenticator/AuthHTTP.php';

/**
 * Authenticates a XOOPS account
 *
 * This class uses PEAR Auth and DB_Lite Auth_Container to connect to local/remote database server
 * where XOOPS user data is hosted.
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
 */
class Sabai_User_Authenticator_Auth_XOOPS extends Sabai_User_Authenticator_AuthHTTP
{
    var $_xoopsUrl;

    /**
     * Constructor
     *
     * @param string $xoopsUrl
     * @param string $dbPass
     * @param string $dbName
     * @param string $dbPrefix
     * @param string $dbHost
     * @param string $dbScheme
     * @return Sabai_User_Authenticator_AuthHTTP_XOOPS
     */
    function Sabai_User_Authenticator_AuthHTTP_XOOPS($xoopsUrl, $dbUser = 'root', $dbPass = '', $dbName = 'xoops',
                                                     $dbPrefix = 'xoops', $dbHost = 'localhost', $dbScheme = 'mysql')
    {
        $options = array('table'       => $dbPrefix . '_users',
                         'usernamecol' => 'uname',
                         'passwordcol' => 'pass',
                         'dsn'         => sprintf('%s://%s:%s@%s/%s', $dbScheme, $dbUser, $dbPass, $dbHost, $dbName),
                         'db_fields'   => '*');
        parent::Sabai_User_Authenticator_AuthHTTP(new Auth_HTTP('DBLite', $options));
        $this->_xoopsUrl = $xoopsUrl;
    }

    /**
     * Creates a user instance
     *
     * @access protected
     * @return Sabai_User_Identity
     * @param Sabai_Request $request
     * @param Auth $auth
     */
    function &_getUserIdentity(&$request, &$auth)
    {
        $identity = new Sabai_User_Identity($auth->getAuthData('uid'), $auth->getAuthData('uname'));
        $identity->setName($auth->getAuthData('name'));
        $identity->setEmail($auth->getAuthData('email'));
        $identity->setUrl($auth->getAuthData('url'));
        $identity->setTimeCreated($auth->getAuthData('user_regdate'));
        if ('blank.gif' != $avatar = $auth->getAuthData('user_avatar')) {
            $identity->setImage($this->_xoopsUrl . '/uploads/' . $avatar);
        }
        return $identity;
    }
}