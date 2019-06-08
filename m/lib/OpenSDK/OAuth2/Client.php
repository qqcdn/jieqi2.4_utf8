<?php

require_once dirname(__DIR__) . '/Util.php';
/**
 * OAuth协议接口
 *
 * 依赖：
 * PHP 5 >= 5.1.2, PECL hash >= 1.1 (no need now)
 * 
 * @ignore
 * @author icehu@vip.qq.com
 *
 */

class OpenSDK_OAuth2_Client
{

    /**
     * 上一次请求返回的Httpcode
     * @var number
     */
    protected $_http_code = null;

    /**
     * 是否debug
     * @var bool
     */
    protected $_debug = false;

    public function  __construct( $debug=false)
    {
        $this->_debug = $debug;
    }

    /**
     * 组装参数签名并请求接口
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @param false|array $multi false:普通post array: array ( '{fieldname}' =>array('type'=>'mine','name'=>'filename','data'=>'filedata') ) 文件上传
     * @param array $extheaders 附加的HTTP头信息
     * @return string
     */
    public function request( $url, $method, $params, $multi = false ,$extheaders=array())
    {
        return $this->http($url, $params, $method, $multi ,$extheaders);
    }

    /**
     * Http请求接口
     * 优先使用curl，在不支持curl的情况下，使用socket操作
     *
     * 发送文件需要使用 $method = 'POST' ， 同时指定 $multi = array('{fieldname}'=>'/path/to/file)
     *
     * @param string $url
     * @param array $params
     * @param string $method 支持 GET / POST / DELETE
     * @param false|array $multi false:普通post array: array ( '{fieldname}'=>'/path/to/file' ) 文件上传
     * @return string
     */
    protected function http( $url , $params , $method='GET' , $multi=false ,$extheaders=array())
    {
        return $this->curl_http($url, $params, $method, $multi ,$extheaders);
    }

    protected $_http_header = array();

    protected $_useragent = 'JIEQICMS-OAuth2.0';

    protected $_http_info = array();

    public $connecttimeout = 10;
    public $timeout = 10;
    public $ssl_verifypeer = false;

    protected function curl_http( $url , $params , $method='GET' , $multi=false ,$extheaders=array())
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);

        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));

        curl_setopt($ci, CURLOPT_HEADER, false);

        $headers = (array)$extheaders;
        switch ($method)
        {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($params))
                {
                    if($multi)
                    {
                        foreach($multi as $key => $file)
                        {
                            $params[$key] = '@' . $file;
                        }
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                        $headers[] = 'Expect: ';
                    }
                    else
                    {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params))
                {
                    $url = $url . (strpos($url, '?') ? '&' : '?')
                        . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
        curl_setopt($ci, CURLOPT_URL, $url);
        if($headers)
        {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        }

        $response = curl_exec($ci);
        $this->_http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->_http_info = array_merge($this->_http_info, curl_getinfo($ci));

        if($this->_debug)
        {
            echo 'Http Code ' , $this->_http_code , "\r\n";
            foreach ((array)$this->_http_info as $k => $v )
            {
                echo $k , ': ' , $v , "\r\n";
            }
            echo "\r\n";
            echo $response;
            echo "\r\n";
        }
        curl_close ($ci);
        return $response;
    }

    /**
     * Get the header info to store.
     *
     * @return int
     */
    function getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i))
        {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->_http_header[$key] = $value;
        }
        return strlen($header);
    }

    /**
     * 返回上一次请求的httpCode
     * @return number
     */
    public function getHttpCode()
    {
        return $this->_http_code;
    }
}
