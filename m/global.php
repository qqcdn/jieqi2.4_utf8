<?php
class JieqiObject
{
	public $vars = array();
	public $errors = array();

	public function __construct()
	{
	}

	static public function getInstance($classname = '', $valarray = '')
	{
		static $instance;

		if (is_null($instance)) {
			if (empty($classname)) {
				$classname = get_called_class();
			}

			if (class_exists($classname)) {
				if ($valarray == '') {
					$instance = new $classname();
				}
				else {
					$instance = new $classname($valarray);
				}
			}
			else {
				return false;
			}
		}

		return $instance;
	}

	public function getVar($key, $format = 's')
	{
		if (isset($this->vars[$key])) {
			if (is_string($this->vars[$key])) {
				switch (strtolower($format)) {
				case 's':
					return jieqi_htmlstr($this->vars[$key]);
				case 'e':
					return jieqi_htmlchars($this->vars[$key], ENT_QUOTES);
				case 'q':
					return jieqi_dbslashes($this->vars[$key]);
				case 'n':
				default:
					return $this->vars[$key];
				}
			}
			else {
				return $this->vars[$key];
			}
		}
		else {
			return false;
		}
	}

	public function getVars()
	{
		return $this->vars;
	}

	public function setVar($key, $value)
	{
		$this->vars[$key] = $value;
	}

	public function setVars($var_arr)
	{
		foreach ($var_arr as $key => $value) {
			$this->setVar($key, $value);
		}
	}

	public function clearVars()
	{
		$this->vars = array();
	}

	public function raiseError($message = 'unknown error!', $mode = JIEQI_ERROR_DIE)
	{
		switch ($mode) {
		case JIEQI_ERROR_DIE:
			jieqi_printfail($message);
			break;

		case JIEQI_ERROR_RETURN:
		case JIEQI_ERROR_PRINT:
			$this->errors[$mode][] = $message;
			break;

		default:
			$this->errors[JIEQI_ERROR_RETURN][] = $message;
			break;
		}
	}

	public function isError($mode = 0)
	{
		if ($mode == 0 || strlen($mode) == 0) {
			return count($this->errors);
		}
		else if (isset($this->errors[$mode])) {
			return count($this->errors[$mode]);
		}
		else {
			return 0;
		}
	}

	public function getErrors($mode = 0)
	{
		if ($mode == 0 || strlen($mode) == 0) {
			return $this->errors;
		}

		return $this->errors[$mode];
	}

	public function clearErrors($mode = 0)
	{
		if ($mode == 0 || strlen($mode) == 0) {
			$this->errors = array();
		}
		else {
			$this->errors[$mode] = array();
		}
	}
}

class JieqiBlock extends JieqiObject
{
	public $blockvars = array();
	public $module = '';
	public $template = '';
	public $cachetime = JIEQI_CACHE_LIFETIME;

	public function __construct(&$vars)
	{
		global $jieqiModules;
		global $jieqiTpl;
		$this->blockvars = $vars;

		if (empty($this->module)) {
			$this->module = empty($this->blockvars['module']) ? 'system' : $this->blockvars['module'];
		}

		if (empty($this->blockvars['template'])) {
			$this->blockvars['template'] = $this->template;
		}

		if (!empty($this->blockvars['template'])) {
			$this->blockvars['tlpfile'] = $jieqiModules[$this->module]['path'] . '/templates/blocks/' . $this->blockvars['template'];
		}
		else {
			$this->blockvars['tlpfile'] = '';
		}

		if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $this->blockvars['tlpfile']))) {
			$this->blockvars['tlpfile'] = str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $this->blockvars['tlpfile']);
		}

		if ($this->cachetime == 0) {
			$this->cachetime = JIEQI_CACHE_LIFETIME;
		}

		if (empty($this->blockvars['cachetime'])) {
			$this->blockvars['cachetime'] = $this->cachetime;
		}

		if (empty($this->blockvars['overtime'])) {
			$this->blockvars['overtime'] = 0;
		}

		if (empty($this->blockvars['cacheid'])) {
			$this->blockvars['cacheid'] = NULL;
		}

		if (empty($this->blockvars['compileid'])) {
			$this->blockvars['compileid'] = NULL;
		}

		if (!empty($this->blockvars['template'])) {
			$this->template = $this->blockvars['template'];
		}

		if (!is_object($jieqiTpl) && !empty($this->blockvars['tlpfile'])) {
			include_once JIEQI_ROOT_PATH . '/lib/template/template.php';
			$jieqiTpl = JieqiTpl::getInstance();
		}

		$jieqiTpl->setCachType(0);
	}

	public function getTitle()
	{
		return isset($this->blockvars['title']) ? $this->blockvars['title'] : '';
	}

	public function getContent()
	{
		global $jieqiTpl;
		if (JIEQI_USE_CACHE && !empty($this->blockvars['tlpfile']) && 0 < $this->blockvars['cachetime'] && $jieqiTpl->is_cached($this->blockvars['tlpfile'], $this->blockvars['cacheid'], $this->blockvars['compileid'], $this->blockvars['cachetime'], $this->blockvars['overtime'])) {
			$jieqiTpl->setCaching(1);
			$jieqiTpl->setCachType(0);
			return $jieqiTpl->fetch($this->blockvars['tlpfile'], $this->blockvars['cacheid'], $this->blockvars['compileid'], $this->blockvars['cachetime'], $this->blockvars['overtime'], false);
		}
		else {
			return $this->updateContent(true);
		}
	}

	public function updateContent($isreturn = false)
	{
		global $jieqiTpl;
		$this->setContent();

		if (!empty($this->blockvars['tlpfile'])) {
			if (JIEQI_USE_CACHE && 0 < $this->blockvars['cachetime']) {
				$jieqiTpl->setCaching(2);
				$jieqiTpl->setCachType(0);
			}
			else {
				$jieqiTpl->setCaching(0);
			}

			$tmpvar = $jieqiTpl->fetch($this->blockvars['tlpfile'], $this->blockvars['cacheid'], $this->blockvars['compileid'], $this->blockvars['cachetime'], $this->blockvars['overtime'], false);

			if ($isreturn) {
				return $tmpvar;
			}
		}
	}

	public function setContent($isreturn = false)
	{
	}
}

class JieqiCache extends JieqiObject
{
	static public $instance = array();

	static public function retInstance()
	{
		return self::$instance;
	}

	static public function close($cache = NULL)
	{
		if (is_object($cache)) {
			$cache->closec();
		}
		else if (!empty(self::$instance)) {
			foreach (self::$instance as $cache) {
				$cache->closec();
			}
		}
	}

	static public function getInstance($type = false, $options = array())
	{
		if (in_array(strtolower($type), array('file', 'memcached', 'redis'))) {
			$type = strtolower($type);
		}
		else {
			$type = 'file';
		}

		if (JIEQI_VERSION_TYPE == '' || JIEQI_VERSION_TYPE == 'Free') {
			$type = 'file';
		}

		$class = 'JieqiCache' . ucfirst($type);
		$inskey = md5($class . '::' . serialize($options));

		if (!isset(self::$instance[$inskey])) {
			self::$instance[$inskey] = new $class($options);
			if ($type != 'file' && self::$instance[$inskey] === false) {
				self::$instance[$inskey] = new JieqiCacheFile($options);
			}
		}

		if (!defined('JIEQI_CACHE_CONNECTED')) {
			@define('JIEQI_CACHE_CONNECTED', true);
		}

		return self::$instance[$inskey];
	}
}

class JieqiCacheFile extends JieqiCache
{

	public function __construct()
	{
		return true;
	}

	public function closec($cache = NULL)
	{
		return true;
	}

	public function iscached($name, $ttl = 0, $over = 0)
	{
		if ($ttl == 0 && $over == 0) {
			return is_file($name);
		}
		else {
			$ctime = $this->cachedtime($name);

			if (!$ctime) {
				return false;
			}

			if (0 < $ttl && $ttl < JIEQI_NOW_TIME - $ctime || 0 < $over && $ctime < $over) {
				$this->delete($name);
				return false;
			}
			else {
				return true;
			}
		}
	}

	public function cachedtime($name)
	{
		if (file_exists($name)) {
			return filemtime($name);
		}
		else {
			return 0;
		}
	}

	public function uptime($name)
	{
		@touch($name, time());
		@clearstatcache();
	}

	public function get($name, $ttl = 0, $over = 0)
	{
		if ($ttl == 0 && $over == 0) {
			return jieqi_readfile($name);
		}
		else {
			$ctime = $this->cachedtime($name);

			if (!$ctime) {
				return false;
			}

			if (0 < $ttl && $ttl < JIEQI_NOW_TIME - $ctime || 0 < $over && $ctime < $over) {
				jieqi_delfile($name);
				return false;
			}
			else {
				return jieqi_readfile($name);
			}
		}
	}

	public function set($name, $value, $ttl = 0, $over = 0)
	{
		if (jieqi_checkdir(dirname($name), true)) {
			return jieqi_writefile($name, $value);
		}
		else {
			return false;
		}
	}

	public function delete($name)
	{
		return jieqi_delfile($name);
	}

	public function clear($path = '')
	{
		if (0 < strlen($path) && is_dir($path)) {
			jieqi_delfolder($path);
		}
	}
}

class JieqiCacheMemcached extends JieqiCache
{
	public $_connected;
	public $_mc;
	public $_md5key = true;
	public $_keyext = '.mt';

	public function __construct($options)
	{
		if (!class_exists('Memcache')) {
			exit('Memcache class not exists');
		}

		if (!isset($options['host'])) {
			$options['host'] = '127.0.0.1';
		}

		if (!isset($options['port'])) {
			$options['port'] = 11211;
		}

		if (!isset($options['timeout'])) {
			$options['timeout'] = false;
		}

		if (!isset($options['persistent'])) {
			$options['persistent'] = false;
		}

		$func = $options['persistent'] ? 'pconnect' : 'connect';

		if (!is_object($this->_mc)) {
			$this->_mc = new Memcache();
		}

		$this->_connected = $options['timeout'] === false ? @$this->_mc->{$func}($options['host'], $options['port']) : @$this->_mc->func($options['host'], $options['port'], $options['timeout']);
		if (!$this->_connected && 0 < JIEQI_ERROR_MODE) {
			echo 'Could not connect to memcache and try to use file cache now!<br />';
		}

		return $this->_connected;
	}

	public function closec($cache = NULL)
	{
		if (is_object($this->_mc)) {
			return $this->_mc->close();
		}
		else {
			return true;
		}
	}

	public function iscached($name, $ttl = 0, $over = 0)
	{
		return $this->get($name, $ttl, $over) === false ? false : true;
	}

	public function cachedtime($name)
	{
		if ($this->_md5key) {
			$name = md5($name);
		}

		return intval($this->_mc->get($name . $this->_keyext));
	}

	public function uptime($name)
	{
		if ($this->_md5key) {
			$name = md5($name);
		}

		return $this->_mc->set($name . $this->_keyext, time(), 0, 0);
	}

	public function get($name, $ttl = 0, $over = 0)
	{
		$key = $this->_md5key == true ? md5($name) : $name;
		$ret = $this->_mc->get($key);
		if ($ret === false || $ttl == 0 && $over == 0) {
			return $ret;
		}
		else {
			$ctime = $this->cachedtime($name);
			if (0 < $ttl && $ttl < JIEQI_NOW_TIME - $ctime || 0 < $over && $ctime < $over) {
				$this->delete($name);
				return false;
			}
			else {
				return $ret;
			}
		}
	}

