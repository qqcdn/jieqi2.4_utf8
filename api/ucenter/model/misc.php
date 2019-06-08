<?php

class miscmodel
{
    public $db;
    public $base;
    public function __construct(&$base)
    {
        $this->miscmodel($base);
    }
    public function miscmodel(&$base)
    {
        $this->base = $base;
        $this->db = $base->db;
    }
    public function get_apps($col = '*', $where = '')
    {
        $arr = $this->db->fetch_all('SELECT ' . $col . ' FROM ' . UC_DBTABLEPRE . 'applications' . ($where ? ' WHERE ' . $where : ''));
        return $arr;
    }
    public function delete_apps($appids)
    {
    }
    public function update_app($appid, $name, $url, $authkey, $charset, $dbcharset)
    {
    }
    public function alter_app_table($appid, $operation = 'ADD')
    {
    }
    public function get_host_by_url($url)
    {
    }
    public function check_url($url)
    {
    }
    public function check_ip($url)
    {
    }
    public function test_api($url, $ip = '')
    {
    }
    public function dfopen2($url, $limit = 0, $post = '', $cookie = '', $bysocket = false, $ip = '', $timeout = 15, $block = true, $encodetype = 'URLENCODE')
    {
        $__times__ = isset($_GET['__times__']) ? intval($_GET['__times__']) + 1 : 1;
        if (2 < $__times__) {
            return '';
        }
        $url .= (strpos($url, '?') === false ? '?' : '&') . '__times__=' . $__times__;
        return $this->dfopen($url, $limit, $post, $cookie, $bysocket, $ip, $timeout, $block, $encodetype);
    }
    public function dfopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = false, $ip = '', $timeout = 15, $block = true, $encodetype = 'URLENCODE')
    {
        $return = '';
        $matches = parse_url($url);
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;
        if ($post) {
            $out = 'POST ' . $path . ' HTTP/1.0' . "\r\n" . '';
            $out .= 'Accept: */*' . "\r\n" . '';
            $out .= 'Accept-Language: zh-cn' . "\r\n" . '';
            $boundary = $encodetype == 'URLENCODE' ? '' : ';' . substr($post, 0, trim(strpos($post, "\n")));
            $out .= $encodetype == 'URLENCODE' ? 'Content-Type: application/x-www-form-urlencoded' . "\r\n" . '' : 'Content-Type: multipart/form-data' . $boundary . "\r\n";
            $out .= 'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
            $out .= 'Host: ' . $host . "\r\n";
            $out .= 'Content-Length: ' . strlen($post) . "\r\n";
            $out .= 'Connection: Close' . "\r\n" . '';
            $out .= 'Cache-Control: no-cache' . "\r\n" . '';
            $out .= 'Cookie: ' . $cookie . '' . "\r\n" . '' . "\r\n" . '';
            $out .= $post;
        } else {
            $out = 'GET ' . $path . ' HTTP/1.0' . "\r\n" . '';
            $out .= 'Accept: */*' . "\r\n" . '';
            $out .= 'Accept-Language: zh-cn' . "\r\n" . '';
            $out .= 'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
            $out .= 'Host: ' . $host . "\r\n";
            $out .= 'Connection: Close' . "\r\n" . '';
            $out .= 'Cookie: ' . $cookie . '' . "\r\n" . '' . "\r\n" . '';
        }
        if (function_exists('fsockopen')) {
            $fp = @fsockopen($ip ? $ip : $host, $port, $errno, $errstr, $timeout);
        } else {
            if (function_exists('pfsockopen')) {
                $fp = @pfsockopen($ip ? $ip : $host, $port, $errno, $errstr, $timeout);
            } else {
                $fp = false;
            }
        }
        if (!$fp) {
            return '';
        } else {
            stream_set_blocking($fp, $block);
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            $status = stream_get_meta_data($fp);
            if (!$status['timed_out']) {
                while (!feof($fp)) {
                    if (($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n")) {
                        break;
                    }
                }
                $stop = false;
                while (!feof($fp) && !$stop) {
                    $data = fread($fp, $limit == 0 || 8192 < $limit ? 8192 : $limit);
                    $return .= $data;
                    if ($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }
            }
            @fclose($fp);
            return $return;
        }
    }
    public function array2string($arr)
    {
        $s = $sep = '';
        if ($arr && is_array($arr)) {
            foreach ($arr as $k => $v) {
                $s .= $sep . $k . UC_ARRAY_SEP_1 . $v;
                $sep = UC_ARRAY_SEP_2;
            }
        }
        return $s;
    }
    public function string2array($s)
    {
        $arr = explode(UC_ARRAY_SEP_2, $s);
        $arr2 = array();
        foreach ($arr as $k => $v) {
            list($key, $val) = explode(UC_ARRAY_SEP_1, $v);
            $arr2[$key] = $val;
        }
        return $arr2;
    }
}
!defined('IN_UC') && exit('Access Denied');
define('UC_ARRAY_SEP_1', 'UC_ARRAY_SEP_1');
define('UC_ARRAY_SEP_2', 'UC_ARRAY_SEP_2');