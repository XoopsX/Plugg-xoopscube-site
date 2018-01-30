<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/Project/Model/Report.php
*/
abstract class Plugg_Project_Model_Base_Report extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Report', $model);
        $this->_vars = array('report_id' => 0, 'report_created' => 0, 'report_updated' => 0, 'report_data' => null, 'report_ip' => null, 'report_type' => 0, 'report_comment' => null, 'report_comment_html' => null, 'report_comment_filter_id' => 0, 'report_release_id' => null, 'report_userid' => null);
    }

    public function getUserId()
    {
        return $this->getVar('userid');
    }

    public function assignUser($user)
    {
        $this->_setVar('userid', $user->getId());
    }

    protected function _fetchUser($withData = false)
    {
        if (!isset($this->_objects['User'])) {
            $user_id = $this->getUserId();
            $identities = $this->_model->fetchUserIdentities(array($user_id), $withData);
            $this->_objects['User'] = $identities[$user_id];
        }
        return $this->_objects['User'];
    }

    public function isOwnedBy($user)
    {
        return $this->getUserId() == $user->getId();
    }

    public function getId()
    {
        return $this->getVar('id');
    }

    public function setId($value)
    {
        $this->setVar('id', $value);
    }

    public function getTimeCreated()
    {
        return $this->getVar('created');
    }

    public function getTimeUpdated()
    {
        return $this->getVar('updated');
    }

    public function assignRelease(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() != 'Release') return false;

        return $this->_assignEntity($entity, 'release_id');
    }

    public function unassignRelease()
    {
        return $this->_unassignEntity('Release', 'release_id');
    }

    protected function _fetchRelease()
    {
        return $this->_fetchEntity('Release', 'release_id');
    }

    protected function _getVar($name)
    {
        return $this->_vars['report_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['report_id'] = $value;
            break;
        case 'data':
            $this->_vars['report_data'] = trim($value);
            break;
        case 'ip':
            $this->_vars['report_ip'] = trim($value);
            break;
        case 'type':
            $this->_vars['report_type'] = $value;
            break;
        case 'comment':
            $this->_vars['report_comment'] = trim($value);
            break;
        case 'comment_html':
            $this->_vars['report_comment_html'] = trim($value);
            break;
        case 'comment_filter_id':
            $this->_vars['report_comment_filter_id'] = $value;
            break;
        case 'release_id':
            $this->_vars['report_release_id'] = $value;
            break;
        case 'userid':
            $this->_vars['report_userid'] = trim($value);
            break;
        default:
            trigger_error(sprintf('Error trying to set value for variable %s. This variable is either read-only or does not exist for this entity', $name), E_USER_WARNING);
            return false;
        }
        return true;
    }

    protected function _get($name, $sort, $order)
    {
        switch ($name) {
        case 'data':
            return $this->getVar('data');
        case 'ip':
            return $this->getVar('ip');
        case 'type':
            return $this->getVar('type');
        case 'comment':
            return $this->getVar('comment');
        case 'comment_html':
            return $this->getVar('comment_html');
        case 'comment_filter_id':
            return $this->getVar('comment_filter_id');
        case 'Release':
            return $this->_fetchRelease();
        case 'User':
            return $this->_fetchUser();
        case 'UserWithData':
            return $this->_fetchUser(true);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'data':
            $this->setVar('data', $value);
            break;
        case 'ip':
            $this->setVar('ip', $value);
            break;
        case 'type':
            $this->setVar('type', $value);
            break;
        case 'comment':
            $this->setVar('comment', $value);
            break;
        case 'comment_html':
            $this->setVar('comment_html', $value);
            break;
        case 'comment_filter_id':
            $this->setVar('comment_filter_id', $value);
            break;
        case 'Release':
            $entity = is_array($value) ? $value[0] : $value;
            $this->assignRelease($entity);
            break;
        }
    }

    public function initVar($name, $value)
    {
        switch ($name) {
        default:
            $this->_vars[$name] = $value;
            break;
        }
    }
}

abstract class Plugg_Project_Model_Base_ReportRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Report', $model);
    }
    public function fetchByUser($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('report_userid', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByUser($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('User', $id, $perpage, $sort, $order);
    }

    public function countByUser($id)
    {
        return $this->_countByForeign('report_userid', $id);
    }

    public function fetchByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('report_userid', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('User', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByUserAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('report_userid', $id, $criteria);
    }

    public function fetchByRelease($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('report_release_id', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByRelease($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('Release', $id, $perpage, $sort, $order);
    }

    public function countByRelease($id)
    {
        return $this->_countByForeign('report_release_id', $id);
    }

    public function fetchByReleaseAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('report_release_id', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByReleaseAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('Release', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByReleaseAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('report_release_id', $id, $criteria);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_Project_Model_Base_ReportsByRowset($rs, $this->_model->create('Report'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_Project_Model_Base_Reports($this->_model, $entities);
    }
}

class Plugg_Project_Model_Base_ReportsByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Reports', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_Project_Model_Base_Reports extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Reports', $entities);
    }
}