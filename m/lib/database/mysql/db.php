<?php

class JieqiMySQLDatabase extends JieqiObject
{
    /**
     * 数据库连接资源
     *
     * @var resource
     */
    public $conn;
    public $conn_m;
    public $conn_s;
    public $hid_s = -1;
    /**
     * 数据库连接参数
     *
     * @var array
     */
    public $dbset;
    /**
     * 服务器数组
     *
     * @var array
     */
    public $hosts;
    /**
     * 服务器数量
     *
     * @var array
     */
    public $hnum;
    public function __construct($db = '')
    {
        parent::__construct();
    }
    public function setDbset($dbset)
    {
        $this->dbset = $dbset;
        $hostary = explode(',', $dbset['dbhost']);
        $this->hnum = count($hostary);
        if (1 < $this->hnum) {
            $userary = explode(',', $dbset['dbuser']);
            $passary = explode(',', $dbset['dbpass']);
            $nameary = explode(',', $dbset['dbname']);
            $this->hosts = array();
            foreach ($hostary as $k => $v) {
                $this->hosts[$k]['dbhost'] = $v;
                $this->hosts[$k]['dbuser'] = isset($userary[$k]) ? $userary[$k] : $userary[0];
                $this->hosts[$k]['dbpass'] = isset($passary[$k]) ? $passary[$k] : $passary[0];
                $this->hosts[$k]['dbname'] = isset($nameary[$k]) ? $nameary[$k] : $nameary[0];
            }
        } else {
            $this->hosts = array();
            $this->hosts[0] = array('dbhost' => $this->dbset['dbhost'], 'dbuser' => $this->dbset['dbuser'], 'dbpass' => $this->dbset['dbpass'], 'dbname' => $this->dbset['dbname']);
        }
        if (!empty($this->dbset['dbusage']) && !isset($this->hosts[$this->dbset['dbusage'] - 1])) {
            $this->dbset['dbusage'] = 1;
        }
        return true;
    }
    public function connect($master = false)
    {
        if (empty($this->dbset['dbusage'])) {
            if ($master) {
                $hid = 0;
            } else {
                $hid = -1;
            }
        } else {
            $hid = $this->dbset['dbusage'] - 1;
            if ($hid == 0) {
                $master = true;
            }
        }
        if ($master && is_resource($this->conn_m)) {
            $this->conn =& $this->conn_m;
            return true;
        } else {
            if (!$master && is_resource($this->conn_s)) {
                $this->conn =& $this->conn_s;
                return true;
            }
        }
        if ($hid == -1) {
            $hid = mt_rand(0, $this->hnum - 1);
        }
        if ($this->dbset['dbpconnect'] == 1) {
            if ($master) {
                $this->conn_m = @mysql_pconnect($this->hosts[$hid]['dbhost'], $this->hosts[$hid]['dbuser'], $this->hosts[$hid]['dbpass']);
                $this->conn =& $this->conn_m;
            } else {
                $this->conn_s = @mysql_pconnect($this->hosts[$hid]['dbhost'], $this->hosts[$hid]['dbuser'], $this->hosts[$hid]['dbpass']);
                $this->conn =& $this->conn_s;
            }
        } else {
            if ($master) {
                $this->conn_m = @mysql_connect($this->hosts[$hid]['dbhost'], $this->hosts[$hid]['dbuser'], $this->hosts[$hid]['dbpass'], true);
                $this->conn =& $this->conn_m;
            } else {
                $this->conn_s = @mysql_connect($this->hosts[$hid]['dbhost'], $this->hosts[$hid]['dbuser'], $this->hosts[$hid]['dbpass'], true);
                $this->conn =& $this->conn_s;
            }
        }
        if (!$this->conn) {
            jieqi_printfail('Can not connect to database!<br /><br />error: ' . mysql_error());
        }
        $this->connectcharset();
        if (0 < strlen($this->hosts[$hid]['dbname'])) {
            if (!mysql_select_db($this->hosts[$hid]['dbname'], $this->conn)) {
                jieqi_printfail('Can not select database!<br /><br />error: ' . mysql_error());
            }
        }
        if (!defined('JIEQI_DB_CONNECTED')) {
            @define('JIEQI_DB_CONNECTED', true);
        }
        return true;
    }
    public function reconnect()
    {
        if (!mysql_ping($this->conn)) {
            $this->close();
            return $this->connect();
        } else {
            $this->connectcharset();
            return true;
        }
    }
    public function connectcharset()
    {
        $mysql_version = mysql_get_server_info();
        if ('4.1' < $mysql_version) {
            if (isset($this->dbset['dbcharset'])) {
                if ($this->dbset['dbcharset'] != 'default') {
                    @mysql_query('SET character_set_connection=' . $this->dbset['dbcharset'] . ', character_set_results=' . $this->dbset['dbcharset'] . ', character_set_client=binary', $this->conn);
                }
            } else {
                @mysql_query('SET character_set_connection=' . JIEQI_SYSTEM_CHARSET . ', character_set_results=' . JIEQI_SYSTEM_CHARSET . ', character_set_client=binary', $this->conn);
            }
        }
        if ('5.0' < $mysql_version) {
            @mysql_query('SET sql_mode=\'\'', $this->conn);
        }
    }
    public function genId($sequence = '')
    {
        return 0;
    }
    public function fetchRow($result)
    {
        return @mysql_fetch_row($result);
    }
    public function fetchArray($result)
    {
        return @mysql_fetch_array($result, MYSQL_ASSOC);
    }
    public function getInsertId()
    {
        return mysql_insert_id($this->conn);
    }
    public function getRowsNum($result)
    {
        return @mysql_num_rows($result);
    }
    public function getAffectedRows()
    {
        return mysql_affected_rows($this->conn);
    }
    public function close()
    {
        @mysql_close($this->conn);
    }
    public function freeRecordSet($result)
    {
        return mysql_free_result($result);
    }
    public function error()
    {
        return @mysql_error();
    }
    public function errno()
    {
        return @mysql_errno();
    }
    public function quoteString($str)
    {
        return '\'' . jieqi_dbslashes($str) . '\'';
    }
    public function sqllog($do = 'add', $sql = '')
    {
        static $sqllog = array();
        switch ($do) {
            case 'add':
                if (!empty($sql)) {
                    $sqllog[] = $sql;
                }
                break;
            case 'ret':
                return $sqllog;
                break;
            case 'count':
                return count($sqllog);
                break;
            case 'show':
                echo '<br />queries: ' . count($sqllog);
                foreach ($sqllog as $sql) {
                    echo '<br />' . jieqi_htmlstr($sql);
                }
                break;
        }
    }
    public function query($sql, $limit = 0, $start = 0, $nobuffer = false)
    {
        if (!empty($this->dbset['dbusage'])) {
            $this->connect();
        } else {
            if (strtoupper(substr(ltrim($sql), 0, 6)) == 'SELECT') {
                $this->connect(false);
            } else {
                $this->connect(true);
            }
        }
        if (!empty($limit)) {
            if (empty($start)) {
                $start = 0;
            }
            $sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;
        }
        if (defined('JIEQI_DEBUG_MODE') && 0 < JIEQI_DEBUG_MODE) {
            $this->sqllog('add', $sql);
        }
        if ($nobuffer) {
            $result = mysql_unbuffered_query($sql, $this->conn);
        } else {
            $result = mysql_query($sql, $this->conn);
        }
        if ($result) {
            return $result;
        } else {
            $errno = mysql_errno($this->conn);
            if ($errno == 2013 || $errno == 2006) {
                $this->reconnect();
                if ($nobuffer) {
                    $result = mysql_unbuffered_query($sql, $this->conn);
                } else {
                    $result = mysql_query($sql, $this->conn);
                }
                if ($result) {
                    return $result;
                }
            }
            if (defined('JIEQI_DEBUG_MODE') && 0 < JIEQI_DEBUG_MODE) {
                jieqi_printfail('SQL: ' . jieqi_htmlstr($sql) . '<br /><br />ERROR: ' . mysql_error($this->conn) . '(' . mysql_errno($this->conn) . ')');
            }
            return false;
        }
    }
}