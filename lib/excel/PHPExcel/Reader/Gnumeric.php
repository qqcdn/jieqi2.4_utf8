<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Reader_Gnumeric extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Formats
     *
     * @var array
     */
    private $_styles = array();
    /**
     * Shared Expressions
     *
     * @var array
     */
    private $_expressions = array();
    private $_referenceHelper;
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
        $this->_referenceHelper = PHPExcel_ReferenceHelper::getInstance();
    }
    public function canRead($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        if (!function_exists('gzread')) {
            throw new PHPExcel_Reader_Exception('gzlib library is not enabled');
        }
        $fh = fopen($pFilename, 'r');
        $data = fread($fh, 2);
        fclose($fh);
        if ($data != chr(31) . chr(139)) {
            return false;
        }
        return true;
    }
    public function listWorksheetNames($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $xml = new XMLReader();
        $xml->open('compress.zlib://' . realpath($pFilename), NULL, PHPExcel_Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);
        $worksheetNames = array();
        while ($xml->read()) {
            if ($xml->name == 'gnm:SheetName' && $xml->nodeType == XMLReader::ELEMENT) {
                $xml->read();
                $worksheetNames[] = (string) $xml->value;
            } else {
                if ($xml->name == 'gnm:Sheets') {
                    break;
                }
            }
        }
        return $worksheetNames;
    }
    public function listWorksheetInfo($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $xml = new XMLReader();
        $xml->open('compress.zlib://' . realpath($pFilename), NULL, PHPExcel_Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);
        $worksheetInfo = array();
        while ($xml->read()) {
            if ($xml->name == 'gnm:Sheet' && $xml->nodeType == XMLReader::ELEMENT) {
                $tmpInfo = array('worksheetName' => '', 'lastColumnLetter' => 'A', 'lastColumnIndex' => 0, 'totalRows' => 0, 'totalColumns' => 0);
                while ($xml->read()) {
                    if ($xml->name == 'gnm:Name' && $xml->nodeType == XMLReader::ELEMENT) {
                        $xml->read();
                        $tmpInfo['worksheetName'] = (string) $xml->value;
                    } else {
                        if ($xml->name == 'gnm:MaxCol' && $xml->nodeType == XMLReader::ELEMENT) {
                            $xml->read();
                            $tmpInfo['lastColumnIndex'] = (int) $xml->value;
                            $tmpInfo['totalColumns'] = (int) $xml->value + 1;
                        } else {
                            if ($xml->name == 'gnm:MaxRow' && $xml->nodeType == XMLReader::ELEMENT) {
                                $xml->read();
                                $tmpInfo['totalRows'] = (int) $xml->value + 1;
                                break;
                            }
                        }
                    }
                }
                $tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
                $worksheetInfo[] = $tmpInfo;
            }
        }
        return $worksheetInfo;
    }
    private function _gzfileGetContents($filename)
    {
        $file = @gzopen($filename, 'rb');
        if ($file !== false) {
            $data = '';
            while (!gzeof($file)) {
                $data .= gzread($file, 1024);
            }
            gzclose($file);
        }
        return $data;
    }
    public function load($pFilename)
    {
        $objPHPExcel = new PHPExcel();
        return $this->loadIntoExisting($pFilename, $objPHPExcel);
    }
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new DateTimeZone('UTC');
        $gFileData = $this->_gzfileGetContents($pFilename);
        $xml = simplexml_load_string($gFileData, 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
        $namespacesMeta = $xml->getNamespaces(true);
        $gnmXML = $xml->children($namespacesMeta['gnm']);
        $docProps = $objPHPExcel->getProperties();
        if (isset($namespacesMeta['office'])) {
            $officeXML = $xml->children($namespacesMeta['office']);
            $officeDocXML = $officeXML->{'document-meta'};
            $officeDocMetaXML = $officeDocXML->meta;
            foreach ($officeDocMetaXML as $officePropertyData) {
                $officePropertyDC = array();
                if (isset($namespacesMeta['dc'])) {
                    $officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
                }
                foreach ($officePropertyDC as $propertyName => $propertyValue) {
                    $propertyValue = (string) $propertyValue;
                    switch ($propertyName) {
                        case 'title':
                            $docProps->setTitle(trim($propertyValue));
                            break;
                        case 'subject':
                            $docProps->setSubject(trim($propertyValue));
                            break;
                        case 'creator':
                            $docProps->setCreator(trim($propertyValue));
                            $docProps->setLastModifiedBy(trim($propertyValue));
                            break;
                        case 'date':
                            $creationDate = strtotime(trim($propertyValue));
                            $docProps->setCreated($creationDate);
                            $docProps->setModified($creationDate);
                            break;
                        case 'description':
                            $docProps->setDescription(trim($propertyValue));
                            break;
                    }
                }
                $officePropertyMeta = array();
                if (isset($namespacesMeta['meta'])) {
                    $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
                }
                foreach ($officePropertyMeta as $propertyName => $propertyValue) {
                    $attributes = $propertyValue->attributes($namespacesMeta['meta']);
                    $propertyValue = (string) $propertyValue;
                    switch ($propertyName) {
                        case 'keyword':
                            $docProps->setKeywords(trim($propertyValue));
                            break;
                        case 'initial-creator':
                            $docProps->setCreator(trim($propertyValue));
                            $docProps->setLastModifiedBy(trim($propertyValue));
                            break;
                        case 'creation-date':
                            $creationDate = strtotime(trim($propertyValue));
                            $docProps->setCreated($creationDate);
                            $docProps->setModified($creationDate);
                            break;
                        case 'user-defined':
                            list(, $attrName) = explode(':', $attributes['name']);
                            switch ($attrName) {
                                case 'publisher':
                                    $docProps->setCompany(trim($propertyValue));
                                    break;
                                case 'category':
                                    $docProps->setCategory(trim($propertyValue));
                                    break;
                                case 'manager':
                                    $docProps->setManager(trim($propertyValue));
                                    break;
                            }
                            break;
                    }
                }
            }
        } else {
            if (isset($gnmXML->Summary)) {
                foreach ($gnmXML->Summary->Item as $summaryItem) {
                    $propertyName = $summaryItem->name;
                    $propertyValue = $summaryItem->{'val-string'};
                    switch ($propertyName) {
                        case 'title':
                            $docProps->setTitle(trim($propertyValue));
                            break;
                        case 'comments':
                            $docProps->setDescription(trim($propertyValue));
                            break;
                        case 'keywords':
                            $docProps->setKeywords(trim($propertyValue));
                            break;
                        case 'category':
                            $docProps->setCategory(trim($propertyValue));
                            break;
                        case 'manager':
                            $docProps->setManager(trim($propertyValue));
                            break;
                        case 'author':
                            $docProps->setCreator(trim($propertyValue));
                            $docProps->setLastModifiedBy(trim($propertyValue));
                            break;
                        case 'company':
                            $docProps->setCompany(trim($propertyValue));
                            break;
                    }
                }
            }
        }
        $worksheetID = 0;
        foreach ($gnmXML->Sheets->Sheet as $sheet) {
            $worksheetName = (string) $sheet->Name;
            if (isset($this->_loadSheetsOnly) && !in_array($worksheetName, $this->_loadSheetsOnly)) {
                continue;
            }
            $maxRow = $maxCol = 0;
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex($worksheetID);
            $objPHPExcel->getActiveSheet()->setTitle($worksheetName, false);
            if (!$this->_readDataOnly && isset($sheet->PrintInformation)) {
                if (isset($sheet->PrintInformation->Margins)) {
                    foreach ($sheet->PrintInformation->Margins->children('gnm', true) as $key => $margin) {
                        $marginAttributes = $margin->attributes();
                        $marginSize = 72 / 100;
                        switch ($marginAttributes['PrefUnit']) {
                            case 'mm':
                                $marginSize = intval($marginAttributes['Points']) / 100;
                                break;
                        }
                        switch ($key) {
                            case 'top':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setTop($marginSize);
                                break;
                            case 'bottom':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom($marginSize);
                                break;
                            case 'left':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft($marginSize);
                                break;
                            case 'right':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setRight($marginSize);
                                break;
                            case 'header':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader($marginSize);
                                break;
                            case 'footer':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter($marginSize);
                                break;
                        }
                    }
                }
            }
            foreach ($sheet->Cells->Cell as $cell) {
                $cellAttributes = $cell->attributes();
                $row = (int) $cellAttributes->Row + 1;
                $column = (int) $cellAttributes->Col;
                if ($maxRow < $row) {
                    $maxRow = $row;
                }
                if ($maxCol < $column) {
                    $maxCol = $column;
                }
                $column = PHPExcel_Cell::stringFromColumnIndex($column);
                if ($this->getReadFilter() !== NULL) {
                    if (!$this->getReadFilter()->readCell($column, $row, $worksheetName)) {
                        continue;
                    }
                }
                $ValueType = $cellAttributes->ValueType;
                $ExprID = (string) $cellAttributes->ExprID;
                $type = PHPExcel_Cell_DataType::TYPE_FORMULA;
                if ('' < $ExprID) {
                    if ('' < (string) $cell) {
                        $this->_expressions[$ExprID] = array('column' => $cellAttributes->Col, 'row' => $cellAttributes->Row, 'formula' => (string) $cell);
                    } else {
                        $expression = $this->_expressions[$ExprID];
                        $cell = $this->_referenceHelper->updateFormulaReferences($expression['formula'], 'A1', $cellAttributes->Col - $expression['column'], $cellAttributes->Row - $expression['row'], $worksheetName);
                    }
                    $type = PHPExcel_Cell_DataType::TYPE_FORMULA;
                } else {
                    switch ($ValueType) {
                        case '10':
                            $type = PHPExcel_Cell_DataType::TYPE_NULL;
                            break;
                        case '20':
                            $type = PHPExcel_Cell_DataType::TYPE_BOOL;
                            $cell = $cell == 'TRUE' ? true : false;
                            break;
                        case '30':
                            $cell = intval($cell);
                        case '40':
                            $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                            break;
                        case '50':
                            $type = PHPExcel_Cell_DataType::TYPE_ERROR;
                            break;
                        case '60':
                            $type = PHPExcel_Cell_DataType::TYPE_STRING;
                            break;
                        case '70':
                        case '80':
                    }
                }
                $objPHPExcel->getActiveSheet()->getCell($column . $row)->setValueExplicit($cell, $type);
            }
            if (!$this->_readDataOnly && isset($sheet->Objects)) {
                foreach ($sheet->Objects->children('gnm', true) as $key => $comment) {
                    $commentAttributes = $comment->attributes();
                    if ($commentAttributes->Text) {
                        $objPHPExcel->getActiveSheet()->getComment((string) $commentAttributes->ObjectBound)->setAuthor((string) $commentAttributes->Author)->setText($this->_parseRichText((string) $commentAttributes->Text));
                    }
                }
            }
            foreach ($sheet->Styles->StyleRegion as $styleRegion) {
                $styleAttributes = $styleRegion->attributes();
                if ($styleAttributes['startRow'] <= $maxRow && $styleAttributes['startCol'] <= $maxCol) {
                    $startColumn = PHPExcel_Cell::stringFromColumnIndex((int) $styleAttributes['startCol']);
                    $startRow = $styleAttributes['startRow'] + 1;
                    $endColumn = $maxCol < $styleAttributes['endCol'] ? $maxCol : (int) $styleAttributes['endCol'];
                    $endColumn = PHPExcel_Cell::stringFromColumnIndex($endColumn);
                    $endRow = $maxRow < $styleAttributes['endRow'] ? $maxRow : $styleAttributes['endRow'];
                    $endRow += 1;
                    $cellRange = $startColumn . $startRow . ':' . $endColumn . $endRow;
                    $styleAttributes = $styleRegion->Style->attributes();
                    if (!$this->_readDataOnly || PHPExcel_Shared_Date::isDateTimeFormatCode((string) $styleAttributes['Format'])) {
                        $styleArray = array();
                        $styleArray['numberformat']['code'] = (string) $styleAttributes['Format'];
                        if (!$this->_readDataOnly) {
                            switch ($styleAttributes['HAlign']) {
                                case '1':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
                                    break;
                                case '2':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                                    break;
                                case '4':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                                    break;
                                case '8':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                                    break;
                                case '16':
                                case '64':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS;
                                    break;
                                case '32':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY;
                                    break;
                            }
                            switch ($styleAttributes['VAlign']) {
                                case '1':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_TOP;
                                    break;
                                case '2':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
                                    break;
                                case '4':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_CENTER;
                                    break;
                                case '8':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_JUSTIFY;
                                    break;
                            }
                            $styleArray['alignment']['wrap'] = $styleAttributes['WrapText'] == '1' ? true : false;
                            $styleArray['alignment']['shrinkToFit'] = $styleAttributes['ShrinkToFit'] == '1' ? true : false;
                            $styleArray['alignment']['indent'] = 0 < intval($styleAttributes['Indent']) ? $styleAttributes['indent'] : 0;
                            $RGB = self::_parseGnumericColour($styleAttributes['Fore']);
                            $styleArray['font']['color']['rgb'] = $RGB;
                            $RGB = self::_parseGnumericColour($styleAttributes['Back']);
                            $shade = $styleAttributes['Shade'];
                            if ($RGB != '000000' || $shade != '0') {
                                $styleArray['fill']['color']['rgb'] = $styleArray['fill']['startcolor']['rgb'] = $RGB;
                                $RGB2 = self::_parseGnumericColour($styleAttributes['PatternColor']);
                                $styleArray['fill']['endcolor']['rgb'] = $RGB2;
                                switch ($shade) {
                                    case '1':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                                        break;
                                    case '2':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR;
                                        break;
                                    case '3':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_GRADIENT_PATH;
                                        break;
                                    case '4':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKDOWN;
                                        break;
                                    case '5':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKGRAY;
                                        break;
                                    case '6':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKGRID;
                                        break;
                                    case '7':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKHORIZONTAL;
                                        break;
                                    case '8':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKTRELLIS;
                                        break;
                                    case '9':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKUP;
                                        break;
                                    case '10':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKVERTICAL;
                                        break;
                                    case '11':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_GRAY0625;
                                        break;
                                    case '12':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_GRAY125;
                                        break;
                                    case '13':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTDOWN;
                                        break;
                                    case '14':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY;
                                        break;
                                    case '15':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRID;
                                        break;
                                    case '16':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTHORIZONTAL;
                                        break;
                                    case '17':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTTRELLIS;
                                        break;
                                    case '18':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTUP;
                                        break;
                                    case '19':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTVERTICAL;
                                        break;
                                    case '20':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_MEDIUMGRAY;
                                        break;
                                }
                            }
                            $fontAttributes = $styleRegion->Style->Font->attributes();
                            $styleArray['font']['name'] = (string) $styleRegion->Style->Font;
                            $styleArray['font']['size'] = intval($fontAttributes['Unit']);
                            $styleArray['font']['bold'] = $fontAttributes['Bold'] == '1' ? true : false;
                            $styleArray['font']['italic'] = $fontAttributes['Italic'] == '1' ? true : false;
                            $styleArray['font']['strike'] = $fontAttributes['StrikeThrough'] == '1' ? true : false;
                            switch ($fontAttributes['Underline']) {
                                case '1':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_SINGLE;
                                    break;
                                case '2':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_DOUBLE;
                                    break;
                                case '3':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING;
                                    break;
                                case '4':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING;
                                    break;
                                default:
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_NONE;
                                    break;
                            }
                            switch ($fontAttributes['Script']) {
                                case '1':
                                    $styleArray['font']['superScript'] = true;
                                    break;
                                case '-1':
                                    $styleArray['font']['subScript'] = true;
                                    break;
                            }
                            if (isset($styleRegion->Style->StyleBorder)) {
                                if (isset($styleRegion->Style->StyleBorder->Top)) {
                                    $styleArray['borders']['top'] = self::_parseBorderAttributes($styleRegion->Style->StyleBorder->Top->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Bottom)) {
                                    $styleArray['borders']['bottom'] = self::_parseBorderAttributes($styleRegion->Style->StyleBorder->Bottom->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Left)) {
                                    $styleArray['borders']['left'] = self::_parseBorderAttributes($styleRegion->Style->StyleBorder->Left->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Right)) {
                                    $styleArray['borders']['right'] = self::_parseBorderAttributes($styleRegion->Style->StyleBorder->Right->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Diagonal) && isset($styleRegion->Style->StyleBorder->{'Rev-Diagonal'})) {
                                    $styleArray['borders']['diagonal'] = self::_parseBorderAttributes($styleRegion->Style->StyleBorder->Diagonal->attributes());
                                    $styleArray['borders']['diagonaldirection'] = PHPExcel_Style_Borders::DIAGONAL_BOTH;
                                } else {
                                    if (isset($styleRegion->Style->StyleBorder->Diagonal)) {
                                        $styleArray['borders']['diagonal'] = self::_parseBorderAttributes($styleRegion->Style->StyleBorder->Diagonal->attributes());
                                        $styleArray['borders']['diagonaldirection'] = PHPExcel_Style_Borders::DIAGONAL_UP;
                                    } else {
                                        if (isset($styleRegion->Style->StyleBorder->{'Rev-Diagonal'})) {
                                            $styleArray['borders']['diagonal'] = self::_parseBorderAttributes($styleRegion->Style->StyleBorder->{'Rev-Diagonal'}->attributes());
                                            $styleArray['borders']['diagonaldirection'] = PHPExcel_Style_Borders::DIAGONAL_DOWN;
                                        }
                                    }
                                }
                            }
                            if (isset($styleRegion->Style->HyperLink)) {
                                $hyperlink = $styleRegion->Style->HyperLink->attributes();
                            }
                        }
                        $objPHPExcel->getActiveSheet()->getStyle($cellRange)->applyFromArray($styleArray);
                    }
                }
            }
            if (!$this->_readDataOnly && isset($sheet->Cols)) {
                $columnAttributes = $sheet->Cols->attributes();
                $defaultWidth = $columnAttributes['DefaultSizePts'] / 5.4;
                $c = 0;
                foreach ($sheet->Cols->ColInfo as $columnOverride) {
                    $columnAttributes = $columnOverride->attributes();
                    $column = $columnAttributes['No'];
                    $columnWidth = $columnAttributes['Unit'] / 5.4;
                    $hidden = isset($columnAttributes['Hidden']) && $columnAttributes['Hidden'] == '1' ? true : false;
                    $columnCount = isset($columnAttributes['Count']) ? $columnAttributes['Count'] : 1;
                    while ($c < $column) {
                        $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth($defaultWidth);
                        ++$c;
                    }
                    while ($c < $column + $columnCount && $c <= $maxCol) {
                        $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth($columnWidth);
                        if ($hidden) {
                            $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setVisible(false);
                        }
                        ++$c;
                    }
                }
                while ($c <= $maxCol) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth($defaultWidth);
                    ++$c;
                }
            }
            if (!$this->_readDataOnly && isset($sheet->Rows)) {
                $rowAttributes = $sheet->Rows->attributes();
                $defaultHeight = $rowAttributes['DefaultSizePts'];
                $r = 0;
                foreach ($sheet->Rows->RowInfo as $rowOverride) {
                    $rowAttributes = $rowOverride->attributes();
                    $row = $rowAttributes['No'];
                    $rowHeight = $rowAttributes['Unit'];
                    $hidden = isset($rowAttributes['Hidden']) && $rowAttributes['Hidden'] == '1' ? true : false;
                    $rowCount = isset($rowAttributes['Count']) ? $rowAttributes['Count'] : 1;
                    while ($r < $row) {
                        ++$r;
                        $objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
                    }
                    while ($r < $row + $rowCount && $r < $maxRow) {
                        ++$r;
                        $objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight($rowHeight);
                        if ($hidden) {
                            $objPHPExcel->getActiveSheet()->getRowDimension($r)->setVisible(false);
                        }
                    }
                }
                while ($r < $maxRow) {
                    ++$r;
                    $objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
                }
            }
            if (isset($sheet->MergedRegions)) {
                foreach ($sheet->MergedRegions->Merge as $mergeCells) {
                    if (strpos($mergeCells, ':') !== false) {
                        $objPHPExcel->getActiveSheet()->mergeCells($mergeCells);
                    }
                }
            }
            $worksheetID++;
        }
        if (isset($gnmXML->Names)) {
            foreach ($gnmXML->Names->Name as $namedRange) {
                $name = (string) $namedRange->name;
                $range = (string) $namedRange->value;
                if (stripos($range, '#REF!') !== false) {
                    continue;
                }
                $range = explode('!', $range);
                $range[0] = trim($range[0], '\'');
                if ($worksheet = $objPHPExcel->getSheetByName($range[0])) {
                    $extractedRange = str_replace('$', '', $range[1]);
                    $objPHPExcel->addNamedRange(new PHPExcel_NamedRange($name, $worksheet, $extractedRange));
                }
            }
        }
        return $objPHPExcel;
    }
    private static function _parseBorderAttributes($borderAttributes)
    {
        $styleArray = array();
        if (isset($borderAttributes['Color'])) {
            $RGB = self::_parseGnumericColour($borderAttributes['Color']);
            $styleArray['color']['rgb'] = $RGB;
        }
        switch ($borderAttributes['Style']) {
            case '0':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_NONE;
                break;
            case '1':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_THIN;
                break;
            case '2':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUM;
                break;
            case '4':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DASHED;
                break;
            case '5':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_THICK;
                break;
            case '6':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DOUBLE;
                break;
            case '7':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DOTTED;
                break;
            case '9':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DASHDOT;
                break;
            case '10':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT;
                break;
            case '11':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DASHDOTDOT;
                break;
            case '12':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT;
                break;
            case '13':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT;
                break;
            case '3':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_SLANTDASHDOT;
                break;
            case '8':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHED;
                break;
        }
        return $styleArray;
    }
    private function _parseRichText($is = '')
    {
        $value = new PHPExcel_RichText();
        $value->createText($is);
        return $value;
    }
    private static function _parseGnumericColour($gnmColour)
    {
        list($gnmR, $gnmG, $gnmB) = explode(':', $gnmColour);
        $gnmR = substr(str_pad($gnmR, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmG = substr(str_pad($gnmG, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmB = substr(str_pad($gnmB, 4, '0', STR_PAD_RIGHT), 0, 2);
        $RGB = $gnmR . $gnmG . $gnmB;
        return $RGB;
    }
}