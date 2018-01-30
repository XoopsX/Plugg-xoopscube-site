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
 * Sabai_Response
 */
require 'Sabai/Response.php';

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
class Sabai_Response_Web extends Sabai_Response
{
    protected $_template;
    protected $_contentType = 'text/html';
    protected $_charset = SABAI_CHARSET;
    protected $_redirect = true;
    protected $_layout = true;
    protected $_layoutUrl;
    protected $_layoutPath;
    protected $_layoutFile;
    protected $_js;
    protected $_jsHead = array();
    protected $_jsHeadAjax;
    protected $_jsFoot = array();
    protected $_jsFiles = array();
    protected $_jsIndex = -1;
    protected $_css = array();
    protected $_cssFiles = array();
    protected $_cssIndex = -1;
    protected $_cssFileIndices = array();
    protected $_htmlHead = array();
    protected $_htmlHeadTitle;
    protected $_pageInfo = array();
    protected $_pageTitle;
    protected $_contentStackLevel = 0;
    protected $_contentRegion;

    public function __construct(Sabai_Template $template)
    {
        $this->_template = $template;
    }

    public function setLayout($layoutUrl, $layoutPath = '.')
    {
        $this->_layout = true;
        $this->_layoutUrl = $layoutUrl;
        $this->_layoutPath = $layoutPath;
        return $this;
    }

    public function setLayoutFile($layoutFile)
    {
        $this->_layoutFile = $layoutFile;
        return $this;
    }

    public function noLayout()
    {
        $this->_layout = false;
        return $this;
    }

    public function setContentStackLevel($contentStackLevel)
    {
        $this->_contentStackLevel = $contentStackLevel;
        return $this;
    }

    public function setContentRegion($contentRegion)
    {
        $this->_contentRegion = $contentRegion;
        return $this;
    }

