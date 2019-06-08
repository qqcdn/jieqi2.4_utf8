<?php

class PHPExcel_HashTable
{
    /**
     * HashTable elements
     *
     * @var array
     */
    public $_items = array();
    /**
     * HashTable key map
     *
     * @var array
     */
    public $_keyMap = array();
    public function __construct($pSource = NULL)
    {
        if ($pSource !== NULL) {
            $this->addFromSource($pSource);
        }
    }
    public function addFromSource($pSource = NULL)
    {
        if ($pSource == NULL) {
            return NULL;
        } else {
            if (!is_array($pSource)) {
                throw new PHPExcel_Exception('Invalid array parameter passed.');
            }
        }
        foreach ($pSource as $item) {
            $this->add($item);
        }
    }
    public function add(PHPExcel_IComparable $pSource = NULL)
    {
        $hash = $pSource->getHashCode();
        if (!isset($this->_items[$hash])) {
            $this->_items[$hash] = $pSource;
            $this->_keyMap[count($this->_items) - 1] = $hash;
        }
    }
    public function remove(PHPExcel_IComparable $pSource = NULL)
    {
        $hash = $pSource->getHashCode();
        if (isset($this->_items[$hash])) {
            unset($this->_items[$hash]);
            $deleteKey = -1;
            foreach ($this->_keyMap as $key => $value) {
                if (0 <= $deleteKey) {
                    $this->_keyMap[$key - 1] = $value;
                }
                if ($value == $hash) {
                    $deleteKey = $key;
                }
            }
            unset($this->_keyMap[count($this->_keyMap) - 1]);
        }
    }
    public function clear()
    {
        $this->_items = array();
        $this->_keyMap = array();
    }
    public function count()
    {
        return count($this->_items);
    }
    public function getIndexForHashCode($pHashCode = '')
    {
        return array_search($pHashCode, $this->_keyMap);
    }
    public function getByIndex($pIndex = 0)
    {
        if (isset($this->_keyMap[$pIndex])) {
            return $this->getByHashCode($this->_keyMap[$pIndex]);
        }
        return NULL;
    }
    public function getByHashCode($pHashCode = '')
    {
        if (isset($this->_items[$pHashCode])) {
            return $this->_items[$pHashCode];
        }
        return NULL;
    }
    public function toArray()
    {
        return $this->_items;
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->{$key} = clone $value;
            }
        }
    }
}