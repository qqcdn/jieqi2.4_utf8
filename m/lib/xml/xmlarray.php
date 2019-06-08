<?php

class XMLArray
{
    public $text;
    public $arrays;
    public $keys;
    public $node_flag;
    public $depth;
    public $xml_parser;
    public $encoding = 'ISO-8859-1';
    public $entities = array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;', '\'' => '&apos;', '"' => '&quot;');
    public function __construct($encoding = '')
    {
        if (!empty($encoding)) {
            $this->encoding = $encoding;
        } else {
            if (defined('JIEQI_SYSTEM_CHARSET')) {
                $this->encoding = JIEQI_SYSTEM_CHARSET;
            }
        }
    }
    public function array2xml($array, $rname = 'array')
    {
        $this->text = '<?xml version="1.0" encoding="' . $this->encoding . '"?>' . "\n" . '<' . $rname . '>' . "\n" . '';
        $this->text .= $this->array_transform($array);
        $this->text .= '</' . $rname . '>';
        return $this->text;
    }
    public function array_transform($array)
    {
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                if (preg_match('/[&<>"\']/is', $value)) {
                    $value = '<![CDATA[' . $value . ']]>';
                }
                $this->text .= '<' . $key . '>' . $value . '</' . $key . '>' . "\n" . '';
            } else {
                $this->text .= '<' . $key . '>' . "\n" . '';
                $this->array_transform($value);
                $this->text .= '</' . $key . '>' . "\n" . '';
            }
        }
        return $array_text;
    }
    public function xml2array($xml)
    {
        libxml_disable_entity_loader(true);
        $ret = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$ret) {
            return false;
        }
        $ret = @json_encode($ret);
        return @json_decode($ret, true);
    }
}
if (!function_exists('json_decode')) {
    function json_encode($value)
    {
        $jsonObj = new Services_JSON();
        return $json->encode($value);
    }
    function json_decode($json, $assoc = NULL)
    {
        if ($assoc) {
            $jsonObj = new Services_JSON(16);
        } else {
            $jsonObj = new Services_JSON();
        }
        return $jsonObj->decode($json);
    }
    include_once JIEQI_ROOT_PATH . '/lib/Services/JSON.php';
}