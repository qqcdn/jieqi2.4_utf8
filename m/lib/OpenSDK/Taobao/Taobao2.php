<?php

class OpenSDK_Taobao_Oauth2 extends OpenSDK_OAuth_Interface
{
    const ACCESS_TOKEN = 'taobao2_access_token';
    const REFRESH_TOKEN = 'taobao2_refresh_token';
    const EXPIRES_IN = 'taobao2_expires_in';
    const OAUTH_USER_ID = 'taobao_user_id';
    const OAUTH_USER_NICK = 'taobao_user_nick';
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
    private static $accessTokenURL = 'https://oauth.taobao.com/token';
    private static $authorizeURL = 'https://oauth.taobao.com/authorize';
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
    public static function getAuthorizeURL($url, $response_type, $state, $view = 'web')
    {
        $params = array();
        $params['client_id'] = self::$client_id;
        $params['redirect_uri'] = $url;
        $params['response_type'] = $response_type;
        $params['state'] = $state;
        $params['view'] = $view;
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
            self::setParam(self::OAUTH_USER_ID, $token['taobao_user_id']);
            self::setParam(self::OAUTH_USER_NICK, $token['taobao_user_nick']);
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
        $response = self::request('https://gw.api.tbsandbox.com/' . ltrim($command, '/') . '.' . $format, $method, $params, $multi);
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