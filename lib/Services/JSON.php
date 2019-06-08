<?php

class Services_JSON
{
    public $_mb_strlen = false;
    public $_mb_substr = false;
    public $_mb_convert_encoding = false;
    public function __construct($use = 0)
    {
        $this->use = $use;
        $this->_mb_strlen = function_exists('mb_strlen');
        $this->_mb_convert_encoding = function_exists('mb_convert_encoding');
        $this->_mb_substr = function_exists('mb_substr');
    }
    public function utf162utf8($utf16)
    {
        if ($this->_mb_convert_encoding) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }
        $bytes = ord($utf16[0]) << 8 | ord($utf16[1]);
        switch (true) {
            case (127 & $bytes) == $bytes:
                return chr(127 & $bytes);
            case 15:
                return chr(192 | $bytes >> 6 & 31) . chr(128 | $bytes & 63);
            case 25:
                return chr(224 | $bytes >> 12 & 15) . chr(128 | $bytes >> 6 & 63) . chr(128 | $bytes & 63);
        }
        return '';
    }
    public function utf82utf16($utf8)
    {
        if ($this->_mb_convert_encoding) {
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }
        switch ($this->strlen8($utf8)) {
            case 1:
                return $utf8;
            case 2:
                return chr(7 & ord($utf8[0]) >> 2) . chr(192 & ord($utf8[0]) << 6 | 63 & ord($utf8[1]));
            case 3:
                return chr(240 & ord($utf8[0]) << 4 | 15 & ord($utf8[1]) >> 2) . chr(192 & ord($utf8[1]) << 6 | 127 & ord($utf8[2]));
        }
        return '';
    }
    public function encode($var)
    {
        return $this->encodeUnsafe($var);
    }
    public function encodeUnsafe($var)
    {
        $lc = setlocale(LC_NUMERIC, 0);
        setlocale(LC_NUMERIC, 'C');
        $ret = $this->_encode($var);
        setlocale(LC_NUMERIC, $lc);
        return $ret;
    }
    public function _encode($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'NULL':
                return 'null';
            case 'integer':
                return (int) $var;
            case 'double':
            case 'float':
                return (double) $var;
            case 'string':
                $ascii = '';
                $strlen_var = $this->strlen8($var);
                for ($c = 0; $c < $strlen_var; ++$c) {
                    $ord_var_c = ord($var[$c]);
                    switch (true) {
                        case $ord_var_c == 8:
                            $ascii .= '\\b';
                            break;
                        case 18:
                            $ascii .= '\\t';
                            break;
                        case 20:
                            $ascii .= '\\n';
                            break;
                        case 22:
                            $ascii .= '\\f';
                            break;
                        case 24:
                            $ascii .= '\\r';
                            break;
                        case 26:
                        case 27:
                        case 28:
                            $ascii .= '\\' . $var[$c];
                            break;
                        case 32:
                            $ascii .= $var[$c];
                            break;
                        case 37:
                            if ($strlen_var <= $c + 1) {
                                $c += 1;
                                $ascii .= '?';
                                break;
                            }
                            $char = pack('C*', $ord_var_c, ord($var[$c + 1]));
                            $c += 1;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\\u%04s', bin2hex($utf16));
                            break;
                        case 55:
                            if ($strlen_var <= $c + 2) {
                                $c += 2;
                                $ascii .= '?';
                                break;
                            }
                            $char = pack('C*', $ord_var_c, @ord($var[$c + 1]), @ord($var[$c + 2]));
                            $c += 2;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\\u%04s', bin2hex($utf16));
                            break;
                        case 78:
                            if ($strlen_var <= $c + 3) {
                                $c += 3;
                                $ascii .= '?';
                                break;
                            }
                            $char = pack('C*', $ord_var_c, ord($var[$c + 1]), ord($var[$c + 2]), ord($var[$c + 3]));
                            $c += 3;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\\u%04s', bin2hex($utf16));
                            break;
                        case 102:
                            if ($strlen_var <= $c + 4) {
                                $c += 4;
                                $ascii .= '?';
                                break;
                            }
                            $char = pack('C*', $ord_var_c, ord($var[$c + 1]), ord($var[$c + 2]), ord($var[$c + 3]), ord($var[$c + 4]));
                            $c += 4;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\\u%04s', bin2hex($utf16));
                            break;
                        case 129:
                            if ($strlen_var <= $c + 5) {
                                $c += 5;
                                $ascii .= '?';
                                break;
                            }
                            $char = pack('C*', $ord_var_c, ord($var[$c + 1]), ord($var[$c + 2]), ord($var[$c + 3]), ord($var[$c + 4]), ord($var[$c + 5]));
                            $c += 5;
                            $utf16 = $this->utf82utf16($char);
                            $ascii .= sprintf('\\u%04s', bin2hex($utf16));
                            break;
                    }
                }
                return '"' . $ascii . '"';
            case 'array':
                if (is_array($var) && count($var) && array_keys($var) !== range(0, sizeof($var) - 1)) {
                    $properties = array_map(array($this, 'name_value'), array_keys($var), array_values($var));
                    foreach ($properties as $property) {
                        if (Services_JSON::isError($property)) {
                            return $property;
                        }
                    }
                    return '{' . join(',', $properties) . '}';
                }
                $elements = array_map(array($this, '_encode'), $var);
                foreach ($elements as $element) {
                    if (Services_JSON::isError($element)) {
                        return $element;
                    }
                }
                return '[' . join(',', $elements) . ']';
            case 'object':
                if ($this->use & SERVICES_JSON_USE_TO_JSON && method_exists($var, 'toJSON')) {
                    $recode = $var->toJSON();
                    if (method_exists($recode, 'toJSON')) {
                        return $this->use & SERVICES_JSON_SUPPRESS_ERRORS ? 'null' : new Services_JSON_Error(class_name($var) . ' toJSON returned an object with a toJSON method.');
                    }
                    return $this->_encode($recode);
                }
                $vars = get_object_vars($var);
                $properties = array_map(array($this, 'name_value'), array_keys($vars), array_values($vars));
                foreach ($properties as $property) {
                    if (Services_JSON::isError($property)) {
                        return $property;
                    }
                }
                return '{' . join(',', $properties) . '}';
            default:
                return $this->use & SERVICES_JSON_SUPPRESS_ERRORS ? 'null' : new Services_JSON_Error(gettype($var) . ' can not be encoded as JSON string');
        }
    }
    public function name_value($name, $value)
    {
        $encoded_value = $this->_encode($value);
        if (Services_JSON::isError($encoded_value)) {
            return $encoded_value;
        }
        return $this->_encode(strval($name)) . ':' . $encoded_value;
    }
    public function reduce_string($str)
    {
        $str = preg_replace(array('#^\\s*//(.+)$#m', '#^\\s*/\\*(.+)\\*/#Us', '#/\\*(.+)\\*/\\s*$#Us'), '', $str);
        return trim($str);
    }
    public function decode($str)
    {
        $str = $this->reduce_string($str);
        switch (strtolower($str)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return NULL;
            default:
                $m = array();
                if (is_numeric($str)) {
                    return (double) $str == (int) $str ? (int) $str : (double) $str;
                } else {
                    if (preg_match('/^("|\').*(\\1)$/s', $str, $m) && $m[1] == $m[2]) {
                        $delim = $this->substr8($str, 0, 1);
                        $chrs = $this->substr8($str, 1, -1);
                        $utf8 = '';
                        $strlen_chrs = $this->strlen8($chrs);
                        for ($c = 0; $c < $strlen_chrs; ++$c) {
                            $substr_chrs_c_2 = $this->substr8($chrs, $c, 2);
                            $ord_chrs_c = ord($chrs[$c]);
                            switch (true) {
                                case $substr_chrs_c_2 == '\\b':
                                    $utf8 .= chr(8);
                                    ++$c;
                                    break;
                                case 43:
                                    $utf8 .= chr(9);
                                    ++$c;
                                    break;
                                case 47:
                                    $utf8 .= chr(10);
                                    ++$c;
                                    break;
                                case 51:
                                    $utf8 .= chr(12);
                                    ++$c;
                                    break;
                                case 55:
                                    $utf8 .= chr(13);
                                    ++$c;
                                    break;
                                case 59:
                                case 60:
                                case 61:
                                case 62:
                                    if ($delim == '"' && $substr_chrs_c_2 != '\\\'' || $delim == '\'' && $substr_chrs_c_2 != '\\"') {
                                        $utf8 .= $chrs[++$c];
                                    }
                                    break;
                                case 72:
                                    $utf16 = chr(hexdec($this->substr8($chrs, $c + 2, 2))) . chr(hexdec($this->substr8($chrs, $c + 4, 2)));
                                    $utf8 .= $this->utf162utf8($utf16);
                                    $c += 5;
                                    break;
                                case 89:
                                    $utf8 .= $chrs[$c];
                                    break;
                                case 94:
                                    $utf8 .= $this->substr8($chrs, $c, 2);
                                    ++$c;
                                    break;
                                case 100:
                                    $utf8 .= $this->substr8($chrs, $c, 3);
                                    $c += 2;
                                    break;
                                case 106:
                                    $utf8 .= $this->substr8($chrs, $c, 4);
                                    $c += 3;
                                    break;
                                case 112:
                                    $utf8 .= $this->substr8($chrs, $c, 5);
                                    $c += 4;
                                    break;
                                case 118:
                                    $utf8 .= $this->substr8($chrs, $c, 6);
                                    $c += 5;
                                    break;
                            }
                        }
                        return $utf8;
                    } else {
                        if (preg_match('/^\\[.*\\]$/s', $str) || preg_match('/^\\{.*\\}$/s', $str)) {
                            if ($str[0] == '[') {
                                $stk = array(SERVICES_JSON_IN_ARR);
                                $arr = array();
                            } else {
                                if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                                    $stk = array(SERVICES_JSON_IN_OBJ);
                                    $obj = array();
                                } else {
                                    $stk = array(SERVICES_JSON_IN_OBJ);
                                    $obj = new stdClass();
                                }
                            }
                            array_push($stk, array('what' => SERVICES_JSON_SLICE, 'where' => 0, 'delim' => false));
                            $chrs = $this->substr8($str, 1, -1);
                            $chrs = $this->reduce_string($chrs);
                            if ($chrs == '') {
                                if (reset($stk) == SERVICES_JSON_IN_ARR) {
                                    return $arr;
                                } else {
                                    return $obj;
                                }
                            }
                            $strlen_chrs = $this->strlen8($chrs);
                            for ($c = 0; $c <= $strlen_chrs; ++$c) {
                                $top = end($stk);
                                $substr_chrs_c_2 = $this->substr8($chrs, $c, 2);
                                if ($c == $strlen_chrs || $chrs[$c] == ',' && $top['what'] == SERVICES_JSON_SLICE) {
                                    $slice = $this->substr8($chrs, $top['where'], $c - $top['where']);
                                    array_push($stk, array('what' => SERVICES_JSON_SLICE, 'where' => $c + 1, 'delim' => false));
                                    if (reset($stk) == SERVICES_JSON_IN_ARR) {
                                        array_push($arr, $this->decode($slice));
                                    } else {
                                        if (reset($stk) == SERVICES_JSON_IN_OBJ) {
                                            $parts = array();
                                            if (preg_match('/^\\s*(["\'].*[^\\\\]["\'])\\s*:/Uis', $slice, $parts)) {
                                                $key = $this->decode($parts[1]);
                                                $val = $this->decode(trim(substr($slice, strlen($parts[0])), ', 	' . "\n" . '' . "\r" . '' . "\0" . ''));
                                                if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                                                    $obj[$key] = $val;
                                                } else {
                                                    $obj->{$key} = $val;
                                                }
                                            } else {
                                                if (preg_match('/^\\s*(\\w+)\\s*:/Uis', $slice, $parts)) {
                                                    $key = $parts[1];
                                                    $val = $this->decode(trim(substr($slice, strlen($parts[0])), ', 	' . "\n" . '' . "\r" . '' . "\0" . ''));
                                                    if ($this->use & SERVICES_JSON_LOOSE_TYPE) {
                                                        $obj[$key] = $val;
                                                    } else {
                                                        $obj->{$key} = $val;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (($chrs[$c] == '"' || $chrs[$c] == '\'') && $top['what'] != SERVICES_JSON_IN_STR) {
                                        array_push($stk, array('what' => SERVICES_JSON_IN_STR, 'where' => $c, 'delim' => $chrs[$c]));
                                    } else {
                                        if ($chrs[$c] == $top['delim'] && $top['what'] == SERVICES_JSON_IN_STR && ($this->strlen8($this->substr8($chrs, 0, $c)) - $this->strlen8(rtrim($this->substr8($chrs, 0, $c), '\\'))) % 2 != 1) {
                                            array_pop($stk);
                                        } else {
                                            if ($chrs[$c] == '[' && in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
                                                array_push($stk, array('what' => SERVICES_JSON_IN_ARR, 'where' => $c, 'delim' => false));
                                            } else {
                                                if ($chrs[$c] == ']' && $top['what'] == SERVICES_JSON_IN_ARR) {
                                                    array_pop($stk);
                                                } else {
                                                    if ($chrs[$c] == '{' && in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
                                                        array_push($stk, array('what' => SERVICES_JSON_IN_OBJ, 'where' => $c, 'delim' => false));
                                                    } else {
                                                        if ($chrs[$c] == '}' && $top['what'] == SERVICES_JSON_IN_OBJ) {
                                                            array_pop($stk);
                                                        } else {
                                                            if ($substr_chrs_c_2 == '/*' && in_array($top['what'], array(SERVICES_JSON_SLICE, SERVICES_JSON_IN_ARR, SERVICES_JSON_IN_OBJ))) {
                                                                array_push($stk, array('what' => SERVICES_JSON_IN_CMT, 'where' => $c, 'delim' => false));
                                                                $c++;
                                                            } else {
                                                                if ($substr_chrs_c_2 == '*/' && $top['what'] == SERVICES_JSON_IN_CMT) {
                                                                    array_pop($stk);
                                                                    $c++;
                                                                    for ($i = $top['where']; $i <= $c; ++$i) {
                                                                        $chrs = substr_replace($chrs, ' ', $i, 1);
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
                            }
                            if (reset($stk) == SERVICES_JSON_IN_ARR) {
                                return $arr;
                            } else {
                                if (reset($stk) == SERVICES_JSON_IN_OBJ) {
                                    return $obj;
                                }
                            }
                        }
                    }
                }
        }
    }
    public function isError($data, $code = NULL)
    {
        if (is_object($data) && (get_class($data) == 'services_json_error' || is_subclass_of($data, 'services_json_error'))) {
            return true;
        }
        return false;
    }
    public function strlen8($str)
    {
        if ($this->_mb_strlen) {
            return mb_strlen($str, '8bit');
        }
        return strlen($str);
    }
    public function substr8($string, $start, $length = false)
    {
        if ($length === false) {
            $length = $this->strlen8($string) - $start;
        }
        if ($this->_mb_substr) {
            return mb_substr($string, $start, $length, '8bit');
        }
        return substr($string, $start, $length);
    }
}
class Services_JSON_Error
{
    public function __construct($message = 'unknown error', $code = NULL, $mode = NULL, $options = NULL, $userinfo = NULL)
    {
    }
}
define('SERVICES_JSON_SLICE', 1);
define('SERVICES_JSON_IN_STR', 2);
define('SERVICES_JSON_IN_ARR', 3);
define('SERVICES_JSON_IN_OBJ', 4);
define('SERVICES_JSON_IN_CMT', 5);
define('SERVICES_JSON_LOOSE_TYPE', 16);
define('SERVICES_JSON_SUPPRESS_ERRORS', 32);
define('SERVICES_JSON_USE_TO_JSON', 64);