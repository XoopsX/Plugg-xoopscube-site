<?php
class Plugg_Xigg_Model_Trackback extends Plugg_Xigg_Model_Base_Trackback
{
    function getHTMLLink($target = '_blank')
    {
        return sprintf('<a href="%s" target="%s">%s</a>', $this->get('url'), h($target), h(($title = $this->get('title')) ? $title : $this->get('url')));
    }
}

class Plugg_Xigg_Model_TrackbackRepository extends Plugg_Xigg_Model_Base_TrackbackRepository
{
}