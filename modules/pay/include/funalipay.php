<?php

function jieqi_pay_makesign($params, $key, $sort = true)
{
    if (is_array($params)) {
        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        if (isset($params['sign_type'])) {
            unset($params['sign_type']);
        }
        $params = jieqi_pay_makequery($params, false, $sort);
    }
    return md5($params . $key);
}
function jieqi_pay_signvars($params)
{
    $ret = array();
    $ret['service'] = strval($params['service']);
    $ret['v'] = strval($params['v']);
    $ret['sec_id'] = strval($params['sec_id']);
    $ret['notify_data'] = strval($params['notify_data']);
    return $ret;
}
function jieqi_pay_xmltext($text)
{
    $entities = array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;', '\'' => '&apos;', '"' => '&quot;');
    $text = strtr($text, $entities);
    $text = preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/', '', $text);
    return $text;
}
function jieqi_pay_parseres($str_text)
{
    $para_text = array();
    $para_split = explode('&', $str_text);
    foreach ($para_split as $item) {
        $item = urldecode($item);
        $nPos = strpos($item, '=');
        $nLen = strlen($item);
        $key = substr($item, 0, $nPos);
        $value = substr($item, $nPos + 1, $nLen - $nPos - 1);
        $para_text[$key] = $value;
    }
    if (!empty($para_text['res_data'])) {
        $doc = new DOMDocument();
        $doc->loadXML($para_text['res_data']);
        $para_text['request_token'] = $doc->getElementsByTagName('request_token')->item(0)->nodeValue;
    }
    return $para_text;
}
function jieqi_pay_verifyget($url, $cacert_url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);
    $responseText = curl_exec($curl);
    curl_close($curl);
    return $responseText;
}