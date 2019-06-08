<?php

class base
{
    public $time;
    public $onlineip;
    public $db;
    public $key;
    public $settings = array();
    public $cache = array();
    public $app = array();
    public $user = array();
    public $input = array();
    public function __construct()
    {
        $this->base();
    }
    public function base()
    {
        $this->init_var();
        $this->init_db();
        $this->init_cache();
        $this->init_note();
        $this->init_mail();
    }
    public function init_var()
    {
        $this->time = time();
        $cip = getenv('HTTP_CLIENT_IP');
        $xip = getenv('HTTP_X_FORWARDED_FOR');
        $rip = getenv('REMOTE_ADDR');
        $srip = $_SERVER['REMOTE_ADDR'];
        if ($cip && strcasecmp($cip, 'unknown')) {
            $this->onlineip = $cip;
        } else {
            if ($xip && strcasecmp($xip, 'unknown')) {
                $this->onlineip = $xip;
            } else {
                if ($rip && strcasecmp($rip, 'unknown')) {
                    $this->onlineip = $rip;
                } else {
                    if ($srip && strcasecmp($srip, 'unknown')) {
                        $this->onlineip = $srip;
                    }
                }
            }
        }
        preg_match('/[\\d\\.]{7,15}/', $this->onlineip, $match);
        $this->onlineip = $match[0] ? $match[0] : 'unknown';
        $this->app['appid'] = UC_APPID;
    }
    public function init_input()
    {
    }
    public function init_db()
    {
        require_once UC_ROOT . 'lib/db.class.php';
        $this->db = new ucclient_db();
        $this->db->connect(UC_DBHOST, UC_DBUSER, UC_DBPW, '', UC_DBCHARSET, UC_DBCONNECT, UC_DBTABLEPRE);
    }
    public function load($model, $base = NULL)
    {
        $base = $base ? $base : $this;
        if (empty($_ENV[$model])) {
            require_once UC_ROOT . './model/' . $model . '.php';
            eval('$_ENV[$model] = new ' . $model . 'model($base);');
        }
        return $_ENV[$model];
    }
    public function date($time, $type = 3)
    {
        if (!$this->settings) {
            $this->settings = $this->cache('settings');
        }
        $format[] = $type & 2 ? !empty($this->settings['dateformat']) ? $this->settings['dateformat'] : 'Y-n-j' : '';
        $format[] = $type & 1 ? !empty($this->settings['timeformat']) ? $this->settings['timeformat'] : 'H:i' : '';
        return gmdate(implode(' ', $format), $time + $this->settings['timeoffset']);
    }
    public function page_get_start($page, $ppp, $totalnum)
    {
        $totalpage = ceil($totalnum / $ppp);
        $page = max(1, min($totalpage, intval($page)));
        return ($page - 1) * $ppp;
    }
    public function implode($arr)
    {
        return '\'' . implode('\',\'', (array) $arr) . '\'';
    }
    public function cache($cachefile)
    {
        static $_CACHE = array();
        if (!isset($_CACHE[$cachefile])) {
            $cachepath = UC_DATADIR . './cache/' . $cachefile . '.php';
            if (!file_exists($cachepath)) {
                $this->load('cache');
                $_ENV['cache']->updatedata($cachefile);
            } else {
                include_once $cachepath;
            }
        }
        return $_CACHE[$cachefile];
    }
    public function get_setting($k = array(), $decode = false)
    {
        $return = array();
        $sqladd = $k ? 'WHERE k IN (' . $this->implode($k) . ')' : '';
        $settings = $this->db->fetch_all('SELECT * FROM ' . UC_DBTABLEPRE . 'settings ' . $sqladd);
        if (is_array($settings)) {
            foreach ($settings as $arr) {
                $return[$arr['k']] = $decode ? unserialize($arr['v']) : $arr['v'];
            }
        }
        return $return;
    }
    public function init_cache()
    {
        $this->settings = $this->cache('settings');
        $this->cache['apps'] = $this->cache('apps');
        if ('5.1' < PHP_VERSION) {
            $timeoffset = intval($this->settings['timeoffset'] / 3600);
            @date_default_timezone_set('Etc/GMT' . (0 < $timeoffset ? '-' : '+') . abs($timeoffset));
        }
    }
    public function cutstr($string, $length, $dot = ' ...')
    {
        if (strlen($string) <= $length) {
            return $string;
        }
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
        $strcut = '';
        if (strtolower(UC_CHARSET) == 'utf-8') {
            $n = $tn = $noc = 0;
            while ($n < strlen($string)) {
                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || 32 <= $t && $t <= 126) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } else {
                    if (194 <= $t && $t <= 223) {
                        $tn = 2;
                        $n += 2;
                        $noc += 2;
                    } else {
                        if (224 <= $t && $t < 239) {
                            $tn = 3;
                            $n += 3;
                            $noc += 2;
                        } else {
                            if (240 <= $t && $t <= 247) {
                                $tn = 4;
                                $n += 4;
                                $noc += 2;
                            } else {
                                if (248 <= $t && $t <= 251) {
                                    $tn = 5;
                                    $n += 5;
                                    $noc += 2;
                                } else {
                                    if ($t == 252 || $t == 253) {
                                        $tn = 6;
                                        $n += 6;
                                        $noc += 2;
                                    } else {
                                        $n++;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($length <= $noc) {
                    break;
                }
            }
            if ($length < $noc) {
                $n -= $tn;
            }
            $strcut = substr($string, 0, $n);
        } else {
            for ($i = 0; $i < $length; $i++) {
                $strcut .= 127 < ord($string[$i]) ? $string[$i] . $string[++$i] : $string[$i];
            }
        }
        $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
        return $strcut . $dot;
    }
    public function init_note()
    {
        if ($this->note_exists()) {
            $this->load('note');
            $_ENV['note']->send();
        }
    }
    public function note_exists()
    {
        $noteexists = $this->db->fetch_first('SELECT value FROM ' . UC_DBTABLEPRE . 'vars WHERE name=\'noteexists' . UC_APPID . '\'');
        if (empty($noteexists)) {
            return false;
        } else {
            return true;
        }
    }
    public function init_mail()
    {
        if ($this->mail_exists() && !getgpc('inajax')) {
            $this->load('mail');
            $_ENV['mail']->send();
        }
    }
    public function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        return uc_authcode($string, $operation, $key, $expiry);
    }
    public function unserialize($s)
    {
        return uc_unserialize($s);
    }
    public function input($k)
    {
        return isset($this->input[$k]) ? is_array($this->input[$k]) ? $this->input[$k] : trim($this->input[$k]) : NULL;
    }
    public function mail_exists()
    {
        $mailexists = $this->db->fetch_first('SELECT value FROM ' . UC_DBTABLEPRE . 'vars WHERE name=\'mailexists\'');
        if (empty($mailexists)) {
            return false;
        } else {
            return true;
        }
    }
    public function dstripslashes($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = $this->dstripslashes($val);
            }
        } else {
            $string = stripslashes($string);
        }
        return $string;
    }
}
!defined('IN_UC') && exit('Access Denied');
if (!function_exists('getgpc')) {
    function getgpc($k, $var = 'G')
    {
        switch ($var) {
            case 'G':
                $var =& $_GET;
                break;
            case 'P':
                $var =& $_POST;
                break;
            case 'C':
                $var =& $_COOKIE;
                break;
            case 'R':
                $var =& $_REQUEST;
                break;
        }
        return isset($var[$k]) ? $var[$k] : NULL;
    }
}