<?php

class OpenSDK_Sina_Weibo extends OpenSDK_OAuth_Interface
{
    const OAUTH_TOKEN = 'sina_oauth_token';
    const OAUTH_TOKEN_SECRET = 'sina_oauth_token_secret';
    const ACCESS_TOKEN = 'sina_access_token';
    const OAUTH_SCREEN_NAME = 'sina_screen_name';
    const OAUTH_USER_ID = 'sina_user_id';
    /**
     * app key
     * @var string
     */
    protected static $_appkey = '';
    /**
     * app secret
     * @var string
     */
    protected static $_appsecret = '';
    /**
     * OAuth 对象
     * @var OpenSDK_OAuth_Client
     */
    private static $oauth;
    private static $accessTokenURL = 'http://api.t.sina.com.cn/oauth/access_token';
    private static $authorizeURL = 'http://api.t.sina.com.cn/oauth/authorize';
    private static $requestTokenURL = 'http://api.t.sina.com.cn/oauth/request_token';
    /**
     * OAuth 版本
     * @var string
     */
    protected static $version = '1.0a';
    protected static $_debug = false;
    public static function init($appkey, $appsecret)
    {
        self::$_appkey = $appkey;
        self::$_appsecret = $appsecret;
    }
    public static function getRequestToken($callback = 'null')
    {
        self::getOAuth()->setTokenSecret('');
        $response = self::request(self::$requestTokenURL, 'GET', array('oauth_callback' => $callback));
        parse_str($response, $rt);
        if ($rt['oauth_token'] && $rt['oauth_token_secret']) {
            self::getOAuth()->setTokenSecret($rt['oauth_token_secret']);
            self::setParam(self::OAUTH_TOKEN, $rt['oauth_token']);
            self::setParam(self::OAUTH_TOKEN_SECRET, $rt['oauth_token_secret']);
            return $rt;
        } else {
            return false;
        }
    }
    public static function getAuthorizeURL($token)
    {
        if (is_array($token)) {
            $token = $token['oauth_token'];
        }
        return self::$authorizeURL . '?oauth_token=' . $token;
    }
    public static function getAccessToken($oauth_verifier = false)
    {
        $response = self::request(self::$accessTokenURL, 'GET', array('oauth_token' => self::getParam(self::OAUTH_TOKEN), 'oauth_verifier' => $oauth_verifier));
        parse_str($response, $rt);
        if ($rt['oauth_token'] && $rt['oauth_token_secret']) {
            self::getOAuth()->setTokenSecret($rt['oauth_token_secret']);
            self::setParam(self::ACCESS_TOKEN, $rt['oauth_token']);
            self::setParam(self::OAUTH_TOKEN_SECRET, $rt['oauth_token_secret']);
            self::setParam(self::OAUTH_SCREEN_NAME, $rt['screen_name']);
            self::setParam(self::OAUTH_USER_ID, $rt['user_id']);
            return $rt;
        }
        return false;
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
        $params['oauth_token'] = self::getParam(self::ACCESS_TOKEN);
        $response = self::request('http://api.t.sina.com.cn/' . ltrim($command, '/') . '.' . $format, $method, $params, $multi);
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
    public static function clearOauth()
    {
        self::$oauth = NULL;
    }
    public static function debug($debug = false)
    {
        self::$_debug = $debug;
    }
    protected static function getOAuth()
    {
        if (NULL === self::$oauth) {
            self::$oauth = new OpenSDK_OAuth_Client(self::$_appsecret, self::$_debug);
            $secret = self::getParam(self::OAUTH_TOKEN_SECRET);
            if ($secret) {
                self::$oauth->setTokenSecret($secret);
            }
        }
        return self::$oauth;
    }
    protected static function request($url, $method, $params, $multi = false)
    {
        if (!self::$_appkey || !self::$_appsecret) {
            exit('app key or app secret not init');
        }
        $params['oauth_nonce'] = md5(mt_rand(1, 100000) . microtime(true));
        $params['oauth_consumer_key'] = self::$_appkey;
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_version'] = self::$version;
        $params['oauth_timestamp'] = self::getTimestamp();
        $extheaders = array('API-RemoteIP: ' . self::getRemoteIp(), 'SaeRemoteIP: ' . self::getRemoteIp());
        return self::getOAuth()->request($url, $method, $params, $multi, $extheaders);
    }
}
require_once dirname(__DIR__) . '/OAuth/Client.php';
require_once dirname(__DIR__) . '/OAuth/Interface.php';