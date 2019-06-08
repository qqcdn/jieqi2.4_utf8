<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Reader_Excel2003XML extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Formats
     *
     * @var array
     */
    private $_styles = array();
    /**
     * Character set used in the file
     *
     * @var string
     */
    private $_charSet = 'UTF-8';
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }
    public function canRead($pFilename)
    {
        $signature = array('<?xml version="1.0"', '<?mso-application progid="Excel.Sheet"?>');
        $this->_openFile($pFilename);
        $fileHandle = $this->_fileHandle;
        $data = fread($fileHandle, 2048);
        fclose($fileHandle);
        $valid = true;
        foreach ($signature as $match) {
            if (strpos($data, $match) === false) {
                $valid = false;
                break;
            }
        }
        if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/um', $data, $matches)) {
            $this->_charSet = strtoupper($matches[1]);
        }
        return $valid;
    }
    public function listWorksheetNames($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        if (!$this->canRead($pFilename)) {
            throw new PHPExcel_Reader_Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $worksheetNames = array();
        $xml = simplexml_load_file($pFilename, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
        $namespaces = $xml->getNamespaces(true);
        $xml_ss = $xml->children($namespaces['ss']);
        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = $worksheet->attributes($namespaces['ss']);
            $worksheetNames[] = self::_convertStringEncoding((string) $worksheet_ss['Name'], $this->_charSet);
        }
        return $worksheetNames;
    }
    public function listWorksheetInfo($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $worksheetInfo = array();
        $xml = simplexml_load_file($pFilename, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
        $namespaces = $xml->getNamespaces(true);
        $worksheetID = 1;
        $xml_ss = $xml->children($namespaces['ss']);
        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = $worksheet->attributes($namespaces['ss']);
            $tmpInfo = array();
            $tmpInfo['worksheetName'] = '';
            $tmpInfo['lastColumnLetter'] = 'A';
            $tmpInfo['lastColumnIndex'] = 0;
            $tmpInfo['totalRows'] = 0;
            $tmpInfo['totalColumns'] = 0;
            if (isset($worksheet_ss['Name'])) {
                $tmpInfo['worksheetName'] = (string) $worksheet_ss['Name'];
            } else {
                $tmpInfo['worksheetName'] = 'Worksheet_' . $worksheetID;
            }
            if (isset($worksheet->Table->Row)) {
                $rowIndex = 0;
                foreach ($worksheet->Table->Row as $rowData) {
                    $columnIndex = 0;
                    $rowHasData = false;
                    foreach ($rowData->Cell as $cell) {
                        if (isset($cell->Data)) {
                            $tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
                            $rowHasData = true;
                        }
                        ++$columnIndex;
                    }
                    ++$rowIndex;
                    if ($rowHasData) {
                        $tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
                    }
                }
            }
            $tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
            $tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;
            $worksheetInfo[] = $tmpInfo;
            ++$worksheetID;
        }
        return $worksheetInfo;
    }
    public function load($pFilename)
    {
        $objPHPExcel = new PHPExcel();
        return $this->loadIntoExisting($pFilename, $objPHPExcel);
    }
    private static function identifyFixedStyleValue($styleList, &$styleAttributeValue)
    {
        $styleAttributeValue = strtolower($styleAttributeValue);
        foreach ($styleList as $style) {
            if ($styleAttributeValue == strtolower($style)) {
                $styleAttributeValue = $style;
                return true;
            }
        }
        return false;
    }
    private static function _pixel2WidthUnits($pxs)
    {
        $UNIT_OFFSET_MAP = array(0, 36, 73, 109, 146, 182, 219);
        $widthUnits = 256 * ($pxs / 7);
        $widthUnits += $UNIT_OFFSET_MAP[$pxs % 7];
        return $widthUnits;
    }
    private static function _widthUnits2Pixel($widthUnits)
    {
        $pixels = $widthUnits / 256 * 7;
        $offsetWidthUnits = $widthUnits % 256;
        $pixels += round($offsetWidthUnits / 256 / 7);
        return $pixels;
    }
    private static function _hex2str($hex)
    {
        return chr(hexdec($hex[1]));
    }
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        $fromFormats = array('\\-', '\\ ');
        $toFormats = array('-', ' ');
        $underlineStyles = array(PHPExcel_Style_Font::UNDERLINE_NONE, PHPExcel_Style_Font::UNDERLINE_DOUBLE, PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING, PHPExcel_Style_Font::UNDERLINE_SINGLE, PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING);
        $verticalAlignmentStyles = array(PHPExcel_Style_Alignment::VERTICAL_BOTTOM, PHPExcel_Style_Alignment::VERTICAL_TOP, PHPExcel_Style_Alignment::VERTICAL_CENTER, PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
        $horizontalAlignmentStyles = array(PHPExcel_Style_Alignment::HORIZONTAL_GENERAL, PHPExcel_Style_Alignment::HORIZONTAL_LEFT, PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, PHPExcel_Style_Alignment::HORIZONTAL_CENTER, PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS, PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new DateTimeZone('UTC');
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        if (!$this->canRead($pFilename)) {
            throw new PHPExcel_Reader_Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $xml = simplexml_load_file($pFilename, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
        $namespaces = $xml->getNamespaces(true);
        $docProps = $objPHPExcel->getProperties();
        if (isset($xml->DocumentProperties[0])) {
            foreach ($xml->DocumentProperties[0] as $propertyName => $propertyValue) {
                switch ($propertyName) {
                    case 'Title':
                        $docProps->setTitle(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'Subject':
                        $docProps->setSubject(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'Author':
                        $docProps->setCreator(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'Created':
                        $creationDate = strtotime($propertyValue);
                        $docProps->setCreated($creationDate);
                        break;
                    case 'LastAuthor':
                        $docProps->setLastModifiedBy(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'LastSaved':
                        $lastSaveDate = strtotime($propertyValue);
                        $docProps->setModified($lastSaveDate);
                        break;
                    case 'Company':
                        $docProps->setCompany(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'Category':
                        $docProps->setCategory(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'Manager':
                        $docProps->setManager(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'Keywords':
                        $docProps->setKeywords(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                    case 'Description':
                        $docProps->setDescription(self::_convertStringEncoding($propertyValue, $this->_charSet));
                        break;
                }
            }
        }
        if (isset($xml->CustomDocumentProperties)) {
            foreach ($xml->CustomDocumentProperties[0] as $propertyName => $propertyValue) {
                $propertyAttributes = $propertyValue->attributes($namespaces['dt']);
                $propertyName = preg_replace_callback('/_x([0-9a-z]{4})_/', 'PHPExcel_Reader_Excel2003XML::_hex2str', $propertyName);
                $propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_UNKNOWN;
                switch ((string) $propertyAttributes) {
                    case 'string':
                        $propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
                        $propertyValue = trim($propertyValue);
                        break;
                    case 'boolean':
                        $propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_BOOLEAN;
                        $propertyValue = (bool) $propertyValue;
                        break;
                    case 'integer':
                        $propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_INTEGER;
                        $propertyValue = intval($propertyValue);
                        break;
                    case 'float':
                        $propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_FLOAT;
                        $propertyValue = floatval($propertyValue);
                        break;
                    case 'dateTime.tz':
                        $propertyType = PHPExcel_DocumentProperties::PROPERTY_TYPE_DATE;
                        $propertyValue = strtotime(trim($propertyValue));
                        break;
                }
                (string) $propertyAttributes;
                $docProps->setCustomProperty($propertyName, $propertyValue, $propertyType);
            }
        }
        foreach ($xml->Styles[0] as $style) {
            $style_ss = $style->attributes($namespaces['ss']);
            $styleID = (string) $style_ss['ID'];
            if ($styleID == 'Default') {
                $this->_styles['Default'] = array();
            } else {
                $this->_styles[$styleID] = $this->_styles['Default'];
            }
            foreach ($style as $styleType => $styleData) {
                $styleAttributes = $styleData->attributes($namespaces['ss']);
                switch ($styleType) {
                    case 'Alignment':
                        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
                            $styleAttributeValue = (string) $styleAttributeValue;
                            switch ($styleAttributeKey) {
                                case 'Vertical':
                                    if (self::identifyFixedStyleValue($verticalAlignmentStyles, $styleAttributeValue)) {
                                        $this->_styles[$styleID]['alignment']['vertical'] = $styleAttributeValue;
                                    }
                                    break;
                                case 'Horizontal':
                                    if (self::identifyFixedStyleValue($horizontalAlignmentStyles, $styleAttributeValue)) {
                                        $this->_styles[$styleID]['alignment']['horizontal'] = $styleAttributeValue;
                                    }
                                    break;
                                case 'WrapText':
                                    $this->_styles[$styleID]['alignment']['wrap'] = true;
                                    break;
                            }
                        }
                        break;
                    case 'Borders':
                        foreach ($styleData->Border as $borderStyle) {
                            $borderAttributes = $borderStyle->attributes($namespaces['ss']);
                            $thisBorder = array();
                            foreach ($borderAttributes as $borderStyleKey => $borderStyleValue) {
                                switch ($borderStyleKey) {
                                    case 'LineStyle':
                                        $thisBorder['style'] = PHPExcel_Style_Border::BORDER_MEDIUM;
                                        break;
                                    case 'Weight':
                                        break;
                                    case 'Position':
                                        $borderPosition = strtolower($borderStyleValue);
                                        break;
                                    case 'Color':
                                        $borderColour = substr($borderStyleValue, 1);
                                        $thisBorder['color']['rgb'] = $borderColour;
                                        break;
                                }
                            }
                            if (!empty($thisBorder)) {
                                if ($borderPosition == 'left' || $borderPosition == 'right' || $borderPosition == 'top' || $borderPosition == 'bottom') {
                                    $this->_styles[$styleID]['borders'][$borderPosition] = $thisBorder;
                                }
                            }
                        }
                        break;
                    case 'Font':
                        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
                            $styleAttributeValue = (string) $styleAttributeValue;
                            switch ($styleAttributeKey) {
                                case 'FontName':
                                    $this->_styles[$styleID]['font']['name'] = $styleAttributeValue;
                                    break;
                                case 'Size':
                                    $this->_styles[$styleID]['font']['size'] = $styleAttributeValue;
                                    break;
                                case 'Color':
                                    $this->_styles[$styleID]['font']['color']['rgb'] = substr($styleAttributeValue, 1);
                                    break;
                                case 'Bold':
                                    $this->_styles[$styleID]['font']['bold'] = true;
                                    break;
                                case 'Italic':
                                    $this->_styles[$styleID]['font']['italic'] = true;
                                    break;
                                case 'Underline':
                                    if (self::identifyFixedStyleValue($underlineStyles, $styleAttributeValue)) {
                                        $this->_styles[$styleID]['font']['underline'] = $styleAttributeValue;
                                    }
                                    break;
                            }
                        }
                        break;
                    case 'Interior':
                        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
                            switch ($styleAttributeKey) {
                                case 'Color':
                                    $this->_styles[$styleID]['fill']['color']['rgb'] = substr($styleAttributeValue, 1);
                                    break;
                            }
                        }
                        break;
                    case 'NumberFormat':
                        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
                            $styleAttributeValue = str_replace($fromFormats, $toFormats, $styleAttributeValue);
                            switch ($styleAttributeValue) {
                                case 'Short Date':
                                    $styleAttributeValue = 'dd/mm/yyyy';
                                    break;
                            }
                            if ('' < $styleAttributeValue) {
                                $this->_styles[$styleID]['numberformat']['code'] = $styleAttributeValue;
                            }
                        }
                        break;
                    case 'Protection':
                        foreach ($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
                        }
                        break;
                }
            }
        }
        $worksheetID = 0;
        $xml_ss = $xml->children($namespaces['ss']);
        foreach ($xml_ss->Worksheet as $worksheet) {
            $worksheet_ss = $worksheet->attributes($namespaces['ss']);
            if (isset($this->_loadSheetsOnly) && isset($worksheet_ss['Name']) && !in_array($worksheet_ss['Name'], $this->_loadSheetsOnly)) {
                continue;
            }
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex($worksheetID);
            if (isset($worksheet_ss['Name'])) {
                $worksheetName = self::_convertStringEncoding((string) $worksheet_ss['Name'], $this->_charSet);
                $objPHPExcel->getActiveSheet()->setTitle($worksheetName, false);
            }
            $columnID = 'A';
            if (isset($worksheet->Table->Column)) {
                foreach ($worksheet->Table->Column as $columnData) {
                    $columnData_ss = $columnData->attributes($namespaces['ss']);
                    if (isset($columnData_ss['Index'])) {
                        $columnID = PHPExcel_Cell::stringFromColumnIndex($columnData_ss['Index'] - 1);
                    }
                    if (isset($columnData_ss['Width'])) {
                        $columnWidth = $columnData_ss['Width'];
                        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setWidth($columnWidth / 5.4);
                    }
                    ++$columnID;
                }
            }
            $rowID = 1;
            if (isset($worksheet->Table->Row)) {
                foreach ($worksheet->Table->Row as $rowData) {
                    $rowHasData = false;
                    $row_ss = $rowData->attributes($namespaces['ss']);
                    if (isset($row_ss['Index'])) {
                        $rowID = (int) $row_ss['Index'];
                    }
                    $columnID = 'A';
                    foreach ($rowData->Cell as $cell) {
                        $cell_ss = $cell->attributes($namespaces['ss']);
                        if (isset($cell_ss['Index'])) {
                            $columnID = PHPExcel_Cell::stringFromColumnIndex($cell_ss['Index'] - 1);
                        }
                        $cellRange = $columnID . $rowID;
                        if ($this->getReadFilter() !== NULL) {
                            if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                continue;
                            }
                        }
                        if (isset($cell_ss['MergeAcross']) || isset($cell_ss['MergeDown'])) {
                            $columnTo = $columnID;
                            if (isset($cell_ss['MergeAcross'])) {
                                $columnTo = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cell_ss['MergeAcross'] - 1);
                            }
                            $rowTo = $rowID;
                            if (isset($cell_ss['MergeDown'])) {
                                $rowTo = $rowTo + $cell_ss['MergeDown'];
                            }
                            $cellRange .= ':' . $columnTo . $rowTo;
                            $objPHPExcel->getActiveSheet()->mergeCells($cellRange);
                        }
                        $cellIsSet = $hasCalculatedValue = false;
                        $cellDataFormula = '';
                        if (isset($cell_ss['Formula'])) {
                            $cellDataFormula = $cell_ss['Formula'];
                            if (isset($cell_ss['ArrayRange'])) {
                                $cellDataCSEFormula = $cell_ss['ArrayRange'];
                            }
                            $hasCalculatedValue = true;
                        }
                        if (isset($cell->Data)) {
                            $cellValue = $cellData = $cell->Data;
                            $type = PHPExcel_Cell_DataType::TYPE_NULL;
                            $cellData_ss = $cellData->attributes($namespaces['ss']);
                            if (isset($cellData_ss['Type'])) {
                                $cellDataType = $cellData_ss['Type'];
                                switch ($cellDataType) {
                                    case 'String':
                                        $cellValue = self::_convertStringEncoding($cellValue, $this->_charSet);
                                        $type = PHPExcel_Cell_DataType::TYPE_STRING;
                                        break;
                                    case 'Number':
                                        $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                        $cellValue = (double) $cellValue;
                                        if (floor($cellValue) == $cellValue) {
                                            $cellValue = (int) $cellValue;
                                        }
                                        break;
                                    case 'Boolean':
                                        $type = PHPExcel_Cell_DataType::TYPE_BOOL;
                                        $cellValue = $cellValue != 0;
                                        break;
                                    case 'DateTime':
                                        $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                        $cellValue = PHPExcel_Shared_Date::PHPToExcel(strtotime($cellValue));
                                        break;
                                    case 'Error':
                                        $type = PHPExcel_Cell_DataType::TYPE_ERROR;
                                        break;
                                }
                            }
                            if ($hasCalculatedValue) {
                                $type = PHPExcel_Cell_DataType::TYPE_FORMULA;
                                $columnNumber = PHPExcel_Cell::columnIndexFromString($columnID);
                                if (substr($cellDataFormula, 0, 3) == 'of:') {
                                    $cellDataFormula = substr($cellDataFormula, 3);
                                    $temp = explode('"', $cellDataFormula);
                                    $key = false;
                                    foreach ($temp as &$value) {
                                        if ($key = !$key) {
                                            $value = str_replace(array('[.', '.', ']'), '', $value);
                                        }
                                    }
                                } else {
                                    $temp = explode('"', $cellDataFormula);
                                    $key = false;
                                    foreach ($temp as &$value) {
                                        if ($key = !$key) {
                                            preg_match_all('/(R(\\[?-?\\d*\\]?))(C(\\[?-?\\d*\\]?))/', $value, $cellReferences, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                                            $cellReferences = array_reverse($cellReferences);
                                            foreach ($cellReferences as $cellReference) {
                                                $rowReference = $cellReference[2][0];
                                                if ($rowReference == '') {
                                                    $rowReference = $rowID;
                                                }
                                                if ($rowReference[0] == '[') {
                                                    $rowReference = $rowID + trim($rowReference, '[]');
                                                }
                                                $columnReference = $cellReference[4][0];
                                                if ($columnReference == '') {
                                                    $columnReference = $columnNumber;
                                                }
                                                if ($columnReference[0] == '[') {
                                                    $columnReference = $columnNumber + trim($columnReference, '[]');
                                                }
                                                $A1CellReference = PHPExcel_Cell::stringFromColumnIndex($columnReference - 1) . $rowReference;
                                                $value = substr_replace($value, $A1CellReference, $cellReference[0][1], strlen($cellReference[0][0]));
                                            }
                                        }
                                    }
                                }
                                unset($value);
                                $cellDataFormula = implode('"', $temp);
                            }
                            $objPHPExcel->getActiveSheet()->getCell($columnID . $rowID)->setValueExplicit($hasCalculatedValue ? $cellDataFormula : $cellValue, $type);
                            if ($hasCalculatedValue) {
                                $objPHPExcel->getActiveSheet()->getCell($columnID . $rowID)->setCalculatedValue($cellValue);
                            }
                            $cellIsSet = $rowHasData = true;
                        }
                        if (isset($cell->Comment)) {
                            $commentAttributes = $cell->Comment->attributes($namespaces['ss']);
                            $author = 'unknown';
                            if (isset($commentAttributes->Author)) {
                                $author = (string) $commentAttributes->Author;
                            }
                            $node = $cell->Comment->Data->asXML();
                            $annotation = strip_tags($node);
                            $objPHPExcel->getActiveSheet()->getComment($columnID . $rowID)->setAuthor(self::_convertStringEncoding($author, $this->_charSet))->setText($this->_parseRichText($annotation));
                        }
                        if ($cellIsSet && isset($cell_ss['StyleID'])) {
                            $style = (string) $cell_ss['StyleID'];
                            if (isset($this->_styles[$style]) && !empty($this->_styles[$style])) {
                                if (!$objPHPExcel->getActiveSheet()->cellExists($columnID . $rowID)) {
                                    $objPHPExcel->getActiveSheet()->getCell($columnID . $rowID)->setValue(NULL);
                                }
                                $objPHPExcel->getActiveSheet()->getStyle($cellRange)->applyFromArray($this->_styles[$style]);
                            }
                        }
                        ++$columnID;
                    }
                    if ($rowHasData) {
                        if (isset($row_ss['StyleID'])) {
                            $rowStyle = $row_ss['StyleID'];
                        }
                        if (isset($row_ss['Height'])) {
                            $rowHeight = $row_ss['Height'];
                            $objPHPExcel->getActiveSheet()->getRowDimension($rowID)->setRowHeight($rowHeight);
                        }
                    }
                    ++$rowID;
                }
            }
            ++$worksheetID;
        }
        return $objPHPExcel;
    }
    private static function _convertStringEncoding($string, $charset)
    {
        if ($charset != 'UTF-8') {
            return PHPExcel_Shared_String::ConvertEncoding($string, 'UTF-8', $charset);
        }
        return $string;
    }
    private function _parseRichText($is = '')
    {
        $value = new PHPExcel_RichText();
        $value->createText(self::_convertStringEncoding($is, $this->_charSet));
        return $value;
    }
}