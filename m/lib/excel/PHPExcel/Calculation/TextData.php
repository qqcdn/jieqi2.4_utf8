<?php

class PHPExcel_Calculation_TextData
{
    private static $_invalidChars;
    private static function _uniord($c)
    {
        if (0 <= ord($c[0]) && ord($c[0]) <= 127) {
            return ord($c[0]);
        }
        if (192 <= ord($c[0]) && ord($c[0]) <= 223) {
            return (ord($c[0]) - 192) * 64 + (ord($c[1]) - 128);
        }
        if (224 <= ord($c[0]) && ord($c[0]) <= 239) {
            return (ord($c[0]) - 224) * 4096 + (ord($c[1]) - 128) * 64 + (ord($c[2]) - 128);
        }
        if (240 <= ord($c[0]) && ord($c[0]) <= 247) {
            return (ord($c[0]) - 240) * 262144 + (ord($c[1]) - 128) * 4096 + (ord($c[2]) - 128) * 64 + (ord($c[3]) - 128);
        }
        if (248 <= ord($c[0]) && ord($c[0]) <= 251) {
            return (ord($c[0]) - 248) * 16777216 + (ord($c[1]) - 128) * 262144 + (ord($c[2]) - 128) * 4096 + (ord($c[3]) - 128) * 64 + (ord($c[4]) - 128);
        }
        if (252 <= ord($c[0]) && ord($c[0]) <= 253) {
            return (ord($c[0]) - 252) * 1073741824 + (ord($c[1]) - 128) * 16777216 + (ord($c[2]) - 128) * 262144 + (ord($c[3]) - 128) * 4096 + (ord($c[4]) - 128) * 64 + (ord($c[5]) - 128);
        }
        if (254 <= ord($c[0]) && ord($c[0]) <= 255) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        return 0;
    }
    public static function CHARACTER($character)
    {
        $character = PHPExcel_Calculation_Functions::flattenSingleValue($character);
        if (!is_numeric($character) || $character < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding('&#' . intval($character) . ';', 'UTF-8', 'HTML-ENTITIES');
        } else {
            return chr(intval($character));
        }
    }
    public static function TRIMNONPRINTABLE($stringValue = '')
    {
        $stringValue = PHPExcel_Calculation_Functions::flattenSingleValue($stringValue);
        if (is_bool($stringValue)) {
            return $stringValue ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        if (self::$_invalidChars == NULL) {
            self::$_invalidChars = range(chr(0), chr(31));
        }
        if (is_string($stringValue) || is_numeric($stringValue)) {
            return str_replace(self::$_invalidChars, '', trim($stringValue, '' . "\0" . '..'));
        }
        return NULL;
    }
    public static function TRIMSPACES($stringValue = '')
    {
        $stringValue = PHPExcel_Calculation_Functions::flattenSingleValue($stringValue);
        if (is_bool($stringValue)) {
            return $stringValue ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        if (is_string($stringValue) || is_numeric($stringValue)) {
            return trim(preg_replace('/ +/', ' ', trim($stringValue, ' ')));
        }
        return NULL;
    }
    public static function ASCIICODE($characters)
    {
        if ($characters === NULL || $characters === '') {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $characters = PHPExcel_Calculation_Functions::flattenSingleValue($characters);
        if (is_bool($characters)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $characters = (int) $characters;
            } else {
                $characters = $characters ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
            }
        }
        $character = $characters;
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (1 < mb_strlen($characters, 'UTF-8')) {
                $character = mb_substr($characters, 0, 1, 'UTF-8');
            }
            return self::_uniord($character);
        } else {
            if (0 < strlen($characters)) {
                $character = substr($characters, 0, 1);
            }
            return ord($character);
        }
    }
    public static function CONCATENATE()
    {
        $returnValue = '';
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_bool($arg)) {
                if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                    $arg = (int) $arg;
                } else {
                    $arg = $arg ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
                }
            }
            $returnValue .= $arg;
        }
        return $returnValue;
    }
    public static function DOLLAR($value = 0, $decimals = 2)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $decimals = is_null($decimals) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($decimals);
        if (!is_numeric($value) || !is_numeric($decimals)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $decimals = floor($decimals);
        $mask = '$#,##0';
        if (0 < $decimals) {
            $mask .= '.' . str_repeat('0', $decimals);
        } else {
            $round = pow(10, abs($decimals));
            if ($value < 0) {
                $round = -$round;
            }
            $value = PHPExcel_Calculation_MathTrig::MROUND($value, $round);
        }
        return PHPExcel_Style_NumberFormat::toFormattedString($value, $mask);
    }
    public static function SEARCHSENSITIVE($needle, $haystack, $offset = 1)
    {
        $needle = PHPExcel_Calculation_Functions::flattenSingleValue($needle);
        $haystack = PHPExcel_Calculation_Functions::flattenSingleValue($haystack);
        $offset = PHPExcel_Calculation_Functions::flattenSingleValue($offset);
        if (!is_bool($needle)) {
            if (is_bool($haystack)) {
                $haystack = $haystack ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
            }
            if (0 < $offset && $offset < PHPExcel_Shared_String::CountCharacters($haystack)) {
                if (PHPExcel_Shared_String::CountCharacters($needle) == 0) {
                    return $offset;
                }
                if (function_exists('mb_strpos')) {
                    $pos = mb_strpos($haystack, $needle, --$offset, 'UTF-8');
                } else {
                    $pos = strpos($haystack, $needle, --$offset);
                }
                if ($pos !== false) {
                    return ++$pos;
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SEARCHINSENSITIVE($needle, $haystack, $offset = 1)
    {
        $needle = PHPExcel_Calculation_Functions::flattenSingleValue($needle);
        $haystack = PHPExcel_Calculation_Functions::flattenSingleValue($haystack);
        $offset = PHPExcel_Calculation_Functions::flattenSingleValue($offset);
        if (!is_bool($needle)) {
            if (is_bool($haystack)) {
                $haystack = $haystack ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
            }
            if (0 < $offset && $offset < PHPExcel_Shared_String::CountCharacters($haystack)) {
                if (PHPExcel_Shared_String::CountCharacters($needle) == 0) {
                    return $offset;
                }
                if (function_exists('mb_stripos')) {
                    $pos = mb_stripos($haystack, $needle, --$offset, 'UTF-8');
                } else {
                    $pos = stripos($haystack, $needle, --$offset);
                }
                if ($pos !== false) {
                    return ++$pos;
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function FIXEDFORMAT($value, $decimals = 2, $no_commas = false)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $decimals = PHPExcel_Calculation_Functions::flattenSingleValue($decimals);
        $no_commas = PHPExcel_Calculation_Functions::flattenSingleValue($no_commas);
        if (!is_numeric($value) || !is_numeric($decimals)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $decimals = floor($decimals);
        $valueResult = round($value, $decimals);
        if ($decimals < 0) {
            $decimals = 0;
        }
        if (!$no_commas) {
            $valueResult = number_format($valueResult, $decimals);
        }
        return (string) $valueResult;
    }
    public static function LEFT($value = '', $chars = 1)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $chars = PHPExcel_Calculation_Functions::flattenSingleValue($chars);
        if ($chars < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_bool($value)) {
            $value = $value ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $chars, 'UTF-8');
        } else {
            return substr($value, 0, $chars);
        }
    }
    public static function MID($value = '', $start = 1, $chars = NULL)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $start = PHPExcel_Calculation_Functions::flattenSingleValue($start);
        $chars = PHPExcel_Calculation_Functions::flattenSingleValue($chars);
        if ($start < 1 || $chars < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_bool($value)) {
            $value = $value ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        if (function_exists('mb_substr')) {
            return mb_substr($value, --$start, $chars, 'UTF-8');
        } else {
            return substr($value, --$start, $chars);
        }
    }
    public static function RIGHT($value = '', $chars = 1)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $chars = PHPExcel_Calculation_Functions::flattenSingleValue($chars);
        if ($chars < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_bool($value)) {
            $value = $value ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        if (function_exists('mb_substr') && function_exists('mb_strlen')) {
            return mb_substr($value, mb_strlen($value, 'UTF-8') - $chars, $chars, 'UTF-8');
        } else {
            return substr($value, strlen($value) - $chars);
        }
    }
    public static function STRINGLENGTH($value = '')
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        if (is_bool($value)) {
            $value = $value ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        } else {
            return strlen($value);
        }
    }
    public static function LOWERCASE($mixedCaseString)
    {
        $mixedCaseString = PHPExcel_Calculation_Functions::flattenSingleValue($mixedCaseString);
        if (is_bool($mixedCaseString)) {
            $mixedCaseString = $mixedCaseString ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        return PHPExcel_Shared_String::StrToLower($mixedCaseString);
    }
    public static function UPPERCASE($mixedCaseString)
    {
        $mixedCaseString = PHPExcel_Calculation_Functions::flattenSingleValue($mixedCaseString);
        if (is_bool($mixedCaseString)) {
            $mixedCaseString = $mixedCaseString ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        return PHPExcel_Shared_String::StrToUpper($mixedCaseString);
    }
    public static function PROPERCASE($mixedCaseString)
    {
        $mixedCaseString = PHPExcel_Calculation_Functions::flattenSingleValue($mixedCaseString);
        if (is_bool($mixedCaseString)) {
            $mixedCaseString = $mixedCaseString ? PHPExcel_Calculation::getTRUE() : PHPExcel_Calculation::getFALSE();
        }
        return PHPExcel_Shared_String::StrToTitle($mixedCaseString);
    }
    public static function REPLACE($oldText = '', $start = 1, $chars = NULL, $newText)
    {
        $oldText = PHPExcel_Calculation_Functions::flattenSingleValue($oldText);
        $start = PHPExcel_Calculation_Functions::flattenSingleValue($start);
        $chars = PHPExcel_Calculation_Functions::flattenSingleValue($chars);
        $newText = PHPExcel_Calculation_Functions::flattenSingleValue($newText);
        $left = self::LEFT($oldText, $start - 1);
        $right = self::RIGHT($oldText, self::STRINGLENGTH($oldText) - ($start + $chars) + 1);
        return $left . $newText . $right;
    }
    public static function SUBSTITUTE($text = '', $fromText = '', $toText = '', $instance = 0)
    {
        $text = PHPExcel_Calculation_Functions::flattenSingleValue($text);
        $fromText = PHPExcel_Calculation_Functions::flattenSingleValue($fromText);
        $toText = PHPExcel_Calculation_Functions::flattenSingleValue($toText);
        $instance = floor(PHPExcel_Calculation_Functions::flattenSingleValue($instance));
        if ($instance == 0) {
            if (function_exists('mb_str_replace')) {
                return mb_str_replace($fromText, $toText, $text);
            } else {
                return str_replace($fromText, $toText, $text);
            }
        } else {
            $pos = -1;
            while (0 < $instance) {
                if (function_exists('mb_strpos')) {
                    $pos = mb_strpos($text, $fromText, $pos + 1, 'UTF-8');
                } else {
                    $pos = strpos($text, $fromText, $pos + 1);
                }
                if ($pos === false) {
                    break;
                }
                --$instance;
            }
            if ($pos !== false) {
                if (function_exists('mb_strlen')) {
                    return self::REPLACE($text, ++$pos, mb_strlen($fromText, 'UTF-8'), $toText);
                } else {
                    return self::REPLACE($text, ++$pos, strlen($fromText), $toText);
                }
            }
        }
        return $text;
    }
    public static function RETURNSTRING($testValue = '')
    {
        $testValue = PHPExcel_Calculation_Functions::flattenSingleValue($testValue);
        if (is_string($testValue)) {
            return $testValue;
        }
        return NULL;
    }
    public static function TEXTFORMAT($value, $format)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $format = PHPExcel_Calculation_Functions::flattenSingleValue($format);
        if (is_string($value) && !is_numeric($value) && PHPExcel_Shared_Date::isDateTimeFormatCode($format)) {
            $value = PHPExcel_Calculation_DateTime::DATEVALUE($value);
        }
        return (string) PHPExcel_Style_NumberFormat::toFormattedString($value, $format);
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}