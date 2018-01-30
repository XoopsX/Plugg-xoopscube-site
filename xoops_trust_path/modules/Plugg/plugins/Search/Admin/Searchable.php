<?php
class Plugg_Search_Admin_Searchable extends Plugg_RoutingController
{
    private $_searchable;
    private $_searchablePlugin;

    public function __construct()
    {
        parent::__construct('View', 'Plugg_Search_Admin_Searchable_', dirname(__FILE__) . '/Searchable');
        $this->addFilter('_isValidSearchableRequested');
    }

    protected function _isValidSearchableRequestedBeforeFilter($context)
    {
        $this->_searchable = $this->isValidEntityRequested($context, 'Searchable', 'searchable_id');
        if (!$this->_searchablePlugin = $this->_application->getPlugin($this->_searchable->get('plugin'))) {
            $context->response->setError($context->plugin->_('Invalid request'));
            $context->response->send($this->_application);
        }

        $context->response->setPageInfo(
            sprintf($this->_searchablePlugin->searchGetNicename($this->_searchable->name), $this->_searchablePlugin->getNicename()),
            array('path' => '/' . $this->_searchable->getId())
        );
    }

    protected function _isValidSearchableRequestedAfterFilter($context){}

    protected function _getRoutes($context)
    {
        return array(
            'import' => array(
                'controller' => 'Import',
            ),
        );
    }

    public function getSearchable()
    {
        return $this->_searchable;
    }

    public function getSearchablePlugin()
    {
        return $this->_searchablePlugin;
    }
}