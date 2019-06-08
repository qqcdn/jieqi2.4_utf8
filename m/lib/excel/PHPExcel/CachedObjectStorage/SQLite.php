<?php

class PHPExcel_CachedObjectStorage_SQLite extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    /**
     * Database table name
     *
     * @var string
     */
    private $_TableName;
    /**
     * Database handle
     *
     * @var resource
     */
    private $_DBHandle;
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
            $this->_currentObject->detach();
            if (!$this->_DBHandle->queryExec('INSERT OR REPLACE INTO kvp_' . $this->_TableName . ' VALUES(\'' . $this->_currentObjectID . '\',\'' . sqlite_escape_string(serialize($this->_currentObject)) . '\')')) {
                throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
            }
            $this->_currentCellIsDirty = false;
        }
        $this->_currentObjectID = $this->_currentObject = NULL;
    }
    public function addCacheData($pCoord, PHPExcel_Cell $cell)
    {
        if ($pCoord !== $this->_currentObjectID && $this->_currentObjectID !== NULL) {
            $this->_storeData();
        }
        $this->_currentObjectID = $pCoord;
        $this->_currentObject = $cell;
        $this->_currentCellIsDirty = true;
        return $cell;
    }
    public function getCacheData($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            return $this->_currentObject;
        }
        $this->_storeData();
        $query = 'SELECT value FROM kvp_' . $this->_TableName . ' WHERE id=\'' . $pCoord . '\'';
        $cellResultSet = $this->_DBHandle->query($query, SQLITE_ASSOC);
        if ($cellResultSet === false) {
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        } else {
            if ($cellResultSet->numRows() == 0) {
                return NULL;
            }
        }
        $this->_currentObjectID = $pCoord;
        $cellResult = $cellResultSet->fetchSingle();
        $this->_currentObject = unserialize($cellResult);
        $this->_currentObject->attach($this);
        return $this->_currentObject;
    }
    public function isDataSet($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            return true;
        }
        $query = 'SELECT id FROM kvp_' . $this->_TableName . ' WHERE id=\'' . $pCoord . '\'';
        $cellResultSet = $this->_DBHandle->query($query, SQLITE_ASSOC);
        if ($cellResultSet === false) {
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        } else {
            if ($cellResultSet->numRows() == 0) {
                return false;
            }
        }
        return true;
    }
    public function deleteCacheData($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            $this->_currentObject->detach();
            $this->_currentObjectID = $this->_currentObject = NULL;
        }
        $query = 'DELETE FROM kvp_' . $this->_TableName . ' WHERE id=\'' . $pCoord . '\'';
        if (!$this->_DBHandle->queryExec($query)) {
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        }
        $this->_currentCellIsDirty = false;
    }
    public function moveCell($fromAddress, $toAddress)
    {
        if ($fromAddress === $this->_currentObjectID) {
            $this->_currentObjectID = $toAddress;
        }
        $query = 'DELETE FROM kvp_' . $this->_TableName . ' WHERE id=\'' . $toAddress . '\'';
        $result = $this->_DBHandle->exec($query);
        if ($result === false) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
        }
        $query = 'UPDATE kvp_' . $this->_TableName . ' SET id=\'' . $toAddress . '\' WHERE id=\'' . $fromAddress . '\'';
        $result = $this->_DBHandle->exec($query);
        if ($result === false) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
        }
        return true;
    }
    public function getCellList()
    {
        if ($this->_currentObjectID !== NULL) {
            $this->_storeData();
        }
        $query = 'SELECT id FROM kvp_' . $this->_TableName;
        $cellIdsResult = $this->_DBHandle->unbufferedQuery($query, SQLITE_ASSOC);
        if ($cellIdsResult === false) {
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        }
        $cellKeys = array();
        foreach ($cellIdsResult as $row) {
            $cellKeys[] = $row['id'];
        }
        return $cellKeys;
    }
    public function copyCellCollection(PHPExcel_Worksheet $parent)
    {
        $this->_currentCellIsDirty;
        $this->_storeData();
        $tableName = str_replace('.', '_', $this->_getUniqueID());
        if (!$this->_DBHandle->queryExec('CREATE TABLE kvp_' . $tableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)' . "\r\n" . '													AS SELECT * FROM kvp_' . $this->_TableName)) {
            throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
        }
        $this->_TableName = $tableName;
    }
    public function unsetWorksheetCells()
    {
        if (!is_null($this->_currentObject)) {
            $this->_currentObject->detach();
            $this->_currentObject = $this->_currentObjectID = NULL;
        }
        $this->_parent = NULL;
        $this->__destruct();
    }
    public function __construct(PHPExcel_Worksheet $parent)
    {
        parent::__construct($parent);
        if (is_null($this->_DBHandle)) {
            $this->_TableName = str_replace('.', '_', $this->_getUniqueID());
            $_DBName = ':memory:';
            $this->_DBHandle = new SQLiteDatabase($_DBName);
            if ($this->_DBHandle === false) {
                throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
            }
            if (!$this->_DBHandle->queryExec('CREATE TABLE kvp_' . $this->_TableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)')) {
                throw new PHPExcel_Exception(sqlite_error_string($this->_DBHandle->lastError()));
            }
        }
    }
    public function __destruct()
    {
        if (!is_null($this->_DBHandle)) {
            $this->_DBHandle->queryExec('DROP TABLE kvp_' . $this->_TableName);
        }
        $this->_DBHandle = NULL;
    }
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('sqlite_open')) {
            return false;
        }
        return true;
    }
}