	public function set($name, $value, $ttl = 0, $over = 0)
	{
		if (2592000 < $ttl) {
			$ttl = 2592000;
		}

		if ($this->_md5key) {
			$name = md5($name);
		}

		if (JIEQI_NOW_TIME < $over && $over - JIEQI_NOW_TIME < $ttl) {
			$ttl = $over - JIEQI_NOW_TIME;
		}

		return $this->_mc->set($name . $this->_keyext, time(), 0, $ttl) && $this->_mc->set($name, $value, 0, $ttl);
	}

	public function delete($name)
	{
		if ($this->_md5key) {
			$name = md5($name);
		}

		return $this->_mc->delete($name . $this->_keyext) && $this->_mc->delete($name);
	}

	public function clear()
	{
		return $this->_mc->flush();
	}
}

class JieqiCacheRedis extends JieqiCache
{
	public $_connected;
	public $_co;
	public $_md5key = true;
	public $_keyext = '.ct';

	public function __construct($options)
	{
		if (!class_exists('Redis')) {
			exit('Redis class not exists');
		}

		if (!isset($options['host'])) {
			$options['host'] = '127.0.0.1';
		}

		if (!isset($options['port'])) {
			$options['port'] = 6379;
		}

		if (!isset($options['timeout'])) {
			$options['timeout'] = false;
		}

		if (!isset($options['persistent'])) {
			$options['persistent'] = false;
		}

		$func = $options['persistent'] ? 'pconnect' : 'connect';

		if (!is_object($this->_co)) {
			$this->_co = new Redis();
		}

		$this->_connected = $options['timeout'] === false ? @$this->_co->{$func}($options['host'], $options['port']) : @$this->_co->func($options['host'], $options['port'], $options['timeout']);

		if (!$this->_connected) {
			if (0 < JIEQI_ERROR_MODE) {
				echo 'Could not connect to redis and try to use file cache now!<br />';
			}
		}
		else if (!empty($options['pass'])) {
			$this->_co->auth($options['pass']);
		}

		return $this->_connected;
	}

	public function closec($cache = NULL)
	{
		if (is_object($this->_co)) {
			$this->_co = NULL;
		}

		return true;
	}

	public function iscached($name, $ttl = 0, $over = 0)
	{
		if ($ttl == 0 && $over == 0) {
			return $this->exists($name);
		}
		else {
			$ctime = $this->cachedtime($name);

			if (!$ctime) {
				return false;
			}

			if (0 < $ttl && $ttl < JIEQI_NOW_TIME - $ctime || 0 < $over && $ctime < $over) {
				$this->delete($name);
				return false;
			}
			else {
				return true;
			}
		}
	}

	public function cachedtime($name)
	{
		if ($this->_md5key) {
			$name = md5($name);
		}

		return intval($this->_co->get($name . $this->_keyext));
	}

	public function uptime($name)
	{
		if ($this->_md5key) {
			$name = md5($name);
		}

		return $this->_co->set($name . $this->_keyext, time(), 0, 0);
	}

	public function get($name, $ttl = 0, $over = 0)
	{
		$key = $this->_md5key == true ? md5($name) : $name;
		$ret = $this->_co->get($key);
		if ($ret === false || $ttl == 0 && $over == 0) {
			return $ret;
		}
		else {
			$ctime = $this->cachedtime($name);
			if (0 < $ttl && $ttl < JIEQI_NOW_TIME - $ctime || 0 < $over && $ctime < $over) {
				$this->delete($name);
				return false;
			}
			else {
				return $ret;
			}
		}
	}

	public function set($name, $value, $ttl = 0, $over = 0)
	{
		if (2592000 < $ttl) {
			$ttl = 2592000;
		}

		if ($this->_md5key) {
			$name = md5($name);
		}

		if (JIEQI_NOW_TIME < $over && $over - JIEQI_NOW_TIME < $ttl) {
			$ttl = $over - JIEQI_NOW_TIME;
		}

		if (0 < $ttl) {
			return $this->_co->setx($name . $this->_keyext, $ttl, time()) && $this->_co->setx($name, $ttl, $value);
		}
		else {
			return $this->_co->set($name . $this->_keyext, time()) && $this->_co->set($name, $value);
		}
	}

	public function delete($name)
	{
		if ($this->_md5key) {
			$name = md5($name);
		}

		return $this->_co->delete($name . $this->_keyext) && $this->_co->delete($name);
	}

	public function clear()
	{
		return $this->_co->flushDB();
	}
}

function jieqi_jumppage($url, $title = '', $content = '', $direct = NULL)
{
	if (!preg_match('/^(\\/\\w+|https?:\\/\\/)/i', $url)) {
		$url = JIEQI_LOCAL_URL;
	}

	if (strlen($content) == 0) {
		$direct = true;
	}
	else if ($direct === NULL) {
		$direct = defined('JIEQI_JUMP_DIRECT') && 0 < JIEQI_JUMP_DIRECT ? true : false;
	}

	if (empty($_REQUEST['ajax_request'])) {
		if ($direct == true) {
			if (!headers_sent()) {
				header('Location: ' . jieqi_headstr($url));
			}
			else {
				echo '<html><head><meta http-equiv="content-type" content="text/html; charset=' . JIEQI_CHAR_SET . '" /><meta http-equiv="refresh" content="0; url=' . $url . '" /><title>' . jieqi_htmlstr($title) . '</title></head><body>' . $content . '</body></html>';
			}
		}
		else {
			include_once JIEQI_ROOT_PATH . '/lib/template/template.php';
			$url = jieqi_htmlstr($url);
			$title = jieqi_htmlstr($title);
			$jieqiTpl = JieqiTpl::getInstance();
			$jieqiTpl->assign(array('jieqi_themecss' => JIEQI_THEME_ROOTURL . '/themes/' . JIEQI_THEME_NAME . '/style.css', 'pagetitle' => $title, 'title' => $title, 'content' => $content, 'url' => $url));
			$jieqiTpl->setCaching(0);
			$jieqiTpl->display(JIEQI_THEME_ROOTPATH . '/themes/' . JIEQI_THEME_NAME . '/jumppage.html');
		}
	}
	else {
		header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
		header('Cache-Control:no-cache');
		echo $url;
	}

	jieqi_freeresource();
	exit();
}

function jieqi_msgshow($title, $content, $type = 'msgwin')
{
	global $jieqiTset;
	global $jieqiTpl;
	global $jieqiGroups;

	if (!in_array($type, array('msgerr', 'msgwin', 'msgbox'))) {
		$type = 'msgwin';
	}

	if (defined('JIEQI_NOCONVERT_CHAR') && !empty($GLOBALS['charset_convert_out'])) {
		@ob_start($GLOBALS['charset_convert_out']);
	}

	$debuginfo = '';
	if (defined('JIEQI_DEBUG_MODE') && 0 < JIEQI_DEBUG_MODE) {
		$trace = debug_backtrace();
		$debuginfo = 'FILE: ' . jieqi_htmlstr($trace[0]['file']) . ' LINE:' . jieqi_htmlstr($trace[0]['line']);
	}

	if (empty($_REQUEST['ajax_request'])) {
		$jieqiTset['jieqi_contents_template'] = JIEQI_THEME_ROOTPATH . '/themes/' . JIEQI_THEME_NAME . '/' . $type . '.html';
		$tplvars = array('title' => $title, 'content' => $content, 'errorinfo' => $content, 'debuginfo' => $debuginfo, 'jieqi_sitename' => JIEQI_SITE_NAME);
		$tplvars['ispost'] = empty($_POST) ? 0 : 1;

		if ($type == 'msgbox') {
			if (!isset($jieqiTpl) || !is_a($jieqiTpl, 'JieqiTpl')) {
				include_once JIEQI_ROOT_PATH . '/lib/template/template.php';
				$jieqiTpl = JieqiTpl::getInstance();
			}

			$jieqiTpl->assign($tplvars);
			$jieqiTpl->setCaching(0);
			return $jieqiTpl->fetch($jieqiTset['jieqi_contents_template']);
		}
		else {
			if (preg_match('/\\/admin\\/\\w+\\.php$/i', $_SERVER['PHP_SELF'])) {
				include_once JIEQI_ROOT_PATH . '/admin/header.php';
			}
			else {
				include_once JIEQI_ROOT_PATH . '/header.php';
			}

			$jieqiTpl->assign($tplvars);
			$jieqiTpl->setCaching(0);

			if (defined('JIEQI_ADMIN_PAGE')) {
				include JIEQI_ROOT_PATH . '/admin/footer.php';
			}
			else {
				include JIEQI_ROOT_PATH . '/footer.php';
			}
		}
	}
	else {
		header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
		header('Cache-Control:no-cache');

		if ($type == 'msgbox') {
			return $content;
		}
		else {
			echo $content;
		}
	}

	jieqi_freeresource();
	exit();
}

function jieqi_msgbox($title, $content)
{
	jieqi_msgshow($title, $content, 'msgbox');
}

function jieqi_msgwin($title, $content)
{
	jieqi_msgshow($title, $content, 'msgwin');
}

function jieqi_printfail($content)
{
	jieqi_msgshow('', $content, 'msgerr');
}

function jieqi_flush()
{
	if (function_exists('apache_setenv')) {
		@apache_setenv('no-gzip', 1);
	}

	@ini_set('output_buffering', 0);
	@ini_set('zlib.output_compression', 0);
	@ini_set('implicit_flush', 1);

	for ($i = 0; $i < @ob_get_level(); $i++) {
		@ob_end_flush();
	}

	@ob_implicit_flush(1);
	echo str_repeat(' ', 4096);
	return true;
}

