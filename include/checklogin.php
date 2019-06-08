<?php
/**
 * 检查用户登录
 *
 * 验证登录账号、密码、验证码等
 *
 * 调用模板：无
 *
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: checklogin.php 344 2009-06-23 03:06:07Z juny $
 */

/**
 * 验证登录账号、密码、验证码，同过的话进行登录处理
 *
 * @param      string $username 用户名
 * @param      string $password 密码
 * @param      string $checkcode 验证码（false表示不校验）
 * @param      int $usecookie 是否记录到cookie，下次自动登录。0表示不记录，大于0表示cookie保存时间
 * @param      int $encode 密码是否已经加密，0-不加密 1-加密 2-二次加密 3-16位加密
 * @param      int $uidtype username类型，-1-自动判断 0-用户名 1-用户ID 2-Email 3-Mobile
 * @access     public
 * @return     int         0 正常, -1 用户名为空 -2 密码为空 -3 用户名或者密码为空 -4 用户名不存在 -5 密码错误 -6 用户名或密码错误 -7 验证码错误 -8 帐号已经有人登陆 -9 用户属于游客组
 */
function jieqi_logincheck($username = '', $password = '', $checkcode = '', $usecookie = 0, $encode = 0, $uidtype = 0){
	global $users_handler;

	if($uidtype == -1){
		$uidtype = 0;
		if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i', $username)) $uidtype = 2;
		elseif(floatval(JIEQI_VERSION) >= 2.3){
			if(preg_match('/^1[34578]\d{9}$/', $username)) $uidtype = 3;
		}
		elseif(is_numeric($username)) $uidtype = 1;
	}
	if(!in_array($uidtype, array(0, 1, 2, 3))) $uidtype = 0;
	$ret = jieqi_loginpass($username, $password, $checkcode, $usecookie, $encode, $uidtype);
	if(is_object($ret)){
		return jieqi_loginprocess($ret, $usecookie);
	}
	elseif($ret == -10){
		if(!in_array($uidtype, array(0, 1, 2))) $uidtype = 0;
		//临时用户，未设置密码，ucenter存在时候自动更新密码，否则返回密码错误
		if(defined('JIEQI_USER_INTERFACE') && preg_match('/^\w+$/is', JIEQI_USER_INTERFACE)) include_once(JIEQI_ROOT_PATH . '/include/funuser_' . JIEQI_USER_INTERFACE . '.php');
		else include_once(JIEQI_ROOT_PATH . '/include/funuser.php');
		if(function_exists('uc_user_login')){
			$isuid = $uidtype == 1 ? 1 : 0;
			list($uid, $uname, $upass, $uemail) = uc_user_login($username, $password, $isuid);
			if($uid > 0){
				if(!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')){
					include_once(JIEQI_ROOT_PATH . '/class/users.php');
					$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
				}
				switch($uidtype){
					case 1:
						$criteria = new CriteriaCompo(new Criteria('uid', intval($username)));
						break;
					case 2:
						$criteria = new CriteriaCompo(new Criteria('email', $username));
						break;
					case 3:
						$criteria = new CriteriaCompo(new Criteria('mobile', intval($username)));
						break;
					case 0:
					default:
						$criteria = new CriteriaCompo(new Criteria('uname', $username));
						break;
				}
				$criteria->setLimit(1);
				$users_handler->queryObjects($criteria);
				$userobj = $users_handler->getObject();
				if(is_object($userobj)){
					$salt = $userobj->getVar('salt', 'n');
					$userobj->setVar('pass', $users_handler->encryptPass($upass, $salt));
					$userobj->setVar('email', $uemail);
					$users_handler->insert($userobj);
					return jieqi_loginprocess($userobj, $usecookie);
				}
			}
		}
		return -5;
	}
	else{
		return $ret;
	}
}

/**
 * 仅验证登录账号、密码、验证码，返回是否验证通过信息
 *
 * @param      string $username 用户名
 * @param      string $password 密码
 * @param      string $checkcode 验证码
 * @param      int $usecookie 是否记录到cookie，下次自动登录。0表示不记录，大于0表示cookie保存时间
 * @param      int $encode 密码是否已经加密，0-不加密 1-加密 2-二次加密 3-16位加密
 * @param      int $uidtype username类型，0-用户名 1-用户ID 2-Email 3-Mobile
 * @access     public
 * @return     int         0 正常, -1 用户名为空 -2 密码为空 -3 用户名或者密码为空 -4 用户名不存在 -5 密码错误 -6 用户名或密码错误 -7 验证码错误 -8 帐号已经有人登陆 -9 用户属于游客组 -10 未设置密码
 */
