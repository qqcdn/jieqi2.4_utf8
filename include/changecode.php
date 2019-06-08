<?php

function jieqi_gb2big5($text, $addslashes = false)
{
    $chgcode = new ChangeCode('GB2312', 'BIG5', $text);
    if (isset($chgcode)) {
        $chgcode->addslashes = $addslashes;
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_big52gb($text, $addslashes = false)
{
    $chgcode = new ChangeCode('BIG5', 'GB2312', $text);
    if (isset($chgcode)) {
        $chgcode->addslashes = $addslashes;
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_gb2py($text)
{
    $chgcode = new ChangeCode('GB2312', 'PinYin', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_big52py($text)
{
    $chgcode = new ChangeCode('BIG5', 'PinYin', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_gb2unicode($text)
{
    if ($text == '') {
        return '';
    }
    if (function_exists('iconv')) {
        $text = iconv('GBK', 'UCS-2//IGNORE', $text);
        $len = strlen($text);
        $ret = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $text[$i];
            $c2 = $text[$i + 1];
            if (0 < ord($c)) {
                $ret .= '&#x' . strtoupper(base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT)) . ';';
            } else {
                $ret .= '&#x' . strtoupper(str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT)) . ';';
            }
        }
        return $ret;
    }
    $chgcode = new ChangeCode('GB2312', 'UNICODE', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_big52unicode($text)
{
    if ($text == '') {
        return '';
    }
    if (function_exists('iconv')) {
        $text = iconv('BIG5', 'UCS-2//IGNORE', $text);
        $len = strlen($text);
        $ret = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $text[$i];
            $c2 = $text[$i + 1];
            if (0 < ord($c)) {
                $ret .= '&#x' . strtoupper(base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT)) . ';';
            } else {
                $ret .= '&#x' . strtoupper(str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT)) . ';';
            }
        }
        return $ret;
    }
    $chgcode = new ChangeCode('BIG5', 'UNICODE', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_gb2utf8($text)
{
    if ($text == '') {
        return '';
    }
    if (function_exists('iconv')) {
        $ret = iconv('GBK', 'UTF-8//IGNORE', $text);
        if ($ret) {
            return $ret;
        }
    }
    $chgcode = new ChangeCode('GB2312', 'UTF8', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_big52utf8($text)
{
    if ($text == '') {
        return '';
    }
    if (function_exists('iconv')) {
        $ret = iconv('BIG5', 'UTF-8//IGNORE', $text);
        if ($ret) {
            return $ret;
        }
    }
    $chgcode = new ChangeCode('BIG5', 'UTF8', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_utf82gb($text)
{
    if ($text == '') {
        return '';
    }
    if (function_exists('iconv')) {
        $ret = iconv('UTF-8', 'GBK//IGNORE', $text);
        if (0 < strlen($ret) && floor(strlen($text) / 2) <= strlen($ret)) {
            return $ret;
        }
    }
    $chgcode = new ChangeCode('UTF8', 'GB2312', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
function jieqi_utf82big5($text)
{
    if ($text == '') {
        return '';
    }
    if (function_exists('iconv')) {
        $ret = iconv('UTF-8', 'BIG5//IGNORE', $text);
        if (0 < strlen($ret) && floor(strlen($text) / 2) <= strlen($ret)) {
            return $ret;
        }
    }
    $chgcode = new ChangeCode('UTF8', 'BIG5', $text);
    if (isset($chgcode)) {
        return $chgcode->ConvertIT();
    } else {
        return $text;
    }
}
class ChangeCode extends JieqiObject
{
    public $pinyin_table = array();
    public $unicode_table = array();
    public $ctf;
    public $SourceText = '';
    public $codetable_dir;
    public $addslashes = false;
    public $config = array('SourceLang' => '', 'TargetLang' => '', 'GBtoBIG5_table' => 'gb-big5.table', 'BIG5toGB_table' => 'big5-gb.table', 'GBtoPinYin_table' => 'gb-pinyin.table', 'GBtoUnicode_table' => 'gb-unicode.table', 'BIG5toUnicode_table' => 'big5-unicode.table');
    public function __construct($SourceLang, $TargetLang, $SourceString = '')
    {
        $this->codetable_dir = dirname(__FILE__) . '/';
        if ($SourceLang != '') {
            $this->config['SourceLang'] = $SourceLang;
        }
        if ($TargetLang != '') {
            $this->config['TargetLang'] = $TargetLang;
        }
        if ($SourceString != '') {
            $this->SourceText = $SourceString;
        }
        $this->OpenTable();
    }
    public function _hex2bin($hexdata)
    {
        $bindata = '';
        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }
    public function OpenTable()
    {
        if ($this->config['SourceLang'] == 'GB2312') {
            if ($this->config['TargetLang'] == 'BIG5') {
                $this->ctf = fopen($this->codetable_dir . $this->config['GBtoBIG5_table'], 'r');
                if (is_null($this->ctf)) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
            }
            if ($this->config['TargetLang'] == 'PinYin') {
                $tmp = @file($this->codetable_dir . $this->config['GBtoPinYin_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $i = 0;
                for ($i = 0; $i < count($tmp); $i++) {
                    $tmp1 = explode('	', $tmp[$i]);
                    $this->pinyin_table[$i] = array($tmp1[0], $tmp1[1]);
                }
            }
            if ($this->config['TargetLang'] == 'UTF8') {
                $tmp = @file($this->codetable_dir . $this->config['GBtoUnicode_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $this->unicode_table = array();
                while (list($key, $value) = each($tmp)) {
                    $this->unicode_table[hexdec(substr($value, 0, 6))] = substr($value, 7, 6);
                }
            }
            if ($this->config['TargetLang'] == 'UNICODE') {
                $tmp = @file($this->codetable_dir . $this->config['GBtoUnicode_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $this->unicode_table = array();
                while (list($key, $value) = each($tmp)) {
                    $this->unicode_table[hexdec(substr($value, 0, 6))] = substr($value, 9, 4);
                }
            }
        }
        if ($this->config['SourceLang'] == 'BIG5') {
            if ($this->config['TargetLang'] == 'GB2312') {
                $this->ctf = fopen($this->codetable_dir . $this->config['BIG5toGB_table'], 'r');
                if (is_null($this->ctf)) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
            }
            if ($this->config['TargetLang'] == 'UTF8') {
                $tmp = @file($this->codetable_dir . $this->config['BIG5toUnicode_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $this->unicode_table = array();
                while (list($key, $value) = each($tmp)) {
                    $this->unicode_table[hexdec(substr($value, 0, 6))] = substr($value, 7, 6);
                }
            }
            if ($this->config['TargetLang'] == 'UNICODE') {
                $tmp = @file($this->codetable_dir . $this->config['BIG5toUnicode_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $this->unicode_table = array();
                while (list($key, $value) = each($tmp)) {
                    $this->unicode_table[hexdec(substr($value, 0, 6))] = substr($value, 9, 4);
                }
            }
            if ($this->config['TargetLang'] == 'PinYin') {
                $tmp = @file($this->codetable_dir . $this->config['GBtoPinYin_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $i = 0;
                for ($i = 0; $i < count($tmp); $i++) {
                    $tmp1 = explode('	', $tmp[$i]);
                    $this->pinyin_table[$i] = array($tmp1[0], $tmp1[1]);
                }
            }
        }
        if ($this->config['SourceLang'] == 'UTF8') {
            if ($this->config['TargetLang'] == 'GB2312') {
                $tmp = @file($this->codetable_dir . $this->config['GBtoUnicode_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $this->unicode_table = array();
                while (list($key, $value) = each($tmp)) {
                    $this->unicode_table[hexdec(substr($value, 7, 6))] = substr($value, 0, 6);
                }
            }
            if ($this->config['TargetLang'] == 'BIG5') {
                $tmp = @file($this->codetable_dir . $this->config['BIG5toUnicode_table']);
                if (!$tmp) {
                    $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
                }
                $this->unicode_table = array();
                while (list($key, $value) = each($tmp)) {
                    $this->unicode_table[hexdec(substr($value, 7, 6))] = substr($value, 0, 6);
                }
            }
        }
    }
    public function CHSUtoUTF8($c)
    {
        if (empty($c)) {
            return '';
        }
        $str = '';
        if ($c < 128) {
            $str .= $c;
        } else {
            if ($c < 2048) {
                $str .= 192 | $c >> 6;
                $str .= 128 | $c & 63;
            } else {
                if ($c < 65536) {
                    $str .= 224 | $c >> 12;
                    $str .= 128 | $c >> 6 & 63;
                    $str .= 128 | $c & 63;
                } else {
                    if ($c < 2097152) {
                        $str .= 240 | $c >> 18;
                        $str .= 128 | $c >> 12 & 63;
                        $str .= 128 | $c >> 6 & 63;
                        $str .= 128 | $c & 63;
                    }
                }
            }
        }
        return $str;
    }
    public function CHStoUTF8()
    {
        if ($this->config['SourceLang'] == 'BIG5' || $this->config['SourceLang'] == 'GB2312') {
            $ret = '';
            while ($this->SourceText != '') {
                if (127 < ord(substr($this->SourceText, 0, 1))) {
                    if ($this->config['SourceLang'] == 'BIG5') {
                        $utf8 = $this->CHSUtoUTF8(hexdec($this->unicode_table[hexdec(bin2hex(substr($this->SourceText, 0, 2)))]));
                    }
                    if ($this->config['SourceLang'] == 'GB2312') {
                        $utf8 = $this->CHSUtoUTF8(hexdec($this->unicode_table[hexdec(bin2hex(substr($this->SourceText, 0, 2))) - 32896]));
                    }
                    for ($i = 0; $i < strlen($utf8); $i += 3) {
                        $ret .= chr(substr($utf8, $i, 3));
                    }
                    $this->SourceText = substr($this->SourceText, 2, strlen($this->SourceText));
                } else {
                    $ret .= substr($this->SourceText, 0, 1);
                    $this->SourceText = substr($this->SourceText, 1, strlen($this->SourceText));
                }
            }
            $this->SourceText = '';
            return $ret;
        }
        if ($this->config['SourceLang'] == 'UTF8') {
            $out = '';
            $len = strlen($this->SourceText);
            $i = 0;
            while ($i < $len) {
                $c = ord(substr($this->SourceText, $i++, 1));
                switch ($c >> 4) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                        $out .= substr($this->SourceText, $i - 1, 1);
                        break;
                    case 12:
                    case 13:
                        $char2 = ord(substr($this->SourceText, $i++, 1));
                        $char3 = 0;
                        $char3 = $this->unicode_table[($c & 31) << 6 | $char2 & 63];
                        if (isset($char3)) {
                            if ($this->config['TargetLang'] == 'GB2312') {
                                $out .= $this->_hex2bin(dechex($char3 + 32896));
                            }
                            if ($this->config['TargetLang'] == 'BIG5') {
                                $out .= $this->_hex2bin($char3);
                            }
                        } else {
                            $out .= ' ';
                        }
                        break;
                    case 14:
                        $char2 = ord(substr($this->SourceText, $i++, 1));
                        $char3 = ord(substr($this->SourceText, $i++, 1));
                        $char4 = 0;
                        $char4 = $this->unicode_table[($c & 15) << 12 | ($char2 & 63) << 6 | ($char3 & 63) << 0];
                        if (isset($char4)) {
                            if ($this->config['TargetLang'] == 'GB2312') {
                                $out .= $this->_hex2bin(dechex($char4 + 32896));
                            }
                            if ($this->config['TargetLang'] == 'BIG5') {
                                $out .= $this->_hex2bin($char4);
                            }
                        } else {
                            $out .= ' ';
                        }
                        break;
                }
                $c >> 4;
            }
            return $out;
        }
    }
    public function CHStoUNICODE()
    {
        $utf = '';
        while ($this->SourceText != '') {
            if (127 < ord(substr($this->SourceText, 0, 1))) {
                if ($this->config['SourceLang'] == 'GB2312') {
                    $utf .= '&#x' . $this->unicode_table[hexdec(bin2hex(substr($this->SourceText, 0, 2))) - 32896] . ';';
                }
                if ($this->config['SourceLang'] == 'BIG5') {
                    $utf .= '&#x' . $this->unicode_table[hexdec(bin2hex(substr($this->SourceText, 0, 2)))] . ';';
                }
                $this->SourceText = substr($this->SourceText, 2, strlen($this->SourceText));
            } else {
                $utf .= substr($this->SourceText, 0, 1);
                $this->SourceText = substr($this->SourceText, 1, strlen($this->SourceText));
            }
        }
        return $utf;
    }
    public function GB2312toBIG5()
    {
        $max = strlen($this->SourceText) - 1;
        $result = '';
        for ($i = 0; $i < $max; $i++) {
            $h = ord($this->SourceText[$i]);
            if (160 <= $h) {
                $l = ord($this->SourceText[$i + 1]);
                if ($h == 161 && $l == 64) {
                    $result .= '  ';
                } else {
                    fseek($this->ctf, ($h - 160) * 510 + ($l - 1) * 2);
                    if ($this->addslashes !== true) {
                        $result .= fread($this->ctf, 2);
                    } else {
                        $result .= addslashes(fread($this->ctf, 2));
                    }
                }
                $i++;
            } else {
                $result .= $this->SourceText[$i];
            }
        }
        if ($i == $max) {
            $result .= $this->SourceText[$i];
        }
        fclose($this->ctf);
        $this->SourceText = '';
        return $result;
    }
    public function PinYinSearch($num)
    {
        if (0 < $num && $num < 160) {
            return chr($num);
        } else {
            if ($num < -20319 || -10247 < $num) {
                return '';
            } else {
                for ($i = count($this->pinyin_table) - 1; 0 <= $i; $i--) {
                    if ($this->pinyin_table[$i][1] <= $num) {
                        break;
                    }
                }
                return $this->pinyin_table[$i][0];
            }
        }
    }
    public function CHStoPinYin()
    {
        if ($this->config['SourceLang'] == 'BIG5') {
            $this->ctf = fopen($this->codetable_dir . $this->config['BIG5toGB_table'], 'r');
            if (is_null($this->ctf)) {
                $this->raiseError('Open code table file failure!', JIEQI_ERROR_RETURN);
            }
            $this->SourceText = $this->GB2312toBIG5();
            $this->config['TargetLang'] = 'PinYin';
        }
        $ret = array();
        $ri = 0;
        for ($i = 0; $i < strlen($this->SourceText); $i++) {
            $p = ord(substr($this->SourceText, $i, 1));
            if (160 < $p) {
                $q = ord(substr($this->SourceText, ++$i, 1));
                $p = $p * 256 + $q - 65536;
            }
            $ret[$ri] = $this->PinYinSearch($p);
            $ri = $ri + 1;
        }
        $this->SourceText = '';
        $this->pinyin_table = array();
        return implode(' ', $ret);
    }
    public function ConvertIT()
    {
        if (($this->config['SourceLang'] == 'GB2312' || $this->config['SourceLang'] == 'BIG5') && ($this->config['TargetLang'] == 'GB2312' || $this->config['TargetLang'] == 'BIG5')) {
            return $this->GB2312toBIG5();
        }
        if (($this->config['SourceLang'] == 'GB2312' || $this->config['SourceLang'] == 'BIG5') && $this->config['TargetLang'] == 'PinYin') {
            return $this->CHStoPinYin();
        }
        if (($this->config['SourceLang'] == 'GB2312' || $this->config['SourceLang'] == 'BIG5' || $this->config['SourceLang'] == 'UTF8') && ($this->config['TargetLang'] == 'UTF8' || $this->config['TargetLang'] == 'GB2312' || $this->config['TargetLang'] == 'BIG5')) {
            return $this->CHStoUTF8();
        }
        if (($this->config['SourceLang'] == 'GB2312' || $this->config['SourceLang'] == 'BIG5') && $this->config['TargetLang'] == 'UNICODE') {
            return $this->CHStoUNICODE();
        }
    }
}