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
 * @since      File available since Release 0.2.1
*/

require_once 'Sabai/Template.php';

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
 * @since      Class available since Release 0.2.1
 */
class Sabai_Template_Smarty extends Sabai_Template
{
    var $_smarty;
    var $_templateDir = array();

    /**
     * Constructor
     *
     * @param Smarty $smarty
     * @return Sabai_Template_Smarty
     */
    function Sabai_Template_Smarty($smarty)
    {
        $this->_smarty = $smarty;
        $this->_templateDir = array($this->_smarty->template_dir);
    }
    
    function setTemplateDir($templateDir)
    {
        $this->_smarty->template_dir = $templateDir;
        $this->_templateDir = array($templateDir);
    }

    function addTemplateDir($templateDir)
    {
        array_unshift($this->_templateDir, $templateDir);
    }

    function getTemplatePath($file)
    {
        // if the file name contains "/", it's considered a file outside the template directories
        if (false !== strpos($file, '/')) {
            if ($this->_smarty->templateExists($file)) {
                return $file;
            }
        } else {
            foreach ($this->_templateDir as $template_dir) {
                $this->_smarty->template_dir = $template_dir;
                if ($this->_smarty->templateExists($file)) {
                    return $file;
                }
            }
        }
        return false;
    }

    function _doDisplay($file, $vars)
    {
        foreach (array_keys($vars) as $k) {
            if (is_object($vars[$k])) {
                $this->_smarty->assign_by_ref($k, $vars[$k]);
            } else {
                $this->_smarty->assign($k, $vars[$k]);
            }
        }
        $this->_smarty->display($file);
    }

    function setObject($name, &$object)
    {
        $this->_smarty->assign_by_ref($name, $object);
    }
}