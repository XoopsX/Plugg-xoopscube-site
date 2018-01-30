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
class Sabai_DB_Connection_PostgreSQL extends Sabai_DB_Connection 
{
    protected $_resourceHost;
    protected $_resourceUser;
    protected $_resourceUserPassword;
    protected $_resourcePort;
    
    private static $_charsets = array(
        'big5'       => 'BIG5',
        'euc-jp'     => 'EUC_JP',
        'euc-kr'     => 'EUC_KR',
        'euc-cn'     => 'EUC_CN',
        'gb2312'     => 'GB18030',
        'utf-8'      => 'UTF8',
        'tis-620'    => 'tis620',
        'iso-8859-2' => 'LATIN2',
        'iso-8859-5' => 'ISO_8859_5',
        'iso-8859-6' => 'ISO_8859_6',
        'iso-8859-7' => 'ISO_8859_7',
        'iso-8859-8' => 'ISO_8859_8',
        'shift_jis'  => 'SJIS'
    );

    /**
     * Constructor
     *
     * @return Sabai_DB_PostgreSQL
     */
    public function __construct(array $config)
    {
        parent::__construct('PostgreSQL');
        $this->_resourceName = $config['dbname'];
        $this->_resourceHost = $config['host'];
        $this->_resourceUser = $config['user'];
        $this->_resourceUserPassword = $config['pass'];
        $this->_resourcePort = isset($config['port']) ? intval($config['port']) : 5432;
        $this->_clientEncoding = @$config['clientEncoding'];
    }

    public function connect()
    {
        $conn_str = sprintf('host=%s dbname=%s user=%s password=%s port=%d', $config['host'], $config['dbname'], $config['user'], $config['pass'], $this->_resourcePort);
        if (!$link = pg_connect($conn_str)) {
            trigger_error('Unable to connect to database server', E_USER_WARNING);
            return false;
        }
        
        if (!empty($this->_clientEncoding)) {
            $key = strtoupper($this->_clientEncoding);
            if (isset(self::$_charsets[$key])) {
                if (0 !== pg_set_client_encoding($link, self::$_charsets[$key])) $this->_clientEncoding = null;
            }
        }
        
        $this->_resourceId = $link;
        return true;
    }

    public function getDSN()
    {
        return sprintf('pgsql://%s:%s@%s:%d/%s',
            rawurlencode($this->_resourceUser),
            rawurlencode($this->_resourceUserPassword),
            rawurlencode($this->_resourceHost),
            $this->_resourcePort,
            rawurlencode($this->_resourceName)
        );
    }
    
    public function getClientEncoding()
    {
        return $this->_clientEncoding;
    }
}