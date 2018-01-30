<?php
class Sabai_Application_URL
{
    private $_baseUrl;
    private $_baseScript;
    private $_routeBase = '';
    private $_modRewrite = false;
    private $_modRewriteFormat;
    private $_routeParam;
    private $_scriptAlias = array();

    public function __construct($baseUrl, $baseScript, $routeParam)
    {
        $this->_baseUrl = $baseUrl;
        $this->_baseScript = $baseScript;
        $this->_routeParam = $routeParam;
    }

    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    public function getBaseScript()
    {
        return $this->_baseScript;
    }

    public function getScriptUrl()
    {
        return $this->_baseUrl . '/' . $this->_baseScript;
    }

    public function setRouteBase($routeBase)
    {
        $this->_routeBase = $routeBase;
        return $this;
    }

    public function getRouteBase()
    {
        return $this->_routeBase;
    }

    public function getRouteParam()
    {
        return $this->_routeParam;
    }

    public function useModRewrite($flag = true)
    {
        $this->_modRewrite = $flag;
        return $this;
    }

    public function setModRewrite($modRewriteFormat, $url)
    {
        $this->_modRewriteFormat[$url] = $modRewriteFormat;
        return $this;
    }

    public function setScriptAlias($alias, $script)
    {
        $this->_scriptAlias[$alias] = $script;
        return $this;
    }

    public function getScriptAlias($alias)
    {
        return $this->_scriptAlias[$alias];
    }

    /**
     * Creates an application URL from an array of options.
     *
     * @param array $options
     * @return string
     */
    public function create($options = array())
    {
        $default = array(
            'base' => $this->_routeBase,
            'path' => '',
            'params' => array(),
            'fragment' => '',
            'script' => $this->_baseScript,
            'separator' => '&amp;',
            'script_alias' => null
        );
        $options = array_merge($default, $options);
        if (($alias = $options['script_alias']) && isset($this->_scriptAlias[$alias])) {
            $url = $this->_baseUrl . '/' . $this->_scriptAlias[$alias];
        } else {
            $url = $this->_baseUrl . '/' . $options['script'];
        }
        if (($route = $options['base'] . $options['path']) &&
            (!$this->_modRewrite || !isset($this->_modRewriteFormat[$url]))
        ) {
            $params = array_merge($options['params'], array($this->_routeParam => $route));
        } else {
            $params = $options['params'];
        }
        if ($query_str = http_build_query($params, null, $options['separator'])) {
            $query = '?' . $query_str;
        } else {
            $query = '';
        }
        if (isset($this->_modRewriteFormat[$url])) {
            $url = sprintf($this->_modRewriteFormat[$url], $route, $query_str, $query);
        } else {
            $url .= $query;
        }
        return !empty($options['fragment']) ? $url . '#' . $options['fragment'] : $url;
    }
}