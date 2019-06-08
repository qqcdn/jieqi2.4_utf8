<?php

class JieqiApiSign
{
    public static function makeSign($params, $key, $mode = 1)
    {
        ksort($params);
        $query_string = '';
        foreach ($params as $k => $v) {
            if (0 < strlen($query_string)) {
                $query_string .= '&';
            }
            $query_string .= urlencode($k) . '=' . urlencode($v);
        }
        switch ($mode) {
            case 3:
                $sign = $key;
                break;
            case 2:
                $sign = $query_string == '' ? md5('key=' . $key) : md5($query_string . '&key=' . $key);
                break;
            case 1:
            default:
                $sign = md5($query_string . $key);
                break;
        }
        return $sign;
    }
}
class JieqiApiRequest
{
    public $_http_header = array();
    public $_useragent = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)';
    public $_http_code;
    public $connecttimeout = 30;
    public $timeout = 30;
    public $ssl_verifypeer = false;
    public $_debug = false;
    public function httpRequest($url, $params, $method = 'GET', $multi = false, $extheaders = array())
    {
        if (function_exists('curl_init')) {
            if (is_array($url)) {
                return $this->curl_muti($url, $params, $method, $multi, $extheaders);
            } else {
                return $this->curl_http($url, $params, $method, $multi, $extheaders);
            }
        } else {
            if (is_array($url)) {
                return $this->socket_muti($url, $params, $method, $multi, $extheaders);
            } else {
                return $this->socket_http($url, $params, $method, $multi, $extheaders);
            }
        }
    }
    public function curl_http($url, $params, $method = 'GET', $multi = false, $extheaders = array())
    {
        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, false);
        $headers = array();
        foreach ($extheaders as $k => $v) {
            $headers[] = $k . ': ' . $v;
        }
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($params)) {
                    if ($multi) {
                        foreach ($multi as $key => $file) {
                            $params[$key] = '@' . $file;
                        }
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                        $headers[] = 'Expect: ';
                    } else {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $this->makeQueryString($params));
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? $this->makeQueryString($params) : $params);
                }
                break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        curl_setopt($ci, CURLOPT_URL, $url);
        if ($headers) {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        }
        $ret = curl_exec($ci);
        $err = curl_error($ci);
        $this->_http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        if ($this->_debug) {
            echo 'Http Code ';
            echo $this->_http_code;
            echo "\r\n";
            echo "\r\n";
            echo $ret;
            echo "\r\n";
        }
        if (false === $ret || !empty($err)) {
            $errno = curl_errno($ci);
            $info = curl_getinfo($ci);
            curl_close($ci);
            return array('ret' => -1, 'msg' => $err, 'errno' => $errno, 'info' => $info);
        }
        curl_close($ci);
        return array('ret' => 0, 'msg' => $ret);
    }
    public function curl_muti($url, $params, $method = 'GET', $multi = false, $extheaders = array())
    {
        $method = strtoupper($method);
        $queue = curl_multi_init();
        $map = array();
        foreach ($url as $k => $u) {
            $ci = curl_init();
            curl_setopt($ci, CURLOPT_USERAGENT, $this->_useragent);
            curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
            curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
            curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
            curl_setopt($ci, CURLOPT_HEADER, false);
            curl_setopt($ci, CURLOPT_NOSIGNAL, true);
            $headers = array();
            foreach ($extheaders as $k => $v) {
                $headers[] = $k . ': ' . $v;
            }
            switch ($method) {
                case 'POST':
                    curl_setopt($ci, CURLOPT_POST, true);
                    if (!empty($params[$k])) {
                        if (is_array($multi[$k])) {
                            foreach ($multi[$k] as $key => $file) {
                                $params[$k][$key] = '@' . $file;
                            }
                            curl_setopt($ci, CURLOPT_POSTFIELDS, $params[$k]);
                            $headers[] = 'Expect: ';
                        } else {
                            curl_setopt($ci, CURLOPT_POSTFIELDS, $this->makeQueryString($params[$k]));
                        }
                    }
                    break;
                case 'DELETE':
                case 'GET':
                    $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    if (!empty($params[$k])) {
                        $url[$k] = $url[$k] . (strpos($url[$k], '?') ? '&' : '?') . (is_array($params[$k]) ? $this->makeQueryString($params[$k]) : $params[$k]);
                    }
                    break;
            }
            curl_setopt($ci, CURLINFO_HEADER_OUT, true);
            curl_setopt($ci, CURLOPT_URL, $url[$k]);
            if ($headers) {
                curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
            }
            curl_multi_add_handle($queue, $ci);
            $map[(string) $ci] = $k;
        }
        $responses = array();
        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) {
            }
            if ($code != CURLM_OK) {
                break;
            }
            while ($done = curl_multi_info_read($queue)) {
                $ret = curl_error($done['handle']);
                $msg = curl_multi_getcontent($done['handle']);
                if (false === $msg || !empty($ret)) {
                    $msg = $ret;
                    $ret = -1;
                } else {
                    $ret = 0;
                }
                $responses[$map[(string) $done['handle']]] = compact('ret', 'msg');
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }
            if (0 < $active) {
                curl_multi_select($queue, 0.5);
            }
        } while ($active);
        curl_multi_close($queue);
        return $responses;
    }
    public function getHeader($ci, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->_http_header[$key] = $value;
        }
        return strlen($header);
    }
    public function socket_http($url, $params, $method = 'GET', $multi = false, $extheaders = array())
    {
        include_once JIEQI_ROOT_PATH . '/lib/net/client.php';
        $client = new JieqiClient();
        $client->enableHistory(false);
        $client->setRequestParameter('timeout', $this->connecttimeout);
        $client->setRequestParameter('readTimeout', $this->timeout);
        $client->setDefaultHeader('User-Agent', $this->_useragent);
        if (!empty($extheaders)) {
            $client->setDefaultHeader($extheaders);
        }
        if (strtoupper($method) == 'GET') {
            $client->get($url, $params);
        } else {
            if (is_array($multi) && !empty($multi)) {
                $client->post($url, $params, false, $multi);
            } else {
                $client->post($url, $params);
            }
        }
        $res = $client->currentResponse();
        if ($res['code'] == '200') {
            return array('ret' => 0, 'msg' => $res['body']);
        } else {
            return array('ret' => 0, 'msg' => 'HTTP Status Code: ' . $res['code']);
        }
    }
    public function socket_muti($url, $params, $method = 'GET', $multi = false, $extheaders = array())
    {
        $responses = array();
        foreach ($url as $k => $u) {
            if (is_array($multi[$k])) {
                $mul = $multi[$k];
            } else {
                $mul = $multi;
            }
            $responses[$k] = socket_http($url[$k], $params[$k], $method, $mul, $extheaders);
        }
    }
    public function get_file_mime($file)
    {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $mime = 'image/jpg';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'html':
            case 'htm':
                $mime = 'text/html';
                break;
            case 'txt':
                $mime = 'text/plain';
                break;
            case 'zip':
                $mime = 'multipart/x-zip';
                break;
            case 'gz':
                $mime = 'multipart/x-gzip';
                break;
            default:
                $mime = 'application/x-octet-stream';
                break;
        }
        return $mime;
    }
    public function getHttpCode()
    {
        return $this->_http_code;
    }
    public function fwrite($handle, $data)
    {
        fwrite($handle, $data);
        if ($this->_debug) {
            echo $data;
        }
    }
    public static function makeQueryString($params)
    {
        if (is_string($params)) {
            return $params;
        }
        if (function_exists('http_build_query')) {
            return http_build_query($params);
        }
        $query_string = array();
        foreach ($params as $key => $value) {
            array_push($query_string, rawurlencode($key) . '=' . rawurlencode($value));
        }
        $query_string = join('&', $query_string);
        return $query_string;
    }
}
if (!function_exists('json_decode')) {
    function json_encode($value)
    {
        $jsonObj = new Services_JSON();
        return $json->encode($value);
    }
    function json_decode($json, $assoc = NULL)
    {
        if ($assoc) {
            $jsonObj = new Services_JSON(16);
        } else {
            $jsonObj = new Services_JSON();
        }
        return $jsonObj->decode($json);
    }
    include_once JIEQI_ROOT_PATH . '/lib/Services/JSON.php';
}