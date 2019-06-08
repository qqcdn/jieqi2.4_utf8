<?php

class OpenSDK_Tencent_SNS extends OpenSDK_OAuth_Interface
{
    const OAUTH_TOKEN = 'tensns_oauth_token';
    const OAUTH_TOKEN_SECRET = 'tensns_oauth_token_secret';
    const ACCESS_TOKEN = 'tensns_access_token';
    const OAUTH_OPENID = 'tensns_oauth_openid';
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
    private static $accessTokenURL = 'http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token';
    private static $authorizeURL = 'http://openapi.qzone.qq.com/oauth/qzoneoauth_authorize';
    private static $requestTokenURL = 'http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token';
    /**
     * OAuth 对象
     * @var OpenSDK_OAuth_Client
     */
    protected static $oauth;
    /**
     * OAuth 版本
     * @var string
     */
    protected static $version = '1.0';
    protected static $_debug = false;
    public static function init($appkey, $appsecret)
    {
        self::$_appkey = $appkey;
        self::$_appsecret = $appsecret;
    }
    public static function getRequestToken()
    {
        self::getOAuth()->setTokenSecret('');
        $response = self::request(self::$requestTokenURL, 'GET', array());
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
    public static function getAuthorizeURL($token, $callback)
    {
        if (is_array($token)) {
            $token = $token['oauth_token'];
        }
        return self::$authorizeURL . '?oauth_token=' . $token . '&oauth_consumer_key=' . self::$_appkey . '&oauth_callback=' . rawurlencode($callback);
    }
    public static function getAccessToken($oauth_verifier = false)
    {
        $response = self::request(self::$accessTokenURL, 'GET', array('oauth_token' => self::getParam(self::OAUTH_TOKEN), 'oauth_vericode' => $oauth_verifier));
        parse_str($response, $rt);
        if ($rt['oauth_token'] && $rt['oauth_token_secret']) {
            self::getOAuth()->setTokenSecret($rt['oauth_token_secret']);
            self::setParam(self::ACCESS_TOKEN, $rt['oauth_token']);
            self::setParam(self::OAUTH_TOKEN_SECRET, $rt['oauth_token_secret']);
            self::setParam(self::OAUTH_OPENID, $rt['openid']);
            return $rt;
        }
        return false;
    }
    public static function call($command, $params = array(), $method = 'GET', $multi = false, $decode = true, $format = self::RETURN_JSON)
    {
        if ($format == self::RETURN_XML) {
        } else {
            $format == self::RETURN_JSON;
        }
        $params['format'] = $format;
        foreach ($params as $key => $val) {
            if (strlen($val) == 0) {
                unset($params[$key]);
            }
        }
        $params['oauth_token'] = self::getParam(self::ACCESS_TOKEN);
        $response = self::request('http://openapi.qzone.qq.com/' . ltrim($command, '/'), $method, $params, $multi);
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
            self::$oauth = new OpenSDK_OAuth_QQSNSClient(self::$_appsecret, self::$_debug);
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
        $params['oauth_nonce'] = mt_rand();
        $params['oauth_consumer_key'] = self::$_appkey;
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_version'] = self::$version;
        $params['oauth_timestamp'] = self::getTimestamp();
        if ($openid = self::getParam(self::OAUTH_OPENID)) {
            $params['openid'] = $openid;
        }
        return self::getOAuth()->request($url, $method, $params, $multi);
    }
}
require_once dirname(__DIR__) . '/OAuth/Interface.php';
require_once dirname(__DIR__) . '/OAuth/QQSNSClient.php';