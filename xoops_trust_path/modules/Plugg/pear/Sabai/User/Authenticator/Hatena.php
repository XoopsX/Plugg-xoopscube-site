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
 * Auth_Hatena
 */
require_once 'Auth/Hatena.php';

/**
 * Authenticats XOOPS users
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
 * @since      Class available since Release 0.1.1
 */
class Sabai_User_Authenticator_Hatena extends Sabai_User_Authenticator
{
    /**
     * @access protected
     * @var string
     */
    var $_apiKey;
    /**
     * @access protected
     * @var string
     */
    var $_apiSecret;
    /**
     * @access protected
     * @var string
     */
    var $_apiJsonLib;

    /**
     * Constructor
     *
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $apiJsonLib
     * @return Sabai_User_Authenticator_Hatena
     */
    function Sabai_User_Authenticator_Hatena($apiKey, $apiSecret, $apiJsonLib = 'json')
    {
        $this->_apiKey = $apiKey;
        $this->_apiSecret = $apiSecret;
        $this->_apiJsonLib = in_array($apiJsonLib, array('json', 'services_json', 'jsphon')) ? $apiJsonLib : 'json';
    }

    /**
     * Authenticates a user
     *
     * @param Sabai_Request $request
     * @return Sabai_User on success, false on failure
     */
    function &authenticate(&$request)
    {
        $hatena_api = new Auth_Hatena($this->_apiKey, $this->_apiSecret);
        $hatena_api->json_parser = $this->_apiJsonLib;
        if ($cert = $request->getAsStr('cert', false)) {
            if ($hatena_user = $hatena_api->login($cert)) {
                $identity = new Sabai_User_Identity($hatena_user['name'], $hatena_user['name']);
                $identity->setImage($hatena_user['image_url']);
                $user = new Sabai_User($identity, true);
                return $user;
            }
        }
        header('Location: ' . $hatena_api->uri_to_login());
        exit;
    }
}