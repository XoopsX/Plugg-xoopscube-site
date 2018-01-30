<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/Xigg/Model/Vote.php
*/
abstract class Plugg_Xigg_Model_Base_Vote extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Vote', $model);
        $this->_vars = array('vote_id' => 0, 'vote_created' => 0, 'vote_updated' => 0, 'vote_score' => 0, 'vote_ip' => null, 'vote_node_id' => null, 'vote_userid' => null);
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

    public function assignNode(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() != 'Node') return false;

        return $this->_assignEntity($entity, 'node_id');
    }

    public function unassignNode()
    {
        return $this->_unassignEntity('Node', 'node_id');
    }

    protected function _fetchNode()
    {
        return $this->_fetchEntity('Node', 'node_id');
    }

    protected function _getVar($name)
    {
        return $this->_vars['vote_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['vote_id'] = $value;
            break;
        case 'score':
            $this->_vars['vote_score'] = $value;
            break;
        case 'ip':
            $this->_vars['vote_ip'] = trim($value);
            break;
        case 'node_id':
            $this->_vars['vote_node_id'] = $value;
            break;
        case 'userid':
            $this->_vars['vote_userid'] = trim($value);
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
        case 'score':
            return $this->getVar('score');
        case 'ip':
            return $this->getVar('ip');
        case 'Node':
            return $this->_fetchNode();
        case 'User':
            return $this->_fetchUser();
        case 'UserWithData':
            return $this->_fetchUser(true);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'score':
            $this->setVar('score', $value);
            break;
        case 'ip':
            $this->setVar('ip', $value);
            break;
        case 'Node':
            $entity = is_array($value) ? $value[0] : $value;
            $this->assignNode($entity);
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

abstract class Plugg_Xigg_Model_Base_VoteRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Vote', $model);
    }
    public function fetchByUser($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('vote_userid', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByUser($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('User', $id, $perpage, $sort, $order);
    }

    public function countByUser($id)
    {
        return $this->_countByForeign('vote_userid', $id);
    }

    public function fetchByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('vote_userid', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByUserAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('User', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByUserAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('vote_userid', $id, $criteria);
    }

    public function fetchByNode($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeign('vote_node_id', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByNode($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('Node', $id, $perpage, $sort, $order);
    }

    public function countByNode($id)
    {
        return $this->_countByForeign('vote_node_id', $id);
    }

    public function fetchByNodeAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByForeignAndCriteria('vote_node_id', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function paginateByNodeAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('Node', $id, $criteria, $perpage, $sort, $order);
    }

    public function countByNodeAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByForeignAndCriteria('vote_node_id', $id, $criteria);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_Xigg_Model_Base_VotesByRowset($rs, $this->_model->create('Vote'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_Xigg_Model_Base_Votes($this->_model, $entities);
    }
}

class Plugg_Xigg_Model_Base_VotesByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Votes', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_Xigg_Model_Base_Votes extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Votes', $entities);
    }
}