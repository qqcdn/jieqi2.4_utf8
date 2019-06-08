<?php
//客户端设备检查处理
function jieqi_device_check(){
	if(jieqi_ismobile()){
		//手机访问情况
		if(defined('JIEQI_MOBILE_LOCATION') && strlen(trim(JIEQI_MOBILE_LOCATION)) > 0 && defined('JIEQI_LOCAL_URL') && strpos(JIEQI_LOCAL_URL, JIEQI_MOBILE_LOCATION) === false){
			$url = jieqi_device_route_mobile();
			if(!empty($url)){
				header('Location: ' . jieqi_headstr($url));
				exit();
			}
		}
	}else{
		//电脑访问情况
		if(defined('JIEQI_PC_LOCATION') && strlen(trim(JIEQI_PC_LOCATION)) > 0 && defined('JIEQI_LOCAL_URL') && strpos(JIEQI_LOCAL_URL, JIEQI_PC_LOCATION) === false){
			$url = jieqi_device_route_pc();
			if(!empty($url)){
				header('Location: ' . jieqi_headstr($url));
				exit();
			}
		}
	}
}

//跳转到手机站规则
function jieqi_device_route_mobile(){
	if(!defined('JIEQI_MOBILE_LOCATION') || $_REQUEST['device'] == 'pc') return false;
	if(preg_match(jieqi_device_route_match(), $_SERVER['REQUEST_URI'])) $url = JIEQI_MOBILE_LOCATION . $_SERVER['REQUEST_URI'];
	else $url = JIEQI_MOBILE_LOCATION;
	return $url;
}

//跳转到电脑站规则
function jieqi_device_route_pc(){
	if(!defined('JIEQI_PC_LOCATION') || $_REQUEST['device'] == 'mobile') return false;
	if(preg_match(jieqi_device_route_match(), $_SERVER['REQUEST_URI'])) $url = JIEQI_PC_LOCATION . $_SERVER['REQUEST_URI'];
	else $url = JIEQI_PC_LOCATION;
	return $url;
}

//匹配常用跳转页面
function jieqi_device_route_match(){
	global $jieqiConfigs;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
	$urlary = array();
	if(empty($jieqiConfigs['article']['fakeinfo'])) $urlary['articleinfo'] = '\/modules\/article\/articleinfo\.php\?id=\d+';
	else $urlary['articleinfo'] = str_replace(array('\\<\\{\\$jieqi_url\\}\\>', '\\<\\{\\$id\\}\\>', '\\<\\{\\$id\\|subdirectory\\}\\>', '\\<\\{\\$acode\\}\\>'), array('', '\\d+', '/\\d+', '\\w+'), preg_quote($jieqiConfigs['article']['fakeinfo']));

	if(empty($jieqiConfigs['article']['fakearticle'])) $urlary['articleindex'] = '\/modules\/article\/reader\.php\?aid=\d+';
	else $urlary['articleindex'] = str_replace(array('\\<\\{\\$jieqi_url\\}\\>', '\\<\\{\\$aid\\}\\>', '\\<\\{\\$aid\\|subdirectory\\}\\>', '\\<\\{\\$acode\\}\\>'), array('', '\\d+', '/\\d+', '\\w+'), preg_quote($jieqiConfigs['article']['fakearticle']));

	if(empty($jieqiConfigs['article']['fakechapter'])) $urlary['articlechapter'] = '\/modules\/article\/reader\.php\?aid=\d+&cid=\d+';
	else $urlary['articlechapter'] = str_replace(array('\\<\\{\\$jieqi_url\\}\\>', '\\<\\{\\$aid\\}\\>', '\\<\\{\\$aid\\|subdirectory\\}\\>', '\\<\\{\\$aid\\}\\>', '\\<\\{\\$acode\\}\\>'), array('', '\\d+', '/\\d+', '\\d+', '\\w+'), preg_quote($jieqiConfigs['article']['fakechapter']));

	$matchstr = '/^('.implode('|', $urlary) . ')$/is';
	return $matchstr;
}
?>