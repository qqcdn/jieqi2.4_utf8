<?php

class PHPExcel_CachedObjectStorage_APC extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    /**
     * Prefix used to uniquely identify cache data for this worksheet
     *
     * @access    private
     * @var string
     */
    private $_cachePrefix;
    /**
     * Cache timeout
     *
     * @access    private
     * @var integer
     */
    private $_cacheTime = 600;
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
            $this->_currentObject->detach();
            if (!apc_store($this->_cachePrefix . $this->_currentObjectID . '.cache', serialize($this->_currentObject), $this->_cacheTime)) {
                $this->__destruct();
                throw new PHPExcel_Exception('Failed to store cell ' . $this->_currentObjectID . ' in APC');
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
        $this->_cellCache[$pCoord] = true;
        $this->_currentObjectID = $pCoord;
        $this->_currentObject = $cell;
        $this->_currentCellIsDirty = true;
        return $cell;
    }
    public function isDataSet($pCoord)
    {
        if (parent::isDataSet($pCoord)) {
            if ($this->_currentObjectID == $pCoord) {
                return true;
            }
            $success = apc_fetch($this->_cachePrefix . $pCoord . '.cache');
            if ($success === false) {
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry ' . $pCoord . ' no longer exists in APC cache');
            }
            return true;
        }
        return false;
    }
    public function getCacheData($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            return $this->_currentObject;
        }
        $this->_storeData();
        if (parent::isDataSet($pCoord)) {
            $obj = apc_fetch($this->_cachePrefix . $pCoord . '.cache');
            if ($obj === false) {
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry ' . $pCoord . ' no longer exists in APC cache');
            }
        } else {
            return NULL;
        }
        $this->_currentObjectID = $pCoord;
        $this->_currentObject = unserialize($obj);
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
    public function deleteCacheData($pCoord)
    {
        apc_delete($this->_cachePrefix . $pCoord . '.cache');
        parent::deleteCacheData($pCoord);
    }
    public function copyCellCollection(PHPExcel_Worksheet $parent)
    {
        parent::copyCellCollection($parent);
        $baseUnique = $this->_getUniqueID();
        $newCachePrefix = substr(md5($baseUnique), 0, 8) . '.';
        $cacheList = $this->getCellList();
        foreach ($cacheList as $cellID) {
            if ($cellID != $this->_currentObjectID) {
                $obj = apc_fetch($this->_cachePrefix . $cellID . '.cache');
                if ($obj === false) {
                    parent::deleteCacheData($cellID);
                    throw new PHPExcel_Exception('Cell entry ' . $cellID . ' no longer exists in APC');
                }
                if (!apc_store($newCachePrefix . $cellID . '.cache', $obj, $this->_cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell ' . $cellID . ' in APC');
                }
            }
        }
        $this->_cachePrefix = $newCachePrefix;
    }
    public function unsetWorksheetCells()
    {
        if ($this->_currentObject !== NULL) {
            $this->_currentObject->detach();
            $this->_currentObject = $this->_currentObjectID = NULL;
        }
        $this->__destruct();
        $this->_cellCache = array();
        $this->_parent = NULL;
    }
    public function __construct(PHPExcel_Worksheet $parent, $arguments)
    {
        $cacheTime = isset($arguments['cacheTime']) ? $arguments['cacheTime'] : 600;
        if ($this->_cachePrefix === NULL) {
            $baseUnique = $this->_getUniqueID();
            $this->_cachePrefix = substr(md5($baseUnique), 0, 8) . '.';
            $this->_cacheTime = $cacheTime;
            parent::__construct($parent);
        }
    }
    public function __destruct()
    {
        $cacheList = $this->getCellList();
        foreach ($cacheList as $cellID) {
            apc_delete($this->_cachePrefix . $cellID . '.cache');
        }
    }
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('apc_store')) {
            return false;
        }
        if (apc_sma_info() === false) {
            return false;
        }
        return true;
    }
}