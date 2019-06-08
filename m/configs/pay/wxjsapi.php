<?php
//微信扫码充值相关参数
class WxPayConfig
{
	//=======【基本信息设置】=====================================
	//
	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 * 
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 * 
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 * 
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * 
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
	const APPID = '000000';
	const MCHID = '000000';
	const KEY = '000000';
	const APPSECRET = '000000';
	
	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	const SSLCERT_PATH = '../../configs/pay/wxpay/apiclient_cert.pem';
	const SSLKEY_PATH = '../../configs/pay/wxpay/apiclient_key.pem';
	
	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	const CURL_PROXY_PORT = 0;//8080;
	
	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;
}
/*
$jieqiPayset['wxjsapi']['payid'] = 'wxd15c8b556d173a00'; //绑定支付的APPID（必须配置，开户邮件中可查看）

$jieqiPayset['wxjsapi']['paykey'] = '06512785b20220a7b31c15c78dda5bb1';  //商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
//设置地址：https://pay.weixin.qq.com/index.php/account/api_cert

$jieqiPayset['wxjsapi']['mchid'] = '1333558101'; //商户号（必须配置，开户邮件中可查看）

$jieqiPayset['wxjsapi']['appsecret'] = '1ba851d2e49e339bf0d36e5df1caf443'; //公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
//https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN

$jieqiPayset['wxjsapi']['payurl'] = '';  //提交到支付网站的网址

$jieqiPayset['wxjsapi']['payreturn'] = JIEQI_LOCAL_URL.'/modules/pay/wxjsapireturn.php';  //本站接收返回的网址
*/
$jieqiPayset['wxjsapi']['payrate'] = 100; //默认充值1元钱兑换虚拟币的值
$jieqiPayset['wxjsapi']['paycustom'] = 0; //是否允许自定义购买金额，0-不允许，1-允许
$jieqiPayset['wxjsapi']['paylimit'] = array('1000'=>'10', '2000'=>'20', '5000'=>'50', '10000'=>'100', '20000'=>'200', '50000'=>'500'); //允许选择的 虚拟币=>金额 选项，如 '1000'=>'10' 是指购买 1000虚拟币需要10元
$jieqiPayset['wxjsapi']['paydefault'] = '1000'; //默认充值虚拟币

//以下私有参数
$jieqiPayset['wxjsapi']['body'] = JIEQI_EGOLD_NAME;  //商品描述，可以留空

$jieqiPayset['wxjsapi']['notify_url'] = JIEQI_LOCAL_URL.'/modules/pay/wxjsapinotify.php'; //本站接收异步返回的网址


$jieqiPayset['wxjsapi']['addvars'] = array();  //附加参数
?>