function jieqi_userip()
{
	$ip = '';

	if (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

	$ip = trim($ip);

	if (!is_numeric(str_replace('.', '', $ip))) {
		$ip = '';
	}

	return $ip;
}

function jieqi_getsubdir($id, $divnum = 1000)
{
	return '/' . floor(intval($id) / $divnum);
}

function jieqi_geturl($module, $target)
{
	global $jieqiModules;

	if (!isset($jieqiModules[$module])) {
		$module = 'system';
	}

	$funname = 'jieqi_url_' . $module . '_' . $target;
	if (!function_exists($funname) && isset($jieqiModules[$module]['path']) && is_file($jieqiModules[$module]['path'] . '/include/funurl.php')) {
		include_once $jieqiModules[$module]['path'] . '/include/funurl.php';
	}

	if (function_exists($funname)) {
		$numargs = func_num_args();
		$args = func_get_args();

		switch ($numargs) {
		case 0:
		case 1:
		case 2:
			return $funname();
			break;

		case 3:
			return $funname($args[2]);
			break;

		case 4:
			return $funname($args[2], $args[3]);
			break;

		case 5:
			return $funname($args[2], $args[3], $args[4]);
			break;

		case 6:
			return $funname($args[2], $args[3], $args[4], $args[5]);
			break;

		case 7:
			return $funname($args[2], $args[3], $args[4], $args[5], $args[6]);
			break;

		case 8:
		default:
			return $funname($args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
			break;
		}
	}
	else {
		return false;
	}
}

function jieqi_uploadpath($path, $module = '')
{
	if (strpos($path, '/') === false && strpos($path, '\\') === false) {
		if ($module == '' && defined('JIEQI_MODULE_NAME')) {
			$module = JIEQI_MODULE_NAME;
		}

		if ($path == '') {
			return JIEQI_FILES_PATH . '/' . $module;
		}
		else {
			return JIEQI_FILES_PATH . '/' . $module . '/' . $path;
		}
	}
	else {
		return $path;
	}
}

function jieqi_uploadurl($path, $url = '', $module = '', $systemurl = '')
{
	if (0 < strlen($url)) {
		return $url;
	}
	else {
		if ($module == '' && defined('JIEQI_MODULE_NAME')) {
			$module = JIEQI_MODULE_NAME;
		}

		if ($systemurl != '') {
			$tmpary = parse_url($systemurl);
			$systemurl = '';

			if (isset($tmpary['scheme'])) {
				$systemurl .= $tmpary['scheme'] . '://';
			}

			if (isset($tmpary['user']) && isset($tmpary['pass'])) {
				$systemurl .= $tmpary['user'] . ':' . $tmpary['pass'] . '@';
			}

			if (isset($tmpary['host'])) {
				$systemurl .= $tmpary['host'];
			}

			if (isset($tmpary['port'])) {
				$systemurl .= ':' . $tmpary['port'];
			}

			if (0 < strlen(JIEQI_URL)) {
				$tmpary1 = parse_url(JIEQI_URL);

				if (isset($tmpary1['path'])) {
					$systemurl .= $tmpary1['path'];
				}
			}
		}

		if ($systemurl == '') {
			if (defined('JIEQI_FILES_URL') && JIEQI_FILES_URL != '') {
				$filesurl = JIEQI_FILES_URL;
			}
			else {
				if (defined('JIEQI_FILES_DIR') && preg_match('/^\\w+$/', JIEQI_FILES_DIR)) {
					$filesurl = JIEQI_URL . '/' . JIEQI_FILES_DIR;
				}
				else {
					$filesurl = JIEQI_URL . '/files';
				}
			}
		}
		else {
			if (defined('JIEQI_FILES_DIR') && preg_match('/^\\w+$/', JIEQI_FILES_DIR)) {
				$filesurl = $systemurl . '/' . JIEQI_FILES_DIR;
			}
			else {
				$filesurl = $systemurl . '/files';
			}
		}

		if ($path == '') {
			return $filesurl . '/' . $module;
		}
		else {
			return $filesurl . '/' . $module . '/' . $path;
		}
	}
}

function jieqi_checkpower($powerset = array(), $ustatus = '0', $ugroup = '0', $isreturn = false, $isadmin = false)
{
	if (empty($_POST)) {
		$local_domain_url = empty($_SERVER['HTTP_HOST']) ? '' : 'http://' . $_SERVER['HTTP_HOST'];
		$jumpurl = $local_domain_url . jieqi_addurlvars(array());
	}
	else if (!empty($_SERVER['HTTP_REFERER'])) {
		$jumpurl = $_SERVER['HTTP_REFERER'];
	}
	else {
		$jumpurl = JIEQI_MAIN_URL;
	}

	if ((!isset($_SESSION['jieqiAdminLogin']) || $_SESSION['jieqiAdminLogin'] != 1) && !empty($_COOKIE['jieqiOnlineInfo'])) {
		$jieqi_online_info = jieqi_strtosary($_COOKIE['jieqiOnlineInfo']);

		if ($jieqi_online_info['jieqiAdminLogin'] == 1) {
			$_SESSION['jieqiAdminLogin'] = 1;
		}
	}

	if ($ustatus == JIEQI_GROUP_ADMIN) {
		if ($isadmin && empty($_SESSION['jieqiAdminLogin'])) {
			if ($isreturn) {
				return false;
			}
			else {
				header('Location: ' . jieqi_headstr(JIEQI_LOCAL_URL . '/admin/login.php?jumpurl=' . urlencode($jumpurl)));
				exit();
			}
		}
		else {
			return true;
		}
	}
	else {
		if (is_array($powerset['groups']) && (in_array($ugroup, $powerset['groups'], false) || in_array('0', $powerset['groups'], false))) {
			if ($isadmin && empty($_SESSION['jieqiAdminLogin'])) {
				if ($isreturn) {
					return false;
				}
				else {
					header('Location: ' . jieqi_headstr(JIEQI_LOCAL_URL . '/admin/login.php?jumpurl=' . urlencode($jumpurl)));
					exit();
				}
			}
			else {
				return true;
			}
		}
		else if ($isreturn) {
			return false;
		}
		else if ($ugroup == JIEQI_GROUP_GUEST) {
			if (empty($_REQUEST['ajax_request'])) {
				if ($isadmin) {
					header('Location: ' . jieqi_headstr(JIEQI_USER_URL . '/admin/login.php?jumpurl=' . urlencode($jumpurl)));
				}
				else {
					header('Location: ' . jieqi_headstr(JIEQI_USER_URL . '/login.php?jumpurl=' . urlencode($jumpurl)));
				}
			}
			else {
				header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
				header('Cache-Control:no-cache');
				echo LANG_NEED_LOGIN;
			}

			exit();
		}
		else {
			jieqi_printfail(LANG_NO_PERMISSION);
		}
	}
}

function jieqi_checklogin($isreturn = false, $isadmin = false)
{
	global $jieqiUsersGroup;

	if ($jieqiUsersGroup == JIEQI_GROUP_GUEST) {
		$ret = false;
	}
	else {
		$ret = true;
	}

	if ($isreturn) {
		return $ret;
	}
	else if (!$ret) {
		if (empty($_REQUEST['ajax_request'])) {
			if (empty($_POST)) {
				$local_domain_url = empty($_SERVER['HTTP_HOST']) ? '' : 'http://' . $_SERVER['HTTP_HOST'];
				$jumpurl = $local_domain_url . jieqi_addurlvars(array());
			}
			else if (!empty($_SERVER['HTTP_REFERER'])) {
				$jumpurl = $_SERVER['HTTP_REFERER'];
			}
			else {
				$jumpurl = JIEQI_MAIN_URL;
			}

			if ($isadmin) {
				header('Location: ' . jieqi_headstr(JIEQI_USER_URL . '/admin/login.php?jumpurl=' . urlencode($jumpurl)));
			}
			else {
				header('Location: ' . jieqi_headstr(JIEQI_USER_URL . '/login.php?jumpurl=' . urlencode($jumpurl)));
			}
		}
		else {
			header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
			header('Cache-Control:no-cache');
			echo LANG_NEED_LOGIN;
		}

		exit();
	}
}

function jieqi_checkpost($isreturn = false, $token = '')
{
	if (empty($token) && !empty($_POST[JIEQI_TOKEN_NAME])) {
		$token = $_POST[JIEQI_TOKEN_NAME];
	}

	if (!empty($_SESSION['jieqiUserToken']) && $_SESSION['jieqiUserToken'] != $token) {
		if ($isreturn) {
			return false;
		}
		else {
			jieqi_printfail(LANG_ERROR_CHECKING);
		}
	}
	else {
		return true;
	}
}

function jieqi_useraction($action, &$params)
{
	include_once JIEQI_ROOT_PATH . '/include/useraction.php';
	$fname = 'jieqi_user_' . $action;

	if (function_exists('jieqi_user_' . $action)) {
		return $fname($params);
	}
}

function jieqi_setusersession($user)
{
	global $jieqiHonors;
	global $jieqiModules;
	$_SESSION['jieqiUserId'] = intval($user->getVar('uid', 'n'));
	$_SESSION['jieqiUserUname'] = $user->getVar('uname', 'n');
	$_SESSION['jieqiUserName'] = 0 < strlen($user->getVar('name', 'n')) ? $user->getVar('name', 'n') : $user->getVar('uname', 'n');
	$_SESSION['jieqiUserPass'] = $user->getVar('pass', 'n');
	$_SESSION['jieqiUserGroup'] = $user->getVar('groupid', 'n');
	$_SESSION['jieqiUserEmail'] = $user->getVar('email', 'n');
	$_SESSION['jieqiUserAvatar'] = intval($user->getVar('avatar', 'n'));
	$_SESSION['jieqiUserScore'] = intval($user->getVar('score', 'n'));
	$_SESSION['jieqiUserExperience'] = intval($user->getVar('experience', 'n'));
	$_SESSION['jieqiUserVip'] = intval($user->getVar('isvip', 'n'));
	$_SESSION['jieqiUserEgold'] = intval($user->getVar('egold', 'n'));
	$_SESSION['jieqiUserChannel'] = $user->getVar('channel', 'n');
	$_SESSION['jieqiUserOvertime'] = intval($user->getVar('overtime', 'n'));
	$_SESSION['jieqiUserToken'] = md5(session_id() . intval($user->getVar('uid', 'n')) . $user->getVar('salt', 'n'));
	jieqi_getconfigs('system', 'honors');
	$honorid = intval(jieqi_gethonorid($user->getVar('score'), $jieqiHonors));
	$_SESSION['jieqiUserHonorId'] = $honorid;
	$_SESSION['jieqiUserHonor'] = isset($jieqiHonors[$honorid]['name'][intval($user->getVar('workid', 'n'))]) ? $jieqiHonors[$honorid]['name'][intval($user->getVar('workid', 'n'))] : $jieqiHonors[$honorid]['caption'];

	if (!empty($jieqiModules['badge']['publish'])) {
		$_SESSION['jieqiUserBadges'] = $user->getVar('badges', 'n');
	}

	$_SESSION['jieqiUserSet'] = jieqi_unserialize($user->getVar('setting', 'n'));
	$_SESSION['jieqiUserVerify'] = JieqiUsersHandler::extractUserset($user->getVar('verify', 'n'), 'verify');
}

function jieqi_upusersession($sid, $vars)
{
	if (empty($_SESSION['jieqiUserId'])) {
		return false;
	}

	$myuid = $_SESSION['jieqiUserId'];
	$mysid = session_id();
	$other = $sid == $mysid ? false : true;

	if ($other) {
		@session_write_close();
		@session_id(jieqi_headstr($sid));
		@session_start();
	}

	if (!empty($_SESSION['jieqiUserId']) && ($other == false || $other == true && $_SESSION['jieqiUserId'] != $myuid)) {
		if (!is_array($vars) || empty($vars)) {
			@session_unset();
			@session_destroy();
		}
		else {
			foreach ($vars as $k => $v) {
				$operator = substr($k, -1);

				if ($operator == '+') {
					$_SESSION[substr($k, 0, -1)] += $v;
				}
				else if ($operator == '-') {
					$_SESSION[substr($k, 0, -1)] -= $v;
				}
				else if ($operator == '*') {
					$_SESSION[substr($k, 0, -1)] *= $v;
				}
				else if ($operator == '/') {
					$_SESSION[substr($k, 0, -1)] /= $v;
				}
				else {
					$_SESSION[$k] = $v;
				}
			}
		}
	}

	$ret = $_SESSION;

	if ($other) {
		@session_write_close();
		@session_id($mysid);
		@session_start();
	}

	return $ret;
}

function jieqi_addurlvars($varary, $addget = true, $addpost = false, $filter = '')
{
	global $charset_convert_out;

	if (!empty($_SERVER['PHP_SELF'])) {
		$ret = $_SERVER['PHP_SELF'];
	}
	else {
		if (!empty($_SERVER['SCRIPT_NAME']) && substr($_SERVER['SCRIPT_NAME'], -4) == '.php') {
			$ret = $_SERVER['SCRIPT_NAME'];
		}
		else {
			$ret = '';
		}
	}

	$start = 0;

	if (!is_array($filter)) {
		$filter = array();
	}

	if ($addget) {
		foreach ($_GET as $k => $v) {
			if (!array_key_exists($k, $varary) && !in_array($k, $filter) && is_string($v) && 0 < strlen($v)) {
				if (!empty($charset_convert_out)) {
					$v = $charset_convert_out($v);
				}

				$ret .= 0 < $start++ ? '&' . $k . '=' . urlencode($v) : '?' . $k . '=' . urlencode($v);
			}
		}
	}

	if ($addpost) {
		foreach ($_POST as $k => $v) {
			if (!array_key_exists($k, $varary) && !in_array($k, $filter) && is_string($v) && 0 < strlen($v)) {
				if (!empty($charset_convert_out)) {
					$v = $charset_convert_out($v);
				}

				$ret .= 0 < $start++ ? '&' . $k . '=' . urlencode($v) : '?' . $k . '=' . urlencode($v);
			}
		}
	}

	if (is_array($varary)) {
		foreach ($varary as $k => $v) {
			$ret .= 0 < $start++ ? '&' . $k . '=' . $v : '?' . $k . '=' . $v;
		}
	}

	return $ret;
}

function jieqi_includedb()
{
	if (!defined('JIEQI_DBCLASS_INCLUDE')) {
		include_once JIEQI_ROOT_PATH . '/lib/database/database.php';
		define('JIEQI_DBCLASS_INCLUDE', true);
	}
}

function jieqi_closedb($db = NULL)
{
	if (defined('JIEQI_DB_CONNECTED') && !defined('JIEQI_DB_NOTCLOSE') && JIEQI_DB_PCONNECT == false) {
		JieqiDatabase::close($db);
	}
}

function jieqi_closeftp($ftp = NULL)
{
	if (defined('JIEQI_FTP_CONNECTED') && !defined('JIEQI_FTP_NOTCLOSE')) {
		JieqiFTP::close($ftp);
	}
}

function jieqi_closecache($cache = NULL)
{
	if (defined('JIEQI_CACHE_CONNECTED') && !defined('JIEQI_CACHE_NOTCLOSE')) {
		JieqiCache::close($cache);
	}
}

function jieqi_freeresource()
{
	jieqi_closedb();
	jieqi_closeftp();
	jieqi_closecache();
}

function jieqi_loadlang($fname, $module = 'system')
{
	global $jieqiLang;
	global $jieqiModules;

	if (empty($jieqiLang[$module][$fname])) {
		if ($module == 'system' || $module == '') {
			$file = JIEQI_ROOT_PATH . '/lang/lang_' . $fname . '.php';
		}
		else {
			$file = $jieqiModules[$module]['path'] . '/lang/lang_' . $fname . '.php';
		}

		$file = @realpath($file);
		if (is_file($file) && preg_match('/\\.php$/i', $file)) {
			include_once $file;
			return true;
		}
		else {
			return false;
		}
	}
}

function jieqi_gethonorid($userscore = 0, $jieqiHonors = array())
{
	if (is_array($jieqiHonors)) {
		foreach ($jieqiHonors as $k => $v) {
			if ($v['minscore'] <= $userscore && $userscore < $v['maxscore']) {
				return $k;
			}
		}
	}

	return false;
}

function jieqi_ismobile()
{
	if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
		return true;
	}
	else {
		if (isset($_SERVER['HTTP_VIA']) && stripos($_SERVER['HTTP_VIA'], 'wap') !== false) {
			return true;
		}
		else {
			if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|phone|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|wap|windows ce|xda |xda_|xoom)/i', $_SERVER['HTTP_USER_AGENT'])) {
				return true;
			}
			else {
				if (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'text/html') === false && (stripos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false || stripos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.xhtml+xml') !== false)) {
					return true;
				}
				else {
					return false;
				}
			}
		}
	}
}

