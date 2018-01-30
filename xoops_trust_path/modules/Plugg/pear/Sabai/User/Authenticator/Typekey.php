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
 * Auth_Typekey
 */
require_once 'Auth/TypeKey.php';

/**
 * Authenticates Typekey users
 *
 * Authenticates Typekey users
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
 */
class Sabai_User_Authenticator_Typekey extends Sabai_User_Authenticator
{
    /**
     * @access protected
     * @var string
     */
    var $_apiToken;
    /**
     * @access protected
     * @var bool
     */
    var $_needEmail;

    /**
     * Constructor
     *
     * @param string $apiToken
     * @param bool $needEmail
     * @return Sabai_User_Authenticator_Typekey
     */
    function Sabai_User_Authenticator_Typekey($apiToken, $needEmail = false)
    {
        $this->_apiToken = $apiToken;
        $this->_needEmail = $needEmail;
    }

    /**
     * Authenticates a user
     *
     * @param Sabai_Request $request
     * @return Sabai_User on success, false on failure
     */
    function &authenticate(&$request)
    {
        $this->_typekey = new Auth_TypeKey();
        $this->_typekey->site_token($this->_apiToken);
        if ($request->getAsStr('sig', false)) {
            if (true === $result = $this->_typekey->verifyTypeKey($request->getAll())) {
                $identity = new Sabai_User_Identity($request->getAsStr('nick'), $request->getAsStr('nick'));
                $identity->setName($request->getAsStr('name'));
                $user = new Sabai_User($identity, true);
                return $user;
            }
        }
        header('Location: '. $this->_typekey->urlSignIn($request->getUri(), $this->_needEmail));
        exit();
    }
}