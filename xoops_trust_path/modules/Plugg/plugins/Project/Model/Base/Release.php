<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/Project/Model/Release.php
*/
abstract class Plugg_Project_Model_Base_Release extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Release', $model);
        $this->_vars = array('release_id' => 0, 'release_created' => 0, 'release_updated' => 0, 'release_version' => null, 'release_stability' => 0, 'release_date' => 0, 'release_download_url' => null, 'release_note_url' => null, 'release_summary' => null, 'release_summary_html' => null, 'release_summary_filter_id' => 0, 'release_status' => 1, 'release_allow_reports' => 1, 'release_allow_download' => 1, 'release_project_id' => null, 'release_userid' => null, 'release_report_count' => 0, 'release_report_last' => 0, 'release_report_lasttime' => 0);
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

    public function getReportCount()
    {
        return $this->getVar('report_count');
    }

    public function hasLastReport()
    {
        $last_id = $this->getVar('report_last');
        return !empty($last_id);
    }

    public function assignProject(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() != 'Project') return false;

        return $this->_assignEntity($entity, 'project_id');
    }

    public function unassignProject()
    {
        return $this->_unassignEntity('Project', 'project_id');
    }

    protected function _fetchProject()
    {
        return $this->_fetchEntity('Project', 'project_id');
    }

    public function addReport(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() != 'Report') return false;

        return $this->_addEntity($entity);
    }

    public function removeReport(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() != 'Report') return;

        return $this->removeReportById($entity->getId());
    }

    public function removeReportById($id)
    {
        return $this->_removeEntityById('report_id', 'Report', $id);
    }

    public function createReport()
    {
        return $this->_createEntity('Report');
    }

    protected function _fetchReports($limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchEntities('Report', $limit, $offset, $sort, $order);
    }

    protected function _fetchLastReport()
    {
        if (!isset($this->_objects['LastReport']) && $this->hasLastReport()) {
            $this->_objects['LastReport'] = $this->_fetchEntities('Report', 1, 0, 'report_created', 'DESC')->getNext();
        }
        return $this->_objects['LastReport'];
    }

    public function paginateReports($perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateEntities('Report', $perpage, $sort, $order);
    }

    public function removeReports()
    {
        return $this->_removeEntities('Report');
    }

    public function countReports()
    {
        return $this->_countEntities('Report');
    }

    protected function _getVar($name)
    {
        return $this->_vars['release_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['release_id'] = $value;
            break;
        case 'version':
            $this->_vars['release_version'] = trim($value);
            break;
        case 'stability':
            $this->_vars['release_stability'] = $value;
            break;
        case 'date':
            $this->_vars['release_date'] = $value;
            break;
        case 'download_url':
            $this->_vars['release_download_url'] = trim($value);
            break;
        case 'note_url':
            $this->_vars['release_note_url'] = trim($value);
            break;
        case 'summary':
            $this->_vars['release_summary'] = trim($value);
            break;
        case 'summary_html':
            $this->_vars['release_summary_html'] = trim($value);
            break;
        case 'summary_filter_id':
            $this->_vars['release_summary_filter_id'] = $value;
            break;
        case 'status':
            $this->_vars['release_status'] = $value;
            break;
        case 'allow_reports':
            $this->_vars['release_allow_reports'] = $value;
            break;
        case 'allow_download':
            $this->_vars['release_allow_download'] = $value;
            break;
        case 'project_id':
            $this->_vars['release_project_id'] = $value;
            break;
        case 'userid':
            $this->_vars['release_userid'] = trim($value);
            break;
        case 'report_count':
            $this->_vars['release_report_count'] = $value;
            break;
        case 'report_last':
            $this->_vars['release_report_last'] = $value;
            break;
        case 'report_lasttime':
            $this->_vars['release_report_lasttime'] = $value;
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
        case 'version':
            return $this->getVar('version');
        case 'stability':
            return $this->getVar('stability');
        case 'date':
            return $this->getVar('date');
        case 'download_url':
            return $this->getVar('download_url');
        case 'note_url':
            return $this->getVar('note_url');
        case 'summary':
            return $this->getVar('summary');
        case 'summary_html':
            return $this->getVar('summary_html');
        case 'summary_filter_id':
            return $this->getVar('summary_filter_id');
        case 'status':
            return $this->getVar('status');
        case 'allow_reports':
            return $this->getVar('allow_reports');
        case 'allow_download':
            return $this->getVar('allow_download');
        case 'Project':
            return $this->_fetchProject();
        case 'Reports':
            return $this->_fetchReports(0, 0, $sort, $order);
        case 'LastReport':
            return $this->_fetchLastReport();
        case 'User':
            return $this->_fetchUser();
        case 'UserWithData':
            return $this->_fetchUser(true);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'version':
            $this->setVar('version', $value);
            break;
        case 'stability':
            $this->setVar('stability', $value);
            break;
        case 'date':
            $this->setVar('date', $value);
            break;
        case 'download_url':
            $this->setVar('download_url', $value);
            break;
        case 'note_url':
            $this->setVar('note_url', $value);
            break;
        case 'summary':
            $this->setVar('summary', $value);
            break;
        case 'summary_html':
            $this->setVar('summary_html', $value);
            break;
        case 'summary_filter_id':
            $this->setVar('summary_filter_id', $value);
            break;
        case 'status':
            $this->setVar('status', $value);
            break;
        case 'allow_reports':
            $this->setVar('allow_reports', $value);
            break;
        case 'allow_download':
            $this->setVar('allow_download', $value);
            break;
        case 'Project':
            $entity = is_array($value) ? $value[0] : $value;
            $this->assignProject($entity);
            break;
        case 'Reports':
            $this->removeReports();
            foreach (array_keys($value) as $i) {
                $this->addReport($value[$i]);
            }
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

abstract class Plugg_Project_Model_Base_ReleaseRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Release', $model);
    }
    public function fetchByUser($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('release_userid', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByUser($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('User', $id, $perpage, $sort, $order);
    }

    public function countByUser($id)
    {
        return $this->_countByForeign('release_userid', $id);
    }

    public function fetchByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('release_userid', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('User', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByUserAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('release_userid', $id, $criteria);
    }

    public function fetchByProject($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('release_project_id', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByProject($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('Project', $id, $perpage, $sort, $order);
    }

    public function countByProject($id)
    {
        return $this->_countByForeign('release_project_id', $id);
    }

    public function fetchByProjectAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('release_project_id', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByProjectAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('Project', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByProjectAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('release_project_id', $id, $criteria);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_Project_Model_Base_ReleasesByRowset($rs, $this->_model->create('Release'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_Project_Model_Base_Releases($this->_model, $entities);
    }
}

class Plugg_Project_Model_Base_ReleasesByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Releases', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_Project_Model_Base_Releases extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Releases', $entities);
    }
}