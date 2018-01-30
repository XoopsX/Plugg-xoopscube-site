<?php
require_once 'Sabai/User/IdentityFetcher.php';

class Plugg_User_IdentityFetcher extends Sabai_User_IdentityFetcher
{
    protected $_pluginHandle;
    protected $_db;

    public function __construct(Sabai_Handle $pluginHandle, Sabai_DB $db)
    {
        $this->_pluginHandle = $pluginHandle;
        $this->_db = $db;
        $this->_idField = 'Id';
        $this->_usernameField = 'Username';
        $this->_nameField = 'Name';
        $this->_emailField = 'Email';
        $this->_urlField = 'Url';
    }

    protected function _doFetchUserIdentities($userIds, $withData = false)
    {
        $identities = $this->_pluginHandle->instantiate()->userFetchIdentitiesByIds($userIds);
        if ($withData) $this->loadIdentitiesWithData($identities);
        return $identities;
    }

    protected function _doFetchIdentities($limit, $offset, $sort, $order)
    {;
        $method = 'userFetchIdentitiesSortby' . $sort;
        return $this->_pluginHandle->instantiate()->$method($limit, $offset, $order);
    }

    public function countIdentities()
    {
        return $this->_pluginHandle->instantiate()->userCountIdentities();
    }

    protected function _doFetchUserIdentityByUsername($userName, $withData = false)
    {
        $identity = $this->_pluginHandle->instantiate()->userFetchIdentityByUsername($userName);
        if ($withData) $this->_loadIdentityWithData($identity);
        return $identity;
    }

    protected function _doFetchUserIdentityByEmail($email, $withData = false)
    {
        $identity = $this->_pluginHandle->instantiate()->userFetchIdentityByEmail($email);
        if ($withData) $this->_loadIdentityWithData($identity);
        return $identity;
    }

    public function loadIdentityWithData(Sabai_User_Identity $identity)
    {
        $table_prefix = $this->_db->getResourcePrefix();
        $id = $identity->getId();
        $sql = sprintf('SELECT extra_data FROM %sextra WHERE extra_userid = %d', $table_prefix, $id);
        if ($rs = $this->_db->query($sql)) {
            if ($data_chunk = $rs->fetchSingle()) {
                if ($data = unserialize($data_chunk)) {
                    $identity->setData($data);
                }
            }
        }
    }

    public function loadIdentitiesWithData(array $identities)
    {
        $table_prefix = $this->_db->getResourcePrefix();
        $ids_str = implode(',', array_keys($identities));
        $sql = sprintf('SELECT extra_userid, extra_data FROM %sextra WHERE extra_userid IN (%s)', $table_prefix, $ids_str);
        if ($rs = $this->_db->query($sql)) {
            while ($row = $rs->fetchRow()) {
                if ($data = unserialize($row[1])) {
                    $identities[$row[0]]->setData($data);
                }
            }
        }
    }
}