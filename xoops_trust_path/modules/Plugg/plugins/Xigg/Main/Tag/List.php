<?php
class Plugg_Xigg_Main_Tag_List extends Sabai_Application_Controller
{
    protected function _doExecute(Sabai_Application_Context $context)
    {
        $cache = $context->plugin->getCache();
        if ($data = $cache->get('Main_Tag_List')) {
            $data = unserialize($data);
        } else {
            $data = $context->plugin->buildTagCloud();
            $cache->save(serialize($data), 'Main_Tag_List');
        }
        $this->_application->tags = $data;
    }
}