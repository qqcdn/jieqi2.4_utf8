<?php

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