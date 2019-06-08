<?php

class OpenSDK_OAuth_QQSNSClient extends OpenSDK_OAuth_Client
{
    public function request($url, $method, $params, $multi = false)
    {
        $oauth_signature = $this->sign($url, $method, $params, $multi);
        $params[$this->oauth_signature_key] = $oauth_signature;
        return $this->http($url, $params, $method, $multi);
    }
    protected function sign($url, $method, $params, $multi)
    {
        if ($multi && is_array($multi)) {
            foreach ($multi as $field => $path) {
                $params[$field] = file_get_contents($path);
            }
            uksort($params, 'strcmp');
            $pairs = array();
            foreach ($params as $key => $value) {
                $pairs[] = $key . '=' . $value;
            }
            $sign_parts = implode('&', $pairs);
            $base_string = implode('&', array(strtoupper($method), $url, $sign_parts));
        } else {
            uksort($params, 'strcmp');
            $pairs = array();
            foreach ($params as $key => $value) {
                $pairs[] = $key . '=' . $value;
            }
            $sign_parts = self::urlencode_rfc1738(implode('&', $pairs));
            $base_string = implode('&', array(strtoupper($method), self::urlencode_rfc1738($url), $sign_parts));
        }
        $key_parts = array(self::urlencode_rfc1738($this->_app_secret), self::urlencode_rfc1738($this->_token_secret));
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
    protected static function urlencode_rfc1738($str)
    {
        return rawurlencode($str);
    }
}
require_once dirname(__DIR__) . '/OAuth/Client.php';