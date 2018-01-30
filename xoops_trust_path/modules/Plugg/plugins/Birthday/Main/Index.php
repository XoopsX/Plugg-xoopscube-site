<?php
class Plugg_Birthday_Main_Index extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        // Forward to current date page
        $date = getdate();
        $route = sprintf('%d/%d/%d', $date['year'], $date['mon'], $date['mday']);
        $this->forward($route, $context);
    }
}