function jieqi_htmlchars($str, $flags = ENT_COMPAT, $encoding = 'ISO-8859-1')
{
	return htmlspecialchars($str, $flags, $encoding);
}

function jieqi_htmlstr($str, $flags = ENT_QUOTES)
{
	$str = jieqi_htmlchars($str, $flags);
	$str = nl2br($str);
	$str = str_replace(array('  ', '&amp;'), array('&emsp;', '&'), $str);
	return $str;
}

function jieqi_htmlclickable($str, $styles = NULL)
{
	$patterns = array();
	$replacements = array();

	if (empty($styles)) {
		$styles = array();
	}

	$k = -1;
	$k++;
	$patterns[$k] = '/&lt;a(\\s+((?!&gt;|&lt;).)+\\s*)&gt;(((?!&gt;|&lt;).)*)&lt;\\/a&gt;/i';
	$replacements[$k] = '<a$1>$3</a>';
	$k++;
	$patterns[$k] = '/(href|target|title|class)\\s*=\\s*(?:&quot;|&#039;)([^<>;]*)(?:&quot;|&#039;)/i';
	$replacements[$k] = '$1="$2"';
	$k++;
	$patterns[$k] = '/(?<=[^\\[\\]="\'\\/\\.<>]|^)((https?|ftp):\\/\\/)([^\\s\\r\\n\\t\\f<>]+(\\.gif|\\.jpg|\\.jpeg|\\.png|\\.bmp))/i';
	$replacements[$k] = '<img src="$1$3"';

	if (!isset($styles['imgclass'])) {
		$styles['imgclass'] = 'imagecontent';
	}

	if (!empty($styles['imgclass'])) {
		$replacements[$k] .= ' class="' . $styles['imgclass'] . '"';
	}

	if (!empty($styles['imgresize'])) {
		$replacements[$k] .= ' onload="imgResize(this);" onmouseover="imgMenu(this);" onclick="imgDialog(\'$1$3\', this);"';
	}
	else if (!isset($styles['imgresize'])) {
		$replacements[$k] .= ' onload="imgResize(this);"';
	}

	$replacements[$k] .= ' />';

	if (!isset($styles['imgdiv'])) {
		$styles['imgdiv'] = 'divimage';
	}

	if (!empty($styles['imgdiv'])) {
		$replacements[$k] = '<div class="' . $styles['imgdiv'] . '">' . $replacements[$k] . '</div>';
	}

	$k++;
	$patterns[$k] = '/(?<=[^\\[\\]="\'\\/\\.<>]|^)((https?|ftp):\\/\\/)([^\\s\\r\\n\\t\\f<>]+(\\.txt|\\.zip|\\.rar|\\.gz|\\.tar))/i';
	$replacements[$k] = '<a href="$1$3"';

	if (!isset($styles['target'])) {
		$styles['target'] = '_blank';
	}

	if (!empty($styles['target'])) {
		$replacements[$k] .= ' target="' . $styles['target'] . '"';
	}

	if (!isset($styles['fileclass'])) {
		$styles['fileclass'] = 'filecontent';
	}

	if (!empty($styles['fileclass'])) {
		$replacements[$k] .= ' class="' . $styles['fileclass'] . '"';
	}

	$replacements[$k] .= '>$1$3</a>';
	$k++;
	$patterns[$k] = '/(?<=[^\\[\\]="\'\\/\\.<>a-z0-9-]|^)([a-z]+?):\\/\\/([a-z0-9\\/\\-_+=.~!%@?#%&;:$\\│]+)/i';
	$replacements[$k] = '<a href="$1://$2"';

	if (!isset($styles['target'])) {
		$styles['target'] = '_blank';
	}

	if (!empty($styles['target'])) {
		$replacements[$k] .= ' target="' . $styles['target'] . '"';
	}

	if (!isset($styles['linkclass'])) {
		$styles['linkclass'] = 'linkcontent';
	}

	if (!empty($styles['linkclass'])) {
		$replacements[$k] .= ' class="' . $styles['linkclass'] . '"';
	}

	$replacements[$k] .= '>$1://$2</a>';
	$k++;
	$patterns[$k] = '/(?<=[^\\[\\]="\'\\/\\.<>]|^)www\\.([a-z0-9\\-]+)\\.([a-z0-9\\/\\-_+=.~!%@?#%&;:$\\│]+)/i';
	$replacements[$k] = '<a href="http://www.$1.$2"';

	if (!isset($styles['target'])) {
		$styles['target'] = '_blank';
	}

	if (!empty($styles['target'])) {
		$replacements[$k] .= ' target="' . $styles['target'] . '"';
	}

	if (!isset($styles['linkclass'])) {
		$styles['linkclass'] = 'linkcontent';
	}

	if (!empty($styles['linkclass'])) {
		$replacements[$k] .= ' class="' . $styles['linkclass'] . '"';
	}

	$replacements[$k] .= '>www.$1.$2</a>';
	$k++;
	$patterns[$k] = '/(?<=[^\\[\\]="\'\\/\\.<>]|^)([a-z0-9\\-_\\.]{3,})@([a-z0-9\\/\\-_+=.~!%@?#%&;:$\\│]+)/i';
	$replacements[$k] = '<a href="mailto:$1@$2">$1@$2</a>';
	return preg_replace($patterns, $replacements, $str);
}

