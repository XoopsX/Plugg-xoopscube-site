<?php
class Plugg_Project_Model_Link extends Plugg_Project_Model_Base_Link
{
    function isApproved()
    {
        return $this->get('status') == Plugg_Project_Plugin::LINK_STATUS_APPROVED;
    }

    function setApproved()
    {
        $this->set('status', Plugg_Project_Plugin::LINK_STATUS_APPROVED);
    }

    function setPending()
    {
        $this->set('status', Plugg_Project_Plugin::LINK_STATUS_PENDING);
    }

    function updateScore()
    {
        if ($this->getLinkvoteCount() == 0) {
            return array(0, 0);
        }
        if (false !== $score = $this->_model->getGateway('Linkvote')->getSumAndCountByLinkId($this->getId())) {
            if (0 > $link_score = $score[0] - ($score[1] - $score[0])) {
                $link_score = 0;
            }
            $this->set('score', $link_score);
            if ($this->commit()) {
                return array($link_score, $score[1]);
            }
        }
        return false;
    }

    function getScreenshot()
    {
        return sprintf('<img src="http://mozshot.nemui.org/shot?img_x=120:img_y=120;effect=true;uri=%1$s" width="120" height="120" alt="%1$s" />', urlencode($this->get('url')));
    }
}

class Plugg_Project_Model_LinkRepository extends Plugg_Project_Model_Base_LinkRepository
{
}