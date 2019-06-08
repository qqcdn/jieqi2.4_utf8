<?php

class PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Read data only?
     * Identifies whether the Reader should only read data values for cells, and ignore any formatting information;
     *		or whether it should read both data and formatting
     *
     * @var	boolean
     */
    protected $_readDataOnly = false;
    /**
     * Read charts that are defined in the workbook?
     * Identifies whether the Reader should read the definitions for any charts that exist in the workbook;
     *
     * @var	boolean
     */
    protected $_includeCharts = false;
    /**
     * Restrict which sheets should be loaded?
     * This property holds an array of worksheet names to be loaded. If null, then all worksheets will be loaded.
     *
     * @var array of string
     */
    protected $_loadSheetsOnly;
    /**
     * PHPExcel_Reader_IReadFilter instance
     *
     * @var PHPExcel_Reader_IReadFilter
     */
    protected $_readFilter;
    protected $_fileHandle;
    public function getReadDataOnly()
    {
        return $this->_readDataOnly;
    }
    public function setReadDataOnly($pValue = false)
    {
        $this->_readDataOnly = $pValue;
        return $this;
    }
    public function getIncludeCharts()
    {
        return $this->_includeCharts;
    }
    public function setIncludeCharts($pValue = false)
    {
        $this->_includeCharts = (bool) $pValue;
        return $this;
    }
    public function getLoadSheetsOnly()
    {
        return $this->_loadSheetsOnly;
    }
    public function setLoadSheetsOnly($value = NULL)
    {
        $this->_loadSheetsOnly = is_array($value) ? $value : array($value);
        return $this;
    }
    public function setLoadAllSheets()
    {
        $this->_loadSheetsOnly = NULL;
        return $this;
    }
    public function getReadFilter()
    {
        return $this->_readFilter;
    }
    public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue)
    {
        $this->_readFilter = $pValue;
        return $this;
    }
    protected function _openFile($pFilename)
    {
        if (!file_exists($pFilename) || !is_readable($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $this->_fileHandle = fopen($pFilename, 'r');
        if ($this->_fileHandle === false) {
            throw new PHPExcel_Reader_Exception('Could not open file ' . $pFilename . ' for reading.');
        }
    }
    public function canRead($pFilename)
    {
        try {
            $this->_openFile($pFilename);
        } catch (Exception $e) {
            return false;
        }
        $readable = $this->_isValidFormat();
        fclose($this->_fileHandle);
        return $readable;
    }
}