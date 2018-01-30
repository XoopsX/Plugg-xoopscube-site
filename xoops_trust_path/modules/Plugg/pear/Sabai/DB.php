<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: LGPL
 *
 * @category   Sabai
 * @package    Sabai_DB
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      File available since Release 0.1.1
*/

/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @category   Sabai
 * @package    Sabai_DB
 * @copyright  Copyright (c) 2006 myWeb Japan (http://www.myweb.ne.jp/)
 * @author     Kazumi Ono <onokazu@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-license.php GNU LGPL
 * @version    CVS: $Id:$
 * @link
 * @since      Class available since Release 0.1.1
 */
abstract class Sabai_DB
{
    /**
     * @var Sabai_DB_Connection
     */
    protected $_connection;
    /**
     * @var string
     */
    protected $_resourcePrefix;

    /**
     * Createa an instance of Sabai_DB
     *
     * @param Sabai_DB_Connection $connection
     * @param string $tablePrefix
     * @return Sabai_DB
     */
    public static function factory(Sabai_DB_Connection $connection, $tablePrefix)
    {
        $scheme = $connection->getScheme();
        $class = 'Sabai_DB_' . $scheme;
        if (!class_exists($class, false)) {
            $file = 'Sabai/DB/' . $scheme . '.php';
            require $file;
        }
        $db = new $class($connection);
        $db->setResourcePrefix($tablePrefix);
        return $db;
    }

    /**
     * Constructor
     *
     * @param Sabai_DB_Connection $connection
     */
    protected function __construct(Sabai_DB_Connection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Gets the name of database scheme
     *
     * @return resource
     */
    public function getScheme()
    {
        return $this->_connection->getScheme();
    }

    /**
     * Gets the resource handle of datasource
     *
     * @return resource
     */
    public function getResourceId()
    {
        return $this->_connection->getResourceId();
    }

    /**
     * Gets the name of datasource
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->_connection->getResourceName();
    }

    /**
     * Gets the name of prefix used in datasource
     *
     * @return string
     */
    public function getResourcePrefix()
    {
        return $this->_resourcePrefix;
    }

    /**
     * Sets the name of prefix used in datasource
     *
     * @param string $prefix
     */
    public function setResourcePrefix($prefix)
    {
        $this->_resourcePrefix = $prefix;
    }

    /**
     * Gets the string representation of db connection used
     *
     * @return string
     */
    public function getDSN()
    {
        return $this->_connection->getDSN();
    }

    /**
     * Checks whether triggers can be used
     *
     * @return bool
     */
    public function isTriggerEnabled()
    {
        return true;
    }

    /**
     * Returns optional config varaibles for creating database tables, used by MDB2_Schema
     *
     * @return array
     */
    public function getMDB2CreateTableOptions()
    {
        return array();
    }

    abstract public function beginTransaction();
    abstract public function commit();
    abstract public function rollback();
    abstract public function query($sql, $limit = 0, $offset = 0);
    abstract public function exec($sql, $useAffectedRows = true);
    abstract public function affectedRows();
    abstract public function lastInsertId($tableName, $keyName);
    abstract public function lastError();
    abstract public function escapeBool($value);
    abstract public function escapeString($value);
    abstract public function escapeBlob($value);
}