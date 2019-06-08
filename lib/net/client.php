<?php

class JieqiClient extends JieqiObject
{
    public $_cookieManager;
    public $_responses = array();
    public $_defaultHeaders = array();
    public $_defaultRequestParams = array();
    public $_redirectCount = 0;
    public $_maxRedirects = 5;
    public $_listeners = array();
    public $_propagate = array();
    public $_isHistoryEnabled = false;
    public $_resErrno = 0;
    public function __construct($defaultRequestParams = NULL, $defaultHeaders = NULL, $defaultCookies = NULL)
    {
        parent::__construct();
        $this->_cookieManager = new JieqiClient_CookieManager();
        if (isset($defaultHeaders)) {
            $this->setDefaultHeader($defaultHeaders);
        }
        if (isset($defaultRequestParams)) {
            $this->setRequestParameter($defaultRequestParams);
        }
        if (isset($defaultCookies)) {
            $this->setDefaultCookies($defaultCookies);
        }
    }
    public function setMaxRedirects($value)
    {
        $this->_maxRedirects = $value;
    }
    public function enableHistory($enable)
    {
        $this->_isHistoryEnabled = (bool) $enable;
    }
    public function _createRequest($url, $method = HTTP_REQUEST_METHOD_GET)
    {
        $req = new JieqiRequest($url, $this->_defaultRequestParams);
        $req->setMethod($method);
        foreach ($this->_defaultHeaders as $name => $value) {
            $req->addHeader($name, $value);
        }
        $this->_cookieManager->passCookies($req);
        foreach ($this->_propagate as $id => $propagate) {
            if ($propagate) {
                $req->attach($this->_listeners[$id]);
            }
        }
        return $req;
    }
    public function head($url)
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_HEAD);
        return $this->_performRequest($request);
    }
    public function get($url, $data = NULL, $preEncoded = false)
    {
        $request =& $this->_createRequest($url);
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $request->addQueryString($name, $value, $preEncoded);
            }
        } else {
            if (isset($data)) {
                $request->addRawQueryString($data, $preEncoded);
            }
        }
        return $this->_performRequest($request);
    }
    public function post($url, $data, $preEncoded = false, $files = array())
    {
        $request =& $this->_createRequest($url, HTTP_REQUEST_METHOD_POST);
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $request->addPostData($name, $value, $preEncoded);
            }
        } else {
            $request->addRawPostData($data, $preEncoded);
        }
        foreach ($files as $fileData) {
            $res = call_user_func_array(array(&$request, 'addFile'), $fileData);
            if ($res == false) {
                return $res;
            }
        }
        return $this->_performRequest($request);
    }
    public function setDefaultHeader($name, $value = NULL)
    {
        if (is_array($name)) {
            $this->_defaultHeaders = array_merge($this->_defaultHeaders, $name);
        } else {
            $this->_defaultHeaders[$name] = $value;
        }
    }
    public function setRequestParameter($name, $value = NULL)
    {
        if (is_array($name)) {
            $this->_defaultRequestParams = array_merge($this->_defaultRequestParams, $name);
        } else {
            $this->_defaultRequestParams[$name] = $value;
        }
    }
    public function setDefaultCookies($defaultCookies)
    {
        $this->_cookieManager->setCookies($defaultCookies);
    }
    public function getDefaultCookies()
    {
        return $this->_cookieManager->getCookies();
    }
    public function _performRequest(&$request)
    {
        if (0 == $this->_redirectCount) {
            $this->_notify('request', $request->_url->getUrl());
        }
        $err = $request->sendRequest();
        if ($err == false) {
            $this->_resErrno = $request->_resErrno;
            return false;
        }
        $this->_pushResponse($request);
        $code = $request->getResponseCode();
        if (0 < $this->_maxRedirects && in_array($code, array(300, 301, 302, 303, 307))) {
            if ($this->_maxRedirects < ++$this->_redirectCount) {
                $this->raiseError('too many redirects!', JIEQI_ERROR_RETURN);
                return false;
            }
            $location = $request->getResponseHeader('Location');
            if ('' == $location) {
                $this->raiseError('error redirects url!', JIEQI_ERROR_RETURN);
                return false;
            }
            $url = $this->_redirectUrl($request->_url, $location);
            $this->_notify('httpRedirect', $url);
            switch ($request->_method) {
                case HTTP_REQUEST_METHOD_POST:
                    if (302 == $code || 303 == $code) {
                        return $this->get($url);
                    } else {
                        $postFiles = array();
                        foreach ($request->_postFiles as $name => $data) {
                            $postFiles[] = array($name, $data['name'], $data['type']);
                        }
                        return $this->post($url, $request->_postData, true, $postFiles);
                    }
                case HTTP_REQUEST_METHOD_HEAD:
                    return 303 == $code ? $this->get($url) : $this->head($url);
                case HTTP_REQUEST_METHOD_GET:
                default:
                    return $this->get($url);
            }
        } else {
            $this->_redirectCount = 0;
            if ($code <= 400) {
                $this->_notify('httpSuccess');
                if (!preg_match('/\\.(gif|jpg|jpeg|png|bmp|swf|css|js|zip|rar|gz|tgz|pdf|umd|jar)$/is', $request->_url->path)) {
                    $this->setDefaultHeader('Referer', $request->_url->getUrl());
                }
            } else {
                $this->_notify('httpError');
            }
        }
        return $code;
    }
    public function currentResponse()
    {
        if (0 < count($this->_responses)) {
            return $this->_responses[count($this->_responses) - 1];
        } else {
            return false;
        }
    }
    public function _pushResponse(&$request)
    {
        $this->_cookieManager->updateCookies($request);
        $idx = $this->_isHistoryEnabled ? count($this->_responses) : 0;
        if ($idx < 0) {
            $this->_responses = array();
            $idx = 0;
        }
        $this->_responses[$idx] = array('code' => $request->getResponseCode(), 'headers' => $request->getResponseHeader(), 'body' => $request->getResponseBody());
    }
    public function reset()
    {
        $this->_cookieManager->reset();
        $this->_responses = array();
        $this->_defaultHeaders = array();
        $this->_defaultRequestParams = array();
    }
    public function attach(&$listener, $propagate = false)
    {
        if (!is_a($listener, 'JieqiRequest_Listener')) {
            return false;
        }
        $this->_listeners[$listener->getId()] =& $listener;
        $this->_propagate[$listener->getId()] = $propagate;
        return true;
    }
    public function detach(&$listener)
    {
        if (!is_a($listener, 'JieqiRequest_Listener') || !isset($this->_listeners[$listener->getId()])) {
            return false;
        }
        unset($this->_listeners[$listener->getId()]);
        unset($this->_propagate[$listener->getId()]);
        return true;
    }
    public function _notify($event, $data = NULL)
    {
        foreach (array_keys($this->_listeners) as $id) {
            $this->_listeners[$id]->update($this, $event, $data);
        }
    }
    public function _redirectUrl($url, $location)
    {
        if (preg_match('!^https?://!i', $location)) {
            return $location;
        } else {
            if ('/' == $location[0]) {
                $url->path = JieqiUrl::resolvePath($location);
            } else {
                if ('/' == substr($url->path, -1)) {
                    $url->path = JieqiUrl::resolvePath($url->path . $location);
                } else {
                    $dirname = DIRECTORY_SEPARATOR == dirname($url->path) ? '/' : dirname($url->path);
                    $url->path = JieqiUrl::resolvePath($dirname . '/' . $location);
                }
            }
            $url->querystring = array();
            $url->anchor = '';
            return $url->getUrl();
        }
    }
}
require_once 'request.php';
require_once 'cookiemanager.php';