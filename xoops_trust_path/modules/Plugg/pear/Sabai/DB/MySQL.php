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
class Sabai_DB_MySQL extends Sabai_DB
{
    /**
     * @var string
     */
    private $_version;

    /**
     * Constructor
     *
     * @return Sabai_DB_MySQL
     */
    public function __construct(Sabai_DB_Connection_MySQL $connection)
    {
        parent::__construct($connection);
    }

    /**
     * Begins transaction
     *
     * @return bool
     */
    public function beginTransaction()
    {
        return mysql_query('START TRANSACTION', $this->_connection->getResourceId());
    }

    /**
     * Commits changes made to the database
     *
     * @return bool
     */
    public function commit()
    {
        return mysql_query('COMMIT', $this->_connection->getResourceId());
    }

    /**
     * Rollbacks the commit
     *
     * @return bool
     */
    public function rollback()
    {
        return mysql_query('ROLLBACK', $this->_connection->getResourceId());
    }

    /**
     * Queries the database
     *
     * @param string $sql
     * @param int $limit
     * @param int $offset
     * @return Sabai_DB_Rowset_MySQL
     */
    public function query($sql, $limit = 0, $offset = 0)
    {
        if (intval($limit) > 0) $sql .=  sprintf(' LIMIT %d, %d', $offset, $limit);
        if ($rs = mysql_query($sql, $this->_connection->getResourceId())) {
            Sabai_Log::info(sprintf('SQL "%s" executed', $sql));
            if (!class_exists('Sabai_DB_Rowset_MySQL', false)) {
                require 'Sabai/DB/Rowset/MySQL.php';
            }
            return new Sabai_DB_Rowset_MySQL($rs);
            
        }
        Sabai_Log::warn(sprintf('SQL "%s" failed. Error: "%s"', $sql, $this->lastError()));
        return false;
    }

    /**
     * Executes an SQL query against the DB
     *
     * @param string $sql
     * @param bool $useAffectedRows
     * @return bool
     */
    public function exec($sql, $useAffectedRows = true)
    {
        if (!mysql_query($sql, $this->_connection->getResourceId())) {
            Sabai_Log::warn(sprintf('SQL "%s" failed. Error: "%s"', $sql, $this->lastError()));
            return false;
        }
        Sabai_Log::info(sprintf('SQL "%s" executed', $sql));
        // updating 0 row will also return true
        return $useAffectedRows ? mysql_affected_rows($this->_connection->getResourceId()) : true;
    }

    /**
     * Gets the primary key of te last inserted row
     *
     * @param string $tableName
     * @param string $keyName
     * @return string
     */
    public function lastInsertId($tableName, $keyName)
    {
        if (!$id = mysql_insert_id($this->_connection->getResourceId())) {
            // return false when $id is 0 or false
            return false;
        }
        return $id;
    }

    /**
     * Gets the number of affected rows
     *
     * @return int
     */
    public function affectedRows()
    {
        return mysql_affected_rows($this->_connection->getResourceId());
    }

    /**
     * Gets the last error occurred
     *
     * @return string
     */
    public function lastError()
    {
        return sprintf('%s(%s)', mysql_error($this->_connection->getResourceId()), mysql_errno($this->_connection->getResourceId()));
    }

    /**
     * Escapes a boolean value for MySQL DB
     *
     * @param bool $value
     * @return int
     */
    public function escapeBool($value)
    {
        return intval($value);
    }

    /**
     * Escapes a string value for MySQL DB
     *
     * @param string $value
     * @return string
     */
    public function escapeString($value)
    {
        return "'" . mysql_real_escape_string($value, $this->_connection->getResourceId()) . "'";
    }

    /**
     * Escapes a blob value for MySQL DB
     *
     * @param string $value
     * @return string
     */
    public function escapeBlob($value)
    {
        return $this->escapeString($value);
    }

    /**
     * Checks if the server version is at least the requested version
     *
     * @protected
     * @param string $base
     * @return bool
     */
    protected function _checkVersion($base) {
        if (!isset($this->_version)) {
            $version = mysql_get_server_info($this->_connection->getResourceId());
            $version = explode('.', substr($version, 0, strpos($version, '-')));
            $this->_version = $version[0] * 10000 + intval(@$version[1]) * 100 + intval(@$version[2]);
        }
        if (false !== strpos($base, '.')) {
            $base = explode('.', $base);
            $base = $base[0] * 10000 + $base[1] * 100 + $base[2];
        }
        return $this->_version >= $base;
    }

    public function isTriggerEnabled()
    {
        // Disable trigger for now.. 
        return false;
        return $this->_checkVersion(50106);
    }

    /**
     * Returns optional config varaibles for creating database tables, used by MDB2_Schema
     *
     * @return array
     */
    public function getMDB2CreateTableOptions()
    {
        // Character set support is from MySQL 4.1.0
        // MDB2 does not check the version, so it must be done here
        if ($this->_checkVersion(40100)) {
            return array(
                'type' => 'InnoDB',
                'charset' => $this->_connection->getClientEncoding(true),
                'collate' => null
            );
        }
        return array('type' => 'InnoDB');
    }
}

function sabai_db_unescapeBlob($value)
{
    return $value;
}