<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Reader_CSV extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Input encoding
     *
     * @access	private
     * @var	string
     */
    private $_inputEncoding = 'UTF-8';
    /**
     * Delimiter
     *
     * @access	private
     * @var string
     */
    private $_delimiter = ',';
    /**
     * Enclosure
     *
     * @access	private
     * @var	string
     */
    private $_enclosure = '"';
    /**
     * Line ending
     *
     * @access	private
     * @var	string
     */
    private $_lineEnding = PHP_EOL;
    /**
     * Sheet index to read
     *
     * @access	private
     * @var	int
     */
    private $_sheetIndex = 0;
    /**
     * Load rows contiguously
     *
     * @access	private
     * @var	int
     */
    private $_contiguous = false;
    /**
     * Row counter for loading rows contiguously
     *
     * @var	int
     */
    private $_contiguousRow = -1;
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }
    protected function _isValidFormat()
    {
        return true;
    }
    public function setInputEncoding($pValue = 'UTF-8')
    {
        $this->_inputEncoding = $pValue;
        return $this;
    }
    public function getInputEncoding()
    {
        return $this->_inputEncoding;
    }
    protected function _skipBOM()
    {
        rewind($this->_fileHandle);
        switch ($this->_inputEncoding) {
            case 'UTF-8':
                fgets($this->_fileHandle, 4) == '锘? ? fseek($this->_fileHandle, 3) : fseek($this->_fileHandle, 0);
                break;
            case 'UTF-16LE':
                fgets($this->_fileHandle, 3) == '? ? fseek($this->_fileHandle, 2) : fseek($this->_fileHandle, 0);
                break;
            case 'UTF-16BE':
                fgets($this->_fileHandle, 3) == '?' ? fseek($this->_fileHandle, 2) : fseek($this->_fileHandle, 0);
                break;
            case 'UTF-32LE':
                fgets($this->_fileHandle, 5) == '? . "\0" . '' . "\0" . '' ? fseek($this->_fileHandle, 4) : fseek($this->_fileHandle, 0);
                break;
            case 'UTF-32BE':
                fgets($this->_fileHandle, 5) == '' . "\0" . '' . "\0" . '?' ? fseek($this->_fileHandle, 4) : fseek($this->_fileHandle, 0);
                break;
            default:
                break;
        }
    }
    public function listWorksheetInfo($pFilename)
    {
        $this->_openFile($pFilename);
        if (!$this->_isValidFormat()) {
            fclose($this->_fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $fileHandle = $this->_fileHandle;
        $this->_skipBOM();
        $escapeEnclosures = array('\\' . $this->_enclosure, $this->_enclosure . $this->_enclosure);
        $worksheetInfo = array();
        $worksheetInfo[0]['worksheetName'] = 'Worksheet';
        $worksheetInfo[0]['lastColumnLetter'] = 'A';
        $worksheetInfo[0]['lastColumnIndex'] = 0;
        $worksheetInfo[0]['totalRows'] = 0;
        $worksheetInfo[0]['totalColumns'] = 0;
        while (($rowData = fgetcsv($fileHandle, 0, $this->_delimiter, $this->_enclosure)) !== false) {
            $worksheetInfo[0]['totalRows']++;
            $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
        }
        $worksheetInfo[0]['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex']);
        $worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;
        fclose($fileHandle);
        return $worksheetInfo;
    }
    public function load($pFilename)
    {
        $objPHPExcel = new PHPExcel();
        return $this->loadIntoExisting($pFilename, $objPHPExcel);
    }
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        $lineEnding = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', true);
        $this->_openFile($pFilename);
        if (!$this->_isValidFormat()) {
            fclose($this->_fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $fileHandle = $this->_fileHandle;
        $this->_skipBOM();
        while ($objPHPExcel->getSheetCount() <= $this->_sheetIndex) {
            $objPHPExcel->createSheet();
        }
        $sheet = $objPHPExcel->setActiveSheetIndex($this->_sheetIndex);
        $escapeEnclosures = array('\\' . $this->_enclosure, $this->_enclosure . $this->_enclosure);
        $currentRow = 1;
        if ($this->_contiguous) {
            $currentRow = $this->_contiguousRow == -1 ? $sheet->getHighestRow() : $this->_contiguousRow;
        }
        while (($rowData = fgetcsv($fileHandle, 0, $this->_delimiter, $this->_enclosure)) !== false) {
            $columnLetter = 'A';
            foreach ($rowData as $rowDatum) {
                if ($rowDatum != '' && $this->_readFilter->readCell($columnLetter, $currentRow)) {
                    $rowDatum = str_replace($escapeEnclosures, $this->_enclosure, $rowDatum);
                    if ($this->_inputEncoding !== 'UTF-8') {
                        $rowDatum = PHPExcel_Shared_String::ConvertEncoding($rowDatum, 'UTF-8', $this->_inputEncoding);
                    }
                    $sheet->getCell($columnLetter . $currentRow)->setValue($rowDatum);
                }
                ++$columnLetter;
            }
            ++$currentRow;
        }
        fclose($fileHandle);
        if ($this->_contiguous) {
            $this->_contiguousRow = $currentRow;
        }
        ini_set('auto_detect_line_endings', $lineEnding);
        return $objPHPExcel;
    }
    public function getDelimiter()
    {
        return $this->_delimiter;
    }
    public function setDelimiter($pValue = ',')
    {
        $this->_delimiter = $pValue;
        return $this;
    }
    public function getEnclosure()
    {
        return $this->_enclosure;
    }
    public function setEnclosure($pValue = '"')
    {
        if ($pValue == '') {
            $pValue = '"';
        }
        $this->_enclosure = $pValue;
        return $this;
    }
    public function getLineEnding()
    {
        return $this->_lineEnding;
    }
    public function setLineEnding($pValue = PHP_EOL)
    {
        $this->_lineEnding = $pValue;
        return $this;
    }
    public function getSheetIndex()
    {
        return $this->_sheetIndex;
    }
    public function setSheetIndex($pValue = 0)
    {
        $this->_sheetIndex = $pValue;
        return $this;
    }
    public function setContiguous($contiguous = false)
    {
        $this->_contiguous = (bool) $contiguous;
        if (!$contiguous) {
            $this->_contiguousRow = -1;
        }
        return $this;
    }
    public function getContiguous()
    {
        return $this->_contiguous;
    }
}