<?php

function jieqi_api_logininit()
{
    global $apiName;
    if (!empty($_REQUEST['jumpurl']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
        $_SESSION['jieqiUserApi'][$apiName]['jumpurl'] = $_REQUEST['jumpurl'];
    } else {
        if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_SERVER['HTTP_REFERER']) && !preg_match('/(login\\.php|register\\.php|getpass\\.php|setpass\\.php)/i', $_SERVER['HTTP_REFERER'])) {
            $_SESSION['jieqiUserApi'][$apiName]['jumpurl'] = $_SERVER['HTTP_REFERER'];
        } else {
            $_SESSION['jieqiUserApi'][$apiName]['jumpurl'] = JIEQI_URL . '/';
        }
    }
    $_SESSION['jieqiUserApi'][$apiName]['state'] = md5(uniqid(rand(), true));
}
function jieqi_api_getauthparam($key)
{
    global $apiName;
    $key = substr(strstr($key, '_'), 1);
    return isset($_SESSION['jieqiUserApi'][$apiName][$key]) ? $_SESSION['jieqiUserApi'][$apiName][$key] : NULL;
}
function jieqi_api_setauthparam($key, $val)
{
    global $apiName;
    $key = substr(strstr($key, '_'), 1);
    if ($key == 'uid') {
        $key = 'openid';
    }
    $_SESSION['jieqiUserApi'][$apiName][$key] = $val;
}
function jieqi_api_charsetconvert($data, $charset = 'utf-8')
{
    if (JIEQI_SYSTEM_CHARSET != $charset) {
        global $jieqi_charset_map;
        include_once JIEQI_ROOT_PATH . '/include/changecode.php';
        if (is_array($data)) {
            return jieqi_funtoarray('jieqi_' . $jieqi_charset_map[$charset] . '2' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET], $data);
        } else {
            return call_user_func('jieqi_' . $jieqi_charset_map[$charset] . '2' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET], $data);
        }
    } else {
        return $data;
    }
}
function jieqi_api_unamefilter($uname)
{
    return preg_replace('/[%,;:\\|\\*\\"\'\\\\\\/\\s\\t\\<\\>\\&]/is', '', $uname);
}
function jieqi_api_bindcheck()
{
    global $apiName;
    global $apiOrder;
    global $query;
    global $users_handler;
    global $apiConfigs;
    $apiField = $apiName . 'id';
    $apiOrder = intval($apiOrder);
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    if (!empty($_SESSION['jieqiUserApi'][$apiName]['unionid'])) {
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE ' . jieqi_dbslashes($apiField) . ' = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['openid']) . '\' OR wxunid = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['unionid']) . '\' LIMIT 0, 2';
    } else {
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE ' . jieqi_dbslashes($apiField) . ' = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['openid']) . '\' LIMIT 0, 1';
    }
    $query->execute($sql);
    $apirow = $query->getRow();
    if (is_array($apirow)) {
        $tmprow = $query->getRow();
        if (is_array($tmprow) && $tmprow[$apiField] == $_SESSION['jieqiUserApi'][$apiName]['openid']) {
            $apirow = $tmprow;
        }
    }
    $jieqiUsers = NULL;
    if (is_array($apirow) && !empty($apirow['uid'])) {
        $jieqiUsers = $users_handler->get($apirow['uid']);
        if (!is_object($jieqiUsers)) {
            $flagnum = pow(2, $apiOrder - 1);
            $flagstr = str_repeat('1', 30);
            $flagstr[30 - $apiOrder] = '0';
            if ($apirow['apiflag'] == $flagnum) {
                $sql = 'DELETE FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE uid = ' . intval($apirow['uid']);
                $query->execute($sql);
                $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET conisbind = 0 WHERE uid = ' . intval($apirow['uid']);
                $query->execute($sql);
            } else {
                $sql = 'UPDATE ' . jieqi_dbprefix('system_userapi') . ' SET apiflag = apiflag & ' . bindec($flagstr) . ', ' . jieqi_dbslashes($apiField) . ' = \'\' WHERE uid = ' . intval($apirow['uid']);
                $query->execute($sql);
                $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET conisbind = conisbind & ' . bindec($flagstr) . ' WHERE uid = ' . intval($apirow['uid']);
                $query->execute($sql);
            }
        } else {
            $apidata = jieqi_unserialize($apirow['apidata']);
            if (!is_array($apidata)) {
                $apidata = array();
            }
            $apidata[$apiName] = $_SESSION['jieqiUserApi'][$apiName];
            if (!empty($_SESSION['jieqiUserApi'][$apiName]['unionid']) && empty($apirow[$apiField])) {
                $flagstr = str_repeat('1', 30);
                $flagstr[30 - $apiOrder] = '0';
                $sql = 'UPDATE ' . jieqi_dbprefix('system_userapi') . ' SET apiflag = apiflag & ' . bindec($flagstr) . ', apidata = \'' . jieqi_dbslashes(serialize($apidata)) . '\', ' . jieqi_dbslashes($apiField) . ' = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['openid']) . '\', wxunid = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['unionid']) . '\' WHERE uid = ' . intval($apirow['uid']);
                $query->execute($sql);
                $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET conisbind = conisbind & ' . bindec($flagstr) . ' WHERE uid = ' . intval($apirow['uid']);
                $query->execute($sql);
            } else {
                $sql = 'UPDATE ' . jieqi_dbprefix('system_userapi') . ' SET apidata = \'' . jieqi_dbslashes(serialize($apidata)) . '\'';
                if (!empty($_SESSION['jieqiUserApi'][$apiName]['unionid']) && empty($apirow['wxunid'])) {
                    $sql .= ', wxunid = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['unionid']) . '\'';
                }
                $sql .= ' WHERE uid = ' . intval($apirow['uid']);
                $query->execute($sql);
            }
        }
    }
    return $jieqiUsers;
}
function jieqi_api_bindshow($uinfo = array())
{
    global $apiName;
    global $apiTitle;
    include_once JIEQI_ROOT_PATH . '/header.php';
    if (empty($uinfo)) {
        $uinfo = jieqi_api_userinfo();
    }
    $jieqiTpl->assign_by_ref('api_userinfo', $uinfo);
    $jieqiTpl->assign_by_ref('api_nickname', $uinfo['uname']);
    $jieqiTpl->assign('apiname', $apiName);
    $jieqiTpl->assign('apititle', $apiTitle);
    $jieqiTpl->assign('check_url', JIEQI_USER_URL . '/regcheck.php');
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/api/' . $apiName . '/templates/bind.html';
    include_once JIEQI_ROOT_PATH . '/footer.php';
}
function jieqi_api_binduser($params = array())
{
    global $query;
    global $apiName;
    global $apiOrder;
    global $apiConfigs;
    global $jieqiConfigs;
    global $jieqi_image_type;
    if (!isset($params['userid'])) {
        $params['userid'] = $_SESSION['jieqiUserId'];
    }
    $params['userid'] = intval($params['userid']);
    $apiField = $apiName . 'id';
    $apiOrder = intval($apiOrder);
    $apiflag = pow(2, $apiOrder - 1);
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    if (!isset($params['apirow'])) {
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_userapi') . ' WHERE uid = ' . $params['userid'] . ' LIMIT 0, 1';
        $query->execute($sql);
        $apirow = $query->getRow();
    } else {
        $apirow = $params['apirow'];
    }
    if (is_array($apirow)) {
        $apiflag = $apirow['apiflag'] | $apiflag;
        $apidata = jieqi_unserialize($apirow['apidata']);
        $apidata[$apiName] = array('expire_in' => $_SESSION['jieqiUserApi'][$apiName]['expire_in'], 'access_token' => $_SESSION['jieqiUserApi'][$apiName]['access_token'], 'openid' => $_SESSION['jieqiUserApi'][$apiName]['openid'], 'openkey' => $_SESSION['jieqiUserApi'][$apiName]['openkey']);
        $apiset = jieqi_unserialize($apirow['apiset']);
        $apiset[$apiName] = array('isregister' => $params['isregister']);
        $sql = 'UPDATE ' . jieqi_dbprefix('system_userapi') . ' SET apiflag = ' . $apiflag . ', apidata = \'' . jieqi_dbslashes(serialize($apidata)) . '\', apiset = \'' . jieqi_dbslashes(serialize($apiset)) . '\', ' . jieqi_dbslashes($apiField) . ' = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['openid']) . '\'';
        if (!empty($_SESSION['jieqiUserApi'][$apiName]['unionid'])) {
            $sql .= ', wxunid = \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['unionid']) . '\'';
        }
        $sql .= ' WHERE uid = ' . $params['userid'];
    } else {
        $apidata = array();
        $apidata[$apiName] = array('expire_in' => $_SESSION['jieqiUserApi'][$apiName]['expire_in'], 'access_token' => $_SESSION['jieqiUserApi'][$apiName]['access_token'], 'openid' => $_SESSION['jieqiUserApi'][$apiName]['openid'], 'openkey' => $_SESSION['jieqiUserApi'][$apiName]['openkey']);
        $apiset = array();
        $apiset[$apiName] = array('isregister' => $params['isregister']);
        $wxunid = empty($_SESSION['jieqiUserApi'][$apiName]['unionid']) ? '' : $_SESSION['jieqiUserApi'][$apiName]['unionid'];
        $sql = 'INSERT INTO ' . jieqi_dbprefix('system_userapi') . ' (`uid`, `apiflag`, `apidata`, `apiset`, `' . jieqi_dbslashes($apiField) . '`, `wxunid`) VALUES (\'' . $params['userid'] . '\', \'' . intval($apiflag) . '\', \'' . jieqi_dbslashes(serialize($apidata)) . '\', \'' . jieqi_dbslashes(serialize($apiset)) . '\', \'' . jieqi_dbslashes($_SESSION['jieqiUserApi'][$apiName]['openid']) . '\', \'' . jieqi_dbslashes($wxunid) . '\');';
    }
    $query->execute($sql);
    $sql = 'SELECT uid, conisbind, avatar FROM ' . jieqi_dbprefix('system_users') . ' WHERE uid = ' . $params['userid'] . ' LIMIT 0, 1';
    $query->execute($sql);
    $userrow = $query->getRow();
    $conisbind = $userrow['conisbind'] | $apiflag;
    if ($conisbind < 1) {
        $conisbind = 1;
    }
    $avatar = $userrow['avatar'];
    if ($userrow['avatar'] == 0) {
        if (!empty($params['apiuinfo'])) {
            $uinfo = $params['apiuinfo'];
        } else {
            $uinfo = jieqi_api_userinfo();
        }
        if (!empty($uinfo['url_avatar']) && preg_match('/^https?:\\/\\//i', $uinfo['url_avatar'])) {
            if (!isset($jieqiConfigs['system'])) {
                jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
            }
            include_once JIEQI_ROOT_PATH . '/include/funusers.php';
            include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
            jieqi_system_avatarset();
            $avatardir = jieqi_uploadpath($jieqiConfigs['system']['avatardir'], 'system');
            $avatardir .= jieqi_getsubdir($params['userid']);
            jieqi_checkdir($avatardir, true);
            $imagefile = $avatardir . '/' . $params['userid'] . $jieqiConfigs['system']['avatardt'];
            $imgdata = jieqi_urlcontents($uinfo['url_avatar'], array('charset' => 'image', 'connecttimeout' => 10, 'timeout' => 10));
            jieqi_writefile($imagefile, $imgdata);
            jieqi_system_avatarresize($params['userid'], $imagefile);
        }
        foreach ($jieqi_image_type as $k => $v) {
            if ($jieqiConfigs['system']['avatardt'] == $v) {
                $avatar = $k;
                break;
            }
        }
    }
    if ($conisbind != $userrow['conisbind'] || $avatar != $userrow['avatar']) {
        $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET conisbind = ' . intval($conisbind) . ', avatar = ' . intval($avatar) . ' WHERE uid = ' . $params['userid'];
        $query->execute($sql);
    }
    return true;
}
function jieqi_api_bindauto()
{
    global $query;
    global $users_handler;
    global $jieqiLang;
    global $apiName;
    global $apiConfigs;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $params = array();
    $uinfo = jieqi_api_userinfo();
    $puid = 0;
    $errors = $users_handler->validField('username', $uinfo['uname']);
    if (empty($errors)) {
        $params['username'] = $uinfo['uname'];
    } else {
        $sql = 'SHOW TABLE STATUS WHERE name = \'' . jieqi_dbprefix('system_users') . '\'';
        $query->execute($sql);
        $row = $query->getRow();
        $puid = intval($row['Auto_increment']);
        $params['username'] = $uinfo['uname'] . '*' . $puid;
    }
    $params['nickname'] = $params['username'];
    $params['sex'] = isset($uinfo['sex']) ? intval($uinfo['sex']) : 0;
    $params['apiname'] = $apiName;
    $jieqiUsers = $users_handler->usersAdd($params);
    if (!$jieqiUsers) {
        jieqi_printfail($jieqiLang['system']['api_register_failure']);
    } else {
        $uid = intval($jieqiUsers->getVar('uid', 'n'));
        if (0 < $puid && $puid != $uid) {
            $uname = $uinfo['uname'] . '*' . $uid;
            $uname_db = jieqi_dbslashes($uname_n);
            $sql = 'UPDATE ' . jieqi_dbprefix('system_users') . ' SET uname = \'' . $uname_db . '\', name = \'' . $uname_db . '\' WHERE uid = ' . $uid;
            $ret = $query->execute($sql);
            if ($ret) {
                $jieqiUsers->setVar('uname', $uname);
                $jieqiUsers->setVar('name', $uname);
            }
        }
    }
    jieqi_api_binduser(array('isregister' => 1, 'userid' => $uid, 'apirow' => false, 'apiuinfo' => $uinfo));
    return $jieqiUsers;
}
function jieqi_api_bindlogin($jieqiUsers)
{
    global $jieqiLang;
    global $apiName;
    include_once JIEQI_ROOT_PATH . '/include/checklogin.php';
    jieqi_loginprocess($jieqiUsers);
    if (defined('JIEQI_USER_INTERFACE') && preg_match('/^\\w+$/is', JIEQI_USER_INTERFACE)) {
        include_once JIEQI_ROOT_PATH . '/include/funuser_' . JIEQI_USER_INTERFACE . '.php';
    } else {
        include_once JIEQI_ROOT_PATH . '/include/funuser.php';
    }
    $ucsyncode = '';
    if (function_exists('uc_get_user')) {
        if ($data = uc_get_user($jieqiUsers->getVar('uname', 'n'))) {
            $email = $data[2];
            $username = $data[1];
            $uid = $data[0];
            $ucsyncode = uc_user_synlogin($uid);
        }
    }
    jieqi_api_loginjump($jieqiUsers, $ucsyncode);
}
function jieqi_api_loginjump($user, $ucsyncode = '')
{
    global $jieqiLang;
    global $apiName;
    if (empty($_SESSION['jieqiUserApi'][$apiName]['jumpurl'])) {
        $jumpurl = JIEQI_URL . '/';
    } else {
        $jumpurl = $_SESSION['jieqiUserApi'][$apiName]['jumpurl'];
    }
    $username = is_array($user) ? $user['name'] : (is_object($user) ? $user->getVar('name') : strval($user));
    if ($_REQUEST['jumphide']) {
        jieqi_jumppage($jumpurl, '', $ucsyncode, true);
    } else {
        jieqi_jumppage($jumpurl, $jieqiLang['system']['login_title'], sprintf($jieqiLang['system']['api_login_success'], $username) . $ucsyncode);
    }
}
if (class_exists('OpenSDK_OAuth_Interface')) {
    OpenSDK_OAuth_Interface::param_set_save_handler('jieqi_api_getauthparam', 'jieqi_api_setauthparam');
}