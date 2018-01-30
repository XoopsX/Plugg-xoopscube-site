<?php
class Plugg_Project_Model_Project extends Plugg_Project_Model_Base_Project
{
    function isReadable($user)
    {
        if (!$this->isApproved() && !$user->hasPermission('project approve')) {
            return false;
        }
        if ($this->isHidden() && !$user->hasPermission('project hide')) {
            return false;
        }
        return true;
    }

    function addViewCount()
    {
        if (!isset($_SESSION['Plugg_Project_Model_Project_views'][$this->getId()])) {
            $_SESSION['Plugg_Project_Model_Project_views'][$this->getId()] = 1;
            $this->set('views', $this->get('views') + 1);
            $this->commit();
        }
    }

    function isApproved()
    {
        return $this->get('status') == Plugg_Project_Plugin::PROJECT_STATUS_APPROVED;
    }

    function setApproved()
    {
        $this->set('status', Plugg_Project_Plugin::PROJECT_STATUS_APPROVED);
        // approve all pending project releases
        if ($this->getReleaseCount()) {
            foreach ($this->Releases as $release) {
                if (!$release->isApproved()) $release->setApproved();
            }
        }
        if ($this->getCommentCount()) {
            foreach ($this->Comments as $comment) {
                if (!$comment->isApproved()) $comment->setApproved();
            }
        }
        if ($this->getLinkCount()) {
            foreach ($this->Links as $link) {
                if (!$link->isApproved()) $link->setApproved();
            }
        }
    }

    function setPending()
    {
        $this->set('status', Plugg_Project_Plugin::PROJECT_STATUS_PENDING);
    }

    function ishidden()
    {
        return (bool)$this->get('hidden');
    }

    function setHidden()
    {
        $this->set('hidden', 1);
    }

    function unsetHidden()
    {
        $this->set('hidden', 0);
    }

    function isDeveloper($user, $approved = true)
    {
        if (!$user->isAuthenticated()) return false;
        foreach ($this->Developers as $dev) {
            if ($dev->getUserId() == $user->getId()) {
                if ($approved) {
                    if ($dev->isApproved()) return $dev->get('role');
                } else {
                    return $dev->get('role');
                }
            }
        }
        return false;
    }

    function getRatingStr(Plugg_Template $tpl)
    {
        $ret = '';
        if ($this->getCommentCount() == 0) {
            return $ret;
        }
        $comment_rating = $this->get('comment_rating');
        for ($i = 1; $i <= $comment_rating; $i++) {
            $ret .= sprintf('<img src="%s" width="16" height="16" alt="star" />', $tpl->URL->getImageUrl($tpl->Plugin->getLibrary(), 'star.gif'));
        }
        if ($i < 5) {
            if ($comment_rating_decimal = round(10 * ($comment_rating - $i + 1))) {
                if ($comment_rating_decimal == 10) {

                } elseif ($comment_rating_decimal > 7) {
                    ++$i;
                    $ret .= sprintf('<img src="%s" width="16" height="16" alt="star" />', $tpl->URL->getImageUrl($tpl->Plugin->getLibrary(), 'star.gif'));
                } elseif ($comment_rating_decimal > 2) {
                    ++$i;
                    $ret .= sprintf('<img src="%s" width="16" height="16" alt="half star" />', $tpl->URL->getImageUrl($tpl->Plugin->getLibrary(), 'star_half.gif'));
                }
            }
        }
        for (; $i <= 5; $i++) {
            $ret .= sprintf('<img src="%s" width="16" height="16" alt="empty star" />', $tpl->URL->getImageUrl($tpl->Plugin->getLibrary(), 'star_empty.gif'));
        }
        return $ret;
    }

    function getLatestRelease()
    {
        if (!isset($this->_objects['LatestRelease'])) {
            if (!$release_id = $this->get('release_latest')) {
                return false;
            }
            $this->_objects['LatestRelease'] = $this->_model->Release->fetchById($release_id);
        }
        return $this->_objects['LatestRelease'];
    }

