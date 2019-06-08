<?php

class PHPExcel_CachedObjectStorage_Memory extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    protected function _storeData()
    {
    }
    public function addCacheData($pCoord, PHPExcel_Cell $cell)
    {
        $this->_cellCache[$pCoord] = $cell;
        $this->_currentObjectID = $pCoord;
        return $cell;
    }
    public function getCacheData($pCoord)
    {
        if (!isset($this->_cellCache[$pCoord])) {
            $this->_currentObjectID = NULL;
            return NULL;
        }
        $this->_currentObjectID = $pCoord;
        return $this->_cellCache[$pCoord];
    }
    public function copyCellCollection(PHPExcel_Worksheet $parent)
    {
        parent::copyCellCollection($parent);
        $newCollection = array();
        foreach ($this->_cellCache as $k => &$cell) {
            $newCollection[$k] = clone $cell;
            $newCollection[$k]->attach($this);
        }
        $this->_cellCache = $newCollection;
    }
    public function unsetWorksheetCells()
    {
        foreach ($this->_cellCache as $k => &$cell) {
            $cell->detach();
            $this->_cellCache[$k] = NULL;
        }
        unset($cell);
        $this->_cellCache = array();
        $this->_parent = NULL;
    }
}