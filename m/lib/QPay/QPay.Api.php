<?php

class QPayApi
{
    public static function unifiedOrder($inputObj, $timeOut = 15)
    {
        $url = 'https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi';
        if (!$inputObj->IsOut_trade_noSet()) {
            throw new QPayException('缺少统一支付接口必填参数out_trade_no！');
        } else {
            if (!$inputObj->IsBodySet()) {
                throw new QPayException('缺少统一支付接口必填参数body！');
            } else {
                if (!$inputObj->IsTotal_feeSet()) {
                    throw new QPayException('缺少统一支付接口必填参数total_fee！');
                } else {
                    if (!$inputObj->IsTrade_typeSet()) {
                        throw new QPayException('缺少统一支付接口必填参数trade_type！');
                    }
                }
            }
        }
        if (!$inputObj->IsNotify_urlSet()) {
            $inputObj->SetNotify_url(QPayConfig::NOTIFY_URL);
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function orderQuery($inputObj, $timeOut = 6)
    {
        $url = 'https://qpay.qq.com/cgi-bin/pay/qpay_order_query.cgi';
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new QPayException('订单查询接口中，out_trade_no、transaction_id至少填一个！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function closeOrder($inputObj, $timeOut = 6)
    {
        $url = 'https://qpay.qq.com/cgi-bin/pay/qpay_close_order.cgi';
        if (!$inputObj->IsOut_trade_noSet()) {
            throw new QPayException('订单查询接口中，out_trade_no必填！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function refund($inputObj, $timeOut = 6)
    {
        $url = 'https://api.qpay.qq.com/cgi-bin/pay/qpay_refund.cgi';
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new QPayException('退款申请接口中，out_trade_no、transaction_id至少填一个！');
        } else {
            if (!$inputObj->IsOut_refund_noSet()) {
                throw new QPayException('退款申请接口中，缺少必填参数out_refund_no！');
            } else {
                if (!$inputObj->IsTotal_feeSet()) {
                    throw new QPayException('退款申请接口中，缺少必填参数total_fee！');
                } else {
                    if (!$inputObj->IsRefund_feeSet()) {
                        throw new QPayException('退款申请接口中，缺少必填参数refund_fee！');
                    } else {
                        if (!$inputObj->IsOp_user_idSet()) {
                            throw new QPayException('退款申请接口中，缺少必填参数op_user_id！');
                        }
                    }
                }
            }
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, true, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function refundQuery($inputObj, $timeOut = 6)
    {
        $url = 'https://qpay.qq.com/cgi-bin/pay/qpay_refund_query.cgi';
        if (!$inputObj->IsOut_refund_noSet() && !$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet() && !$inputObj->IsRefund_idSet()) {
            throw new QPayException('退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function downloadBill($inputObj, $timeOut = 6)
    {
        $url = 'https://qpay.qq.com/cgi-bin/sp_download/qpay_mch_statement_down.cgi';
        if (!$inputObj->IsBill_dateSet()) {
            throw new QPayException('对账单接口中，缺少必填参数bill_date！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        if (substr($response, 0, 5) == '<xml>') {
            return '';
        }
        return $response;
    }
    public static function micropay($inputObj, $timeOut = 10)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/micropay';
        if (!$inputObj->IsBodySet()) {
            throw new QPayException('提交被扫支付API接口中，缺少必填参数body！');
        } else {
            if (!$inputObj->IsOut_trade_noSet()) {
                throw new QPayException('提交被扫支付API接口中，缺少必填参数out_trade_no！');
            } else {
                if (!$inputObj->IsTotal_feeSet()) {
                    throw new QPayException('提交被扫支付API接口中，缺少必填参数total_fee！');
                } else {
                    if (!$inputObj->IsAuth_codeSet()) {
                        throw new QPayException('提交被扫支付API接口中，缺少必填参数auth_code！');
                    }
                }
            }
        }
        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function reverse($inputObj, $timeOut = 6)
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new QPayException('撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, true, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function report($inputObj, $timeOut = 1)
    {
        $url = 'https://api.mch.weixin.qq.com/payitil/report';
        if (!$inputObj->IsInterface_urlSet()) {
            throw new QPayException('接口URL，缺少必填参数interface_url！');
        }
        if (!$inputObj->IsReturn_codeSet()) {
            throw new QPayException('返回状态码，缺少必填参数return_code！');
        }
        if (!$inputObj->IsResult_codeSet()) {
            throw new QPayException('业务结果，缺少必填参数result_code！');
        }
        if (!$inputObj->IsUser_ipSet()) {
            throw new QPayException('访问接口IP，缺少必填参数user_ip！');
        }
        if (!$inputObj->IsExecute_time_Set()) {
            throw new QPayException('接口耗时，缺少必填参数execute_time_！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetUser_ip($_SERVER['REMOTE_ADDR']);
        $inputObj->SetTime(date('YmdHis'));
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        return $response;
    }
    public static function bizpayurl($inputObj, $timeOut = 6)
    {
        if (!$inputObj->IsProduct_idSet()) {
            throw new QPayException('生成二维码，缺少必填参数product_id！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetTime_stamp(time());
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        return $inputObj->GetValues();
    }
    public static function shorturl($inputObj, $timeOut = 6)
    {
        $url = 'https://api.mch.weixin.qq.com/tools/shorturl';
        if (!$inputObj->IsLong_urlSet()) {
            throw new QPayException('需要转换的URL，签名用原串，传输需URL encode！');
        }
        $inputObj->SetAppid(QPayConfig::APPID);
        $inputObj->SetMch_id(QPayConfig::MCHID);
        $inputObj->SetNonce_str(self::getNonceStr());
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond();
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = QPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);
        return $result;
    }
    public static function notify($callback, &$msg)
    {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        try {
            $result = QPayResults::Init($xml);
        } catch (QPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }
        return call_user_func($callback, $result);
    }
    public static function getNonceStr($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public static function replyNotify($xml)
    {
        echo $xml;
    }
    private static function reportCostTime($url, $startTimeStamp, $data)
    {
        if (QPayConfig::REPORT_LEVENL == 0) {
            return NULL;
        }
        if (QPayConfig::REPORT_LEVENL == 1 && array_key_exists('return_code', $data) && $data['return_code'] == 'SUCCESS' && array_key_exists('result_code', $data) && $data['result_code'] == 'SUCCESS') {
            return NULL;
        }
        $endTimeStamp = self::getMillisecond();
        $objInput = new QPayReport();
        $objInput->SetInterface_url($url);
        $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
        if (array_key_exists('return_code', $data)) {
            $objInput->SetReturn_code($data['return_code']);
        }
        if (array_key_exists('return_msg', $data)) {
            $objInput->SetReturn_msg($data['return_msg']);
        }
        if (array_key_exists('result_code', $data)) {
            $objInput->SetResult_code($data['result_code']);
        }
        if (array_key_exists('err_code', $data)) {
            $objInput->SetErr_code($data['err_code']);
        }
        if (array_key_exists('err_code_des', $data)) {
            $objInput->SetErr_code_des($data['err_code_des']);
        }
        if (array_key_exists('out_trade_no', $data)) {
            $objInput->SetOut_trade_no($data['out_trade_no']);
        }
        if (array_key_exists('device_info', $data)) {
            $objInput->SetDevice_info($data['device_info']);
        }
        try {
            self::report($objInput);
        } catch (QPayException $e) {
        }
    }
    private static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        if (QPayConfig::CURL_PROXY_HOST != '0.0.0.0' && QPayConfig::CURL_PROXY_PORT != 0) {
            curl_setopt($ch, CURLOPT_PROXY, QPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, QPayConfig::CURL_PROXY_PORT);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($useCert == true) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, QPayConfig::SSLCERT_PATH);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, QPayConfig::SSLKEY_PATH);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new QPayException('curl ' . $url . ' 出错，错误码:' . $error . ' , 错误信息：' . $error_msg);
        }
    }
    private static function getMillisecond()
    {
        $time = explode(' ', microtime());
        $time = $time[1] . $time[0] * 1000;
        $time2 = explode('.', $time);
        $time = $time2[0];
        return $time;
    }
}
require_once __DIR__ . '/QPay.Exception.php';
require_once __DIR__ . '/QPay.Data.php';