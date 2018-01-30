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

require_once 'Sabai/Template.php';
require_once 'Sabai/Template/PHP/Helper.php';

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
 */
class Sabai_Template_PHP extends Sabai_Template
{
    private $_templateDir;
    private $_helperDir;
    private $_helpers = array();

    /**
     * Constructor
     *
     * @param array $templateDir
     * @return Sabai_Template_PHP
     */
    public function __construct($templateDir)
    {
        $this->setTemplateDir($templateDir);
    }

    public function setTemplateDir($templateDir, $priority = 0)
    {
        foreach ((array)$templateDir as $template_dir) {
            $this->addTemplateDir($template_dir, $priority);
        }

        return $this;
    }

    public function addTemplateDir($templateDir, $priority = 0)
    {
        if (!isset($this->_templateDir[$priority])) {
            $this->_templateDir[$priority] = array($templateDir);
        } else {
            array_unshift($this->_templateDir[$priority], $templateDir);
        }
        if (!isset($this->_helperDir[$priority])) {
            $this->_helperDir[$priority] = array($templateDir . '/helpers');
        } else {
            array_unshift($this->_helperDir[$priority], $templateDir . '/helpers');
        }
        krsort($this->_templateDir, SORT_NUMERIC);
        krsort($this->_helperDir, SORT_NUMERIC);

        return $this;
    }

    public function getTemplatePath($file)
    {
        // if the file name contains any slashes, it's considered a file outside the template directories
        if (false !== strpos($file, '/')) {
            if (file_exists($file)) {
                return $file;
            }
        } else {
            foreach (array_keys($this->_templateDir) as $i) {
                foreach ($this->_templateDir[$i] as $template_dir) {
                    $path = $template_dir . '/' . $file;
                    if (file_exists($path)) {
                        return $path;
                    }
                }
            }
        }
        return false;
    }

    protected function _doDisplay($__file, array $__vars)
    {
        extract($__vars, EXTR_REFS);
        include $__file;
    }

    public function setObject($name, $object)
    {
        $this->setHelper($name, $object);
        return $this;
    }

    public function getHelper($name)
    {
        $this->loadHelper($name);
        return $this->_helpers[$name];
    }

    public function loadHelper($name)
    {
        if (!isset($this->_helpers[$name])) {
            $class = 'Sabai_Template_PHP_Helper_' . $name;
            if (!class_exists($class)) {
                foreach (array_keys($this->_helperDir) as $i) {
                    foreach ($this->_helperDir[$i] as $helper_dir) {
                        $class_path = sprintf('%s/%s.php', $helper_dir, $name);
                        if (file_exists($class_path)) {
                            require $class_path;
                            break 2;
                        }
                    }
                }
            }
            $this->setHelper($name, new $class($this));
        } elseif ($this->_helpers[$name] instanceof Sabai_Handle) {
            $this->setHelper($name, $this->_helpers[$name]->instantiate());
        }

        return $this;
    }

    public function loadHelpers($names)
    {
        foreach ($names as $name) {
            $this->loadHelper($name);
        }

        return $this;
    }

    public function setHelper($name, $helper)
    {
        $this->_helpers[$name] = $helper;
        return $this;
    }

    public function __get($name)
    {
        return $this->getHelper($name);
    }

    /**
     * PHP magic method
     *
     * @param string $name
     * @param bool
     */
    public function __isset($name)
    {
        return isset($this->_helpers[$name]);
    }

    /**
     * PHP magic method
     *
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->_helpers[$name]);
    }

    /**
     * PHP magic method
     *
     * @param string $name
     * @param mixed $helper
     */
    public function __set($name, $helper)
    {
        $this->_helpers[$name] = $helper;
    }
}