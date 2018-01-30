<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_Template
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.5
*/

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_Template
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.5
 * @abstract
 */
abstract class Sabai_Template
{
    /**
     * Returns the rendered content of a template file
     *
     * @param string $fileName
     * @param array $vars
     * @return mixed string or false
     */
    public function render($fileName, array $vars = array())
    {
        ob_start();
        if ($this->display($fileName, $vars)) {
            return ob_get_clean();
        }
        ob_end_clean();
        return false;
    }

    /**
     * Outputs the content of a template
     *
     * @return bool
     * @param string $fileName
     * @param array $vars
     */
    public function display($fileName, array $vars = array())
    {
        if ($path = $this->getTemplatePath($fileName)) {
            $this->_doDisplay($path, $vars);
            return true;
        }
        return false;
    }

    /**
     * Sets a new template directory. This should clear th list of template direcotories
     * added previously by the addTemplateDir method.
     *
     * @param string $templateDir
     * @param int $priority
     */
    abstract public function setTemplateDir($templateDir, $priority = 0);
    /**
     * Adds a template directory
     *
     * @param string $templateDir
     * @param int $priority
     */
    abstract public function addTemplateDir($templateDir, $priority = 0);

    /**
     * Gets the path to a template file
     *
     * @param string $file
     */
    abstract public function getTemplatePath($templateName);

    /**
     * Outputs the content of a template
     *
     * @param string $file
     * @param array $vars
     */
    abstract protected function _doDisplay($file, array $vars);
}