function jieqi_substr($str, $start, $length, $trimmarker = '...')
{
	if (function_exists('mb_strimwidth')) {
		return mb_strimwidth($str, $start, $length, $trimmarker);
	}

	$length = $length - strlen($trimmarker);
	$len = strlen($str);
	$ret = '';
	$i = 0;
	$j = 0;
	$l = 0;
	$utf8 = JIEQI_SYSTEM_CHARSET == 'utf-8' ? true : false;

	while ($i < $len && $l < $length) {
		$cs = 1;
		$cl = 1;
		$asc = ord($str[$i]);

		if (128 < $asc) {
			if (!$utf8) {
				$cs = 2;
				$cl = 2;
			}
			else {
				if (192 <= $asc && $asc <= 223) {
					$cs = 2;
					$cl = 2;
				}
				else {
					if (224 <= $asc && $asc <= 239) {
						$cs = 3;
						$cl = 2;
					}
					else {
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

		$i += $cs;
		$j += $cl;
	}

	if ($i < $len) {
		$ret .= $trimmarker;
	}

	return $ret;
}

function jieqi_strwords($str)
{
	if (defined('JIEQI_SYSTEM_SBYTE') && 0 < JIEQI_SYSTEM_SBYTE) {
		return count(preg_split('/\\s+/', $str, -1, PREG_SPLIT_NO_EMPTY));
	}
	else {
		$utf8 = JIEQI_SYSTEM_CHARSET == 'utf-8' ? true : false;
		$len = strlen($str);
		$words = 0;
		$i = 0;

		while ($i < $len) {
			$asc = ord($str[$i]);

			if (128 < $asc) {
				if (!$utf8) {
					$i += 2;
				}
				else {
					if (192 <= $asc && $asc <= 223) {
						$i += 2;
					}
					else {
						if (224 <= $asc && $asc <= 239) {
							$i += 3;
						}
						else {
							if (240 <= $asc && $asc <= 247) {
								$i += 4;
							}
						}
					}
				}

				$words++;
			}
			else {
				$cs = 0;

				while ($i < $len && $asc <= 128 && $asc != 32 && ($asc < 9 || 13 < $asc)) {
					$cs++;
					$i++;
					$asc = ord($str[$i]);
				}

				if (0 < $cs) {
					$words++;
				}

				while ($i < $len && ($asc == 32 || 9 <= $asc && $asc <= 13)) {
					$i++;
					$asc = ord($str[$i]);
				}
			}
		}

		return $words;
	}
}

function jieqi_sizeformat($size, $type = 'c', $precision = 1, $wordlen = 0)
{
	if (empty($wordlen)) {
		$wordlen = defined('JIEQI_SYSTEM_SBYTE') && 0 < JIEQI_SYSTEM_SBYTE ? 1 : (JIEQI_SYSTEM_CHARSET == 'utf-8' ? 3 : 2);
	}

	switch ($type) {
	case 'c':
		return ceil($size / $wordlen);
		break;

	case 'k':
		return ceil($size / $wordlen / 1000);
		break;

	case 'w':
		return round($size / $wordlen / 10000, $precision);
		break;

	default:
		return $size;
		break;
	}
}

function jieqi_wordsformat($size, $type = 'c', $precision = 1)
{
	jieqi_sizeformat($size, $type, $precision, 1);
}

function jieqi_strsize($str)
{
	$wordlen = defined('JIEQI_SYSTEM_SBYTE') && 0 < JIEQI_SYSTEM_SBYTE ? 1 : (JIEQI_SYSTEM_CHARSET == 'utf-8' ? 3 : 2);
	return jieqi_strwords($str) * $wordlen;
}

function jieqi_funtoarray($funname, $ary)
{
	if (is_array($ary)) {
		foreach ($ary as $k => $v) {
			if (is_string($v)) {
				$ary[$k] = call_user_func($funname, $v);
			}
			else if (is_array($v)) {
				$ary[$k] = jieqi_funtoarray($funname, $v);
			}
		}
	}
	else {
		$ary = call_user_func($funname, $ary);
	}

	return $ary;
}

function jieqi_dbprefix($tbname, $fullname = false)
{
	if (JIEQI_DB_PREFIX == '' || $fullname) {
		return $tbname;
	}
	else {
		return JIEQI_DB_PREFIX . '_' . $tbname;
	}
}

function jieqi_setslashes($str, $filter = '')
{
	if ($filter == '"') {
		return str_replace(array('\\', '\''), array('\\\\', '\\\''), $str);
	}
	else if ($filter == '\'') {
		return str_replace(array('\\', '"'), array('\\\\', '\\"'), $str);
	}
	else {
		return addslashes($str);
	}
}

function jieqi_dbslashes($str, $use_slashes = false)
{
	if ($use_slashes) {
		return $str;
	}
	else {
		if (JIEQI_SYSTEM_CHARSET == 'big5' && JIEQI_DB_CHARSET != 'big5') {
			$str = strval($str);
			$l = strlen($str);
			$ret = '';

			for ($i = 0; $i < $l; $i++) {
				$o = ord($str[$i]);

				if (128 < $o) {
					$ret .= $str[$i] . $str[$i + 1];
					$i++;
				}
				else {
					if ($o == 0 || $o == 34 || $o == 39 || $o == 92) {
						$ret .= chr(92) . $str[$i];
					}
					else {
						$ret .= $str[$i];
					}
				}
			}

			return $ret;
		}
		else {
			return addslashes($str);
		}
	}
}

function jieqi_sarytostr($ary, $equal = '=', $split = ',')
{
	$ret = '';

	foreach ($ary as $k => $v) {
		if (!empty($ret)) {
			$ret .= $split;
		}

		$ret .= $k . $equal . $v;
	}

	return $ret;
}

function jieqi_strtosary($str, $equal = '=', $split = ',')
{
	$ret = array();
	$tmpary = explode($split, $str);

	foreach ($tmpary as $v) {
		$idx = strpos($v, $equal);

		if (0 < $idx) {
			$ret[substr($v, 0, $idx)] = substr($v, $idx + 1);
		}
	}

	return $ret;
}

function jieqi_unserialize($str)
{
	$ret = @unserialize($str);

	if ($ret === false) {
		$ret = @unserialize(preg_replace_callback('/s:([0-9]+?):"([\\s\\S]*?)";/', function($m) {
			return 's:' . strlen($m[2]) . ':' . '"' . $m[2] . '";';
		}, $str));
	}

	return $ret;
}

function jieqi_headstr($str)
{
	return trim(strip_tags(str_replace(array('
', '
', '	', '\''), '', $str)));
}

function jieqi_charsetconvert($data, $fromset = '', $toset = '')
{
	global $jieqi_charset_map;
	$fromset = empty($fromset) ? strtolower(JIEQI_SYSTEM_CHARSET) : strtolower($fromset);
	$toset = empty($toset) ? strtolower(JIEQI_SYSTEM_CHARSET) : strtolower($toset);

	if ($jieqi_charset_map[$fromset] != $jieqi_charset_map[$toset]) {
		include_once JIEQI_ROOT_PATH . '/include/changecode.php';

		if (is_array($data)) {
			return jieqi_funtoarray('jieqi_' . $jieqi_charset_map[$fromset] . '2' . $jieqi_charset_map[$toset], $data);
		}
		else {
			return call_user_func('jieqi_' . $jieqi_charset_map[$fromset] . '2' . $jieqi_charset_map[$toset], $data);
		}
	}
	else {
		return $data;
	}
}

function jieqi_readfile($file_name)
{
	if (function_exists('file_get_contents')) {
		return @file_get_contents($file_name);
	}
	else {
		$filenum = @fopen($file_name, 'rb');
		@flock($filenum, LOCK_SH);
		$file_data = @fread($filenum, @filesize($file_name));
		@flock($filenum, LOCK_UN);
		@fclose($filenum);
		return $file_data;
	}
}

function jieqi_writefile($file_name, $data, $method = 'wb')
{
	$filenum = @fopen($file_name, $method);

	if (!$filenum) {
		return false;
	}

	@flock($filenum, LOCK_EX);
	$ret = @fwrite($filenum, $data);
	@flock($filenum, LOCK_UN);
	@fclose($filenum);
	@chmod($file_name, 511);
	return $ret;
}

function jieqi_delfile($file_name)
{
	$file_name = trim($file_name);
	$matches = array();

	if (!preg_match('/^(ftps?):\\/\\/([^:\\/]+):([^:\\/]*)@([0-9a-z\\-\\.]+)(:(\\d+))?([0-9a-z_\\-\\/\\.]*)/is', $file_name, $matches)) {
		return @unlink($file_name);
	}
	else {
		include_once JIEQI_ROOT_PATH . '/lib/ftp/ftp.php';
		$ftpssl = strtolower($matches[1]) == 'ftps' ? 1 : 0;
		$matches[6] = intval(trim($matches[6]));
		$ftpport = 0 < $matches[6] ? $matches[6] : 21;
		$ftp = JieqiFTP::getInstance($matches[4], $matches[2], $matches[3], '.', $ftpport, 0, $ftpssl);

		if (!$ftp) {
			return false;
		}

		$matches[7] = trim($matches[7]);
		return $ftp->ftp_delete($matches[7]);
	}
}

function jieqi_delfolder($dirname, $flag = true)
{
	$dirname = trim($dirname);
	$matches = array();

	if (!preg_match('/^(ftps?):\\/\\/([^:\\/]+):([^:\\/]*)@([0-9a-z\\-\\.]+)(:(\\d+))?([0-9a-z_\\-\\/\\.]*)/is', $dirname, $matches)) {
		$handle = @opendir($dirname);

		if ($handle === false) {
			return true;
		}

		while (($file = @readdir($handle)) !== false) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
					jieqi_delfolder($dirname . DIRECTORY_SEPARATOR . $file, true);
				}
				else {
					@unlink($dirname . DIRECTORY_SEPARATOR . $file);
				}
			}
		}

		@closedir($handle);

		if ($flag) {
			@rmdir($dirname);
		}

		return true;
	}
	else {
		include_once JIEQI_ROOT_PATH . '/lib/ftp/ftp.php';
		$ftpssl = strtolower($matches[1]) == 'ftps' ? 1 : 0;
		$matches[6] = intval(trim($matches[6]));
		$ftpport = 0 < $matches[6] ? $matches[6] : 21;
		$ftp = JieqiFTP::getInstance($matches[4], $matches[2], $matches[3], '.', $ftpport, 0, $ftpssl);

		if (!$ftp) {
			return false;
		}

		$matches[7] = trim($matches[7]);
		return $ftp->ftp_delfolder($matches[7], $flag);
	}
}

function jieqi_createdir($dirname, $mode = 511, $recursive = false)
{
	if (!$recursive) {
		$ret = @mkdir($dirname, $mode);

		if ($ret) {
			@chmod($dirname, $mode);
		}

		return $ret;
	}

	if (is_dir($dirname)) {
		return true;
	}
	else if (jieqi_createdir(dirname($dirname), $mode, true)) {
		$ret = @mkdir($dirname, $mode);

		if ($ret) {
			@chmod($dirname, $mode);
		}

		return $ret;
	}
	else {
		return false;
	}
}

function jieqi_checkdir($dirname, $autocreate = false)
{
	if (is_dir($dirname)) {
		return true;
	}
	else if (!$autocreate) {
		return false;
	}
	else {
		return jieqi_createdir($dirname, 511, true);
	}
}

function jieqi_downfile($filename, $contenttype = 'application/octet-stream')
{
	if (file_exists($filename)) {
		header('Content-type: ' . jieqi_headstr($contenttype));
		header('Accept-Ranges: bytes');
		header('Content-Disposition: attachment; filename=' . jieqi_headstr(basename($filename)));
		echo jieqi_readfile($filename);
		return true;
	}
	else {
		return false;
	}
}

function jieqi_copyfile($from_file, $to_file, $mode = 511, $move = false)
{
	$from_file = trim($from_file);

	if (!is_file($from_file)) {
		return false;
	}

	$to_file = trim($to_file);
	$matches = array();

	if (!preg_match('/^(ftps?):\\/\\/([^:\\/]+):([^:\\/]*)@([0-9a-z\\-\\.]+)(:(\\d+))?([0-9a-z_\\-\\/\\.]*)/is', $to_file, $matches)) {
		jieqi_checkdir(dirname($to_file), true);

		if (is_file($to_file)) {
			@unlink($to_file);
		}

		if ($move) {
			$ret = rename($from_file, $to_file);
		}
		else {
			$ret = copy($from_file, $to_file);
		}

		if ($ret && $mode) {
			@chmod($to_file, $mode);
		}

		return $ret;
	}
	else {
		include_once JIEQI_ROOT_PATH . '/lib/ftp/ftp.php';
		$ftpssl = strtolower($matches[1]) == 'ftps' ? 1 : 0;
		$matches[6] = intval(trim($matches[6]));
		$ftpport = 0 < $matches[6] ? $matches[6] : 21;
		$ftp = JieqiFTP::getInstance($matches[4], $matches[2], $matches[3], '.', $ftpport, 0, $ftpssl);

		if (!$ftp) {
			return false;
		}

		$matches[7] = trim($matches[7]);

		if (!$ftp->ftp_chdir(dirname($matches[7]))) {
			if (substr($matches[7], 0, 1) == '/') {
				$matches[7] = substr($matches[7], 1);
			}

			$pathary = explode('/', dirname($matches[7]));

			foreach ($pathary as $v) {
				$v = trim($v);

				if (0 < strlen($v)) {
					if ($ftp->ftp_mkdir($v) !== false && $mode) {
						$ftp->ftp_chmod($mode, $v);
					}

					$ftp->ftp_chdir($v);
				}
			}
		}

		$ret = $ftp->ftp_put(basename($matches[7]), $from_file);
		if ($ret && $mode) {
			$ftp->ftp_chmod($mode, basename($matches[7]));
		}

		if ($move) {
			@unlink($from_file);
		}

		return $ret;
	}
}

function jieqi_extractvars($varname, &$vars)
{
	return '$' . $varname . ' = ' . var_export($vars, true) . ';';
}

function jieqi_setconfigs($fname = '', $vname = '', &$vars, $module = 'system')
{
	global $jieqiModules;
	if (!preg_match('/^\\w+$/', $fname) || !preg_match('/^\\w+$/', $vname) || !preg_match('/^\\w*$/', $module)) {
		return false;
	}

	$dir = JIEQI_ROOT_PATH . '/configs';
	if ($module != 'system' && isset($jieqiModules[$module])) {
		$dir .= '/' . $module;
	}

	jieqi_checkdir($dir, true);
	$dir .= '/' . $fname . '.php';

	if (is_array($vars)) {
		$varstring = '<?php
';

		foreach ($vars as $k => $v) {
			$varstring .= jieqi_extractvars($vname . '[\'' . jieqi_setslashes($k, '"') . '\']', $v) . '
';
		}

		$varstring .= '
?>';
	}
	else {
		$varstring = '<?php
' . jieqi_extractvars($vname, $vars) . '
?>';
	}

	return jieqi_writefile($dir, $varstring);
}

function jieqi_getconfigs($module = 'system', $fname = '', $vname = '')
{
	if ($vname !== false) {
		if ($vname == '') {
			$vname = 'jieqi' . ucfirst($fname);
		}

		${$vname} = &$GLOBALS[$vname];
	}

	if (!preg_match('/^\\w+$/', $fname) || !preg_match('/^\\w+$/', $vname) || !preg_match('/^\\w*$/', $module)) {
		return false;
	}

	if ($vname == 'jieqiBlocks' && isset($jieqiBlocks)) {
		return $jieqiBlocks;
	}
	else {
		if ($module == 'system' || $module == '') {
			$file = JIEQI_ROOT_PATH . '/configs/' . $fname . '.php';
		}
		else {
			$file = JIEQI_ROOT_PATH . '/configs/' . $module . '/' . $fname . '.php';
		}

		$file = @realpath($file);

		if (preg_match('/\\.php$/i', $file)) {
			if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(array('\\', JIEQI_ROOT_PATH), array('/', JIEQI_THEME_ROOTPATH), $file))) {
				include_once str_replace(array('\\', JIEQI_ROOT_PATH), array('/', JIEQI_THEME_ROOTPATH), $file);
			}
			else {
				include_once $file;
			}

			return ${$vname};
		}
		else {
			return false;
		}
	}
}

