<?php
require_once 'Sabai/Response/Web.php';
require_once 'Plugg/Template.php';

class Plugg_Response extends Sabai_Response_Web
{
    private $_displayRawMessage = false;
    private $_tabs = array();
    private $_currentTabSet = 0;
    private $_currentTab;
    private $_tabPageInfo = array();
    private $_tabPageTitle;
    private $_loginRequiredError = false;

    public function __construct(Plugg_Template $template = null)
    {
        if (is_null($template)) $template = new Plugg_Template();
        parent::__construct($template);
    }

    public function getCurrentTabSet()
    {
        return $this->_currentTabSet;
    }

    public function addTabSet($tabs)
    {
        ++$this->_currentTabSet;
        foreach ($tabs as $tab_name => $tab_data) {
            $this->_tabs[$this->_currentTabSet][$tab_name] = array(
                'title' => $tab_data['title'],
                'url' => $tab_data['url'],
                'ajax' => $tab_data['ajax']
            );
        }
        return $this;
    }

    public function removeTabSet()
    {
        unset(
            $this->_tabs[$this->_currentTabSet],
            $this->_currentTab[$this->_currentTabSet],
            $this->_tabPageInfo[$this->_currentTabSet],
            $this->_tabPageTitle[$this->_currentTabSet]
        );
        --$this->_currentTabSet;
        return $this;
    }

    public function setCurrentTab($tabName)
    {
        if (isset($this->_tabs[$this->_currentTabSet][$tabName])) {
            $this->_currentTab[$this->_currentTabSet] = $tabName;
        }
        return $this;
    }

    public function setPageInfo($pageTitle, $pageUrl = array(), $ajax = false)
    {
        if (empty($this->_tabs) || empty($this->_currentTab)) {
            return parent::setPageInfo($pageTitle, $pageUrl);
        }

        return $this->setTabPageInfo($this->_currentTabSet, $pageTitle, $pageUrl, $ajax);
    }

    public function popPageInfo()
    {
        if (empty($this->_tabs) || empty($this->_currentTab)) {
            return parent::popPageInfo();
        }

        return array_pop($this->_tabPageInfo[$this->_currentTabSet]);
    }

    public function setPageTitle($pageTitle)
    {
        if (empty($this->_tabs) || empty($this->_currentTab)) {
            return parent::setPageTitle($pageTitle);
        }

        $this->_tabPageTitle[$this->_currentTabSet] = $pageTitle;

        return $this;
    }

    public function setTabPageInfo($tabSet, $pageTitle, $pageUrl = array(), $ajax = false)
    {
        $this->_tabPageInfo[$tabSet][] = array(
            'title' => $pageTitle,
            'url' => $pageUrl,
            'ajax' => $ajax
        );

        return $this;
    }

    public function clearTabPageInfo()
    {
        $this->_tabPageInfo = array();
        $this->_tabPageTitle = null;

        return $this;
    }

    protected function _sendContent(Sabai_Application $application, $contentNames, $vars)
    {
        if (!empty($this->_currentTab)) {
            $vars['TAB_CURRENT'] = $this->_currentTab;
            foreach ($this->_currentTab as $tab_set => $current_tab) {
                $vars['TABS'][$tab_set] = $this->_tabs[$tab_set];
                $vars['TAB_PAGE_TITLE'][$tab_set] = isset($this->_tabPageTitle[$tab_set]) ? $this->_tabPageTitle[$tab_set] : null;
                $vars['TAB_PAGE_BREADCRUMBS'][$tab_set] = array();
                if (isset($this->_tabPageInfo[$tab_set]) && count($this->_tabPageInfo[$tab_set]) > 1) {
                    $page_info_last = array_pop($this->_tabPageInfo[$tab_set]);
                    if (!isset($vars['TAB_PAGE_TITLE'][$tab_set])) {
                        $vars['TAB_PAGE_TITLE'][$tab_set] = $page_info_last['title'];
                    }
                    $vars['TAB_PAGE_BREADCRUMBS'][$tab_set] = $this->_tabPageInfo[$tab_set];
                }
            }
        }

        parent::_sendContent($application, $contentNames, $vars);
    }

    public function setDisplayRawMessage($flag = true)
    {
        $this->_displayRawMessage = $flag;
    }

    protected function _displayMessages($messages)
    {
        if ($this->_displayRawMessage) {
            foreach ($messages as $message) {
                echo $message[0];
            }

            return;
        }

        $html = array();
        foreach ($messages as $message) {
            switch ($message[1]) {
                case Sabai_Response::MESSAGE_ERROR:
                    $html[] = '<div class="stop">';
                    break;
                case Sabai_Response::MESSAGE_WARNING:
                    $html[] = '<div class="warning">';
                    break;
                case Sabai_Response::MESSAGE_INFO:
                    $html[] = '<div class="info fadeout">';
                    break;
                default:
                    $html[] = '<div class="go fadeout">';
            }
            $html[] = '<p>';
            $html[] = $message[0];
            $html[] = '</p>';
            $html[] = '</div>';
        }

        echo implode("\n", $html);
    }

    public function setLoginRequiredError($flag = true)
    {
        $this->_loginRequiredError = $flag;
        $this->setError(); // Only set the status as error. Error message and URL will be set later upon send().
    }

    public function send(Sabai_Application $application, $exit = true)
    {
        if ($this->isError() && $this->_loginRequiredError) {
            $login_url = array(
                'script_alias' => 'main',
                'base' => '/user/login',
                'params' => array('return' => 1)
            );
            $this->setError(
                sprintf(
                    $application->getGettext()->_('You must <a href="%s">login</a> to perform this operation'),
                    $application->createUrl($login_url)
                ),
                $login_url
            );
        }

        parent::send($application, $exit);
    }
}