<?php

function jieqi_pay_makequery($params, $ue = true, $sort = true)
{
    if ($sort) {
        ksort($params);
        reset($params);
    }
    $query_string = '';
    foreach ($params as $k => $v) {
        if (0 < strlen($v)) {
            if (0 < strlen($query_string)) {
                $query_string .= '&';
            }
            $query_string .= $ue == true ? urlencode($k) . '=' . urlencode($v) : $k . '=' . $v;
        }
    }
    return $query_string;
}
function jieqi_pay_signfilter($params)
{
    $ret = array();
    $funcode = $params['funcode'];
    foreach ($params as $k => $v) {
        switch ($funcode) {
            case 'WP001':
                if ($k != 'funcode' && $k != 'deviceType' && $k != 'mhtSignType' && $k != 'mhtSignature') {
                    $ret[$k] = $v;
                }
                break;
            case 'N001':
            case 'N002':
                if ($k != 'signType' && $k != 'signature') {
                    $ret[$k] = $v;
                }
                break;
            case 'MQ001':
                if ($k != 'mhtSignType' && $k != 'mhtSignature' && $k != 'signType' && $k != 'signature') {
                    $ret[$k] = $v;
                }
                break;
        }
    }
    return $ret;
}
function jieqi_pay_makesign($params, $key, $sort = true)
{
    if (is_array($params)) {
        $params = jieqi_pay_signfilter($params);
        $params = jieqi_pay_makequery($params, false, $sort);
    }
    return md5($params . '&' . md5($key));
}
function jieqi_pay_charsetconvert($data, $fromset = '', $toset = '')
{
    global $jieqi_charset_map;
    if (empty($jieqi_charset_map)) {
        $jieqi_charset_map = array('gb2312' => 'gb', 'gbk' => 'gb', 'gb' => 'gb', 'big5' => 'big5', 'utf-8' => 'utf8', 'utf8' => 'utf8');
    }
    if (empty($fromset)) {
        $fromset = JIEQI_SYSTEM_CHARSET;
    }
    if (empty($toset)) {
        $toset = JIEQI_SYSTEM_CHARSET;
    }
    if ($fromset != $toset) {
        include_once JIEQI_ROOT_PATH . '/include/changecode.php';
        if (is_array($data)) {
            return jieqi_funtoarray('jieqi_' . $jieqi_charset_map[$fromset] . '2' . $jieqi_charset_map[$toset], $data);
        } else {
            return call_user_func('jieqi_' . $jieqi_charset_map[$fromset] . '2' . $jieqi_charset_map[$toset], $data);
        }
    } else {
        return $data;
    }
}