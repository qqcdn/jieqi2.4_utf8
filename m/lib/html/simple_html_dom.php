<?php

class simple_html_dom_node
{
    public $nodetype = HDOM_TYPE_TEXT;
    public $tag = 'text';
    public $attr = array();
    public $children = array();
    public $nodes = array();
    public $parent;
    public $_ = array();
    public $tag_start = 0;
    private $dom;
    public function __construct($dom)
    {
        $this->dom = $dom;
        $dom->nodes[] = $this;
    }
    public function __destruct()
    {
        $this->clear();
    }
    public function __toString()
    {
        return $this->outertext();
    }
    public function clear()
    {
        $this->dom = NULL;
        $this->nodes = NULL;
        $this->parent = NULL;
        $this->children = NULL;
    }
    public function dump($show_attr = true, $deep = 0)
    {
        $lead = str_repeat('    ', $deep);
        echo $lead . $this->tag;
        if ($show_attr && 0 < count($this->attr)) {
            echo '(';
            foreach ($this->attr as $k => $v) {
                echo '[' . $k . ']=>"' . $this->{$k} . '", ';
            }
            echo ')';
        }
        echo "\n";
        if ($this->nodes) {
            foreach ($this->nodes as $c) {
                $c->dump($show_attr, $deep + 1);
            }
        }
    }
    public function dump_node($echo = true)
    {
        $string = $this->tag;
        if (0 < count($this->attr)) {
            $string .= '(';
            foreach ($this->attr as $k => $v) {
                $string .= '[' . $k . ']=>"' . $this->{$k} . '", ';
            }
            $string .= ')';
        }
        if (0 < count($this->_)) {
            $string .= ' $_ (';
            foreach ($this->_ as $k => $v) {
                if (is_array($v)) {
                    $string .= '[' . $k . ']=>(';
                    foreach ($v as $k2 => $v2) {
                        $string .= '[' . $k2 . ']=>"' . $v2 . '", ';
                    }
                    $string .= ')';
                } else {
                    $string .= '[' . $k . ']=>"' . $v . '", ';
                }
            }
            $string .= ')';
        }
        if (isset($this->text)) {
            $string .= ' text: (' . $this->text . ')';
        }
        $string .= ' HDOM_INNER_INFO: \'';
        if (isset($node->_[HDOM_INFO_INNER])) {
            $string .= $node->_[HDOM_INFO_INNER] . '\'';
        } else {
            $string .= ' NULL ';
        }
        $string .= ' children: ' . count($this->children);
        $string .= ' nodes: ' . count($this->nodes);
        $string .= ' tag_start: ' . $this->tag_start;
        $string .= "\n";
        if ($echo) {
            echo $string;
            return NULL;
        } else {
            return $string;
        }
    }
    public function parent($parent = NULL)
    {
        if ($parent !== NULL) {
            $this->parent = $parent;
            $this->parent->nodes[] = $this;
            $this->parent->children[] = $this;
        }
        return $this->parent;
    }
    public function has_child()
    {
        return !empty($this->children);
    }
    public function children($idx = -1)
    {
        if ($idx === -1) {
            return $this->children;
        }
        if (isset($this->children[$idx])) {
            return $this->children[$idx];
        }
        return NULL;
    }
    public function first_child()
    {
        if (0 < count($this->children)) {
            return $this->children[0];
        }
        return NULL;
    }
    public function last_child()
    {
        if (0 < ($count = count($this->children))) {
            return $this->children[$count - 1];
        }
        return NULL;
    }
    public function next_sibling()
    {
        if ($this->parent === NULL) {
            return NULL;
        }
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx < $count && $this !== $this->parent->children[$idx]) {
            ++$idx;
        }
        if ($count <= ++$idx) {
            return NULL;
        }
        return $this->parent->children[$idx];
    }
    public function prev_sibling()
    {
        if ($this->parent === NULL) {
            return NULL;
        }
        $idx = 0;
        $count = count($this->parent->children);
        while ($idx < $count && $this !== $this->parent->children[$idx]) {
            ++$idx;
        }
        if (--$idx < 0) {
            return NULL;
        }
        return $this->parent->children[$idx];
    }
    public function find_ancestor_tag($tag)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        $returnDom = $this;
        while (!is_null($returnDom)) {
            if (is_object($debugObject)) {
                $debugObject->debugLog(2, 'Current tag is: ' . $returnDom->tag);
            }
            if ($returnDom->tag == $tag) {
                break;
            }
            $returnDom = $returnDom->parent;
        }
        return $returnDom;
    }
    public function innertext()
    {
        if (isset($this->_[HDOM_INFO_INNER])) {
            return $this->_[HDOM_INFO_INNER];
        }
        if (isset($this->_[HDOM_INFO_TEXT])) {
            return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
        }
        $ret = '';
        foreach ($this->nodes as $n) {
            $ret .= $n->outertext();
        }
        return $ret;
    }
    public function outertext()
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $text = '';
            if ($this->tag == 'text') {
                if (!empty($this->text)) {
                    $text = ' with text: ' . $this->text;
                }
            }
            $debugObject->debugLog(1, 'Innertext of tag: ' . $this->tag . $text);
        }
        if ($this->tag === 'root') {
            return $this->innertext();
        }
        if ($this->dom && $this->dom->callback !== NULL) {
            call_user_func_array($this->dom->callback, array($this));
        }
        if (isset($this->_[HDOM_INFO_OUTER])) {
            return $this->_[HDOM_INFO_OUTER];
        }
        if (isset($this->_[HDOM_INFO_TEXT])) {
            return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
        }
        if ($this->dom && $this->dom->nodes[$this->_[HDOM_INFO_BEGIN]]) {
            $ret = $this->dom->nodes[$this->_[HDOM_INFO_BEGIN]]->makeup();
        } else {
            $ret = '';
        }
        if (isset($this->_[HDOM_INFO_INNER])) {
            if ($this->tag != 'br') {
                $ret .= $this->_[HDOM_INFO_INNER];
            }
        } else {
            if ($this->nodes) {
                foreach ($this->nodes as $n) {
                    $ret .= $this->convert_text($n->outertext());
                }
            }
        }
        if (isset($this->_[HDOM_INFO_END]) && $this->_[HDOM_INFO_END] != 0) {
            $ret .= '</' . $this->tag . '>';
        }
        return $ret;
    }
    public function text()
    {
        if (isset($this->_[HDOM_INFO_INNER])) {
            return $this->_[HDOM_INFO_INNER];
        }
        switch ($this->nodetype) {
            case HDOM_TYPE_TEXT:
                return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
            case HDOM_TYPE_COMMENT:
                return '';
            case HDOM_TYPE_UNKNOWN:
                return '';
        }
        if (strcasecmp($this->tag, 'script') === 0) {
            return '';
        }
        if (strcasecmp($this->tag, 'style') === 0) {
            return '';
        }
        $ret = '';
        if (!is_null($this->nodes)) {
            foreach ($this->nodes as $n) {
                $ret .= $this->convert_text($n->text());
            }
            if ($this->tag == 'span') {
                $ret .= $this->dom->default_span_text;
            }
        }
        return $ret;
    }
    public function xmltext()
    {
        $ret = $this->innertext();
        $ret = str_ireplace('<![CDATA[', '', $ret);
        $ret = str_replace(']]>', '', $ret);
        return $ret;
    }
    public function makeup()
    {
        if (isset($this->_[HDOM_INFO_TEXT])) {
            return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
        }
        $ret = '<' . $this->tag;
        $i = -1;
        foreach ($this->attr as $key => $val) {
            ++$i;
            if ($val === NULL || $val === false) {
                continue;
            }
            $ret .= $this->_[HDOM_INFO_SPACE][$i][0];
            if ($val === true) {
                $ret .= $key;
            } else {
                switch ($this->_[HDOM_INFO_QUOTE][$i]) {
                    case HDOM_QUOTE_DOUBLE:
                        $quote = '"';
                        break;
                    case HDOM_QUOTE_SINGLE:
                        $quote = '\'';
                        break;
                    default:
                        $quote = '';
                }
                $ret .= $key . $this->_[HDOM_INFO_SPACE][$i][1] . '=' . $this->_[HDOM_INFO_SPACE][$i][2] . $quote . $val . $quote;
            }
        }
        $ret = $this->dom->restore_noise($ret);
        return $ret . $this->_[HDOM_INFO_ENDSPACE] . '>';
    }
    public function find($selector, $idx = NULL, $lowercase = false)
    {
        $selectors = $this->parse_selector($selector);
        if (($count = count($selectors)) === 0) {
            return array();
        }
        $found_keys = array();
        for ($c = 0; $c < $count; ++$c) {
            if (($levle = count($selectors[$c])) === 0) {
                return array();
            }
            if (!isset($this->_[HDOM_INFO_BEGIN])) {
                return array();
            }
            $head = array($this->_[HDOM_INFO_BEGIN] => 1);
            for ($l = 0; $l < $levle; ++$l) {
                $ret = array();
                foreach ($head as $k => $v) {
                    $n = $k === -1 ? $this->dom->root : $this->dom->nodes[$k];
                    $n->seek($selectors[$c][$l], $ret, $lowercase);
                }
                $head = $ret;
            }
            foreach ($head as $k => $v) {
                if (!isset($found_keys[$k])) {
                    $found_keys[$k] = 1;
                }
            }
        }
        ksort($found_keys);
        $found = array();
        foreach ($found_keys as $k => $v) {
            $found[] = $this->dom->nodes[$k];
        }
        if (is_null($idx)) {
            return $found;
        } else {
            if ($idx < 0) {
                $idx = count($found) + $idx;
            }
        }
        return isset($found[$idx]) ? $found[$idx] : NULL;
    }
    protected function seek($selector, &$ret, $lowercase = false)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        $no_key = $selector[4];
        $exp = $selector[3];
        $val = $selector[2];
        $key = $selector[1];
        $tag = $selector[0];
        if ($tag && $key && is_numeric($key)) {
            $count = 0;
            foreach ($this->children as $c) {
                if ($tag === '*' || $tag === $c->tag) {
                    if (++$count == $key) {
                        $ret[$c->_[HDOM_INFO_BEGIN]] = 1;
                        return NULL;
                    }
                }
            }
            return NULL;
        }
        $end = !empty($this->_[HDOM_INFO_END]) ? $this->_[HDOM_INFO_END] : 0;
        if ($end == 0) {
            $parent = $this->parent;
            while (!isset($parent->_[HDOM_INFO_END]) && $parent !== NULL) {
                $end -= 1;
                $parent = $parent->parent;
            }
            $end += $parent->_[HDOM_INFO_END];
        }
        for ($i = $this->_[HDOM_INFO_BEGIN] + 1; $i < $end; ++$i) {
            $node = $this->dom->nodes[$i];
            $pass = true;
            if ($tag === '*' && !$key) {
                if (in_array($node, $this->children, true)) {
                    $ret[$i] = 1;
                }
                continue;
            }
            if ($tag && $tag != $node->tag && $tag !== '*') {
                $pass = false;
            }
            if ($pass && $key) {
                if ($no_key) {
                    if (isset($node->attr[$key])) {
                        $pass = false;
                    }
                } else {
                    if ($key != 'plaintext' && !isset($node->attr[$key])) {
                        $pass = false;
                    }
                }
            }
            if ($pass && $key && $val && $val !== '*') {
                if ($key == 'plaintext') {
                    $nodeKeyValue = $node->text();
                } else {
                    $nodeKeyValue = $node->attr[$key];
                }
                if (is_object($debugObject)) {
                    $debugObject->debugLog(2, 'testing node: ' . $node->tag . ' for attribute: ' . $key . $exp . $val . ' where nodes value is: ' . $nodeKeyValue);
                }
                if ($lowercase) {
                    $check = $this->match($exp, strtolower($val), strtolower($nodeKeyValue));
                } else {
                    $check = $this->match($exp, $val, $nodeKeyValue);
                }
                if (is_object($debugObject)) {
                    $debugObject->debugLog(2, 'after match: ' . ($check ? 'true' : 'false'));
                }
                if (!$check && strcasecmp($key, 'class') === 0) {
                    foreach (explode(' ', $node->attr[$key]) as $k) {
                        if (!empty($k)) {
                            if ($lowercase) {
                                $check = $this->match($exp, strtolower($val), strtolower($k));
                            } else {
                                $check = $this->match($exp, $val, $k);
                            }
                            if ($check) {
                                break;
                            }
                        }
                    }
                }
                if (!$check) {
                    $pass = false;
                }
            }
            if ($pass) {
                $ret[$i] = 1;
            }
            unset($node);
        }
        if (is_object($debugObject)) {
            $debugObject->debugLog(1, 'EXIT - ret: ', $ret);
        }
    }
    protected function match($exp, $pattern, $value)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        switch ($exp) {
            case '=':
                return $value === $pattern;
            case '!=':
                return $value !== $pattern;
            case '^=':
                return preg_match('/^' . preg_quote($pattern, '/') . '/', $value);
            case '$=':
                return preg_match('/' . preg_quote($pattern, '/') . '$/', $value);
            case '*=':
                if ($pattern[0] == '/') {
                    return preg_match($pattern, $value);
                }
                return preg_match('/' . $pattern . '/i', $value);
        }
        return false;
    }
    protected function parse_selector($selector_string)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        $pattern = '/([\\w-:\\*]*)(?:\\#([\\w-]+)|\\.([\\w-]+))?(?:\\[@?(!?[\\w-:]+)(?:([!*^$]?=)["\']?(.*?)["\']?)?\\])?([\\/, ]+)/is';
        preg_match_all($pattern, trim($selector_string) . ' ', $matches, PREG_SET_ORDER);
        if (is_object($debugObject)) {
            $debugObject->debugLog(2, 'Matches Array: ', $matches);
        }
        $selectors = array();
        $result = array();
        foreach ($matches as $m) {
            $m[0] = trim($m[0]);
            if ($m[0] === '' || $m[0] === '/' || $m[0] === '//') {
                continue;
            }
            if ($m[1] === 'tbody') {
                continue;
            }
            list($tag, $key, $val, $exp, $no_key) = array($m[1], NULL, NULL, '=', false);
            if (!empty($m[2])) {
                $key = 'id';
                $val = $m[2];
            }
            if (!empty($m[3])) {
                $key = 'class';
                $val = $m[3];
            }
            if (!empty($m[4])) {
                $key = $m[4];
            }
            if (!empty($m[5])) {
                $exp = $m[5];
            }
            if (!empty($m[6])) {
                $val = $m[6];
            }
            if ($this->dom->lowercase) {
                $tag = strtolower($tag);
                $key = strtolower($key);
            }
            if (isset($key[0]) && $key[0] === '!') {
                $key = substr($key, 1);
                $no_key = true;
            }
            $result[] = array($tag, $key, $val, $exp, $no_key);
            if (trim($m[7]) === ',') {
                $selectors[] = $result;
                $result = array();
            }
        }
        if (0 < count($result)) {
            $selectors[] = $result;
        }
        return $selectors;
    }
    public function __get($name)
    {
        if (isset($this->attr[$name])) {
            return $this->convert_text($this->attr[$name]);
        }
        switch ($name) {
            case 'outertext':
                return $this->outertext();
            case 'innertext':
                return $this->innertext();
            case 'plaintext':
                return $this->text();
            case 'xmltext':
                return $this->xmltext();
            default:
                return array_key_exists($name, $this->attr);
        }
    }
    public function __set($name, $value)
    {
        switch ($name) {
            case 'outertext':
                return $this->_[HDOM_INFO_OUTER] = $value;
            case 'innertext':
                if (isset($this->_[HDOM_INFO_TEXT])) {
                    return $this->_[HDOM_INFO_TEXT] = $value;
                }
                return $this->_[HDOM_INFO_INNER] = $value;
        }
        if (!isset($this->attr[$name])) {
            $this->_[HDOM_INFO_SPACE][] = array(' ', '', '');
            $this->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
        }
        $this->attr[$name] = $value;
    }
    public function __isset($name)
    {
        switch ($name) {
            case 'outertext':
                return true;
            case 'innertext':
                return true;
            case 'plaintext':
                return true;
        }
        return array_key_exists($name, $this->attr) ? true : isset($this->attr[$name]);
    }
    public function __unset($name)
    {
        if (isset($this->attr[$name])) {
            unset($this->attr[$name]);
        }
    }
    public function convert_text($text)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        $converted_text = $text;
        $sourceCharset = '';
        $targetCharset = '';
        if ($this->dom) {
            $sourceCharset = strtoupper($this->dom->_charset);
            $targetCharset = strtoupper($this->dom->_target_charset);
        }
        if (is_object($debugObject)) {
            $debugObject->debugLog(3, 'source charset: ' . $sourceCharset . ' target charaset: ' . $targetCharset);
        }
        if (!empty($sourceCharset) && !empty($targetCharset) && strcasecmp($sourceCharset, $targetCharset) != 0) {
            if (strcasecmp($targetCharset, 'UTF-8') == 0 && $this->is_utf8($text)) {
                $converted_text = $text;
            } else {
                $converted_text = iconv($sourceCharset, $targetCharset, $text);
            }
        }
        if ($targetCharset == 'UTF-8') {
            if (substr($converted_text, 0, 3) == '锘?) {
                $converted_text = substr($converted_text, 3);
            }
            if (substr($converted_text, -3) == '锘?) {
                $converted_text = substr($converted_text, 0, -3);
            }
        }
        return $converted_text;
    }
    public static function is_utf8($str)
    {
        $c = 0;
        $b = 0;
        $bits = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if (128 < $c) {
                if (254 <= $c) {
                    return false;
                } else {
                    if (252 <= $c) {
                        $bits = 6;
                    } else {
                        if (248 <= $c) {
                            $bits = 5;
                        } else {
                            if (240 <= $c) {
                                $bits = 4;
                            } else {
                                if (224 <= $c) {
                                    $bits = 3;
                                } else {
                                    if (192 <= $c) {
                                        $bits = 2;
                                    } else {
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($len < $i + $bits) {
                    return false;
                }
                while (1 < $bits) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || 191 < $b) {
                        return false;
                    }
                    $bits--;
                }
            }
        }
        return true;
    }
    public function get_display_size()
    {
        global $debugObject;
        $width = -1;
        $height = -1;
        if ($this->tag !== 'img') {
            return false;
        }
        if (isset($this->attr['width'])) {
            $width = $this->attr['width'];
        }
        if (isset($this->attr['height'])) {
            $height = $this->attr['height'];
        }
        if (isset($this->attr['style'])) {
            $attributes = array();
            preg_match_all('/([\\w-]+)\\s*:\\s*([^;]+)\\s*;?/', $this->attr['style'], $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $attributes[$match[1]] = $match[2];
            }
            if (isset($attributes['width']) && $width == -1) {
                if (strtolower(substr($attributes['width'], -2)) == 'px') {
                    $proposed_width = substr($attributes['width'], 0, -2);
                    if (filter_var($proposed_width, FILTER_VALIDATE_INT)) {
                        $width = $proposed_width;
                    }
                }
            }
            if (isset($attributes['height']) && $height == -1) {
                if (strtolower(substr($attributes['height'], -2)) == 'px') {
                    $proposed_height = substr($attributes['height'], 0, -2);
                    if (filter_var($proposed_height, FILTER_VALIDATE_INT)) {
                        $height = $proposed_height;
                    }
                }
            }
        }
        $result = array('height' => $height, 'width' => $width);
        return $result;
    }
    public function getAllAttributes()
    {
        return $this->attr;
    }
    public function getAttribute($name)
    {
        return $this->__get($name);
    }
    public function setAttribute($name, $value)
    {
        $this->__set($name, $value);
    }
    public function hasAttribute($name)
    {
        return $this->__isset($name);
    }
    public function removeAttribute($name)
    {
        $this->__set($name, NULL);
    }
    public function getElementById($id)
    {
        return $this->find('#' . $id, 0);
    }
    public function getElementsById($id, $idx = NULL)
    {
        return $this->find('#' . $id, $idx);
    }
    public function getElementByTagName($name)
    {
        return $this->find($name, 0);
    }
    public function getElementsByTagName($name, $idx = NULL)
    {
        return $this->find($name, $idx);
    }
    public function parentNode()
    {
        return $this->parent();
    }
    public function childNodes($idx = -1)
    {
        return $this->children($idx);
    }
    public function firstChild()
    {
        return $this->first_child();
    }
    public function lastChild()
    {
        return $this->last_child();
    }
    public function nextSibling()
    {
        return $this->next_sibling();
    }
    public function previousSibling()
    {
        return $this->prev_sibling();
    }
    public function hasChildNodes()
    {
        return $this->has_child();
    }
    public function nodeName()
    {
        return $this->tag;
    }
    public function appendChild($node)
    {
        $node->parent($this);
        return $node;
    }
}
class simple_html_dom
{
    public $root;
    public $nodes = array();
    public $callback;
    public $lowercase = false;
    public $original_size;
    public $size;
    protected $pos;
    protected $doc;
    protected $char;
    protected $cursor;
    protected $parent;
    protected $noise = array();
    protected $token_blank = ' 	' . "\r\n" . '';
    protected $token_equal = ' =/>';
    protected $token_slash = ' />' . "\r\n" . '	';
    protected $token_attr = ' >';
    public $_charset = '';
    public $_target_charset = '';
    protected $default_br_text = '';
    public $default_span_text = '';
    protected $self_closing_tags = array('img' => 1, 'br' => 1, 'input' => 1, 'meta' => 1, 'link' => 1, 'hr' => 1, 'base' => 1, 'embed' => 1, 'spacer' => 1);
    protected $block_tags = array('root' => 1, 'body' => 1, 'form' => 1, 'div' => 1, 'span' => 1, 'table' => 1);
    protected $optional_closing_tags = array('tr' => array('tr' => 1, 'td' => 1, 'th' => 1), 'th' => array('th' => 1), 'td' => array('td' => 1), 'li' => array('li' => 1), 'dt' => array('dt' => 1, 'dd' => 1), 'dd' => array('dd' => 1, 'dt' => 1), 'dl' => array('dd' => 1, 'dt' => 1), 'p' => array('p' => 1), 'nobr' => array('nobr' => 1), 'b' => array('b' => 1), 'option' => array('option' => 1));
    public function __construct($str = NULL, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT)
    {
        if ($str) {
            if (preg_match('/^http:\\/\\//i', $str) || is_file($str)) {
                $this->load_file($str);
            } else {
                $this->load($str, $lowercase, $stripRN, $defaultBRText, $defaultSpanText);
            }
        }
        if (!$forceTagsClosed) {
            $this->optional_closing_array = array();
        }
        $this->_target_charset = $target_charset;
    }
    public function __destruct()
    {
        $this->clear();
    }
    public function load($str, $lowercase = true, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT)
    {
        global $debugObject;
        $this->prepare($str, $lowercase, $stripRN, $defaultBRText, $defaultSpanText);
        $this->remove_noise('\'<!--(.*?)-->\'is');
        $this->remove_noise('\'<!\\[CDATA\\[(.*?)\\]\\]>\'is', true);
        $this->remove_noise('\'<\\s*script[^>]*[^/]>(.*?)<\\s*/\\s*script\\s*>\'is');
        $this->remove_noise('\'<\\s*script\\s*>(.*?)<\\s*/\\s*script\\s*>\'is');
        $this->remove_noise('\'<\\s*style[^>]*[^/]>(.*?)<\\s*/\\s*style\\s*>\'is');
        $this->remove_noise('\'<\\s*style\\s*>(.*?)<\\s*/\\s*style\\s*>\'is');
        $this->remove_noise('\'<\\s*(?:code)[^>]*>(.*?)<\\s*/\\s*(?:code)\\s*>\'is');
        $this->remove_noise('\'(<\\?)(.*?)(\\?>)\'s', true);
        $this->remove_noise('\'(\\{\\w)(.*?)(\\})\'s', true);
        while ($this->parse()) {
        }
        $this->root->_[HDOM_INFO_END] = $this->cursor;
        $this->parse_charset();
        return $this;
    }
    public function load_file()
    {
        $args = func_get_args();
        $this->load(call_user_func_array('file_get_contents', $args), true);
        if (($error = error_get_last()) !== NULL) {
            $this->clear();
            return false;
        }
    }
    public function set_callback($function_name)
    {
        $this->callback = $function_name;
    }
    public function remove_callback()
    {
        $this->callback = NULL;
    }
    public function save($filepath = '')
    {
        $ret = $this->root->innertext();
        if ($filepath !== '') {
            file_put_contents($filepath, $ret, LOCK_EX);
        }
        return $ret;
    }
    public function find($selector, $idx = NULL, $lowercase = false)
    {
        return $this->root->find($selector, $idx, $lowercase);
    }
    public function clear()
    {
        foreach ($this->nodes as $n) {
            $n->clear();
            $n = NULL;
        }
        if (isset($this->children)) {
            foreach ($this->children as $n) {
                $n->clear();
                $n = NULL;
            }
        }
        if (isset($this->parent)) {
            $this->parent->clear();
            unset($this->parent);
        }
        if (isset($this->root)) {
            $this->root->clear();
            unset($this->root);
        }
        unset($this->doc);
        unset($this->noise);
    }
    public function dump($show_attr = true)
    {
        $this->root->dump($show_attr);
    }
    protected function prepare($str, $lowercase = true, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT)
    {
        $this->clear();
        $this->size = strlen($str);
        $this->original_size = $this->size;
        if ($stripRN) {
            $str = str_replace("\r", ' ', $str);
            $str = str_replace("\n", ' ', $str);
            $this->size = strlen($str);
        }
        $this->doc = $str;
        $this->pos = 0;
        $this->cursor = 1;
        $this->noise = array();
        $this->nodes = array();
        $this->lowercase = $lowercase;
        $this->default_br_text = $defaultBRText;
        $this->default_span_text = $defaultSpanText;
        $this->root = new simple_html_dom_node($this);
        $this->root->tag = 'root';
        $this->root->_[HDOM_INFO_BEGIN] = -1;
        $this->root->nodetype = HDOM_TYPE_ROOT;
        $this->parent = $this->root;
        if (0 < $this->size) {
            $this->char = $this->doc[0];
        }
    }
    protected function parse()
    {
        if (($s = $this->copy_until_char('<')) === '') {
            return $this->read_tag();
        }
        $node = new simple_html_dom_node($this);
        ++$this->cursor;
        $node->_[HDOM_INFO_TEXT] = $s;
        $this->link_nodes($node, false);
        return true;
    }
    protected function parse_charset()
    {
        global $debugObject;
        $charset = NULL;
        if (function_exists('get_last_retrieve_url_contents_content_type')) {
            $contentTypeHeader = get_last_retrieve_url_contents_content_type();
            $success = preg_match('/charset=(.+)/', $contentTypeHeader, $matches);
            if ($success) {
                $charset = $matches[1];
                if (is_object($debugObject)) {
                    $debugObject->debugLog(2, 'header content-type found charset of: ' . $charset);
                }
            }
        }
        if (empty($charset)) {
            $el = $this->root->find('meta[http-equiv=Content-Type]', 0);
            if (!empty($el)) {
                $fullvalue = $el->content;
                if (is_object($debugObject)) {
                    $debugObject->debugLog(2, 'meta content-type tag found' . $fullvalue);
                }
                if (!empty($fullvalue)) {
                    $success = preg_match('/charset=(.+)/', $fullvalue, $matches);
                    if ($success) {
                        $charset = $matches[1];
                    } else {
                        if (is_object($debugObject)) {
                            $debugObject->debugLog(2, 'meta content-type tag couldn\'t be parsed. using iso-8859 default.');
                        }
                        $charset = 'ISO-8859-1';
                    }
                }
            }
        }
        if (empty($charset)) {
            $charset = mb_detect_encoding($this->root->plaintext . 'ascii', $encoding_list = array('UTF-8', 'CP1252'));
            if (is_object($debugObject)) {
                $debugObject->debugLog(2, 'mb_detect found: ' . $charset);
            }
            if ($charset === false) {
                if (is_object($debugObject)) {
                    $debugObject->debugLog(2, 'since mb_detect failed - using default of utf-8');
                }
                $charset = 'UTF-8';
            }
        }
        if (strtolower($charset) == strtolower('ISO-8859-1') || strtolower($charset) == strtolower('Latin1') || strtolower($charset) == strtolower('Latin-1')) {
            if (is_object($debugObject)) {
                $debugObject->debugLog(2, 'replacing ' . $charset . ' with CP1252 as its a superset');
            }
            $charset = 'CP1252';
        }
        if (is_object($debugObject)) {
            $debugObject->debugLog(1, 'EXIT - ' . $charset);
        }
        return $this->_charset = $charset;
    }
    protected function read_tag()
    {
        if ($this->char !== '<') {
            $this->root->_[HDOM_INFO_END] = $this->cursor;
            return false;
        }
        $begin_tag_pos = $this->pos;
        $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
        if ($this->char === '/') {
            $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
            $this->skip($this->token_blank);
            $tag = $this->copy_until_char('>');
            if (($pos = strpos($tag, ' ')) !== false) {
                $tag = substr($tag, 0, $pos);
            }
            $parent_lower = strtolower($this->parent->tag);
            $tag_lower = strtolower($tag);
            if ($parent_lower !== $tag_lower) {
                if (isset($this->optional_closing_tags[$parent_lower]) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[HDOM_INFO_END] = 0;
                    $org_parent = $this->parent;
                    while ($this->parent->parent && strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $this->parent->parent;
                    }
                    if (strtolower($this->parent->tag) !== $tag_lower) {
                        $this->parent = $org_parent;
                        if ($this->parent->parent) {
                            $this->parent = $this->parent->parent;
                        }
                        $this->parent->_[HDOM_INFO_END] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                } else {
                    if ($this->parent->parent && isset($this->block_tags[$tag_lower])) {
                        $this->parent->_[HDOM_INFO_END] = 0;
                        $org_parent = $this->parent;
                        while ($this->parent->parent && strtolower($this->parent->tag) !== $tag_lower) {
                            $this->parent = $this->parent->parent;
                        }
                        if (strtolower($this->parent->tag) !== $tag_lower) {
                            $this->parent = $org_parent;
                            $this->parent->_[HDOM_INFO_END] = $this->cursor;
                            return $this->as_text_node($tag);
                        }
                    } else {
                        if ($this->parent->parent && strtolower($this->parent->parent->tag) === $tag_lower) {
                            $this->parent->_[HDOM_INFO_END] = 0;
                            $this->parent = $this->parent->parent;
                        } else {
                            return $this->as_text_node($tag);
                        }
                    }
                }
            }
            $this->parent->_[HDOM_INFO_END] = $this->cursor;
            if ($this->parent->parent) {
                $this->parent = $this->parent->parent;
            }
            $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
            return true;
        }
        $node = new simple_html_dom_node($this);
        $node->_[HDOM_INFO_BEGIN] = $this->cursor;
        ++$this->cursor;
        $tag = $this->copy_until($this->token_slash);
        $node->tag_start = $begin_tag_pos;
        if (isset($tag[0]) && $tag[0] === '!') {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until_char('>');
            if (isset($tag[2]) && $tag[1] === '-' && $tag[2] === '-') {
                $node->nodetype = HDOM_TYPE_COMMENT;
                $node->tag = 'comment';
            } else {
                $node->nodetype = HDOM_TYPE_UNKNOWN;
                $node->tag = 'unknown';
            }
            if ($this->char === '>') {
                $node->_[HDOM_INFO_TEXT] .= '>';
            }
            $this->link_nodes($node, true);
            $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
            return true;
        }
        if ($pos = strpos($tag, '<') !== false) {
            $tag = '<' . substr($tag, 0, -1);
            $node->_[HDOM_INFO_TEXT] = $tag;
            $this->link_nodes($node, false);
            $this->char = $this->doc[--$this->pos];
            return true;
        }
        if (!preg_match('/^[\\w-:]+$/', $tag)) {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until('<>');
            if ($this->char === '<') {
                $this->link_nodes($node, false);
                return true;
            }
            if ($this->char === '>') {
                $node->_[HDOM_INFO_TEXT] .= '>';
            }
            $this->link_nodes($node, false);
            $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
            return true;
        }
        $node->nodetype = HDOM_TYPE_ELEMENT;
        $tag_lower = strtolower($tag);
        $node->tag = $this->lowercase ? $tag_lower : $tag;
        if (isset($this->optional_closing_tags[$tag_lower])) {
            while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
                $this->parent->_[HDOM_INFO_END] = 0;
                $this->parent = $this->parent->parent;
            }
            $node->parent = $this->parent;
        }
        $guard = 0;
        $space = array($this->copy_skip($this->token_blank), '', '');
        do {
            if ($this->char !== NULL && $space[0] === '') {
                break;
            }
            $name = $this->copy_until($this->token_equal);
            if ($guard === $this->pos) {
                $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
                continue;
            }
            $guard = $this->pos;
            if ($this->size - 1 <= $this->pos && $this->char !== '>') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = '<' . $tag . $space[0] . $name;
                $node->tag = 'text';
                $this->link_nodes($node, false);
                return true;
            }
            if ($this->doc[$this->pos - 1] == '<') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->tag = 'text';
                $node->attr = array();
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = substr($this->doc, $begin_tag_pos, $this->pos - $begin_tag_pos - 1);
                $this->pos -= 2;
                $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
                $this->link_nodes($node, false);
                return true;
            }
            if ($name !== '/' && $name !== '') {
                $space[1] = $this->copy_skip($this->token_blank);
                $name = $this->restore_noise($name);
                if ($this->lowercase) {
                    $name = strtolower($name);
                }
                if ($this->char === '=') {
                    $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
                    $this->parse_attr($node, $name, $space);
                } else {
                    $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                    $node->attr[$name] = true;
                    if ($this->char != '>') {
                        $this->char = $this->doc[--$this->pos];
                    }
                }
                $node->_[HDOM_INFO_SPACE][] = $space;
                $space = array($this->copy_skip($this->token_blank), '', '');
            } else {
                break;
            }
        } while ($this->char !== '>' && $this->char !== '/');
        $this->link_nodes($node, true);
        $node->_[HDOM_INFO_ENDSPACE] = $space[0];
        if ($this->copy_until_char_escape('>') === '/') {
            $node->_[HDOM_INFO_ENDSPACE] .= '/';
            $node->_[HDOM_INFO_END] = 0;
        } else {
            if (!isset($this->self_closing_tags[strtolower($node->tag)])) {
                $this->parent = $node;
            }
        }
        $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
        if ($node->tag == 'br') {
            $node->_[HDOM_INFO_INNER] = $this->default_br_text;
        }
        return true;
    }
    protected function parse_attr($node, $name, &$space)
    {
        if (isset($node->attr[$name])) {
            return NULL;
        }
        $space[2] = $this->copy_skip($this->token_blank);
        switch ($this->char) {
            case '"':
                $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
                $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
                $node->attr[$name] = $this->restore_noise($this->copy_until_char_escape('"'));
                $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
                break;
            case '\'':
                $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_SINGLE;
                $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
                $node->attr[$name] = $this->restore_noise($this->copy_until_char_escape('\''));
                $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
                break;
            default:
                $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                $node->attr[$name] = $this->restore_noise($this->copy_until($this->token_attr));
        }
        $node->attr[$name] = str_replace("\r", '', $node->attr[$name]);
        $node->attr[$name] = str_replace("\n", '', $node->attr[$name]);
        if ($name == 'class') {
            $node->attr[$name] = trim($node->attr[$name]);
        }
    }
    protected function link_nodes(&$node, $is_child)
    {
        $node->parent = $this->parent;
        $this->parent->nodes[] = $node;
        if ($is_child) {
            $this->parent->children[] = $node;
        }
    }
    protected function as_text_node($tag)
    {
        $node = new simple_html_dom_node($this);
        ++$this->cursor;
        $node->_[HDOM_INFO_TEXT] = '</' . $tag . '>';
        $this->link_nodes($node, false);
        $this->char = ++$this->pos < $this->size ? $this->doc[$this->pos] : NULL;
        return true;
    }
    protected function skip($chars)
    {
        $this->pos += strspn($this->doc, $chars, $this->pos);
        $this->char = $this->pos < $this->size ? $this->doc[$this->pos] : NULL;
    }
    protected function copy_skip($chars)
    {
        $pos = $this->pos;
        $len = strspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = $this->pos < $this->size ? $this->doc[$this->pos] : NULL;
        if ($len === 0) {
            return '';
        }
        return substr($this->doc, $pos, $len);
    }
    protected function copy_until($chars)
    {
        $pos = $this->pos;
        $len = strcspn($this->doc, $chars, $pos);
        $this->pos += $len;
        $this->char = $this->pos < $this->size ? $this->doc[$this->pos] : NULL;
        return substr($this->doc, $pos, $len);
    }
    protected function copy_until_char($char)
    {
        if ($this->char === NULL) {
            return '';
        }
        if (($pos = strpos($this->doc, $char, $this->pos)) === false) {
            $ret = substr($this->doc, $this->pos, $this->size - $this->pos);
            $this->char = NULL;
            $this->pos = $this->size;
            return $ret;
        }
        if ($pos === $this->pos) {
            return '';
        }
        $pos_old = $this->pos;
        $this->char = $this->doc[$pos];
        $this->pos = $pos;
        return substr($this->doc, $pos_old, $pos - $pos_old);
    }
    protected function copy_until_char_escape($char)
    {
        if ($this->char === NULL) {
            return '';
        }
        $start = $this->pos;
        while (1) {
            if (($pos = strpos($this->doc, $char, $start)) === false) {
                $ret = substr($this->doc, $this->pos, $this->size - $this->pos);
                $this->char = NULL;
                $this->pos = $this->size;
                return $ret;
            }
            if ($pos === $this->pos) {
                return '';
            }
            if ($this->doc[$pos - 1] === '\\') {
                $start = $pos + 1;
                continue;
            }
            $pos_old = $this->pos;
            $this->char = $this->doc[$pos];
            $this->pos = $pos;
            return substr($this->doc, $pos_old, $pos - $pos_old);
        }
    }
    protected function remove_noise($pattern, $remove_tag = false)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        $count = preg_match_all($pattern, $this->doc, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        for ($i = $count - 1; -1 < $i; --$i) {
            $key = '___noise___' . sprintf('% 5d', count($this->noise) + 1000);
            if (is_object($debugObject)) {
                $debugObject->debugLog(2, 'key is: ' . $key);
            }
            $idx = $remove_tag ? 0 : 1;
            $this->noise[$key] = $matches[$i][$idx][0];
            $this->doc = substr_replace($this->doc, $key, $matches[$i][$idx][1], strlen($matches[$i][$idx][0]));
        }
        $this->size = strlen($this->doc);
        if (0 < $this->size) {
            $this->char = $this->doc[0];
        }
    }
    public function restore_noise($text)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        while (($pos = strpos($text, '___noise___')) !== false) {
            if ($pos + 15 < strlen($text)) {
                $key = '___noise___' . $text[$pos + 11] . $text[$pos + 12] . $text[$pos + 13] . $text[$pos + 14] . $text[$pos + 15];
                if (is_object($debugObject)) {
                    $debugObject->debugLog(2, 'located key of: ' . $key);
                }
                if (isset($this->noise[$key])) {
                    $text = substr($text, 0, $pos) . $this->noise[$key] . substr($text, $pos + 16);
                } else {
                    $text = substr($text, 0, $pos) . 'UNDEFINED NOISE FOR KEY: ' . $key . substr($text, $pos + 16);
                }
            } else {
                $text = substr($text, 0, $pos) . 'NO NUMERIC NOISE KEY' . substr($text, $pos + 11);
            }
        }
        return $text;
    }
    public function search_noise($text)
    {
        global $debugObject;
        if (is_object($debugObject)) {
            $debugObject->debugLogEntry(1);
        }
        foreach ($this->noise as $noiseElement) {
            if (strpos($noiseElement, $text) !== false) {
                return $noiseElement;
            }
        }
    }
    public function __toString()
    {
        return $this->root->innertext();
    }
    public function __get($name)
    {
        switch ($name) {
            case 'outertext':
                return $this->root->innertext();
            case 'innertext':
                return $this->root->innertext();
            case 'plaintext':
                return $this->root->text();
            case 'charset':
                return $this->_charset;
            case 'target_charset':
                return $this->_target_charset;
        }
    }
    public function childNodes($idx = -1)
    {
        return $this->root->childNodes($idx);
    }
    public function firstChild()
    {
        return $this->root->first_child();
    }
    public function lastChild()
    {
        return $this->root->last_child();
    }
    public function createElement($name, $value = NULL)
    {
        return @str_get_html('<' . 'name' . '>' . $value . '</' . $name . '>')->first_child();
    }
    public function createTextNode($value)
    {
        return @end(str_get_html($value)->nodes);
    }
    public function getElementById($id)
    {
        return $this->find('#' . $id, 0);
    }
    public function getElementsById($id, $idx = NULL)
    {
        return $this->find('#' . $id, $idx);
    }
    public function getElementByTagName($name)
    {
        return $this->find($name, 0);
    }
    public function getElementsByTagName($name, $idx = -1)
    {
        return $this->find($name, $idx);
    }
    public function loadFile()
    {
        $args = func_get_args();
        $this->load_file($args);
    }
}
function file_get_html($url, $use_include_path = false, $context = NULL, $offset = -1, $maxLen = -1, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT)
{
    $dom = new simple_html_dom(NULL, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
    $contents = file_get_contents($url, $use_include_path, $context, $offset);
    if (empty($contents) || MAX_FILE_SIZE < strlen($contents)) {
        return false;
    }
    $dom->load($contents, $lowercase, $stripRN);
    return $dom;
}
function str_get_html($str, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT, $defaultSpanText = DEFAULT_SPAN_TEXT)
{
    $dom = new simple_html_dom(NULL, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
    if (empty($str) || MAX_FILE_SIZE < strlen($str)) {
        $dom->clear();
        return false;
    }
    $dom->load($str, $lowercase, $stripRN);
    return $dom;
}
function dump_html_tree($node, $show_attr = true, $deep = 0)
{
    $node->dump($node);
}
define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT', 3);
define('HDOM_TYPE_ENDTAG', 4);
define('HDOM_TYPE_ROOT', 5);
define('HDOM_TYPE_UNKNOWN', 6);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO', 3);
define('HDOM_INFO_BEGIN', 0);
define('HDOM_INFO_END', 1);
define('HDOM_INFO_QUOTE', 2);
define('HDOM_INFO_SPACE', 3);
define('HDOM_INFO_TEXT', 4);
define('HDOM_INFO_INNER', 5);
define('HDOM_INFO_OUTER', 6);
define('HDOM_INFO_ENDSPACE', 7);
define('DEFAULT_TARGET_CHARSET', 'UTF-8');
define('DEFAULT_BR_TEXT', "\r\n");
define('DEFAULT_SPAN_TEXT', ' ');
define('MAX_FILE_SIZE', 600000);