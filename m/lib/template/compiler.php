<?php

class JieqiCompiler extends JieqiTpl
{
    public $unite = false;
    public $tplinc = '';
    public $functions = array('noparam' => array('addslashes', 'htmlspecialchars', 'htmlentities', 'nl2br', 'rawurlencode', 'rawurldecode', 'bin2hex', 'strip_tags', 'stripslashes', 'strlen', 'strtolower', 'strtoupper', 'trim', 'ucfirst', 'ucwords', 'sizeof', 'basename', 'dirname', 'base64_encode', 'base64_decode', 'empty', 'is_array', 'isset', 'getdate', 'crc32', 'md5', 'count', 'ceil', 'floor', 'round', 'abs', 'urlencode', 'urldecode', 'intval', 'strval', 'serialize', 'subdirectory', 'is_array', 'yuan2fen', 'fen2yuan', 'htmlclickable'), 'right' => array('strrchr', 'strstr', 'strpos', 'str_pad', 'number_format', 'substr', 'wordwrap', 'truncate', 'arithmetic', 'defaultval', 'jieqi_geturl', 'in_array'), 'left' => array('date', 'implode', 'sprintf', 'str_replace', 'str_repeat'));
    public $regexp = array('sqstr' => '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"', 'dqstr' => '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'', 'qstr' => '(?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\')', 'set' => ' *set +([\\$a-zA-Z_0-9]+) *= *[\'"]?([^"]*)[\'"]? *', 'block' => ' *block (.*)', 'var' => ' *[\\$]([a-zA-Z_0-9]+.*) *', 'loop' => ' *section +name *=(.*)loop *=(.*)(columns *=(.*))?', 'if' => ' *(else if|elseif|if)(.*)(!=|>=|<=|==|>|<)(.*)', 'include' => ' *include +file *=(.*)', 'function' => ' *function ([a-zA-Z_0-9]+.*) *');
    public static $instance;
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new JieqiCompiler();
        }
        return self::$instance;
    }
    public function _addslashes($str)
    {
        return str_replace(array('\\', '\''), array('\\\\', '\\\''), $str);
    }
    public function _init_template_vars(&$template)
    {
        $this->template_dir = $template->template_dir;
        $this->compile_dir = $template->compile_dir;
        $this->force_compile = $template->force_compile;
        $this->caching = $template->caching;
        $this->left_delimiter = $template->left_delimiter;
        $this->right_delimiter = $template->right_delimiter;
        $this->left_comments = $template->left_comments;
        $this->right_comments = $template->right_comments;
        $this->_tpl_vars =& $template->_tpl_vars;
        $this->compile_id = $template->compile_id;
    }
    public function _compile_file(&$resource_name, $isfile = true)
    {
        $this->tplinc = '';
        if ($isfile) {
            $str = jieqi_readfile($resource_name);
        } else {
            $str =& $resource_name;
        }
        $rep_from = array('/' . $this->left_comments . '.*' . $this->right_comments . '/isU', '/<\\?(?!xml\\s+[a-z]).*\\?>/isU', '/<%.*%>/isU', '/<\\s*script[^>]+language\\s*=\\s*[\'"]?php[\'"]?.*>.*<\\/\\s*script\\s*>/isU');
        $rep_to = array('', '', '', '');
        $str = preg_replace($rep_from, $rep_to, $str);
        $htmlStrs = preg_split('/(' . $this->left_delimiter . '.*' . $this->right_delimiter . ')/isU', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        $str = '';
        $n = count($htmlStrs);
        $this->unite = false;
        for ($i = 0; $i < $n; $i++) {
            if (0 < strlen($htmlStrs[$i]) && $htmlStrs[$i] != "\n" && $htmlStrs[$i] != "\r\n") {
                if ($this->unite) {
                    $str .= '.\'' . $this->_addslashes($htmlStrs[$i]) . '\'';
                } else {
                    $str .= 'echo \'' . $this->_addslashes($htmlStrs[$i]) . '\'';
                }
                $this->unite = true;
            }
            $i++;
            if ($i < $n) {
                $tmpflag = $this->unite;
                $tmpvar = strval($this->gettplstr($htmlStrs[$i]));
                if ($tmpflag == true && $this->unite == true) {
                    $str .= '.' . $tmpvar;
                } else {
                    if ($tmpflag == true && $this->unite == false) {
                        $str .= ';' . "\r\n" . '' . $tmpvar;
                    } else {
                        if ($tmpflag == false && $this->unite == true) {
                            $str .= 'echo ' . $tmpvar;
                        } else {
                            if ($tmpflag == false && $this->unite == false) {
                                $str .= $tmpvar;
                            }
                        }
                    }
                }
            }
        }
        if ($this->unite) {
            $str .= ';';
        }
        unset($regs);
        unset($htmlStrs);
        return $str;
    }
    public function gettplstr($tplstr)
    {
        $regs = array();
        if (0 < preg_match('/' . $this->left_delimiter . ' *\\/(if|section) *' . $this->right_delimiter . '/isU', $tplstr, $regs)) {
            $ret = '}' . "\r\n" . '';
            $this->unite = false;
        } else {
            if (0 < preg_match('/' . $this->left_delimiter . ' *else *' . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                $ret = '}else{' . "\r\n" . '';
                $this->unite = false;
            } else {
                if (0 < preg_match('/' . $this->left_delimiter . $this->regexp['var'] . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                    return $this->getVar($regs);
                } else {
                    if (0 < preg_match('/' . $this->left_delimiter . $this->regexp['set'] . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                        $ret = $this->getSet($regs);
                    } else {
                        if (0 < preg_match('/' . $this->left_delimiter . $this->regexp['block'] . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                            return $this->getBlock($regs);
                        } else {
                            if (0 < preg_match('/' . $this->left_delimiter . $this->regexp['loop'] . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                                return $this->getLoop($regs);
                            } else {
                                if (0 < preg_match('/' . $this->left_delimiter . $this->regexp['if'] . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                                    return $this->getIf($regs);
                                } else {
                                    if (0 < preg_match('/' . $this->left_delimiter . $this->regexp['include'] . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                                        return $this->getInclude($regs);
                                    } else {
                                        if (0 < preg_match('/' . $this->left_delimiter . $this->regexp['function'] . $this->right_delimiter . '/isU', $tplstr, $regs)) {
                                            return $this->getFunction($regs);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($ret === false) {
            $this->unite = true;
            return '\'' . $this->_addslashes($tplstr) . '\'';
        } else {
            return $ret;
        }
    }
    public function getSet($regs)
    {
        $var = isset($regs[1]) ? $regs[1] : '';
        $value = isset($regs[2]) ? $regs[2] : '';
        $params = array();
        preg_match_all('/[\\$][\\w\\[\\]\\.\'"]+/i', $var, $params, PREG_SET_ORDER);
        $fromvar = array();
        $tovar = array();
        foreach ($params as $k => $v) {
            $fromvar[$k] = $v[0];
            $tovar[$k] = $this->getVarStr($v[0]);
        }
        $params = array();
        preg_match_all('/\\{?([\\$]\\w+[\\$\\w\\[\\]\\.\'"]*)\\}?/i', $value, $params, PREG_SET_ORDER);
        $fromval = array();
        $toval = array();
        foreach ($params as $k => $v) {
            $fromval[$k] = $v[0];
            $toval[$k] = '{' . $this->getVarStr($v[1]) . '}';
        }
        if (0 < strlen($var)) {
            $this->unite = false;
            $tmpvalue = empty($fromval) ? '\'' . $this->_addslashes(stripslashes($value)) . '\'' : '"' . str_replace($fromval, $toval, $value) . '"';
            if (empty($fromvar)) {
                $ret = '$GLOBALS[\'jieqiTset\'][\'' . $this->_addslashes(stripslashes($var)) . '\'] = ' . $tmpvalue . ';' . "\r\n" . '';
            } else {
                $ret = '@' . str_replace($fromvar, $tovar, $var) . ' = ' . $tmpvalue . ';' . "\r\n" . '';
            }
            $this->tplinc .= $ret;
            return '';
        } else {
            return false;
        }
    }
    public function getBlock($regs)
    {
        $blockconfig = isset($regs[1]) ? trim($regs[1]) : '';
        if (0 < strlen($blockconfig)) {
            preg_match_all('/([a-zA-Z_0-9]+) *= *[\'"]([^\'"]*)[\'"]/isU', $blockconfig, $bcs, PREG_SET_ORDER);
            $bcstr = '';
            foreach ($bcs as $bc) {
                if (!empty($bcstr)) {
                    $bcstr .= ', ';
                }
                $bcstr .= '\'' . $bc[1] . '\'=>\'' . $this->_addslashes(stripslashes($bc[2])) . '\'';
            }
            $this->unite = true;
            $ret = 'jieqi_get_block(array(' . $bcstr . '), 1)';
            return $ret;
        } else {
            return false;
        }
    }
    public function getFunction($funcs)
    {
        $func = isset($funcs[1]) ? trim($funcs[1]) : '';
        if (!empty($func)) {
            return $this->getFunctionStr($func);
        } else {
            return false;
        }
    }
    public function getFunctionStr($func, $varStr = '')
    {
        if (!empty($func)) {
            $func = str_replace(chr(0), '', $func);
            $funstrs = array();
            preg_match_all('/' . $this->regexp['qstr'] . '/i', $func, $funstrs);
            if (!empty($funstrs)) {
                $func = preg_replace('/' . $this->regexp['qstr'] . '/i', chr(0), $func);
            }
            $func = explode('|', $func);
            $p = 0;
            $i = 0;
            for ($n = count($func); $i < $n; $i++) {
                $cfunc = explode(':', $func[$i]);
                $funcname = trim($cfunc[0]);
                if (0 < strlen($funcname)) {
                    $param = array();
                    $j = 1;
                    for ($k = count($cfunc); $j < $k; $j++) {
                        $funcvars = array();
                        if (strpos($cfunc[$j], chr(0)) !== false) {
                            $tmpi = 0;
                            $tmpl = strlen($cfunc[$j]);
                            $tmps = '';
                            for ($m = 0; $m < $tmpl; $m++) {
                                if (0 < ord($cfunc[$j][$m])) {
                                    $tmps .= $cfunc[$j][$m];
                                } else {
                                    $tmps .= '\'' . $this->_addslashes(stripslashes(substr($funstrs[0][$p], 1, -1))) . '\'';
                                    $p++;
                                }
                            }
                            if (!preg_match('/^\\$([a-zA-Z_0-9]+.*)/is', $tmps, $funcvars)) {
                                $param[] = trim($tmps);
                            } else {
                                $param[] = trim($this->getVarStr($funcvars[1]));
                            }
                        } else {
                            $cfunc[$j] = trim($cfunc[$j]);
                            if (!preg_match('/^\\$([a-zA-Z_0-9]+.*)/is', $cfunc[$j], $funcvars)) {
                                $param[] = '\'' . $this->_addslashes($cfunc[$j]) . '\'';
                            } else {
                                $param[] = $this->getVarStr($funcvars[1]);
                            }
                        }
                    }
                    if (in_array($funcname, $this->functions['noparam'])) {
                        $varStr = $funcname . '(' . $varStr . ')';
                    } else {
                        if (in_array($funcname, $this->functions['right'])) {
                            $varStr = $varStr != '' ? $funcname . '(' . $varStr . ',' . implode(',', $param) . ')' : $funcname . '(' . implode(',', $param) . ')';
                        } else {
                            if (in_array($funcname, $this->functions['left'])) {
                                if ($funcname != 'date') {
                                    $varStr = $varStr != '' ? $funcname . '(' . implode(',', $param) . ',' . $varStr . ')' : $funcname . '(' . implode(',', $param) . ')';
                                } else {
                                    $varStr = $varStr != '' ? $funcname . '(\'' . str_replace('\'', '', implode(':', $param)) . '\',' . $varStr . ')' : $funcname . '(\'' . str_replace('\'', '', implode(':', $param)) . ')';
                                }
                            }
                        }
                    }
                }
            }
            $this->unite = true;
            return $varStr;
        } else {
            return false;
        }
    }
    public function getVar($regs)
    {
        $name = isset($regs[1]) ? trim($regs[1]) : '';
        $newStr = $this->getVarStr($name);
        if ($newStr !== false) {
            $this->unite = true;
            return $newStr;
        } else {
            return false;
        }
    }
    public function getVarStr($str)
    {
        if (!preg_match('/([a-zA-Z_0-9]+) *(\\[[^\\|]*\\])*((\\.[a-zA-Z_0-9]+)*)( *\\|.*)*/is', $str, $regs)) {
            return '';
        }
        $name = isset($regs[1]) ? $regs[1] : '';
        $cname = isset($regs[2]) ? $regs[2] : '';
        $sname = isset($regs[3]) ? $regs[3] : '';
        $func = isset($regs[5]) ? trim($regs[5]) : '';
        $cname = preg_replace('/\\[\\$([a-zA-Z_0-9]+)/i', '[$this->_tpl_vars[\'$1\']', $cname);
        $cname = preg_replace('/\\.([a-zA-Z_0-9]+)/i', '[\'$1\']', $cname);
        $cname = preg_replace('/\\[([^\'"\\[\\]]*)\\]/i', '[$this->_tpl_vars[\'$1\'][\'key\']]', $cname);
        $varStr = '$this->_tpl_vars[\'' . $name . '\']' . $cname;
        if (!empty($sname)) {
            $varStr .= preg_replace('/\\.([a-zA-Z_0-9]+)/i', '[\'$1\']', trim($sname));
        }
        if (!empty($func)) {
            return $this->getFunctionStr($func, $varStr);
        }
        unset($regs);
        return $varStr;
    }
    public function getLoop($regs)
    {
        $name = isset($regs[1]) ? trim($regs[1]) : '';
        $data = isset($regs[2]) ? trim($regs[2]) : '';
        $columns = isset($regs[4]) ? intval(trim($regs[4])) : 1;
        if ($columns < 1) {
            $columns = 1;
        }
        $dataStr = $this->getVarStr($data);
        $this->unite = false;
        if (!preg_match('/^\\w+$/i', $name) || strlen($dataStr) == 0) {
            return '';
        }
        return 'if (empty(' . $dataStr . ')) ' . $dataStr . ' = array();' . "\r\n" . 'elseif (!is_array(' . $dataStr . ')) ' . $dataStr . ' = (array)' . $dataStr . ';' . "\r\n" . '$this->_tpl_vars[\'' . $name . '\']=array();' . "\r\n" . '$this->_tpl_vars[\'' . $name . '\'][\'columns\'] = ' . $columns . ';' . "\r\n" . '$this->_tpl_vars[\'' . $name . '\'][\'count\'] = count(' . $dataStr . ');' . "\r\n" . '$this->_tpl_vars[\'' . $name . '\'][\'addrows\'] = count(' . $dataStr . ') % $this->_tpl_vars[\'' . $name . '\'][\'columns\'] == 0 ? 0 : $this->_tpl_vars[\'' . $name . '\'][\'columns\'] - count(' . $dataStr . ') % $this->_tpl_vars[\'' . $name . '\'][\'columns\'];' . "\r\n" . '$this->_tpl_vars[\'' . $name . '\'][\'loops\'] = $this->_tpl_vars[\'' . $name . '\'][\'count\'] + $this->_tpl_vars[\'' . $name . '\'][\'addrows\'];' . "\r\n" . 'reset(' . $dataStr . ');' . "\r\n" . 'for($this->_tpl_vars[\'' . $name . '\'][\'index\'] = 0; $this->_tpl_vars[\'' . $name . '\'][\'index\'] < $this->_tpl_vars[\'' . $name . '\'][\'loops\']; $this->_tpl_vars[\'' . $name . '\'][\'index\']++){' . "\r\n" . '	$this->_tpl_vars[\'' . $name . '\'][\'order\'] = $this->_tpl_vars[\'' . $name . '\'][\'index\'] + 1;' . "\r\n" . '	$this->_tpl_vars[\'' . $name . '\'][\'row\'] = ceil($this->_tpl_vars[\'' . $name . '\'][\'order\'] / $this->_tpl_vars[\'' . $name . '\'][\'columns\']);' . "\r\n" . '	$this->_tpl_vars[\'' . $name . '\'][\'column\'] = $this->_tpl_vars[\'' . $name . '\'][\'order\'] % $this->_tpl_vars[\'' . $name . '\'][\'columns\'];' . "\r\n" . '	if($this->_tpl_vars[\'' . $name . '\'][\'column\'] == 0) $this->_tpl_vars[\'' . $name . '\'][\'column\'] = $this->_tpl_vars[\'' . $name . '\'][\'columns\'];' . "\r\n" . '	if($this->_tpl_vars[\'' . $name . '\'][\'index\'] < $this->_tpl_vars[\'' . $name . '\'][\'count\']){' . "\r\n" . '		list($this->_tpl_vars[\'' . $name . '\'][\'key\'], $this->_tpl_vars[\'' . $name . '\'][\'value\']) = each(' . $dataStr . ');' . "\r\n" . '		$this->_tpl_vars[\'' . $name . '\'][\'append\'] = 0;' . "\r\n" . '	}else{' . "\r\n" . '		$this->_tpl_vars[\'' . $name . '\'][\'key\'] = \'\';' . "\r\n" . '		$this->_tpl_vars[\'' . $name . '\'][\'value\'] = \'\';' . "\r\n" . '		$this->_tpl_vars[\'' . $name . '\'][\'append\'] = 1;' . "\r\n" . '	}' . "\r\n" . '	';
    }
    public function getIf($regs)
    {
        $tplStr = isset($regs[0]) ? $regs[0] : '';
        if (preg_match('/[\\(\\);]/i', $tplStr)) {
            $this->unite = false;
            return '';
        }
        $fromary = array();
        $toary = array();
        $params = array();
        preg_match_all('/[$][^!=<>\\)\\s\\?]+/i', $tplStr, $params, PREG_SET_ORDER);
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $fromary[$k] = $v[0];
                $toary[$k] = $this->getVarStr($v[0]);
            }
        }
        $ifStr = isset($regs[1]) ? $regs[1] : '';
        $param1 = isset($regs[2]) ? $regs[2] : '';
        $operator = isset($regs[3]) ? $regs[3] : '';
        $param2 = isset($regs[4]) ? $regs[4] : '';
        $tmpStr = '';
        if (strtolower($ifStr) == 'if') {
            $tmpStr .= $ifStr;
        } else {
            $tmpStr .= '}' . $ifStr;
        }
        $tmpStr .= '(' . str_replace($fromary, $toary, trim($param1 . $operator . $param2)) . '){' . "\r\n" . '';
        $this->unite = false;
        return $tmpStr;
    }
    public function getInclude($regs)
    {
        $file = isset($regs[1]) ? trim($regs[1]) : '';
        if (!preg_match('/^[\'"].*[\'"]$/', $file)) {
            $file = $this->getVarStr($file);
        } else {
            $file = substr($file, 1, -1);
        }
        $expFile = explode('?', $file);
        $fileName =& $expFile[0];
        $this->unite = false;
        if (!preg_match('/\\.html?$/i', $fileName)) {
            return '';
        }
        if (isset($expFile[1])) {
            $expVars = explode('&', trim($expFile[1]));
            foreach ($expVars as $val) {
                if (!empty($val)) {
                    $expVar = explode('=', $val);
                    if (!empty($varstr)) {
                        $varstr .= ',';
                    }
                    $varstr .= '\'' . str_replace('\'', '"', $expVar[0]) . '\'=>\'' . str_replace('\'', '"', $expVar[1]) . '\'';
                }
            }
        }
        $varstr = 'array(' . $varstr . ')';
        return '$_template_tpl_vars = $this->_tpl_vars;' . "\r\n" . ' $this->_template_include(array(\'template_include_tpl_file\' => \'' . $fileName . '\', \'template_include_vars\' => ' . $varstr . '));' . "\r\n" . ' $this->_tpl_vars = $_template_tpl_vars;' . "\r\n" . ' unset($_template_tpl_vars);' . "\r\n" . '';
    }
}