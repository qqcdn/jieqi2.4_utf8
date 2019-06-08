<?php

class OpenSDK_Sina_Weibo2 extends OpenSDK_OAuth_Interface
{
    const ACCESS_TOKEN = 'sina2_access_token';
    const REFRESH_TOKEN = 'sina2_refresh_token';
    const EXPIRES_IN = 'sina2_expires_in';
    const OAUTH_USER_ID = 'sina_openid';
    /**
     * app key
     * @var string
     */
    protected static $client_id = '';
    /**
     * app secret
     * @var string
     */
    protected static $client_secret = '';
    /**
     * OAuth 对象
     * @var OpenSDK_OAuth_Client
     */
    private static $oauth;
    private static $accessTokenURL = 'https://api.weibo.com/oauth2/access_token';
    private static $authorizeURL = 'https://api.weibo.com/oauth2/authorize';
    /**
     * OAuth 版本
     * @var string
     */
    protected static $version = '2.0';
    protected static $_debug = false;
    public static function init($appkey, $appsecret)
    {
        self::$client_id = $appkey;
        self::$client_secret = $appsecret;
    }
    public static function getAuthorizeURL($url, $response_type, $state, $display = 'default')
    {
        $params = array();
        $params['client_id'] = self::$client_id;
        $params['redirect_uri'] = $url;
        $params['response_type'] = $response_type;
        $params['state'] = $state;
        $params['display'] = $display;
        return self::$authorizeURL . '?' . http_build_query($params);
    }
    public static function getAccessToken($type, $keys)
    {
        $params = array();
        $params['client_id'] = self::$client_id;
        $params['client_secret'] = self::$client_secret;
        if ($type === 'token') {
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $keys['refresh_token'];
        } else {
            if ($type === 'code') {
                $params['grant_type'] = 'authorization_code';
                $params['code'] = $keys['code'];
                $params['redirect_uri'] = $keys['redirect_uri'];
            } else {
                if ($type === 'password') {
                    $params['grant_type'] = 'password';
                    $params['username'] = $keys['username'];
                    $params['password'] = $keys['password'];
                } else {
                    exit('wrong auth type');
                }
            }
        }
        $response = self::request(self::$accessTokenURL, 'POST', $params);
        $token = OpenSDK_Util::json_decode($response, true);
        if (is_array($token) && !isset($token['error'])) {
            self::setParam(self::ACCESS_TOKEN, $token['access_token']);
            self::setParam(self::REFRESH_TOKEN, $token['refresh_token']);
            self::setParam(self::EXPIRES_IN, $token['expires_in']);
            self::setParam(self::OAUTH_USER_ID, $token['uid']);
        } else {
            exit('get access token failed.' . $token['error']);
        }
        return $token;
    }
    public static function call($command, $params = array(), $method = 'GET', $multi = false, $decode = true, $format = 'json')
    {
        if ($format == self::RETURN_XML) {
        } else {
            $format == self::RETURN_JSON;
        }
        foreach ($params as $key => $val) {
            if (strlen($val) == 0) {
                unset($params[$key]);
            }
        }
        $params['access_token'] = self::getParam(self::ACCESS_TOKEN);
        $params['source'] = self::$client_id;
        $response = self::request('https://api.weibo.com/2/' . ltrim($command, '/') . '.' . $format, $method, $params, $multi);
        if ($decode) {
            if ($format == self::RETURN_JSON) {
                return OpenSDK_Util::json_decode($response, true);
            } else {
                return $response;
            }
        } else {
            return $response;
        }
    }
    public static function debug($debug = false)
    {
        self::$_debug = $debug;
    }
    protected static function getOAuth()
    {
        if (NULL === self::$oauth) {
            self::$oauth = new OpenSDK_OAuth2_Client(self::$_debug);
        }
        return self::$oauth;
    }
    protected static function request($url, $method, $params, $multi = false)
    {
        if (!self::$client_id || !self::$client_secret) {
            exit('app key or app secret not init');
        }
        $headers = array('API-RemoteIP: ' . self::getRemoteIp());
        return self::getOAuth()->request($url, $method, $params, $multi, $headers);
    }
}
require_once dirname(__DIR__) . '/OAuth2/Client.php';
require_once dirname(__DIR__) . '/OAuth/Interface.php';