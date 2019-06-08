<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Reader_HTML extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
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
    private $_formats = array('h1' => array('font' => array('bold' => true, 'size' => 24)), 'h2' => array('font' => array('bold' => true, 'size' => 18)), 'h3' => array('font' => array('bold' => true, 'size' => 13.5)), 'h4' => array('font' => array('bold' => true, 'size' => 12)), 'h5' => array('font' => array('bold' => true, 'size' => 10)), 'h6' => array('font' => array('bold' => true, 'size' => 7.5)), 'a' => array('font' => array('underline' => true, 'color' => array('argb' => PHPExcel_Style_Color::COLOR_BLUE))), 'hr' => array('borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array(PHPExcel_Style_Color::COLOR_BLACK)))));
    private $_dataArray = array();
    private $_tableLevel = 0;
    private $_nestedColumn = array('A');
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }
    protected function _isValidFormat()
    {
        $data = fread($this->_fileHandle, 2048);
        if (strpos($data, '<') !== false && strlen($data) !== strlen(strip_tags($data))) {
            return true;
        }
        return false;
    }
    public function load($pFilename)
    {
        $objPHPExcel = new PHPExcel();
        return $this->loadIntoExisting($pFilename, $objPHPExcel);
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
    private function _setTableStartColumn($column)
    {
        if ($this->_tableLevel == 0) {
            $column = 'A';
        }
        ++$this->_tableLevel;
        $this->_nestedColumn[$this->_tableLevel] = $column;
        return $this->_nestedColumn[$this->_tableLevel];
    }
    private function _getTableStartColumn()
    {
        return $this->_nestedColumn[$this->_tableLevel];
    }
    private function _releaseTableStartColumn()
    {
        --$this->_tableLevel;
        return array_pop($this->_nestedColumn);
    }
    private function _flushCell($sheet, $column, $row, &$cellContent)
    {
        if (is_string($cellContent)) {
            if ('' < trim($cellContent)) {
                $cell = $sheet->setCellValue($column . $row, $cellContent, true);
                $this->_dataArray[$row][$column] = $cellContent;
            }
        } else {
            $this->_dataArray[$row][$column] = 'RICH TEXT: ' . $cellContent;
        }
        $cellContent = (string) '';
    }
    private function _processDomElement(DOMNode $element, $sheet, &$row, &$column, &$cellContent)
    {
        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMText) {
                $domText = preg_replace('/\\s+/', ' ', trim($child->nodeValue));
                if (is_string($cellContent)) {
                    $cellContent .= $domText;
                }
            } else {
                if ($child instanceof DOMElement) {
                    $attributeArray = array();
                    foreach ($child->attributes as $attribute) {
                        $attributeArray[$attribute->name] = $attribute->value;
                    }
                    switch ($child->nodeName) {
                        case 'meta':
                            foreach ($attributeArray as $attributeName => $attributeValue) {
                                switch ($attributeName) {
                                    case 'content':
                                        break;
                                }
                            }
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            break;
                        case 'title':
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            $sheet->setTitle($cellContent);
                            $cellContent = '';
                            break;
                        case 'span':
                        case 'div':
                        case 'font':
                        case 'i':
                        case 'em':
                        case 'strong':
                        case 'b':
                            if ('' < $cellContent) {
                                $cellContent .= ' ';
                            }
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            if ('' < $cellContent) {
                                $cellContent .= ' ';
                            }
                            break;
                        case 'hr':
                            $this->_flushCell($sheet, $column, $row, $cellContent);
                            ++$row;
                            if (isset($this->_formats[$child->nodeName])) {
                                $sheet->getStyle($column . $row)->applyFromArray($this->_formats[$child->nodeName]);
                            } else {
                                $cellContent = '----------';
                                $this->_flushCell($sheet, $column, $row, $cellContent);
                            }
                            ++$row;
                        case 'br':
                            if (0 < $this->_tableLevel) {
                                $cellContent .= "\n";
                            } else {
                                $this->_flushCell($sheet, $column, $row, $cellContent);
                                ++$row;
                            }
                            break;
                        case 'a':
                            foreach ($attributeArray as $attributeName => $attributeValue) {
                                switch ($attributeName) {
                                    case 'href':
                                        $sheet->getCell($column . $row)->getHyperlink()->setUrl($attributeValue);
                                        if (isset($this->_formats[$child->nodeName])) {
                                            $sheet->getStyle($column . $row)->applyFromArray($this->_formats[$child->nodeName]);
                                        }
                                        break;
                                }
                            }
                            $cellContent .= ' ';
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            break;
                        case 'h1':
                        case 'h2':
                        case 'h3':
                        case 'h4':
                        case 'h5':
                        case 'h6':
                        case 'ol':
                        case 'ul':
                        case 'p':
                            if (0 < $this->_tableLevel) {
                                $cellContent .= "\n";
                                $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            } else {
                                if ('' < $cellContent) {
                                    $this->_flushCell($sheet, $column, $row, $cellContent);
                                    $row += 2;
                                }
                                $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                                $this->_flushCell($sheet, $column, $row, $cellContent);
                                if (isset($this->_formats[$child->nodeName])) {
                                    $sheet->getStyle($column . $row)->applyFromArray($this->_formats[$child->nodeName]);
                                }
                                $row += 2;
                                $column = 'A';
                            }
                            break;
                        case 'li':
                            if (0 < $this->_tableLevel) {
                                $cellContent .= "\n";
                                $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            } else {
                                if ('' < $cellContent) {
                                    $this->_flushCell($sheet, $column, $row, $cellContent);
                                }
                                ++$row;
                                $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                                $this->_flushCell($sheet, $column, $row, $cellContent);
                                $column = 'A';
                            }
                            break;
                        case 'table':
                            $this->_flushCell($sheet, $column, $row, $cellContent);
                            $column = $this->_setTableStartColumn($column);
                            if (1 < $this->_tableLevel) {
                                --$row;
                            }
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            $column = $this->_releaseTableStartColumn();
                            if (1 < $this->_tableLevel) {
                                ++$column;
                            } else {
                                ++$row;
                            }
                            break;
                        case 'thead':
                        case 'tbody':
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            break;
                        case 'tr':
                            ++$row;
                            $column = $this->_getTableStartColumn();
                            $cellContent = '';
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            break;
                        case 'th':
                        case 'td':
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            $this->_flushCell($sheet, $column, $row, $cellContent);
                            ++$column;
                            break;
                        case 'body':
                            $row = 1;
                            $column = 'A';
                            $content = '';
                            $this->_tableLevel = 0;
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                            break;
                        default:
                            $this->_processDomElement($child, $sheet, $row, $column, $cellContent);
                    }
                }
            }
        }
    }
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        $this->_openFile($pFilename);
        if (!$this->_isValidFormat()) {
            fclose($this->_fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . ' is an Invalid HTML file.');
        }
        fclose($this->_fileHandle);
        while ($objPHPExcel->getSheetCount() <= $this->_sheetIndex) {
            $objPHPExcel->createSheet();
        }
        $objPHPExcel->setActiveSheetIndex($this->_sheetIndex);
        $dom = new domDocument();
        $loaded = $dom->loadHTMLFile($pFilename, PHPExcel_Settings::getLibXmlLoaderOptions());
        if ($loaded === false) {
            throw new PHPExcel_Reader_Exception('Failed to load ', $pFilename, ' as a DOM Document');
        }
        $dom->preserveWhiteSpace = false;
        $row = 0;
        $column = 'A';
        $content = '';
        $this->_processDomElement($dom, $objPHPExcel->getActiveSheet(), $row, $column, $content);
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