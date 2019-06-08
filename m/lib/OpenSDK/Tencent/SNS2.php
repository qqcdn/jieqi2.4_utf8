<?php

class OpenSDK_Tencent_SNS2 extends OpenSDK_OAuth_Interface
{
    const ACCESS_TOKEN = 'tensns2_access_token';
    const REFRESH_TOKEN = 'tensns2_refresh_token';
    const EXPIRES_IN = 'tensns2_expires_in';
    const SCOPE = 'tensns2_scope';
    const OPENID = 'tensns2_openid';
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
    private static $accessTokenURL = 'https://graph.qq.com/oauth2.0/token';
    private static $authorizeURL = 'https://graph.qq.com/oauth2.0/authorize';
    private static $openidURL = 'https://graph.qq.com/oauth2.0/me';
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
    public static function getAuthorizeURL($url, $response_type, $state, $ismobile = 0, $scope = '')
    {
        $params = array();
        $params['client_id'] = self::$client_id;
        $params['redirect_uri'] = $url;
        $params['response_type'] = $response_type;
        $params['state'] = $state;
        if ($ismobile) {
            $params['display'] = 'mobile';
            $params['g_ut'] = $ismobile;
        }
        $scope && ($params['scope'] = $scope);
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
        if (substr($response, 0, 9) == 'callback(') {
            $json = substr($response, 10, -3);
            $token = OpenSDK_Util::json_decode($json, true);
        } else {
            parse_str($response, $token);
        }
        if (is_array($token) && !isset($token['error'])) {
            self::setParam(self::ACCESS_TOKEN, $token['access_token']);
            self::setParam(self::REFRESH_TOKEN, $token['refresh_token']);
            self::setParam(self::EXPIRES_IN, $token['expires_in']);
            self::getOpenID();
        } else {
            exit('get access token failed.' . $token['error']);
        }
        return $token;
    }
    protected static function getOpenID()
    {
        $response = self::request(self::$openidURL, 'GET', array('access_token' => self::getParam(self::ACCESS_TOKEN)));
        $json = substr($response, 10, -3);
        $token = OpenSDK_Util::json_decode($json, true);
        if ($token['openid']) {
            self::setParam(self::OPENID, $token['openid']);
        }
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
        $params['oauth_consumer_key'] = self::$client_id;
        $params['openid'] = self::getParam(self::OPENID);
        $params['format'] = $format;
        $response = self::request('https://graph.qq.com/' . ltrim($command, '/'), $method, $params, $multi);
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
        return self::getOAuth()->request($url, $method, $params, $multi);
    }
}
require_once dirname(__DIR__) . '/OAuth2/Client.php';
require_once dirname(__DIR__) . '/OAuth/Interface.php';