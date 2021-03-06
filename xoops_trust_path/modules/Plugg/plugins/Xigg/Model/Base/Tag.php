<?php
/*
This file has been generated by the Sabai scaffold script. Do not edit this file directly.
If you need to customize the class, use the following file:
plugins/Xigg/Model/Tag.php
*/
abstract class Plugg_Xigg_Model_Base_Tag extends Sabai_Model_Entity
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Tag', $model);
        $this->_vars = array('tag_id' => 0, 'tag_created' => 0, 'tag_updated' => 0, 'tag_name' => null);
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

    public function getLabel()
    {
        return $this->getVar('name');
    }

    public function linkNode(Sabai_Model_Entity $entity)
    {
        if ($entity->getName() == 'Node') return $this->linkNodeById($entity->getId());

        return false;
    }

    public function linkNodeById($id)
    {
        return $this->_linkEntityById('Node2tag', 'node_id', $id);
    }

    public function unlinkNode(Sabai_Model_entity $entity)
    {
        if ($entity->getName() != 'Node') return 0;

        return $this->unlinkNodeById($entity->getId());
    }

    public function unlinkNodeById($id)
    {
        return $this->_unlinkEntityById('Node2tag', 'node2tag_tag_id', 'node2tag_node_id', $id);
    }

    public function unlinkNodes()
    {
        return $this->_unlinkEntities('Node2tag');
    }

    protected function _fetchNodes($limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchEntities('Node', $limit, $offset, $sort, $order);
    }

    public function paginateNodes($perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateEntities('Node', $perpage, $sort, $order);
    }

    public function countNodes()
    {
        return $this->_countEntities('Node');
    }

    protected function _getVar($name)
    {
        return $this->_vars['tag_' . $name];
    }

    protected function _setVar($name, $value)
    {
        switch ($name) {
        case 'id':
            $this->_vars['tag_id'] = $value;
            break;
        case 'name':
            $this->_vars['tag_name'] = trim($value);
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
        case 'name':
            return $this->getVar('name');
        case 'Nodes':
            return $this->_fetchNodes(0, 0, $sort, $order);
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
        case 'name':
            $this->setVar('name', $value);
            break;
        case 'Nodes':
            $this->unlinkNodes();
            foreach (array_keys($value) as $i) {
                if (is_object($value[$i])) {
                    $this->linkNode($value[$i]);
                } else {
                    $this->linkNodeById($value[$i]);
                }
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

abstract class Plugg_Xigg_Model_Base_TagRepository extends Sabai_Model_EntityRepository
{
    public function __construct(Sabai_Model $model)
    {
        parent::__construct('Tag', $model);
    }

    public function fetchByNode($id, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByAssoc('tag', 'Node2tag', 'node2tag_node_id', $id, $limit, $offset, $sort, $order);
    }

    public function paginateByNode($id, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntity('Node', $id, $perpage, $sort, $order);
    }

    public function countByNode($id)
    {
        return $this->_countByAssoc('tag_id', 'Node2tag', 'node2tag_node_id', $id);
    }

    public function fetchByNodeAndCriteria($id, Sabai_Model_Criteria $criteria, $limit = 0, $offset = 0, $sort = null, $order = null)
    {
        return $this->_fetchByAssocAndCriteria('tag', 'Node2tag', 'node2tag_node_id', $id, $criteria, $limit, $offset, $sort, $order);
    }

    public function countByNodeAndCriteria($id, Sabai_Model_Criteria $criteria)
    {
        return $this->_countByAssocAndCriteria('tag_id', 'Node2tag', 'node2tag_node_id', $id, $criteria);
    }

    public function paginateByNodeAndCriteria($id, Sabai_Model_Criteria $criteria, $perpage = 10, $sort = null, $order = null)
    {
        return $this->_paginateByEntityAndCriteria('Node', $id, $criteria, $perpage, $sort, $order);
    }

    protected function _getCollectionByRowset(Sabai_DB_Rowset $rs)
    {
        return new Plugg_Xigg_Model_Base_TagsByRowset($rs, $this->_model->create('Tag'), $this->_model);
    }

    public function createCollection(array $entities = array())
    {
        return new Plugg_Xigg_Model_Base_Tags($this->_model, $entities);
    }
}

class Plugg_Xigg_Model_Base_TagsByRowset extends Sabai_Model_EntityCollection_Rowset
{
    public function __construct(Sabai_DB_Rowset $rs, Sabai_Model_Entity $emptyEntity, Sabai_Model $model)
    {
        parent::__construct('Tags', $rs, $emptyEntity, $model);
    }

    protected function _loadRow(Sabai_Model_Entity $entity, array $row)
    {
        $entity->initVars($row);
    }
}

class Plugg_Xigg_Model_Base_Tags extends Sabai_Model_EntityCollection_Array
{
    public function __construct(Sabai_Model $model, array $entities = array())
    {
        parent::__construct($model, 'Tags', $entities);
    }
}