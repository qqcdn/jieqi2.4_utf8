<?php

class OpenSDK_Tencent_Wxmp extends OpenSDK_OAuth_Interface
{
    const ACCESS_TOKEN = 'wxmp_access_token';
    const REFRESH_TOKEN = 'wxmp_refresh_token';
    const EXPIRES_IN = 'wxmp_expires_in';
    const SCOPE = 'wxmp_scope';
    const OPENID = 'wxmp_openid';
    const UNIONID = 'wxmp_unionid';
    /**
     * app key
     * @var string
     */
    protected static $appid = '';
    /**
     * app secret
     * @var string
     */
    protected static $appsecret = '';
    /**
     * OAuth 对象
     * @var OpenSDK_OAuth_Client
     */
    private static $oauth;
    private static $accessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    private static $authorizeURL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    /**
     * OAuth 版本
     * @var string
     */
    protected static $version = '2.0';
    protected static $_debug = false;
    public static function init($appkey, $appsecret)
    {
        self::$appid = $appkey;
        self::$appsecret = $appsecret;
    }
    public static function getAuthorizeURL($url, $response_type, $state, $scope = 'snsapi_login')
    {
        $params = array();
        $params['appid'] = self::$appid;
        $params['redirect_uri'] = $url;
        $params['response_type'] = $response_type;
        $params['scope'] = $scope;
        $params['state'] = $state;
        return self::$authorizeURL . '?' . http_build_query($params) . '#wechat_redirect';
    }
    public static function getAccessToken($type, $keys)
    {
        $params = array();
        $params['appid'] = self::$appid;
        $params['secret'] = self::$appsecret;
        if ($type === 'token') {
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $keys['refresh_token'];
        } else {
            if ($type === 'code') {
                $params['grant_type'] = 'authorization_code';
                $params['code'] = $keys['code'];
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
        if (is_array($token) && !isset($token['errmsg'])) {
            self::setParam(self::ACCESS_TOKEN, $token['access_token']);
            self::setParam(self::REFRESH_TOKEN, $token['refresh_token']);
            self::setParam(self::EXPIRES_IN, $token['expires_in']);
            self::setParam(self::OPENID, $token['openid']);
            self::setParam(self::SCOPE, $token['scope']);
            if (!empty($token['unionid'])) {
                self::setParam(self::UNIONID, $token['unionid']);
            }
        } else {
            exit('get access token failed.' . $token['errmsg']);
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
        $params['openid'] = self::getParam(self::OPENID);
        $response = self::request('https://api.weixin.qq.com/' . ltrim($command, '/'), $method, $params, $multi);
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
        if (!self::$appid || !self::$appsecret) {
            exit('app key or app secret not init');
        }
        return self::getOAuth()->request($url, $method, $params, $multi);
    }
}
require_once dirname(__DIR__) . '/OAuth2/Client.php';
require_once dirname(__DIR__) . '/OAuth/Interface.php';