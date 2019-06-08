<?php

class JieqiTpl
{
    public $template_dir = 'templates';
    public $compile_dir = 'compiled';
    public $compile_check = true;
    public $force_compile = false;
    public $caching = 0;
    public $cache_type = 0;
    public $cache_dir = 'cache';
    public $cache_lifetime = 3600;
    public $cache_overtime = 0;
    public $left_delimiter = '{\\?';
    public $right_delimiter = '\\?}';
    public $left_comments = '{\\*';
    public $right_comments = '\\*}';
    public $compile_id;
    public $_tpl_vars = array();
    public $_tmp_vars = array();
    public $_file_perms = 511;
    public $_dir_perms = 511;
    public $_compile_prefix = '.php';
    public $_include_prefix = '.inc.php';
    public static $instance;
    public function __construct()
    {
        global $jieqiModules;
        $this->template_dir = JIEQI_ROOT_PATH;
        $this->cache_dir = JIEQI_CACHE_PATH;
        $this->compile_dir = JIEQI_COMPILED_PATH;
        if (JIEQI_USE_CACHE) {
            $this->caching = 1;
        } else {
            $this->caching = 0;
        }
        $this->cache_lifetime = JIEQI_CACHE_LIFETIME;
        $this->assign(array('jieqi_url' => JIEQI_URL, 'jieqi_rootpath' => JIEQI_ROOT_PATH, 'jieqi_charset' => JIEQI_CHAR_SET, 'jieqi_version' => JIEQI_VERSION, 'jieqi_main_url' => JIEQI_MAIN_URL, 'jieqi_user_url' => JIEQI_USER_URL, 'jieqi_local_url' => JIEQI_LOCAL_URL, 'jieqi_pc_location' => JIEQI_PC_LOCATION, 'jieqi_mobile_location' => JIEQI_MOBILE_LOCATION, 'jieqi_device_for' => JIEQI_DEVICE_FOR, 'jieqi_theme' => JIEQI_THEME_NAME, 'jieqi_theme_rooturl' => JIEQI_THEME_ROOTURL, 'jieqi_themeurl' => JIEQI_THEME_ROOTURL . '/themes/' . JIEQI_THEME_NAME . '/', 'jieqi_sitename' => JIEQI_SITE_NAME, 'jieqi_email' => JIEQI_CONTACT_EMAIL, 'meta_keywords' => JIEQI_META_KEYWORDS, 'meta_description' => JIEQI_META_DESCRIPTION, 'meta_copyright' => JIEQI_META_COPYRIGHT, 'meta_author' => 'http://www.jieqi.com (jieqi cms)', 'jieqi_host' => JIEQI_LOCAL_HOST, 'jieqi_time' => JIEQI_NOW_TIME, 'jieqi_browser' => JIEQI_BROWSER_NAME, 'egoldname' => JIEQI_EGOLD_NAME, 'fun' => NULL));
        if (!empty($_REQUEST['ajax_request'])) {
            $this->assign('ajax_request', 1);
        } else {
            $this->assign('ajax_request', 0);
        }
        if (isset($_SESSION)) {
            $this->assign('jieqi_sessid', @session_id());
        }
        $this->assign_by_ref('jieqi_modules', $jieqiModules);
    }
    public static function getInstance()
    {
        if (empty(self::$instance) || !is_a(self::$instance, 'JieqiTpl')) {
            self::$instance = new JieqiTpl();
        }
        return self::$instance;
    }
    public function getCachType()
    {
        return $this->cache_type;
    }
    public function setCachType($num = 0)
    {
        $this->cache_type = (int) $num;
    }
    public function getCaching()
    {
        return $this->caching;
    }
    public function setCaching($num = 0)
    {
        $this->caching = (int) $num;
    }
    public function getCacheTime()
    {
        return $this->cache_lifetime;
    }
    public function setCacheTime($num = 0)
    {
        $this->cache_lifetime = (int) $num;
    }
    public function getOverTime()
    {
        return $this->cache_overtime;
    }
    public function setOverTime($num = 0)
    {
        $this->cache_overtime = (int) $num;
    }
    public function assign($tpl_var, $value = NULL)
    {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $key => $val) {
                if ($key != '') {
                    $this->_tpl_vars[$key] = $val;
                }
            }
        } else {
            if ($tpl_var != '') {
                $this->_tpl_vars[$tpl_var] = $value;
            }
        }
    }
    public function assign_by_ref($tpl_var, &$value)
    {
        if ($tpl_var != '') {
            $this->_tpl_vars[$tpl_var] =& $value;
        }
    }
    public function clear_assign($tpl_var)
    {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $curr_var) {
                unset($this->_tpl_vars[$curr_var]);
            }
        } else {
            unset($this->_tpl_vars[$tpl_var]);
        }
    }
    public function clear_all_assign()
    {
        $this->_tpl_vars = array();
    }
    public function get_assign($vname)
    {
        $keys = explode('.', $vname);
        $ret = false;
        $first = true;
        foreach ($keys as $key) {
            if ($first) {
                $ret = $this->_tpl_vars[$key];
            } else {
                $ret = $ret[$key];
            }
            $first = false;
        }
        return $ret;
    }
    public function get_all_assign()
    {
        return $this->_tpl_vars;
    }
    public function set_all_assign($vars)
    {
        $this->_tpl_vars = $vars;
    }
    public function clear_cache($tpl_file = NULL, $cache_id = NULL, $compile_id = NULL)
    {
        global $jieqiCache;
        if (!isset($compile_id)) {
            $compile_id = $this->compile_id;
        }
        if (!isset($tpl_file)) {
            $compile_id = NULL;
        }
        $_auto_id = $this->_get_auto_id($cache_id, $compile_id);
        $_tname = $this->_get_auto_filename($this->cache_dir, $tpl_file, $_auto_id);
        $jieqiCache->delete($_tname);
    }
    public function clear_all_cache()
    {
        global $jieqiCache;
        $jieqiCache->clear();
    }
    public function is_cached($tpl_file, $cache_id = NULL, $compile_id = NULL, $cache_time = NULL, $over_time = NULL, $return_value = false)
    {
        global $jieqiCache;
        if (!JIEQI_USE_CACHE) {
            return false;
        }
        if ($this->force_compile) {
            return false;
        }
        if (!isset($compile_id)) {
            $compile_id = $this->compile_id;
        }
        $_auto_id = $this->_get_auto_id($cache_id, $compile_id);
        $_cache_file = $this->_get_auto_filename($this->cache_dir, $tpl_file, $_auto_id);
        if (is_null($cache_time)) {
            $cache_time = $this->cache_lifetime;
        }
        if (is_null($over_time)) {
            $over_time = $this->cache_overtime;
        }
        if (empty($over_time) && $this->cache_type == 0) {
            $over_time = intval(@filemtime($tpl_file));
        }
        if (!$return_value) {
            return $jieqiCache->iscached($_cache_file, $cache_time, $over_time);
        } else {
            $_cache_data = $jieqiCache->get($_cache_file, $cache_time, $over_time);
            if ($this->cache_type == 1 && $_cache_data != false) {
                @eval('$_temp_vars = ' . trim($_cache_data) . ';');
                if (is_array($_temp_vars)) {
                    foreach ($_temp_vars as $k => $v) {
                        if (!isset($this->_tpl_vars[$k])) {
                            $this->_tpl_vars[$k] = $v;
                        }
                    }
                }
                unset($_temp_vars);
            }
            return $_cache_data;
        }
    }
    public function get_cachekey($tpl_file, $cache_id = NULL, $compile_id = NULL)
    {
        return $this->_get_auto_filename($this->cache_dir, $tpl_file, $this->_get_auto_id($cache_id, $compile_id));
    }
    public function get_cachedtime($tpl_file, $cache_id = NULL, $compile_id = NULL)
    {
        global $jieqiCache;
        $cachefile = $this->_get_auto_filename($this->cache_dir, $tpl_file, $this->_get_auto_id($cache_id, $compile_id));
        return $jieqiCache->cachedtime($cachefile);
    }
    public function update_cachedtime($tpl_file, $cache_id = NULL, $compile_id = NULL)
    {
        global $jieqiCache;
        $cachefile = $this->_get_auto_filename($this->cache_dir, $tpl_file, $this->_get_auto_id($cache_id, $compile_id));
        return $jieqiCache->uptime($cachefile);
    }
    public function clear_compiled_tpl($tpl_file = NULL, $compile_id = NULL)
    {
        if (!isset($compile_id)) {
            $compile_id = $this->compile_id;
        }
        $_tname = $this->_get_auto_filename($this->compile_dir, $tpl_file, $compile_id);
        @unlink($_tname . $this->_compile_prefix);
        @unlink($_tname . $this->_include_prefix);
    }
    public function template_exists($tpl_file)
    {
        return is_file($tpl_file);
    }
    public function get_template_vars($name = NULL)
    {
        if (!isset($name)) {
            return $this->_tpl_vars;
        }
        if (isset($this->_tpl_vars[$name])) {
            return $this->_tpl_vars[$name];
        }
    }
    public function get_compiled_inc($resource_name, $compile_id = NULL)
    {
        $resource_dir = dirname($resource_name);
        if (empty($resource_dir) || $resource_dir == '.') {
            $resource_name = $this->template_dir . '/' . $resource_name;
        }
        if (!isset($compile_id)) {
            $compile_id = $this->compile_id;
        }
        $_template_compile_path = $this->_get_compile_path($resource_name);
        if ($this->_is_compiled($resource_name, $_template_compile_path) || $this->_compile_resource($resource_name, $_template_compile_path)) {
            $incfile = $_template_compile_path . $this->_include_prefix;
            if (is_file($incfile)) {
                return $incfile;
            } else {
                return false;
            }
        }
    }
    public function include_compiled_inc($resource_name, $compile_id = NULL, $force = false)
    {
        $incfile = $this->get_compiled_inc($resource_name, $compile_id);
        if (!empty($incfile)) {
            if ($force) {
                include $incfile;
            } else {
                include_once $incfile;
            }
        }
    }
    public function display($resource_name, $cache_id = NULL, $compile_id = NULL, $cache_time = NULL, $over_time = NULL)
    {
        return $this->fetch($resource_name, $cache_id, $compile_id, $cache_time, $over_time, true);
    }
    public function fetch($resource_name, $cache_id = NULL, $compile_id = NULL, $cache_time = NULL, $over_time = NULL, $display = false)
    {
        global $jieqiCache;
        $resource_dir = dirname($resource_name);
        if (empty($resource_dir) || $resource_dir == '.') {
            $resource_name = $this->template_dir . '/' . $resource_name;
        }
        if (!isset($compile_id)) {
            $compile_id = $this->compile_id;
        }
        $_template_compile_path = $this->_get_compile_path($resource_name);
        if (is_null($cache_time)) {
            $cache_time = $this->cache_lifetime;
        }
        if (is_null($over_time)) {
            $over_time = $this->cache_overtime;
        }
        $save_cachevars = false;
        if ($this->caching == 1) {
            if ($this->cache_type == 1) {
                if ($this->is_cached($resource_name, $cache_id, $compile_id, $cache_time, $over_time, true) !== false) {
                    $save_cachevars = true;
                }
            } else {
                $_template_results = $this->is_cached($resource_name, $cache_id, $compile_id, $cache_time, $over_time, true);
                if (false !== $_template_results) {
                    if ($display) {
                        echo $_template_results;
                        return true;
                    } else {
                        return $_template_results;
                    }
                } else {
                    if ($display) {
                        header('Last-Modified: ' . date('D, d M Y H:i:s', JIEQI_NOW_TIME) . ' GMT');
                    }
                }
            }
        } else {
            if (0 < $this->caching) {
            }
        }
        ob_start();
        if ($this->_is_compiled($resource_name, $_template_compile_path) || $this->_compile_resource($resource_name, $_template_compile_path)) {
            include $_template_compile_path . $this->_compile_prefix;
        }
        $_template_results = ob_get_contents();
        ob_end_clean();
        if (0 < $this->caching) {
            $_auto_id = $this->_get_auto_id($cache_id, $compile_id);
            $_cache_file = $this->_get_auto_filename($this->cache_dir, $resource_name, $_auto_id);
            if ($this->cache_type == 1) {
                if (!$save_cachevars) {
                    $_cache_vars = array();
                    foreach ($this->_tpl_vars as $k => $v) {
                        if (substr($k, 0, 6) != 'jieqi_') {
                            $_cache_vars[$k] = $v;
                        }
                    }
                    $_template_vars = var_export($_cache_vars, true);
                    unset($_cache_vars);
                    $jieqiCache->set($_cache_file, $_template_vars, $cache_time, $over_time);
                }
            } else {
                $jieqiCache->set($_cache_file, $_template_results, $cache_time, $over_time);
            }
        }
        if ($display) {
            if (isset($_template_results)) {
                echo $_template_results;
            }
            return true;
        } else {
            if (isset($_template_results)) {
                return $_template_results;
            }
        }
    }
    public function parse_string($str, $retcode = false)
    {
        include_once TEMPLATE_DIR . 'compiler.php';
        $template_compiler = JieqiCompiler::getInstance();
        $template_compiler->_init_template_vars($this);
        $compiled_content = $template_compiler->_compile_file($str, false);
        if ($retcode) {
            return $compiled_content;
        } else {
            ob_start();
            eval($compiled_content);
            $results = ob_get_contents();
            ob_end_clean();
            return $results;
        }
    }
    public function _is_compiled($resource_name, $compile_path)
    {
        $compile_path .= $this->_compile_prefix;
        if (!$this->force_compile && file_exists($compile_path)) {
            if (!$this->compile_check) {
                return true;
            } else {
                if (!is_file($resource_name)) {
                    return false;
                }
                if (filemtime($resource_name) <= filemtime($compile_path)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
    public function _compile_resource($resource_name, $compile_path)
    {
        if (!is_file($resource_name)) {
            echo 'Template file (' . str_replace(JIEQI_ROOT_PATH, '', $resource_name) . ') is not exists!';
            return false;
        }
        $_resource_timestamp = filemtime($resource_name);
        $this->_compile_source($resource_name, $_compiled_content, $_compiled_include);
        $_compile_file = $compile_path . $this->_compile_prefix;
        if (jieqi_checkdir(dirname($_compile_file), true)) {
            $ret = jieqi_writefile($_compile_file, $_compiled_content);
            if ($ret && $_resource_timestamp) {
                @touch($_compile_file, $_resource_timestamp);
            }
        }
        if (0 < strlen($_compiled_include)) {
            $_compile_infile = $compile_path . $this->_include_prefix;
            if (jieqi_checkdir(dirname($_compile_infile), true)) {
                $ret1 = jieqi_writefile($_compile_infile, $_compiled_include);
                if ($ret1 && $_resource_timestamp) {
                    @touch($_compile_infile, $_resource_timestamp);
                }
            }
        } else {
            $this->_unlink($compile_path . $this->_include_prefix);
        }
        if ($ret && $_resource_timestamp) {
            @clearstatcache();
        }
        return $ret;
    }
    public function _compile_source($resource_name, &$compiled_content, &$compiled_include)
    {
        include_once TEMPLATE_DIR . 'compiler.php';
        $template_compiler = JieqiCompiler::getInstance();
        $template_compiler->_init_template_vars($this);
        $compiled_content = '<?php' . "\r\n" . $template_compiler->_compile_file($resource_name) . "\r\n" . '?>';
        $compiled_include = strlen($template_compiler->tplinc) == '' ? '' : '<?php' . "\r\n" . $template_compiler->tplinc . "\r\n" . '?>';
        return true;
    }
    public function _get_compile_path($resource_name)
    {
        return $this->_get_auto_filename($this->compile_dir, $resource_name, $this->compile_id);
    }
    public function _get_auto_filename($auto_base, $auto_source = NULL, $auto_id = NULL)
    {
        $_filename = basename($auto_source);
        $_dir = dirname($auto_source);
        $_return = str_replace(JIEQI_ROOT_PATH, $auto_base, $_dir);
        if ($_return == $_dir) {
            $_dir = trim(str_replace(array('\\', ':'), array('/', ''), $_dir));
            if ($dir[0] != '/') {
                $_return = $auto_base . '/' . $_dir;
            } else {
                $_return = $auto_base . $_dir;
            }
        }
        if (isset($auto_id) && 0 < strlen($auto_id)) {
            $_return .= '/' . $_filename;
            if (!preg_match('/^\\w+$/', $auto_id)) {
                $auto_id = md5($auto_id);
            }
            if (is_numeric($auto_id)) {
                $_return .= jieqi_getsubdir(intval($auto_id)) . '/' . $auto_id;
            } else {
                $l = strlen($auto_id);
                $p = 0;
                if (3 < $l) {
                    $_return .= '/' . substr($auto_id, 0, 3);
                    $p = 3;
                    if (8 < $l) {
                        $_return .= '/' . substr($auto_id, 3, 5);
                        $p = 8;
                        if (16 < $l) {
                            $_return .= '/' . substr($auto_id, 8, 8);
                            $p = 16;
                        }
                    }
                }
                if (0 < $p) {
                    $_return .= '/' . substr($auto_id, $p);
                } else {
                    $_return .= '/' . $auto_id;
                }
            }
            $_return .= strrchr($_filename, '.');
        } else {
            $_return .= '/' . $_filename;
        }
        return $_return;
    }
    public function _get_auto_id($cache_id = NULL, $compile_id = NULL)
    {
        if (isset($cache_id)) {
            return isset($compile_id) ? $cache_id . '|' . $compile_id : $cache_id;
        } else {
            if (isset($compile_id)) {
                return $compile_id;
            } else {
                return NULL;
            }
        }
    }
    public function _unlink($resource, $exp_time = NULL)
    {
        if (isset($exp_time)) {
            if ($exp_time <= JIEQI_NOW_TIME - @filemtime($resource)) {
                return @unlink($resource);
            }
        } else {
            return @unlink($resource);
        }
    }
    public function _template_include($params)
    {
        $this->_tpl_vars = array_merge($this->_tpl_vars, $params['template_include_vars']);
        $params['template_include_tpl_file'] = trim($params['template_include_tpl_file']);
        if ($params['template_include_tpl_file'][0] != '/' && $params['template_include_tpl_file'][1] != ':') {
            $params['template_include_tpl_file'] = $this->template_dir . '/' . $params['template_include_tpl_file'];
        }
        $_template_compile_path = $this->_get_compile_path($params['template_include_tpl_file']);
        if ($this->_is_compiled($params['template_include_tpl_file'], $_template_compile_path) || $this->_compile_resource($params['template_include_tpl_file'], $_template_compile_path)) {
            include $_template_compile_path . $this->_compile_prefix;
        }
    }
}
function truncate($str, $length = 10, $trimmarker = '', $html = 1)
{
    $start = 0;
    if ($html && (strpos($str, '<') !== false || strpos($str, '&') !== false)) {
        $length = $length - strlen($trimmarker);
        $len = strlen($str);
        $ret = '';
        $i = 0;
        $j = 0;
        $l = 0;
        $htmltag = '';
        $htmlflag = 0;
        $utf8 = JIEQI_SYSTEM_CHARSET == 'utf-8' ? true : false;
        while ($i < $len && $l < $length) {
            $cs = 1;
            $cl = 1;
            if ($str[$i] == '<') {
                $htmlflag = 1;
            } else {
                if ($str[$i] == '&') {
                    $htmlflag = 2;
                }
            }
            if (0 < $htmlflag) {
                $htmltag .= $str[$i];
                if ($htmlflag == 1 && $str[$i] == '>' || $htmlflag == 2 && $str[$i] == ';') {
                    if ($start <= $j) {
                        $ret .= $htmltag;
                    }
                    $htmlflag = 0;
                    $htmltag = '';
                }
            } else {
                $asc = ord($str[$i]);
                if (128 < $asc) {
                    if (!$utf8) {
                        $cs = 2;
                        $cl = 2;
                    } else {
                        if (192 <= $asc && $asc <= 223) {
                            $cs = 2;
                            $cl = 2;
                        } else {
                            if (224 <= $asc && $asc <= 239) {
                                $cs = 3;
                                $cl = 2;
                            } else {
                                if (240 <= $asc && $asc <= 247) {
                                    $cs = 4;
                                    $cl = 2;
                                }
                            }
                        }
                    }
                }
                if ($start <= $j) {
                    $ret .= substr($str, $i, $cs);
                    $l += $cl;
                }
            }
            $i += $cs;
            $j += $cl;
        }
        if ($i < $len) {
            $ret .= $trimmarker;
        }
        return $ret;
    } else {
        return jieqi_substr($str, $start, $length, $trimmarker);
    }
}
function htmlclickable($str)
{
    return jieqi_htmlclickable($str);
}
function arithmetic($str, $opt = '', $val = 0, $front = 0)
{
    $optary = array('+', '-', '*', '/', '%');
    if (is_numeric($str) && is_numeric($val) && in_array($opt, $optary)) {
        if (!$front) {
            eval('$ret = $str ' . $opt . ' $val;');
        } else {
            eval('$ret = $val ' . $opt . ' $str;');
        }
        return $ret;
    } else {
        return $str;
    }
}
function subdirectory($id)
{
    return jieqi_getsubdir($id);
}
function defaultval($str, $val)
{
    if (!isset($str) || empty($str) || is_array($str) && count($str) == 0) {
        $str = $val;
    }
    return $str;
}
function yuan2fen($money)
{
    return round(floatval($money) * 100);
}
function fen2yuan($money)
{
    return floatval(intval($money) / 100);
}
if (!defined('TEMPLATE_DIR')) {
    define('TEMPLATE_DIR', dirname(__FILE__) . '/');
}