<?php
class Plugg_Footprint_User_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $sortby_allowed = array(
            'timestamp,DESC' => $context->plugin->_('Newest first'),
            'timestamp,ASC' => $context->plugin->_('Oldest first'),
        );
        $sortby_requested = $context->request->getAsStr('sortby', 'timestamp,DESC', array_keys($sortby_allowed));

        $sortby = explode(',', $sortby_requested);
        $pages = $context->plugin->getModel()->Footprint
            ->criteria()
            ->hidden_is(0)
            ->target_is($this->_application->identity->getId())
            ->paginate(20, 'footprint_' . $sortby[0], $sortby[1]);
        $page = $pages->getValidPage($context->request->getAsInt('page', 1));

        $this->_application->setData(array(
            'footprints' => $page->getElements()->with('User'),
            'pages' => $pages,
            'page' => $page,
            'sortby' => $sortby_requested,
            'sortby_allowed' => $sortby_allowed,
        ));
    }
}