    function getFeaturedImage()
    {
        if (!isset($this->_objects['FeaturedImage'])) {
            if (!$image_id = $this->get('image_featured')) {
                return false;
            }
            $this->_objects['FeaturedImage'] = $this->_model->Image->fetchById($image_id);
        }
        return $this->_objects['FeaturedImage'];
    }

    function updateLatestRelease()
    {
        $releases = $this->_model->Release
            ->criteria()
            ->status_is(Plugg_Project_Plugin::RELEASE_STATUS_APPROVED)
            ->fetchByProject($this->getId(), 1, 0, array('release_date', 'release_created'), array('DESC', 'DESC'));
        if ($releases->count() > 0 && ($latest = $releases->getFirst())) {
            $release_latest = $latest->getId();
            $lastupdate = $latest->get('date') > $this->getTimeCreated() ? $latest->get('date') : $this->getTimeCreated();
        } else {
            $release_latest = 0;
            $lastupdate = $this->getTimeCreated();
        }
        if ($this->get('lastupdate') != $lastupdate) $this->set('lastupdate', $lastupdate);
        if ($this->get('release_latest') != $release_latest) $this->set('release_latest', $release_latest);
        return $this->commit();
    }

    function updateFeaturedImage()
    {
        $images = $this->_model->Image
            ->fetchByProject($this->getId(), 1, 0, array('image_priority', 'image_created'), array('DESC', 'DESC'));
        if ($images->count() > 0 && ($image = $images->getFirst())) {
            $image_featured = $image->getId();
        } else {
            $image_featured = 0;
        }
        if ($this->get('image_featured') != $image_featured) $this->set('image_featured', $image_featured);
        return $this->commit();
    }

    function updateCommentRating()
    {
        if ($this->getCommentCount() == 0) {
            if ($this->get('comment_rating') == 0) return true;
            $rating = 0;
        } else {
            if (false === $rating = $this->_model->getGateway('Comment')->getRatingSumAndCountByProjectId($this->getId())) {
                return false;
            }
            $rating = round($rating[0] / $rating[1], 1);
        }
        $this->set('comment_rating', $rating);
        return $this->commit();
    }

    function getViewableReleaseCount($user, $isDeveloper)
    {
        if ($isDeveloper || $user->hasPermission('project release approve')) {
            return $this->getReleaseCount();
        }
        $criteria = Sabai_Model_Criteria::createValue('release_status', Plugg_Project_Plugin::RELEASE_STATUS_APPROVED);
        return $this->_model->Release->countByProjectAndCriteria($this->getId(), $criteria);
    }

    function getViewableDeveloperCount($user, $isDeveloper)
    {
        if ($isDeveloper || $user->hasPermission('project developer approve')) {
            return $this->getDeveloperCount();
        }
        $criteria = Sabai_Model_Criteria::createValue('developer_status', Plugg_Project_Plugin::DEVELOPER_STATUS_APPROVED);
        return $this->_model->Developer->countByProjectAndCriteria($this->getId(), $criteria);
    }

    function setData($data)
    {
        $this->set('data', serialize($data));
    }

    function getData()
    {
        if ($data = $this->get('data')) {
            return unserialize($data);
        }
        return array();
    }

    function getDataHumanReadable($elements, $html = true)
    {
        $ret = array();
        $data = $this->getData();
        foreach (array_keys($elements) as $k) {
            if (!isset($data[$k])) continue;
            $value = $data[$k];
            switch ($elements[$k]['type']) {
                case 'url':
                    $value = $html && !empty($value) ? sprintf('<a href="%1$s">%1$s</a>', h($value)) : $value;
                    break;
                case 'select':
                    $value = isset($elements[$k]['options'][$value]) ? $elements[$k]['options'][$value] : '';
                    if ($html) $value = h($value);
                    break;
                case 'select_multi':
                    $values = array();
                    foreach ((array)$value as $_value) {
                        if (isset($elements[$k]['options'][$_value])) $values[] = $elements[$k]['options'][$_value];
                    }
                    $value = implode(', ', $values);
                    if ($html) $value = h($value);
                   break;
                default:
            }
            $ret[$elements[$k]['label']] = $value;
        }
        return $ret;
    }
}

class Plugg_Project_Model_ProjectRepository extends Plugg_Project_Model_Base_ProjectRepository
{
}