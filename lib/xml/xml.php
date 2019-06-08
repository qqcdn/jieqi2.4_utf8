<?php

class XMLText
{
    public $nodeValue;
    public $nodeType;
    public $entities = array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;', '\'' => '&apos;', '"' => '&quot;', "\r" => '&#x0D;', "\n" => '&#x0A;', '	' => '&#x09;', ' ' => '&#x20;');
    public function __construct()
    {
        $this->nodeValue = NULL;
        $this->nodeType = XML_TYPE_TEXT;
    }
}
class XMLNode extends XMLText
{
    public $attributes;
    public $childNodes;
    public $firstChild;
    public $lastChild;
    public $previousSibling;
    public $nextSibling;
    public $nodeName;
    public $parentNode;
    public $nodeType;
    public function _xml_get_children($vals, &$i)
    {
        $children = array();
        if (isset($vals[$i]['value'])) {
            $tmp = new XMLText();
            $tmp->nodeValue = $vals[$i]['value'];
            $tmp->nodeType = XML_TYPE_CDATA;
            $children[] = $tmp;
        }
        $lastelm = '';
        $nChildren = count($vals);
        while (++$i < $nChildren) {
            switch ($vals[$i]['type']) {
                case 'cdata':
                    if ($lastelm != 'cdata') {
                        $tmp = new XMLText();
                        $tmp->nodeValue = $vals[$i]['value'];
                        $tmp->nodeType = XML_TYPE_CDATA;
                        $children[] = $tmp;
                    } else {
                        $children[count($children) - 1]->nodeValue .= $vals[$i]['value'];
                    }
                    break;
                case 'complete':
                    $tmp = new XMLNode();
                    $tmp->nodeName = $vals[$i]['tag'];
                    $tmp->attributes = isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : NULL;
                    if (isset($vals[$i]['value'])) {
                        $textnode = XMLNode::createTextNode($vals[$i]['value']);
                        $tmp->appendChild($textnode);
                    }
                    $tmp->parentNode = $this;
                    $children[] = $tmp;
                    break;
                case 'open':
                    $tmp = new XMLNode();
                    $tmp->nodeName = $vals[$i]['tag'];
                    $tmp->attributes = isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : NULL;
                    $tmp->parentNode = $this;
                    $tmp->childNodes = $tmp->_xml_get_children($vals, $i);
                    $children[] = $tmp;
                    break;
                case 'close':
                    $nThisChildren = count($children);
                    if (1 < $nThisChildren) {
                        for ($j = $nThisChildren - 2; 0 <= $j; $j--) {
                            $children[$j]->nextSibling =& $children[$j + 1];
                        }
                        for ($j = 1; $j < $nThisChildren; $j++) {
                            $children[$j]->previousSibling =& $children[$j - 1];
                        }
                    }
                    $this->firstChild =& $children[0];
                    $this->lastChild =& $children[($nThisChildren - 1) % $nThisChildren];
                    return $children;
                    break;
            }
            $lastelm = $vals[$i]['type'];
        }
    }
    public function appendChild($child)
    {
        $child->parentNode =& $this;
        if ($child->nodeType == XML_TYPE_NODE) {
            $child->previousSibling =& $this->lastChild;
        }
        $this->childNodes[] = $child;
        $lastkey = key($this->childNodes);
        if ($this->nodeType == XML_TYPE_NODE) {
            if (!is_null($this->lastChild)) {
                $this->lastChild->nextSibling =& $this->childNodes[$lastkey];
            }
            $this->firstChild =& $this->childNodes[0];
            $this->lastChild =& $this->childNodes[$lastkey];
        }
    }
    public static function createElement($name)
    {
        $tmp = new XMLNode();
        $tmp->nodeName = $name;
        return $tmp;
    }
    public static function createTextNode($value)
    {
        $tmp = new XMLText();
        $tmp->nodeValue = trim($value);
        return $tmp;
    }
    public function hasChildNodes()
    {
        return !is_null($this->childNodes);
    }
    public function insertBefore(&$child, $refChild = NULL)
    {
    }
    public function removeChild()
    {
    }
    public function toString()
    {
        $tagOpen = '<';
        $tagClose = '>';
        $tagBreak = "\n";
        $retVal = '';
        if (is_null($this->parentNode)) {
            if (!empty($this->xmlDecl)) {
                $retVal .= $this->xmlDecl . $tagBreak;
            }
            if (!empty($this->docTypeDecl)) {
                $retVal .= $this->docTypeDecl . $tagBreak;
            }
        }
        $sAttr = '';
        if (isset($this->attributes)) {
            foreach ($this->attributes as $key => $val) {
                $sAttr .= ' ' . $key . '="' . strtr($this->attributes[$key], $this->entities) . '"';
            }
        }
        if (isset($this->nodeName)) {
            if ($this->hasChildNodes()) {
                $retVal .= $tagOpen . $this->nodeName . $sAttr . $tagClose;
                if ($this->firstChild->nodeType != XML_TYPE_TEXT && $this->firstChild->nodeType != XML_TYPE_CDATA) {
                    $retVal .= $tagBreak;
                }
            } else {
                if (isset($this->firstChild->nodeValue)) {
                    $retVal .= $tagOpen . $this->nodeName . $sAttr . $tagClose . strtr($this->firstChild->nodeValue, $this->entities) . $tagOpen . '/' . $this->nodeName . $tagClose . $tagBreak;
                } else {
                    $retVal .= $tagOpen . $this->nodeName . $sAttr . ' /' . $tagClose . $tagBreak;
                }
            }
        }
        if ($this->hasChildNodes()) {
            foreach ($this->childNodes as $nk => $nv) {
                switch ($this->childNodes[$nk]->nodeType) {
                    case XML_TYPE_NODE:
                    default:
                        $retVal .= $this->childNodes[$nk]->toString();
                        break;
                    case XML_TYPE_TEXT:
                        $retVal .= strtr($this->childNodes[$nk]->nodeValue, $this->entities);
                        break;
                    case XML_TYPE_CDATA:
                        $retVal .= '<![CDATA[' . $this->childNodes[$nk]->nodeValue . ']]>';
                        break;
                }
            }
        }
        if ($this->hasChildNodes() && isset($this->nodeName)) {
            $retVal .= $tagOpen . '/' . $this->nodeName . $tagClose . $tagBreak;
        }
        return $retVal;
    }
}
class XML extends XMLNode
{
    public $status;
    public $error;
    public $version;
    public $encoding;
    public $contentType;
    public $docTypeDecl;
    public $xmlDecl;
    public function load($url)
    {
        if (empty($url)) {
            return false;
        }
        $this->parseXML(@file_get_contents($url));
    }
    public function parseXML($source)
    {
        if (preg_match('/<\\?xml\\s([^<>]*)\\?>/i', $source, $matches)) {
            $this->xmlDecl = '<?xml ' . $matches[1] . '?>';
            if (preg_match('/version\\s*=\\s*(\'|")([^<>\'"]*)(\'|")/i', $matches[1], $versionInfo)) {
                $this->version = $versionInfo[2];
            }
            if (preg_match('/encoding\\s*=\\s*(\'|")([^<>\'"]*)(\'|")/i', $matches[1], $encodingInfo)) {
                if (in_array(strtoupper($encodingInfo[2]), array('ISO-8859-1', 'UTF-8', 'US-ASCII'))) {
                    $this->encoding = strtoupper($encodingInfo[2]);
                } else {
                    $this->encoding = 'ISO-8859-1';
                    $source = preg_replace('/<\\?xml([^<>]*)(encoding\\s*=\\s*(\'|")([^<>\'"]*)(\'|"))([^<>]*)\\?>/i', '<?xml${1}encoding="ISO-8859-1"${6}?>', $source);
                }
            }
        }
        if (preg_match('/<!doctype\\ (.*?)>/i', $source, $matches)) {
            $this->docTypeDecl = '<!DOCTYPE ' . $matches[1] . '>';
        }
        $parser = xml_parser_create($this->encoding);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        $source = preg_replace('/>\\s+</i', '><', $source);
        $ret = @xml_parse_into_struct($parser, $source, $vals);
        if (!$ret) {
            $ret = xml_parse_into_struct($parser, preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/', '', $source), $vals);
        }
        xml_parser_free($parser);
        if (!empty($vals)) {
            $root = XMLNode::createElement($vals[0]['tag']);
            $root->attributes = isset($vals[0]['attributes']) ? $vals[0]['attributes'] : NULL;
            $i = 0;
            $root->childNodes = $root->_xml_get_children($vals, $i);
            $this->appendChild($root);
        }
        return $ret;
    }
}
define('XML_TYPE_NODE', 1);
define('XML_TYPE_TEXT', 3);
define('XML_TYPE_CDATA', 4);