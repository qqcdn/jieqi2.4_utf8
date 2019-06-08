<?php

class PHPExcel_CachedObjectStorage_PHPTemp extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    /**
     * Name of the file for this cache
     *
     * @var string
     */
    private $_fileHandle;
    /**
     * Memory limit to use before reverting to file cache
     *
     * @var integer
     */
    private $_memoryCacheSize;
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
            $this->_currentObject->detach();
            fseek($this->_fileHandle, 0, SEEK_END);
            $offset = ftell($this->_fileHandle);
            fwrite($this->_fileHandle, serialize($this->_currentObject));
            $this->_cellCache[$this->_currentObjectID] = array('ptr' => $offset, 'sz' => ftell($this->_fileHandle) - $offset);
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
        if (!isset($this->_cellCache[$pCoord])) {
            return NULL;
        }
        $this->_currentObjectID = $pCoord;
        fseek($this->_fileHandle, $this->_cellCache[$pCoord]['ptr']);
        $this->_currentObject = unserialize(fread($this->_fileHandle, $this->_cellCache[$pCoord]['sz']));
        $this->_currentObject->attach($this);
        return $this->_currentObject;
    }
    public function getCellList()
    {
        if ($this->_currentObjectID !== NULL) {
            $this->_storeData();
        }
        return parent::getCellList();
    }
    public function copyCellCollection(PHPExcel_Worksheet $parent)
    {
        parent::copyCellCollection($parent);
        $newFileHandle = fopen('php://temp/maxmemory:' . $this->_memoryCacheSize, 'a+');
        fseek($this->_fileHandle, 0);
        while (!feof($this->_fileHandle)) {
            fwrite($newFileHandle, fread($this->_fileHandle, 1024));
        }
        $this->_fileHandle = $newFileHandle;
    }
    public function unsetWorksheetCells()
    {
        if (!is_null($this->_currentObject)) {
            $this->_currentObject->detach();
            $this->_currentObject = $this->_currentObjectID = NULL;
        }
        $this->_cellCache = array();
        $this->_parent = NULL;
        $this->__destruct();
    }
    public function __construct(PHPExcel_Worksheet $parent, $arguments)
    {
        $this->_memoryCacheSize = isset($arguments['memoryCacheSize']) ? $arguments['memoryCacheSize'] : '1MB';
        parent::__construct($parent);
        if (is_null($this->_fileHandle)) {
            $this->_fileHandle = fopen('php://temp/maxmemory:' . $this->_memoryCacheSize, 'a+');
        }
    }
    public function __destruct()
    {
        if (!is_null($this->_fileHandle)) {
            fclose($this->_fileHandle);
        }
        $this->_fileHandle = NULL;
    }
}