<?php

function jieqi_getpinyin($str, $ishead = 0, $isclose = 0)
{
    global $jieqiPinyins;
    if (defined('JIEQI_SYSTEM_CHARSET') && JIEQI_SYSTEM_CHARSET != 'gbk') {
        return '';
    }
    $ret = '';
    $str = trim($str);
    $slen = strlen($str);
    if ($slen < 2) {
        return $str;
    }
    if (!isset($jieqiPinyins) || !is_array($jieqiPinyins)) {
        $jieqiPinyins = array();
        $fp = fopen(dirname(__FILE__) . '/pinyin.dat', 'r');
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            $jieqiPinyins[$line[0] . $line[1]] = substr($line, 2);
        }
        fclose($fp);
    }
    for ($i = 0; $i < $slen; $i++) {
        if (128 < ord($str[$i])) {
            $c = $str[$i] . $str[$i + 1];
            $i++;
            if (isset($jieqiPinyins[$c])) {
                if (!$ishead) {
                    $ret .= $jieqiPinyins[$c];
                } else {
                    $ret .= $jieqiPinyins[$c][0];
                }
            } else {
                $ret .= '_';
            }
        } else {
            if (preg_match('/[a-z0-9]/i', $str[$i])) {
                $ret .= $str[$i];
            } else {
                $ret .= '_';
            }
        }
    }
    if ($isclose) {
        unset($jieqiPinyins);
    }
    return $ret;
}
function jieqi_getinitial($str)
{
    $ret = jieqi_getpinyin($str, 1);
    return $ret[0] == '_' ? '1' : strtoupper($ret[0]);
}
function jieqi_limitwidth($str = '', $width = 80, $start = 0)
{
    $tmpstr = '';
    $strlen = strlen($str);
    $point = $start;
    for ($i = 0; $i < $strlen; $i++) {
        if ($width <= $point) {
            $tmpstr .= "\n";
            $point = 0;
        }
        if (128 < ord($str[$i])) {
            $tmpstr .= $str[$i] . $str[++$i];
            $point += 2;
        } else {
            $tmpstr .= $str[$i];
            if ($str[$i] == "\n") {
                $point = 0;
            } else {
                $point += 1;
            }
        }
    }
    return $tmpstr;
}
function jieqi_safestring($str)
{
    return true;
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        $tmpvar = ord($str[$i]);
        if (128 < $tmpvar) {
            $i++;
        } else {
            if ($tmpvar == 34 || $tmpvar == 38 || $tmpvar == 39 || $tmpvar == 44 || $tmpvar == 47 || $tmpvar == 59 || $tmpvar == 60 || $tmpvar == 62 || $tmpvar == 92 || $tmpvar == 124) {
                return false;
            }
        }
    }
    return true;
}
function jieqi_pregconvert($str)
{
    $from = array(' ', '/', '\\*', '\\!', '~', '\\$', '\\^');
    $to = array('\\s', '\\/', '.*', '[^\\>\\<]*', '[^\\>\'"]*', '[\\d]*', '[^\\<\\>\\d]*');
    $str = preg_quote($str);
    $str = str_replace($from, $to, $str);
    return $str;
}
function jieqi_sbcstr($str)
{
    return $str;
    $repary = array(' ' => '　', '"' => '＂', '&' => '＆', '\'' => '＇', ',' => '，', '/' => '／', ';' => '；', '<' => '＜', '>' => '＞', '\\' => '＼');
    $len = strlen($str);
    $ret = '';
    for ($i = 0; $i < $len; $i++) {
        $tmpvar = ord($str[$i]);
        if (128 < $tmpvar) {
            $ret .= $str[$i] . $str[$i + 1];
            $i++;
        } else {
            $ret .= isset($repary[$str[$i]]) ? $repary[$str[$i]] : $str[$i];
        }
    }
    return $ret;
}
function jieqi_dbcstr($str)
{
    return $str;
    $from = array('　', '＂', '＆', '＇', '，', '／', '；', '＜', '＞', '＼');
    $to = array(' ', '"', '&', '\'', ',', '/', ';', '<', '>', '\\');
    $str = str_replace($from, $to, $str);
    return $str;
}
function jieqi_textstr($str, $unclickable = false)
{
    if ($unclickable) {
        $search = array('/<img[^\\<\\>]+src=[\'"]?([^\\<\\>\'"\\s]*)[\'"]?/is', '/<a[^\\<\\>]+href=[\'"]?([^\\<\\>\'"\\s]*)[\'"]?/is', '/on[a-z]+[\\s]*=[\\s]*"[^"]*"/is', '/on[a-z]+[\\s]*=[\\s]*\'[^\']*\'/is');
        $replace = array('\\1<br>\\0', '\\1<br>\\0', '', '');
        $str = preg_replace($search, $replace, $str);
    }
    $search = array('/(' . "\r" . '|' . "\n" . ')\\s+/', '/' . "\r" . '|' . "\n" . '/', '/\\<br[^\\>]*\\>/i', '/\\<[\\s]*\\/p[\\s]*\\>/i', '/\\<[\\s]*p[\\s]*\\>/i', '/\\<script[^\\>]*\\>.*\\<\\/script\\>/is', '/\\<[\\/\\!]*[^\\<\\>]*\\>/is', '/&(quot|#34);/i', '/&(amp|#38);/i', '/&(lt|#60);/i', '/&(gt|#62);/i', '/&(nbsp|#160);/i', '/&([a-z]+);/i');
    $replace = array(' ', '', "\r\n", '', '' . "\r\n" . '' . "\r\n" . '', '', '', '"', '&', '<', '>', ' ', '');
    $str = preg_replace($search, $replace, $str);
    $str = strip_tags($str);
    return $str;
}
function jieqi_urlcontents($url, $params = array())
{
    $ret = '';
    $count = 0;
    $url = str_replace(' ', '%20', $url);
    if (is_numeric($params)) {
        $params = array('repeat' => $params);
    }
    if (!isset($params['repeat']) || !is_numeric($params['repeat'])) {
        $params['repeat'] = 1;
    }
    if (!isset($params['delay'])) {
        $params['delay'] = 0;
    }
    if (!isset($params['charset'])) {
        $params['charset'] = 'auto';
    }
    while (empty($ret) && $count < $params['repeat']) {
        $count++;
        if (1 < $count && 0 < $params['delay']) {
            if (1000 <= $params['delay']) {
                usleep($params['delay']);
            } else {
                sleep($params['delay']);
            }
        }
        if (!empty($params['proxy_host']) && !empty($params['proxy_port']) || !empty($params['referer']) || !empty($params['cookiefile'])) {
            if (!defined('LIB_REQUEST_INCLUDE')) {
                include_once JIEQI_ROOT_PATH . '/lib/net/client.php';
                define('LIB_REQUEST_INCLUDE', 1);
            }
            $client = new JieqiClient();
            $client->enableHistory(false);
            if (!empty($params['useragent'])) {
                $client->setDefaultHeader('User-Agent', jieqi_headstr($params['useragent']));
            }
            if (!empty($params['referer']) && substr($params['referer'], 0, 4) == 'http') {
                $client->setDefaultHeader('Referer', jieqi_headstr($params['referer']));
            }
            if (!empty($params['proxy_host']) && !empty($params['proxy_port'])) {
                $client->setRequestParameter('proxy_host', $params['proxy_host']);
                $client->setRequestParameter('proxy_port', $params['proxy_port']);
                if (!empty($params['proxy_user'])) {
                    $client->setRequestParameter('proxy_user', $params['proxy_user']);
                    $client->setDefaultHeader('Proxy-Authorization', jieqi_headstr('Basic ' . base64_encode($params['proxy_user'] . ':' . $params['proxy_pass'])));
                }
                if (!empty($params['proxy_pass'])) {
                    $client->setRequestParameter('proxy_pass', $params['proxy_pass']);
                }
            }
            $jieqiCollectCookies = array();
            if (!empty($params['cookiefile']) && preg_match('/^[\\w\\.\\/\\\\:]+$/', $params['cookiefile']) && is_file($params['cookiefile']) && preg_match('/\\.php$/i', @realpath($params['cookiefile'])) && JIEQI_NOW_TIME - filemtime($params['cookiefile']) < $params['cookielife']) {
                include_once $params['cookiefile'];
                $client->setDefaultCookies($jieqiCollectCookies);
            }
            $client->get($url);
            $res = $client->currentResponse();
            $ret = '';
            if ($res['code'] == '200' && !empty($res['body'])) {
                $ret = $res['body'];
                if (!empty($params['cookiefile']) && preg_match('/^[\\w\\.\\/\\\\:]+$/', $params['cookiefile'])) {
                    $jieqiCollectCookies = $client->getDefaultCookies();
                    $filedata = jieqi_extractvars('jieqiCollectCookies', $jieqiCollectCookies);
                    $filedata = '<?php' . "\r\n" . '' . $filedata . '' . "\r\n" . '?>';
                    jieqi_writefile($params['cookiefile'], $filedata);
                }
            }
            unset($client);
        } else {
            if (function_exists('curl_init')) {
                $ci = curl_init();
                $useragent = empty($params['useragent']) ? 'User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)' : $params['useragent'];
                curl_setopt($ci, CURLOPT_USERAGENT, $useragent);
                $connecttimeout = empty($params['connecttimeout']) ? 15 : $params['connecttimeout'];
                curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
                $timeout = empty($params['timeout']) ? 15 : $params['timeout'];
                curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ci, CURLOPT_HEADER, false);
                curl_setopt($ci, CURLINFO_HEADER_OUT, true);
                curl_setopt($ci, CURLOPT_URL, $url);
                $ret = curl_exec($ci);
                $err = curl_error($ci);
                if (false === $ret || !empty($err)) {
                    $ret = $err;
                }
                curl_close($ci);
            } else {
                $context = array('http' => array('header' => empty($params['useragent']) ? 'User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)' . "\r\n" . '' : $params['useragent'] . "\r\n", 'timeout' => empty($params['timeout']) ? 3 : $params['timeout']));
                $stream_context = stream_context_create($context);
                $ret = @file_get_contents($url, false, $stream_context);
            }
        }
    }
    if (!empty($ret) && !preg_match('/\\.(gif|jpg|jpeg|png|bmp|swf|svg)$/i', $url) && in_array($params['charset'], array('auto', 'gb2312', 'gbk', 'gb', 'big5', 'utf8', 'utf-8'))) {
        if ($params['charset'] == 'auto') {
            preg_match('/\\<meta[^\\<\\>]*content[\\s]*=[\\s]*(\'|")?[^\\/;]*\\/[^\\/;]*;[\\s]*charset[\\s]*=[\\s]*(gb2312|gbk|big5|utf-8)(\'|")?[^\\<\\>]*\\>/is', $ret, $matches);
            if (!empty($matches[2])) {
                $pagecherset = strtolower(trim($matches[2]));
            } else {
                $pagecherset = strtolower(JIEQI_SYSTEM_CHARSET);
            }
        } else {
            $pagecherset = $params['charset'];
        }
        $defaultcharset = strtolower(JIEQI_SYSTEM_CHARSET);
        $jieqi_charset_map = array('gb2312' => 'gb', 'gbk' => 'gb', 'gb' => 'gb', 'big5' => 'big5', 'utf-8' => 'utf8', 'utf8' => 'utf8');
        if ($pagecherset != $defaultcharset && isset($jieqi_charset_map[$pagecherset]) && isset($jieqi_charset_map[$defaultcharset])) {
            include_once JIEQI_ROOT_PATH . '/include/changecode.php';
            $funname = 'jieqi_' . $jieqi_charset_map[$pagecherset] . '2' . $jieqi_charset_map[$defaultcharset];
            if (function_exists($funname)) {
                $ret = call_user_func($funname, $ret);
            }
        }
    }
    return $ret;
}
function jieqi_randstr($length = 6, $mode = 7)
{
    $str1 = '1234567890';
    $str2 = 'abcdefghijklmnopqrstuvwxyz';
    $str3 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str4 = '_';
    $str5 = '`~!@#$%^&*()-+=\\|{}[];:\'",./?';
    $str = '';
    if (0 < ($mode & 1)) {
        $str .= $str1;
    }
    if (0 < ($mode & 2)) {
        $str .= $str2;
    }
    if (0 < ($mode & 4)) {
        $str .= $str3;
    }
    if (0 < ($mode & 8)) {
        $str .= $str4;
    }
    if (0 < ($mode & 16)) {
        $str .= $str5;
    }
    $result = '';
    $l = strlen($str) - 1;
    srand((double) microtime() * 1000000);
    for ($i = 0; $i < $length; $i++) {
        $num = rand(0, $l);
        $result .= $str[$num];
    }
    return $result;
}
function jieqi_mbreplace($from, $to, $str)
{
    if (function_exists('mb_eregi_replace')) {
        $system_charset = strtolower(JIEQI_SYSTEM_CHARSET);
        $jieqi_charset_map = array('gb2312' => 'CP936', 'gbk' => 'CP936', 'gb' => 'CP936', 'big5' => 'CP950', 'utf-8' => 'UTF-8', 'utf8' => 'UTF-8');
        $charset_name_in = 'UTF-8';
        $charset_name_out = isset($jieqi_charset_map[$system_charset]) ? $jieqi_charset_map[$system_charset] : 'UTF-8';
        mb_regex_encoding('UTF-8');
        if ($charset_name_in != $charset_name_out) {
            $str = mb_convert_encoding($str, $charset_name_in, $charset_name_out);
        }
        if (!is_array($from)) {
            $from = array($from);
        }
        foreach ($from as $k => $f) {
            $f = preg_quote($f);
            if ($charset_name_in != $charset_name_out) {
                $f = mb_convert_encoding($f, $charset_name_in, $charset_name_out);
            }
            if (is_array($to) && isset($to[$k])) {
                $t = $to[$k];
            } else {
                if (is_string($to)) {
                    $t = $to;
                } else {
                    $t = '';
                }
            }
            if ($charset_name_in != $charset_name_out) {
                $t = mb_convert_encoding($t, $charset_name_in, $charset_name_out);
            }
            $str = mb_eregi_replace($f, $t, $str);
        }
        if ($charset_name_in != $charset_name_out) {
            $str = mb_convert_encoding($str, $charset_name_out, $charset_name_in);
        }
    } else {
        $str = str_replace($from, $to, $str);
    }
    return $str;
}