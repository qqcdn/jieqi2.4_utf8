<?php

class JieqiRequest extends JieqiObject
{
    public $_url;
    public $_method;
    public $_http;
    public $_requestHeaders;
    public $_user;
    public $_pass;
    public $_sock;
    public $_proxy_host;
    public $_proxy_port;
    public $_proxy_user;
    public $_proxy_pass;
    public $_postData;
    public $_postFiles = array();
    public $_timeout;
    public $_response;
    public $_allowRedirects;
    public $_maxRedirects;
    public $_redirects;
    public $_useBrackets = true;
    public $_listeners = array();
    public $_saveBody = true;
    public $_readTimeout;
    public $_socketOptions;
    public function __construct($url = '', $params = array())
    {
        parent::__construct();
        $this->_sock = new JieqiSocket();
        $this->_method = HTTP_REQUEST_METHOD_GET;
        $this->_http = HTTP_REQUEST_HTTP_VER_1_1;
        $this->_requestHeaders = array();
        $this->_postData = NULL;
        $this->_user = NULL;
        $this->_pass = NULL;
        $this->_proxy_host = NULL;
        $this->_proxy_port = NULL;
        $this->_proxy_user = NULL;
        $this->_proxy_pass = NULL;
        $this->_allowRedirects = false;
        $this->_maxRedirects = 3;
        $this->_redirects = 0;
        $this->_timeout = NULL;
        $this->_response = NULL;
        foreach ($params as $key => $value) {
            $this->{'_' . $key} = $value;
        }
        if (!empty($url)) {
            $this->setURL($url);
        }
        if (empty($this->_requestHeaders['User-Agent'])) {
            $this->addHeader('User-Agent', 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)');
        }
        $this->addHeader('Connection', 'close');
        if (!empty($this->_user)) {
            $this->addHeader('Authorization', 'Basic ' . base64_encode($this->_user . ':' . $this->_pass));
        }
        if (HTTP_REQUEST_HTTP_VER_1_1 == $this->_http && extension_loaded('zlib') && 0 == 2 & ini_get('mbstring.func_overload')) {
            $this->addHeader('Accept-Encoding', 'gzip');
        }
    }
    public function _generateHostHeader()
    {
        if ($this->_url->port != 80 && strcasecmp($this->_url->protocol, 'http') == 0) {
            $host = $this->_url->host . ':' . $this->_url->port;
        } else {
            if ($this->_url->port != 443 && strcasecmp($this->_url->protocol, 'https') == 0) {
                $host = $this->_url->host . ':' . $this->_url->port;
            } else {
                if ($this->_url->port == 443 && strcasecmp($this->_url->protocol, 'https') == 0 && strpos($this->_url->url, ':443') !== false) {
                    $host = $this->_url->host . ':' . $this->_url->port;
                } else {
                    $host = $this->_url->host;
                }
            }
        }
        return $host;
    }
    public function reset($url, $params = array())
    {
        $this->JieqiRequest($url, $params);
    }
    public function setURL($url)
    {
        $this->_url = new JieqiUrl($url, $this->_useBrackets);
        if (!empty($this->_url->user) || !empty($this->_url->pass)) {
            $this->setBasicAuth($this->_url->user, $this->_url->pass);
        }
        if (HTTP_REQUEST_HTTP_VER_1_1 == $this->_http) {
            $this->addHeader('Host', $this->_generateHostHeader());
        }
    }
    public function setProxy($host, $port = 8080, $user = NULL, $pass = NULL)
    {
        $this->_proxy_host = $host;
        $this->_proxy_port = $port;
        $this->_proxy_user = $user;
        $this->_proxy_pass = $pass;
        if (!empty($user)) {
            $this->addHeader('Proxy-Authorization', 'Basic ' . base64_encode($user . ':' . $pass));
        }
    }
    public function setBasicAuth($user, $pass)
    {
        $this->_user = $user;
        $this->_pass = $pass;
        $this->addHeader('Authorization', 'Basic ' . base64_encode($user . ':' . $pass));
    }
    public function setMethod($method)
    {
        $this->_method = $method;
    }
    public function setHttpVer($http)
    {
        $this->_http = $http;
    }
    public function addHeader($name, $value)
    {
        $this->_requestHeaders[$name] = $value;
    }
    public function removeHeader($name)
    {
        if (isset($this->_requestHeaders[$name])) {
            unset($this->_requestHeaders[$name]);
        }
    }
    public function addQueryString($name, $value, $preencoded = false)
    {
        $this->_url->addQueryString($name, $value, $preencoded);
    }
    public function addRawQueryString($querystring, $preencoded = true)
    {
        $this->_url->addRawQueryString($querystring, $preencoded);
    }
    public function addPostData($name, $value, $preencoded = false)
    {
        if ($preencoded) {
            $this->_postData[$name] = $value;
        } else {
            $this->_postData[$name] = $this->_arrayMapRecursive('urlencode', $value);
        }
    }
    public function _arrayMapRecursive($callback, $value)
    {
        if (!is_array($value)) {
            return call_user_func($callback, $value);
        } else {
            $map = array();
            foreach ($value as $k => $v) {
                $map[$k] = $this->_arrayMapRecursive($callback, $v);
            }
            return $map;
        }
    }
    public function addFile($inputName, $fileName, $contentType = 'application/octet-stream')
    {
        if (!is_array($fileName) && !is_readable($fileName)) {
            return $this->raiseError('file \'' . $fileName . '\' is not readable');
        } else {
            if (is_array($fileName)) {
                foreach ($fileName as $name) {
                    if (!is_readable($name)) {
                        return $this->raiseError('file \'' . $fileName . '\' is not readable');
                    }
                }
            }
        }
        $this->addHeader('Content-Type', 'multipart/form-data');
        $this->_postFiles[$inputName] = array('name' => $fileName, 'type' => $contentType);
        return true;
    }
    public function addRawPostData($postdata, $preencoded = true)
    {
        $this->_postData = $preencoded ? $postdata : urlencode($postdata);
    }
    public function clearPostData()
    {
        $this->_postData = NULL;
    }
    public function addCookie($name, $value)
    {
        $cookies = isset($this->_requestHeaders['Cookie']) ? $this->_requestHeaders['Cookie'] . '; ' : '';
        $this->addHeader('Cookie', $cookies . $name . '=' . $value);
    }
    public function clearCookies()
    {
        $this->removeHeader('Cookie');
    }
    public function sendRequest($saveBody = true)
    {
        if (!is_a($this->_url, 'JieqiUrl')) {
            return $this->raiseError('URL not set');
        }
        $host = isset($this->_proxy_host) ? $this->_proxy_host : $this->_url->host;
        $port = isset($this->_proxy_port) ? $this->_proxy_port : $this->_url->port;
        if (strcasecmp($this->_url->protocol, 'https') == 0 && function_exists('file_get_contents') && extension_loaded('openssl')) {
            if (isset($this->_proxy_host)) {
                return $this->raiseError('not support proxy');
            }
            $host = 'ssl://' . $host;
        }
        $err = $this->_sock->connect($host, $port, NULL, $this->_timeout, $this->_socketOptions);
        if ($err == false) {
            return false;
        }
        $err = $this->_sock->write($this->_buildRequest());
        if ($err == false) {
            return false;
        }
        if (!empty($this->_readTimeout)) {
            $this->_sock->setTimeout($this->_readTimeout[0], $this->_readTimeout[1]);
        }
        $this->_notify('sentRequest');
        $this->_response = new JieqiResponse($this->_sock, $this->_listeners);
        $err = $this->_response->process($this->_saveBody && $saveBody);
        if ($err == false) {
            return false;
        }
        if ($this->_allowRedirects && $this->_redirects <= $this->_maxRedirects && 300 < $this->getResponseCode() && $this->getResponseCode() < 399 && !empty($this->_response->_headers['location'])) {
            $redirect = $this->_response->_headers['location'];
            if (preg_match('/^https?:\\/\\//i', $redirect)) {
                $this->_url = new JieqiUrl($redirect);
                $this->addHeader('Host', $this->_generateHostHeader());
            } else {
                if ($redirect[0] == '/') {
                    $this->_url->path = $redirect;
                } else {
                    if (substr($redirect, 0, 3) == '../' || substr($redirect, 0, 2) == './') {
                        if (substr($this->_url->path, -1) == '/') {
                            $redirect = $this->_url->path . $redirect;
                        } else {
                            $redirect = dirname($this->_url->path) . '/' . $redirect;
                        }
                        $redirect = JieqiUrl::resolvePath($redirect);
                        $this->_url->path = $redirect;
                    } else {
                        if (substr($this->_url->path, -1) == '/') {
                            $redirect = $this->_url->path . $redirect;
                        } else {
                            $redirect = dirname($this->_url->path) . '/' . $redirect;
                        }
                        $this->_url->path = $redirect;
                    }
                }
            }
            $this->_redirects++;
            return $this->sendRequest($saveBody);
        } else {
            if ($this->_allowRedirects && $this->_maxRedirects < $this->_redirects) {
                $this->raiseError('too many redirects', JIEQI_ERROR_RETURN);
                return false;
            }
        }
        $this->_sock->disconnect();
        return true;
    }
    public function getResponseCode()
    {
        return isset($this->_response->_code) ? $this->_response->_code : false;
    }
    public function getResponseHeader($headername = NULL)
    {
        if (!isset($headername)) {
            return isset($this->_response->_headers) ? $this->_response->_headers : array();
        } else {
            return isset($this->_response->_headers[$headername]) ? $this->_response->_headers[$headername] : false;
        }
    }
    public function getResponseBody()
    {
        return isset($this->_response->_body) ? $this->_response->_body : false;
    }
    public function getResponseCookies()
    {
        return isset($this->_response->_cookies) ? $this->_response->_cookies : false;
    }
    public function _buildRequest()
    {
        $separator = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&');
        $querystring = $querystring = $this->_url->getQueryString() ? '?' . $querystring : '';
        ini_set('arg_separator.output', $separator);
        $host = isset($this->_proxy_host) ? $this->_url->protocol . '://' . $this->_url->host : '';
        $port = isset($this->_proxy_host) && $this->_url->port != 80 ? ':' . $this->_url->port : '';
        $path = (empty($this->_url->path) ? '/' : $this->_url->path) . $querystring;
        $url = $host . $port . $path;
        $request = $this->_method . ' ' . $url . ' HTTP/' . $this->_http . "\r\n";
        if (HTTP_REQUEST_METHOD_POST != $this->_method && HTTP_REQUEST_METHOD_PUT != $this->_method) {
            $this->removeHeader('Content-Type');
        } else {
            if (empty($this->_requestHeaders['Content-Type'])) {
                $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            } else {
                if ('multipart/form-data' == $this->_requestHeaders['Content-Type']) {
                    $boundary = 'JieqiRequest_' . md5(uniqid('request') . microtime());
                    $this->addHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary);
                }
            }
        }
        if (!empty($this->_requestHeaders)) {
            foreach ($this->_requestHeaders as $name => $value) {
                $request .= $name . ': ' . $value . "\r\n";
            }
        }
        if (HTTP_REQUEST_METHOD_POST != $this->_method && HTTP_REQUEST_METHOD_PUT != $this->_method || empty($this->_postData) && empty($this->_postFiles)) {
            $request .= "\r\n";
        } else {
            if (!empty($this->_postData) && is_array($this->_postData) || !empty($this->_postFiles)) {
                if (!isset($boundary)) {
                    $postdata = implode('&', array_map(create_function('$a', 'return $a[0] . \'=\' . $a[1];'), $this->_flattenArray('', $this->_postData)));
                } else {
                    $postdata = '';
                    if (!empty($this->_postData)) {
                        $flatData = $this->_flattenArray('', $this->_postData);
                        foreach ($flatData as $item) {
                            $postdata .= '--' . $boundary . "\r\n";
                            $postdata .= 'Content-Disposition: form-data; name="' . $item[0] . '"';
                            $postdata .= '' . "\r\n" . '' . "\r\n" . '' . urldecode($item[1]) . "\r\n";
                        }
                    }
                    foreach ($this->_postFiles as $name => $value) {
                        if (is_array($value['name'])) {
                            $varname = $name . ($this->_useBrackets ? '[]' : '');
                        } else {
                            $varname = $name;
                            $value['name'] = array($value['name']);
                        }
                        foreach ($value['name'] as $key => $filename) {
                            $fp = fopen($filename, 'r');
                            $data = fread($fp, filesize($filename));
                            fclose($fp);
                            $basename = basename($filename);
                            $type = is_array($value['type']) ? @'value'['type']['key'] : $value['type'];
                            $postdata .= '--' . $boundary . "\r\n";
                            $postdata .= 'Content-Disposition: form-data; name="' . $varname . '"; filename="' . $basename . '"';
                            $postdata .= '' . "\r\n" . 'Content-Type: ' . $type;
                            $postdata .= '' . "\r\n" . '' . "\r\n" . '' . $data . "\r\n";
                        }
                    }
                    $postdata .= '--' . $boundary . "\r\n";
                }
                $request .= 'Content-Length: ' . strlen($postdata) . '' . "\r\n" . '' . "\r\n" . '';
                $request .= $postdata;
            } else {
                if (!empty($this->_postData)) {
                    $request .= 'Content-Length: ' . strlen($this->_postData) . '' . "\r\n" . '' . "\r\n" . '';
                    $request .= $this->_postData;
                }
            }
        }
        return $request;
    }
    public function _flattenArray($name, $values)
    {
        if (!is_array($values)) {
            return array(array($name, $values));
        } else {
            $ret = array();
            foreach ($values as $k => $v) {
                if (empty($name)) {
                    $newName = $k;
                } else {
                    if ($this->_useBrackets) {
                        $newName = $name . '[' . $k . ']';
                    } else {
                        $newName = $name;
                    }
                }
                $ret = array_merge($ret, $this->_flattenArray($newName, $v));
            }
            return $ret;
        }
    }
    public function attach(&$listener)
    {
        if (!is_a($listener, 'JieqiRequest_Listener')) {
            return false;
        }
        $this->_listeners[$listener->getId()] =& $listener;
        return true;
    }
    public function detach(&$listener)
    {
        if (!is_a($listener, 'JieqiRequest_Listener') || !isset($this->_listeners[$listener->getId()])) {
            return false;
        }
        unset($this->_listeners[$listener->getId()]);
        return true;
    }
    public function _notify($event, $data = NULL)
    {
        foreach (array_keys($this->_listeners) as $id) {
            $this->_listeners[$id]->update($this, $event, $data);
        }
    }
}
class JieqiResponse extends JieqiObject
{
    public $_sock;
    public $_protocol;
    public $_code;
    public $_headers;
    public $_cookies;
    public $_body = '';
    public $_chunkLength = 0;
    public $_listeners = array();
    public function __construct(&$sock, &$listeners)
    {
        parent::__construct();
        $this->_sock =& $sock;
        $this->_listeners =& $listeners;
    }
    public function process($saveBody = true)
    {
        do {
            $line = $this->_sock->readLine();
            if (sscanf($line, 'HTTP/%s %s', $http_version, $returncode) != 2) {
                $this->raiseError('Malformed response.', JIEQI_ERROR_RETURN);
                return false;
            } else {
                $this->_protocol = 'HTTP/' . $http_version;
                $this->_code = intval($returncode);
            }
            while ('' !== ($header = $this->_sock->readLine())) {
                $this->_processHeader($header);
            }
        } while (100 == $this->_code);
        $this->_notify('gotHeaders', $this->_headers);
        $chunked = isset($this->_headers['transfer-encoding']) && 'chunked' == $this->_headers['transfer-encoding'];
        $gzipped = isset($this->_headers['content-encoding']) && 'gzip' == $this->_headers['content-encoding'];
        $hasBody = false;
        while (!$this->_sock->eof()) {
            if ($chunked) {
                $data = $this->_readChunked();
            } else {
                $data = $this->_sock->read(4096);
            }
            if ('' != $data) {
                $hasBody = true;
                if ($saveBody || $gzipped) {
                    $this->_body .= $data;
                }
                $this->_notify($gzipped ? 'gzTick' : 'tick', $data);
            }
        }
        if ($hasBody) {
            if ($gzipped) {
                $this->_body = gzinflate(substr($this->_body, 10));
                $this->_notify('gotBody', $this->_body);
            } else {
                $this->_notify('gotBody');
            }
        }
        return true;
    }
    public function _processHeader($header)
    {
        list($headername, $headervalue) = explode(':', $header, 2);
        $headername_i = strtolower($headername);
        $headervalue = ltrim($headervalue);
        if ('set-cookie' != $headername_i) {
            $this->_headers[$headername] = $headervalue;
            $this->_headers[$headername_i] = $headervalue;
        } else {
            $this->_parseCookie($headervalue);
        }
    }
    public function _parseCookie($headervalue)
    {
        $cookie = array('expires' => NULL, 'domain' => NULL, 'path' => NULL, 'secure' => false);
        if (!strpos($headervalue, ';')) {
            $pos = strpos($headervalue, '=');
            $cookie['name'] = trim(substr($headervalue, 0, $pos));
            $cookie['value'] = trim(substr($headervalue, $pos + 1));
        } else {
            $elements = explode(';', $headervalue);
            $pos = strpos($elements[0], '=');
            $cookie['name'] = trim(substr($elements[0], 0, $pos));
            $cookie['value'] = trim(substr($elements[0], $pos + 1));
            for ($i = 1; $i < count($elements); $i++) {
                list($elName, $elValue) = array_map('trim', explode('=', $elements[$i]));
                $elName = strtolower($elName);
                if ('secure' == $elName) {
                    $cookie['secure'] = true;
                } else {
                    if ('expires' == $elName) {
                        $cookie['expires'] = str_replace('"', '', $elValue);
                    } else {
                        if ('path' == $elName || 'domain' == $elName) {
                            $cookie[$elName] = urldecode($elValue);
                        } else {
                            $cookie[$elName] = $elValue;
                        }
                    }
                }
            }
        }
        $this->_cookies[] = $cookie;
    }
    public function _readChunked()
    {
        if (0 == $this->_chunkLength) {
            $line = $this->_sock->readLine();
            if (preg_match('/^([0-9a-f]+)/i', $line, $matches)) {
                $this->_chunkLength = hexdec($matches[1]);
                if (0 == $this->_chunkLength) {
                    $this->_sock->readAll();
                    return '';
                }
            }
        }
        $data = $this->_sock->read($this->_chunkLength);
        $this->_chunkLength -= strlen($data);
        if (0 == $this->_chunkLength) {
            $this->_sock->readLine();
        }
        return $data;
    }
    public function _notify($event, $data = NULL)
    {
        foreach (array_keys($this->_listeners) as $id) {
            $this->_listeners[$id]->update($this, $event, $data);
        }
    }
}
include_once 'socket.php';
include_once 'url.php';
define('HTTP_REQUEST_METHOD_GET', 'GET', true);
define('HTTP_REQUEST_METHOD_HEAD', 'HEAD', true);
define('HTTP_REQUEST_METHOD_POST', 'POST', true);
define('HTTP_REQUEST_METHOD_PUT', 'PUT', true);
define('HTTP_REQUEST_METHOD_DELETE', 'DELETE', true);
define('HTTP_REQUEST_METHOD_OPTIONS', 'OPTIONS', true);
define('HTTP_REQUEST_METHOD_TRACE', 'TRACE', true);
define('HTTP_REQUEST_HTTP_VER_1_0', '1.0', true);
define('HTTP_REQUEST_HTTP_VER_1_1', '1.1', true);