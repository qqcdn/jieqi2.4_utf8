<?php

class PHPExcel_CachedObjectStorage_Memcache extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
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
    /**
     * Memcache interface
     *
     * @var resource
     */
    private $_memcache;
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
            $this->_currentObject->detach();
            $obj = serialize($this->_currentObject);
            if (!$this->_memcache->replace($this->_cachePrefix . $this->_currentObjectID . '.cache', $obj, NULL, $this->_cacheTime)) {
                if (!$this->_memcache->add($this->_cachePrefix . $this->_currentObjectID . '.cache', $obj, NULL, $this->_cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell ' . $this->_currentObjectID . ' in MemCache');
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
            $success = $this->_memcache->get($this->_cachePrefix . $pCoord . '.cache');
            if ($success === false) {
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry ' . $pCoord . ' no longer exists in MemCache');
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
            $obj = $this->_memcache->get($this->_cachePrefix . $pCoord . '.cache');
            if ($obj === false) {
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry ' . $pCoord . ' no longer exists in MemCache');
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
        $this->_memcache->delete($this->_cachePrefix . $pCoord . '.cache');
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
                $obj = $this->_memcache->get($this->_cachePrefix . $cellID . '.cache');
                if ($obj === false) {
                    parent::deleteCacheData($cellID);
                    throw new PHPExcel_Exception('Cell entry ' . $cellID . ' no longer exists in MemCache');
                }
                if (!$this->_memcache->add($newCachePrefix . $cellID . '.cache', $obj, NULL, $this->_cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell ' . $cellID . ' in MemCache');
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
        $memcacheServer = isset($arguments['memcacheServer']) ? $arguments['memcacheServer'] : 'localhost';
        $memcachePort = isset($arguments['memcachePort']) ? $arguments['memcachePort'] : 11211;
        $cacheTime = isset($arguments['cacheTime']) ? $arguments['cacheTime'] : 600;
        if (is_null($this->_cachePrefix)) {
            $baseUnique = $this->_getUniqueID();
            $this->_cachePrefix = substr(md5($baseUnique), 0, 8) . '.';
            $this->_memcache = new Memcache();
            if (!$this->_memcache->addServer($memcacheServer, $memcachePort, false, 50, 5, 5, true, array($this, 'failureCallback'))) {
                throw new PHPExcel_Exception('Could not connect to MemCache server at ' . $memcacheServer . ':' . $memcachePort);
            }
            $this->_cacheTime = $cacheTime;
            parent::__construct($parent);
        }
    }
    public function failureCallback($host, $port)
    {
        throw new PHPExcel_Exception('memcache ' . $host . ':' . $port . ' failed');
    }
    public function __destruct()
    {
        $cacheList = $this->getCellList();
        foreach ($cacheList as $cellID) {
            $this->_memcache->delete($this->_cachePrefix . $cellID . '.cache');
        }
    }
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('memcache_add')) {
            return false;
        }
        return true;
    }
}