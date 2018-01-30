<?php
require_once 'Sabai/Application/ModelEntityController/List.php';

class Plugg_Project_Main_Project_ListImages extends Sabai_Application_ModelEntityController_List
{
    var $_sortBy = array('priority', 'DESC');

    function __construct()
    {
        parent::__construct('Image');
        $this->addFilter('filter');
    }

    function filterBeforeFilter(Sabai_Application_Context $context)
    {
        if ((!$project = $this->getRequestedProject($context)) || !$project->isReadable($context->user) || !$project->get('allow_images')) {
            $context->response->setError($context->plugin->_('Invalid request'));
            $context->response->send($this->_application);
        }
        if (!($context->user->hasPermission('project image add') || $project->isDeveloper($context->user))) {
            $context->response->setError($context->plugin->_('Invalid request'));
            return;
        }
        $this->_application->project = $project;
    }

    function filterAfterFilter(Sabai_Application_Context $context){}

    function _getRequestedSort($request)
    {
        if ($sort_by = $request->getAsStr('sortby')) {
            $sort_by = explode(',', $sort_by);
            if (count($sort_by) == 2) {
                $this->_sortBy = $sort_by;
            }
        }
        if ($this->_sortBy[0] == 'priority') {
            return array('priority');
        }
        return array($this->_sortBy[0], 'priority');
    }

    function _getRequestedOrder($request)
    {
        if ($this->_sortBy[0] != 'priority') {
            return array($this->_sortBy[1], 'DESC');
        }
        return array('DESC');
    }

    function _onListEntities($entities, Sabai_Application_Context $context)
    {
        $this->_application->setData(array('requested_sortby' => implode(',', $this->_sortBy)));
        $context->response->setPageInfo($context->plugin->_('Screenshots'));
        return $entities;
    }

    function _getCriteria(Sabai_Application_Context $context)
    {
        $project = $this->getRequestedProject($context);
        return $this->_getModel($context)->createCriteria('Image')->projectId_is($project->getId());
    }

    protected function _getModel(Sabai_Application_Context $context)
    {
        return $context->plugin->getModel();
    }
}
