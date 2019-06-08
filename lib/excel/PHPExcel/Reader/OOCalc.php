<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Reader_OOCalc extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Formats
     *
     * @var array
     */
    private $_styles = array();
    public function __construct()
    {
        $this->_readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }
    public function canRead($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $zipClass = PHPExcel_Settings::getZipClass();
        $mimeType = 'UNKNOWN';
        $zip = new $zipClass();
        if ($zip->open($pFilename) === true) {
            $stat = $zip->statName('mimetype');
            if ($stat && $stat['size'] <= 255) {
                $mimeType = $zip->getFromName($stat['name']);
            } else {
                if ($stat = $zip->statName('META-INF/manifest.xml')) {
                    $xml = simplexml_load_string($zip->getFromName('META-INF/manifest.xml'), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
                    $namespacesContent = $xml->getNamespaces(true);
                    if (isset($namespacesContent['manifest'])) {
                        $manifest = $xml->children($namespacesContent['manifest']);
                        foreach ($manifest as $manifestDataSet) {
                            $manifestAttributes = $manifestDataSet->attributes($namespacesContent['manifest']);
                            if ($manifestAttributes->{'full-path'} == '/') {
                                $mimeType = (string) $manifestAttributes->{'media-type'};
                                break;
                            }
                        }
                    }
                }
            }
            $zip->close();
            return $mimeType === 'application/vnd.oasis.opendocument.spreadsheet';
        }
        return false;
    }
    public function listWorksheetNames($pFilename)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $zipClass = PHPExcel_Settings::getZipClass();
        $zip = new $zipClass();
        if (!$zip->open($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }
        $worksheetNames = array();
        $xml = new XMLReader();
        $res = $xml->open('zip://' . realpath($pFilename) . '#content.xml', NULL, PHPExcel_Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);
        $xml->read();
        while ($xml->read()) {
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            while ($xml->read()) {
                if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    do {
                        $worksheetNames[] = $xml->getAttribute('table:name');
                        $xml->next();
                    } while ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT);
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
        $worksheetInfo = array();
        $zipClass = PHPExcel_Settings::getZipClass();
        $zip = new $zipClass();
        if (!$zip->open($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }
        $xml = new XMLReader();
        $res = $xml->open('zip://' . realpath($pFilename) . '#content.xml', NULL, PHPExcel_Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);
        $xml->read();
        while ($xml->read()) {
            while ($xml->name !== 'office:body') {
                if ($xml->isEmptyElement) {
                    $xml->read();
                } else {
                    $xml->next();
                }
            }
            while ($xml->read()) {
                if ($xml->name == 'table:table' && $xml->nodeType == XMLReader::ELEMENT) {
                    $worksheetNames[] = $xml->getAttribute('table:name');
                    $tmpInfo = array('worksheetName' => $xml->getAttribute('table:name'), 'lastColumnLetter' => 'A', 'lastColumnIndex' => 0, 'totalRows' => 0, 'totalColumns' => 0);
                    $currCells = 0;
                    do {
                        $xml->read();
                        if ($xml->name == 'table:table-row' && $xml->nodeType == XMLReader::ELEMENT) {
                            $rowspan = $xml->getAttribute('table:number-rows-repeated');
                            $rowspan = empty($rowspan) ? 1 : $rowspan;
                            $tmpInfo['totalRows'] += $rowspan;
                            $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                            $currCells = 0;
                            $xml->read();
                            do {
                                if ($xml->name == 'table:table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                    if (!$xml->isEmptyElement) {
                                        $currCells++;
                                        $xml->next();
                                    } else {
                                        $xml->read();
                                    }
                                } else {
                                    if ($xml->name == 'table:covered-table-cell' && $xml->nodeType == XMLReader::ELEMENT) {
                                        $mergeSize = $xml->getAttribute('table:number-columns-repeated');
                                        $currCells += $mergeSize;
                                        $xml->read();
                                    }
                                }
                            } while ($xml->name != 'table:table-row');
                        }
                    } while ($xml->name != 'table:table');
                    $tmpInfo['totalColumns'] = max($tmpInfo['totalColumns'], $currCells);
                    $tmpInfo['lastColumnIndex'] = $tmpInfo['totalColumns'] - 1;
                    $tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
                    $worksheetInfo[] = $tmpInfo;
                }
            }
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
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! File does not exist.');
        }
        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new DateTimeZone('UTC');
        $zipClass = PHPExcel_Settings::getZipClass();
        $zip = new $zipClass();
        if (!$zip->open($pFilename)) {
            throw new PHPExcel_Reader_Exception('Could not open ' . $pFilename . ' for reading! Error opening file.');
        }
        $xml = simplexml_load_string($zip->getFromName('meta.xml'), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
        $namespacesMeta = $xml->getNamespaces(true);
        $docProps = $objPHPExcel->getProperties();
        $officeProperty = $xml->children($namespacesMeta['office']);
        foreach ($officeProperty as $officePropertyData) {
            $officePropertyDC = array();
            if (isset($namespacesMeta['dc'])) {
                $officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
            }
            foreach ($officePropertyDC as $propertyName => $propertyValue) {
                $propertyValue = (string) $propertyValue;
                switch ($propertyName) {
                    case 'title':
                        $docProps->setTitle($propertyValue);
                        break;
                    case 'subject':
                        $docProps->setSubject($propertyValue);
                        break;
                    case 'creator':
                        $docProps->setCreator($propertyValue);
                        $docProps->setLastModifiedBy($propertyValue);
                        break;
                    case 'date':
                        $creationDate = strtotime($propertyValue);
                        $docProps->setCreated($creationDate);
                        $docProps->setModified($creationDate);
                        break;
                    case 'description':
                        $docProps->setDescription($propertyValue);
                        break;
                }
            }
            $officePropertyMeta = array();
            if (isset($namespacesMeta['dc'])) {
                $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
            }
            foreach ($officePropertyMeta as $propertyName => $propertyValue) {
                $propertyValueAttributes = $propertyValue->attributes($namespacesMeta['meta']);
                $propertyValue = (string) $propertyValue;
                switch ($propertyName) {
                    case 'initial-creator':
                        $docProps->setCreator($propertyValue);
                        break;
                    case 'keyword':
                        $docProps->setKeywords($propertyValue);
                        break;
                    case 'creation-date':
                        $creationDate = strtotime($propertyValue);
                        $docProps->setCreated($creationDate);
                        break;
                    case 'user-defined':
                        $propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
                        foreach ($propertyValueAttributes as $key => $value) {
                            if ($key == 'name') {
                                $propertyValueName = (string) $value;
                            } else {
                                if ($key == 'value-type') {
                                    switch ($value) {
                                        case 'date':
                                            $propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue, 'date');
                                            $propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_DATE;
                                            break;
                                        case 'boolean':
                                            $propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue, 'bool');
                                            $propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_BOOLEAN;
                                            break;
                                        case 'float':
                                            $propertyValue = PHPExcel_DocumentProperties::convertProperty($propertyValue, 'r4');
                                            $propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_FLOAT;
                                            break;
                                        default:
                                            $propertyValueType = PHPExcel_DocumentProperties::PROPERTY_TYPE_STRING;
                                    }
                                }
                            }
                        }
                        $docProps->setCustomProperty($propertyValueName, $propertyValue, $propertyValueType);
                        break;
                }
            }
        }
        $xml = simplexml_load_string($zip->getFromName('content.xml'), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
        $namespacesContent = $xml->getNamespaces(true);
        $workbook = $xml->children($namespacesContent['office']);
        foreach ($workbook->body->spreadsheet as $workbookData) {
            $workbookData = $workbookData->children($namespacesContent['table']);
            $worksheetID = 0;
            foreach ($workbookData->table as $worksheetDataSet) {
                $worksheetData = $worksheetDataSet->children($namespacesContent['table']);
                $worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);
                if (isset($this->_loadSheetsOnly) && isset($worksheetDataAttributes['name']) && !in_array($worksheetDataAttributes['name'], $this->_loadSheetsOnly)) {
                    continue;
                }
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($worksheetID);
                if (isset($worksheetDataAttributes['name'])) {
                    $worksheetName = (string) $worksheetDataAttributes['name'];
                    $objPHPExcel->getActiveSheet()->setTitle($worksheetName, false);
                }
                $rowID = 1;
                foreach ($worksheetData as $key => $rowData) {
                    switch ($key) {
                        case 'table-header-rows':
                            foreach ($rowData as $key => $cellData) {
                                $rowData = $cellData;
                                break;
                            }
                        case 'table-row':
                            $rowDataTableAttributes = $rowData->attributes($namespacesContent['table']);
                            $rowRepeats = isset($rowDataTableAttributes['number-rows-repeated']) ? $rowDataTableAttributes['number-rows-repeated'] : 1;
                            $columnID = 'A';
                            foreach ($rowData as $key => $cellData) {
                                if ($this->getReadFilter() !== NULL) {
                                    if (!$this->getReadFilter()->readCell($columnID, $rowID, $worksheetName)) {
                                        continue;
                                    }
                                }
                                $cellDataText = isset($namespacesContent['text']) ? $cellData->children($namespacesContent['text']) : '';
                                $cellDataOffice = $cellData->children($namespacesContent['office']);
                                $cellDataOfficeAttributes = $cellData->attributes($namespacesContent['office']);
                                $cellDataTableAttributes = $cellData->attributes($namespacesContent['table']);
                                $type = $formatting = $hyperlink = NULL;
                                $hasCalculatedValue = false;
                                $cellDataFormula = '';
                                if (isset($cellDataTableAttributes['formula'])) {
                                    $cellDataFormula = $cellDataTableAttributes['formula'];
                                    $hasCalculatedValue = true;
                                }
                                if (isset($cellDataOffice->annotation)) {
                                    $annotationText = $cellDataOffice->annotation->children($namespacesContent['text']);
                                    $textArray = array();
                                    foreach ($annotationText as $t) {
                                        foreach ($t->span as $text) {
                                            $textArray[] = (string) $text;
                                        }
                                    }
                                    $text = implode("\n", $textArray);
                                    $objPHPExcel->getActiveSheet()->getComment($columnID . $rowID)->setText($this->_parseRichText($text));
                                }
                                if (isset($cellDataText->p)) {
                                    $dataArray = array();
                                    foreach ($cellDataText->p as $pData) {
                                        if (isset($pData->span)) {
                                            $spanSection = '';
                                            foreach ($pData->span as $spanData) {
                                                $spanSection .= $spanData;
                                            }
                                            array_push($dataArray, $spanSection);
                                        } else {
                                            array_push($dataArray, $pData);
                                        }
                                    }
                                    $allCellDataText = implode($dataArray, "\n");
                                    switch ($cellDataOfficeAttributes['value-type']) {
                                        case 'string':
                                            $type = PHPExcel_Cell_DataType::TYPE_STRING;
                                            $dataValue = $allCellDataText;
                                            if (isset($dataValue->a)) {
                                                $dataValue = $dataValue->a;
                                                $cellXLinkAttributes = $dataValue->attributes($namespacesContent['xlink']);
                                                $hyperlink = $cellXLinkAttributes['href'];
                                            }
                                            break;
                                        case 'boolean':
                                            $type = PHPExcel_Cell_DataType::TYPE_BOOL;
                                            $dataValue = $allCellDataText == 'TRUE' ? true : false;
                                            break;
                                        case 'percentage':
                                            $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                            $dataValue = (double) $cellDataOfficeAttributes['value'];
                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (int) $dataValue;
                                            }
                                            $formatting = PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
                                            break;
                                        case 'currency':
                                            $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                            $dataValue = (double) $cellDataOfficeAttributes['value'];
                                            if (floor($dataValue) == $dataValue) {
                                                $dataValue = (int) $dataValue;
                                            }
                                            $formatting = PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE;
                                            break;
                                        case 'float':
                                            $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                            $dataValue = (double) $cellDataOfficeAttributes['value'];
                                            if (floor($dataValue) == $dataValue) {
                                                if ($dataValue == (int) $dataValue) {
                                                    $dataValue = (int) $dataValue;
                                                } else {
                                                    $dataValue = (double) $dataValue;
                                                }
                                            }
                                            break;
                                        case 'date':
                                            $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                            $dateObj = new DateTime($cellDataOfficeAttributes['date-value'], $GMT);
                                            $dateObj->setTimeZone($timezoneObj);
                                            list($year, $month, $day, $hour, $minute, $second) = explode(' ', $dateObj->format('Y m d H i s'));
                                            $dataValue = PHPExcel_Shared_Date::FormattedPHPToExcel($year, $month, $day, $hour, $minute, $second);
                                            if ($dataValue != floor($dataValue)) {
                                                $formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15 . ' ' . PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
                                            } else {
                                                $formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15;
                                            }
                                            break;
                                        case 'time':
                                            $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                            $dataValue = PHPExcel_Shared_Date::PHPToExcel(strtotime('01-01-1970 ' . implode(':', sscanf($cellDataOfficeAttributes['time-value'], 'PT%dH%dM%dS'))));
                                            $formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
                                            break;
                                    }
                                } else {
                                    $type = PHPExcel_Cell_DataType::TYPE_NULL;
                                    $dataValue = NULL;
                                }
                                if ($hasCalculatedValue) {
                                    $type = PHPExcel_Cell_DataType::TYPE_FORMULA;
                                    $cellDataFormula = substr($cellDataFormula, strpos($cellDataFormula, ':=') + 1);
                                    $temp = explode('"', $cellDataFormula);
                                    $tKey = false;
                                    foreach ($temp as &$value) {
                                        if ($tKey = !$tKey) {
                                            $value = preg_replace('/\\[([^\\.]+)\\.([^\\.]+):\\.([^\\.]+)\\]/Ui', '$1!$2:$3', $value);
                                            $value = preg_replace('/\\[([^\\.]+)\\.([^\\.]+)\\]/Ui', '$1!$2', $value);
                                            $value = preg_replace('/\\[\\.([^\\.]+):\\.([^\\.]+)\\]/Ui', '$1:$2', $value);
                                            $value = preg_replace('/\\[\\.([^\\.]+)\\]/Ui', '$1', $value);
                                            $value = PHPExcel_Calculation::_translateSeparator(';', ',', $value, $inBraces);
                                        }
                                    }
                                    unset($value);
                                    $cellDataFormula = implode('"', $temp);
                                }
                                $colRepeats = isset($cellDataTableAttributes['number-columns-repeated']) ? $cellDataTableAttributes['number-columns-repeated'] : 1;
                                if ($type !== NULL) {
                                    for ($i = 0; $i < $colRepeats; ++$i) {
                                        if (0 < $i) {
                                            ++$columnID;
                                        }
                                        if ($type !== PHPExcel_Cell_DataType::TYPE_NULL) {
                                            for ($rowAdjust = 0; $rowAdjust < $rowRepeats; ++$rowAdjust) {
                                                $rID = $rowID + $rowAdjust;
                                                $objPHPExcel->getActiveSheet()->getCell($columnID . $rID)->setValueExplicit($hasCalculatedValue ? $cellDataFormula : $dataValue, $type);
                                                if ($hasCalculatedValue) {
                                                    $objPHPExcel->getActiveSheet()->getCell($columnID . $rID)->setCalculatedValue($dataValue);
                                                }
                                                if ($formatting !== NULL) {
                                                    $objPHPExcel->getActiveSheet()->getStyle($columnID . $rID)->getNumberFormat()->setFormatCode($formatting);
                                                } else {
                                                    $objPHPExcel->getActiveSheet()->getStyle($columnID . $rID)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
                                                }
                                                if ($hyperlink !== NULL) {
                                                    $objPHPExcel->getActiveSheet()->getCell($columnID . $rID)->getHyperlink()->setUrl($hyperlink);
                                                }
                                            }
                                        }
                                    }
                                }
                                if (isset($cellDataTableAttributes['number-columns-spanned']) || isset($cellDataTableAttributes['number-rows-spanned'])) {
                                    if ($type !== PHPExcel_Cell_DataType::TYPE_NULL || !$this->_readDataOnly) {
                                        $columnTo = $columnID;
                                        if (isset($cellDataTableAttributes['number-columns-spanned'])) {
                                            $columnTo = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cellDataTableAttributes['number-columns-spanned'] - 2);
                                        }
                                        $rowTo = $rowID;
                                        if (isset($cellDataTableAttributes['number-rows-spanned'])) {
                                            $rowTo = $rowTo + $cellDataTableAttributes['number-rows-spanned'] - 1;
                                        }
                                        $cellRange = $columnID . $rowID . ':' . $columnTo . $rowTo;
                                        $objPHPExcel->getActiveSheet()->mergeCells($cellRange);
                                    }
                                }
                                ++$columnID;
                            }
                            $rowID += $rowRepeats;
                            break;
                    }
                }
                ++$worksheetID;
            }
        }
        return $objPHPExcel;
    }
    private function _parseRichText($is = '')
    {
        $value = new PHPExcel_RichText();
        $value->createText($is);
        return $value;
    }
}