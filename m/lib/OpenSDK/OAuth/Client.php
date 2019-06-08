<?php

class OpenSDK_OAuth_Client
{
    /**
     * 签名的url标签
     * @var string
     */
    public $oauth_signature_key = 'oauth_signature';
    /**
     * app secret
     * @var string
     */
    protected $_app_secret = '';
    /**
     * token secret
     * @var string
     */
    protected $_token_secret = '';
    /**
     * 上一次请求返回的Httpcode
     * @var number
     */
    protected $_http_code;
    /**
     * 是否debug
     * @var bool
     */
    protected $_debug = false;
    protected $_http_header = array();
    protected $_useragent = 'JIEQICMS-OAuth1.0';
    protected $_http_info = array();
    public $connecttimeout = 3;
    public $timeout = 3;
    public $ssl_verifypeer = false;
    public function __construct($appsecret = '', $debug = false)
    {
        $this->_app_secret = $appsecret;
        $this->_debug = $debug;
    }
    public function setAppSecret($appsecret)
    {
        $this->_app_secret = $appsecret;
    }
    public function setTokenSecret($tokensecret)
    {
        $this->_token_secret = $tokensecret;
    }
    public function request($url, $method, $params, $multi = false, $extheaders = array())
    {
        $oauth_signature = $this->sign($url, $method, $params);
        $params[$this->oauth_signature_key] = $oauth_signature;
        return $this->http($url, $params, $method, $multi, $extheaders);
    }
    protected function sign($url, $method, $params)
    {
        uksort($params, 'strcmp');
        $pairs = array();
        foreach ($params as $key => $value) {
            $key = OpenSDK_Util::urlencode_rfc3986($key);
            if (is_array($value)) {
                natsort($value);
                foreach ($value as $duplicate_value) {
                    $pairs[] = $key . '=' . OpenSDK_Util::urlencode_rfc3986($duplicate_value);
                }
            } else {
                $pairs[] = $key . '=' . OpenSDK_Util::urlencode_rfc3986($value);
            }
        }
        $sign_parts = OpenSDK_Util::urlencode_rfc3986(implode('&', $pairs));
        $base_string = implode('&', array(strtoupper($method), OpenSDK_Util::urlencode_rfc3986($url), $sign_parts));
        $key_parts = array(OpenSDK_Util::urlencode_rfc3986($this->_app_secret), OpenSDK_Util::urlencode_rfc3986($this->_token_secret));
        $key = implode('&', $key_parts);
        $sign = base64_encode(OpenSDK_Util::hash_hmac('sha1', $base_string, $key, true));
        if ($this->_debug) {
            echo 'base_string: ';
            echo $base_string;
            echo "\n";
            echo 'sign key: ';
            echo $key;
            echo "\n";
            echo 'sign: ';
            echo $sign;
            echo "\n";
        }
        return $sign;
    }
    protected function http($url, $params, $method = 'GET', $multi = false, $extheaders = array())
    {
        if (function_exists('curl_init')) {
            return $this->curl_http($url, $params, $method, $multi, $extheaders);
        } else {
            return $this->socket_http($url, $params, $method, $multi, $extheaders);
        }
    }
    protected function curl_http($url, $params, $method = 'GET', $multi = false, $extheaders = array())
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
        $headers = (array) $extheaders;
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
                        curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        curl_setopt($ci, CURLOPT_URL, $url);
        if ($headers) {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($ci);
        $this->_http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->_http_info = array_merge($this->_http_info, curl_getinfo($ci));
        if ($this->_debug) {
            echo 'Http Code ';
            echo $this->_http_code;
            echo "\r\n";
            foreach ((array) $this->_http_info as $k => $v) {
                echo $k;
                echo ': ';
                echo $v;
                echo "\r\n";
            }
            echo "\r\n";
            echo $response;
            echo "\r\n";
        }
        curl_close($ci);
        return $response;
    }
    public function getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->_http_header[$key] = $value;
        }
        return strlen($header);
    }
    protected function socket_http($url, $params, $method = 'GET', $multi = false, $extheaders = array())
    {
        $method = strtoupper($method);
        $postdata = '';
        $urls = @parse_url($url);
        $httpurl = $urlpath = $urls['path'] . ($urls['query'] ? '?' . $urls['query'] : '');
        if (!$multi) {
            $parts = array();
            foreach ($params as $key => $val) {
                $parts[] = urlencode($key) . '=' . urlencode($val);
            }
            if ($parts) {
                $postdata = implode('&', $parts);
                $httpurl = $httpurl . (strpos($httpurl, '?') ? '&' : '?') . $postdata;
            }
        }
        $host = $urls['host'];
        $port = $urls['port'] ? $urls['port'] : 80;
        $version = '1.1';
        if ($urls['scheme'] === 'https') {
            $port = 443;
        }
        $headers = array();
        if ($method == 'GET') {
            $headers[] = 'GET ' . $httpurl . ' HTTP/' . $version;
        } else {
            if ($method == 'DELETE') {
                $headers[] = 'DELETE ' . $httpurl . ' HTTP/' . $version;
            } else {
                $headers[] = 'POST ' . $urlpath . ' HTTP/' . $version;
            }
        }
        $headers[] = 'Host: ' . $host;
        $headers[] = 'User-Agent: ' . $this->_useragent;
        foreach ((array) $extheaders as $head) {
            $headers[] = $head;
        }
        $headers[] = 'Connection: Close';
        if ($method == 'POST') {
            if ($multi) {
                $boundary = uniqid('------------------');
                $MPboundary = '--' . $boundary;
                $endMPboundary = $MPboundary . '--';
                $multipartbody = '';
                $headers[] = 'Content-Type: multipart/form-data; boundary=' . $boundary;
                foreach ($params as $key => $val) {
                    $multipartbody .= $MPboundary . "\r\n";
                    $multipartbody .= 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n" . '' . "\r\n" . '';
                    $multipartbody .= $val . "\r\n";
                }
                foreach ($multi as $key => $path) {
                    $multipartbody .= $MPboundary . "\r\n";
                    $multipartbody .= 'Content-Disposition: form-data; name="' . $key . '"; filename="' . pathinfo($path, PATHINFO_BASENAME) . '"' . "\r\n";
                    $multipartbody .= 'Content-Type: ' . self::get_image_mime($path) . '' . "\r\n" . '' . "\r\n" . '';
                    $multipartbody .= file_get_contents($path) . "\r\n";
                }
                $multipartbody .= $endMPboundary . "\r\n";
                $postdata = $multipartbody;
            } else {
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            }
        }
        $ret = '';
        $fp = fsockopen($host, $port, $errno, $errstr, 5);
        if (!$fp) {
            $error = 'Open Socket Error';
            return '';
        } else {
            if ($method != 'GET' && $postdata) {
                $headers[] = 'Content-Length: ' . strlen($postdata);
            }
            $this->fwrite($fp, implode("\r\n", $headers));
            $this->fwrite($fp, '' . "\r\n" . '' . "\r\n" . '');
            if ($method != 'GET' && $postdata) {
                $this->fwrite($fp, $postdata);
            }
            while (!feof($fp)) {
                $ret .= fgets($fp, 1024);
            }
            if ($this->_debug) {
                echo $ret;
            }
            fclose($fp);
            $pos = strpos($ret, '' . "\r\n" . '' . "\r\n" . '');
            if ($pos) {
                $rt = trim(substr($ret, $pos + 1));
                $responseHead = trim(substr($ret, 0, $pos));
                $responseHeads = explode("\r\n", $responseHead);
                $httpcode = explode(' ', $responseHeads[0]);
                $this->_http_code = $httpcode[1];
                if (strpos(substr($ret, 0, $pos), 'Transfer-Encoding: chunked')) {
                    $response = explode("\r\n", $rt);
                    $t = array_slice($response, 1, -1);
                    return implode('', $t);
                }
                return $rt;
            }
            return '';
        }
    }
    public static function get_image_mime($file)
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
            default:
                $mime = 'image/gif';
                break;
        }
        return $mime;
    }
    public function getHttpCode()
    {
        return $this->_http_code;
    }
    protected function fwrite($handle, $data)
    {
        fwrite($handle, $data);
        if ($this->_debug) {
            echo $data;
        }
    }
}
require_once dirname(__DIR__) . '/Util.php';