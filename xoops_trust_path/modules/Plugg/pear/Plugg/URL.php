<?php
class Plugg_URL extends Sabai_Application_URL
{
    public function __construct($baseUrl, $baseScript, $routeParam)
    {
        parent::__construct($baseUrl, $baseScript, $routeParam);
        $this->setScriptAlias('main', 'index.php');
        $this->setScriptAlias('admin', 'admin.php');
    }

    public function getImageUrl($plugin, $file, $dir = '', $separator = '&amp;')
    {
        return $this->create(array(
            'base' => '',
            'script' => 'image.php',
            'params' => array(
                'plugin' => $plugin,
                'file' => $file,
                'dir' => $dir
            ),
            'separator' => $separator
        ));
    }

    public function getCssUrl($plugin, $file = '', $separator = '&amp;')
    {
        return $this->create(array(
            'base' => '',
            'script' => 'css.php',
            'params' => array(
                'plugin' => $plugin,
                'file' => $file
            ),
            'separator' => $separator
        ));
    }

    public function getJsUrl($plugin, $file, $separator = '&amp;')
    {
        return $this->create(array(
            'base' => '',
            'script' => 'js.php',
            'params' => array(
                'plugin' => $plugin,
                'file' => $file
            ),
            'separator' => $separator
        ));
    }

    public function getMainUrl(array $parts)
    {
        return $this->create(array_merge($parts, array('script_alias' => 'main')));
    }

    public function getAdminUrl(array $parts)
    {
        return $this->create(array_merge($parts, array('script_alias' => 'admin')));
    }
}