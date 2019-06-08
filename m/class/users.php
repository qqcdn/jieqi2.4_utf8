<?php
jieqi_includedb();
class JieqiUsers extends JieqiObjectData
{
    public $tableFields = array();
    public function __construct()
    {
        global $system_users_fields;
        parent::__construct();
        $this->tableFields =& $system_users_fields;
        foreach ($this->tableFields as $k => $v) {
            $this->initVar($k, $v['type'], $v['value'], $v['caption'], $v['required'], $v['maxlength']);
        }
    }
    public function getGroup()
    {
        global $jieqiGroups;
        return $jieqiGroups[$this->getVar('groupid')];
    }
    public function getViptype()
    {
        global $jieqiLang;
        jieqi_loadlang('users', 'system');
        $vipflag = $this->getVar('isvip');
        if ($vipflag == 0) {
            return $jieqiLang['system']['user_no_vip'];
        } else {
            if ($vipflag == 1) {
                return $jieqiLang['system']['user_is_vip'];
            } else {
                if (1 < $vipflag) {
                    return $jieqiLang['system']['user_super_vip'];
                }
            }
        }
    }
    public function getStatus()
    {
        switch ($this->getVar('groupid')) {
            case JIEQI_GROUP_GUEST:
                return JIEQI_GROUP_GUEST;
                break;
            case JIEQI_GROUP_ADMIN:
                return JIEQI_GROUP_ADMIN;
                break;
            default:
                return JIEQI_GROUP_USER;
                break;
        }
    }
    public function getUserset($field, $name = '')
    {
        global $system_users_soptions;
        if (!isset($system_users_soptions[$field])) {
            return false;
        }
        $ret = $system_users_soptions[$field];
        $var = intval($this->getVar($field, 'n'));
        $s = decbin($var);
        $l = strlen($s);
        $p = 1;
        foreach ($ret as $k => $v) {
            if ($p <= $l && $s[$l - $p] != '0') {
                $ret[$k] = 1;
            }
            if ($name == $k) {
                return $ret[$k];
            }
            $p++;
        }
        return $ret;
    }
    public function upUserset($field, $name, $value)
    {
        global $system_users_soptions;
        $ret = intval($this->getVar($field, 'n'));
        if (!isset($system_users_soptions[$field])) {
            return $ret;
        }
        $p = array_search($name, $system_users_soptions[$field]);
        if ($p === false) {
            return $ret;
        }
        $value = 0 < $value ? '1' : '0';
        $s = decbin($ret);
        $l = strlen($s);
        $p += 1;
        if ($l < $p) {
            $s = str_repeat('0', $p - $l);
            $l = $p;
        }
        $s[$l - $p] = $value;
        return bindec($s);
    }
    public function encryptPass($pass)
    {
        $salt = $this->getVar('salt', 'n');
        if (!empty($salt)) {
            return md5(md5($pass) . $salt);
        } else {
            return md5($pass);
        }
    }
    public function getEmoney($type = 'all')
    {
        global $jieqiSetting;
        global $jieqiLang;
        $ret = array();
        $ret['egold'] = intval($this->getVar('egold'));
        $ret['esilver'] = intval($this->getVar('esilver'));
        $ret['emoney'] = $ret['egold'] + $ret['esilver'];
        switch ($type) {
            case 'egold':
                return $ret['egold'];
                break;
            case 'esilver':
                return $ret['esilver'];
                break;
            case 'emoney':
                return $ret['emoney'];
                break;
            case 'all':
            default:
                return $ret;
                break;
        }
    }
    public function saveToSession()
    {
        global $jieqiModules;
        global $jieqiHonors;
        if ($_SESSION['jieqiUserId'] == $this->getVar('uid')) {
            $_SESSION['jieqiUserName'] = 0 < strlen($this->getVar('name', 'n')) ? $this->getVar('name', 'n') : $this->getVar('uname', 'n');
            $_SESSION['jieqiUserPass'] = $this->getVar('pass', 'n');
            $_SESSION['jieqiUserGroup'] = $this->getVar('groupid', 'n');
            $_SESSION['jieqiUserEmail'] = $this->getVar('email', 'n');
            $_SESSION['jieqiUserAvatar'] = $this->getVar('avatar', 'n');
            $_SESSION['jieqiUserScore'] = $this->getVar('score', 'n');
            $_SESSION['jieqiUserExperience'] = $this->getVar('experience', 'n');
            $_SESSION['jieqiUserVip'] = $this->getVar('isvip', 'n');
            $_SESSION['jieqiUserEgold'] = $this->getVar('egold', 'n');
            $_SESSION['jieqiUserOvertime'] = intval($this->getVar('overtime', 'n'));
            jieqi_getconfigs('system', 'honors');
            $honorid = intval(jieqi_gethonorid($this->getVar('score'), $jieqiHonors));
            $_SESSION['jieqiUserHonorid'] = $honorid;
            $_SESSION['jieqiUserHonor'] = isset($jieqiHonors[$honorid]['name'][intval($this->getVar('workid', 'n'))]) ? $jieqiHonors[$honorid]['name'][intval($this->getVar('workid', 'n'))] : $jieqiHonors[$honorid]['caption'];
            if (!empty($jieqiModules['badge']['publish'])) {
                $_SESSION['jieqiUserBadges'] = $this->getVar('badges', 'n');
            }
            $_SESSION['jieqiUserSet'] = jieqi_unserialize($this->getVar('setting', 'n'));
            $_SESSION['jieqiUserVerify'] = JieqiUsersHandler::extractUserset($this->getVar('verify', 'n'), 'verify');
        }
    }
}
class JieqiUsersHandler extends JieqiObjectHandler
{
    public $tableFields = array();
    public $tableFieldid = array();
    public function __construct($db = '')
    {
        global $system_users_fields;
        parent::__construct($db);
        $this->tableFields =& $system_users_fields;
        $this->basename = 'users';
        $this->autoid = 'uid';
        $this->dbname = jieqi_dbprefix('system_users');
        $this->fullname = true;
        foreach ($this->tableFields as $k => $v) {
            $this->tableFieldid[$v['name']] = $k;
        }
    }
    public function validField($type, $str)
    {
        $fun = 'valid' . ucfirst($type);
        return $this->{$fun}($str);
    }
    public function validUsername($str)
    {
        global $jieqiConfigs;
        global $jieqiAction;
        global $jieqiDeny;
        global $jieqiLang;
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
        }
        if (!isset($jieqiAction['system'])) {
            jieqi_getconfigs('system', 'action', 'jieqiAction');
        }
        if (!isset($jieqiDeny['users'])) {
            jieqi_getconfigs('system', 'deny', 'jieqiDeny');
        }
        if (empty($jieqiLang['system']['users'])) {
            jieqi_loadlang('users', 'system');
        }
        $errors = array();
        if (strlen($str) == 0) {
            $errors[] = $jieqiLang['system']['need_username'];
        } else {
            if (!empty($jieqiAction['system']['register']['lenmin']) && strlen($str) < intval($jieqiAction['system']['register']['lenmin'])) {
                $errors[] = sprintf($jieqiLang['system']['username_over_lenmin'] . '<br />', $jieqiAction['system']['register']['lenmin']);
            } else {
                if (!empty($jieqiAction['system']['register']['lenmax']) && intval($jieqiAction['system']['register']['lenmax']) < strlen($str)) {
                    $errors[] = sprintf($jieqiLang['system']['username_over_lenmax'] . '<br />', $jieqiAction['system']['register']['lenmax']);
                }
            }
        }
        if ($jieqiConfigs['system']['usernamelimit'] == 1 && !preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $str)) {
            $errors[] = $jieqiLang['system']['username_need_engnum'];
        } else {
            if (preg_match('/[%,;:\\|\\*\\"\'\\\\\\/\\s\\t\\<\\>\\&]|^c:\\con\\con|^guest|^\\xD3\\xCE\\xBF\\xCD|\\xA1\\xA1|\\xAC\\xA3|\\xB9\\x43\\xAB\\xC8/is', $str)) {
                $errors[] = $jieqiLang['system']['error_user_format'];
            } else {
                if (!empty($jieqiDeny['users'])) {
                    include_once JIEQI_ROOT_PATH . '/include/checker.php';
                    $checker = new JieqiChecker();
                    $matchwords = $checker->deny_words($str, $jieqiDeny['users'], true, true);
                    if (is_array($matchwords)) {
                        $errors[] = $jieqiLang['system']['username_check_deny'];
                    }
                }
            }
        }
        if (empty($errors) && $this->getByname($str, 3) != false) {
            $errors[] = $jieqiLang['system']['user_has_registered'];
        }
        return $errors;
    }
    public function validNickname($str)
    {
        global $jieqiConfigs;
        global $jieqiAction;
        global $jieqiDeny;
        global $jieqiLang;
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
        }
        if (!isset($jieqiAction['system'])) {
            jieqi_getconfigs('system', 'action', 'jieqiAction');
        }
        if (!isset($jieqiDeny['users'])) {
            jieqi_getconfigs('system', 'deny', 'jieqiDeny');
        }
        if (empty($jieqiLang['system']['users'])) {
            jieqi_loadlang('users', 'system');
        }
        $errors = array();
        if (strlen($str) == 0) {
            $errors[] = $jieqiLang['system']['need_nickname'];
        }
        if (preg_match('/[%,;:\\|\\*\\"\'\\\\\\/\\s\\t\\<\\>\\&]|^c:\\con\\con|^guest|^\\xD3\\xCE\\xBF\\xCD|\\xA1\\xA1|\\xAC\\xA3|\\xB9\\x43\\xAB\\xC8/is', $str)) {
            $errors[] = $jieqiLang['system']['error_nick_format'];
        } else {
            if (!empty($jieqiDeny['users'])) {
                include_once JIEQI_ROOT_PATH . '/include/checker.php';
                $checker = new JieqiChecker();
                $matchwords = $checker->deny_words($str, $jieqiDeny['users'], true, true);
                if (is_array($matchwords)) {
                    $errors[] = $jieqiLang['system']['nickname_check_deny'];
                } else {
                    if ($this->getByname($str, 3) != false) {
                        $errors[] = $jieqiLang['system']['nick_has_used'];
                    }
                }
            }
        }
        return $errors;
    }
    public function validEmail($str)
    {
        global $jieqiConfigs;
        global $jieqiAction;
        global $jieqiDeny;
        global $jieqiLang;
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
        }
        if (!isset($jieqiAction['system'])) {
            jieqi_getconfigs('system', 'action', 'jieqiAction');
        }
        if (!isset($jieqiDeny['users'])) {
            jieqi_getconfigs('system', 'deny', 'jieqiDeny');
        }
        if (empty($jieqiLang['system']['users'])) {
            jieqi_loadlang('users', 'system');
        }
        $errors = array();
        if (empty($str)) {
            if (!empty($jieqiAction['system']['register']['needemail'])) {
                $errors[] = $jieqiLang['system']['need_email'];
            }
        } else {
            if (!preg_match('/^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+([\\.][a-z0-9-]+)+$/i', $str)) {
                $errors[] = $jieqiLang['system']['error_email_format'];
            } else {
                if (0 < $this->getCount(new Criteria('email', $str, '='))) {
                    $errors[] = $jieqiLang['system']['email_has_registered'];
                }
            }
        }
        return $errors;
    }
    public function validMobile($str)
    {
        global $jieqiConfigs;
        global $jieqiAction;
        global $jieqiDeny;
        global $jieqiLang;
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
        }
        if (!isset($jieqiAction['system'])) {
            jieqi_getconfigs('system', 'action', 'jieqiAction');
        }
        if (!isset($jieqiDeny['users'])) {
            jieqi_getconfigs('system', 'deny', 'jieqiDeny');
        }
        if (empty($jieqiLang['system']['users'])) {
            jieqi_loadlang('users', 'system');
        }
        $errors = array();
        if (empty($str)) {
            if (!empty($jieqiAction['system']['register']['needmobile'])) {
                $errors[] = $jieqiLang['system']['need_mobile'];
            }
        } else {
            if (!preg_match('/^(1[34578][0-9]{9})$/', $str)) {
                $errors[] = $jieqiLang['system']['error_mobile_format'];
            }
        }
        return $errors;
    }
    public function validPassword($str)
    {
        global $jieqiConfigs;
        global $jieqiAction;
        global $jieqiDeny;
        global $jieqiLang;
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
        }
        if (!isset($jieqiAction['system'])) {
            jieqi_getconfigs('system', 'action', 'jieqiAction');
        }
        if (!isset($jieqiDeny['users'])) {
            jieqi_getconfigs('system', 'deny', 'jieqiDeny');
        }
        if (empty($jieqiLang['system']['users'])) {
            jieqi_loadlang('users', 'system');
        }
        $errors = array();
        if (is_array($str)) {
            $password = $str['password'];
            $repassword = $str['repassword'];
        } else {
            $password = $str;
        }
        if (strlen($password) == 0) {
            $errors[] = $jieqiLang['system']['need_pass_repass'];
        } else {
            if (is_array($str) && $password != $repassword) {
                $errors[] = $jieqiLang['system']['password_not_equal'];
            } else {
                if (!empty($jieqiAction['system']['register']['passmin']) && strlen($password) < intval($jieqiAction['system']['register']['passmin'])) {
                    $errors[] = sprintf($jieqiLang['system']['password_over_lenmin'], $jieqiAction['system']['register']['passmin']);
                } else {
                    if (!empty($jieqiAction['system']['register']['passmax']) && intval($jieqiAction['system']['register']['passmax']) < strlen($password)) {
                        $errors[] = sprintf($jieqiLang['system']['password_over_lenmax'], $jieqiAction['system']['register']['passmax']);
                    }
                }
            }
        }
        return $errors;
    }
    public static function encryptPass($pass, $salt = '')
    {
        if (!empty($salt)) {
            return md5(md5($pass) . $salt);
        } else {
            return md5($pass);
        }
    }
    public static function extractUserset($var, $field, $name = '')
    {
        global $system_users_soptions;
        if (!isset($system_users_soptions[$field])) {
            return false;
        }
        $ret = $system_users_soptions[$field];
        $var = intval($var);
        $s = decbin($var);
        $l = strlen($s);
        $p = 1;
        foreach ($ret as $k => $v) {
            if ($p <= $l && $s[$l - $p] != '0') {
                $ret[$k] = 1;
            }
            if ($name == $k) {
                return $ret[$k];
            }
            $p++;
        }
        return $ret;
    }
    public function getByname($name, $flag = 1)
    {
        if (!empty($name)) {
            $name = jieqi_dbslashes($name);
            if ($flag == 3) {
                $sql = 'SELECT * FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' WHERE ' . $this->tableFields['uname']['name'] . '=\'' . $name . '\' OR ' . $this->tableFields['name']['name'] . '=\'' . $name . '\' ORDER BY name DESC';
            } else {
                if ($flag == 2) {
                    $sql = 'SELECT * FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' WHERE ' . $this->tableFields['name']['name'] . '=\'' . $name . '\'';
                } else {
                    $sql = 'SELECT * FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' WHERE ' . $this->tableFields['uname']['name'] . '=\'' . $name . '\'';
                }
            }
            if (!($result = $this->db->query($sql))) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 <= $numrows) {
                $tmpvar = 'Jieqi' . ucfirst($this->basename);
                ${$this}->basename = new $tmpvar();
                ${$this}->basename->setVars($this->db->fetchArray($result));
                return ${$this}->basename;
            }
        }
        return false;
    }
    public function changeCredit($uid, $credit, $isadd = true)
    {
        if (empty($credit) || !is_numeric($credit) || empty($uid) || !is_numeric($uid)) {
            return false;
        }
        if ($isadd) {
            $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET credit=credit+' . $credit . ' WHERE ' . $this->tableFields['uid']['name'] . '=' . $uid;
        } else {
            $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET credit=credit-' . $credit . ' WHERE ' . $this->tableFields['uid']['name'] . '=' . $uid;
        }
        $this->db->query($sql);
        return true;
    }
    public function changeScore($uid, $score, $isadd = true, $delexperience = true)
    {
        if (empty($score) || !is_numeric($score) || empty($uid) || !is_numeric($uid)) {
            return false;
        }
        if ($score < 0) {
            $isadd = !$isadd;
            $score = abs($score);
        }
        if ($isadd) {
            $tmpuser = $this->get($uid);
            if (!is_object($tmpuser)) {
                return false;
            }
            $oldscore = $tmpuser->getVar('lastscore', 'n');
            $lastdate = date('Y-m-d', $oldscore);
            $lasttime = JIEQI_NOW_TIME;
            $nowdate = date('Y-m-d', $lasttime);
            $nowweek = date('w', $lasttime);
            $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET experience=experience+' . $score . ', score=score+' . $score;
            if ($nowdate == $lastdate) {
                $sql .= ', monthscore=monthscore+' . $score . ', weekscore=weekscore+' . $score . ', dayscore=dayscore+' . $score;
            } else {
                if (substr($nowdate, 0, 7) == substr($lastdate, 0, 7)) {
                    $sql .= ', monthscore=monthscore+' . $score;
                } else {
                    $sql .= ', monthscore=' . $score;
                }
                if ($nowweek == 1) {
                    $sql .= ', weekscore=' . $score;
                } else {
                    $sql .= ', weekscore=weekscore+' . $score;
                }
                $sql .= ', dayscore=' . $score;
            }
            $sql .= ' WHERE ' . $this->tableFields['uid']['name'] . '=' . $uid;
        } else {
            if ($delexperience) {
                $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET experience=experience-' . $score . ', score=score-' . $score . ', monthscore=monthscore-' . $score . ' WHERE ' . $this->tableFields['uid']['name'] . '=' . $uid;
            } else {
                $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET score=score-' . $score . ', monthscore=monthscore-' . $score . ' WHERE ' . $this->tableFields['uid']['name'] . '=' . $uid;
            }
        }
        $this->db->query($sql);
        if ($_SESSION['jieqiUserId'] == $uid) {
            if ($isadd) {
                $_SESSION['jieqiUserScore'] = $_SESSION['jieqiUserScore'] + $score;
                $_SESSION['jieqiUserExperience'] = $_SESSION['jieqiUserExperience'] + $score;
            } else {
                $_SESSION['jieqiUserScore'] = $_SESSION['jieqiUserScore'] - $score;
                if ($delexperience) {
                    $_SESSION['jieqiUserExperience'] = $_SESSION['jieqiUserExperience'] - $score;
                }
            }
        }
        return true;
    }
    public function payout(&$users, $emoney, $usesilver = false)
    {
        $emoney = intval($emoney);
        if ($emoney < 0) {
            return false;
        } else {
            if ($emoney == 0) {
                return $users;
            }
        }
        if (is_a($users, 'JieqiUsers')) {
            $tmpuser =& $users;
            $uid = intval($tmpuser->getVar('uid', 'n'));
        } else {
            $uid = intval($users);
            if ($uid <= 0) {
                return false;
            }
            $tmpuser = $this->get($uid);
            if (!is_object($tmpuser)) {
                return false;
            }
        }
        $useregold = intval($tmpuser->getVar('egold', 'n'));
        $useresilver = $usesilver ? intval($tmpuser->getVar('esilver', 'n')) : 0;
        $expenses = intval($tmpuser->getVar('expenses', 'n'));
        $useremoney = $useregold + $useresilver;
        if ($useremoney < $emoney) {
            return false;
        }
        if ($emoney <= $useregold) {
            $tmpuser->setVar('egold', $useregold - $emoney);
        } else {
            if ($emoney <= $useresilver) {
                $tmpuser->setVar('esilver', $useresilver - $emoney);
            } else {
                $tmpuser->setVar('egold', 0);
                $tmpuser->setVar('esilver', $useresilver + $useregold - $emoney);
            }
        }
        $tmpuser->setVar('expenses', $expenses + $emoney);
        if (!empty($_SESSION['jieqiUserId']) && $tmpuser->getVar('uid', 'n') == $_SESSION['jieqiUserId']) {
            $tmpuser->saveToSession();
        }
        return $this->insert($tmpuser);
    }
    public function payback($uid, $emoney)
    {
        if (is_a($uid, 'JieqiUsers')) {
            $uid = intval($uid->getVar('uid', 'n'));
        } else {
            $uid = intval($uid);
        }
        $emoney = intval($emoney);
        if ($uid <= 0 || $emoney <= 0) {
            return false;
        }
        $sql = 'UPDATE ' . $this->dbname . ' SET egold = egold + ' . $emoney . ', expenses = expenses - ' . $emoney . ' WHERE uid = ' . $uid;
        return $this->execute($sql);
    }
    public function income($uid, $emoney, $extras = array())
    {
        global $jieqiConfigs;
        global $jieqiGroups;
        if (is_numeric($extras)) {
            $extras = array('usesilver' => $extras);
            $numargs = func_num_args();
            if (4 <= $numargs) {
                $extras['addscore'] = func_get_arg(3);
            }
            if (5 <= $numargs) {
                $extras['updatevip'] = func_get_arg(4);
            }
        } else {
            if (!is_array($extras)) {
                $extras = array();
            }
        }
        $tmpuser = $this->get($uid);
        if (is_object($tmpuser)) {
            if (isset($extras['emoneyrate']) && is_numeric($extras['emoneyrate']) && 0 < $extras['emoneyrate']) {
                jieqi_getconfigs('system', 'rule');
                if (function_exists('jieqi_rule_system_channelerate')) {
                    $extras['emoneyrate'] = jieqi_rule_system_channelerate($extras['emoneyrate'], $tmpuser);
                }
                $emoney = floor($emoney * $extras['emoneyrate'] / 100);
            }
            if (!empty($extras['usesilver'])) {
                $tmpuser->setVar('esilver', $tmpuser->getVar('esilver') + $emoney);
            } else {
                $tmpuser->setVar('egold', $tmpuser->getVar('egold') + $emoney);
            }
            $monthlyrows = array();
            if (0 <= $emoney) {
                $userset = jieqi_unserialize($tmpuser->getVar('setting', 'n'));
                if (isset($extras['updatevip']) && is_numeric($extras['updatevip']) && 0 < $extras['updatevip'] && $tmpuser->getVar('isvip') < $extras['updatevip']) {
                    $tmpuser->setVar('isvip', intval($extras['updatevip']));
                }
                if (0 <= $extras['moneytype']) {
                    global $jieqiTasks;
                    $taskscore = 0;
                    jieqi_getconfigs('system', 'tasks', 'jieqiTasks');
                    if (isset($jieqiTasks['pay']['buy']) && empty($userset['tasks']['pay']['buy'])) {
                        $userset['tasks']['pay']['buy'] = 1;
                        $taskscore = intval($jieqiTasks['pay']['buy']['score']);
                    }
                } else {
                    $taskscore = 0;
                }
                $addscore = empty($extras['addscore']) ? 0 : intval($extras['addscore']);
                $addscore += $taskscore;
                if (0 < $addscore) {
                    $tmpuser->setVar('score', $tmpuser->getVar('score') + $addscore);
                }
                if (isset($extras['updategroup'])) {
                    $groupid = $tmpuser->getVar('groupid', 'n');
                    if (is_numeric($extras['updategroup'])) {
                        $newgroup = intval($extras['updategroup']);
                    } else {
                        if (is_array($extras['updategroup']) && is_numeric($extras['updategroup'][$groupid])) {
                            $newgroup = intval($extras['updategroup'][$groupid]);
                        } else {
                            $newgroup = $groupid;
                        }
                    }
                    if ($groupid != JIEQI_GROUP_ADMIN && $groupid != $newgroup && isset($jieqiGroups[$newgroup])) {
                        $tmpuser->setVar('groupid', $newgroup);
                        $userset['pregroupid'] = $groupid;
                    }
                }
                if (isset($extras['addmonthly']) && is_numeric($extras['addmonthly']) && 0 < $extras['addmonthly']) {
                    $extras['addmonthly'] = intval($extras['addmonthly']);
                    $begintime = intval($tmpuser->getVar('overtime', 'n'));
                    if ($begintime < JIEQI_NOW_TIME) {
                        $begintime = JIEQI_NOW_TIME;
                    }
                    $overtime = mktime(date('H', $begintime), date('i', $begintime), date('s', $begintime), date('m', $begintime) + $extras['addmonthly'], date('d', $begintime), date('Y', $begintime));
                    $tmpuser->setVar('overtime', $overtime);
                    $monthlyrows['buytime'] = JIEQI_NOW_TIME;
                    $monthlyrows['userid'] = $tmpuser->getVar('uid', 'n');
                    $monthlyrows['username'] = $tmpuser->getVar('name', 'n');
                    $monthlyrows['month'] = $extras['addmonthly'];
                    $monthlyrows['vipbegin'] = $begintime;
                    $monthlyrows['vipend'] = $overtime;
                    $monthlyrows['egold'] = 0;
                    $monthlyrows['money'] = $extras['money'];
                    $monthlyrows['paytype'] = 1;
                    $monthlyrows['paynote'] = '';
                    $monthlyrows['payflag'] = 0;
                }
                $earnvipvote = 0;
                if (isset($extras['earnvipvote']) && is_numeric($extras['earnvipvote']) && 0 < $extras['earnvipvote']) {
                    $earnvipvote = intval($extras['earnvipvote']);
                } else {
                    if (0 < $emoney) {
                        if (!isset($jieqiConfigs['system'])) {
                            jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
                        }
                        if (!empty($jieqiConfigs['system']['inaddvipvote'])) {
                            $earnvipvote = floor($emoney / intval($jieqiConfigs['system']['inaddvipvote']));
                        }
                    }
                }
                if (0 < $earnvipvote) {
                    if (intval(date('Ym', intval($tmpuser->getVar('lastlogin', 'n')))) < intval(date('Ym', JIEQI_NOW_TIME))) {
                        $tmpuser->setVar('lastlogin', JIEQI_NOW_TIME);
                        $userset['gift']['vipvote'] = $earnvipvote;
                    } else {
                        $userset['gift']['vipvote'] = intval($userset['gift']['vipvote']) + $earnvipvote;
                    }
                }
                if (0 <= $extras['moneytype']) {
                    if (!isset($userset['lastpay']) || date('Y-m-d', intval($userset['lastpay'])) != date('Y-m-d', JIEQI_NOW_TIME)) {
                        $userset['lastpay'] = JIEQI_NOW_TIME;
                        include_once JIEQI_ROOT_PATH . '/include/funactivity.php';
                        jieqi_activity_update(array('acttype' => 'pay', 'userid' => $tmpuser->getVar('uid', 'n'), 'joindate' => date('Ymd', $tmpuser->getVar('regdate', 'n'))));
                    }
                }
                $tmpuser->setVar('setting', serialize($userset));
            }
            if (!empty($_SESSION['jieqiUserId']) && $uid == $_SESSION['jieqiUserId']) {
                $tmpuser->saveToSession();
            }
            $this->insert($tmpuser);
            if (!empty($monthlyrows)) {
                $sql = $this->makeupsql(jieqi_dbprefix('obook_monthlylog'), $monthlyrows, 'INSERT');
                $this->execute($sql);
            }
            if (!empty($extras['usesilver'])) {
                $earnlog = array();
                $earnlog['siteid'] = JIEQI_SITE_ID;
                $earnlog['logtime'] = JIEQI_NOW_TIME;
                $earnlog['userid'] = $tmpuser->getVar('uid', 'n');
                $earnlog['username'] = $tmpuser->getVar('name', 'n');
                $earnlog['emoney'] = $emoney;
                $earnlog['moneytype'] = isset($extras['frommoneytype']) ? $extras['frommoneytype'] : 0;
                $earnlog['money'] = isset($extras['frommoney']) ? $extras['frommoney'] : 0;
                $earnlog['fromuid'] = isset($extras['fromuid']) ? $extras['fromuid'] : 0;
                $earnlog['fromuname'] = isset($extras['fromuname']) ? $extras['fromuname'] : '';
                $earnlog['fromtid'] = isset($extras['fromtid']) ? $extras['fromtid'] : 0;
                $earnlog['fromtname'] = isset($extras['fromtname']) ? $extras['fromtname'] : '';
                $earnlog['earntype'] = isset($extras['earntype']) ? $extras['earntype'] : 0;
                $sql = $this->makeupsql(jieqi_dbprefix('system_earnlog'), $earnlog, 'INSERT');
                $this->execute($sql);
            }
            $userchannel = $tmpuser->getVar('channel', 'n');
            if (!empty($userchannel) && 0 <= $extras['moneytype'] && 0 < $extras['money']) {
                if (!isset($jieqiConfigs['system'])) {
                    jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
                }
                if (empty($jieqiConfigs['system']['channeldlimit'])) {
                    $jieqiConfigs['system']['channeldlimit'] = 0;
                } else {
                    $jieqiConfigs['system']['channeldlimit'] = intval($jieqiConfigs['system']['channeldlimit']);
                }
                if (isset($jieqiConfigs['system']['channelerate']) && is_numeric($jieqiConfigs['system']['channelerate']) && 0 < $jieqiConfigs['system']['channelerate'] && $jieqiConfigs['system']['channelerate'] <= 100 && ($jieqiConfigs['system']['channeldlimit'] <= 0 || JIEQI_NOW_TIME - $tmpuser->getVar('regdate', 'n') < $jieqiConfigs['system']['channeldlimit'] * 3600 * 24)) {
                    $fromuid = 0;
                    if (is_numeric($userchannel)) {
                        $fromuid = floor($userchannel);
                    } else {
                        global $jieqiChannels;
                        if (!isset($jieqiChannels)) {
                            jieqi_getconfigs('system', 'channels');
                        }
                        if (!empty($jieqiChannels[$userchannel]['uid'])) {
                            $fromuid = floor($jieqiChannels[$userchannel]['uid']);
                        }
                    }
                    if (0 < $fromuid) {
                        $drate = 1;
                        if ($extras['moneytype'] == 1) {
                            if (isset($jieqiConfigs['channeldrate']) && is_numeric($jieqiConfigs['channeldrate']) && 0 < $jieqiConfigs['channeldrate']) {
                                $drate = $jieqiConfigs['channeldrate'];
                            } else {
                                $drate = 6;
                            }
                        }
                        $esilver = $extras['money'] * $drate;
                        if (0 < $esilver) {
                            $this->income($fromuid, $esilver, array('moneytype' => -11, 'money' => 0, 'emoneyrate' => $jieqiConfigs['system']['channelerate'], 'usesilver' => 1, 'earntype' => 1, 'frommoneytype' => $extras['moneytype'], 'frommoney' => $extras['money'], 'fromuid' => $tmpuser->getVar('uid', 'n'), 'fromuname' => $tmpuser->getVar('name', 'n')));
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
    public function usersAdd(&$params)
    {
        global $jieqiAction;
        global $jieqiConfigs;
        global $query;
        if (!isset($jieqiAction['system'])) {
            jieqi_getconfigs('system', 'action', 'jieqiAction');
        }
        include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
        $newUser = $this->create();
        $newUser->setVar('siteid', JIEQI_SITE_ID);
        $newUser->setVar('uname', $params['username']);
        if (!isset($params['nickname'])) {
            $params['nickname'] = $params['username'];
        }
        $newUser->setVar('name', $params['nickname']);
        $salt = substr(md5(uniqid(rand(), true)), -16);
        $newUser->setVar('salt', $salt);
        if (!isset($params['password']) || strlen($params['password']) == 0) {
            $newUser->setVar('pass', '');
        } else {
            $newUser->setVar('pass', $this->encryptPass($params['password'], $salt));
        }
        $newUser->setVar('groupid', JIEQI_GROUP_USER);
        $newUser->setVar('regdate', JIEQI_NOW_TIME);
        $newUser->setVar('initial', jieqi_getinitial($params['username']));
        if (isset($params['sex'])) {
            $newUser->setVar('sex', intval($params['sex']));
        }
        if (isset($params['workid'])) {
            $newUser->setVar('workid', intval($params['workid']));
        }
        if (isset($params['email'])) {
            $newUser->setVar('email', $params['email']);
        }
        if (isset($params['url'])) {
            $newUser->setVar('url', $params['url']);
        }
        if (isset($params['qq'])) {
            $newUser->setVar('qq', $params['qq']);
        }
        if (isset($params['weixin'])) {
            $newUser->setVar('weixin', $params['weixin']);
        }
        if (isset($params['weibo'])) {
            $newUser->setVar('weibo', $params['weibo']);
        }
        if (isset($params['mobile'])) {
            $newUser->setVar('mobile', $params['mobile']);
        }
        if (isset($params['uip'])) {
            $newUser->setVar('setting', serialize(array('regip' => $params['uip'])));
        }
        $newUser->setVar('lastlogin', JIEQI_NOW_TIME);
        if (isset($params['verify'])) {
            $newUser->setVar('verify', intval($params['verify']));
        }
        if (isset($params['showset_email']) && $params['showset_email'] != 1) {
            $params['showset_email'] = 0;
        }
        $newUser->setVar('showset', $params['showset_email']);
        if (isset($params['acceptset_email']) && $params['acceptset_email'] != 1) {
            $params['acceptset_email'] = 0;
        }
        $newUser->setVar('acceptset', $params['acceptset_email']);
        $action_earnscore = intval($jieqiAction['system']['register']['earnscore']);
        $newUser->setVar('experience', $action_earnscore);
        $newUser->setVar('score', $action_earnscore);
        if (isset($params['apiname'])) {
            $newUser->setVar('apiname', $params['apiname']);
        }
        if (isset($_COOKIE['jieqiChannel']) && 0 < strlen($_COOKIE['jieqiChannel'])) {
            if (is_numeric($_COOKIE['jieqiChannel'])) {
                $newUser->setVar('channel', floor($_COOKIE['jieqiChannel']));
            } else {
                global $jieqiChannels;
                if (!isset($jieqiChannels)) {
                    jieqi_getconfigs('system', 'channels');
                }
                if (isset($jieqiChannels[$_COOKIE['jieqiChannel']])) {
                    $newUser->setVar('channel', $_COOKIE['jieqiChannel']);
                }
            }
        }
        if (defined('JIEQI_DEVICE_FOR')) {
            $newUser->setVar('device', JIEQI_DEVICE_FOR);
        }
        if (!$this->insert($newUser)) {
            $params['uid'] = 0;
            return false;
        } else {
            $params['uid'] = $newUser->getVar('uid', 'n');
            if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
                jieqi_includedb();
                $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            }
            if (!empty($params['uip'])) {
                if (!isset($jieqiConfigs['system'])) {
                    jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
                }
                if (0 < $jieqiConfigs['system']['regtimelimit']) {
                    $sql = 'DELETE FROM ' . jieqi_dbprefix('system_registerip') . ' WHERE regtime<' . (JIEQI_NOW_TIME - (72 < $jieqiConfigs['system']['regtimelimit'] ? $jieqiConfigs['system']['regtimelimit'] : 72) * 3600);
                    $query->execute($sql);
                    $sql = 'INSERT INTO ' . jieqi_dbprefix('system_registerip') . ' (`ip`, `regtime`, `count`) VALUES (\'' . jieqi_dbslashes($params['uip']) . '\', \'' . JIEQI_NOW_TIME . '\', \'0\')';
                    $query->execute($sql);
                }
            }
            $fieldrows = array();
            $fieldrows['userid'] = $newUser->getVar('uid', 'n');
            $fieldrows['username'] = $newUser->getVar('name', 'n');
            $fieldrows['apiname'] = $newUser->getVar('apiname', 'n');
            $fieldrows['channel'] = $newUser->getVar('channel', 'n');
            $fieldrows['device'] = $newUser->getVar('device', 'n');
            $fieldrows['joinmonth'] = date('Ym', $newUser->getVar('regdate', 'n'));
            $fieldrows['joindate'] = date('Ymd', $newUser->getVar('regdate', 'n'));
            $fieldrows['lastdate'] = $fieldrows['joindate'];
            $sql = $query->makeupsql(jieqi_dbprefix('system_activity'), $fieldrows, 'INSERT');
            $query->execute($sql);
            if (!empty($jieqiAction['system']['register']['islog'])) {
                include_once JIEQI_ROOT_PATH . '/include/funaction.php';
                $actions = array('actname' => 'register', 'actnum' => 1);
                jieqi_system_actionlog($actions, $newUser);
            }
            if (0 < JIEQI_PROMOTION_REGISTER && !empty($_COOKIE['jieqiChannel']) && is_numeric($_COOKIE['jieqiChannel'])) {
                $this->changeCredit(intval($_COOKIE['jieqiChannel']), intval(JIEQI_PROMOTION_REGISTER), true);
            }
            if (isset($_COOKIE['jieqiChannel'])) {
                setcookie('jieqiChannel', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
            }
            return $newUser;
        }
    }
    public function get($id, $fieldname = '')
    {
        if (is_numeric($id) && 0 < intval($id)) {
            $id = intval($id);
            $fieldname = $this->tableFields[$this->autoid]['name'];
            $sql = 'SELECT * FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' WHERE ' . $fieldname . '=' . $id;
            if (!($result = $this->db->query($sql, 0, 0, true))) {
                return false;
            }
            $datarow = $this->db->fetchArray($result);
            if (is_array($datarow)) {
                $tmpvar = 'Jieqi' . ucfirst($this->basename);
                ${$this->basename} = new $tmpvar();
                foreach ($datarow as $k => $v) {
                    if (isset($this->tableFieldid[$k])) {
                        ${$this->basename}->setVar($this->tableFieldid[$k], $v, true, false);
                    } else {
                        ${$this->basename}->setVar($k, $v, true, false);
                    }
                }
                return ${$this->basename};
            }
        }
        return false;
    }
    public function insert(&$baseobj)
    {
        if (strcasecmp(get_class($baseobj), 'jieqi' . $this->basename) != 0) {
            return false;
        }
        if ($baseobj->isNew()) {
            if (is_numeric($baseobj->getVar($this->autoid, 'n'))) {
                ${$this->autoid} = intval($baseobj->getVar($this->autoid, 'n'));
            } else {
                ${$this->autoid} = $this->db->genId($this->dbname . '_' . $this->autoid . '_seq');
            }
            $sql = 'INSERT INTO ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' (';
            $values = ') VALUES (';
            $start = true;
            foreach ($baseobj->vars as $k => $v) {
                if (!$start) {
                    $sql .= ', ';
                    $values .= ', ';
                } else {
                    $start = false;
                }
                if (isset($this->tableFields[$k]['name'])) {
                    $sql .= $this->tableFields[$k]['name'];
                } else {
                    $sql .= $k;
                }
                if ($v['type'] == JIEQI_TYPE_INT) {
                    if ($k != $this->autoid) {
                        if (!is_numeric($v['value'])) {
                            $v['value'] = @intval($v['value']);
                        }
                        $values .= $this->db->quoteString($v['value']);
                    } else {
                        $values .= ${$this->autoid};
                    }
                } else {
                    $values .= $this->db->quoteString($v['value']);
                }
            }
            $sql .= $values . ')';
            unset($values);
        } else {
            $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET ';
            $start = true;
            foreach ($baseobj->vars as $k => $v) {
                if ($k != $this->autoid && $v['isdirty']) {
                    if (!$start) {
                        $sql .= ', ';
                    } else {
                        $start = false;
                    }
                    if (isset($this->tableFields[$k]['name'])) {
                        $k = $this->tableFields[$k]['name'];
                    }
                    if ($v['type'] == JIEQI_TYPE_INT) {
                        if (!is_numeric($v['value'])) {
                            $v['value'] = @intval($v['value']);
                        }
                        $sql .= $k . '=' . $this->db->quoteString($v['value']);
                    } else {
                        $sql .= $k . '=' . $this->db->quoteString($v['value']);
                    }
                }
            }
            if ($start) {
                return true;
            }
            $sql .= ' WHERE ' . $this->tableFields[$this->autoid]['name'] . '=' . intval($baseobj->vars[$this->autoid]['value']);
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        if ($baseobj->isNew()) {
            $baseobj->setVar($this->autoid, $this->db->getInsertId());
        }
        return true;
    }
    public function delete($criteria = 0, $fieldname = '')
    {
        $sql = '';
        if (is_numeric($criteria)) {
            $criteria = intval($criteria);
            $fieldname = $this->tableFields[$this->autoid]['name'];
            $sql = 'DELETE FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' WHERE ' . $fieldname . '=' . $criteria;
        } else {
            if (is_object($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
                $tmpstr = $criteria->renderWhere();
                if (!empty($tmpstr)) {
                    $sql = 'DELETE FROM ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' ' . $tmpstr;
                }
            }
        }
        if (empty($sql)) {
            return false;
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        return true;
    }
    public function queryObjects($criteria = NULL, $nobuffer = false)
    {
        $limit = $start = 0;
        $sql = 'SELECT * FROM ' . jieqi_dbprefix($this->dbname, $this->fullname);
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->getGroupby() != '') {
                $sql .= ' GROUP BY ' . $criteria->getGroupby();
            }
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $this->sqlres = $this->db->query($sql, $limit, $start, $nobuffer);
        return $this->sqlres;
    }
    public function getObject($result = '')
    {
        static $dbrowobj;
        if ($result == '') {
            $result = $this->sqlres;
        }
        if (!$result) {
            return false;
        } else {
            $tmpvar = 'Jieqi' . ucfirst($this->basename);
            $myrow = $this->db->fetchArray($result);
            if (!$myrow) {
                return false;
            } else {
                if (!isset($dbrowobj)) {
                    $dbrowobj = new $tmpvar();
                }
                foreach ($myrow as $k => $v) {
                    if (isset($this->tableFieldid[$k])) {
                        $dbrowobj->setVar($this->tableFieldid[$k], $v, true, false);
                    } else {
                        $dbrowobj->setVar($k, $v, true, false);
                    }
                }
                return $dbrowobj;
            }
        }
    }
    public function getRow($result = '')
    {
        if ($result == '') {
            $result = $this->sqlres;
        }
        if (!$result) {
            return false;
        } else {
            $myrow = $this->db->fetchArray($result);
            if (!$myrow) {
                return false;
            } else {
                return $myrow;
            }
        }
    }
    public function getCount($criteria = NULL)
    {
        $sql = 'SELECT COUNT(*) FROM ' . jieqi_dbprefix($this->dbname, $this->fullname);
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql, 0, 0, true);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }
    public function updatefields($fields, $criteria = NULL)
    {
        $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname, $this->fullname) . ' SET ';
        $start = true;
        if (is_array($fields)) {
            foreach ($fields as $k => $v) {
                if (!$start) {
                    $sql .= ', ';
                } else {
                    $start = false;
                }
                if (isset($this->tableFields[$k]['name'])) {
                    $k = $this->tableFields[$k]['name'];
                }
                if (is_numeric($v)) {
                    $sql .= $k . '=' . $v;
                } else {
                    $sql .= $k . '=' . $this->db->quoteString($v);
                }
            }
        } else {
            $sql .= $fields;
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!($result = $this->db->query($sql))) {
            return false;
        }
        return true;
    }
}
jieqi_includedb();
global $system_users_fields;
$system_users_fields = array();
$system_users_fields['uid'] = array('name' => 'uid', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '序号', 'required' => false, 'maxlength' => 11);
$system_users_fields['siteid'] = array('name' => 'siteid', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '网站序号', 'required' => false, 'maxlength' => 6);
$system_users_fields['uname'] = array('name' => 'uname', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '用户名', 'required' => true, 'maxlength' => 30);
$system_users_fields['name'] = array('name' => 'name', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '真实姓名', 'required' => false, 'maxlength' => 60);
$system_users_fields['pass'] = array('name' => 'pass', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '密码', 'required' => false, 'maxlength' => 32);
$system_users_fields['salt'] = array('name' => 'salt', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '密码加盐', 'required' => false, 'maxlength' => 32);
$system_users_fields['groupid'] = array('name' => 'groupid', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '用户组序号', 'required' => false, 'maxlength' => 3);
$system_users_fields['regdate'] = array('name' => 'regdate', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '注册日期', 'required' => false, 'maxlength' => 11);
$system_users_fields['initial'] = array('name' => 'initial', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '用户名首字母', 'required' => false, 'maxlength' => 1);
$system_users_fields['sex'] = array('name' => 'sex', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '性别', 'required' => false, 'maxlength' => 1);
$system_users_fields['email'] = array('name' => 'email', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => 'Email', 'required' => true, 'maxlength' => 60);
$system_users_fields['url'] = array('name' => 'url', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '网站', 'required' => false, 'maxlength' => 100);
$system_users_fields['avatar'] = array('name' => 'avatar', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '头像', 'required' => false, 'maxlength' => 11);
$system_users_fields['workid'] = array('name' => 'workid', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '职业', 'required' => false, 'maxlength' => 11);
$system_users_fields['qq'] = array('name' => 'qq', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => 'QQ', 'required' => false, 'maxlength' => 15);
$system_users_fields['weixin'] = array('name' => 'weixin', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '微信', 'required' => false, 'maxlength' => 15);
$system_users_fields['weibo'] = array('name' => 'weibo', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '微博', 'required' => false, 'maxlength' => 60);
$system_users_fields['mobile'] = array('name' => 'mobile', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '手机', 'required' => false, 'maxlength' => 20);
$system_users_fields['sign'] = array('name' => 'sign', 'type' => JIEQI_TYPE_TXTAREA, 'value' => '', 'caption' => '签名', 'required' => false, 'maxlength' => NULL);
$system_users_fields['intro'] = array('name' => 'intro', 'type' => JIEQI_TYPE_TXTAREA, 'value' => '', 'caption' => '个人简介', 'required' => false, 'maxlength' => NULL);
$system_users_fields['setting'] = array('name' => 'setting', 'type' => JIEQI_TYPE_TXTAREA, 'value' => '', 'caption' => '用户设置', 'required' => false, 'maxlength' => NULL);
$system_users_fields['badges'] = array('name' => 'badges', 'type' => JIEQI_TYPE_TXTAREA, 'value' => '', 'caption' => '其他信息', 'required' => false, 'maxlength' => NULL);
$system_users_fields['lastlogin'] = array('name' => 'lastlogin', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '最后登录', 'required' => false, 'maxlength' => 10);
$system_users_fields['verify'] = array('name' => 'verify', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '已验证信息', 'required' => false, 'maxlength' => 11);
$system_users_fields['showset'] = array('name' => 'showset', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '显示设置', 'required' => false, 'maxlength' => 11);
$system_users_fields['acceptset'] = array('name' => 'acceptset', 'type' => JIEQI_TYPE_INT, 'value' => 1, 'caption' => '接受设置', 'required' => false, 'maxlength' => 11);
$system_users_fields['monthscore'] = array('name' => 'monthscore', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '本月积分', 'required' => false, 'maxlength' => 11);
$system_users_fields['weekscore'] = array('name' => 'weekscore', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '本周积分', 'required' => false, 'maxlength' => 11);
$system_users_fields['dayscore'] = array('name' => 'dayscore', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '本日积分', 'required' => false, 'maxlength' => 11);
$system_users_fields['lastscore'] = array('name' => 'lastscore', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '最后积分', 'required' => false, 'maxlength' => 11);
$system_users_fields['experience'] = array('name' => 'experience', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '经验值', 'required' => false, 'maxlength' => 11);
$system_users_fields['score'] = array('name' => 'score', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '积分', 'required' => false, 'maxlength' => 11);
$system_users_fields['egold'] = array('name' => 'egold', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '虚拟货币', 'required' => false, 'maxlength' => 11);
$system_users_fields['esilver'] = array('name' => 'esilver', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '收入金额', 'required' => false, 'maxlength' => 11);
$system_users_fields['sumtip'] = array('name' => 'sumtip', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '打赏收入', 'required' => false, 'maxlength' => 11);
$system_users_fields['sumtask'] = array('name' => 'sumtask', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '任务收入', 'required' => false, 'maxlength' => 11);
$system_users_fields['sumworks'] = array('name' => 'sumworks', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '作品收入', 'required' => false, 'maxlength' => 11);
$system_users_fields['sumaward'] = array('name' => 'sumaward', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '奖金收入', 'required' => false, 'maxlength' => 11);
$system_users_fields['sumother'] = array('name' => 'sumother', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '其它收入', 'required' => false, 'maxlength' => 11);
$system_users_fields['sumemoney'] = array('name' => 'sumemoney', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '虚拟币收入', 'required' => false, 'maxlength' => 11);
$system_users_fields['summoney'] = array('name' => 'summoney', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '现金收入', 'required' => false, 'maxlength' => 11);
$system_users_fields['paidmoney'] = array('name' => 'paidmoney', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '已付现金', 'required' => false, 'maxlength' => 11);
$system_users_fields['paidemoney'] = array('name' => 'paidemoney', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '已付虚拟币', 'required' => false, 'maxlength' => 11);
$system_users_fields['paytime'] = array('name' => 'paytime', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '付款时间', 'required' => false, 'maxlength' => 11);
$system_users_fields['credit'] = array('name' => 'credit', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '信用度', 'required' => false, 'maxlength' => 11);
$system_users_fields['goodnum'] = array('name' => 'goodnum', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '好评', 'required' => false, 'maxlength' => 11);
$system_users_fields['badnum'] = array('name' => 'badnum', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '差评', 'required' => false, 'maxlength' => 11);
$system_users_fields['expenses'] = array('name' => 'expenses', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '消费额', 'required' => false, 'maxlength' => 11);
$system_users_fields['conisbind'] = array('name' => 'conisbind', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '是否绑定QQ登录', 'required' => false, 'maxlength' => 11);
$system_users_fields['apiname'] = array('name' => 'apiname', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '登录接口名', 'required' => false, 'maxlength' => 100);
$system_users_fields['channel'] = array('name' => 'channel', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '会员来源渠道', 'required' => false, 'maxlength' => 100);
$system_users_fields['device'] = array('name' => 'device', 'type' => JIEQI_TYPE_TXTBOX, 'value' => '', 'caption' => '会员使用设备', 'required' => false, 'maxlength' => 100);
$system_users_fields['viplevel'] = array('name' => 'viplevel', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '包月等级', 'required' => false, 'maxlength' => 11);
$system_users_fields['isvip'] = array('name' => 'isvip', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '是否VIP会员', 'required' => false, 'maxlength' => 1);
$system_users_fields['overtime'] = array('name' => 'overtime', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '过期时间', 'required' => false, 'maxlength' => 11);
$system_users_fields['state'] = array('name' => 'state', 'type' => JIEQI_TYPE_INT, 'value' => 0, 'caption' => '用户包月等级', 'required' => false, 'maxlength' => 1);
global $system_users_soptions;
$system_users_soptions = array('verify' => array('email' => 0, 'mobile' => 0), 'showset' => array('email' => 0), 'acceptset' => array('email' => 0));