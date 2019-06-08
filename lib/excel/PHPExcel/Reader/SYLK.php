<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Reader_SYLK extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Input encoding
     *
     * @var string
     */
    private $_inputEncoding = 'ANSI';
    /**
     * Sheet index to read
     *
     * @var int
     */
    private $_sheetIndex = 0;
    /**
     * Formats
     *
     * @var array
     */
    private $_formats = array();
    /**
     * Format Count
     *
     * @var int
     */
    private $_format = 0;
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }
    protected function _isValidFormat()
    {
        $data = fread($this->_fileHandle, 2048);
        $delimiterCount = substr_count($data, ';');
        if ($delimiterCount < 1) {
            return false;
        }
        $lines = explode("\n", $data);
        if (substr($lines[0], 0, 4) != 'ID;P') {
            return false;
        }
        return true;
    }
    public function setInputEncoding($pValue = 'ANSI')
    {
        $this->_inputEncoding = $pValue;
        return $this;
    }
    public function getInputEncoding()
    {
        return $this->_inputEncoding;
    }
    public function listWorksheetInfo($pFilename)
    {
        $this->_openFile($pFilename);
        if (!$this->_isValidFormat()) {
            fclose($this->_fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $fileHandle = $this->_fileHandle;
        rewind($fileHandle);
        $worksheetInfo = array();
        $worksheetInfo[0]['worksheetName'] = 'Worksheet';
        $worksheetInfo[0]['lastColumnLetter'] = 'A';
        $worksheetInfo[0]['lastColumnIndex'] = 0;
        $worksheetInfo[0]['totalRows'] = 0;
        $worksheetInfo[0]['totalColumns'] = 0;
        $rowData = array();
        $rowIndex = 0;
        while (($rowData = fgets($fileHandle)) !== false) {
            $columnIndex = 0;
            $rowData = PHPExcel_Shared_String::SYLKtoUTF8($rowData);
            $rowData = explode('	', str_replace('陇', ';', str_replace(';', '	', str_replace(';;', '陇', rtrim($rowData)))));
            $dataType = array_shift($rowData);
            if ($dataType == 'C') {
                foreach ($rowData as $rowDatum) {
                    switch ($rowDatum[0]) {
                        case 'C':
                        case 'X':
                            $columnIndex = substr($rowDatum, 1) - 1;
                            break;
                        case 'R':
                        case 'Y':
                            $rowIndex = substr($rowDatum, 1);
                            break;
                    }
                    $worksheetInfo[0]['totalRows'] = max($worksheetInfo[0]['totalRows'], $rowIndex);
                    $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], $columnIndex);
                }
            }
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
        $this->_openFile($pFilename);
        if (!$this->_isValidFormat()) {
            fclose($this->_fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $fileHandle = $this->_fileHandle;
        rewind($fileHandle);
        while ($objPHPExcel->getSheetCount() <= $this->_sheetIndex) {
            $objPHPExcel->createSheet();
        }
        $objPHPExcel->setActiveSheetIndex($this->_sheetIndex);
        $fromFormats = array('\\-', '\\ ');
        $toFormats = array('-', ' ');
        $rowData = array();
        $column = $row = '';
        while (($rowData = fgets($fileHandle)) !== false) {
            $rowData = PHPExcel_Shared_String::SYLKtoUTF8($rowData);
            $rowData = explode('	', str_replace('陇', ';', str_replace(';', '	', str_replace(';;', '陇', rtrim($rowData)))));
            $dataType = array_shift($rowData);
            if ($dataType == 'P') {
                $formatArray = array();
                foreach ($rowData as $rowDatum) {
                    switch ($rowDatum[0]) {
                        case 'P':
                            $formatArray['numberformat']['code'] = str_replace($fromFormats, $toFormats, substr($rowDatum, 1));
                            break;
                        case 'E':
                        case 'F':
                            $formatArray['font']['name'] = substr($rowDatum, 1);
                            break;
                        case 'L':
                            $formatArray['font']['size'] = substr($rowDatum, 1);
                            break;
                        case 'S':
                            $styleSettings = substr($rowDatum, 1);
                            for ($i = 0; $i < strlen($styleSettings); ++$i) {
                                switch ($styleSettings[$i]) {
                                    case 'I':
                                        $formatArray['font']['italic'] = true;
                                        break;
                                    case 'D':
                                        $formatArray['font']['bold'] = true;
                                        break;
                                    case 'T':
                                        $formatArray['borders']['top']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                        break;
                                    case 'B':
                                        $formatArray['borders']['bottom']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                        break;
                                    case 'L':
                                        $formatArray['borders']['left']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                        break;
                                    case 'R':
                                        $formatArray['borders']['right']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                        break;
                                }
                            }
                            break;
                    }
                }
                $this->_formats['P' . $this->_format++] = $formatArray;
            } else {
                if ($dataType == 'C') {
                    $hasCalculatedValue = false;
                    $cellData = $cellDataFormula = '';
                    foreach ($rowData as $rowDatum) {
                        switch ($rowDatum[0]) {
                            case 'C':
                            case 'X':
                                $column = substr($rowDatum, 1);
                                break;
                            case 'R':
                            case 'Y':
                                $row = substr($rowDatum, 1);
                                break;
                            case 'K':
                                $cellData = substr($rowDatum, 1);
                                break;
                            case 'E':
                                $cellDataFormula = '=' . substr($rowDatum, 1);
                                $temp = explode('"', $cellDataFormula);
                                $key = false;
                                foreach ($temp as &$value) {
                                    if ($key = !$key) {
                                        preg_match_all('/(R(\\[?-?\\d*\\]?))(C(\\[?-?\\d*\\]?))/', $value, $cellReferences, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                                        $cellReferences = array_reverse($cellReferences);
                                        foreach ($cellReferences as $cellReference) {
                                            $rowReference = $cellReference[2][0];
                                            if ($rowReference == '') {
                                                $rowReference = $row;
                                            }
                                            if ($rowReference[0] == '[') {
                                                $rowReference = $row + trim($rowReference, '[]');
                                            }
                                            $columnReference = $cellReference[4][0];
                                            if ($columnReference == '') {
                                                $columnReference = $column;
                                            }
                                            if ($columnReference[0] == '[') {
                                                $columnReference = $column + trim($columnReference, '[]');
                                            }
                                            $A1CellReference = PHPExcel_Cell::stringFromColumnIndex($columnReference - 1) . $rowReference;
                                            $value = substr_replace($value, $A1CellReference, $cellReference[0][1], strlen($cellReference[0][0]));
                                        }
                                    }
                                }
                                unset($value);
                                $cellDataFormula = implode('"', $temp);
                                $hasCalculatedValue = true;
                                break;
                        }
                    }
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($column - 1);
                    $cellData = PHPExcel_Calculation::_unwrapResult($cellData);
                    $objPHPExcel->getActiveSheet()->getCell($columnLetter . $row)->setValue($hasCalculatedValue ? $cellDataFormula : $cellData);
                    if ($hasCalculatedValue) {
                        $cellData = PHPExcel_Calculation::_unwrapResult($cellData);
                        $objPHPExcel->getActiveSheet()->getCell($columnLetter . $row)->setCalculatedValue($cellData);
                    }
                } else {
                    if ($dataType == 'F') {
                        $formatStyle = $columnWidth = $styleSettings = '';
                        $styleData = array();
                        foreach ($rowData as $rowDatum) {
                            switch ($rowDatum[0]) {
                                case 'C':
                                case 'X':
                                    $column = substr($rowDatum, 1);
                                    break;
                                case 'R':
                                case 'Y':
                                    $row = substr($rowDatum, 1);
                                    break;
                                case 'P':
                                    $formatStyle = $rowDatum;
                                    break;
                                case 'W':
                                    list($startCol, $endCol, $columnWidth) = explode(' ', substr($rowDatum, 1));
                                    break;
                                case 'S':
                                    $styleSettings = substr($rowDatum, 1);
                                    for ($i = 0; $i < strlen($styleSettings); ++$i) {
                                        switch ($styleSettings[$i]) {
                                            case 'I':
                                                $styleData['font']['italic'] = true;
                                                break;
                                            case 'D':
                                                $styleData['font']['bold'] = true;
                                                break;
                                            case 'T':
                                                $styleData['borders']['top']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                                break;
                                            case 'B':
                                                $styleData['borders']['bottom']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                                break;
                                            case 'L':
                                                $styleData['borders']['left']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                                break;
                                            case 'R':
                                                $styleData['borders']['right']['style'] = PHPExcel_Style_Border::BORDER_THIN;
                                                break;
                                        }
                                    }
                                    break;
                            }
                        }
                        if ('' < $formatStyle && '' < $column && '' < $row) {
                            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($column - 1);
                            if (isset($this->_formats[$formatStyle])) {
                                $objPHPExcel->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($this->_formats[$formatStyle]);
                            }
                        }
                        if (!empty($styleData) && '' < $column && '' < $row) {
                            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($column - 1);
                            $objPHPExcel->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($styleData);
                        }
                        if ('' < $columnWidth) {
                            if ($startCol == $endCol) {
                                $startCol = PHPExcel_Cell::stringFromColumnIndex($startCol - 1);
                                $objPHPExcel->getActiveSheet()->getColumnDimension($startCol)->setWidth($columnWidth);
                            } else {
                                $startCol = PHPExcel_Cell::stringFromColumnIndex($startCol - 1);
                                $endCol = PHPExcel_Cell::stringFromColumnIndex($endCol - 1);
                                $objPHPExcel->getActiveSheet()->getColumnDimension($startCol)->setWidth($columnWidth);
                                do {
                                    $objPHPExcel->getActiveSheet()->getColumnDimension(++$startCol)->setWidth($columnWidth);
                                } while ($startCol != $endCol);
                            }
                        }
                    } else {
                        foreach ($rowData as $rowDatum) {
                            switch ($rowDatum[0]) {
                                case 'C':
                                case 'X':
                                    $column = substr($rowDatum, 1);
                                    break;
                                case 'R':
                                case 'Y':
                                    $row = substr($rowDatum, 1);
                                    break;
                            }
                        }
                    }
                }
            }
        }
        fclose($fileHandle);
        return $objPHPExcel;
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
}