function jieqi_setcachevars($fname = '', $vname = '', &$vars, $module = 'system', $cacheid = 0)
{
	global $jieqiModules;
	global $jieqiCache;
	if (!preg_match('/^\\w+$/', $fname) || !preg_match('/^\\w+$/', $vname) || !preg_match('/^\\w*$/', $module)) {
		return false;
	}

	$cachefile = JIEQI_CACHE_PATH . '/cachevars';

	if (isset($jieqiModules[$module])) {
		$cachefile .= '/' . $module;
	}

	if ($cacheid == 0 || strlen($cacheid) == 0) {
		$cachefile .= '/' . $fname . '.php';
	}
	else {
		$cacheid = intval($cacheid);
		$cachefile .= '/' . $fname . jieqi_getsubdir($cacheid) . '/' . $cacheid . '.php';
	}

	if (is_a($jieqiCache, 'JieqiCacheMemcached')) {
		return $jieqiCache->set($cachefile, $vars);
	}
	else {
		$varstring = '<?php
' . jieqi_extractvars($vname, $vars) . '
?>';
		return $jieqiCache->set($cachefile, $varstring);
	}
}

function jieqi_getcachevars($module = 'system', $fname = '', $vname = '', $cacheid = 0)
{
	global $jieqiModules;
	global $jieqiCache;

	if ($vname !== false) {
		if ($vname == '') {
			$vname = 'jieqi' . ucfirst($fname);
		}

		${$vname} = &$GLOBALS[$vname];
	}

	if (!preg_match('/^\\w+$/', $fname) || !preg_match('/^\\w+$/', $vname) || !preg_match('/^\\w*$/', $module)) {
		return false;
	}

	$cachefile = JIEQI_CACHE_PATH . '/cachevars';

	if (isset($jieqiModules[$module])) {
		$cachefile .= '/' . $module;
	}

	if ($cacheid == 0 || strlen($cacheid) == 0) {
		$cachefile .= '/' . $fname . '.php';
	}
	else {
		$cacheid = intval($cacheid);
		$cachefile .= '/' . $fname . jieqi_getsubdir($cacheid) . '/' . $cacheid . '.php';
	}

	if (is_a($jieqiCache, 'JieqiCacheMemcached')) {
		${$vname} = $jieqiCache->get($cachefile);
	}
	else {
		$cachefile = @realpath($cachefile);
		if (is_file($cachefile) && preg_match('/\\.php$/i', $cachefile)) {
			include_once $cachefile;
		}
	}

	return ${$vname};
}

function jieqi_setfilevars($dname = '', $fname = '', $vname = '', &$vars, $module = 'system')
{
	if ($vname !== false) {
		if ($vname == '') {
			$vname = 'jieqi' . ucfirst($dname);
		}

		${$vname} = &$GLOBALS[$vname];
	}

	if (!preg_match('/^\\w*$/', $dname) || !preg_match('/^\\w+$/', $fname) || !preg_match('/^\\w+$/', $vname) || !preg_match('/^\\w*$/', $module)) {
		return false;
	}

	$dir = jieqi_uploadpath($dname, $module);

	if (is_numeric($fname)) {
		$dir .= jieqi_getsubdir($fname);
	}

	jieqi_checkdir($dir, true);
	$dir .= '/' . $fname . '.php';
	$varstring = '<?php
' . jieqi_extractvars($vname, $vars) . '
?>';
	return jieqi_writefile($dir, $varstring);
}

function jieqi_getfilevars($module = 'system', $dname = '', $fname = '', $vname = '')
{
	if ($vname !== false) {
		if ($vname == '') {
			$vname = 'jieqi' . ucfirst($dname);
		}

		${$vname} = &$GLOBALS[$vname];
	}

	if (!preg_match('/^\\w*$/', $dname) || !preg_match('/^\\w+$/', $fname) || !preg_match('/^\\w+$/', $vname) || !preg_match('/^\\w*$/', $module)) {
		return false;
	}

	$file = jieqi_uploadpath($dname, $module);

	if (is_numeric($fname)) {
		$file .= jieqi_getsubdir($fname);
	}

	$file .= '/' . $fname . '.php';
	$file = @realpath($file);

	if (preg_match('/\\.php$/i', $file)) {
		include_once $file;
		return ${$vname};
	}
	else {
		return false;
	}
}

$tmpvar = explode(' ', microtime());
define('JIEQI_START_TIME', $tmpvar[1] + $tmpvar[0]);

if (defined('JIEQI_PHP_CLI')) {
	exit('error defined JIEQI_PHP_CLI');
}

if (!empty($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['argv']) && $_SERVER['SCRIPT_FILENAME'] == $_SERVER['argv'][0] || empty($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['argv']) && !empty($_SERVER['argv'][0])) {
	define('JIEQI_PHP_CLI', 1);
}
else {
	define('JIEQI_PHP_CLI', 0);
}

if (defined('JIEQI_SCRIPT_FILENAME')) {
	exit('error defined JIEQI_SCRIPT_FILENAME');
}

$tmpvar = !empty($_SERVER['PATH_TRANSLATED']) && substr($_SERVER['PATH_TRANSLATED'], -4) == '.php' ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
define('JIEQI_SCRIPT_FILENAME', str_replace(array('\\\\', '\\'), '/', $tmpvar));

if (!defined('JIEQI_SITE_ID')) {
	define('JIEQI_SITE_ID', 0);
}

include_once dirname(__FILE__) . '/configs/define.php';

if (defined('JIEQI_LOCAL_HOST')) {
	exit('error defined JIEQI_LOCAL_HOST');
}

if ($_SERVER['HTTP_HOST'] == '' && JIEQI_URL != '') {
	define('JIEQI_LOCAL_HOST', str_replace(array('http://', 'https://'), '', JIEQI_URL));
}
else {
	define('JIEQI_LOCAL_HOST', $_SERVER['HTTP_HOST']);
}

$_SERVER['PHP_SELF'] = jieqi_htmlchars($_SERVER['PHP_SELF'], ENT_QUOTES);
define('JIEQI_NOW_TIME', time());
define('JIEQI_VERSION', '2.40');
define('JIEQI_GLOBAL_INCLUDE', true);

if (!defined('JIEQI_ROOT_PATH')) {
	@define('JIEQI_ROOT_PATH', str_replace(array('\\\\', '\\'), '/', dirname(__FILE__)));
}

if (!defined('JIEQI_COOKIE_DOMAIN')) {
	define('JIEQI_COOKIE_DOMAIN', strval(@ini_get('session.cookie_domain')));
}
else if (JIEQI_COOKIE_DOMAIN != '') {
	@ini_set('session.cookie_domain', JIEQI_COOKIE_DOMAIN);
}

define('JIEQI_SYSTEM_CHARSET', 'utf8');

if (function_exists('mb_internal_encoding')) {
//	mb_internal_encoding('GBK');
    mb_internal_encoding('utf8');
}

if (JIEQI_URL == '') {
	define('JIEQI_LOCAL_URL', 'http://' . $_SERVER['HTTP_HOST']);
}
else {
	define('JIEQI_LOCAL_URL', JIEQI_URL);
}

if (!defined('JIEQI_MAIN_SERVER') || JIEQI_MAIN_SERVER == '') {
	define('JIEQI_MAIN_URL', JIEQI_LOCAL_URL);
}
else {
	define('JIEQI_MAIN_URL', JIEQI_MAIN_SERVER);
}

if (!defined('JIEQI_USER_ENTRY') || JIEQI_USER_ENTRY == '') {
	define('JIEQI_USER_URL', JIEQI_LOCAL_URL);
}
else {
	define('JIEQI_USER_URL', JIEQI_USER_ENTRY);
}

if (defined('JIEQI_THEME_ROOT') && preg_match('/^\\w+$/i', JIEQI_THEME_ROOT)) {
	define('JIEQI_THEME_ROOTPATH', JIEQI_ROOT_PATH . '/' . JIEQI_THEME_ROOT);
	define('JIEQI_THEME_ROOTURL', JIEQI_URL . '/' . JIEQI_THEME_ROOT);
	define('JIEQI_THEME_ROOTNEW', 1);
}
else {
	define('JIEQI_THEME_ROOTPATH', JIEQI_ROOT_PATH);
	define('JIEQI_THEME_ROOTURL', JIEQI_URL);
}

