<?php

class PHPExcel_Calculation_LookupRef
{
    public static function CELL_ADDRESS($row, $column, $relativity = 1, $referenceStyle = true, $sheetText = '')
    {
        $row = PHPExcel_Calculation_Functions::flattenSingleValue($row);
        $column = PHPExcel_Calculation_Functions::flattenSingleValue($column);
        $relativity = PHPExcel_Calculation_Functions::flattenSingleValue($relativity);
        $sheetText = PHPExcel_Calculation_Functions::flattenSingleValue($sheetText);
        if ($row < 1 || $column < 1) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ('' < $sheetText) {
            if (strpos($sheetText, ' ') !== false) {
                $sheetText = '\'' . $sheetText . '\'';
            }
            $sheetText .= '!';
        }
        if (!is_bool($referenceStyle) || $referenceStyle) {
            $rowRelative = $columnRelative = '$';
            $column = PHPExcel_Cell::stringFromColumnIndex($column - 1);
            if ($relativity == 2 || $relativity == 4) {
                $columnRelative = '';
            }
            if ($relativity == 3 || $relativity == 4) {
                $rowRelative = '';
            }
            return $sheetText . $columnRelative . $column . $rowRelative . $row;
        } else {
            if ($relativity == 2 || $relativity == 4) {
                $column = '[' . $column . ']';
            }
            if ($relativity == 3 || $relativity == 4) {
                $row = '[' . $row . ']';
            }
            return $sheetText . 'R' . $row . 'C' . $column;
        }
    }
    public static function COLUMN($cellAddress = NULL)
    {
        if (is_null($cellAddress) || trim($cellAddress) === '') {
            return 0;
        }
        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $value) {
                $columnKey = preg_replace('/[^a-z]/i', '', $columnKey);
                return (int) PHPExcel_Cell::columnIndexFromString($columnKey);
            }
        } else {
            if (strpos($cellAddress, '!') !== false) {
                list($sheet, $cellAddress) = explode('!', $cellAddress);
            }
            if (strpos($cellAddress, ':') !== false) {
                list($startAddress, $endAddress) = explode(':', $cellAddress);
                $startAddress = preg_replace('/[^a-z]/i', '', $startAddress);
                $endAddress = preg_replace('/[^a-z]/i', '', $endAddress);
                $returnValue = array();
                do {
                    $returnValue[] = (int) PHPExcel_Cell::columnIndexFromString($startAddress);
                } while ($startAddress++ != $endAddress);
                return $returnValue;
            } else {
                $cellAddress = preg_replace('/[^a-z]/i', '', $cellAddress);
                return (int) PHPExcel_Cell::columnIndexFromString($cellAddress);
            }
        }
    }
    public static function COLUMNS($cellAddress = NULL)
    {
        if (is_null($cellAddress) || $cellAddress === '') {
            return 1;
        } else {
            if (!is_array($cellAddress)) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        $x = array_keys($cellAddress);
        $x = array_shift($x);
        $isMatrix = is_numeric($x);
        list($columns, $rows) = PHPExcel_Calculation::_getMatrixDimensions($cellAddress);
        if ($isMatrix) {
            return $rows;
        } else {
            return $columns;
        }
    }
    public static function ROW($cellAddress = NULL)
    {
        if (is_null($cellAddress) || trim($cellAddress) === '') {
            return 0;
        }
        if (is_array($cellAddress)) {
            foreach ($cellAddress as $columnKey => $rowValue) {
                foreach ($rowValue as $rowKey => $cellValue) {
                    return (int) preg_replace('/[^0-9]/i', '', $rowKey);
                }
            }
        } else {
            if (strpos($cellAddress, '!') !== false) {
                list($sheet, $cellAddress) = explode('!', $cellAddress);
            }
            if (strpos($cellAddress, ':') !== false) {
                list($startAddress, $endAddress) = explode(':', $cellAddress);
                $startAddress = preg_replace('/[^0-9]/', '', $startAddress);
                $endAddress = preg_replace('/[^0-9]/', '', $endAddress);
                $returnValue = array();
                do {
                    $returnValue[][] = (int) $startAddress;
                } while ($startAddress++ != $endAddress);
                return $returnValue;
            } else {
                list($cellAddress) = explode(':', $cellAddress);
                return (int) preg_replace('/[^0-9]/', '', $cellAddress);
            }
        }
    }
    public static function ROWS($cellAddress = NULL)
    {
        if (is_null($cellAddress) || $cellAddress === '') {
            return 1;
        } else {
            if (!is_array($cellAddress)) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        $i = array_keys($cellAddress);
        $isMatrix = is_numeric(array_shift($i));
        list($columns, $rows) = PHPExcel_Calculation::_getMatrixDimensions($cellAddress);
        if ($isMatrix) {
            return $columns;
        } else {
            return $rows;
        }
    }
    public static function HYPERLINK($linkURL = '', $displayName = NULL, PHPExcel_Cell $pCell = NULL)
    {
        $args = func_get_args();
        $pCell = array_pop($args);
        $linkURL = is_null($linkURL) ? '' : PHPExcel_Calculation_Functions::flattenSingleValue($linkURL);
        $displayName = is_null($displayName) ? '' : PHPExcel_Calculation_Functions::flattenSingleValue($displayName);
        if (!is_object($pCell) || trim($linkURL) == '') {
            return PHPExcel_Calculation_Functions::REF();
        }
        if (is_object($displayName) || trim($displayName) == '') {
            $displayName = $linkURL;
        }
        $pCell->getHyperlink()->setUrl($linkURL);
        return $displayName;
    }
    public static function INDIRECT($cellAddress = NULL, PHPExcel_Cell $pCell = NULL)
    {
        $cellAddress = PHPExcel_Calculation_Functions::flattenSingleValue($cellAddress);
        if (is_null($cellAddress) || $cellAddress === '') {
            return PHPExcel_Calculation_Functions::REF();
        }
        $cellAddress1 = $cellAddress;
        $cellAddress2 = NULL;
        if (strpos($cellAddress, ':') !== false) {
            list($cellAddress1, $cellAddress2) = explode(':', $cellAddress);
        }
        if (!preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress1, $matches) || !is_null($cellAddress2) && !preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellAddress2, $matches)) {
            if (!preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE . '$/i', $cellAddress1, $matches)) {
                return PHPExcel_Calculation_Functions::REF();
            }
            if (strpos($cellAddress, '!') !== false) {
                list($sheetName, $cellAddress) = explode('!', $cellAddress);
                $sheetName = trim($sheetName, '\'');
                $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
            } else {
                $pSheet = $pCell->getWorksheet();
            }
            return PHPExcel_Calculation::getInstance()->extractNamedRange($cellAddress, $pSheet, false);
        }
        if (strpos($cellAddress, '!') !== false) {
            list($sheetName, $cellAddress) = explode('!', $cellAddress);
            $sheetName = trim($sheetName, '\'');
            $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
        } else {
            $pSheet = $pCell->getWorksheet();
        }
        return PHPExcel_Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, false);
    }
    public static function OFFSET($cellAddress = NULL, $rows = 0, $columns = 0, $height = NULL, $width = NULL)
    {
        $rows = PHPExcel_Calculation_Functions::flattenSingleValue($rows);
        $columns = PHPExcel_Calculation_Functions::flattenSingleValue($columns);
        $height = PHPExcel_Calculation_Functions::flattenSingleValue($height);
        $width = PHPExcel_Calculation_Functions::flattenSingleValue($width);
        if ($cellAddress == NULL) {
            return 0;
        }
        $args = func_get_args();
        $pCell = array_pop($args);
        if (!is_object($pCell)) {
            return PHPExcel_Calculation_Functions::REF();
        }
        $sheetName = NULL;
        if (strpos($cellAddress, '!')) {
            list($sheetName, $cellAddress) = explode('!', $cellAddress);
            $sheetName = trim($sheetName, '\'');
        }
        if (strpos($cellAddress, ':')) {
            list($startCell, $endCell) = explode(':', $cellAddress);
        } else {
            $startCell = $endCell = $cellAddress;
        }
        list($startCellColumn, $startCellRow) = PHPExcel_Cell::coordinateFromString($startCell);
        list($endCellColumn, $endCellRow) = PHPExcel_Cell::coordinateFromString($endCell);
        $startCellRow += $rows;
        $startCellColumn = PHPExcel_Cell::columnIndexFromString($startCellColumn) - 1;
        $startCellColumn += $columns;
        if ($startCellRow <= 0 || $startCellColumn < 0) {
            return PHPExcel_Calculation_Functions::REF();
        }
        $endCellColumn = PHPExcel_Cell::columnIndexFromString($endCellColumn) - 1;
        if ($width != NULL && !is_object($width)) {
            $endCellColumn = $startCellColumn + $width - 1;
        } else {
            $endCellColumn += $columns;
        }
        $startCellColumn = PHPExcel_Cell::stringFromColumnIndex($startCellColumn);
        if ($height != NULL && !is_object($height)) {
            $endCellRow = $startCellRow + $height - 1;
        } else {
            $endCellRow += $rows;
        }
        if ($endCellRow <= 0 || $endCellColumn < 0) {
            return PHPExcel_Calculation_Functions::REF();
        }
        $endCellColumn = PHPExcel_Cell::stringFromColumnIndex($endCellColumn);
        $cellAddress = $startCellColumn . $startCellRow;
        if ($startCellColumn != $endCellColumn || $startCellRow != $endCellRow) {
            $cellAddress .= ':' . $endCellColumn . $endCellRow;
        }
        if ($sheetName !== NULL) {
            $pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
        } else {
            $pSheet = $pCell->getWorksheet();
        }
        return PHPExcel_Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, false);
    }
    public static function CHOOSE()
    {
        $chooseArgs = func_get_args();
        $chosenEntry = PHPExcel_Calculation_Functions::flattenArray(array_shift($chooseArgs));
        $entryCount = count($chooseArgs) - 1;
        if (is_array($chosenEntry)) {
            $chosenEntry = array_shift($chosenEntry);
        }
        if (is_numeric($chosenEntry) && !is_bool($chosenEntry)) {
            --$chosenEntry;
        } else {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $chosenEntry = floor($chosenEntry);
        if ($chosenEntry < 0 || $entryCount < $chosenEntry) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_array($chooseArgs[$chosenEntry])) {
            return PHPExcel_Calculation_Functions::flattenArray($chooseArgs[$chosenEntry]);
        } else {
            return $chooseArgs[$chosenEntry];
        }
    }
    public static function MATCH($lookup_value, $lookup_array, $match_type = 1)
    {
        $lookup_array = PHPExcel_Calculation_Functions::flattenArray($lookup_array);
        $lookup_value = PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);
        $match_type = is_null($match_type) ? 1 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($match_type);
        $lookup_value = strtolower($lookup_value);
        if (!is_numeric($lookup_value) && !is_string($lookup_value) && !is_bool($lookup_value)) {
            return PHPExcel_Calculation_Functions::NA();
        }
        if ($match_type !== 0 && $match_type !== -1 && $match_type !== 1) {
            return PHPExcel_Calculation_Functions::NA();
        }
        $lookupArraySize = count($lookup_array);
        if ($lookupArraySize <= 0) {
            return PHPExcel_Calculation_Functions::NA();
        }
        foreach ($lookup_array as $i => $lookupArrayValue) {
            if (!is_numeric($lookupArrayValue) && !is_string($lookupArrayValue) && !is_bool($lookupArrayValue) && !is_null($lookupArrayValue)) {
                return PHPExcel_Calculation_Functions::NA();
            }
            if (is_string($lookupArrayValue)) {
                $lookup_array[$i] = strtolower($lookupArrayValue);
            }
            if (is_null($lookupArrayValue) && ($match_type == 1 || $match_type == -1)) {
                $lookup_array = array_slice($lookup_array, 0, $i - 1);
            }
        }
        if ($match_type == 1) {
            asort($lookup_array);
            $keySet = array_keys($lookup_array);
        } else {
            if ($match_type == -1) {
                arsort($lookup_array);
                $keySet = array_keys($lookup_array);
            }
        }
        foreach ($lookup_array as $i => $lookupArrayValue) {
            if ($match_type == 0 && $lookupArrayValue == $lookup_value) {
                return ++$i;
            } else {
                if ($match_type == -1 && $lookupArrayValue <= $lookup_value) {
                    $i = array_search($i, $keySet);
                    if ($i < 1) {
                        break;
                    } else {
                        return $keySet[$i - 1] + 1;
                    }
                } else {
                    if ($match_type == 1 && $lookup_value <= $lookupArrayValue) {
                        $i = array_search($i, $keySet);
                        if ($i < 1) {
                            break;
                        } else {
                            return $keySet[$i - 1] + 1;
                        }
                    }
                }
            }
        }
        return PHPExcel_Calculation_Functions::NA();
    }
    public static function INDEX($arrayValues, $rowNum = 0, $columnNum = 0)
    {
        if ($rowNum < 0 || $columnNum < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (!is_array($arrayValues)) {
            return PHPExcel_Calculation_Functions::REF();
        }
        $rowKeys = array_keys($arrayValues);
        $columnKeys = @array_keys($arrayValues[$rowKeys[0]]);
        if (count($columnKeys) < $columnNum) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($columnNum == 0) {
                if ($rowNum == 0) {
                    return $arrayValues;
                }
                $rowNum = $rowKeys[--$rowNum];
                $returnArray = array();
                foreach ($arrayValues as $arrayColumn) {
                    if (is_array($arrayColumn)) {
                        if (isset($arrayColumn[$rowNum])) {
                            $returnArray[] = $arrayColumn[$rowNum];
                        } else {
                            return $arrayValues[$rowNum];
                        }
                    } else {
                        return $arrayValues[$rowNum];
                    }
                }
                return $returnArray;
            }
        }
        $columnNum = $columnKeys[--$columnNum];
        if (count($rowKeys) < $rowNum) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($rowNum == 0) {
                return $arrayValues[$columnNum];
            }
        }
        $rowNum = $rowKeys[--$rowNum];
        return $arrayValues[$rowNum][$columnNum];
    }
    public static function TRANSPOSE($matrixData)
    {
        $returnMatrix = array();
        if (!is_array($matrixData)) {
            $matrixData = array(array($matrixData));
        }
        $column = 0;
        foreach ($matrixData as $matrixRow) {
            $row = 0;
            foreach ($matrixRow as $matrixCell) {
                $returnMatrix[$row][$column] = $matrixCell;
                ++$row;
            }
            ++$column;
        }
        return $returnMatrix;
    }
    private static function _vlookupSort($a, $b)
    {
        $f = array_keys($a);
        $firstColumn = array_shift($f);
        if (strtolower($a[$firstColumn]) == strtolower($b[$firstColumn])) {
            return 0;
        }
        return strtolower($a[$firstColumn]) < strtolower($b[$firstColumn]) ? -1 : 1;
    }
    public static function VLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match = true)
    {
        $lookup_value = PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);
        $index_number = PHPExcel_Calculation_Functions::flattenSingleValue($index_number);
        $not_exact_match = PHPExcel_Calculation_Functions::flattenSingleValue($not_exact_match);
        if ($index_number < 1) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (!is_array($lookup_array) || empty($lookup_array)) {
            return PHPExcel_Calculation_Functions::REF();
        } else {
            $f = array_keys($lookup_array);
            $firstRow = array_pop($f);
            if (!is_array($lookup_array[$firstRow]) || count($lookup_array[$firstRow]) < $index_number) {
                return PHPExcel_Calculation_Functions::REF();
            } else {
                $columnKeys = array_keys($lookup_array[$firstRow]);
                $returnColumn = $columnKeys[--$index_number];
                $firstColumn = array_shift($columnKeys);
            }
        }
        if (!$not_exact_match) {
            uasort($lookup_array, array('self', '_vlookupSort'));
        }
        $rowNumber = $rowValue = false;
        foreach ($lookup_array as $rowKey => $rowData) {
            if (is_numeric($lookup_value) && is_numeric($rowData[$firstColumn]) && $lookup_value < $rowData[$firstColumn] || !is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]) && strtolower($lookup_value) < strtolower($rowData[$firstColumn])) {
                break;
            }
            $rowNumber = $rowKey;
            $rowValue = $rowData[$firstColumn];
        }
        if ($rowNumber !== false) {
            if (!$not_exact_match && $rowValue != $lookup_value) {
                return PHPExcel_Calculation_Functions::NA();
            } else {
                $result = $lookup_array[$rowNumber][$returnColumn];
                if (is_numeric($lookup_value) && is_numeric($result) || !is_numeric($lookup_value) && !is_numeric($result)) {
                    return $result;
                }
            }
        }
        return PHPExcel_Calculation_Functions::NA();
    }
    public static function HLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match = true)
    {
        $lookup_value = PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);
        $index_number = PHPExcel_Calculation_Functions::flattenSingleValue($index_number);
        $not_exact_match = PHPExcel_Calculation_Functions::flattenSingleValue($not_exact_match);
        if ($index_number < 1) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (!is_array($lookup_array) || empty($lookup_array)) {
            return PHPExcel_Calculation_Functions::REF();
        } else {
            $f = array_keys($lookup_array);
            $firstRow = array_pop($f);
            if (!is_array($lookup_array[$firstRow]) || count($lookup_array[$firstRow]) < $index_number) {
                return PHPExcel_Calculation_Functions::REF();
            } else {
                $columnKeys = array_keys($lookup_array[$firstRow]);
                $firstkey = $f[0] - 1;
                $returnColumn = $firstkey + $index_number;
                $firstColumn = array_shift($f);
            }
        }
        if (!$not_exact_match) {
            $firstRowH = asort($lookup_array[$firstColumn]);
        }
        $rowNumber = $rowValue = false;
        foreach ($lookup_array[$firstColumn] as $rowKey => $rowData) {
            if (is_numeric($lookup_value) && is_numeric($rowData) && $lookup_value < $rowData || !is_numeric($lookup_value) && !is_numeric($rowData) && strtolower($lookup_value) < strtolower($rowData)) {
                break;
            }
            $rowNumber = $rowKey;
            $rowValue = $rowData;
        }
        if ($rowNumber !== false) {
            if (!$not_exact_match && $rowValue != $lookup_value) {
                return PHPExcel_Calculation_Functions::NA();
            } else {
                $result = $lookup_array[$returnColumn][$rowNumber];
                return $result;
            }
        }
        return PHPExcel_Calculation_Functions::NA();
    }
    public static function LOOKUP($lookup_value, $lookup_vector, $result_vector = NULL)
    {
        $lookup_value = PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);
        if (!is_array($lookup_vector)) {
            return PHPExcel_Calculation_Functions::NA();
        }
        $lookupRows = count($lookup_vector);
        $l = array_keys($lookup_vector);
        $l = array_shift($l);
        $lookupColumns = count($lookup_vector[$l]);
        if ($lookupRows == 1 && 1 < $lookupColumns || $lookupRows == 2 && $lookupColumns != 2) {
            $lookup_vector = self::TRANSPOSE($lookup_vector);
            $lookupRows = count($lookup_vector);
            $l = array_keys($lookup_vector);
            $lookupColumns = count($lookup_vector[array_shift($l)]);
        }
        if (is_null($result_vector)) {
            $result_vector = $lookup_vector;
        }
        $resultRows = count($result_vector);
        $l = array_keys($result_vector);
        $l = array_shift($l);
        $resultColumns = count($result_vector[$l]);
        if ($resultRows == 1 && 1 < $resultColumns || $resultRows == 2 && $resultColumns != 2) {
            $result_vector = self::TRANSPOSE($result_vector);
            $resultRows = count($result_vector);
            $r = array_keys($result_vector);
            $resultColumns = count($result_vector[array_shift($r)]);
        }
        if ($lookupRows == 2) {
            $result_vector = array_pop($lookup_vector);
            $lookup_vector = array_shift($lookup_vector);
        }
        if ($lookupColumns != 2) {
            foreach ($lookup_vector as &$value) {
                if (is_array($value)) {
                    $k = array_keys($value);
                    $key1 = $key2 = array_shift($k);
                    $key2++;
                    $dataValue1 = $value[$key1];
                } else {
                    $key1 = 0;
                    $key2 = 1;
                    $dataValue1 = $value;
                }
                $dataValue2 = array_shift($result_vector);
                if (is_array($dataValue2)) {
                    $dataValue2 = array_shift($dataValue2);
                }
                $value = array($key1 => $dataValue1, $key2 => $dataValue2);
            }
            unset($value);
        }
        return self::VLOOKUP($lookup_value, $lookup_vector, 2);
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}