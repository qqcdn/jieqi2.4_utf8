<?php

if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
class PHPExcel_Cell_AdvancedValueBinder extends PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
{
    public function bindValue(PHPExcel_Cell $cell, $value = NULL)
    {
        if (is_string($value)) {
            $value = PHPExcel_Shared_String::SanitizeUTF8($value);
        }
        $dataType = parent::dataTypeForValue($value);
        if ($dataType === PHPExcel_Cell_DataType::TYPE_STRING && !$value instanceof PHPExcel_RichText) {
            if ($value == PHPExcel_Calculation::getTRUE()) {
                $cell->setValueExplicit(true, PHPExcel_Cell_DataType::TYPE_BOOL);
                return true;
            } else {
                if ($value == PHPExcel_Calculation::getFALSE()) {
                    $cell->setValueExplicit(false, PHPExcel_Cell_DataType::TYPE_BOOL);
                    return true;
                }
            }
            if (preg_match('/^' . PHPExcel_Calculation::CALCULATION_REGEXP_NUMBER . '$/', $value)) {
                $cell->setValueExplicit((double) $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                return true;
            }
            if (preg_match('/^([+-]?)\\s*([0-9]+)\\s?\\/\\s*([0-9]+)$/', $value, $matches)) {
                $value = $matches[2] / $matches[3];
                if ($matches[1] == '-') {
                    $value = -$value;
                }
                $cell->setValueExplicit((double) $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode('??/??');
                return true;
            } else {
                if (preg_match('/^([+-]?)([0-9]*) +([0-9]*)\\s?\\/\\s*([0-9]*)$/', $value, $matches)) {
                    $value = $matches[2] + $matches[3] / $matches[4];
                    if ($matches[1] == '-') {
                        $value = -$value;
                    }
                    $cell->setValueExplicit((double) $value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode('# ??/??');
                    return true;
                }
            }
            if (preg_match('/^\\-?[0-9]*\\.?[0-9]*\\s?\\%$/', $value)) {
                $value = (double) str_replace('%', '', $value) / 100;
                $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                return true;
            }
            $currencyCode = PHPExcel_Shared_String::getCurrencyCode();
            $decimalSeparator = PHPExcel_Shared_String::getDecimalSeparator();
            $thousandsSeparator = PHPExcel_Shared_String::getThousandsSeparator();
            if (preg_match('/^' . preg_quote($currencyCode) . ' *(\\d{1,3}(' . preg_quote($thousandsSeparator) . '\\d{3})*|(\\d+))(' . preg_quote($decimalSeparator) . '\\d{2})?$/', $value)) {
                $value = (double) trim(str_replace(array($currencyCode, $thousandsSeparator, $decimalSeparator), array('', '', '.'), $value));
                $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode(str_replace('$', $currencyCode, PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE));
                return true;
            } else {
                if (preg_match('/^\\$ *(\\d{1,3}(\\,\\d{3})*|(\\d+))(\\.\\d{2})?$/', $value)) {
                    $value = (double) trim(str_replace(array('$', ','), '', $value));
                    $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    return true;
                }
            }
            if (preg_match('/^(\\d|[0-1]\\d|2[0-3]):[0-5]\\d$/', $value)) {
                list($h, $m) = explode(':', $value);
                $days = $h / 24 + $m / 1440;
                $cell->setValueExplicit($days, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3);
                return true;
            }
            if (preg_match('/^(\\d|[0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/', $value)) {
                list($h, $m, $s) = explode(':', $value);
                $days = $h / 24 + $m / 1440 + $s / 86400;
                $cell->setValueExplicit($days, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4);
                return true;
            }
            if (($d = PHPExcel_Shared_Date::stringToExcel($value)) !== false) {
                $cell->setValueExplicit($d, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                if (strpos($value, ':') !== false) {
                    $formatCode = 'yyyy-mm-dd h:mm';
                } else {
                    $formatCode = 'yyyy-mm-dd';
                }
                $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode($formatCode);
                return true;
            }
            if (strpos($value, "\n") !== false) {
                $value = PHPExcel_Shared_String::SanitizeUTF8($value);
                $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
                $cell->getWorksheet()->getStyle($cell->getCoordinate())->getAlignment()->setWrapText(true);
                return true;
            }
        }
        return parent::bindValue($cell, $value);
    }
}