<?php

class PHPExcel_CachedObjectStorage_SQLite3 extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
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
    /**
     * Prepared statement for a SQLite3 select query
     *
     * @var SQLite3Stmt
     */
    private $_selectQuery;
    /**
     * Prepared statement for a SQLite3 insert query
     *
     * @var SQLite3Stmt
     */
    private $_insertQuery;
    /**
     * Prepared statement for a SQLite3 update query
     *
     * @var SQLite3Stmt
     */
    private $_updateQuery;
    /**
     * Prepared statement for a SQLite3 delete query
     *
     * @var SQLite3Stmt
     */
    private $_deleteQuery;
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
            $this->_currentObject->detach();
            $this->_insertQuery->bindValue('id', $this->_currentObjectID, SQLITE3_TEXT);
            $this->_insertQuery->bindValue('data', serialize($this->_currentObject), SQLITE3_BLOB);
            $result = $this->_insertQuery->execute();
            if ($result === false) {
                throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
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
        $this->_selectQuery->bindValue('id', $pCoord, SQLITE3_TEXT);
        $cellResult = $this->_selectQuery->execute();
        if ($cellResult === false) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
        }
        $cellData = $cellResult->fetchArray(SQLITE3_ASSOC);
        if ($cellData === false) {
            return NULL;
        }
        $this->_currentObjectID = $pCoord;
        $this->_currentObject = unserialize($cellData['value']);
        $this->_currentObject->attach($this);
        return $this->_currentObject;
    }
    public function isDataSet($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            return true;
        }
        $this->_selectQuery->bindValue('id', $pCoord, SQLITE3_TEXT);
        $cellResult = $this->_selectQuery->execute();
        if ($cellResult === false) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
        }
        $cellData = $cellResult->fetchArray(SQLITE3_ASSOC);
        return $cellData === false ? false : true;
    }
    public function deleteCacheData($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            $this->_currentObject->detach();
            $this->_currentObjectID = $this->_currentObject = NULL;
        }
        $this->_deleteQuery->bindValue('id', $pCoord, SQLITE3_TEXT);
        $result = $this->_deleteQuery->execute();
        if ($result === false) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
        }
        $this->_currentCellIsDirty = false;
    }
    public function moveCell($fromAddress, $toAddress)
    {
        if ($fromAddress === $this->_currentObjectID) {
            $this->_currentObjectID = $toAddress;
        }
        $this->_deleteQuery->bindValue('id', $toAddress, SQLITE3_TEXT);
        $result = $this->_deleteQuery->execute();
        if ($result === false) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
        }
        $this->_updateQuery->bindValue('toid', $toAddress, SQLITE3_TEXT);
        $this->_updateQuery->bindValue('fromid', $fromAddress, SQLITE3_TEXT);
        $result = $this->_updateQuery->execute();
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
        $cellIdsResult = $this->_DBHandle->query($query);
        if ($cellIdsResult === false) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
        }
        $cellKeys = array();
        while ($row = $cellIdsResult->fetchArray(SQLITE3_ASSOC)) {
            $cellKeys[] = $row['id'];
        }
        return $cellKeys;
    }
    public function copyCellCollection(PHPExcel_Worksheet $parent)
    {
        $this->_currentCellIsDirty;
        $this->_storeData();
        $tableName = str_replace('.', '_', $this->_getUniqueID());
        if (!$this->_DBHandle->exec('CREATE TABLE kvp_' . $tableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)' . "\r\n" . '		                                       AS SELECT * FROM kvp_' . $this->_TableName)) {
            throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
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
            $this->_DBHandle = new SQLite3($_DBName);
            if ($this->_DBHandle === false) {
                throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
            }
            if (!$this->_DBHandle->exec('CREATE TABLE kvp_' . $this->_TableName . ' (id VARCHAR(12) PRIMARY KEY, value BLOB)')) {
                throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
            }
        }
        $this->_selectQuery = $this->_DBHandle->prepare('SELECT value FROM kvp_' . $this->_TableName . ' WHERE id = :id');
        $this->_insertQuery = $this->_DBHandle->prepare('INSERT OR REPLACE INTO kvp_' . $this->_TableName . ' VALUES(:id,:data)');
        $this->_updateQuery = $this->_DBHandle->prepare('UPDATE kvp_' . $this->_TableName . ' SET id=:toId WHERE id=:fromId');
        $this->_deleteQuery = $this->_DBHandle->prepare('DELETE FROM kvp_' . $this->_TableName . ' WHERE id = :id');
    }
    public function __destruct()
    {
        if (!is_null($this->_DBHandle)) {
            $this->_DBHandle->exec('DROP TABLE kvp_' . $this->_TableName);
            $this->_DBHandle->close();
        }
        $this->_DBHandle = NULL;
    }
    public static function cacheMethodIsAvailable()
    {
        if (!class_exists('SQLite3', false)) {
            return false;
        }
        return true;
    }
}