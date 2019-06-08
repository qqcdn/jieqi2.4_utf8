<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
{
    public function bindValue(PHPExcel_Cell $cell, $value = NULL)
    {
        if (is_string($value)) {
            $value = PHPExcel_Shared_String::SanitizeUTF8($value);
        }
        $cell->setValueExplicit($value, self::dataTypeForValue($value));
        return true;
    }
    public static function dataTypeForValue($pValue = NULL)
    {
        if (is_null($pValue)) {
            return PHPExcel_Cell_DataType::TYPE_NULL;
        } else {
            if ($pValue === '') {
                return PHPExcel_Cell_DataType::TYPE_STRING;
            } else {
                if ($pValue instanceof PHPExcel_RichText) {
                    return PHPExcel_Cell_DataType::TYPE_INLINE;
                } else {
                    if ($pValue[0] === '=' && 1 < strlen($pValue)) {
                        return PHPExcel_Cell_DataType::TYPE_FORMULA;
                    } else {
                        if (is_bool($pValue)) {
                            return PHPExcel_Cell_DataType::TYPE_BOOL;
                        } else {
                            if (is_float($pValue) || is_int($pValue)) {
                                return PHPExcel_Cell_DataType::TYPE_NUMERIC;
                            } else {
                                if (preg_match('/^\\-?([0-9]+\\.?[0-9]*|[0-9]*\\.?[0-9]+)$/', $pValue)) {
                                    return PHPExcel_Cell_DataType::TYPE_NUMERIC;
                                } else {
                                    if (is_string($pValue) && array_key_exists($pValue, PHPExcel_Cell_DataType::getErrorCodes())) {
                                        return PHPExcel_Cell_DataType::TYPE_ERROR;
                                    } else {
                                        return PHPExcel_Cell_DataType::TYPE_STRING;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}