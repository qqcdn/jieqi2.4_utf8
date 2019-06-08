<?php

class JieqiSQLiteDatabase extends JieqiObject
{
    /**
     * 数据库连接资源
     *
     * @var resource
     */
    public $conn;
    /**
     * 数据库连接参数
     *
     * @var array
     */
    public $dbset;
    public function __construct($db = '')
    {
        parent::__construct();
    }
    public function setDbset($dbset)
    {
        $this->dbset = $dbset;
        $this->connect();
        return true;
    }
    public function connect($master = false)
    {
        if ($this->dbset['dbpconnect'] == 1) {
            $this->conn = @sqlite_open($this->dbset['dbname'], 438, $sqliteerror);
        } else {
            $this->conn = @sqlite_popen($this->dbset['dbname'], 438, $sqliteerror);
        }
        if (!$this->conn) {
            jieqi_printfail('Can not connect to database!<br /><br />error: ' . $sqliteerror);
            return false;
        } else {
            return true;
        }
    }
    public function genId($sequence = '')
    {
        return 0;
    }
    public function fetchRow($result)
    {
        return @sqlite_fetch_array($result, SQLITE_NUM);
    }
    public function fetchArray($result)
    {
        return @sqlite_fetch_array($result, SQLITE_ASSOC);
    }
    public function getInsertId()
    {
        return sqlite_last_insert_rowid($this->conn);
    }
    public function getRowsNum($result)
    {
        return @sqlite_num_rows($result);
    }
    public function getAffectedRows()
    {
        return sqlite_changes($this->conn);
    }
    public function close()
    {
        @sqlite_close($this->conn);
    }
    public function freeRecordSet($result)
    {
        return true;
    }
    public function error()
    {
        $errno = @sqlite_last_error($this->conn);
        if (!empty($errno)) {
            return @sqlite_error_string($errno);
        } else {
            return '';
        }
    }
    public function errno()
    {
        return @sqlite_last_error($this->conn);
    }
    public function quoteString($str)
    {
        return '\'' . jieqi_dbslashes($str) . '\'';
    }
    public function query($sql, $limit = 0, $start = 0, $nobuffer = false)
    {
        if (!empty($limit)) {
            if (empty($start)) {
                $start = 0;
            }
            $sql = $sql . ' LIMIT ' . (int) $start . ', ' . (int) $limit;
        }
        $sql = str_replace(array('\\\'', '\\"', '\\\\'), array('\'\'', '"', '\\'), $sql);
        if ($nobuffer) {
            $result = sqlite_unbuffered_query($sql, $this->conn);
        } else {
            $result = sqlite_query($sql, $this->conn);
        }
        if ($result) {
            if (!$result) {
                $this->raiseError('SQL: ' . $sql, JIEQI_ERROR_RETURN);
            }
            return $result;
        } else {
            $this->raiseError('SQL: ' . $sql, JIEQI_ERROR_RETURN);
            return false;
        }
    }
    public function list_tables()
    {
        if (function_exists('sqlite_list_tables')) {
            return sqlite_list_tables();
        } else {
            $tables = array();
            $sql = 'SELECT name FROM sqlite_master WHERE (type = \'table\')';
            if ($res = sqlite_query($this->conn, $sql)) {
                while (sqlite_has_more($res)) {
                    $tables[] = sqlite_fetch_single($res);
                }
            }
            return $tables;
        }
    }
    public function table_exists($table)
    {
        if (function_exists('sqlite_table_exists')) {
            return sqlite_table_exists($this->conn, $table);
        } else {
            $sql = 'SELECT count(name) FROM sqlite_master WHERE ((type = \'table\') and (name = \'' . $table . '\'))';
            if ($res = sqlite_query($this->conn, $sql)) {
                return 0 < sqlite_fetch_single($res);
            } else {
                return false;
            }
        }
    }
}