function jieqi_loginpass($username = '', $password = '', $checkcode = '', $usecookie = 0, $encode = 0, $uidtype = 0){
	global $jieqiConfigs;
	global $jieqiHonors;
	global $jieqiGroups;
	global $users_handler;
	if(empty($username) || empty($password)) return -3;

	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
	//检查验证码
	if(!defined('JIEQI_NO_CHECKCODE') && defined('JIEQI_LOGIN_CHECKCODE') && JIEQI_LOGIN_CHECKCODE > 0 && $checkcode !== false){
		if(empty($checkcode) || empty($_SESSION['jieqiCheckCode']) || strtolower($checkcode) != strtolower($_SESSION['jieqiCheckCode'])) return -7;
	}
	//检查用户名和密码
	if(!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH . '/class/users.php');
		$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	switch($uidtype){
		case 1:
			$criteria = new CriteriaCompo(new Criteria('uid', intval($username)));
			break;
		case 2:
			$criteria = new CriteriaCompo(new Criteria('email', $username));
			break;
		case 3:
			$criteria = new CriteriaCompo(new Criteria('mobile', intval($username)));
			break;
		case 0:
		default:
			$criteria = new CriteriaCompo(new Criteria('uname', $username));
			break;
	}

	$criteria->setLimit(1);
	$users_handler->queryObjects($criteria);
	$jieqiUsers = $users_handler->getObject();
	if(!$jieqiUsers){
		return -4;
	}
	$truepass = $jieqiUsers->getVar('pass', 'n');
	if($truepass == '') return -10;

	$passcheck = false;
	$salt = $jieqiUsers->getVar('salt', 'n');
	switch($encode){
		case 1:
			if($truepass == $password) $passcheck = true;
			break;
		case 2:
			if($users_handler->encryptPass($truepass, $salt) == $password) $passcheck = true;
			break;
		case 3:
			if(substr($truepass, 0, 16) == substr($password, 0, 16)) $passcheck = true;
			break;
		case 0:
		default:
			if($truepass == $users_handler->encryptPass($password, $salt)) $passcheck = true;
			break;
	}
	if(!$passcheck){
		return -5;
	}

	if($jieqiUsers->getVar('groupid', 'n') == JIEQI_GROUP_GUEST){
		return -9;
	}

	return $jieqiUsers;
}

/**
 * 用户登录后处理
 *
 * @param      object $jieqiUsers 用户对象
 * @access     public
 * @return     bool
 */
