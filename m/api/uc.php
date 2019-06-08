<?php

class uc_note
{
    public $query;
    public $userhandler;
    public function _serialize($arr, $htmlon = 0)
    {
        if (!function_exists('xml_serialize')) {
            include_once JIEQI_ROOT_PATH . '/api/ucenter/lib/xml.class.php';
        }
        return xml_serialize($arr, $htmlon);
    }
    public function __construct()
    {
        $this->query =& $GLOBALS['query'];
        $this->userhandler =& $GLOBALS['users_handler'];
    }
    public function test($get, $post)
    {
        return API_RETURN_SUCCEED;
    }
    public function _checkids($ids, $separator = ',')
    {
        $idary = explode($separator, $ids);
        if (!is_array($idary)) {
            $idary = array();
        }
        $ids = '';
        foreach ($idary as $id) {
            $id = trim($id);
            if (is_numeric($id)) {
                if ($ids != '') {
                    $ids .= $separator;
                }
                $ids .= intval($id);
            }
        }
        return $ids;
    }
    public function deleteuser($get, $post)
    {
        if (!API_DELETEUSER) {
            return API_RETURN_FORBIDDEN;
        }
        $uids = $this->_checkids($get['ids']);
        if (strlen($uids) == 0) {
            return API_RETURN_FAILED;
        }
        include_once JIEQI_ROOT_PATH . '/api/ucenter/client.php';
        $uidary = explode(',', $uids);
        $unames = '';
        foreach ($uidary as $uid) {
            $uid = intval($uid);
            if ($data = uc_get_user($uid, 1)) {
                $email = $data[2];
                $username = $data[1];
                $uid = $data[0];
            } else {
                return API_RETURN_FAILED;
            }
            if ($unames != '') {
                $unames .= ', ';
            }
            $unames .= '\'' . jieqi_dbslashes($usernamenew) . '\'';
        }
        if (strlen($unames) == 0) {
            return API_RETURN_FAILED;
        }
        $sql = 'DELETE FROM ' . jieqi_dbprefix('system_users') . ' WHERE uname IN (' . $uids . ')';
        $ret = $this->query->execute($sql);
        if ($ret === false) {
            return API_RETURN_FAILED;
        } else {
            return API_RETURN_SUCCEED;
        }
    }
    public function renameuser($get, $post)
    {
        if (!API_RENAMEUSER) {
            return API_RETURN_FORBIDDEN;
        }
        $uid = intval($get['uid']);
        $usernameold = $get['oldusername'];
        $usernamenew = $get['newusername'];
        if ($uid <= 0) {
            return API_RETURN_FAILED;
        }
        $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET uname = \'' . jieqi_dbslashes($usernamenew) . '\' WHERE uname = \'' . jieqi_dbslashes($usernameold) . '\'';
        $ret = $this->query->execute($sql);
        if ($ret === false) {
            return API_RETURN_FAILED;
        } else {
            return API_RETURN_SUCCEED;
        }
    }
    public function gettag($get, $post)
    {
        if (!API_GETTAG) {
            return API_RETURN_FORBIDDEN;
        }
        return API_RETURN_SUCCEED;
    }
    public function synlogin($get, $post)
    {
        if (!API_SYNLOGIN) {
            return API_RETURN_FORBIDDEN;
        }
        $uid = intval($get['uid']);
        $username = $get['username'];
        $jieqiUsers = $this->userhandler->getByname($username);
        if (!is_object($jieqiUsers)) {
            include_once JIEQI_ROOT_PATH . '/api/ucenter/client.php';
            if ($data = uc_get_user($username)) {
                $email = $data[2];
                $username = $data[1];
                $uid = $data[0];
                if (0 < $this->userhandler->getCount(new Criteria('email', $email, '='))) {
                    return API_RETURN_FAILED;
                } else {
                    include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
                    global $jieqiAction;
                    if (!isset($jieqiAction['system'])) {
                        jieqi_getconfigs('system', 'action', 'jieqiAction');
                    }
                    $action_earnscore = intval($jieqiAction['system']['register']['earnscore']);
                    $jieqiUsers = $this->userhandler->create();
                    $jieqiUsers->setVar('siteid', JIEQI_SITE_ID);
                    $jieqiUsers->setVar('uname', $username);
                    $jieqiUsers->setVar('name', $username);
                    $jieqiUsers->setVar('pass', '');
                    $jieqiUsers->setVar('groupid', JIEQI_GROUP_USER);
                    $jieqiUsers->setVar('regdate', JIEQI_NOW_TIME);
                    $jieqiUsers->setVar('initial', jieqi_getinitial($username));
                    $jieqiUsers->setVar('sex', 0);
                    $jieqiUsers->setVar('email', $email);
                    $jieqiUsers->setVar('setting', '');
                    $jieqiUsers->setVar('lastlogin', JIEQI_NOW_TIME);
                    $jieqiUsers->setVar('experience', $action_earnscore);
                    $jieqiUsers->setVar('score', $action_earnscore);
                    $jieqiUsers->setVar('egold', 0);
                    $jieqiUsers->setVar('esilver', 0);
                    $jieqiUsers->setVar('isvip', 0);
                    $jieqiUsers->setVar('overtime', 0);
                    if (!$this->userhandler->insert($jieqiUsers)) {
                        return API_RETURN_FAILED;
                    }
                }
            } else {
                return API_RETURN_FAILED;
            }
        }
        if (is_object($jieqiUsers)) {
            include_once JIEQI_ROOT_PATH . '/include/checklogin.php';
            jieqi_loginprocess($jieqiUsers);
            return API_RETURN_SUCCEED;
        } else {
            return API_RETURN_FAILED;
        }
    }
    public function synlogout($get, $post)
    {
        if (!API_SYNLOGOUT) {
            return API_RETURN_FORBIDDEN;
        }
        include_once JIEQI_ROOT_PATH . '/include/userlocal.php';
        $params = array();
        jieqi_ulogout_lprocess($params);
        return API_RETURN_SUCCEED;
    }
    public function updatepw($get, $post)
    {
        if (!API_UPDATEPW) {
            return API_RETURN_FORBIDDEN;
        }
        $username = $get['username'];
        $password = $get['password'];
        $salt = '';
        $sql = 'SELECT salt FROM ' . jieqi_dbprefix('system_users') . ' WHERE uname = \'' . jieqi_dbslashes($username) . '\' LIMIT 0, 1';
        $ret = $this->query->execute($sql);
        if ($ret) {
            $row = $this->query->getRow();
            $salt = $row['salt'];
        }
        $encpass = $this->userhandler->encryptPass($password, $salt);
        $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET pass = \'' . jieqi_dbslashes($encpass) . '\' WHERE uname = \'' . jieqi_dbslashes($username) . '\'';
        $ret = $this->query->execute($sql);
        if ($ret === false) {
            return API_RETURN_FAILED;
        } else {
            return API_RETURN_SUCCEED;
        }
    }
    public function updatebadwords($get, $post)
    {
        if (!API_UPDATEBADWORDS) {
            return API_RETURN_FORBIDDEN;
        }
        return API_RETURN_SUCCEED;
    }
    public function updatehosts($get, $post)
    {
        if (!API_UPDATEHOSTS) {
            return API_RETURN_FORBIDDEN;
        }
        return API_RETURN_SUCCEED;
    }
    public function updateapps($get, $post)
    {
        if (!API_UPDATEAPPS) {
            return API_RETURN_FORBIDDEN;
        }
        $UC_API = '';
        if ($post['UC_API']) {
            $UC_API = str_replace(array('\'', '"', '\\', '' . "\0" . '', "\n", "\r"), '', $post['UC_API']);
            unset($post['UC_API']);
        }
        $cachefile = JIEQI_ROOT_PATH . '/api/ucenter/data/cache/apps.php';
        $fp = fopen($cachefile, 'w');
        $s = '<?php' . "\r\n" . '';
        $s .= '$_CACHE[\'apps\'] = ' . var_export($post, true) . ';' . "\r\n" . '';
        fwrite($fp, $s);
        fclose($fp);
        if ($UC_API && is_writeable(JIEQI_ROOT_PATH . '/api/ucenter/config.inc.php')) {
            if (preg_match('/^https?:\\/\\//is', $UC_API)) {
                $configfile = trim(file_get_contents(JIEQI_ROOT_PATH . '/api/ucenter/config.inc.php'));
                $configfile = substr($configfile, -2) == '?>' ? substr($configfile, 0, -2) : $configfile;
                $configfile = preg_replace('/define\\(\'UC_API\',\\s*\'.*?\'\\);/i', 'define(\'UC_API\', \'' . addslashes($UC_API) . '\');', $configfile);
                if ($fp = @fopen(JIEQI_ROOT_PATH . '/api/ucenter/config.inc.php', 'w')) {
                    @fwrite($fp, trim($configfile));
                    @fclose($fp);
                }
            }
        }
        return API_RETURN_SUCCEED;
    }
    public function updateclient($get, $post)
    {
        if (!API_UPDATECLIENT) {
            return API_RETURN_FORBIDDEN;
        }
        $cachefile = JIEQI_ROOT_PATH . '/api/ucenter/data/cache/settings.php';
        $fp = fopen($cachefile, 'w');
        $s = '<?php' . "\r\n" . '';
        $s .= '$_CACHE[\'settings\'] = ' . var_export($post, true) . ';' . "\r\n" . '';
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }
    public function updatecredit($get, $post)
    {
        if (!API_UPDATECREDIT) {
            return API_RETURN_FORBIDDEN;
        }
        $credit = intval($get['credit']);
        $amount = intval($get['amount']);
        $uid = intval($get['uid']);
        $creditary = array(1 => 'score', 2 => 'experience', 3 => 'credit', 4 => 'egold');
        if ($uid <= 0) {
            return API_RETURN_FAILED;
        }
        if (!isset($creditary[$credit])) {
            return API_RETURN_FAILED;
        }
        include_once JIEQI_ROOT_PATH . '/api/ucenter/client.php';
        if ($data = uc_get_user($uid, 1)) {
            $email = $data[2];
            $username = $data[1];
            $uid = $data[0];
        } else {
            return API_RETURN_FAILED;
        }
        $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET ' . $creditary[$credit] . ' = ' . $creditary[$credit] . ' + ' . $amount . ' WHERE uname = \'' . jieqi_dbslashes($username) . '\'';
        $ret = $this->query->execute($sql);
        if ($ret === false) {
            return API_RETURN_FAILED;
        } else {
            return API_RETURN_SUCCEED;
        }
    }
    public function getcredit($get, $post)
    {
        if (!API_GETCREDIT) {
            return API_RETURN_FORBIDDEN;
        }
        $uid = intval($get['uid']);
        $credit = intval($get['credit']);
        $creditary = array(1 => 'score', 2 => 'experience', 3 => 'credit', 4 => 'egold');
        if (!isset($creditary[$credit])) {
            return API_RETURN_FAILED;
        }
        include_once JIEQI_ROOT_PATH . '/api/ucenter/client.php';
        if ($data = uc_get_user($uid, 1)) {
            $email = $data[2];
            $username = $data[1];
            $uid = $data[0];
        } else {
            return API_RETURN_FAILED;
        }
        $sql = 'SELECT ' . $creditary[$credit] . ' FROM ' . jieqi_dbprefix('system_users') . ' WHERE uname = \'' . jieqi_dbslashes($username) . '\' LIMIT 0, 1';
        $ret = $this->query->execute($sql);
        if ($ret === false) {
            return API_RETURN_FAILED;
        } else {
            $row = $this->query->getRow();
            return $row[$creditary[$credit]];
        }
    }
    public function getcreditsettings($get, $post)
    {
        if (!API_GETCREDITSETTINGS) {
            return API_RETURN_FORBIDDEN;
        }
        $credits = array();
        $credits[1] = array('积分', '');
        $credits[2] = array('经验', '');
        $credits[3] = array('信用', '');
        $credits[3] = array(JIEQI_EGOLD_NAME, '');
        return $this->_serialize($credits);
    }
    public function updatecreditsettings($get, $post)
    {
        if (!API_UPDATECREDITSETTINGS) {
            return API_RETURN_FORBIDDEN;
        }
        $outextcredits = array();
        foreach ($get['credit'] as $appid => $credititems) {
            if ($appid == UC_APPID) {
                foreach ($credititems as $value) {
                    $outextcredits[$value['appiddesc'] . '|' . $value['creditdesc']] = array('appiddesc' => $value['appiddesc'], 'creditdesc' => $value['creditdesc'], 'creditsrc' => $value['creditsrc'], 'title' => $value['title'], 'unit' => $value['unit'], 'ratiosrc' => $value['ratiosrc'], 'ratiodesc' => $value['ratiodesc'], 'ratio' => $value['ratio']);
                }
            }
        }
        $tmp = array();
        foreach ($outextcredits as $value) {
            $key = $value['appiddesc'] . '|' . $value['creditdesc'];
            if (!isset($tmp[$key])) {
                $tmp[$key] = array('title' => $value['title'], 'unit' => $value['unit']);
            }
            $tmp[$key]['ratiosrc'][$value['creditsrc']] = $value['ratiosrc'];
            $tmp[$key]['ratiodesc'][$value['creditsrc']] = $value['ratiodesc'];
            $tmp[$key]['creditsrc'][$value['creditsrc']] = $value['ratio'];
        }
        $outextcredits = $tmp;
        $cachefile = JIEQI_ROOT_PATH . '/api/ucenter/data/cache/creditsettings.php';
        $fp = fopen($cachefile, 'w');
        $s = '<?php' . "\r\n" . '';
        $s .= '$_CACHE[\'creditsettings\'] = ' . var_export($outextcredits, true) . ';' . "\r\n" . '';
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }
    public function addfeed($get, $post)
    {
        if (!API_ADDFEED) {
            return API_RETURN_FORBIDDEN;
        }
        return API_RETURN_SUCCEED;
    }
}
function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key ? $key : UC_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? $operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ $box[($box[$a] + $box[$j]) % 256]);
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || 0 < substr($result, 0, 10) - time()) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
function _stripslashes($string)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = _stripslashes($val);
        }
    } else {
        $string = stripslashes($string);
    }
    return $string;
}
error_reporting(0);
define('UC_CLIENT_VERSION', '1.6.0');
define('UC_CLIENT_RELEASE', '20110501');
define('API_DELETEUSER', 1);
define('API_RENAMEUSER', 1);
define('API_GETTAG', 0);
define('API_SYNLOGIN', 1);
define('API_SYNLOGOUT', 1);
define('API_UPDATEPW', 1);
define('API_UPDATEBADWORDS', 0);
define('API_UPDATEHOSTS', 0);
define('API_UPDATEAPPS', 1);
define('API_UPDATECLIENT', 1);
define('API_UPDATECREDIT', 1);
define('API_GETCREDIT', 1);
define('API_GETCREDITSETTINGS', 1);
define('API_UPDATECREDITSETTINGS', 1);
define('API_ADDFEED', 0);
define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');
@define('IN_DISCUZ', true);
@define('IN_API', true);
@define('CURSCRIPT', 'api');
define('JIEQI_NEED_SESSION', 1);
require_once dirname(dirname(__FILE__)) . '/global.php';
include_once JIEQI_ROOT_PATH . '/api/ucenter/config.inc.php';
if (!defined('UC_KEY') || UC_KEY == '11223344' || UC_KEY == '123456789' || UC_KEY == '') {
    jieqi_printfail('Ucenter接口配置文件config.inc.php里面，UC_KEY的值请设置成自己的密钥，不要用系统默认值！');
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
if (!defined('IN_UC')) {
    if (function_exists('set_magic_quotes_runtime')) {
        @set_magic_quotes_runtime(0);
    }
    if (!defined('MAGIC_QUOTES_GPC') && function_exists('get_magic_quotes_gpc')) {
        define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    } else {
        define('MAGIC_QUOTES_GPC', false);
    }
    $get = $post = array();
    $code = @$_GET['code'];
    parse_str(_authcode($code, 'DECODE', UC_KEY), $get);
    if (MAGIC_QUOTES_GPC) {
        $get = _stripslashes($get);
    }
    if (3600 < time() - $get['time']) {
        exit('Authracation has expiried');
    }
    if (empty($get)) {
        exit('Invalid Request');
    }
    include_once JIEQI_ROOT_PATH . '/api/ucenter/lib/xml.class.php';
    $post = xml_unserialize(file_get_contents('php://input'));
    if (in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcredit', 'getcreditsettings', 'updatecreditsettings', 'addfeed'))) {
        $uc_note = new uc_note();
        echo $uc_note->{$get['action']}($get, $post);
        exit;
    } else {
        exit(API_RETURN_FAILED);
    }
}