    public function getTemplate()
    {
        return $this->_template;
    }

    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
        return $this;
    }

    public function setCharset($charset)
    {
        $this->_charset = $charset;
        return $this;
    }

    public function setRedirect($flag = true)
    {
        $this->_redirect = (bool)$flag;
        return $this;
    }

    public function send(Sabai_Application $application)
    {
        header(sprintf('Content-type: %s; charset=%s', $this->_contentType, $this->_charset));
        if ($this->_charset != SABAI_CHARSET) {
            ob_start();
            parent::send($application, false);
            echo mb_convert_encoding(ob_get_clean(), $this->_charset, SABAI_CHARSET);
        } else {
            parent::send($application);
        }
    }

    protected function _sendError(Sabai_Application $application, $messages, $url)
    {
        if (!$this->_redirect) {
            $this->_displayMessages($messages);
        } else {
            foreach ($messages as $message) {
                $this->addFlash($message[0], $message[1]);
            }
            header('Location: ' . str_replace(array('&amp;', "\r", "\n"), array('&'), $url));
        }
    }

    protected function _sendSuccess(Sabai_Application $application, $messages, $url)
    {
        if (!$this->_redirect) {
            $this->_displayMessages($messages);
        } else {
            foreach ($messages as $message) {
                $this->addFlash($message[0], $message[1]);
            }
            header('Location: ' . str_replace(array('&amp;', "\r", "\n"), array('&'), $url));
        }
    }

    protected function _sendContent(Sabai_Application $application, $contentNames, $vars)
    {
        if (!headers_sent()) {
            header('Expires: -1');
            header('Cache-Control: no-cache');
            header('Pragma: no-cache');
        }
        $vars['LAYOUT_URL'] = $this->_layoutUrl;
        // Display content directly if no layout and single level for maximum performance
        if ($this->_contentStackLevel == 1 && !$this->_layout) {
            $this->_displaySingleContent($contentNames, $vars);
        } else {
            if ($this->_layout) {
                $this->_layoutContent($application, $this->_renderContent($contentNames, $vars));
            } else {
                $this->_displayContent($contentNames, $vars);
            }
        }
    }

    protected function _displayMessages($messages)
    {
        foreach ($messages as $message) {
            echo $message[0] . "\n";
        }
    }

    protected function _displaySingleContent($contentNames, $vars)
    {
        // only display the template file that was found first
        foreach ($contentNames as $content_name) {
            Sabai_Log::info(sprintf('Fetching template file for %s', $content_name));
            if ($this->_template->display($content_name . '.tpl', $vars)) {
                Sabai_Log::info(sprintf('Rendering template file for %s', $content_name));
                return;
            }
        }
    }

    protected function _displayContent($contentNames, $vars)
    {
        $vars = $this->_renderContent($contentNames, $vars);
        print $vars['CONTENT'];
    }

    protected function _renderContent($contentNames, $vars)
    {
        foreach ($contentNames as $content_name) {
            Sabai_Log::info(sprintf('Fetching template file for %s', $content_name));
            if ($content = $this->_template->render($content_name . '.tpl', $vars)) {
                Sabai_Log::info(sprintf('Rendering template file for %s', $content_name));
                if ($last_slash_pos = strrpos($content_name, '/')) {
                    $_content_name = substr($content_name, $last_slash_pos + 1);
                }
                $vars['CONTENT'] = sprintf("<div id=\"%s\">\n%s\n</div>\n", h(str_replace('_', '-', $content_name)), $content);

                // Stop rendering if reached requested content stack level
                if (--$this->_contentStackLevel == 0) break;
            }
            // Stop rendering content if content region is specified and the region rendered
            if (isset($this->_contentRegion) && $this->_contentRegion == $content_name) break;
        }
        return $vars;
    }

    protected function _layoutContent(Sabai_Application $application, $vars)
    {
        $vars['CSS'] = $this->_getCSSHTML();
        list($vars['JS_HEAD'], $vars['JS_FOOT']) = $this->_getJSHTML();
        $vars['HTML_HEAD'] = implode("\n", $this->_htmlHead);
        $vars['PAGE_TITLE'] = $this->_pageTitle;
        $vars['PAGE_BREADCRUMBS'] = '';
        if (count($this->_pageInfo) > 0) {
            $page_info_last = array_pop($this->_pageInfo);
            if (!isset($vars['PAGE_TITLE'])) $vars['PAGE_TITLE'] = $page_info_last['title'];
            if (!empty($this->_pageInfo)) {
                $breadcrumbs = array();
                foreach ($this->_pageInfo as $page_info) {
                    $breadcrumbs[] = sprintf('<a href="%s">%s</a>', $application->createUrl($page_info['url']), h($page_info['title']));
                }
                $breadcrumbs[] = h($page_info_last['title']);
                $vars['PAGE_BREADCRUMBS'] = implode(' &gt; ', $breadcrumbs);
            }
        }
        $this->_template->display($this->_layoutPath . '/' . $this->_layoutFile, $vars);
    }

    public function addJSHead($js)
    {
        $this->_jsHead[++$this->_jsIndex] = $js;
        return $this;
    }

    public function addJSHeadAjax($js)
    {
        $this->_jsHeadAjax[++$this->_jsIndex] = $js;
        return $this;
    }

    public function addJSFoot($js)
    {
        $this->_jsFoot[++$this->_jsIndex] = $js;
        return $this;
    }

    public function addJSFile($path, $foot = false)
    {
        $this->_jsFiles[$foot ? 'foot' : 'head'][++$this->_jsIndex] = $path;
        return $this;
    }

    public function addJS($js)
    {
        $this->_js[++$this->_jsIndex] = $js;
        return $this;
    }

    function _getJSHTML()
    {
        $html = array('head' => array(), 'foot' => array());
        foreach (array_keys($this->_jsFiles) as $js_where) {
            foreach (array_keys($this->_jsFiles[$js_where]) as $i) {
                $html[$js_where][$i] = sprintf('<script type="text/javascript" src="%s"></script>', $this->_jsFiles[$js_where][$i]);
            }
        }
        foreach (array_keys($this->_jsHead) as $i) {
            $html['head'][$i] = '<script type="text/javascript">' . $this->_jsHead[$i] . '</script>';
        }
        foreach (array_keys($this->_jsFoot) as $i) {
            $html['foot'][$i] = '<script type="text/javascript">' . $this->_jsFoot[$i] . '</script>';
        }
        $js = array('<script type="text/javascript">');
        if (!empty($this->_jsHeadAjax)) {
            ksort($this->_jsHeadAjax);
            //$js[] = 'Ajax.Responders.register({onComplete: function() {';
            $js[] = 'jQuery.ajaxSetup({complete: function (XMLHttpRequest, textStatus) {';
            foreach (array_keys($this->_jsHeadAjax) as $i) {
                $js[] = $this->_jsHeadAjax[$i];
            }
            $js[] = '}})';
        }
        if (!empty($this->_js)) {
            ksort($this->_js);
            //$js[] = 'document.observe("dom:loaded", function() {';
            $js[] = 'jQuery(document).ready(function() {';
            foreach (array_keys($this->_js) as $i) {
                $js[] = $this->_js[$i];
            }
            $js[] = '})';
        }
        $js[] = '</script>';
        $html['head'][++$this->_jsIndex] = implode("\n", $js);
        $html_head = implode("\n", $html['head']);
        $html_foot = implode("\n", $html['foot']);
        return array($html_head, $html_foot);
    }

    public function addCSS($css, $index = null)
    {
        $index = empty($index) ? ++$this->_cssIndex : $index;
        $this->_css[$index] = $css;
        return $this;
    }

    public function addCSSFile($path, $media = 'screen', $id = null, $index = null)
    {
        $index = empty($index) ? ++$this->_cssIndex : $index;
        // Use id to prevent duplicates
        if (isset($id)) {
            if ($id_index = @$this->_cssFileIndices[$id]) {
                unset($this->_cssFiles[$id_index]);
            }
            $this->_cssFileIndices[$id] = $index;
        }
        $this->_cssFiles[$index] = array($path, $media);

        return $this;
    }

    function _getCSSHTML()
    {
        $html = array();
        foreach (array_keys($this->_cssFiles) as $i) {
            $html[$i] = sprintf('<link rel="stylesheet" type="text/css" media="%s" href="%s" />', $this->_cssFiles[$i][1], $this->_cssFiles[$i][0]);
        }
        foreach (array_keys($this->_css) as $i) {
            $html[$i] = implode("\n", array('<style type="text/css">', $this->_css[$i], '</style>'));
        }
        ksort($html);
        return implode("\n", $html);
    }

    public function addHTMLHead($head)
    {
        $this->_htmlHead[] = $head;
        return $this;
    }

    public function setHTMLHeadTitle($title)
    {
        $this->_htmlHeadTitle = $title;
        return $this;
    }

    public function setPageInfo($pageTitle, $pageUrl = array())
    {
        $this->_pageInfo[] = array('title' => $pageTitle, 'url' => $pageUrl);
        return $this;
    }

    public function popPageInfo()
    {
        return array_pop($this->_pageInfo);
    }

    public function clearPageInfo()
    {
        $this->_pageInfo = array();
        $this->_pageTitle = null;
        return $this;
    }

    public function setPageTitle($pageTitle)
    {
        $this->_pageTitle = $pageTitle;
        return $this;
    }
}