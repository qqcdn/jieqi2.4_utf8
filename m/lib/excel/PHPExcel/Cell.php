<?php

class PHPExcel_Cell
{
    const DEFAULT_RANGE = 'A1:A1';
    /**
     *	Value binder to use
     *
     *	@var	PHPExcel_Cell_IValueBinder
     */
    private static $_valueBinder;
    /**
     *	Value of the cell
     *
     *	@var	mixed
     */
    private $_value;
    /**
     *	Calculated value of the cell (used for caching)
     *	This returns the value last calculated by MS Excel or whichever spreadsheet program was used to
     *		create the original spreadsheet file.
     *	Note that this value is not guaranteed to reflect the actual calculated value because it is
     *		possible that auto-calculation was disabled in the original spreadsheet, and underlying data
     *		values used by the formula have changed since it was last calculated.
     *
     *	@var mixed
     */
    private $_calculatedValue;
    /**
     *	Type of the cell data
     *
     *	@var	string
     */
    private $_dataType;
    /**
     *	Parent worksheet
     *
     *	@var	PHPExcel_CachedObjectStorage_CacheBase
     */
    private $_parent;
    /**
     *	Index to cellXf
     *
     *	@var	int
     */
    private $_xfIndex;
    /**
     *	Attributes of the formula
     *
     */
    private $_formulaAttributes;
    public function notifyCacheController()
    {
        $this->_parent->updateCacheData($this);
        return $this;
    }
    public function detach()
    {
        $this->_parent = NULL;
    }
    public function attach(PHPExcel_CachedObjectStorage_CacheBase $parent)
    {
        $this->_parent = $parent;
    }
    public function __construct($pValue = NULL, $pDataType = NULL, PHPExcel_Worksheet $pSheet = NULL)
    {
        $this->_value = $pValue;
        $this->_parent = $pSheet->getCellCacheController();
        if ($pDataType !== NULL) {
            if ($pDataType == PHPExcel_Cell_DataType::TYPE_STRING2) {
                $pDataType = PHPExcel_Cell_DataType::TYPE_STRING;
            }
            $this->_dataType = $pDataType;
        } else {
            if (!self::getValueBinder()->bindValue($this, $pValue)) {
                throw new PHPExcel_Exception('Value could not be bound to cell.');
            }
        }
        $this->_xfIndex = 0;
    }
    public function getColumn()
    {
        return $this->_parent->getCurrentColumn();
    }
    public function getRow()
    {
        return $this->_parent->getCurrentRow();
    }
    public function getCoordinate()
    {
        return $this->_parent->getCurrentAddress();
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function getFormattedValue()
    {
        return (string) PHPExcel_Style_NumberFormat::toFormattedString($this->getCalculatedValue(), $this->getWorksheet()->getParent()->getCellXfByIndex($this->getXfIndex())->getNumberFormat()->getFormatCode());
    }
    public function setValue($pValue = NULL)
    {
        if (!self::getValueBinder()->bindValue($this, $pValue)) {
            throw new PHPExcel_Exception('Value could not be bound to cell.');
        }
        return $this;
    }
    public function setValueExplicit($pValue = NULL, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
    {
        switch ($pDataType) {
            case PHPExcel_Cell_DataType:
                $this->_value = $pValue;
                break;
            case PHPExcel_Cell_DataType:
                $pDataType = PHPExcel_Cell_DataType::TYPE_STRING;
            case PHPExcel_Cell_DataType:
            case PHPExcel_Cell_DataType:
                $this->_value = PHPExcel_Cell_DataType::checkString($pValue);
                break;
            case PHPExcel_Cell_DataType:
                $this->_value = (double) $pValue;
                break;
            case PHPExcel_Cell_DataType:
                $this->_value = (string) $pValue;
                break;
            case PHPExcel_Cell_DataType:
                $this->_value = (bool) $pValue;
                break;
            case PHPExcel_Cell_DataType:
                $this->_value = PHPExcel_Cell_DataType::checkErrorCode($pValue);
                break;
            default:
                throw new PHPExcel_Exception('Invalid datatype: ' . $pDataType);
                break;
        }
        $this->_dataType = $pDataType;
        return $this->notifyCacheController();
    }
    public function getCalculatedValue($resetLog = true)
    {
        if ($this->_dataType == PHPExcel_Cell_DataType::TYPE_FORMULA) {
            try {
                $result = PHPExcel_Calculation::getInstance($this->getWorksheet()->getParent())->calculateCellValue($this, $resetLog);
                if (is_array($result)) {
                    while (is_array($result)) {
                        $result = array_pop($result);
                    }
                }
            } catch (PHPExcel_Exception $ex) {
                if ($ex->getMessage() === 'Unable to access External Workbook' && $this->_calculatedValue !== NULL) {
                    return $this->_calculatedValue;
                }
                $result = '#N/A';
                throw new PHPExcel_Calculation_Exception($this->getWorksheet()->getTitle() . '!' . $this->getCoordinate() . ' -> ' . $ex->getMessage());
            }
            if ($result === '#Not Yet Implemented') {
                return $this->_calculatedValue;
            }
            return $result;
        } else {
            if ($this->_value instanceof PHPExcel_RichText) {
                return $this->_value->getPlainText();
            }
        }
        return $this->_value;
    }
    public function setCalculatedValue($pValue = NULL)
    {
        if ($pValue !== NULL) {
            $this->_calculatedValue = is_numeric($pValue) ? (double) $pValue : $pValue;
        }
        return $this->notifyCacheController();
    }
    public function getOldCalculatedValue()
    {
        return $this->_calculatedValue;
    }
    public function getDataType()
    {
        return $this->_dataType;
    }
    public function setDataType($pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
    {
        if ($pDataType == PHPExcel_Cell_DataType::TYPE_STRING2) {
            $pDataType = PHPExcel_Cell_DataType::TYPE_STRING;
        }
        $this->_dataType = $pDataType;
        return $this->notifyCacheController();
    }
    public function isFormula()
    {
        return $this->_dataType == PHPExcel_Cell_DataType::TYPE_FORMULA;
    }
    public function hasDataValidation()
    {
        if (!isset($this->_parent)) {
            throw new PHPExcel_Exception('Cannot check for data validation when cell is not bound to a worksheet');
        }
        return $this->getWorksheet()->dataValidationExists($this->getCoordinate());
    }
    public function getDataValidation()
    {
        if (!isset($this->_parent)) {
            throw new PHPExcel_Exception('Cannot get data validation for cell that is not bound to a worksheet');
        }
        return $this->getWorksheet()->getDataValidation($this->getCoordinate());
    }
    public function setDataValidation(PHPExcel_Cell_DataValidation $pDataValidation = NULL)
    {
        if (!isset($this->_parent)) {
            throw new PHPExcel_Exception('Cannot set data validation for cell that is not bound to a worksheet');
        }
        $this->getWorksheet()->setDataValidation($this->getCoordinate(), $pDataValidation);
        return $this->notifyCacheController();
    }
    public function hasHyperlink()
    {
        if (!isset($this->_parent)) {
            throw new PHPExcel_Exception('Cannot check for hyperlink when cell is not bound to a worksheet');
        }
        return $this->getWorksheet()->hyperlinkExists($this->getCoordinate());
    }
    public function getHyperlink()
    {
        if (!isset($this->_parent)) {
            throw new PHPExcel_Exception('Cannot get hyperlink for cell that is not bound to a worksheet');
        }
        return $this->getWorksheet()->getHyperlink($this->getCoordinate());
    }
    public function setHyperlink(PHPExcel_Cell_Hyperlink $pHyperlink = NULL)
    {
        if (!isset($this->_parent)) {
            throw new PHPExcel_Exception('Cannot set hyperlink for cell that is not bound to a worksheet');
        }
        $this->getWorksheet()->setHyperlink($this->getCoordinate(), $pHyperlink);
        return $this->notifyCacheController();
    }
    public function getParent()
    {
        return $this->_parent;
    }
    public function getWorksheet()
    {
        return $this->_parent->getParent();
    }
    public function getStyle()
    {
        return $this->getWorksheet()->getParent()->getCellXfByIndex($this->getXfIndex());
    }
    public function rebindParent(PHPExcel_Worksheet $parent)
    {
        $this->_parent = $parent->getCellCacheController();
        return $this->notifyCacheController();
    }
    public function isInRange($pRange = 'A1:A1')
    {
        list($rangeStart, $rangeEnd) = self::rangeBoundaries($pRange);
        $myColumn = self::columnIndexFromString($this->getColumn());
        $myRow = $this->getRow();
        return $rangeStart[0] <= $myColumn && $myColumn <= $rangeEnd[0] && $rangeStart[1] <= $myRow && $myRow <= $rangeEnd[1];
    }
    public static function coordinateFromString($pCoordinateString = 'A1')
    {
        if (preg_match('/^([$]?[A-Z]{1,3})([$]?\\d{1,7})$/', $pCoordinateString, $matches)) {
            return array($matches[1], $matches[2]);
        } else {
            if (strpos($pCoordinateString, ':') !== false || strpos($pCoordinateString, ',') !== false) {
                throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells');
            } else {
                if ($pCoordinateString == '') {
                    throw new PHPExcel_Exception('Cell coordinate can not be zero-length string');
                }
            }
        }
        throw new PHPExcel_Exception('Invalid cell coordinate ' . $pCoordinateString);
    }
    public static function absoluteReference($pCoordinateString = 'A1')
    {
        if (strpos($pCoordinateString, ':') === false && strpos($pCoordinateString, ',') === false) {
            $worksheet = '';
            $cellAddress = explode('!', $pCoordinateString);
            if (1 < count($cellAddress)) {
                $pCoordinateString = $cellAddress[1];
                $worksheet = $cellAddress[0];
            }
            if ('' < $worksheet) {
                $worksheet .= '!';
            }
            if (ctype_digit($pCoordinateString)) {
                return $worksheet . '$' . $pCoordinateString;
            } else {
                if (ctype_alpha($pCoordinateString)) {
                    return $worksheet . '$' . strtoupper($pCoordinateString);
                }
            }
            return $worksheet . self::absoluteCoordinate($pCoordinateString);
        }
        throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells');
    }
    public static function absoluteCoordinate($pCoordinateString = 'A1')
    {
        if (strpos($pCoordinateString, ':') === false && strpos($pCoordinateString, ',') === false) {
            $worksheet = '';
            $cellAddress = explode('!', $pCoordinateString);
            if (1 < count($cellAddress)) {
                $pCoordinateString = $cellAddress[1];
                $worksheet = $cellAddress[0];
            }
            if ('' < $worksheet) {
                $worksheet .= '!';
            }
            list($column, $row) = self::coordinateFromString($pCoordinateString);
            $column = ltrim($column, '$');
            $row = ltrim($row, '$');
            return $worksheet . '$' . $column . '$' . $row;
        }
        throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells');
    }
    public static function splitRange($pRange = 'A1:A1')
    {
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }
        $exploded = explode(',', $pRange);
        $counter = count($exploded);
        for ($i = 0; $i < $counter; ++$i) {
            $exploded[$i] = explode(':', $exploded[$i]);
        }
        return $exploded;
    }
    public static function buildRange($pRange)
    {
        if (!is_array($pRange) || empty($pRange) || !is_array($pRange[0])) {
            throw new PHPExcel_Exception('Range does not contain any information');
        }
        $imploded = array();
        $counter = count($pRange);
        for ($i = 0; $i < $counter; ++$i) {
            $pRange[$i] = implode(':', $pRange[$i]);
        }
        $imploded = implode(',', $pRange);
        return $imploded;
    }
    public static function rangeBoundaries($pRange = 'A1:A1')
    {
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }
        $pRange = strtoupper($pRange);
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }
        $rangeStart = self::coordinateFromString($rangeA);
        $rangeEnd = self::coordinateFromString($rangeB);
        $rangeStart[0] = self::columnIndexFromString($rangeStart[0]);
        $rangeEnd[0] = self::columnIndexFromString($rangeEnd[0]);
        return array($rangeStart, $rangeEnd);
    }
    public static function rangeDimension($pRange = 'A1:A1')
    {
        list($rangeStart, $rangeEnd) = self::rangeBoundaries($pRange);
        return array($rangeEnd[0] - $rangeStart[0] + 1, $rangeEnd[1] - $rangeStart[1] + 1);
    }
    public static function getRangeBoundaries($pRange = 'A1:A1')
    {
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }
        $pRange = strtoupper($pRange);
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }
        return array(self::coordinateFromString($rangeA), self::coordinateFromString($rangeB));
    }
    public static function columnIndexFromString($pString = 'A')
    {
        static $_indexCache = array();
        if (isset($_indexCache[$pString])) {
            return $_indexCache[$pString];
        }
        static $_columnLookup = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10, 'k' => 11, 'l' => 12, 'm' => 13, 'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20, 'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26);
        if (isset($pString[0])) {
            if (!isset($pString[1])) {
                $_indexCache[$pString] = $_columnLookup[$pString];
                return $_indexCache[$pString];
            } else {
                if (!isset($pString[2])) {
                    $_indexCache[$pString] = $_columnLookup[$pString[0]] * 26 + $_columnLookup[$pString[1]];
                    return $_indexCache[$pString];
                } else {
                    if (!isset($pString[3])) {
                        $_indexCache[$pString] = $_columnLookup[$pString[0]] * 676 + $_columnLookup[$pString[1]] * 26 + $_columnLookup[$pString[2]];
                        return $_indexCache[$pString];
                    }
                }
            }
        }
        throw new PHPExcel_Exception('Column string index can not be ' . (isset($pString[0]) ? 'longer than 3 characters' : 'empty'));
    }
    public static function stringFromColumnIndex($pColumnIndex = 0)
    {
        static $_indexCache = array();
        if (!isset($_indexCache[$pColumnIndex])) {
            if ($pColumnIndex < 26) {
                $_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
            } else {
                if ($pColumnIndex < 702) {
                    $_indexCache[$pColumnIndex] = chr(64 + $pColumnIndex / 26) . chr(65 + $pColumnIndex % 26);
                } else {
                    $_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex - 26) / 676) . chr(65 + ($pColumnIndex - 26) % 676 / 26) . chr(65 + $pColumnIndex % 26);
                }
            }
        }
        return $_indexCache[$pColumnIndex];
    }
    public static function extractAllCellReferencesInRange($pRange = 'A1')
    {
        $returnValue = array();
        $cellBlocks = explode(' ', str_replace('$', '', strtoupper($pRange)));
        foreach ($cellBlocks as $cellBlock) {
            if (strpos($cellBlock, ':') === false && strpos($cellBlock, ',') === false) {
                $returnValue[] = $cellBlock;
                continue;
            }
            $ranges = self::splitRange($cellBlock);
            foreach ($ranges as $range) {
                if (!isset($range[1])) {
                    $returnValue[] = $range[0];
                    continue;
                }
                $rangeEnd = $range[1];
                $rangeStart = $range[0];
                sscanf($rangeStart, '%[A-Z]%d', $startCol, $startRow);
                sscanf($rangeEnd, '%[A-Z]%d', $endCol, $endRow);
                $endCol++;
                $currentCol = $startCol;
                $currentRow = $startRow;
                while ($currentCol != $endCol) {
                    while ($currentRow <= $endRow) {
                        $returnValue[] = $currentCol . $currentRow;
                        ++$currentRow;
                    }
                    ++$currentCol;
                    $currentRow = $startRow;
                }
            }
        }
        $sortKeys = array();
        foreach (array_unique($returnValue) as $coord) {
            sscanf($coord, '%[A-Z]%d', $column, $row);
            $sortKeys[sprintf('%3s%09d', $column, $row)] = $coord;
        }
        ksort($sortKeys);
        return array_values($sortKeys);
    }
    public static function compareCells(PHPExcel_Cell $a, PHPExcel_Cell $b)
    {
        if ($a->getRow() < $b->getRow()) {
            return -1;
        } else {
            if ($b->getRow() < $a->getRow()) {
                return 1;
            } else {
                if (self::columnIndexFromString($a->getColumn()) < self::columnIndexFromString($b->getColumn())) {
                    return -1;
                } else {
                    return 1;
                }
            }
        }
    }
    public static function getValueBinder()
    {
        if (self::$_valueBinder === NULL) {
            self::$_valueBinder = new PHPExcel_Cell_DefaultValueBinder();
        }
        return self::$_valueBinder;
    }
    public static function setValueBinder(PHPExcel_Cell_IValueBinder $binder = NULL)
    {
        if ($binder === NULL) {
            throw new PHPExcel_Exception('A PHPExcel_Cell_IValueBinder is required for PHPExcel to function correctly.');
        }
        self::$_valueBinder = $binder;
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value) && $key != '_parent') {
                $this->{$key} = clone $value;
            } else {
                $this->{$key} = $value;
            }
        }
    }
    public function getXfIndex()
    {
        return $this->_xfIndex;
    }
    public function setXfIndex($pValue = 0)
    {
        $this->_xfIndex = $pValue;
        return $this->notifyCacheController();
    }
    public function setFormulaAttributes($pAttributes)
    {
        $this->_formulaAttributes = $pAttributes;
        return $this;
    }
    public function getFormulaAttributes()
    {
        return $this->_formulaAttributes;
    }
    public function __toString()
    {
        return (string) $this->getValue();
    }
}