define('JIEQI_ERROR_RETURN', 1);
define('JIEQI_ERROR_PRINT', 2);
define('JIEQI_ERROR_DIE', 4);
define('JIEQI_GROUP_USER', 3);
define('JIEQI_GROUP_ADMIN', 2);
define('JIEQI_GROUP_GUEST', 1);
define('JIEQI_SIDEBLOCK_CUSTOM', -1);
define('JIEQI_SIDEBLOCK_LEFT', 0);
define('JIEQI_SIDEBLOCK_RIGHT', 1);
define('JIEQI_CENTERBLOCK_LEFT', 2);
define('JIEQI_CENTERBLOCK_RIGHT', 3);
define('JIEQI_CENTERBLOCK_TOP', 4);
define('JIEQI_CENTERBLOCK_MIDDLE', 5);
define('JIEQI_CENTERBLOCK_BOTTOM', 6);
define('JIEQI_TOPBLOCK_ALL', 7);
define('JIEQI_BOTTOMBLOCK_ALL', 8);
define('JIEQI_NAVBLOCK_LEFT', 9);
define('JIEQI_TYPE_TXTBOX', 1);
define('JIEQI_TYPE_TXTAREA', 2);
define('JIEQI_TYPE_INT', 3);
define('JIEQI_TYPE_NUM', 4);
define('JIEQI_TYPE_PASSWORD', 5);
define('JIEQI_TYPE_HIDDEN', 6);
define('JIEQI_TYPE_SELECT', 7);
define('JIEQI_TYPE_MULSELECT', 8);
define('JIEQI_TYPE_RADIO', 9);
define('JIEQI_TYPE_CHECKBOX', 10);
define('JIEQI_TYPE_LABEL', 11);
define('JIEQI_TYPE_FILE', 12);
define('JIEQI_TYPE_DATE', 13);
define('JIEQI_TYPE_UBB', 14);
define('JIEQI_TYPE_HTML', 15);
define('JIEQI_TYPE_CODE', 16);
define('JIEQI_TYPE_SCRIPT', 17);
define('JIEQI_TYPE_OTHER', 20);
define('JIEQI_TARGET_SELF', 'self');
define('JIEQI_TARGET_NEW', 'blank');
define('JIEQI_TARGET_TOP', 'top');
define('JIEQI_CONTENT_TXT', 0);
define('JIEQI_CONTENT_HTML', 1);
define('JIEQI_CONTENT_JS', 2);
define('JIEQI_CONTENT_MIX', 3);
define('JIEQI_CONTENT_PHP', 4);
define('JIEQI_TOKEN_NAME', 'jieqi_token');
$jieqi_image_type = array(1 => '.gif', 2 => '.jpg', 3 => '.jpeg', 4 => '.png', 5 => '.bmp');
$jieqi_file_postfix = array('txt' => '.txt', 'html' => '.html', 'htm' => '.htm', 'xml' => '.xml', 'php' => '.php', 'js' => '.js', 'css' => '.css', 'zip' => '.zip', 'jar' => '.jar', 'jad' => '.jad', 'umd' => '.umd', 'opf' => '.opf');
$jieqi_charset_type = array('gb' => 'gbk', 'gbk' => 'gbk', 'gb2312' => 'gbk', 'big5' => 'big5', 'utf8' => 'utf-8', 'utf-8' => 'utf-8');
$jieqi_charset_map = array('gb2312' => 'gb', 'gbk' => 'gb', 'gb' => 'gb', 'big5' => 'big5', 'utf-8' => 'utf8', 'utf8' => 'utf8');
if (defined('JIEQI_CUSTOM_INCLUDE') && JIEQI_CUSTOM_INCLUDE == 1) {
	$tmpstr = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);

	if (preg_match('/\\.php$/i', $tmpstr)) {
		$tmpstr = substr($tmpstr, 0, -4) . '.inc.php';
		if (is_file($tmpstr) && preg_match('/^\\w+\\.inc\\.php$/i', $tmpstr)) {
			include_once $tmpstr;
		}
	}
}

if (function_exists('date_default_timezone_set') && defined('JIEQI_TIME_ZONE') && 0 < strlen(JIEQI_TIME_ZONE)) {
	@date_default_timezone_set(JIEQI_TIME_ZONE);
}

if (JIEQI_ERROR_MODE == 0) {
	@ini_set('display_errors', 0);
	@error_reporting(0);
}
else if (JIEQI_ERROR_MODE == 1) {
	@ini_set('display_errors', 1);
	@error_reporting(30719 & ~8);
}
else if (JIEQI_ERROR_MODE == 2) {
	@ini_set('display_errors', 1);
	@error_reporting(30719);
}

if (isset($_GET['show_jieqi_version']) && $_GET['show_jieqi_version'] == 1) {
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=' . JIEQI_SYSTEM_CHARSET . '" /><title>Version Info</title></head><body>Site name: ' . JIEQI_SITE_NAME . '<br />URL: ' . JIEQI_URL . '<br />Version: JIEQI CMS V' . JIEQI_VERSION . '<br />Powered by <a href="http://www.jieqi.com">JIEQI CMS</a><br /><br />License key:<br />' . JIEQI_LICENSE_KEY . '</body></html>';
	exit();
}

if (defined('JIEQI_MODULE_VTYPE')) {
	exit('error defined JIEQI_MODULE_VTYPE');
}

$jieqi_license_ary = jieqi_funtoarray('base64_decode', explode('@', JIEQI_LICENSE_KEY));
if (!empty($jieqi_license_ary[1]) && !empty($jieqi_license_ary[2])) {
	$jieqi_license_modules = jieqi_strtosary($jieqi_license_ary[2], '=', '|');
}
else {
	$jieqi_license_modules = array();
}

$matchs = array();
if (empty($jieqi_license_modules) || JIEQI_LOCAL_HOST == '' && JIEQI_PHP_CLI == 1 && ALLOW_PHP_CLI == 1 || preg_match('/^' . preg_quote(str_replace(array('\\\\', '\\'), '/', JIEQI_ROOT_PATH), '/') . '\\/(admin|install|logout\\.php)/is', JIEQI_SCRIPT_FILENAME) || preg_match('/^(http:\\/\\/|https:\\/\\/)?[^\\/\\?]*(localhost|127.0.0.1)/i', JIEQI_LOCAL_HOST, $matchs)) {
}
else {
	$site_is_licensed = false;
	if (!empty($jieqi_license_ary[1]) && preg_match('/^(http:\\/\\/|https:\\/\\/)?[^\\/\\?]*(' . $jieqi_license_ary[1] . ')/i', JIEQI_LOCAL_HOST, $matchs)) {
		$jieqi_license_domain = $jieqi_license_ary[1];
		$tmpvar = md5($jieqi_license_ary[1] . $jieqi_license_ary[2] . 'YddndlHY&ddlpDG178', true);

		if (substr($jieqi_license_ary[0], 0, 16) == $tmpvar) {
			$site_is_licensed = true;
		}
	}

	if (!$site_is_licensed) {
		header('Content-type:text/html;charset=' . JIEQI_SYSTEM_CHARSET);
		if (defined('JIEQI_IS_OPEN') && JIEQI_IS_OPEN == 0) {
			echo JIEQI_CLOSE_INFO;
		}
		else {
			echo 'License check error!<br />Domain: ' . JIEQI_LOCAL_HOST . '<br />Module: ' . JIEQI_MODULE_NAME . '<br /><br />Powered by <a href="http://www.jieqi.com" target="_blank">JIEQI CMS</a>';
		}

		exit();
	}
}

if (isset($jieqi_license_modules[JIEQI_MODULE_NAME]) && isset($jieqi_license_modules['system'])) {
	@define('JIEQI_VERSION_TYPE', $jieqi_license_modules['system']);
	@define('JIEQI_MODULE_VTYPE', $jieqi_license_modules[JIEQI_MODULE_NAME]);
}
else {
	@define('JIEQI_VERSION_TYPE', 'Free');
	@define('JIEQI_MODULE_VTYPE', 'Free');
}

if (defined('JIEQI_MODULE_NAME') && !in_array(JIEQI_MODULE_NAME, array('install', 'help', 'jieqi', 'system', 'article', 'forum', 'news', 'link', 'vote'))) {
	if (JIEQI_MODULE_VTYPE == '' || JIEQI_MODULE_VTYPE == 'Free') {
		exit('License check error!<br />Domain: ' . JIEQI_LOCAL_HOST . '<br />Module: ' . JIEQI_MODULE_NAME . '<br /><br />Powered by <a href="http://www.jieqi.com" target="_blank">JIEQI CMS</a>');
	}
}

if (defined('JIEQI_SUPPORT_MOB')) {
	exit('error defined JIEQI_SUPPORT_MOB');
}

if (isset($jieqi_license_modules['mob'])) {
	define('JIEQI_SUPPORT_MOB', 1);
}

if (!defined('JIEQI_DEVICE_FOR')) {
	if (defined('JIEQI_PC_LOCATION') && defined('JIEQI_MOBILE_LOCATION')) {
		if (7 < strlen(JIEQI_MOBILE_LOCATION) && substr(JIEQI_LOCAL_URL, 0, strlen(JIEQI_MOBILE_LOCATION)) == JIEQI_MOBILE_LOCATION) {
			define('JIEQI_DEVICE_FOR', 'mob');
		}
		else {
			define('JIEQI_DEVICE_FOR', 'pc');
		}
	}
	else {
		define('JIEQI_DEVICE_FOR', 'pc');
	}
}

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
	define('JIEQI_BROWSER_NAME', 'weixin');
}
else {
	define('JIEQI_BROWSER_NAME', '');
}

if (isset($_SERVER['PATH_INFO']) && defined('JIEQI_PATH_INFO') && 0 < JIEQI_PATH_INFO) {
	$tmpary = explode('/', str_replace(array('\'', '"', '.htm', '.html'), '', substr($_SERVER['PATH_INFO'], 1)));
	$tmpcot = count($tmpary);

	for ($i = 0; $i < $tmpcot; $i += 2) {
		if (isset($tmpary[$i + 1]) && !is_numeric($tmpary[$i])) {
			$_GET[$tmpary[$i]] = $tmpary[$i + 1];
			$_REQUEST[$tmpary[$i]] = $tmpary[$i + 1];
		}
	}
}

$jieqiModules = array();
include_once JIEQI_ROOT_PATH . '/configs/modules.php';
if (isset($jieqiModules[JIEQI_MODULE_NAME]['publish']) && $jieqiModules[JIEQI_MODULE_NAME]['publish'] == 0) {
	header('Content-type:text/html;charset=' . JIEQI_SYSTEM_CHARSET);
	echo 'This function is not valid!';
	jieqi_freeresource();
	exit();
}

foreach ($jieqiModules as $k => $v) {
	if (empty($v['dir'])) {
		$jieqiModules[$k]['dir'] = $k == 'system' ? '' : '/modules/' . $k;
	}

	if (empty($v['path'])) {
		$jieqiModules[$k]['path'] = JIEQI_ROOT_PATH . $jieqiModules[$k]['dir'];
	}

	if (empty($v['themepath'])) {
		$jieqiModules[$k]['themepath'] = JIEQI_THEME_ROOTPATH . $jieqiModules[$k]['dir'];
	}

	if (empty($v['url'])) {
		$jieqiModules[$k]['url'] = JIEQI_URL . $jieqiModules[$k]['dir'];
	}

	if (empty($v['themeurl'])) {
		$jieqiModules[$k]['themeurl'] = JIEQI_THEME_ROOTURL . $jieqiModules[$k]['dir'];
	}

	if (empty($v['theme'])) {
		$jieqiModules[$k]['theme'] = JIEQI_THEME_SET;
	}

	if (defined('JIEQI_MODULE_NAME') && JIEQI_MODULE_NAME == $k) {
		if (!empty($jieqiModules[$k]['theme'])) {
			@define('JIEQI_THEME_NAME', $jieqiModules[$k]['theme']);
		}
	}
}

if (!defined('JIEQI_THEME_NAME')) {
	define('JIEQI_THEME_NAME', JIEQI_THEME_SET);
}

if (!defined('JIEQI_CHAR_SET')) {
	if (defined('JIEQI_CHARSET_CONVERT') && JIEQI_CHARSET_CONVERT == 1 && JIEQI_VERSION_TYPE != '' && JIEQI_VERSION_TYPE != 'Free') {
		if (isset($_GET['charset'])) {
			$_GET['charset'] = strtolower($_GET['charset']);
		}

		if (isset($_GET['charset']) && isset($jieqi_charset_type[$_GET['charset']])) {
			@define('JIEQI_CHAR_SET', $jieqi_charset_type[$_GET['charset']]);
		}
		else {
			if (isset($_COOKIE['jieqiUserCharset']) && isset($jieqi_charset_type[$_COOKIE['jieqiUserCharset']])) {
				@define('JIEQI_CHAR_SET', $jieqi_charset_type[$_COOKIE['jieqiUserCharset']]);
			}
			else {
				@define('JIEQI_CHAR_SET', JIEQI_SYSTEM_CHARSET);
			}
		}

		if (!isset($_COOKIE['jieqiUserCharset']) && JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET || isset($_COOKIE['jieqiUserCharset']) && $_COOKIE['jieqiUserCharset'] != JIEQI_CHAR_SET) {
			setcookie('jieqiUserCharset', JIEQI_CHAR_SET, time() + 2592000, '/', JIEQI_COOKIE_DOMAIN, 0);
		}
	}
	else {
		@define('JIEQI_CHAR_SET', JIEQI_SYSTEM_CHARSET);
	}
}

