<?php

class PHPExcel_CachedObjectStorage_Wincache extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    /**
     * Prefix used to uniquely identify cache data for this worksheet
     *
     * @var string
     */
    private $_cachePrefix;
    /**
     * Cache timeout
     *
     * @var integer
     */
    private $_cacheTime = 600;
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
            $this->_currentObject->detach();
            $obj = serialize($this->_currentObject);
            if (wincache_ucache_exists($this->_cachePrefix . $this->_currentObjectID . '.cache')) {
                if (!wincache_ucache_set($this->_cachePrefix . $this->_currentObjectID . '.cache', $obj, $this->_cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell ' . $this->_currentObjectID . ' in WinCache');
                }
            } else {
                if (!wincache_ucache_add($this->_cachePrefix . $this->_currentObjectID . '.cache', $obj, $this->_cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell ' . $this->_currentObjectID . ' in WinCache');
                }
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
            $success = wincache_ucache_exists($this->_cachePrefix . $pCoord . '.cache');
            if ($success === false) {
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry ' . $pCoord . ' no longer exists in WinCache');
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
        $obj = NULL;
        if (parent::isDataSet($pCoord)) {
            $success = false;
            $obj = wincache_ucache_get($this->_cachePrefix . $pCoord . '.cache', $success);
            if ($success === false) {
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry ' . $pCoord . ' no longer exists in WinCache');
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
        wincache_ucache_delete($this->_cachePrefix . $pCoord . '.cache');
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
                $success = false;
                $obj = wincache_ucache_get($this->_cachePrefix . $cellID . '.cache', $success);
                if ($success === false) {
                    parent::deleteCacheData($cellID);
                    throw new PHPExcel_Exception('Cell entry ' . $cellID . ' no longer exists in Wincache');
                }
                if (!wincache_ucache_add($newCachePrefix . $cellID . '.cache', $obj, $this->_cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell ' . $cellID . ' in Wincache');
                }
            }
        }
        $this->_cachePrefix = $newCachePrefix;
    }
    public function unsetWorksheetCells()
    {
        if (!is_null($this->_currentObject)) {
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
        if (is_null($this->_cachePrefix)) {
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
            wincache_ucache_delete($this->_cachePrefix . $cellID . '.cache');
        }
    }
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('wincache_ucache_add')) {
            return false;
        }
        return true;
    }
}