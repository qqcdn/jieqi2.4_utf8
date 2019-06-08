<?php

class JieqiChecker extends JieqiObject
{
    public function __construct()
    {
    }
    public function checkvar(&$val, $fun)
    {
        $funary = explode('|', $fun);
        foreach ($funary as $v) {
            $params = explode(':', $v);
            $fname = $params[0];
            unset($params[0]);
            $parnum = count($params);
            if (!empty($fname) && method_exists($this, $fname)) {
                switch ($parnum) {
                    case 1:
                        $this->{$fname}($val, $params[0]);
                        break;
                    case 2:
                        $this->{$fname}($val, $params[0], $params[1]);
                        break;
                    case 3:
                        $this->{$fname}($val, $params[0], $params[1], $params[2]);
                        break;
                    case 4:
                        $this->{$fname}($val, $params[0], $params[1], $params[2], $params[3]);
                        break;
                    default:
                        $this->{$fname}($val);
                        break;
                }
            }
        }
    }
    public function checkvars(&$vals, $funs)
    {
        foreach ($vals as $k => $v) {
            $this->checkvar($vals[$k], $funs[$k]);
        }
    }
    public function _error($err)
    {
        $this->raiseError($err, JIEQI_ERROR_RETURN);
        return false;
    }
    public function is_required(&$value)
    {
        return 0 < strlen($value) ? true : $this->_error('is_required');
    }
    public function is_numeric(&$value)
    {
        return is_numeric($value) ? true : $this->_error('is_numeric');
    }
    public function is_alpha(&$value)
    {
        return ctype_alpha($value) ? true : $this->_error('is_alpha');
    }
    public function is_alnum(&$value)
    {
        return ctype_alnum($value) ? true : $this->_error('is_alnum');
    }
    public function is_aldash(&$value)
    {
        return preg_match('/^[a-z0-9_]+$/i', $value) ? true : $this->_error('is_aldash');
    }
    public function is_email(&$value)
    {
        return preg_match('/^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+([\\.][a-z0-9-]+)+$/i', $value) ? true : $this->_error('is_email');
    }
    public function is_url(&$value)
    {
        return preg_match('/^https?:\\/\\/[a-z0-9\\/\\-_+=.~!%@?#%&;:$\\│]+$/i', $value) ? true : $this->_error('is_url');
    }
    public function is_date(&$value)
    {
        return preg_match('/^\\d{2,4}-\\d{1,2}-\\d{1,2}$/i', $value) ? true : $this->_error('is_date');
    }
    public function is_time(&$value)
    {
        return preg_match('/^\\d{1,2}:\\d{1,2}:\\d{1,2}$/i', $value) ? true : $this->_error('is_time');
    }
    public function is_datetime(&$value)
    {
        return preg_match('/^\\d{2,4}-\\d{1,2}-\\d{1,2} \\d{1,2}:\\d{1,2}:\\d{1,2}$/i', $value) ? true : $this->_error('is_datetime');
    }
    public function is_ip(&$value)
    {
        return preg_match('/^\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}$/i', $value) ? true : $this->_error('is_ip');
    }
    public function is_match(&$value, $match)
    {
        return preg_match($match, $value) ? true : $this->_error('is_match');
    }
    public function str_min(&$value, $min = 0)
    {
        return intval($min) <= strlen($value) ? true : $this->_error('str_min');
    }
    public function str_max(&$value, $max = 99999999)
    {
        return strlen($value) <= intval($max) ? true : $this->_error('str_max');
    }
    public function str_between(&$value, $min = 0, $max = 99999999)
    {
        return intval($min) <= strlen($value) && strlen($value) <= intval($max) ? true : $this->_error('str_between');
    }
    public function num_min(&$value, $min = 0)
    {
        return $min <= $value ? true : $this->_error('num_min');
    }
    public function num_max(&$value, $max = 99999999)
    {
        return $value <= $max ? true : $this->_error('num_max');
    }
    public function num_between(&$value, $min = 0, $max = 99999999)
    {
        return $min <= $value && $value <= $max ? true : $this->_error('num_between');
    }
    public function deny_words(&$value, $words, $retmatch = false, $fullmatch = false)
    {
        if (!is_array($words)) {
            $words = explode("\n", strval($words));
        }
        if (0 < count($words)) {
            $pregstr = '';
            foreach ($words as $v) {
                $v = trim($v);
                if (0 < strlen($v)) {
                    if ($pregstr != '') {
                        $pregstr .= '|';
                    }
                    $pregstr .= str_replace(array('\\*', '\\?'), array('.*', '.?'), preg_quote($v, '/'));
                }
            }
            if ($pregstr == '') {
                return true;
            } else {
                $pregstr = $fullmatch ? '/^(' . $pregstr . ')$/is' : '/(' . $pregstr . ')/is';
                if ($retmatch) {
                    if (preg_match_all($pregstr, $value, $matches)) {
                        $this->raiseError('deny_words', JIEQI_ERROR_RETURN);
                        return $matches[1];
                    } else {
                        return true;
                    }
                } else {
                    if (preg_match($pregstr, $value)) {
                        $this->raiseError('deny_words', JIEQI_ERROR_RETURN);
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        } else {
            return true;
        }
    }
    public function replace_words(&$value, $words, $hide = '**')
    {
        $from = array();
        $to = array();
        if (is_array($words)) {
            foreach ($words as $k => $v) {
                $k = trim(strval($k));
                if (0 < strlen($k)) {
                    $from[] = $k;
                    $to[] = trim(strval($v));
                }
            }
        } else {
            $words = explode("\n", strval($words));
            foreach ($words as $word) {
                $pos = strpos($word, '=');
                if (0 < $pos) {
                    $sf = trim(substr($word, 0, $pos));
                    if (0 < strlen($sf)) {
                        $st = trim(substr($word, $pos + 1));
                        if (strlen($st) == 0) {
                            $st = $hide;
                        }
                        $from[] = $sf;
                        $to[] = $st;
                    }
                }
            }
        }
        if (0 < count($from)) {
            if (function_exists('mb_eregi_replace')) {
                $system_charset = strtolower(JIEQI_SYSTEM_CHARSET);
                $jieqi_charset_map = array('gb2312' => 'CP936', 'gbk' => 'CP936', 'gb' => 'CP936', 'big5' => 'CP950', 'utf-8' => 'UTF-8', 'utf8' => 'UTF-8');
                $charset_name_in = 'UTF-8';
                $charset_name_out = isset($jieqi_charset_map[$system_charset]) ? $jieqi_charset_map[$system_charset] : 'UTF-8';
                mb_regex_encoding('UTF-8');
                if ($charset_name_in != $charset_name_out) {
                    $value = mb_convert_encoding($value, $charset_name_in, $charset_name_out);
                }
                foreach ($from as $k => $f) {
                    $f = preg_quote($f);
                    if ($charset_name_in != $charset_name_out) {
                        $f = mb_convert_encoding($f, $charset_name_in, $charset_name_out);
                    }
                    $t = $to[$k];
                    if ($charset_name_in != $charset_name_out) {
                        $t = mb_convert_encoding($t, $charset_name_in, $charset_name_out);
                    }
                    $value = mb_eregi_replace($f, $t, $value);
                }
                if ($charset_name_in != $charset_name_out) {
                    $value = mb_convert_encoding($value, $charset_name_out, $charset_name_in);
                }
            } else {
                $value = str_replace($from, $to, $value);
            }
        }
        return $value;
    }
    public function deny_rubbish(&$value, $level = 1)
    {
        if (empty($level)) {
            return true;
        }
        $ret = true;
        $len = strlen($value);
        $specialnum = 0;
        $tmpstr = '';
        $tmpstr1 = '';
        $renum = 0;
        for ($i = 0; $i < $len; $i++) {
            if (128 < ord($value[$i])) {
                $tmpstr = $value[$i] . $value[$i + 1];
                $i++;
            } else {
                $tmpstr = $value[$i];
                $tmpasc = ord($value[$i]);
                if ($tmpasc < 65 || 90 < $tmpasc && $tmpasc < 97 || 122 < $tmpasc) {
                    $specialnum++;
                }
            }
            if ($tmpstr == $tmpstr1) {
                $renum++;
                if (6 < $renum) {
                    $this->raiseError('deny_rubbish', JIEQI_ERROR_RETURN);
                    return false;
                }
            } else {
                $renum = 0;
            }
            if ($tmpstr != ' ') {
                $tmpstr1 = $tmpstr;
            }
        }
        if (10 < $specialnum && $len < $specialnum * 2) {
            $this->raiseError('deny_rubbish', JIEQI_ERROR_RETURN);
            return false;
        }
        return $ret;
    }
    public function auth_type(&$value, $types)
    {
        if (!is_array($types)) {
            $types = array(strval($types));
        }
        if (0 < count($types)) {
            $alltypes = array('int', 'integer', 'numeric', 'float', 'bool', 'string', 'array', 'object');
            foreach ($types as $type) {
                if (in_array($type, $alltypes) && call_user_func('is_' . $type, $value)) {
                    return true;
                }
            }
            $this->raiseError('auth_type', JIEQI_ERROR_RETURN);
            return false;
        } else {
            return true;
        }
    }
    public function deny_time($timeset)
    {
        $now = floatval(date('G.i', JIEQI_NOW_TIME));
        $times = explode("\n", str_replace(':', '.', $timeset));
        foreach ($times as $t) {
            list($timefrom, $timeto) = explode('-', $t);
            $timefrom = floatval(trim($timefrom));
            $timeto = floatval(trim($timeto));
            if ($timeto < $timefrom && ($timefrom <= $now || $now < $timeto) || $timefrom < $timeto && $timefrom <= $now && $now < $timeto) {
                return false;
            }
        }
        return true;
    }
    public function interval_time($sec, $svar, $cvar = 'jieqiVisitTime')
    {
        $sec = intval($sec);
        if (empty($sec)) {
            return true;
        }
        if (isset($_COOKIE[$cvar])) {
            $jieqi_vtime = jieqi_strtosary($_COOKIE[$cvar]);
        } else {
            $jieqi_vtime = array();
        }
        if (!empty($_SESSION[$svar])) {
            $logtime = $_SESSION[$svar];
        } else {
            if (!empty($jieqi_vtime[$svar])) {
                $logtime = $jieqi_vtime[$svar];
            } else {
                $logtime = 0;
            }
        }
        if (0 < $logtime && JIEQI_NOW_TIME - $logtime < $sec) {
            $this->raiseError('interval_time', JIEQI_ERROR_RETURN);
            return false;
        }
        $_SESSION[$svar] = JIEQI_NOW_TIME;
        $jieqi_vtime[$svar] = JIEQI_NOW_TIME;
        setcookie($cvar, jieqi_sarytostr($jieqi_vtime), JIEQI_NOW_TIME + 3600, '/', JIEQI_COOKIE_DOMAIN, 0);
        return true;
    }
    public function valid_checkcode($code, $svar = 'jieqiCheckCode')
    {
        return empty($_SESSION[$svar]) || strcasecmp($code, $_SESSION[$svar]) == 0 ? true : $this->_error('valid_checkcode');
    }
    public function safe_title(&$value)
    {
        $len = strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $tmpvar = ord($value[$i]);
            if (128 < $tmpvar) {
                $i++;
            } else {
                if ($tmpvar == 34 || $tmpvar == 38 || $tmpvar == 39 || $tmpvar == 44 || $tmpvar == 47 || $tmpvar == 59 || $tmpvar == 60 || $tmpvar == 62 || $tmpvar == 92 || $tmpvar == 124) {
                    $this->_error('safe_title');
                    return false;
                }
            }
        }
        return true;
    }
}