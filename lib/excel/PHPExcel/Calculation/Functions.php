<?php

class PHPExcel_Calculation_Functions
{
    const COMPATIBILITY_EXCEL = 'Excel';
    const COMPATIBILITY_GNUMERIC = 'Gnumeric';
    const COMPATIBILITY_OPENOFFICE = 'OpenOfficeCalc';
    const RETURNDATE_PHP_NUMERIC = 'P';
    const RETURNDATE_PHP_OBJECT = 'O';
    const RETURNDATE_EXCEL = 'E';
    /**
     * Compatibility mode to use for error checking and responses
     *
     * @access	private
     * @var string
     */
    protected static $compatibilityMode = self::COMPATIBILITY_EXCEL;
    /**
     * Data Type to use when returning date values
     *
     * @access	private
     * @var string
     */
    protected static $ReturnDateType = self::RETURNDATE_EXCEL;
    /**
     * List of error codes
     *
     * @access	private
     * @var array
     */
    protected static $_errorCodes = array('null' => '#NULL!', 'divisionbyzero' => '#DIV/0!', 'value' => '#VALUE!', 'reference' => '#REF!', 'name' => '#NAME?', 'num' => '#NUM!', 'na' => '#N/A', 'gettingdata' => '#GETTING_DATA');
    public static function setCompatibilityMode($compatibilityMode)
    {
        if ($compatibilityMode == self::COMPATIBILITY_EXCEL || $compatibilityMode == self::COMPATIBILITY_GNUMERIC || $compatibilityMode == self::COMPATIBILITY_OPENOFFICE) {
            self::$compatibilityMode = $compatibilityMode;
            return true;
        }
        return false;
    }
    public static function getCompatibilityMode()
    {
        return self::$compatibilityMode;
    }
    public static function setReturnDateType($returnDateType)
    {
        if ($returnDateType == self::RETURNDATE_PHP_NUMERIC || $returnDateType == self::RETURNDATE_PHP_OBJECT || $returnDateType == self::RETURNDATE_EXCEL) {
            self::$ReturnDateType = $returnDateType;
            return true;
        }
        return false;
    }
    public static function getReturnDateType()
    {
        return self::$ReturnDateType;
    }
    public static function DUMMY()
    {
        return '#Not Yet Implemented';
    }
    public static function DIV0()
    {
        return self::$_errorCodes['divisionbyzero'];
    }
    public static function NA()
    {
        return self::$_errorCodes['na'];
    }
    public static function NaN()
    {
        return self::$_errorCodes['num'];
    }
    public static function NAME()
    {
        return self::$_errorCodes['name'];
    }
    public static function REF()
    {
        return self::$_errorCodes['reference'];
    }
    public static function NULL()
    {
        return self::$_errorCodes['null'];
    }
    public static function VALUE()
    {
        return self::$_errorCodes['value'];
    }
    public static function isMatrixValue($idx)
    {
        return substr_count($idx, '.') <= 1 || 0 < preg_match('/\\.[A-Z]/', $idx);
    }
    public static function isValue($idx)
    {
        return substr_count($idx, '.') == 0;
    }
    public static function isCellValue($idx)
    {
        return 1 < substr_count($idx, '.');
    }
    public static function _ifCondition($condition)
    {
        $condition = PHPExcel_Calculation_Functions::flattenSingleValue($condition);
        if (!isset($condition[0])) {
            $condition = '=""';
        }
        if (!in_array($condition[0], array('>', '<', '='))) {
            if (!is_numeric($condition)) {
                $condition = PHPExcel_Calculation::_wrapResult(strtoupper($condition));
            }
            return '=' . $condition;
        } else {
            preg_match('/([<>=]+)(.*)/', $condition, $matches);
            $operand = $matches[2];
            $operator = $matches[1];
            if (!is_numeric($operand)) {
                $operand = str_replace('"', '""', $operand);
                $operand = PHPExcel_Calculation::_wrapResult(strtoupper($operand));
            }
            return $operator . $operand;
        }
    }
    public static function ERROR_TYPE($value = '')
    {
        $value = self::flattenSingleValue($value);
        $i = 1;
        foreach (self::$_errorCodes as $errorCode) {
            if ($value === $errorCode) {
                return $i;
            }
            ++$i;
        }
        return self::NA();
    }
    public static function IS_BLANK($value = NULL)
    {
        if (!is_null($value)) {
            $value = self::flattenSingleValue($value);
        }
        return is_null($value);
    }
    public static function IS_ERR($value = '')
    {
        $value = self::flattenSingleValue($value);
        return self::IS_ERROR($value) && !self::IS_NA($value);
    }
    public static function IS_ERROR($value = '')
    {
        $value = self::flattenSingleValue($value);
        if (!is_string($value)) {
            return false;
        }
        return in_array($value, array_values(self::$_errorCodes));
    }
    public static function IS_NA($value = '')
    {
        $value = self::flattenSingleValue($value);
        return $value === self::NA();
    }
    public static function IS_EVEN($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        if ($value === NULL) {
            return self::NAME();
        }
        if (is_bool($value) || is_string($value) && !is_numeric($value)) {
            return self::VALUE();
        }
        return $value % 2 == 0;
    }
    public static function IS_ODD($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        if ($value === NULL) {
            return self::NAME();
        }
        if (is_bool($value) || is_string($value) && !is_numeric($value)) {
            return self::VALUE();
        }
        return abs($value) % 2 == 1;
    }
    public static function IS_NUMBER($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        if (is_string($value)) {
            return false;
        }
        return is_numeric($value);
    }
    public static function IS_LOGICAL($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        return is_bool($value);
    }
    public static function IS_TEXT($value = NULL)
    {
        $value = self::flattenSingleValue($value);
        return is_string($value) && !self::IS_ERROR($value);
    }
    public static function IS_NONTEXT($value = NULL)
    {
        return !self::IS_TEXT($value);
    }
    public static function VERSION()
    {
        return 'PHPExcel 1.8.0, 2014-03-02';
    }
    public static function N($value = NULL)
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }
        switch (gettype($value)) {
            case 'double':
            case 'float':
            case 'integer':
                return $value;
                break;
            case 'boolean':
                return (int) $value;
                break;
            case 'string':
                if (0 < strlen($value) && $value[0] == '#') {
                    return $value;
                }
                break;
        }
        return 0;
    }
    public static function TYPE($value = NULL)
    {
        $value = self::flattenArrayIndexed($value);
        if (is_array($value) && 1 < count($value)) {
            $a = array_keys($value);
            $a = array_pop($a);
            if (self::isCellValue($a)) {
                return 16;
            } else {
                if (self::isMatrixValue($a)) {
                    return 64;
                }
            }
        } else {
            if (empty($value)) {
                return 1;
            }
        }
        $value = self::flattenSingleValue($value);
        if ($value === NULL || is_float($value) || is_int($value)) {
            return 1;
        } else {
            if (is_bool($value)) {
                return 4;
            } else {
                if (is_array($value)) {
                    return 64;
                    break;
                } else {
                    if (is_string($value)) {
                        if (0 < strlen($value) && $value[0] == '#') {
                            return 16;
                        }
                        return 2;
                    }
                }
            }
        }
        return 0;
    }
    public static function flattenArray($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }
        $arrayValues = array();
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (is_array($val)) {
                        foreach ($val as $v) {
                            $arrayValues[] = $v;
                        }
                    } else {
                        $arrayValues[] = $val;
                    }
                }
            } else {
                $arrayValues[] = $value;
            }
        }
        return $arrayValues;
    }
    public static function flattenArrayIndexed($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }
        $arrayValues = array();
        foreach ($array as $k1 => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k3 => $v) {
                            $arrayValues[$k1 . '.' . $k2 . '.' . $k3] = $v;
                        }
                    } else {
                        $arrayValues[$k1 . '.' . $k2] = $val;
                    }
                }
            } else {
                $arrayValues[$k1] = $value;
            }
        }
        return $arrayValues;
    }
    public static function flattenSingleValue($value = '')
    {
        while (is_array($value)) {
            $value = array_pop($value);
        }
        return $value;
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
define('MAX_VALUE', 1.2E+308);
define('M_2DIVPI', 0.6366197723675801);
define('MAX_ITERATIONS', 256);
define('PRECISION', 8.88E-16);
if (!function_exists('acosh')) {
    function acosh($x)
    {
        return 2 * log(sqrt(($x + 1) / 2) + sqrt(($x - 1) / 2));
    }
}
if (!function_exists('asinh')) {
    function asinh($x)
    {
        return log($x + sqrt(1 + $x * $x));
    }
}
if (!function_exists('atanh')) {
    function atanh($x)
    {
        return (log(1 + $x) - log(1 - $x)) / 2;
    }
}
if (!function_exists('mb_str_replace') && function_exists('mb_substr') && function_exists('mb_strlen') && function_exists('mb_strpos')) {
    function mb_str_replace($search, $replace, $subject)
    {
        if (is_array($subject)) {
            $ret = array();
            foreach ($subject as $key => $val) {
                $ret[$key] = mb_str_replace($search, $replace, $val);
            }
            return $ret;
        }
        foreach ((array) $search as $key => $s) {
            if ($s == '') {
                continue;
            }
            $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
            $pos = mb_strpos($subject, $s, 0, 'UTF-8');
            while ($pos !== false) {
                $subject = mb_substr($subject, 0, $pos, 'UTF-8') . $r . mb_substr($subject, $pos + mb_strlen($s, 'UTF-8'), 65535, 'UTF-8');
                $pos = mb_strpos($subject, $s, $pos + mb_strlen($r, 'UTF-8'), 'UTF-8');
            }
        }
        return $subject;
    }
}