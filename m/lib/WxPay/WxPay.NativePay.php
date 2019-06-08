<?php


class NativePay
{
    public function GetPrePayUrl($productId)
    {
        $biz = new WxPayBizPayUrl();
        $biz->SetProduct_id($productId);
        $values = WxPayApi::bizpayurl($biz);
        $url = 'weixin://wxpay/bizpayurl?' . $this->ToUrlParams($values);
        return $url;
    }
    private function ToUrlParams($urlObj)
    {
        $buff = '';
        foreach ($urlObj as $k => $v) {
            $buff .= $k . '=' . $v . '&';
        }
        $buff = trim($buff, '&');
        return $buff;
    }
    public function GetPayUrl($input)
    {
        if ($input->GetTrade_type() == 'NATIVE') {
            $result = WxPayApi::unifiedOrder($input);
            return $result;
        }
    }
}
require_once __DIR__ . '/WxPay.Api.php';