if (JIEQI_ENABLE_CACHE && JIEQI_CHAR_SET == JIEQI_SYSTEM_CHARSET) {
	define('JIEQI_USE_CACHE', true);
}
else {
	define('JIEQI_USE_CACHE', false);
}

if (!defined('JIEQI_CACHE_DIR') || JIEQI_CACHE_DIR == '' || 0 < strpos(JIEQI_CACHE_DIR, '://')) {
	define('JIEQI_CACHE_PATH', JIEQI_ROOT_PATH . '/cache');
}
else {
	if (strpos(JIEQI_CACHE_DIR, '/') === false && strpos(JIEQI_CACHE_DIR, '\\') === false) {
		define('JIEQI_CACHE_PATH', JIEQI_ROOT_PATH . '/' . JIEQI_CACHE_DIR);
	}
	else {
		define('JIEQI_CACHE_PATH', JIEQI_CACHE_DIR);
	}
}

if (!defined('JIEQI_COMPILED_DIR') || JIEQI_COMPILED_DIR == '') {
	define('JIEQI_COMPILED_PATH', JIEQI_ROOT_PATH . '/compiled');
}
else {
	if (strpos(JIEQI_COMPILED_DIR, '/') === false && strpos(JIEQI_COMPILED_DIR, '\\') === false) {
		define('JIEQI_COMPILED_PATH', JIEQI_ROOT_PATH . '/' . JIEQI_COMPILED_DIR);
	}
	else {
		define('JIEQI_COMPILED_PATH', JIEQI_COMPILED_DIR);
	}
}

if (!defined('JIEQI_FILES_DIR') || JIEQI_FILES_DIR == '') {
	define('JIEQI_FILES_PATH', JIEQI_ROOT_PATH . '/files');
}
else {
	if (strpos(JIEQI_FILES_DIR, '/') === false && strpos(JIEQI_FILES_DIR, '\\') === false) {
		define('JIEQI_FILES_PATH', JIEQI_ROOT_PATH . '/' . JIEQI_FILES_DIR);
	}
	else {
		define('JIEQI_FILES_PATH', JIEQI_FILES_DIR);
	}
}

$sess_name = session_name();
if (isset($_COOKIE[$sess_name]) && strlen($_COOKIE[$sess_name]) < 16) {
	unset($_COOKIE[$sess_name]);
}

if (JIEQI_USE_GZIP && !(bool) @ini_get('zlib.output_compression')) {
	@ob_start('ob_gzhandler');
}

if (!empty($_COOKIE[$sess_name]) || !empty($_REQUEST[$sess_name]) || defined('JIEQI_NEED_SESSION')) {
	if (0 < JIEQI_SESSION_EXPRIE) {
		@ini_set('session.gc_maxlifetime', JIEQI_SESSION_EXPRIE);
	}

	@session_cache_limiter('private, must-revalidate');

	if (JIEQI_SESSION_STORAGE == 'db') {
		include_once JIEQI_ROOT_PATH . '/include/session.php';
		$sess_handler = JieqiSessionHandler::getInstance('JieqiSessionHandler');
		@session_set_save_handler(array(&$sess_handler, 'open'), array(&$sess_handler, 'close'), array(&$sess_handler, 'read'), array(&$sess_handler, 'write'), array(&$sess_handler, 'destroy'), array(&$sess_handler, 'gc'));
	}
	else {
		if (JIEQI_SESSION_SAVEPATH != '' && is_dir(JIEQI_SESSION_SAVEPATH)) {
			session_save_path(JIEQI_SESSION_SAVEPATH);
		}
	}

	@session_start();
	if (!empty($_COOKIE[$sess_name]) && !empty($_COOKIE['jieqiUserInfo']) && count($_SESSION) == 0) {
		include_once JIEQI_ROOT_PATH . '/class/online.php';
		$online_handler = JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
		$criteria = new CriteriaCompo(new Criteria('sid', $_COOKIE[$sess_name], '='));
		$result = $online_handler->queryObjects($criteria);
		$srow = $online_handler->getRow($result);

		if (!empty($srow['uid'])) {
			include_once JIEQI_ROOT_PATH . '/class/users.php';
			$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
			$jieqiUsers = $users_handler->get($srow['uid']);

			if (is_object($jieqiUsers)) {
				jieqi_setusersession($jieqiUsers);
			}
		}
	}
}

if (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET || !empty($_REQUEST['ajax_request']) && $jieqi_charset_map[JIEQI_CHAR_SET] != 'utf8') {
	include_once JIEQI_ROOT_PATH . '/include/changecode.php';
}

if (!empty($_REQUEST['ajax_request']) && $jieqi_charset_map[JIEQI_CHAR_SET] != 'utf8') {
	$charset_convert_ajax = 'jieqi_' . $jieqi_charset_map['utf8'] . '2' . $jieqi_charset_map[JIEQI_CHAR_SET];
	$_POST = jieqi_funtoarray($charset_convert_ajax, $_POST);
}

$charset_convert_out = '';

if (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET) {
	$charset_convert_out = 'jieqi_' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET] . '2' . $jieqi_charset_map[JIEQI_CHAR_SET];

	if (!defined('JIEQI_NOCONVERT_CHAR')) {
		@ob_start($charset_convert_out);
	}

	$charset_convert_in = 'jieqi_' . $jieqi_charset_map[JIEQI_CHAR_SET] . '2' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET];
	$_GET = jieqi_funtoarray($charset_convert_in, $_GET);
	$_POST = jieqi_funtoarray($charset_convert_in, $_POST);
	$_COOKIE = jieqi_funtoarray($charset_convert_in, $_COOKIE);
}

if (JIEQI_SYSTEM_CHARSET != JIEQI_CHAR_SET || !empty($_REQUEST['ajax_request']) && $jieqi_charset_map[JIEQI_CHAR_SET] != 'utf8') {
	$_REQUEST = array_merge($_REQUEST, $_GET, $_POST, $_COOKIE);
}

if (defined('JIEQI_MAX_PAGES') && 0 < JIEQI_MAX_PAGES && isset($_REQUEST['page']) && is_numeric($_REQUEST['page']) && JIEQI_MAX_PAGES < $_REQUEST['page']) {
	$_REQUEST['page'] = intval(JIEQI_MAX_PAGES);
}

$jieqiUsersStatus = JIEQI_GROUP_GUEST;
$jieqiUsersGroup = JIEQI_GROUP_GUEST;

if (0 < strpos(JIEQI_CACHE_DIR, '://')) {
	$cparams = @parse_url(trim(JIEQI_CACHE_DIR));

	switch ($cparams['scheme']) {
	case 'memcached':
	case 'memcache':
		$jieqiCache = JieqiCache::getInstance('memcached', $cparams);
		break;

	case 'redis':
		$jieqiCache = JieqiCache::getInstance('redis', $cparams);
		break;

	default:
		$jieqiCache = JieqiCache::getInstance('file');
		break;
	}
}
else {
	$jieqiCache = JieqiCache::getInstance('file');
}

if (empty($_SESSION['jieqiUserId'])) {
	if (!empty($_REQUEST['jieqi_username']) && !empty($_REQUEST['jieqi_userpassword'])) {
		session_start();
		include_once JIEQI_ROOT_PATH . '/include/checklogin.php';
		$urllogin = jieqi_logincheck($_REQUEST['jieqi_username'], $_REQUEST['jieqi_userpassword'], false, 0, 0, 0);

		if ($urllogin == 0) {
			$_SESSION['jieqiAdminLogin'] = 1;
		}
	}
	else {
		if (!empty($_REQUEST['luid']) && !empty($_REQUEST['lups'])) {
			session_start();
			include_once JIEQI_ROOT_PATH . '/include/checklogin.php';
			$urllogin = jieqi_logincheck($_REQUEST['luid'], $_REQUEST['lups'], false, 1, 1, 1);

			if ($urllogin == 0) {
				$_SESSION['jieqiAdminLogin'] = 1;
			}
		}
		else if (!empty($_COOKIE['jieqiUserInfo'])) {
			$jieqi_user_info = jieqi_strtosary($_COOKIE['jieqiUserInfo']);
			if (!empty($jieqi_user_info['jieqiUserUname']) && !empty($jieqi_user_info['jieqiUserPassword'])) {
				session_start();
				include_once JIEQI_ROOT_PATH . '/include/checklogin.php';
				jieqi_logincheck($jieqi_user_info['jieqiUserUname'], $jieqi_user_info['jieqiUserPassword'], false, 1, 1, 0);
			}
		}
	}
}

if (!empty($_SESSION['jieqiUserGroup'])) {
	$jieqiUsersGroup = $_SESSION['jieqiUserGroup'];

	switch ($_SESSION['jieqiUserGroup']) {
	case JIEQI_GROUP_GUEST:
		$jieqiUsersStatus = JIEQI_GROUP_GUEST;
		break;

	case JIEQI_GROUP_ADMIN:
		$jieqiUsersStatus = JIEQI_GROUP_ADMIN;
		define('JIEQI_IS_ADMIN', 1);
		break;

	default:
		$jieqiUsersStatus = JIEQI_GROUP_USER;
		break;
	}
}

if (defined('JIEQI_IS_OPEN') && JIEQI_IS_OPEN == 0 && !defined('JIEQI_ADMIN_LOGIN') && $jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
	header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
	echo JIEQI_CLOSE_INFO;

	if (!empty($_SESSION)) {
		$_SESSION = array();
	}

	if (!empty($_COOKIE['jieqiUserInfo'])) {
		setcookie('jieqiUserInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
	}

	jieqi_freeresource();
	exit();
}
else {
	if (defined('JIEQI_IS_OPEN') && JIEQI_IS_OPEN == 2 && !defined('JIEQI_ADMIN_LOGIN') && $jieqiUsersStatus != JIEQI_GROUP_ADMIN && 0 < count($_POST)) {
		header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
		echo LANG_DENY_POST;

		if (!empty($_SESSION)) {
			$_SESSION = array();
		}

		if (!empty($_COOKIE['jieqiUserInfo'])) {
			setcookie('jieqiUserInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
		}

		jieqi_freeresource();
		exit();
	}
}

if (defined('JIEQI_PROXY_DENIED') && JIEQI_PROXY_DENIED != 1) {
	if ($_SERVER['HTTP_X_FORWARDED_FOR'] || $_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] || $_SERVER['HTTP_USER_AGENT_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION']) {
		header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
		echo LANG_DENY_PROXY;

		if (!empty($_SESSION)) {
			$_SESSION = array();
		}

		if (!empty($_COOKIE['jieqiUserInfo'])) {
			setcookie('jieqiUserInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
		}

		jieqi_freeresource();
		exit();
	}
}

$jieqiTset = array();

if (function_exists('jieqi_hooks_global')) {
	jieqi_hooks_global();
}

?>