function jieqi_loginprocess($jieqiUsers, $usecookie = 0){
	global $jieqiConfigs;
	global $jieqiHonors;
	global $jieqiGroups;
	global $users_handler;
	global $jieqiAction;
	global $jieqiLang;
	global $query;

	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
	if(!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH . '/class/users.php');
		$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	//更新在线用户表
	include_once(JIEQI_ROOT_PATH . '/class/online.php');
	$online_handler = JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
	if(!$jieqiUsers->isNew()){
		$criteria = new CriteriaCompo(new Criteria('uid', $jieqiUsers->getVar('uid', 'n')));
		$criteria->setSort('updatetime');
		$criteria->setOrder('DESC');
		$online_handler->queryObjects($criteria);
		$online = $online_handler->getObject();
	}else{
		$online = false;
	}

	//读cookie信息
	$jieqi_user_info = array();
	if(!empty($_COOKIE['jieqiUserInfo'])) $jieqi_user_info = jieqi_strtosary($_COOKIE['jieqiUserInfo']);
	else $jieqi_user_info = array();
	$jieqi_visit_info = array();
	if(!empty($_COOKIE['jieqiVisitInfo'])) $jieqi_visit_info = jieqi_strtosary($_COOKIE['jieqiVisitInfo']);
	else $jieqi_visit_info = array();


	if(is_object($online)){
		$ip = jieqi_userip();
		if(JIEQI_SESSION_EXPRIE > 0) $exprie_time = JIEQI_SESSION_EXPRIE;
		else $exprie_time = @ini_get('session.gc_maxlifetime');
		if(empty($exprie_time)) $exprie_time = 1800;
		if(defined('JIEQI_DENY_RELOGIN') && JIEQI_DENY_RELOGIN == 1 && JIEQI_NOW_TIME - $online->getVar('updatetime') < $exprie_time && $online->getVar('ip', 'n') != $ip && $jieqi_visit_info['jieqiUserId'] != $jieqiUsers->getVar('uid')){
			return -8;
		}
		$tmpvar = strlen($jieqiUsers->getVar('name', 'q')) > 0 ? $jieqiUsers->getVar('name', 'q') : $jieqiUsers->getVar('uname', 'q');
		$sql = "UPDATE " . jieqi_dbprefix('system_online') . " SET uid=" . $jieqiUsers->getVar('uid', 'q') . ", sid='" . jieqi_dbslashes(session_id()) . "', uname='" . $jieqiUsers->getVar('uname', 'q') . "', name='" . $tmpvar . "', pass='" . $jieqiUsers->getVar('pass', 'q') . "',email='" . $jieqiUsers->getVar('email', 'q') . "', groupid=" . $jieqiUsers->getVar('groupid', 'q') . ", updatetime=" . JIEQI_NOW_TIME . ", ip='" . jieqi_dbslashes($ip) . "' WHERE uid=" . $jieqiUsers->getVar('uid', 'q') . " OR sid='" . jieqi_dbslashes(session_id()) . "'";
		$online_handler->execute($sql);
	}
	else{
		include_once(JIEQI_ROOT_PATH . '/include/visitorinfo.php');
		$online = $online_handler->create();
		$online->setVar('uid', $jieqiUsers->getVar('uid', 'n'));
		$online->setVar('siteid', JIEQI_SITE_ID);
		$online->setVar('sid', session_id());
		$online->setVar('uname', $jieqiUsers->getVar('uname', 'n'));
		$tmpvar = strlen($jieqiUsers->getVar('name', 'n')) > 0 ? $jieqiUsers->getVar('name', 'n') : $jieqiUsers->getVar('uname', 'n');
		$online->setVar('name', $tmpvar);
		$online->setVar('pass', $jieqiUsers->getVar('pass', 'n'));
		$online->setVar('email', $jieqiUsers->getVar('email', 'n'));
		$online->setVar('groupid', $jieqiUsers->getVar('groupid', 'n'));
		$tmpvar = JIEQI_NOW_TIME;
		$online->setVar('logintime', $tmpvar);
		$online->setVar('updatetime', $tmpvar);
		$online->setVar('operate', '');
		$tmpvar = VisitorInfo::getIp();
		$online->setVar('ip', $tmpvar);
		$online->setVar('browser', VisitorInfo::getBrowser());
		$online->setVar('os', VisitorInfo::getOS());
		$location = VisitorInfo::getIpLocation($tmpvar);
		if(JIEQI_SYSTEM_CHARSET == 'big5'){
			include_once(JIEQI_ROOT_PATH . '/include/changecode.php');
			$location = jieqi_gb2big5($location);
		}
		$online->setVar('location', $location);
		$online->setVar('state', '0');
		$online->setVar('flag', '0');
		$online_handler->insert($online);
	}
	//删除过期的在线用户
	unset($criteria);
	$ontime = intval($jieqiConfigs['system']['onlinetime']) * 4;
	if($ontime < 7200) $ontime = 7200;
	$criteria = new CriteriaCompo(new Criteria('updatetime', JIEQI_NOW_TIME - $ontime, '<'));
	$online_handler->delete($criteria);

	//登录用户更新资料，新注册的不用
	$newmsgnum = 0;
	if(!$jieqiUsers->isNew()){
		//载入动作参数（不使用统一动作处理，而是分别处理增加积分和记录日志）
		jieqi_getconfigs('system', 'action', 'jieqiAction');
		//参数设置数组
		$userset = jieqi_unserialize($jieqiUsers->getVar('setting', 'n'));

		//检查短消息
		if(!isset($query) || !is_a($query, 'JieqiQueryHandler')){
			jieqi_includedb();
			$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
		}
		$toid = intval($jieqiUsers->getVar('uid', 'n'));
		$toegold = intval($jieqiUsers->getVar('egold', 'n'));
		if(!empty($jieqiConfigs['system']['lowegoldwarn']) && intval($jieqiUsers->getVar('isvip', 'n')) > 0 &&  $toegold < intval($jieqiConfigs['system']['lowegoldwarn'])){
			$sql = "SELECT * FROM " . jieqi_dbprefix('system_message') . " WHERE toid = {$toid} AND messagetype = -11 ORDER BY messageid DESC LIMIT 0, 1";
			$res = $query->execute($sql);
			$msg = $query->getRow($res);

			$lastpay = isset($userset['lastpay']) ? intval($userset['lastpay']) : 0;
			if(!is_array($msg) || $lastpay > $msg['postdate']){
				include_once(JIEQI_ROOT_PATH . '/include/funmessage.php');
				if(empty($jieqiLang['system']['users'])) jieqi_loadlang('users', 'system');
				jieqi_sendmessage(array('toid' => $toid, 'toname' => $jieqiUsers->getVar('name', 'n'), 'title' => $jieqiLang['system']['user_lowegold_title'], 'content' => sprintf($jieqiLang['system']['user_lowegold_content'], $toegold . JIEQI_EGOLD_NAME), 'messagetype' => -11));
			}
		}
		$sql = "SELECT count(*) AS cot FROM " . jieqi_dbprefix('system_message') . " WHERE toid = {$toid} AND isread = 0 AND todel = 0";
		$res = $query->execute($sql);
		$row = $query->getRow($res);
		$newmsgnum = intval($row['cot']);

		//用户信息
		$lastlogin = intval($jieqiUsers->getVar('lastlogin', 'n'));
		$jieqiUsers->setVar('lastlogin', JIEQI_NOW_TIME);
		//最后登录ip
		if(!isset($userset['lastip']) || $userset['lastip'] != jieqi_userip()) $userset['lastip'] = jieqi_userip();
		$userset['loginsid'] = session_id();

		$nowdate = date('Y-m-d', JIEQI_NOW_TIME);
		if(!isset($userset['logindate']) || $userset['logindate'] != $nowdate){
			$userset['logindate'] = date('Y-m-d');
			//活跃天数
			$userset['logindays'] = isset($userset['logindays']) ? $userset['logindays'] + 1 : 1;

			//增加登陆积分
			$action_earnscore = intval($jieqiAction['system']['login']['earnscore']);
			$jieqiUsers->setVar('experience', $jieqiUsers->getVar('experience') + $action_earnscore);
			$jieqiUsers->setVar('score', $jieqiUsers->getVar('score') + $action_earnscore);
			//记录登录活跃度
			include_once(JIEQI_ROOT_PATH . '/include/funactivity.php');
			jieqi_activity_update(array('acttype'=>'login', 'userid'=>$jieqiUsers->getVar('uid', 'n'), 'joindate'=>date('Ymd', $jieqiUsers->getVar('regdate', 'n'))));

		}
		//消费增加月票
		$countpayout = isset($userset['countpayout']) ? intval($userset['countpayout']) : 0;
		$expenses = intval($jieqiUsers->getVar('expenses'));
		if($expenses > $countpayout && !empty($jieqiConfigs['system']['outaddvipvote'])){
			$vipvoteadd = floor(($expenses - $countpayout) / intval($jieqiConfigs['system']['outaddvipvote']));
			$userset['gift']['vipvote'] = isset($userset['gift']['vipvote']) ? intval($userset['gift']['vipvote']) + $vipvoteadd : $vipvoteadd;
			$userset['countpayout'] = $countpayout + ($vipvoteadd * intval($jieqiConfigs['system']['outaddvipvote']));
		}

		//切换到下个月的处理
		if(intval(date('Ym', $lastlogin)) < intval(date('Ym', JIEQI_NOW_TIME))){
			$jieqiUsers->setVar('monthscore', 0);
			if(isset($userset['gift']['vipvote'])) $userset['gift']['vipvote'] = 0;
		}

		//包月到期处理，如果有之前用户等级记录的，退回等级
		if($jieqiUsers->getVar('overtime', 'n') > 0 && intval($jieqiUsers->getVar('overtime', 'n')) < JIEQI_NOW_TIME && !empty($userset['pregroupid'])){
			$jieqiUsers->setVar('groupid', intval($userset['pregroupid']));
			unset($userset['pregroupid']);
		}

		$jieqiUsers->setVar('setting', serialize($userset));
		$jieqiUsers->unsetNew();
		$users_handler->insert($jieqiUsers);

		//动作处理(记录日志)
		if(!empty($jieqiAction['system']['login']['islog'])){
			include_once (JIEQI_ROOT_PATH . '/include/funaction.php');
			$actions = array(
				'actname' => 'login',
				'actnum' => 1
			);
			jieqi_system_actionlog($actions, $jieqiUsers);
		}
	}

	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	//设置SESSION
	jieqi_setusersession($jieqiUsers);

	if($newmsgnum > 0) $_SESSION['jieqiNewMessage'] = $newmsgnum;
	//后台登录状态
	$jieqi_online_info = empty($_COOKIE['jieqiOnlineInfo']) ? array() : jieqi_strtosary($_COOKIE['jieqiOnlineInfo']);
	if(isset($jieqi_online_info['jieqiAdminLogin']) && $jieqi_online_info['jieqiAdminLogin'] == 1) $_SESSION['jieqiAdminLogin'] = 1;

	$jieqi_user_info['jieqiUserId'] = $_SESSION['jieqiUserId'];
	$jieqi_user_info['jieqiUserUname'] = $_SESSION['jieqiUserUname'];
	$jieqi_user_info['jieqiUserName'] = $_SESSION['jieqiUserName'];
	$jieqi_user_info['jieqiUserGroup'] = $_SESSION['jieqiUserGroup'];
	$jieqi_user_info['jieqiUserGroupName'] = $jieqiGroups[$_SESSION['jieqiUserGroup']];
	$jieqi_user_info['jieqiUserVip'] = $_SESSION['jieqiUserVip'];
	$jieqi_user_info['jieqiUserHonorId'] = $_SESSION['jieqiUserHonorId'];
	$jieqi_user_info['jieqiUserHonor'] = $_SESSION['jieqiUserHonor'];
	$jieqi_user_info['jieqiUserToken'] = $_SESSION['jieqiUserToken'];
	$jieqi_user_info['jieqiCodeLogin'] = (defined('JIEQI_LOGIN_CHECKCODE') && !defined('JIEQI_NO_CHECKCODE')) ? JIEQI_LOGIN_CHECKCODE : 0;
	$jieqi_user_info['jieqiCodePost'] = intval($jieqiConfigs['system']['postcheckcode']);

	if($newmsgnum > 0) $jieqi_user_info['jieqiNewMessage'] = $newmsgnum;
	if($usecookie) $jieqi_user_info['jieqiUserPassword'] = $jieqiUsers->getVar('pass', 'n');
	include_once(JIEQI_ROOT_PATH . '/include/changecode.php');

	//用户名不转换，自动登录时候用到
	if(JIEQI_SYSTEM_CHARSET == 'gbk'){
		//$jieqi_user_info['jieqiUserUname'] = jieqi_gb2unicode($_SESSION['jieqiUserUname']);
		$jieqi_user_info['jieqiUserName'] = jieqi_gb2unicode($_SESSION['jieqiUserName']);
		$jieqi_user_info['jieqiUserHonor'] = jieqi_gb2unicode($_SESSION['jieqiUserHonor']);
		$jieqi_user_info['jieqiUserGroupName'] = jieqi_gb2unicode($jieqiGroups[$_SESSION['jieqiUserGroup']]);
	}
	elseif(JIEQI_SYSTEM_CHARSET == 'big5'){
		//$jieqi_user_info['jieqiUserUname'] = jieqi_big52unicode($_SESSION['jieqiUserUname']);
		$jieqi_user_info['jieqiUserName'] = jieqi_big52unicode($_SESSION['jieqiUserName']);
		$jieqi_user_info['jieqiUserHonor'] = jieqi_big52unicode($_SESSION['jieqiUserHonor']);
		$jieqi_user_info['jieqiUserGroupName'] = jieqi_big52unicode($jieqiGroups[$_SESSION['jieqiUserGroup']]);
	}
	$jieqi_user_info['jieqiUserLogin'] = JIEQI_NOW_TIME;
	if($usecookie < 0) $usecookie = 0;
	elseif($usecookie == 1) $usecookie = 315360000;
	if($usecookie) $cookietime = JIEQI_NOW_TIME + $usecookie;
	else $cookietime = 0;
	@setcookie('jieqiUserInfo', jieqi_sarytostr($jieqi_user_info), $cookietime, '/', JIEQI_COOKIE_DOMAIN, 0);
	$jieqi_visit_info['jieqiUserLogin'] = $jieqi_user_info['jieqiUserLogin'];
	$jieqi_visit_info['jieqiUserId'] = $jieqi_user_info['jieqiUserId'];
	@setcookie('jieqiVisitInfo', jieqi_sarytostr($jieqi_visit_info), JIEQI_NOW_TIME + 99999999, '/', JIEQI_COOKIE_DOMAIN, 0);
	return 0;
}

?>