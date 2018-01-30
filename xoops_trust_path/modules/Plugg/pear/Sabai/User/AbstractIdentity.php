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
 * @since      File available since Release 0.1.7
*/

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
 * @since      Class available since Release 0.1.7
 */
abstract class Sabai_User_AbstractIdentity
{
    protected $_id;
    protected $_username = '';
    protected $_name = '';
    protected $_email = '';
    protected $_url = '';
    protected $_image = '';
    protected $_timeCreated;
    private $_data = array();
    private $_dataLoader;
    private $_dataLoaded = false;

    /**
     * Constructor
     *
     * @param string $id
     * @param string $username
     * @return Sabai_User_AbstractIdentity
     */
    protected function __construct($id, $username)
    {
        $this->_id = $id;
        $this->_username = $username;
    }

    /**
     * Magic method
     *
     * @param string $key
     */
    public function __get($key)
    {
        $method = 'get' . ucfirst($key);
        return $this->$method();
    }

    /**
     * Prevent extra data from being serialized
     */
    public function __sleep()
    {
        $this->_data = array();
        unset($this->_dataLoader);
        $this->_dataLoaded = false;
        return array('_id', '_username', '_name', '_email', '_url', '_image', '_timeCreated');
    }

    /**
     * Returns the string identifier of this identity
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Gets the login username of this identity
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Gets the display name of this identity
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets the display name of this identity
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Gets the email address of this identity
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Sets the email address of this identity
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * Gets the website URL of this identity
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Sets the website URL of this identity
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

    /**
     * Gets the image url of this identity
     *
     * @return string
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * Sets the image url of this identity
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->_image = $image;
    }

    /**
     * Gets the timestamp when the identity was created
     *
     * @return int
     */
    public function getTimeCreated()
    {
        return $this->_timeCreated;
    }

    /**
     * Sets the timestamp when the identity was created
     *
     * @param int $timeCreated
     */
    public function setTimeCreated($timeCreated)
    {
        $this->_timeCreated = $timeCreated;
    }

    /**
     * Sets an extra profile data
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * .
     * Gets an extra data. Pass in more parameters to narrow the search.
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key = null)
    {
        $this->loadData(); // lazy loading
        if (!isset($key)) {
            return $this->_data;
        }
        $data = $this->_data[$key];
        if (func_num_args() > 1) {
            $names = array_slice(func_get_args(), 1);
            foreach ($names as $name) {
                if (is_array($data) && array_key_exists($name, $data)) {
                    $data = $data[$name];
                } else {
                    trigger_error(sprintf('Request to non-existent key "%s"', $name), E_USER_NOTICE);
                    $data = null;
                    break;
                }
            }
        }
        return $data;
    }

    /**
     * .
     * Checks is an extra data exists. Pass in more parameters to narrow the search.
     *
     * @param string $key
     * @return mixed
     */
    public function hasData($key)
    {
        $this->loadData(); // lazy loading
        if (!array_key_exists($key, $this->_data)) return false;

        $data = $this->_data[$key];
        if (func_num_args() > 1) {
            $names = array_slice(func_get_args(), 1);
            foreach ($names as $name) {
                if (is_array($data) && array_key_exists($name, $data)) {
                    $data = $data[$name];
                } else {
                    return false;
                }
            }
        }
        return $data;
    }

    /**
     * Loads extra user data using a callback
     *
     */
    public function loadData()
    {
        if (!$this->_dataLoaded) {
            if (isset($this->_dataLoader) && is_callable($this->_dataLoader)) {
                call_user_func_array($this->_dataLoader, array($this));
            }
            $this->_dataLoaded = true;
            unset($this->_dataLoader);
        }
    }

    /**
     * Sets a callback for loading extra user data
     *
     * @param mixed $callback a valid callback function string or array
     */
    public function setDataLoader($callback)
    {
        $this->_dataLoader = $callback;
    }

    abstract public function isAnonymous();
}