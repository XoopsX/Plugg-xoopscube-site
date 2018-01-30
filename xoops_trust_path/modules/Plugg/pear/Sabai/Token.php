<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Token
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * The name of token variable in user request
 */
if (!defined('SABAI_TOKEN_NAME')) {
    define('SABAI_TOKEN_NAME', '__T');
}

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Token
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
class Sabai_Token
{
    const TOKEN_PREFIX = 'Sabai_Token_';
    /**
     * @var string
     */
    protected $_salt;
    /**
     * @var int
     */
    protected $_timestamp;
    /**
     * @var bool
     */
    private static $_seeded = false;

    /**
     * Constructor
     *
     * @param string $salt
     * @return Sabai_Token
     */
    private function __construct($salt)
    {
        $this->_salt = $salt;
        $this->_timestamp = time();
    }

    /**
     * Creates a new Sabai_Token object
     *
     * @return Sabai_Token
     * @param string $tokenId
     */
    public static function create($tokenId)
    {
        if (!self::$_seeded) {
            mt_srand();
            self::$_seeded = true;
        }
        $salt = function_exists('hash') ? hash('md5', uniqid(mt_rand(), true)) : md5(uniqid(mt_rand(), true));
        $token = new Sabai_Token($salt);
        $session_key = self::TOKEN_PREFIX . $tokenId;
        $_SESSION[$session_key] = serialize($token);
        return $token;
    }

    /**
     * Checks if a token with a certain ID exists
     *
     * @param string $tokenID
     * @return mixed Sabai_Token if token exists, false otherwise
     */
    public static function exists($tokenID)
    {
        $session_key = self::TOKEN_PREFIX . $tokenID;
        if (!empty($_SESSION[$session_key])) {
            return unserialize($_SESSION[$session_key]);
        }
        return false;
    }

    /**
     * Destroys an existing token
     *
     * @param string $tokenID
     */
    public static function destroy($tokenID)
    {
        $session_key = self::TOKEN_PREFIX . $tokenID;
        unset($_SESSION[$session_key]);
    }

    /**
     * Validates a token
     *
     * @param string $value;
     * @param string $tokenId
     * @param int $tokenLifeTime
     * @return bool
     */
    public static function validate($value, $tokenId, $tokenLifeTime = 1800, $destroyToken = true)
    {
        if (false === $token = self::exists($tokenId)) {
            Sabai_Log::info(sprintf('Invalid token %s requested', $tokenId), __FILE__, __LINE__);
            return false;
        }

        if ($destroyToken) self::destroy($tokenId);
        if ($token->getTimestamp() + $tokenLifeTime < time()) {
            Sabai_Log::info(sprintf('Token %s already expired', $tokenId), __FILE__, __LINE__);
            return false;
        }

        if ($token->getValue() != $value) {
            Sabai_Log::info('Failed validating token', __FILE__, __LINE__);
            return false;
        }

        return true;
    }

    /**
     * Returns the value of this token
     *
     * @return string
     */
    public function getValue()
    {

        return function_exists('hash') ? hash('sha1', $this->_salt . $this->_timestamp) : sha1($this->_salt . $this->_timestamp);
    }

    /**
     * Returns the tiemstamp at which token was created
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }
}