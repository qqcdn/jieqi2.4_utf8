<?php

class PHPExcel_Calculation_Database
{
    private static function __fieldExtract($database, $field)
    {
        $field = strtoupper(PHPExcel_Calculation_Functions::flattenSingleValue($field));
        $fieldNames = array_map('strtoupper', array_shift($database));
        if (is_numeric($field)) {
            $keys = array_keys($fieldNames);
            return $keys[$field - 1];
        }
        $key = array_search($field, $fieldNames);
        return $key ? $key : NULL;
    }
    private static function __filter($database, $criteria)
    {
        $fieldNames = array_shift($database);
        $criteriaNames = array_shift($criteria);
        $testConditions = $testValues = array();
        $testConditionsCount = 0;
        foreach ($criteriaNames as $key => $criteriaName) {
            $testCondition = array();
            $testConditionCount = 0;
            foreach ($criteria as $row => $criterion) {
                if ('' < $criterion[$key]) {
                    $testCondition[] = '[:' . $criteriaName . ']' . PHPExcel_Calculation_Functions::_ifCondition($criterion[$key]);
                    $testConditionCount++;
                }
            }
            if (1 < $testConditionCount) {
                $testConditions[] = 'OR(' . implode(',', $testCondition) . ')';
                $testConditionsCount++;
            } else {
                if ($testConditionCount == 1) {
                    $testConditions[] = $testCondition[0];
                    $testConditionsCount++;
                }
            }
        }
        if (1 < $testConditionsCount) {
            $testConditionSet = 'AND(' . implode(',', $testConditions) . ')';
        } else {
            if ($testConditionsCount == 1) {
                $testConditionSet = $testConditions[0];
            }
        }
        foreach ($database as $dataRow => $dataValues) {
            $testConditionList = $testConditionSet;
            foreach ($criteriaNames as $key => $criteriaName) {
                $k = array_search($criteriaName, $fieldNames);
                if (isset($dataValues[$k])) {
                    $dataValue = $dataValues[$k];
                    $dataValue = is_string($dataValue) ? PHPExcel_Calculation::_wrapResult(strtoupper($dataValue)) : $dataValue;
                    $testConditionList = str_replace('[:' . $criteriaName . ']', $dataValue, $testConditionList);
                }
            }
            $result = PHPExcel_Calculation::getInstance()->_calculateFormulaValue('=' . $testConditionList);
            if (!$result) {
                unset($database[$dataRow]);
            }
        }
        return $database;
    }
    public static function DAVERAGE($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::AVERAGE($colData);
    }
    public static function DCOUNT($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::COUNT($colData);
    }
    public static function DCOUNTA($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::COUNTA($colData);
    }
    public static function DGET($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        if (1 < count($colData)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return $colData[0];
    }
    public static function DMAX($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::MAX($colData);
    }
    public static function DMIN($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::MIN($colData);
    }
    public static function DPRODUCT($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_MathTrig::PRODUCT($colData);
    }
    public static function DSTDEV($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::STDEV($colData);
    }
    public static function DSTDEVP($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::STDEVP($colData);
    }
    public static function DSUM($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_MathTrig::SUM($colData);
    }
    public static function DVAR($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::VARFunc($colData);
    }
    public static function DVARP($database, $field, $criteria)
    {
        $field = self::__fieldExtract($database, $field);
        if (is_null($field)) {
            return NULL;
        }
        $database = self::__filter($database, $criteria);
        $colData = array();
        foreach ($database as $row) {
            $colData[] = $row[$field];
        }
        return PHPExcel_Calculation_Statistical::VARP($colData);
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}