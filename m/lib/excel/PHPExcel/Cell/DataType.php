<?php

class PHPExcel_Cell_DataType
{
    const TYPE_STRING2 = 'str';
    const TYPE_STRING = 's';
    const TYPE_FORMULA = 'f';
    const TYPE_NUMERIC = 'n';
    const TYPE_BOOL = 'b';
    const TYPE_NULL = 'null';
    const TYPE_INLINE = 'inlineStr';
    const TYPE_ERROR = 'e';
    /**
     * List of error codes
     *
     * @var array
     */
    private static $_errorCodes = array('#NULL!' => 0, '#DIV/0!' => 1, '#VALUE!' => 2, '#REF!' => 3, '#NAME?' => 4, '#NUM!' => 5, '#N/A' => 6);
    public static function getErrorCodes()
    {
        return self::$_errorCodes;
    }
    public static function dataTypeForValue($pValue = NULL)
    {
        return PHPExcel_Cell_DefaultValueBinder::dataTypeForValue($pValue);
    }
    public static function checkString($pValue = NULL)
    {
        if ($pValue instanceof PHPExcel_RichText) {
            return $pValue;
        }
        $pValue = PHPExcel_Shared_String::Substring($pValue, 0, 32767);
        $pValue = str_replace(array("\r\n", "\r"), "\n", $pValue);
        return $pValue;
    }
    public static function checkErrorCode($pValue = NULL)
    {
        $pValue = (string) $pValue;
        if (!array_key_exists($pValue, self::$_errorCodes)) {
            $pValue = '#NULL!';
        }
        return $pValue;
    }
}