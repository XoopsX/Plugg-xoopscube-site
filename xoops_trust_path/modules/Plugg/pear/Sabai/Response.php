<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Response
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Response
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
abstract class Sabai_Response
{
    const ERROR = 1;
    const SUCCESS = 2;
    const VIEW = 3;

    const MESSAGE_ERROR = 1;
    const MESSAGE_WARNING = 2;
    const MESSAGE_INFO = 3;
    const MESSAGE_SUCCESS = 4;

    /**
     * The status of response
     *
     * @var int
     */
    protected $_status = self::VIEW;
    /**
     * Response message strings
     *
     * @var array
     */
    protected $_messages = array();
    /**
     * Uri of the error result
     *
     * @var array
     */
    protected $_errorUri;
    /**
     * Uri of the successful result
     *
     * @var array
     */
    protected $_successUri;
    /**
     * Names for the content
     *
     * @var array
     */
    protected $_contentNames = array();
    /**
     * Contents to display on next response
     *
     * @var array
     */
    protected $_flash = array();
    /**
     * The default URI to be sent upon success
     *
     * @var array
     */
    protected $_defaultErrorUri = array();
    /**
     * The default URI to be sent upon error
     *
     * @var array
     */
    protected $_defaultSuccessUri = array();

    /**
     * Checks if the response status is success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->_status == self::SUCCESS;
    }

    /**
     * Checks if the response status is error
     *
     * @return bool
     */
    public function isError()
    {
        return $this->_status == self::ERROR;
    }

    /**
     * Sets error data for the response
     *
     * @param string
     * @param mixed $uri
     */
    public function setError($msg = null, $uri = null)
    {
        if (isset($msg)) $this->addMessage($msg, self::MESSAGE_ERROR);
        $this->_errorUri = $uri;
        $this->_status = self::ERROR;

        return $this;
    }

    /**
     * Sets successful result data for the response
     *
     * @param string $msg
     * @param mixed $uri
     */
    public function setSuccess($msg = null, $uri = null)
    {
        if (isset($msg)) $this->addMessage($msg, self::MESSAGE_SUCCESS);
        $this->_successUri = $uri;
        $this->_status = self::SUCCESS;

        return $this;
    }

    /**
     * Adds a response message
     *
     * @param string $string
     * @param int $level
     */
    public function addMessage($string, $level = self::MESSAGE_INFO)
    {
        $this->_messages[] = array($string, $level);

        return $this;
    }

    /**
     * Adds the name of content
     *
     * @param mixed $contentName string or array
     */
    public function pushContentName($contentName)
    {
        if (!in_array($contentName, $this->_contentNames)) {
            array_unshift($this->_contentNames, $contentName);
        }

        return $this;
    }

    /**
     * Removes the last content name
     *
     * @return mixed string or array
     */
    public function popContentName()
    {
        return array_shift($this->_contentNames);
    }

    /**
     * Sets the default success URI
     *
     * @param array $uri
     */
    public function setDefaultSuccessUri(array $uri)
    {
        $this->_defaultSuccessUri = $uri;

        return $this;
    }

    /**
     * Sets the default error URI
     *
     * @param array $uri
     */
    public function setDefaultErrorUri(array $uri)
    {
        $this->_defaultErrorUri = $uri;

        return $this;
    }

    /**
     * Sends a response according to its status
     *
     * @param Sabai_Application $application
     * @param bool $exit
     */
    public function send(Sabai_Application $application, $exit = true)
    {
        switch ($this->_status) {
            case self::ERROR:
                if (!empty($this->_errorUri)) {
                    $error_uri = is_array($this->_errorUri) ? $application->createUrl($this->_errorUri) : $this->_errorUri;
                } else {
                    $error_uri = $application->createUrl($this->_defaultErrorUri);
                }
                $this->_sendError($application, $this->_messages, $error_uri);
                break;
            case self::SUCCESS:
                if (!empty($this->_successUri)) {
                    $success_uri = is_array($this->_successUri) ? $application->createUrl($this->_successUri) : $this->_successUri;
                } else {
                    $success_uri = $application->createUrl($this->_defaultSuccessUri);
                }
                $this->_sendSuccess($application, $this->_messages, $success_uri);
                break;
            default:
                $vars = $application->getData();
                $vars['CONTENT'] = '';
                $vars['FLASH'] = empty($_SESSION['Sabai_Response_flash']) ? array() : $_SESSION['Sabai_Response_flash'];
                $this->_sendContent($application, $this->_contentNames, $vars);
                break;
        }
        $_SESSION['Sabai_Response_flash'] = $this->_flash;
        if ($exit) exit();
    }

    /**
     * Adds a flash message
     *
     * @param string$flashMsg
     * @param int $level
     */
    public function addFlash($flashMsg, $level)
    {
        $this->_flash[] = array(
            'msg' => $flashMsg,
            'level' => $level
        );

        return $this;
    }

    public function clearFlash()
    {
        $_SESSION['Sabai_Response_flash'] = array();

        return $this;
    }

    /**
     * Sends an error response
     *
     * @param array $messages
     * @param string $errorUri
     */
    abstract protected function _sendError(Sabai_Application $application, $messages, $errorUri);

    /**
     * Sends a successful response
     *
     * @param array $messages
     * @param string $successUri
     */
    abstract protected function _sendSuccess(Sabai_Application $application, $messages, $successUri);

    /**
     * Sends a content response
     *
     * @param array $contentNames
     * @param array $vars
     */
    abstract protected function _sendContent(Sabai_Application $application, $contentNames, $vars);
}