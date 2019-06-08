<?php

class QPayDataBase
{
    protected $values = array();
    public function SetSign()
    {
        $sign = $this->MakeSign();
        $this->values['sign'] = $sign;
        return $sign;
    }
    public function GetSign()
    {
        return $this->values['sign'];
    }
    public function IsSignSet()
    {
        return array_key_exists('sign', $this->values);
    }
    public function ToXml()
    {
        if (!is_array($this->values) || count($this->values) <= 0) {
            throw new QPayException('数组数据异常！');
        }
        $xml = '<xml>';
        foreach ($this->values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }
    public function FromXml($xml)
    {
        if (!$xml) {
            throw new QPayException('xml数据异常！');
        }
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->values;
    }
    public function ToUrlParams()
    {
        $buff = '';
        foreach ($this->values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }
        $buff = trim($buff, '&');
        return $buff;
    }
    public function MakeSign()
    {
        ksort($this->values);
        $string = $this->ToUrlParams();
        $string = $string . '&key=' . QPayConfig::KEY;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }
    public function GetValues()
    {
        return $this->values;
    }
}
class QPayResults extends QPayDataBase
{
    public function CheckSign()
    {
        if (!$this->IsSignSet()) {
            throw new QPayException('签名错误！');
        }
        $sign = $this->MakeSign();
        if ($this->GetSign() == $sign) {
            return true;
        }
        throw new QPayException('签名错误！');
    }
    public function FromArray($array)
    {
        $this->values = $array;
    }
    public static function InitFromArray($array, $noCheckSign = false)
    {
        $obj = new self();
        $obj->FromArray($array);
        if ($noCheckSign == false) {
            $obj->CheckSign();
        }
        return $obj;
    }
    public function SetData($key, $value)
    {
        $this->values[$key] = $value;
    }
    public static function Init($xml)
    {
        $obj = new self();
        $obj->FromXml($xml);
        if (strtoupper($obj->values['return_code']) != 'SUCCESS') {
            return $obj->GetValues();
        }
        $obj->CheckSign();
        return $obj->GetValues();
    }
}
class QPayNotifyReply extends QPayDataBase
{
    public function SetReturn_code($return_code)
    {
        $this->values['return_code'] = $return_code;
    }
    public function GetReturn_code()
    {
        return $this->values['return_code'];
    }
    public function SetReturn_msg($return_msg)
    {
        $this->values['return_msg'] = $return_msg;
    }
    public function GetReturn_msg()
    {
        return $this->values['return_msg'];
    }
    public function SetData($key, $value)
    {
        $this->values[$key] = $value;
    }
}
class QPayUnifiedOrder extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetBody($value)
    {
        $this->values['body'] = $value;
    }
    public function GetBody()
    {
        return $this->values['body'];
    }
    public function IsBodySet()
    {
        return array_key_exists('body', $this->values);
    }
    public function SetDetail($value)
    {
        $this->values['detail'] = $value;
    }
    public function GetDetail()
    {
        return $this->values['detail'];
    }
    public function IsDetailSet()
    {
        return array_key_exists('detail', $this->values);
    }
    public function SetAttach($value)
    {
        $this->values['attach'] = $value;
    }
    public function GetAttach()
    {
        return $this->values['attach'];
    }
    public function IsAttachSet()
    {
        return array_key_exists('attach', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetFee_type($value)
    {
        $this->values['fee_type'] = $value;
    }
    public function GetFee_type()
    {
        return $this->values['fee_type'];
    }
    public function IsFee_typeSet()
    {
        return array_key_exists('fee_type', $this->values);
    }
    public function SetTotal_fee($value)
    {
        $this->values['total_fee'] = $value;
    }
    public function GetTotal_fee()
    {
        return $this->values['total_fee'];
    }
    public function IsTotal_feeSet()
    {
        return array_key_exists('total_fee', $this->values);
    }
    public function SetSpbill_create_ip($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }
    public function GetSpbill_create_ip()
    {
        return $this->values['spbill_create_ip'];
    }
    public function IsSpbill_create_ipSet()
    {
        return array_key_exists('spbill_create_ip', $this->values);
    }
    public function SetTime_start($value)
    {
        $this->values['time_start'] = $value;
    }
    public function GetTime_start()
    {
        return $this->values['time_start'];
    }
    public function IsTime_startSet()
    {
        return array_key_exists('time_start', $this->values);
    }
    public function SetTime_expire($value)
    {
        $this->values['time_expire'] = $value;
    }
    public function GetTime_expire()
    {
        return $this->values['time_expire'];
    }
    public function IsTime_expireSet()
    {
        return array_key_exists('time_expire', $this->values);
    }
    public function SetGoods_tag($value)
    {
        $this->values['promotion_tag'] = $value;
    }
    public function GetGoods_tag()
    {
        return $this->values['promotion_tag'];
    }
    public function IsGoods_tagSet()
    {
        return array_key_exists('promotion_tag', $this->values);
    }
    public function SetNotify_url($value)
    {
        $this->values['notify_url'] = $value;
    }
    public function GetNotify_url()
    {
        return $this->values['notify_url'];
    }
    public function IsNotify_urlSet()
    {
        return array_key_exists('notify_url', $this->values);
    }
    public function SetTrade_type($value)
    {
        $this->values['trade_type'] = $value;
    }
    public function GetTrade_type()
    {
        return $this->values['trade_type'];
    }
    public function IsTrade_typeSet()
    {
        return array_key_exists('trade_type', $this->values);
    }
    public function SetProduct_id($value)
    {
        $this->values['product_id'] = $value;
    }
    public function GetProduct_id()
    {
        return $this->values['product_id'];
    }
    public function IsProduct_idSet()
    {
        return array_key_exists('product_id', $this->values);
    }
    public function SetOpenid($value)
    {
        $this->values['openid'] = $value;
    }
    public function GetOpenid()
    {
        return $this->values['openid'];
    }
    public function IsOpenidSet()
    {
        return array_key_exists('openid', $this->values);
    }
}
class QPayOrderQuery extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetTransaction_id($value)
    {
        $this->values['transaction_id'] = $value;
    }
    public function GetTransaction_id()
    {
        return $this->values['transaction_id'];
    }
    public function IsTransaction_idSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
}
class QPayCloseOrder extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
}
class QPayRefund extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetTransaction_id($value)
    {
        $this->values['transaction_id'] = $value;
    }
    public function GetTransaction_id()
    {
        return $this->values['transaction_id'];
    }
    public function IsTransaction_idSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetOut_refund_no($value)
    {
        $this->values['out_refund_no'] = $value;
    }
    public function GetOut_refund_no()
    {
        return $this->values['out_refund_no'];
    }
    public function IsOut_refund_noSet()
    {
        return array_key_exists('out_refund_no', $this->values);
    }
    public function SetTotal_fee($value)
    {
        $this->values['total_fee'] = $value;
    }
    public function GetTotal_fee()
    {
        return $this->values['total_fee'];
    }
    public function IsTotal_feeSet()
    {
        return array_key_exists('total_fee', $this->values);
    }
    public function SetRefund_fee($value)
    {
        $this->values['refund_fee'] = $value;
    }
    public function GetRefund_fee()
    {
        return $this->values['refund_fee'];
    }
    public function IsRefund_feeSet()
    {
        return array_key_exists('refund_fee', $this->values);
    }
    public function SetRefund_fee_type($value)
    {
        $this->values['refund_fee_type'] = $value;
    }
    public function GetRefund_fee_type()
    {
        return $this->values['refund_fee_type'];
    }
    public function IsRefund_fee_typeSet()
    {
        return array_key_exists('refund_fee_type', $this->values);
    }
    public function SetOp_user_id($value)
    {
        $this->values['op_user_id'] = $value;
    }
    public function GetOp_user_id()
    {
        return $this->values['op_user_id'];
    }
    public function IsOp_user_idSet()
    {
        return array_key_exists('op_user_id', $this->values);
    }
}
class QPayRefundQuery extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetTransaction_id($value)
    {
        $this->values['transaction_id'] = $value;
    }
    public function GetTransaction_id()
    {
        return $this->values['transaction_id'];
    }
    public function IsTransaction_idSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetOut_refund_no($value)
    {
        $this->values['out_refund_no'] = $value;
    }
    public function GetOut_refund_no()
    {
        return $this->values['out_refund_no'];
    }
    public function IsOut_refund_noSet()
    {
        return array_key_exists('out_refund_no', $this->values);
    }
    public function SetRefund_id($value)
    {
        $this->values['refund_id'] = $value;
    }
    public function GetRefund_id()
    {
        return $this->values['refund_id'];
    }
    public function IsRefund_idSet()
    {
        return array_key_exists('refund_id', $this->values);
    }
}
class QPayDownloadBill extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetBill_date($value)
    {
        $this->values['bill_date'] = $value;
    }
    public function GetBill_date()
    {
        return $this->values['bill_date'];
    }
    public function IsBill_dateSet()
    {
        return array_key_exists('bill_date', $this->values);
    }
    public function SetBill_type($value)
    {
        $this->values['bill_type'] = $value;
    }
    public function GetBill_type()
    {
        return $this->values['bill_type'];
    }
    public function IsBill_typeSet()
    {
        return array_key_exists('bill_type', $this->values);
    }
}
class QPayReport extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetInterface_url($value)
    {
        $this->values['interface_url'] = $value;
    }
    public function GetInterface_url()
    {
        return $this->values['interface_url'];
    }
    public function IsInterface_urlSet()
    {
        return array_key_exists('interface_url', $this->values);
    }
    public function SetExecute_time_($value)
    {
        $this->values['execute_time_'] = $value;
    }
    public function GetExecute_time_()
    {
        return $this->values['execute_time_'];
    }
    public function IsExecute_time_Set()
    {
        return array_key_exists('execute_time_', $this->values);
    }
    public function SetReturn_code($value)
    {
        $this->values['return_code'] = $value;
    }
    public function GetReturn_code()
    {
        return $this->values['return_code'];
    }
    public function IsReturn_codeSet()
    {
        return array_key_exists('return_code', $this->values);
    }
    public function SetReturn_msg($value)
    {
        $this->values['return_msg'] = $value;
    }
    public function GetReturn_msg()
    {
        return $this->values['return_msg'];
    }
    public function IsReturn_msgSet()
    {
        return array_key_exists('return_msg', $this->values);
    }
    public function SetResult_code($value)
    {
        $this->values['result_code'] = $value;
    }
    public function GetResult_code()
    {
        return $this->values['result_code'];
    }
    public function IsResult_codeSet()
    {
        return array_key_exists('result_code', $this->values);
    }
    public function SetErr_code($value)
    {
        $this->values['err_code'] = $value;
    }
    public function GetErr_code()
    {
        return $this->values['err_code'];
    }
    public function IsErr_codeSet()
    {
        return array_key_exists('err_code', $this->values);
    }
    public function SetErr_code_des($value)
    {
        $this->values['err_code_des'] = $value;
    }
    public function GetErr_code_des()
    {
        return $this->values['err_code_des'];
    }
    public function IsErr_code_desSet()
    {
        return array_key_exists('err_code_des', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetUser_ip($value)
    {
        $this->values['user_ip'] = $value;
    }
    public function GetUser_ip()
    {
        return $this->values['user_ip'];
    }
    public function IsUser_ipSet()
    {
        return array_key_exists('user_ip', $this->values);
    }
    public function SetTime($value)
    {
        $this->values['time'] = $value;
    }
    public function GetTime()
    {
        return $this->values['time'];
    }
    public function IsTimeSet()
    {
        return array_key_exists('time', $this->values);
    }
}
class QPayShortUrl extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetLong_url($value)
    {
        $this->values['long_url'] = $value;
    }
    public function GetLong_url()
    {
        return $this->values['long_url'];
    }
    public function IsLong_urlSet()
    {
        return array_key_exists('long_url', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
}
class QPayMicroPay extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetBody($value)
    {
        $this->values['body'] = $value;
    }
    public function GetBody()
    {
        return $this->values['body'];
    }
    public function IsBodySet()
    {
        return array_key_exists('body', $this->values);
    }
    public function SetDetail($value)
    {
        $this->values['detail'] = $value;
    }
    public function GetDetail()
    {
        return $this->values['detail'];
    }
    public function IsDetailSet()
    {
        return array_key_exists('detail', $this->values);
    }
    public function SetAttach($value)
    {
        $this->values['attach'] = $value;
    }
    public function GetAttach()
    {
        return $this->values['attach'];
    }
    public function IsAttachSet()
    {
        return array_key_exists('attach', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetTotal_fee($value)
    {
        $this->values['total_fee'] = $value;
    }
    public function GetTotal_fee()
    {
        return $this->values['total_fee'];
    }
    public function IsTotal_feeSet()
    {
        return array_key_exists('total_fee', $this->values);
    }
    public function SetFee_type($value)
    {
        $this->values['fee_type'] = $value;
    }
    public function GetFee_type()
    {
        return $this->values['fee_type'];
    }
    public function IsFee_typeSet()
    {
        return array_key_exists('fee_type', $this->values);
    }
    public function SetSpbill_create_ip($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }
    public function GetSpbill_create_ip()
    {
        return $this->values['spbill_create_ip'];
    }
    public function IsSpbill_create_ipSet()
    {
        return array_key_exists('spbill_create_ip', $this->values);
    }
    public function SetTime_start($value)
    {
        $this->values['time_start'] = $value;
    }
    public function GetTime_start()
    {
        return $this->values['time_start'];
    }
    public function IsTime_startSet()
    {
        return array_key_exists('time_start', $this->values);
    }
    public function SetTime_expire($value)
    {
        $this->values['time_expire'] = $value;
    }
    public function GetTime_expire()
    {
        return $this->values['time_expire'];
    }
    public function IsTime_expireSet()
    {
        return array_key_exists('time_expire', $this->values);
    }
    public function SetGoods_tag($value)
    {
        $this->values['goods_tag'] = $value;
    }
    public function GetGoods_tag()
    {
        return $this->values['goods_tag'];
    }
    public function IsGoods_tagSet()
    {
        return array_key_exists('goods_tag', $this->values);
    }
    public function SetAuth_code($value)
    {
        $this->values['auth_code'] = $value;
    }
    public function GetAuth_code()
    {
        return $this->values['auth_code'];
    }
    public function IsAuth_codeSet()
    {
        return array_key_exists('auth_code', $this->values);
    }
}
class QPayReverse extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetTransaction_id($value)
    {
        $this->values['transaction_id'] = $value;
    }
    public function GetTransaction_id()
    {
        return $this->values['transaction_id'];
    }
    public function IsTransaction_idSet()
    {
        return array_key_exists('transaction_id', $this->values);
    }
    public function SetOut_trade_no($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    public function GetOut_trade_no()
    {
        return $this->values['out_trade_no'];
    }
    public function IsOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
}
class QPayJsApiPay extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appId'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appId'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appId', $this->values);
    }
    public function SetTimeStamp($value)
    {
        $this->values['timeStamp'] = $value;
    }
    public function GetTimeStamp()
    {
        return $this->values['timeStamp'];
    }
    public function IsTimeStampSet()
    {
        return array_key_exists('timeStamp', $this->values);
    }
    public function SetNonceStr($value)
    {
        $this->values['nonceStr'] = $value;
    }
    public function GetReturn_code()
    {
        return $this->values['nonceStr'];
    }
    public function IsReturn_codeSet()
    {
        return array_key_exists('nonceStr', $this->values);
    }
    public function SetPackage($value)
    {
        $this->values['package'] = $value;
    }
    public function GetPackage()
    {
        return $this->values['package'];
    }
    public function IsPackageSet()
    {
        return array_key_exists('package', $this->values);
    }
    public function SetSignType($value)
    {
        $this->values['signType'] = $value;
    }
    public function GetSignType()
    {
        return $this->values['signType'];
    }
    public function IsSignTypeSet()
    {
        return array_key_exists('signType', $this->values);
    }
    public function SetPaySign($value)
    {
        $this->values['paySign'] = $value;
    }
    public function GetPaySign()
    {
        return $this->values['paySign'];
    }
    public function IsPaySignSet()
    {
        return array_key_exists('paySign', $this->values);
    }
}
class QPayBizPayUrl extends QPayDataBase
{
    public function SetAppid($value)
    {
        $this->values['appid'] = $value;
    }
    public function GetAppid()
    {
        return $this->values['appid'];
    }
    public function IsAppidSet()
    {
        return array_key_exists('appid', $this->values);
    }
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }
    public function SetTime_stamp($value)
    {
        $this->values['time_stamp'] = $value;
    }
    public function GetTime_stamp()
    {
        return $this->values['time_stamp'];
    }
    public function IsTime_stampSet()
    {
        return array_key_exists('time_stamp', $this->values);
    }
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    public function SetProduct_id($value)
    {
        $this->values['product_id'] = $value;
    }
    public function GetProduct_id()
    {
        return $this->values['product_id'];
    }
    public function IsProduct_idSet()
    {
        return array_key_exists('product_id', $this->values);
    }
}
require_once __DIR__ . '/QPay.Exception.php';