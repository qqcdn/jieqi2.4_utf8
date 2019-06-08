<?php
/**
 * 操作小说和章节的通用函数
 *
 * 操作小说和章节的通用函数
 *
 * 调用模板：无
 *
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: actarticle.php 339 2009-06-23 03:03:24Z juny $
 */

if(!defined('JIEQI_ROOT_PATH')) exit();

include_once($jieqiModules['article']['path'] . '/class/article.php');
if(!isset($article_handler)) $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
include_once($jieqiModules['article']['path'] . '/class/chapter.php');
if(!isset($chapter_handler)) $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
global $jieqiConfigs;
if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
$article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];

/*
 * 增加小说变量规范
 * articlename - 小说名（必选）
 * articleid - 指定小说ID（可选），编辑小说的时候必选
 * sortid - 分类ID（必选），默认 0
 * typeid - 子类ID（可选），默认 0
 * backupname - 副标题（可选）
 * keywords - 标签（可选），多个标签用空格分隔
 * agent - 本书管理员（可选），指定一个本站会员名作为本书管理员
 * author - 作者名（可选），仅网站管理员可以输入作者名，默认发表者即作者
 * authorflag - 是否授权给作者（可选），0-不授权，作者名可以填任意名字，1-授权，作者名必须本站会员名
 * reviewer - 书评版主（可选），作者修改
 * permission - 授权级别（可选）
 * firstflag - 首发状态（可选）
 * progress - 写作进度（可选）
 * intro - 内容简介（可选）
 * notice - 本书公告（可选）
 * articlespic - 上传的封面小图（可选）
 * articlelpic - 上传的封面大图（可选）
 *
 * siteid - 来源网站ID（可选），默认0，表示本站作品
 * sourceid - 来源站小说ID，采集时候用
 * postdate - 创建时间，采集时候用
 * lastupdate - 更新时间，采集时候用
 * urlspic - url格式的封面小图（可选），采集时候用
 * urllpic - url格式的封面大图（可选），采集时候用
 * articlecode - 小说拼音代码（可选），默认自动生成，允许管理员自定义
 * eachlinkids - 推荐小说ID（可选），必须是本站小说ID
 * foreword - 编辑点评（可选）,管理员可编辑
 * poster - 发表者（可选），必须是本站会员，管理员可编辑
 * unionid - 是否书盟作品（0-否 1000-是）
 * fullflag - 是否全本（可选），0-连载，1-全本
 * isvip - 是否VIP（可选），0-免费，1-vip
 * issign - 是否签约（可选），0-未签约，1-普通签约，10-vip签约
 * rgroup - 所属频道ID（可选）
 * isshare - 是否共享（可选）
 * monthly - 是否包月（可选），未启用
 * buyout - 是否买断（可选），未启用
 * discount - 是否打折（可选），未启用
 * quality - 是否精品（可选），未启用
 * ispub - 是否已出版（可选），未启用
 *
 *
 * dayvisit - 日点击（可选）
 * weekvisit - 周点击（可选）
 * monthvisit - 月点击（可选）
 * allvisit - 总点击（可选）
 * dayvote - 日推荐（可选）
 * weekvote - 周推荐（可选）
 * monthvote -月推荐（可选）
 * allvote - 总推荐（可选）
 * dayflower - 日鲜花（可选）
 * weekflower - 周鲜花（可选）
 * monthflower -月鲜花（可选）
 * allflower - 总鲜花（可选）
 * dayegg - 日鸡蛋（可选）
 * weekegg - 周鸡蛋（可选）
 * monthegg -月鸡蛋（可选）
 * allegg - 总鸡蛋（可选）
 * dayvipvote - 日月票（可选）
 * weekvipvote - 周月票（可选）
 * monthvipvote -月月票（可选）
 * allvipvote - 总月票（可选）
 *
 */

// 检查新建小说的内容是否合法
function jieqi_article_articlepcheck(&$postvars, $options = array(), $article = ''){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiOption;
	global $jieqiSort;
	global $jieqiLang;
	global $jieqiSites;
	global $jieqiAction;
	global $jieqiDeny;
	global $jieqiPower;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	global $article_handler;

	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(empty($jieqiSort['article'])) jieqi_getconfigs('article', 'sort', 'jieqiSort');
	if(empty($jieqiOption['article'])) jieqi_getconfigs('article', 'option', 'jieqiOption');
	if(empty($jieqiSort['article'])) jieqi_getconfigs('article', 'sort', 'jieqiSort');
	if(empty($jieqiLang['article']['article'])) jieqi_loadlang('article', 'article');
	if(empty($jieqiSites)) jieqi_getconfigs('system', 'sites', 'jieqiSites');
	if(empty($jieqiAction['article'])) jieqi_getconfigs('article', 'action', 'jieqiAction');
	if(empty($jieqiDeny['article'])) jieqi_getconfigs('article', 'deny', 'jieqiDeny');
	if(empty($jieqiPower['article'])) jieqi_getconfigs('article', 'power', 'jieqiPower');

	if(!isset($options['action'])) $options['action'] = 'add';
	if(!isset($options['ismanager'])) $options['ismanager'] = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
	if(!isset($options['allowtrans'])) $options['allowtrans'] = jieqi_checkpower($jieqiPower['article']['transarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);

	$errors = array();
	include_once(JIEQI_ROOT_PATH . '/lib/text/textfunction.php');

	$postvars['articlename'] = trim($postvars['articlename']);
	if(isset($postvars['backupname'])) $postvars['backupname'] = trim($postvars['backupname']);
	if(isset($postvars['author'])) $postvars['author'] = trim($postvars['author']);
	if(isset($postvars['agent'])) $postvars['agent'] = trim($postvars['agent']);
	$postvars['sortid'] = isset($postvars['sortid']) ? intval($postvars['sortid']) : 0;
	$postvars['typeid'] = isset($postvars['typeid']) ? intval($postvars['typeid']) : 0;
	if(!isset($jieqiSort['article'][$postvars['sortid']]['types'][$postvars['typeid']])) $postvars['typeid'] = 0;
	if(!isset($jieqiSort['article'][$postvars['sortid']])) $postvars['sortid'] = 0;

	// 检查标题
	if(strlen($postvars['articlename']) == 0) $errors[] = $jieqiLang['article']['need_article_title'];
	elseif(!jieqi_safestring($postvars['articlename'])) $errors[] = $jieqiLang['article']['limit_article_title'];

	// 检查标题和简介有没有违禁单词
	if(!isset($jieqiDeny['article'])) $jieqiDeny['article'] = $jieqiConfigs['system']['postdenywords'];
	include_once(JIEQI_ROOT_PATH . '/include/checker.php');
	$checker = new JieqiChecker();
	if(!empty($jieqiDeny['article']) || !empty($jieqiConfigs['system']['postdenywords'])){
		if(!empty($jieqiDeny['article'])){
			$matchwords = $checker->deny_words($postvars['articlename'], $jieqiDeny['article'], true, true);
			if(is_array($matchwords)) $errors[] = sprintf($jieqiLang['article']['article_deny_articlename'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
		}
		if(!empty($jieqiConfigs['system']['postdenywords'])){
			if(!empty($postvars['intro'])){
				$matchwords = $checker->deny_words($postvars['intro'], $jieqiConfigs['system']['postdenywords'], true);
				if(is_array($matchwords)) $errors[] = sprintf($jieqiLang['article']['article_deny_intro'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
			}
			if(!empty($postvars['notice'])){
				$matchwords = $checker->deny_words($postvars['notice'], $jieqiConfigs['system']['postdenywords'], true);
				if(is_array($matchwords)) $errors[] = sprintf($jieqiLang['article']['article_deny_notice'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
			}
			if(!empty($postvars['keywords'])){
				$matchwords = $checker->deny_words($postvars['keywords'], $jieqiConfigs['system']['postdenywords'], true);
				if(is_array($matchwords)) $errors[] = sprintf($jieqiLang['article']['article_deny_keywords'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
			}
		}
	}

	// 检查封面
	$typeary = explode(' ', trim($jieqiConfigs['article']['imagetype']));
	foreach($typeary as $k => $v){
		if(substr($v, 0, 1) != '.') $typeary[$k] = '.' . $typeary[$k];
	}
	if(!empty($_FILES['articlespic']['name'])){
		$simage_postfix = strrchr(trim(strtolower($_FILES['articlespic']['name'])), ".");
		if(preg_match("/\.(gif|jpg|jpeg|png|bmp)$/i", $_FILES['articlespic']['name'])){
			if(!in_array($simage_postfix, $typeary)) $errors[] = sprintf($jieqiLang['article']['simage_type_error'], $jieqiConfigs['article']['imagetype']);
			elseif(function_exists('getimagesize') && getimagesize($_FILES['articlespic']['tmp_name']) === false) $errors[] = sprintf($jieqiLang['article']['simage_not_image'], $_FILES['articlespic']['name']);
		}
		else{
			$errors[] = sprintf($jieqiLang['article']['simage_not_image'], $_FILES['articlespic']['name']);
		}
		if(!empty($errtext)) jieqi_delfile($_FILES['articlespic']['tmp_name']);
	}
	if(!empty($_FILES['articlelpic']['name'])){
		$limage_postfix = strrchr(trim(strtolower($_FILES['articlelpic']['name'])), ".");
		if(preg_match("/\.(gif|jpg|jpeg|png|bmp)$/i", $_FILES['articlelpic']['name'])){
			if(!in_array($limage_postfix, $typeary)) $errors[] = sprintf($jieqiLang['article']['limage_type_error'], $jieqiConfigs['article']['imagetype']);
			elseif(function_exists('getimagesize') && getimagesize($_FILES['articlelpic']['tmp_name']) === false) $errors[] = sprintf($jieqiLang['article']['limage_not_image'], $_FILES['articlelpic']['name']);
		}
		else{
			$errors[] = sprintf($jieqiLang['article']['limage_not_image'], $_FILES['articlelpic']['name']);
		}
		if(!empty($errtext)) jieqi_delfile($_FILES['articlelpic']['tmp_name']);
	}

	// 检查指定小说ID
	if(!empty($postvars['articleid']) && $options['allowtrans']){
		$article_handler->execute("SELECT MAX(articleid) AS mid FROM " . jieqi_dbprefix('article_article'));
		$tmprow = $article_handler->getRow();
		if(isset($tmprow['mid'])) $max_articleid = intval($tmprow['mid']);
		else $max_articleid = 0;
		$postvars['articleid'] = intval($postvars['articleid']);
		if($postvars['articleid'] <= 0 || $postvars['articleid'] > $max_articleid){
			$errors[] = sprintf($jieqiLang['article']['customid_number_limit'], $max_articleid);
		}
		else{
			$tmparticle = $article_handler->get($postvars['articleid']);
			if(is_object($tmparticle)) $errors[] = sprintf($jieqiLang['article']['customid_is_exists'], $postvars['articleid']);
		}
	}
	else{
		$postvars['articleid'] = 0;
	}

	// 检查小说是否已经发表
	if($jieqiConfigs['article']['samearticlename'] != 1 && (empty($article) || $article->getVar('articlename', 'n') != $postvars['articlename'])){
		if($article_handler->getCount(new Criteria('articlename', $postvars['articlename'], '=')) > 0) $errors[] = sprintf($jieqiLang['article']['articletitle_has_exists'], jieqi_htmlstr($postvars['articlename']));
	}

	//检查小说拼音是否可用
	$customacode = false; //是否自定义拼音代码
	$postvars['updateacode'] = false; //是否存在拼音重读要加上小说ID的情况
	if($options['ismanager'] && !empty($postvars['articlecode'])) $customacode = true;
	else $postvars['articlecode'] = jieqi_getpinyin($postvars['articlename']);
	$postvars['articlecode'] = strtolower($postvars['articlecode']);
	if(strlen($postvars['articlecode']) > 180) $postvars['articlecode'] = substr($postvars['articlecode'], 0, 180);
	if(!preg_match('/^[a-z]/i', $postvars['articlecode'])) $postvars['articlecode'] = 'i' . str_replace('_', '', $postvars['articlecode']);
	if(empty($article) || $article->getVar('articlecode', 'n') != $postvars['articlecode']){
		if($article_handler->getCount(new Criteria('articlecode', $postvars['articlecode'], '=')) > 0){
			if($customacode) $errors[] = sprintf($jieqiLang['article']['articlecode_has_exists'], jieqi_htmlstr($postvars['articlecode']));
			elseif(is_object($article)) $postvars['articlecode'] .= '_' . $article->getVar('articleid', 'n');
			else $postvars['updateacode'] = true;
		}
	}

	//关键字过滤
	if(!empty($jieqiConfigs['system']['postreplacewords'])){
		if(!empty($postvars['intro'])) $checker->replace_words($postvars['intro'], $jieqiConfigs['system']['postreplacewords']);
		if(!empty($postvars['notice'])) $checker->replace_words($postvars['notice'], $jieqiConfigs['system']['postreplacewords']);
		if(!empty($postvars['keywords'])) $checker->replace_words($postvars['keywords'], $jieqiConfigs['system']['postreplacewords']);
	}

	return $errors;
}

// 保存小说信息到数据库
function jieqi_article_articleadd(&$postvars, $options = array(), $article = ''){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiOption;
	global $jieqiSort;
	global $jieqiLang;
	global $jieqiSites;
	global $jieqiAction;
	global $jieqiDeny;
	global $jieqiPower;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	global $article_handler;
	global $users_handler;
	global $query;

	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(empty($jieqiSort['article'])) jieqi_getconfigs('article', 'sort', 'jieqiSort');
	if(empty($jieqiOption['article'])) jieqi_getconfigs('article', 'option', 'jieqiOption');
	if(empty($jieqiSort['article'])) jieqi_getconfigs('article', 'sort', 'jieqiSort');
	if(empty($jieqiLang['article']['article'])) jieqi_loadlang('article', 'article');
	if(empty($jieqiSites)) jieqi_getconfigs('system', 'sites', 'jieqiSites');
	if(empty($jieqiAction['article'])) jieqi_getconfigs('article', 'action', 'jieqiAction');
	if(empty($jieqiDeny['article'])) jieqi_getconfigs('article', 'deny', 'jieqiDeny');
	if(empty($jieqiPower['article'])) jieqi_getconfigs('article', 'power', 'jieqiPower');

	if(!isset($options['action'])) $options['action'] = 'add';
	if(!isset($options['ismanager'])) $options['ismanager'] = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
	if(!isset($options['allowtrans'])) $options['allowtrans'] = jieqi_checkpower($jieqiPower['article']['transarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);

	include_once(JIEQI_ROOT_PATH . '/lib/text/textfunction.php');

	if(!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH . '/class/users.php');
		$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	if(!isset($query) || !is_a($query, 'JieqiQueryHandler')) $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');

	if($options['action'] == 'add'){
		$article = $article_handler->create();
		if($options['allowtrans'] && !empty($postvars['articleid'])) $article->setVar('articleid', $postvars['articleid']);
		$siteid = empty($postvars['siteid']) ? 0 : intval($postvars['siteid']);
		$article->setVar('siteid', $siteid);
		$sourceid = empty($postvars['sourceid']) ? 0 : intval($postvars['sourceid']);
		$article->setVar('sourceid', $sourceid);
		$postdate = empty($postvars['postdate']) ? JIEQI_NOW_TIME : intval($postvars['postdate']);
		$article->setVar('postdate', $postdate);
		$lastupdate = empty($postvars['lastupdate']) ? 0 : intval($postvars['lastupdate']);
		$article->setVar('lastupdate', $lastupdate);
		$unionid = empty($postvars['unionid']) ? 0 : intval($postvars['unionid']);
		$article->setVar('unionid', $unionid);
	}
	if($article->getVar('infoupdate', 'n') !== false){
		$infoupdate = empty($postvars['infoupdate']) ? JIEQI_NOW_TIME : intval($postvars['infoupdate']);
		$article->setVar('infoupdate', $infoupdate);
	}

	$article->setVar('articlename', $postvars['articlename']);
	if(!$postvars['updateacode']) $article->setVar('articlecode', $postvars['articlecode']);
	if(isset($postvars['backupname'])) $article->setVar('backupname', $postvars['backupname']);

	// tag处理
	if(floatval(JIEQI_VERSION) >= 2){
		include_once(JIEQI_ROOT_PATH . '/include/funtag.php');
		if($options['action'] == 'edit') $oldtags = jieqi_tag_clean($article->getVar('keywords', 'n'));
		$tagary = jieqi_tag_clean($postvars['keywords']);
		$postvars['keywords'] = implode(' ', $tagary);
	}
	$article->setVar('keywords', trim($postvars['keywords']));

	$article->setVar('initial', jieqi_getinitial($postvars['articlename']));


	// 是否允许转载
	if($options['allowtrans']){
		// 允许转载的情况
		if(empty($postvars['author']) || (!empty($_SESSION['jieqiUserId']) && $postvars['author'] == $_SESSION['jieqiUserName'])){
			if(!empty($_SESSION['jieqiUserId'])){
				$article->setVar('authorid', $_SESSION['jieqiUserId']);
				$article->setVar('author', $_SESSION['jieqiUserName']);
			}
			else{
				$article->setVar('authorid', 0);
				$article->setVar('author', '');
			}
		}
		else{
			// 转载作品
			$article->setVar('author', $postvars['author']);
			if($postvars['authorflag']){
				$authorobj = $users_handler->getByname($postvars['author'], 3);
				if(is_object($authorobj)) $article->setVar('authorid', $authorobj->getVar('uid'));
				else $article->setVar('authorid', 0);
			}
			else{
				$article->setVar('authorid', 0);
			}
		}
	}
	else{
		// 不允许转载的情况
		if($options['action'] == 'add'){
			if(!empty($_SESSION['jieqiUserId'])){
				$article->setVar('authorid', $_SESSION['jieqiUserId']);
				$article->setVar('author', $_SESSION['jieqiUserName']);
			}
			else{
				$article->setVar('authorid', 0);
				$article->setVar('author', '');
			}
		}
		//$article->setVar('permission', intval($jieqiOption['article']['permission']['items'][$jieqiOption['article']['permission']['default']]));
	}

	//默认允许作者自己修改的选项
	if(isset($jieqiOption['article']['firstflag']['items'][$postvars['firstflag']])) $article->setVar('firstflag', $postvars['firstflag']); //首发状态
	if(isset($jieqiOption['article']['permission']['items'][$postvars['permission']])) $article->setVar('permission', $postvars['permission']); // 授权级别
	if(isset($jieqiOption['article']['isshort']['items'][$postvars['isshort']])) $article->setVar('isshort', $postvars['isshort']); // 是否短篇
	if(isset($jieqiOption['article']['inmatch']['items'][$postvars['inmatch']])) $article->setVar('inmatch', $postvars['inmatch']); // 是否参赛
	//所属频道，如果分类里面有频道标志，以分类为主，否则以作者提交为准
	$rgroup = 0;
	if(isset($jieqiSort['article'][$postvars['sortid']]['group']) && $jieqiSort['article'][$postvars['sortid']]['group'] >= 0) $rgroup = intval($jieqiSort['article'][$postvars['sortid']]['group']);
	elseif(isset($postvars['rgroup'])) $rgroup = intval($postvars['rgroup']);
	if(isset($jieqiOption['article']['rgroup']['items'][$rgroup])) $article->setVar('rgroup', $rgroup); //所属频道 男生/女生


	// 写作进度
	if(isset($postvars['progress'])){
		$postvars['progress'] = intval($postvars['progress']);
		if(isset($jieqiOption['article']['progress']['items'][$postvars['progress']])){
			$article->setVar('progress', $postvars['progress']);

			$tmpvar = -1;
			foreach($jieqiOption['article']['progress']['items'] as $k => $v){
				if($k > $tmpvar) $tmpvar = $k;
			}
			if(!isset($postvars['fullflag'])) $postvars['fullflag'] = $postvars['progress'] == $tmpvar ? 1 : 0;
		}
	}elseif(!empty($postvars['fullflag'])){
		$tmpvar = -1;
		foreach($jieqiOption['article']['progress']['items'] as $k => $v){
			if($k > $tmpvar) $tmpvar = $k;
		}
		$postvars['progress'] = $tmpvar;
		$article->setVar('progress', $postvars['progress']);
	}

	$article->setVar('fullflag', intval($postvars['fullflag']));

	$article->setVar('sortid', $postvars['sortid']);
	$article->setVar('typeid', $postvars['typeid']);
	$article->setVar('intro', $postvars['intro']);
	$article->setVar('notice', $postvars['notice']);

	//书评版主
	if(isset($postvars['reviewer']) && strlen($postvars['reviewer']) > 0){
		$reviewerobj = $users_handler->getByname($postvars['reviewer'], 3);
		if(is_object($reviewerobj)){
			$article->setVar('reviewer', $reviewerobj->getVar('name', 'n'));
			$article->setVar('reviewerid', $reviewerobj->getVar('uid', 'n'));
		}
	}

	//编辑的时候不修改发表者
	if($options['action'] == 'add'){
		if(!empty($_SESSION['jieqiUserId'])){
			$article->setVar('posterid', $_SESSION['jieqiUserId']);
			$article->setVar('poster', $_SESSION['jieqiUserName']);
		}
		else{
			$article->setVar('posterid', 0);
			$article->setVar('poster', '');
		}

		$article->setVar('lastchapterid', 0);
		$article->setVar('lastchapter', '');
		$article->setVar('lastvolumeid', 0);
		$article->setVar('lastvolume', '');
		$article->setVar('chapters', 0);
		$article->setVar('words', 0);
		$article->setVar('setting', '');
		if(jieqi_checkpower($jieqiPower['article']['needcheck'], $jieqiUsersStatus, $jieqiUsersGroup, true)){
			if(empty($postvars['display']) || !is_numeric($postvars['display'])) $article->setVar('display', 0);
			else $article->setVar('display', intval($postvars['display']));
		}
		else{
			$article->setVar('display', 1); // 待审小说
		}
		$imgflag = 0;
	}
	else{
		$old_imgflag = $article->getVar('imgflag');
		$old_simg = $old_imgflag & 1; //是不是有小图
		$imgflag = $old_imgflag;
	}
	//封面图标志
	$imgtary = array(1 => '.gif', 2 => '.jpg', 3 => '.jpeg', 4 => '.png', 5 => '.bmp');
	if(!empty($_FILES['articlelpic']['name'])){
		$limage_postfix = strrchr(trim(strtolower($_FILES['articlelpic']['name'])), ".");
		$imgflag = $imgflag | 2;
		$tmpvar = intval(array_search($limage_postfix, $imgtary));
		if($tmpvar > 0) $imgflag = $imgflag | ($tmpvar * 32);
	}elseif(isset($postvars['urllpic']) && preg_match('/^https?:\/\/[^\s\r\n\t\f<>]+(\.gif|\.jpg|\.jpeg|\.png|\.bmp)/i', $postvars['urllpic'], $matches)){
		$limage_postfix = $matches[1];
		$imgflag = $imgflag | 2;
		$tmpvar = intval(array_search($limage_postfix, $imgtary));
		if($tmpvar > 0) $imgflag = $imgflag | ($tmpvar * 32);
	}

	if(!empty($_FILES['articlespic']['name']) || !empty($_FILES['articlelpic']['name'])){
		if(empty($_FILES['articlespic']['name'])) $simage_postfix = $limage_postfix;
		else $simage_postfix = strrchr(trim(strtolower($_FILES['articlespic']['name'])), ".");
		$imgflag = $imgflag | 1;
		$tmpvar = intval(array_search($simage_postfix, $imgtary));
		if($tmpvar > 0) $imgflag = $imgflag | ($tmpvar * 4);
	}elseif(isset($postvars['urlspic']) && preg_match('/^https?:\/\/[^\s\r\n\t\f<>]+(\.gif|\.jpg|\.jpeg|\.png|\.bmp)/i', $postvars['urlspic'], $matches)){
		$simage_postfix = $matches[1];
		$imgflag = $imgflag | 1;
		$tmpvar = intval(array_search($simage_postfix, $imgtary));
		if($tmpvar > 0) $imgflag = $imgflag | ($tmpvar * 4);
	}
	$article->setVar('imgflag', $imgflag);

	//互换链接
	if($jieqiConfigs['article']['eachlinknum'] > 0 && isset($postvars['eachlinkids'])){
		$postvars['eachlinkids'] = trim($postvars['eachlinkids']);
		$setting = @jieqi_unserialize($article->getVar('setting', 'n'));
		if(!empty($setting['eachlink']['ids'])) $linkvalue = implode(' ', $setting['eachlink']['ids']);
		else $linkvalue = '';
		if($linkvalue != $postvars['eachlinkids']){
			$tmpary = array_unique(explode(' ', $postvars['eachlinkids']));
			foreach($tmpary as $k => $v){
				if(!is_numeric($v)) unset($tmpary[$k]);
				else $tmpary[$k] = intval($tmpary[$k]);
			}
			$linkids = array();
			$linknames = array();
			$linkcodes = array();
			if(count($tmpary > 0)){
				$sql = "SELECT articleid, articlename, articlecode FROM " . jieqi_dbprefix('article_article') . " WHERE articleid IN (" . implode(',', $tmpary) . ")";
				$query->execute($sql);
				$linknum = 0;
				while(($arow = $query->getRow()) && ($linknum < $jieqiConfigs['article']['eachlinknum'])){
					if($options['action'] == 'add' || $arow['articleid'] != $article->getVar('articleid', 'n')){
						$linkids[$linknum] = $arow['articleid'];
						$linknames[$linknum] = $arow['articlename'];
						$linkcodes[$linknum] = $arow['articlecode'];
						$linknum++;
					}
				}
			}
			$setting['eachlink']['ids'] = $linkids;
			$setting['eachlink']['names'] = $linknames;
			$setting['eachlink']['codes'] = $linkcodes;
			$article->setVar('setting', serialize($setting));
		}
	}

	//允许管理员修改的选项
	if($options['ismanager']){
		//责任编辑
		$agentobj = false;
		if(!empty($postvars['agent'])) $agentobj = $users_handler->getByname($postvars['agent'], 3);
		if(is_object($agentobj)){
			$article->setVar('agentid', $agentobj->getVar('uid'));
			$article->setVar('agent', $agentobj->getVar('uname', 'n'));
		}
		else{
			$article->setVar('agentid', 0);
			$article->setVar('agent', '');
		}
		//发表者修改
		if(isset($postvars['poster']) && strlen($postvars['poster']) > 0){
			$posterobj = $users_handler->getByname($postvars['poster'], 3);
			if(is_object($posterobj)){
				$article->setVar('poster', $posterobj->getVar('name', 'n'));
				$article->setVar('posterid', $posterobj->getVar('uid', 'n'));
			}
		}
		//来源网站
		if(isset($postvars['siteid']) && is_numeric($postvars['siteid']) && !empty($jieqiSites[$postvars['siteid']]['custom'])){
			$article->setVar('siteid', intval($postvars['siteid']));
		}
		if(isset($postvars['foreword'])) $article->setVar('foreword', $postvars['foreword']);
		//整本订阅
		if(isset($postvars['saleprice']) && is_numeric($postvars['saleprice']) && intval($postvars['saleprice']) > 0) $article->setVar('saleprice', intval($postvars['saleprice']));
		//包月
		if(isset($postvars['monthly']) && is_numeric($postvars['monthly']) && isset($jieqiOption['article']['monthly']['items'][$postvars['monthly']])){
			$article->setVar('monthly', intval($postvars['monthly']));
		}
		//限时免费
		if(!isset($postvars['freestart']) && isset($postvars['fsyear']) && isset($postvars['fsmonth']) && isset($postvars['fsday'])){
			$postvars['fshour'] = isset($postvars['fshour']) ? intval(trim($postvars['fshour'])) : 0;
			$postvars['fsminute'] = isset($postvars['fsminute']) ? intval(trim($postvars['fsminute'])) : 0;
			$postvars['fssecond'] = isset($postvars['fssecond']) ? intval(trim($postvars['fssecond'])) : 0;
			$postvars['freestart'] = strval(intval(trim($postvars['fsyear']))) . '-' . sprintf('%02d', intval(trim($postvars['fsmonth']))) . '-' . sprintf('%02d', intval(trim($postvars['fsday']))) . ' ' . sprintf('%02d', intval(trim($postvars['fshour']))) . ':' . sprintf('%02d', intval(trim($postvars['fsminute']))) . ':' . sprintf('%02d', intval(trim($postvars['fssecond'])));
		}
		$postvars['freestart'] = empty($postvars['freestart']) ? 0 : strtotime($postvars['freestart']);
		if(!empty($postvars['freestart'])){
			if(!isset($postvars['freeend'])){
				if(isset($postvars['feyear']) && isset($postvars['femonth']) && isset($postvars['feday'])){
					$postvars['fehour'] = isset($postvars['fehour']) ? intval(trim($postvars['fehour'])) : 0;
					$postvars['feminute'] = isset($postvars['feminute']) ? intval(trim($postvars['feminute'])) : 0;
					$postvars['fesecond'] = isset($postvars['fesecond']) ? intval(trim($postvars['fesecond'])) : 0;
					$postvars['freeend'] = strval(intval(trim($postvars['feyear']))) . '-' . sprintf('%02d', intval(trim($postvars['femonth']))) . '-' . sprintf('%02d', intval(trim($postvars['feday']))) . ' ' . sprintf('%02d', intval(trim($postvars['fehour']))) . ':' . sprintf('%02d', intval(trim($postvars['feminute']))) . ':' . sprintf('%02d', intval(trim($postvars['fesecond'])));
				}elseif(!empty($postvars['freedays'])){
					$postvars['freedays'] = intval($postvars['freedays']);
					if($postvars['freedays'] > 0){
						$postvars['freeend'] = date('Y-m-d H:i:s', $postvars['freestart'] + ($postvars['freedays'] * 3600 * 24));
					}
				}
			}
			$postvars['freeend'] = empty($postvars['freeend']) ? 0 : strtotime($postvars['freeend']);
		}else $postvars['freeend'] = 0;

		$article->setVar('freestart', intval($postvars['freestart']));
		$article->setVar('freeend', intval($postvars['freeend']));

		if(isset($postvars['freestart']))
		if(isset($jieqiOption['article']['buyout']['items'][$postvars['buyout']])) $article->setVar('buyout', $postvars['buyout']); //买断
		if(isset($jieqiOption['article']['discount']['items'][$postvars['discount']])) $article->setVar('discount', $postvars['discount']); //打折
		if(isset($jieqiOption['article']['quality']['items'][$postvars['quality']])) $article->setVar('quality', $postvars['quality']); //精品
		if(isset($jieqiOption['article']['isshare']['items'][$postvars['isshare']])) $article->setVar('isshare', $postvars['isshare']); //共享
		if(empty($rgroup) && isset($jieqiOption['article']['rgroup']['items'][$postvars['rgroup']])) $article->setVar('rgroup', $postvars['rgroup']); //频道（男生/女生）

		if(isset($postvars['issign'])){
			if($options['action'] == 'add'){
				$article->setVar('signtime', 0);
				$article->setVar('isvip', 0);
				$article->setVar('vipid', 0);
			}
			$postvars['issign'] = intval($postvars['issign']);
			$article->setVar('issign', $postvars['issign']);
			if($postvars['issign'] >= 10){
				if(intval($article->getVar('signtime', 'n')) == 0) $article->setVar('signtime', JIEQI_NOW_TIME);
				if(intval($article->getVar('isvip', 'n')) == 0) $article->setVar('isvip', 1);

				if(intval($article->getVar('vipid', 'n')) == 0){
					//如果vip状态已经存在则自动关联
					$sql = "SELECT * FROM ".jieqi_dbprefix('obook_obook')." WHERE obookname = '".jieqi_dbslashes($article->getVar('articlename', 'n'))."' LIMIT 0,1";
					$query->execute($sql);
					$obookrow = $query->getRow();
					if(is_array($obookrow) && !empty($obookrow['articleid'])){
						$sql = "SELECT * FROM ".jieqi_dbprefix('article_article')." WHERE articleid = ".intval($obookrow['articleid'])." LIMIT 0,1";
						$query->execute($sql);
						$articlerow = $query->getRow();
						if(!$articlerow || $articlerow['articleid'] == $article->getVar('articleid', 'n')){
							$article->setVar('viptime', $obookrow['lastupdate']);
							$article->setVar('vipid', $obookrow['obookid']);
							$article->setVar('vipchapters', $obookrow['chapters']);
							$article->setVar('vipwords', $obookrow['words']);
							$article->setVar('vipvolumeid', $obookrow['lastvolumeid']);
							$article->setVar('vipvolume', $obookrow['lastvolume']);
							$article->setVar('vipchapterid', $obookrow['lastchapterid']);
							$article->setVar('vipchapter', $obookrow['lastchapter']);
							$article->setVar('vipsummary', $obookrow['lastsummary']);
							if(!$articlerow){
								//改变obook里面的articleid
								$sql = "UPDATE ".jieqi_dbprefix('obook_obook')." SET articleid = ".intval($article->getVar('articleid', 'n'))." WHERE obookid = ".intval($obookrow['obookid']);
								$query->execute($sql);
								$sql = "UPDATE ".jieqi_dbprefix('obook_ochapter')." SET articleid = ".intval($article->getVar('articleid', 'n'))." WHERE obookid = ".intval($obookrow['obookid']);
								$query->execute($sql);
								$sql = "UPDATE ".jieqi_dbprefix('obook_paidlog')." SET articleid = ".intval($article->getVar('articleid', 'n'))." WHERE obookid = ".intval($obookrow['obookid']);
								$query->execute($sql);
							}
						}
					}
				}
			}elseif($postvars['issign'] > 0){
				if(intval($article->getVar('signtime', 'n')) == 0) $article->setVar('signtime', JIEQI_NOW_TIME);
				if(intval($article->getVar('isvip', 'n')) > 0 && intval($article->getVar('vipchapters', 'n')) == 0) $article->setVar('isvip', 0);
			}elseif($postvars['issign'] == 0){
				if(intval($article->getVar('signtime', 'n')) > 0) $article->setVar('signtime', 0);
				if(intval($article->getVar('isvip', 'n')) > 0 && intval($article->getVar('vipchapters', 'n')) == 0) $article->setVar('isvip', 0);
			}
		}elseif($postvars['isvip'] > 0){
			$article->setVar('issign', 0);
			$article->setVar('isvip', 1);
		}
		//出版及购买信息
		if(isset($postvars['ispub']) && isset($jieqiOption['article']['ispub']['items'][$postvars['ispub']])) $article->setVar('ispub', $postvars['ispub']); //是否已出版
		if(isset($postvars['pubtime'])) $article->setVar('pubtime', strtotime($postvars['pubtime'])); //出版时间
		if(isset($postvars['pubid'])) $article->setVar('pubid', intval($postvars['pubid'])); //出版社id
		if(isset($postvars['pubhouse'])) $article->setVar('pubhouse', $postvars['pubhouse']); //出版社名字
		if(isset($postvars['pubprice'])) $article->setVar('pubprice', round(floatval($postvars['pubprice']) * 100)); //出版价格
		if(isset($postvars['pubpages'])) $article->setVar('pubpages', intval($postvars['pubpages'])); //书籍页数
		if(isset($postvars['pubisbn'])) $article->setVar('pubisbn', $postvars['pubisbn']); //ISBN代码
		if(isset($postvars['pubinfo'])) $article->setVar('pubinfo', $postvars['pubinfo']); //出版信息

		if(isset($postvars['buysid'])) $article->setVar('buysid', intval($postvars['buysid'])); //购买网站id
		if(isset($postvars['buysite'])) $article->setVar('buysite', $postvars['buysite']); //购买网站名
		if(isset($postvars['buyurl'])) $article->setVar('buyurl', $postvars['buyurl']); //购买网址
		if(isset($postvars['buyprice'])) $article->setVar('buyprice', round(floatval($postvars['buyprice']) * 100)); //出版价格
		if(isset($postvars['buyinfo'])) $article->setVar('buyinfo', $postvars['buyinfo']); //购买信息

	}

	//修改统计值权限
	if(!empty($options['allowmodify'])){
		if(floatval(JIEQI_VERSION) >= 2){
			$statary = array('dayvisit', 'weekvisit', 'monthvisit', 'allvisit', 'dayvote', 'weekvote', 'monthvote', 'allvote', 'dayflower', 'weekflower', 'monthflower', 'allflower', 'dayegg', 'weekegg', 'monthegg', 'allegg', 'dayvipvote', 'weekvipvote', 'monthvipvote', 'allvipvote');
		}else{
			$statary = array('dayvisit', 'weekvisit', 'monthvisit', 'allvisit', 'dayvote', 'weekvote', 'monthvote', 'allvote');
		}

		foreach($statary as $v){
			if(isset($postvars[$v]) && is_numeric($postvars[$v]) && $postvars[$v] != $article->getVar($v, 'n')){
				$article->setVar($v, intval($postvars[$v]));
				$tmpv = str_replace(array('day', 'week', 'month', 'all'), 'last', $v);
				$article->setVar($tmpv, JIEQI_NOW_TIME);
			}
		}
	}

	if(!$article_handler->insert($article)){
		if($options['action'] == 'add') return $jieqiLang['article']['article_add_failure'];
		else return $jieqiLang['article']['article_edit_failure'];
	}
	else{
		$id = intval($article->getVar('articleid', 'n'));
		if($options['action'] == 'add'){
			//更新拼音代码
			if($postvars['updateacode']){
				$postvars['articlecode'] .= '_' . $id;
				$article_handler->updatefields(array('articlecode' => $postvars['articlecode']), new Criteria('articleid', $id, '='));
			}
			// 保存tag
			if(floatval(JIEQI_VERSION) >= 2){
				jieqi_tag_save($tagary, $id, array('tag' => jieqi_dbprefix('article_tag'), 'taglink' => jieqi_dbprefix('article_taglink')));
			}

			include_once($jieqiModules['article']['path'] . '/class/package.php');
			$package = new JieqiPackage($id);

			$package->initPackage($article->getVars('n'), true);

			include_once($jieqiModules['article']['path'] . '/include/funarticle.php');
			// 保存封面图片，有大图默认有小图，有小图未必有大图
			if(!empty($_FILES['articlelpic']['name']) && !empty($_FILES['articlespic']['name'])){
				$imagefile = $package->getDir('imagedir') . '/' . $id . 'l' . $limage_postfix;
				jieqi_copyfile($_FILES['articlelpic']['tmp_name'], $imagefile, 0777, true);
				jieqi_article_coverdo($imagefile, 'l');

				$imagefile = $package->getDir('imagedir') . '/' . $id . 's' . $simage_postfix;
				jieqi_copyfile($_FILES['articlespic']['tmp_name'], $imagefile, 0777, true);
				jieqi_article_coverdo($imagefile, 's');
			}
			elseif(!empty($_FILES['articlelpic']['name'])){
				$imagefile = $package->getDir('imagedir') . '/' . $id . 'l' . $limage_postfix;
				jieqi_copyfile($_FILES['articlelpic']['tmp_name'], $imagefile, 0777, false);
				jieqi_article_coverdo($imagefile, 'l');

				$imagefile = $package->getDir('imagedir') . '/' . $id . 's' . $limage_postfix;
				jieqi_copyfile($_FILES['articlelpic']['tmp_name'], $imagefile, 0777, true);
				jieqi_article_coverdo($imagefile, 's');
			}
			elseif(!empty($_FILES['articlespic']['name'])){
				$imagefile = $package->getDir('imagedir') . '/' . $id . 's' . $simage_postfix;
				jieqi_copyfile($_FILES['articlespic']['tmp_name'], $imagefile, 0777, true);
				jieqi_article_coverdo($imagefile, 's');
			}else{
				//封面图是url链接的情况
				if(!empty($postvars['urllpic']) && empty($postvars['urlspic'])) $postvars['urlspic'] = $postvars['urllpic'];
				if(!empty($postvars['urllpic']) && preg_match('/^https?:\/\/[^\s\r\n\t\f<>]+(\.gif|\.jpg|\.jpeg|\.png|\.bmp)/i', $postvars['urllpic'], $matches)){
					$imgfile = $package->getDir('imagedir') . '/' . $id . 'l' . $matches[1];
					jieqi_checkdir(dirname($imgfile), true);
					jieqi_writefile($imgfile, jieqi_urlcontents($postvars['urllpic']));
					jieqi_article_coverdo($imgfile, 'l');
				}
				if(!empty($postvars['urlspic']) && preg_match('/^https?:\/\/[^\s\r\n\t\f<>]+(\.gif|\.jpg|\.jpeg|\.png|\.bmp)/i', $postvars['urlspic'], $matches)){
					$imgfile = $package->getDir('imagedir') . '/' . $id . 's' . $matches[1];
					jieqi_checkdir(dirname($imgfile), true);
					jieqi_writefile($imgfile, jieqi_urlcontents($postvars['urlspic']));
					jieqi_article_coverdo($imgfile, 's');
				}
			}
		}else{
			//更新vip信息
			$vipid = intval($article->getVar('vipid', 'n'));
			if($vipid > 0){
				$fieldsary = array('siteid', 'backupname', 'keywords', 'roles', 'initial', 'sortid', 'typeid', 'libid', 'intro', 'notice', 'foreword', 'authorid', 'author', 'agentid', 'agent', 'reviewerid', 'reviewer', 'posterid', 'poster', 'unionid', 'permission', 'firstflag', 'saleprice', 'imgflag', 'monthly', 'rgroup', 'freestart', 'freeend', 'display');
				$fieldrows = array();
				foreach($fieldsary as $v){
					$fieldrows[$v] = $article->getVar($v, 'n');
				}
				$fieldrows['obookname'] = $article->getVar('articlename', 'n'); //书名字段不同，特别处理
				$sql = $query->makeupsql(jieqi_dbprefix('obook_obook'), $fieldrows, 'UPDATE', array('obookid'=>$vipid));
				$query->execute($sql);
				$sql = "UPDATE ".jieqi_dbprefix('obook_ochapter')." SET obookname = '".jieqi_dbslashes($article->getVar('articlename', 'n'))."' WHERE obookid = ".$vipid;
				$query->execute($sql);
			}

			//更新tag
			if(floatval(JIEQI_VERSION) >= 2){
				jieqi_tag_update($oldtags, $tagary, $id, array('tag' => jieqi_dbprefix('article_tag'), 'taglink' => jieqi_dbprefix('article_taglink')));
			}

			include_once($jieqiModules['article']['path'].'/class/package.php');
			$package = new JieqiPackage($id);
			$package->editPackage($article->getVars('n'), true);

			include_once($jieqiModules['article']['path'].'/include/funarticle.php');
			//删除老封面
			if($old_imgflag != $imgflag){
				$tmpvar = ($old_imgflag >> 2) & 7;
				if(isset($imgtary[$tmpvar])){
					if(is_file($package->getDir('imagedir').'/'.$id.'s'.$imgtary[$tmpvar])) jieqi_delfile($package->getDir('imagedir').'/'.$id.'s'.$imgtary[$tmpvar]);
				}
				$tmpvar = $old_imgflag >> 5;
				if(isset($imgtary[$tmpvar])){
					if(is_file($package->getDir('imagedir').'/'.$id.'l'.$imgtary[$tmpvar])) jieqi_delfile($package->getDir('imagedir').'/'.$id.'l'.$imgtary[$tmpvar]);
				}
			}

			// 保存封面图片，有大图默认有小图，有小图未必有大图
			if(!empty($_FILES['articlelpic']['name']) && !empty($_FILES['articlespic']['name'])){
				$imagefile = $package->getDir('imagedir').'/'.$id.'l'.$limage_postfix;
				jieqi_copyfile($_FILES['articlelpic']['tmp_name'], $imagefile, 0777, true);
				jieqi_article_coverdo($imagefile, 'l');

				$imagefile = $package->getDir('imagedir').'/'.$id.'s'.$simage_postfix;
				jieqi_copyfile($_FILES['articlespic']['tmp_name'], $imagefile, 0777, true);
				jieqi_article_coverdo($imagefile, 's');
			}elseif(!empty($_FILES['articlelpic']['name'])){

				if(!empty($postvars['sameslpic']) || empty($old_simg)){
					$imagefile = $package->getDir('imagedir').'/'.$id.'l'.$limage_postfix;
					jieqi_copyfile($_FILES['articlelpic']['tmp_name'], $imagefile, 0777, false);
					jieqi_article_coverdo($imagefile, 'l');

					$imagefile = $package->getDir('imagedir').'/'.$id.'s'.$limage_postfix;
					jieqi_copyfile($_FILES['articlelpic']['tmp_name'], $imagefile, 0777, true);
					jieqi_article_coverdo($imagefile, 's');
				}else{
					$imagefile = $package->getDir('imagedir').'/'.$id.'l'.$limage_postfix;
					jieqi_copyfile($_FILES['articlelpic']['tmp_name'], $imagefile, 0777, true);
					jieqi_article_coverdo($imagefile, 'l');
				}
			}elseif(!empty($_FILES['articlespic']['name'])){
				$imagefile = $package->getDir('imagedir').'/'.$id.'s'.$simage_postfix;
				jieqi_copyfile($_FILES['articlespic']['tmp_name'], $imagefile, 0777, true);
				jieqi_article_coverdo($imagefile, 's');
			}else{
				//封面图是url链接的情况
				if(!empty($postvars['urllpic']) && empty($postvars['urlspic'])) $postvars['urlspic'] = $postvars['urllpic'];
				if(!empty($postvars['urllpic']) && preg_match('/^https?:\/\/[^\s\r\n\t\f<>]+(\.gif|\.jpg|\.jpeg|\.png|\.bmp)/i', $postvars['urllpic'], $matches)){
					$imgfile = $package->getDir('imagedir') . '/' . $id . 'l' . $matches[1];
					jieqi_checkdir(dirname($imgfile), true);
					jieqi_writefile($imgfile, jieqi_urlcontents($postvars['urllpic']));
					jieqi_article_coverdo($imgfile, 'l');
				}
				if(!empty($postvars['urlspic']) && preg_match('/^https?:\/\/[^\s\r\n\t\f<>]+(\.gif|\.jpg|\.jpeg|\.png|\.bmp)/i', $postvars['urlspic'], $matches)){
					$imgfile = $package->getDir('imagedir') . '/' . $id . 's' . $matches[1];
					jieqi_checkdir(dirname($imgfile), true);
					jieqi_writefile($imgfile, jieqi_urlcontents($postvars['urlspic']));
					jieqi_article_coverdo($imgfile, 's');
				}
			}
		}

		return $article;
	}
}

// 删除一本小说 $batch 是不是批量删除时候调用
function jieqi_article_delete($aid, $batch = false){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiAction;
	global $jieqiPower;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	global $article_handler;
	global $chapter_handler;
	global $query;
	if(!isset($jieqiAction['article'])) jieqi_getconfigs('article', 'action', 'jieqiAction');
	if(!isset($jieqiPower['article'])) jieqi_getconfigs('article', 'power', 'jieqiPower');
	$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);

	$article = $article_handler->get($aid);
	if(!is_object($article)) return false;

	// 删除小说
	$article_handler->delete($aid);

	// 删除文本、html及zip
	include_once($jieqiModules['article']['path'] . '/class/package.php');
	$package = new JieqiPackage($aid);
	$package->delete();
	// 删除章节
	// 检查这篇小说章节发表人，扣积分用
	if(!$batch && !$ismanager){
		$posterary = array();
		if(!empty($jieqiAction['article']['chapteradd']['earnscore'])){
			$criteria0 = new CriteriaCompo(new Criteria('articleid', $aid, '='));
			$chapter_handler->queryObjects($criteria0);
			while($chapterobj = $chapter_handler->getObject()){
				$posterid = intval($chapterobj->getVar('posterid'));
				if(isset($posterary[$posterid])) $posterary[$posterid] += $jieqiAction['article']['chapteradd']['earnscore'];
				else $posterary[$posterid] = $jieqiAction['article']['chapteradd']['earnscore'];
			}
			unset($criteria0);
		}
	}

	// 真正删除章节
	$criteria = new CriteriaCompo(new Criteria('articleid', $aid, '='));
	$chapter_handler->delete($criteria);
	// 删除附件
	include_once($jieqiModules['article']['path'] . '/class/articleattachs.php');
	$attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
	$attachs_handler->delete($criteria);
	// 删除书评
	$criteria1 = new CriteriaCompo(new Criteria('ownerid', $aid, '='));
	include_once($jieqiModules['article']['path'] . '/class/reviews.php');
	$reviews_handler = JieqiReviewsHandler::getInstance('JieqiReviewsHandler');
	$reviews_handler->delete($criteria1);
	include_once($jieqiModules['article']['path'] . '/class/replies.php');
	$replies_handler = JieqiRepliesHandler::getInstance('JieqiRepliesHandler');
	$replies_handler->delete($criteria1);
	/*
	 * include_once($jieqiModules['article']['path'].'/class/review.php');
	 * $review_handler = JieqiReviewHandler::getInstance('JieqiReviewHandler');
	 * $review_handler->delete($criteria);
	 */
	//删除书架收藏
	include_once($jieqiModules['article']['path'] . '/class/bookcase.php');
	$bookcase_handler = JieqiBookcaseHandler::getInstance('JieqiBookcaseHandler');
	$bookcase_handler->delete($criteria);
	// 删除封面
	$imagedir = jieqi_uploadpath($jieqiConfigs['article']['imagedir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid;
	if(is_dir($imagedir)) jieqi_delfolder($imagedir, true);
	//vip小说及章节未销售则删除，销售则下架
	if($article->getVar('isvip', 'n') > 0 && $article->getVar('vipid', 'n') > 0){
		$obookid = intval($article->getVar('vipid', 'n'));

		global $obook_handler;
		if(!isset($obook_handler) || !is_a($obook_handler, 'JieqiObookHandler')){
			include_once($jieqiModules['obook']['path'] . '/class/obook.php');
			$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
		}
		$obook = $obook_handler->get($obookid);
		if(is_object($obook)){
			if(!isset($query) || !is_a($query, 'JieqiQueryHandler')){
				jieqi_includedb();
				$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
			}
			if($obook->getVar('sumemoney', 'n') == 0){
				$obook_handler->delete($obookid);
			}
			if($obook->getVar('sumegold', 'n') == 0){
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ocontent') . " WHERE ochapterid IN (SELECT ochapterid FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid})";
				$query->execute($sql);
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid}";
				$query->execute($sql);
			}else{
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ocontent') . " WHERE ochapterid IN (SELECT ochapterid FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid} AND sumegold = 0)";
				$query->execute($sql);
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid} AND sumegold = 0";
				$query->execute($sql);
				$sql = "UPDATE " . jieqi_dbprefix('obook_ochapter') . " SET display = 2 WHERE obookid = {$obookid}";
				$query->execute($sql);
			}
		}
	}

	// 记录删除日志
	if(!$batch){
		include_once($jieqiModules['article']['path'] . '/class/articlelog.php');
		$articlelog_handler = JieqiArticlelogHandler::getInstance('JieqiArticlelogHandler');
		$newlog = $articlelog_handler->create();
		$newlog->setVar('siteid', $article->getVar('siteid', 'n'));
		$newlog->setVar('logtime', JIEQI_NOW_TIME);
		$newlog->setVar('userid', $_SESSION['jieqiUserId']);
		$newlog->setVar('username', $_SESSION['jieqiUserName']);
		$newlog->setVar('articleid', $article->getVar('articleid', 'n'));
		$newlog->setVar('articlename', $article->getVar('articlename', 'n'));
		$newlog->setVar('chapterid', 0);
		$newlog->setVar('chaptername', '');
		$newlog->setVar('reason', '');
		$newlog->setVar('chginfo', $jieqiLang['article']['delete_article']);
		$newlog->setVar('chglog', '');
		$newlog->setVar('ischapter', '0');
		$newlog->setVar('isdel', '1');
		$newlog->setVar('databak', serialize($article->getVars()));
		$articlelog_handler->insert($newlog);
	}

	// 减少小说和章节积分
	if(!$batch && !$ismanager){
		include_once(JIEQI_ROOT_PATH . '/class/users.php');
		$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
		if(!empty($jieqiAction['article']['articleadd']['earnscore'])){
			$posterid = intval($article->getVar('posterid'));
			if(isset($posterary[$posterid])) $posterary[$posterid] += $jieqiAction['article']['articleadd']['earnscore'];
			else $posterary[$posterid] = $jieqiAction['article']['articleadd']['earnscore'];
		}
		foreach($posterary as $pid => $pscore){
			$users_handler->changeScore($pid, $pscore, false);
		}
	}

	// 删除tag
	if(floatval(JIEQI_VERSION) >= 2){
		include_once(JIEQI_ROOT_PATH . '/include/funtag.php');
		$tags = jieqi_tag_clean($article->getVar('keywords', 'n'));
		jieqi_tag_delete($tags, $article->getVar('articleid', 'n'), array('tag' => jieqi_dbprefix('article_tag'), 'taglink' => jieqi_dbprefix('article_taglink')));
	}

	// 更新最新小说
	if(!$batch) jieqi_article_updateinfo($article, 'articledel');

	return $article;
}

// 清理所有章节
function jieqi_article_clean($aid, $batch = false){
	global $jieqiModules;
	global $article_handler;
	global $chapter_handler;
	global $jieqiConfigs;
	global $jieqiAction;
	global $jieqiPower;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	global $query;
	if(!isset($jieqiAction['article'])) jieqi_getconfigs('article', 'action', 'jieqiAction');
	if(!isset($jieqiPower['article'])) jieqi_getconfigs('article', 'power', 'jieqiPower');
	$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);


	$article = $article_handler->get($aid);
	if(!is_object($article)) return false;

	// 清除小说统计
	$criteria = new CriteriaCompo(new Criteria('articleid', $aid));
	$fields = array('lastchapter' => '', 'lastchapterid' => 0, 'lastvolume' => '', 'lastvolumeid' => 0, 'chapters' => 0, 'words' => 0, 'freewords' => 0);
	$article_handler->updatefields($fields, $criteria);

	// 删除文本、html及zip
	include_once($jieqiModules['article']['path'] . '/class/package.php');
	$package = new JieqiPackage($aid);
	$package->delete();
	$package->initPackage($article->getVars('n'), true);

	// 删除章节


	// 检查这篇小说章节发表人，扣积分用
	if(!$batch && !$ismanager){
		$posterary = array();
		if(!empty($jieqiAction['article']['chapteradd']['earnscore'])){
			$criteria0 = new CriteriaCompo(new Criteria('articleid', $aid, '='));
			$chapter_handler->queryObjects($criteria0);
			while($chapterobj = $chapter_handler->getObject()){
				$posterid = intval($chapterobj->getVar('posterid'));
				if(isset($posterary[$posterid])) $posterary[$posterid] += $jieqiAction['article']['chapteradd']['earnscore'];
				else $posterary[$posterid] = $jieqiAction['article']['chapteradd']['earnscore'];
			}
			unset($criteria0);
		}
	}

	// 真正删除章节
	$criteria = new CriteriaCompo(new Criteria('articleid', $aid, '='));
	$chapter_handler->delete($criteria);
	// 删除附件
	include_once($jieqiModules['article']['path'] . '/class/articleattachs.php');
	$attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
	$attachs_handler->delete($criteria);

	//vip小说及章节未销售则删除，销售则下架
	if($article->getVar('isvip', 'n') > 0 && $article->getVar('vipid', 'n') > 0){
		$obookid = intval($article->getVar('vipid', 'n'));

		global $obook_handler;
		if(!isset($obook_handler) || !is_a($obook_handler, 'JieqiObookHandler')){
			include_once($jieqiModules['obook']['path'] . '/class/obook.php');
			$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
		}
		$obook = $obook_handler->get($obookid);
		if(is_object($obook)){
			if(!isset($query) || !is_a($query, 'JieqiQueryHandler')){
				jieqi_includedb();
				$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
			}
			if($obook->getVar('sumemoney', 'n') == 0){
				$obook_handler->delete($obookid);
			}
			if($obook->getVar('sumegold', 'n') == 0){
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ocontent') . " WHERE ochapterid IN (SELECT ochapterid FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid})";
				$query->execute($sql);
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid}";
				$query->execute($sql);
			}else{
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ocontent') . " WHERE ochapterid IN (SELECT ochapterid FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid} AND sumegold = 0)";
				$query->execute($sql);
				$sql = "DELETE FROM " . jieqi_dbprefix('obook_ochapter') . " WHERE obookid = {$obookid} AND sumegold = 0";
				$query->execute($sql);
				$sql = "UPDATE " . jieqi_dbprefix('obook_ochapter') . " SET display = 2 WHERE obookid = {$obookid}";
				$query->execute($sql);
			}
		}
	}

	// 减少小说和章节积分
	if(!$batch && !$ismanager){
		include_once(JIEQI_ROOT_PATH . '/class/users.php');
		$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
		if(!empty($jieqiAction['article']['articleadd']['earnscore'])){
			$posterid = intval($article->getVar('posterid'));
			if(isset($posterary[$posterid])) $posterary[$posterid] += $jieqiAction['article']['articleadd']['earnscore'];
			else $posterary[$posterid] = $jieqiAction['article']['articleadd']['earnscore'];
		}
		foreach($posterary as $pid => $pscore){
			$users_handler->changeScore($pid, $pscore, false);
		}
	}

	// 更新最新小说
	if(!$batch) jieqi_article_updateinfo(0);

	return $article;
}

// 清理一本书符合条件章节
function jieqi_article_delchapter($aid, $criteria, $batch = false){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiAction;
	global $jieqiPower;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	global $article_handler;
	global $chapter_handler;

	global $jieqi_file_postfix;

	if(!isset($jieqiAction['article'])) jieqi_getconfigs('article', 'action', 'jieqiAction');
	if(!isset($jieqiPower['article'])) jieqi_getconfigs('article', 'power', 'jieqiPower');
	$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);


	if(!is_object($criteria)) return false;
	$criteria->add(new Criteria('articleid', intval($aid)));
	$article = $article_handler->get($aid);
	if(!is_object($article)) return false;

	// 查询符合条件章节
	$posterary = array();
	$chapter_handler->queryObjects($criteria);
	$chapterary = array();
	$k = 0;
	$cids = '';
	$lastchapterid = intval($article->getVar('lastchapterid'));
	$lastvolumeid = intval($article->getVar('lastvolumeid'));
	$vipchapterid = intval($article->getVar('vipchapterid'));
	$vipvolumeid = intval($article->getVar('vipvolumeid'));
	$uplastchapter = false; // 是否更新最新章节
	$uplastvolume = false; // 是否更新最新章节
	$upvipchapter = false;
	$upvipvolume = false;

	$subdaywords = 0;
	$subweekwords = 0;
	$submonthwords = 0;
	$subwords = 0;
	$subfreewords = 0;
	$subvipwords = 0;

	// 日周月总时间判断
	$tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
	$daystart = mktime(0, 0, 0, (int)$tmpvar[1], (int)$tmpvar[2], (int)$tmpvar[0]);
	$monthstart = mktime(0, 0, 0, (int)$tmpvar[1], 1, (int)$tmpvar[0]);
	$tmpvar = date('w', JIEQI_NOW_TIME);
	if($tmpvar == 0) $tmpvar = 7; // 星期天是0，国人习惯作为作为一星期的最后一天
	$weekstart = $daystart;
	if($tmpvar > 1) $weekstart -= ($tmpvar - 1) * 86400;

	while($chapterobj = $chapter_handler->getObject()){
		$chapterary[$k]['id'] = intval($chapterobj->getVar('chapterid'));
		if($chapterary[$k]['id'] == $lastchapterid) $uplastchapter = true;
		if($chapterary[$k]['id'] == $lastvolumeid) $uplastvolume = true;
		if($chapterary[$k]['id'] == $vipchapterid) $upvipchapter = true;
		if($chapterary[$k]['id'] == $vipvolumeid) $upvipvolume = true;

		if($cids != '') $cids .= ',';
		$cids .= $chapterary[$k]['id'];
		$chapterary[$k]['words'] = intval($chapterobj->getVar('words', 'n'));

		$clastupdate = intval($chapterobj->getVar('lastupdate', 'n'));
		if($clastupdate >= $daystart) $subdaywords += $chapterary[$k]['words'];
		if($clastupdate >= $weekstart) $subweekwords += $chapterary[$k]['words'];
		if($clastupdate >= $monthstart) $submonthwords += $chapterary[$k]['words'];
		$subwords += $chapterary[$k]['words'];
		if(intval($chapterobj->getVar('isvip', 'n')) > 0) $subvipwords += $chapterary[$k]['words'];
		else $subfreewords += $chapterary[$k]['words'];

		$chapterary[$k]['attach'] = $chapterobj->getVar('attachment', 'n') == '' ? 0 : 1;
		$chapterary[$k]['chapterorder'] = intval($chapterobj->getVar('chapterorder'));
		$chapterary[$k]['saleprice'] = intval($chapterobj->getVar('saleprice'));
		$chapterary[$k]['isimage'] = intval($chapterobj->getVar('isimage'));
		$chapterary[$k]['isvip'] = intval($chapterobj->getVar('isvip'));
		$chapterary[$k]['chaptertype'] = intval($chapterobj->getVar('chaptertype'));
		$chapterary[$k]['power'] = intval($chapterobj->getVar('power'));

		$k++;
		if(!empty($jieqiAction['article']['chapteradd']['earnscore'])){
			$posterid = intval($chapterobj->getVar('posterid'));
			if(isset($posterary[$posterid])) $posterary[$posterid] += $jieqiAction['article']['chapteradd']['earnscore'];
			else $posterary[$posterid] = $jieqiAction['article']['chapteradd']['earnscore'];
		}
	}
	// 删除章节
	$chapter_handler->delete($criteria);

	// 删除附件数据库
	if($cids != ''){
		$criteria1 = new CriteriaCompo();
		$criteria1->add(new Criteria('chapterid', '(' . $cids . ')', 'IN'));
		include_once($jieqiModules['article']['path'] . '/class/articleattachs.php');
		$attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
		$attachs_handler->delete($criteria1);
	}
	// 删除文本文件、附件文件、html
	include_once($jieqiModules['article']['path'] . '/class/package.php');
	// $txtdir = jieqi_uploadpath($jieqiConfigs['article']['txtdir'],
	// 'article').jieqi_getsubdir($aid).'/'.$aid;
	$htmldir = jieqi_uploadpath($jieqiConfigs['article']['htmldir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid;
	$txtjsdir = jieqi_uploadpath($jieqiConfigs['article']['txtjsdir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid;
	$attachdir = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid;
	foreach($chapterary as $c){
		jieqi_delete_achapterc($aid, $c['id'], intval($c['isvip']), intval($c['chaptertype']));
		// if(is_file($txtdir.'/'.$c['id'].$jieqi_file_postfix['txt']))
		// jieqi_delfile($txtdir.'/'.$c['id'].$jieqi_file_postfix['txt']);
		if(is_file($htmldir . '/' . $c['id'] . $jieqiConfigs['article']['htmlfile'])) jieqi_delfile($htmldir . '/' . $c['id'] . $jieqiConfigs['article']['htmlfile']);
		if(is_file($txtjsdir . '/' . $c['id'] . $jieqi_file_postfix['js'])) jieqi_delfile($txtjsdir . '/' . $c['id'] . $jieqi_file_postfix['js']);
		if(is_dir($attachdir . '/' . $c['id'])) jieqi_delfolder($attachdir . '/' . $c['id']);
	}
	// 重新生成网页和打包
	include_once($jieqiModules['article']['path'] . '/include/repack.php');
	$ptypes = array('makeopf' => 1, 'makehtml' => $jieqiConfigs['article']['makehtml'], 'maketxtjs' => $jieqiConfigs['article']['maketxtjs'], 'makezip' => $jieqiConfigs['article']['makezip'], 'makefull' => $jieqiConfigs['article']['makefull'], 'maketxtfull' => $jieqiConfigs['article']['maketxtfull'], 'makeumd' => $jieqiConfigs['article']['makeumd'], 'makejar' => $jieqiConfigs['article']['makejar']);
	article_repack($aid, $ptypes, 0);
	// 减少小说和章节积分
	if(!$batch && !$ismanager){
		include_once(JIEQI_ROOT_PATH . '/class/users.php');
		$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
		if(!empty($jieqiAction['article']['articleadd']['earnscore'])){
			$posterid = intval($article->getVar('posterid'));
			if(isset($posterary[$posterid])) $posterary[$posterid] += $jieqiAction['article']['articleadd']['earnscore'];
			else $posterary[$posterid] = $jieqiAction['article']['articleadd']['earnscore'];
		}
		foreach($posterary as $pid => $pscore){
			$users_handler->changeScore($pid, $pscore, false);
		}
	}

	// 更新小说信息
	$newdaywords = intval($article->getVar('daywords', 'n')) > $subdaywords ? intval($article->getVar('daywords', 'n')) - $subdaywords : 0;
	$newweekwords = intval($article->getVar('weekwords', 'n')) > $subweekwords ? intval($article->getVar('weekwords', 'n')) - $subweekwords : 0;
	$newmonthwords = intval($article->getVar('monthwords', 'n')) > $submonthwords ? intval($article->getVar('monthwords', 'n')) - $submonthwords : 0;
	$newwords = intval($article->getVar('words', 'n')) > $subwords ? intval($article->getVar('words', 'n')) - $subwords : 0;
	$freewords = intval($article->getVar('freewords', 'n')) > $subfreewords ? intval($article->getVar('freewords', 'n')) - $subfreewords : 0;
	$vipwords = intval($article->getVar('vipwords', 'n')) > $subvipwords ? intval($article->getVar('vipwords', 'n')) - $subvipwords : 0;

	$article->setVar('daywords', $newdaywords);
	$article->setVar('weekwords', $newweekwords);
	$article->setVar('monthwords', $newmonthwords);
	$article->setVar('words', $newwords);
	$article->setVar('freewords', $freewords);
	$article->setVar('vipwords', $vipwords);
	//更新最新章节
	if($uplastchapter || $uplastvolume){
		if($uplastchapter){
			$lastinfo = jieqi_article_searchlast($article, 'lastchapter');
			$article->setVar('lastchapter', $lastinfo['name']);
			$article->setVar('lastchapterid', $lastinfo['id']);
			$article->setVar('lastsummary', $lastinfo['summary']);
		}
		$lastinfo = jieqi_article_searchlast($article, 'lastvolume');
		$article->setVar('lastvolume', $lastinfo['name']);
		$article->setVar('lastvolumeid', $lastinfo['id']);
	}

	if($upvipchapter || $upvipvolume){
		if($upvipchapter){
			$lastinfo = jieqi_article_searchlast($article, 'vipchapter');
			$article->setVar('vipchapter', $lastinfo['name']);
			$article->setVar('vipchapterid', $lastinfo['id']);
			$article->setVar('vipsummary', $lastinfo['summary']);
		}
		$lastinfo = jieqi_article_searchlast($article, 'vipvolume');
		$article->setVar('vipvolume', $lastinfo['name']);
		$article->setVar('vipvolumeid', $lastinfo['id']);
	}

	//更新小说表
	$article_handler->insert($article);

	// 更新最新小说
	if(!$batch) jieqi_article_updateinfo(0);

	return $article;
}

// 删除一个章节
function jieqi_article_delonechapter($chapter, $article = NULL, $batch = false){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiAction;
	global $jieqiPower;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	global $article_handler;
	global $chapter_handler;
	global $jieqi_file_postfix;

	if(!isset($jieqiAction['article'])) jieqi_getconfigs('article', 'action', 'jieqiAction');
	if(!isset($jieqiPower['article'])) jieqi_getconfigs('article', 'power', 'jieqiPower');
	$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);


	if(!is_object($chapter)){
		$chapter = $chapter_handler->get(intval($chapter));
		if(!$chapter) return false;
		$article = $article_handler->get($chapter->getVar('articleid'));
		if(!$article) return false;
	}
	$chapterid = intval($chapter->getVar('chapterid', 'n'));
	$isvip = intval($chapter->getVar('isvip', 'n'));
	$chapter_handler->delete($chapterid);
	// 后面章节的序号前移一位
	if($chapter->getVar('chapterorder') < $article->getVar('chapters')){
		$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
		$criteria->add(new Criteria('chapterorder', $chapter->getVar('chapterorder'), '>'));
		$chapter_handler->updatefields('chapterorder = chapterorder - 1', $criteria);
	}
	unset($criteria);

	// 如果删除最后卷或者章节
	$updateblock = false;
	if($chapterid == $article->getVar('lastchapterid') || $chapterid == $article->getVar('lastvolumeid')){
		if($chapterid == $article->getVar('lastchapterid')){
			$lastinfo = jieqi_article_searchlast($article, 'lastchapter');
			$article->setVar('lastchapter', $lastinfo['name']);
			$article->setVar('lastchapterid', $lastinfo['id']);
			$article->setVar('lastsummary', $lastinfo['summary']);
		}
		$lastinfo = jieqi_article_searchlast($article, 'lastvolume');
		$article->setVar('lastvolume', $lastinfo['name']);
		$article->setVar('lastvolumeid', $lastinfo['id']);

		$updateblock = true;
	}
	elseif($chapterid == $article->getVar('vipchapterid') || $chapterid == $article->getVar('vipvolumeid')){
		if($chapterid == $article->getVar('vipchapterid')){
			$lastinfo = jieqi_article_searchlast($article, 'vipchapter');
			$article->setVar('vipchapter', $lastinfo['name']);
			$article->setVar('vipchapterid', $lastinfo['id']);
			$article->setVar('vipsummary', $lastinfo['summary']);
		}
		$lastinfo = jieqi_article_searchlast($article, 'vipvolume');
		$article->setVar('vipvolume', $lastinfo['name']);
		$article->setVar('vipvolumeid', $lastinfo['id']);
		$updateblock = true;
	}
	$article->setVar('chapters', $article->getVar('chapters') - 1);
	if($isvip > 0) $article->setVar('vipchapters', $article->getVar('vipchapters') - 1);

	$subdaywords = 0;
	$subweekwords = 0;
	$submonthwords = 0;
	$subwords = 0;

	// 日周月总时间判断
	$tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
	$daystart = mktime(0, 0, 0, (int)$tmpvar[1], (int)$tmpvar[2], (int)$tmpvar[0]);
	$monthstart = mktime(0, 0, 0, (int)$tmpvar[1], 1, (int)$tmpvar[0]);
	$tmpvar = date('w', JIEQI_NOW_TIME);
	if($tmpvar == 0) $tmpvar = 7; // 星期天是0，国人习惯作为作为一星期的最后一天
	$weekstart = $daystart;
	if($tmpvar > 1) $weekstart -= ($tmpvar - 1) * 86400;

	$clastupdate = intval($chapter->getVar('lastupdate', 'n'));
	if($clastupdate >= $daystart) $subdaywords += intval($chapter->getVar('words', 'n'));
	if($clastupdate >= $weekstart) $subweekwords += intval($chapter->getVar('words', 'n'));
	if($clastupdate >= $monthstart) $submonthwords += intval($chapter->getVar('words', 'n'));
	$subwords += intval($chapter->getVar('words', 'n'));

	$newdaywords = intval($article->getVar('daywords', 'n')) > $subdaywords ? intval($article->getVar('daywords', 'n')) - $subdaywords : 0;
	$newweekwords = intval($article->getVar('weekwords', 'n')) > $subweekwords ? intval($article->getVar('weekwords', 'n')) - $subweekwords : 0;
	$newmonthwords = intval($article->getVar('monthwords', 'n')) > $submonthwords ? intval($article->getVar('monthwords', 'n')) - $submonthwords : 0;
	$newwords = intval($article->getVar('words', 'n')) > $subwords ? intval($article->getVar('words', 'n')) - $subwords : 0;

	$article->setVar('daywords', $newdaywords);
	$article->setVar('weekwords', $newweekwords);
	$article->setVar('monthwords', $newmonthwords);
	$article->setVar('words', $newwords);
	if($isvip > 0){
		$vipwords = intval($article->getVar('vipwords', 'n')) > $subwords ? intval($article->getVar('vipwords', 'n')) - $subwords : 0;
		$article->setVar('vipwords', $vipwords);
	}
	else{
		$freewords = intval($article->getVar('freewords', 'n')) > $subwords ? intval($article->getVar('freewords', 'n')) - $subwords : 0;
		$article->setVar('freewords', $freewords);
	}

	$article_handler->insert($article);
	// 删除附件记录
	include_once($jieqiModules['article']['path'] . '/class/articleattachs.php');
	$attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
	$criteria = new CriteriaCompo(new Criteria('chapterid', $chapterid));
	$attachs_handler->delete($criteria);
	// 减少章节积分
	if(!$batch && !$ismanager){
		include_once(JIEQI_ROOT_PATH . '/class/users.php');
		$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
		jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
		$article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
		$article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
		if(!empty($jieqiAction['article']['chapteradd']['earnscore'])){
			$users_handler->changeScore($chapter->getVar('posterid'), $jieqiAction['article']['chapteradd']['earnscore'], false);
		}
	}
	// 打包处理
	include_once($jieqiModules['article']['path'] . '/class/package.php');
	$package = new JieqiPackage($article->getVar('articleid'));
	$package->delChapter($chapter);
	return $updateblock;
}

// 设置章节属性
function jieqi_article_chapterset($chapter, $article = NULL, $action = 'free'){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiAction;
	global $article_handler;
	global $chapter_handler;
	global $obook_handler;
	global $ochapter_handler;
	global $ocontent_handler;
	global $jieqi_file_postfix;
	global $query;

	if(!in_array($action, array('vip', 'free', 'show', 'hide'))) return false;
	if(!is_object($chapter)){
		$chapter = $chapter_handler->get(intval($chapter));
		if(!$chapter) return false;
		$article = $article_handler->get($chapter->getVar('articleid'));
		if(!$article) return false;
	}
	$articleid = intval($chapter->getVar('articleid', 'n'));
	$chapterid = intval($chapter->getVar('chapterid', 'n'));
	$isvip = intval($chapter->getVar('isvip', 'n'));
	$display = intval($chapter->getVar('display', 'n'));
	switch($action){
		case 'show':
			if($display == 0) return false;
			$chapter->setVar('display', 0);
			$chapter_handler->insert($chapter);
			//vip章节更新ochapter表
			if($isvip > 0){
				if(!isset($ochapter_handler) || !is_a($ochapter_handler, 'JieqiOchapterHandler')){
					include_once($jieqiModules['obook']['path'] . '/class/ochapter.php');
					$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
				}
				$ochapter = $ochapter_handler->get($chapterid, 'chapterid');
				if(is_object($ochapter)){
					$ochapter->setVar('display', 0);
					$ochapter_handler->insert($ochapter);
				}
			}
			break;
		case 'hide':
			if($display == 1) return false;
			$chapter->setVar('display', 1);
			$chapter_handler->insert($chapter);
			//vip章节更新ochapter表
			if($isvip > 0){
				if(!isset($ochapter_handler) || !is_a($ochapter_handler, 'JieqiOchapterHandler')){
					include_once($jieqiModules['obook']['path'] . '/class/ochapter.php');
					$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
				}
				$ochapter = $ochapter_handler->get($chapterid, 'chapterid');
				if(is_object($ochapter)){
					$ochapter->setVar('display', 1);
					$ochapter_handler->insert($ochapter);
				}
			}
			break;
		case 'free':
			if($isvip == 0) return false;
			$chapter->setVar('isvip', 0);
			$chapter_handler->insert($chapter);
			//vip章节内容转换成免费内容保存
			include_once($jieqiModules['article']['path'] . '/class/package.php');
			jieqi_convert_achapterc($articleid, $chapterid, 'free');
			//vip章节未销售则删除，销售则下架
			if(!isset($ochapter_handler) || !is_a($ochapter_handler, 'JieqiOchapterHandler')){
				include_once($jieqiModules['obook']['path'] . '/class/ochapter.php');
				$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
			}
			if(!isset($ocontent_handler) || !is_a($ocontent_handler, 'JieqiOcontentHandler')){
				include_once($jieqiModules['obook']['path'] . '/class/ocontent.php');
				$ocontent_handler = JieqiOcontentHandler::getInstance('JieqiOcontentHandler');
			}
			$ochapter = $ochapter_handler->get($chapterid, 'chapterid');
			if(is_object($ochapter)){
				if(intval($ochapter->getVar('sumegold', 'n')) == 0){
					$ochapter_handler->delete($chapterid, 'chapterid');
					$ocontent_handler->delete($chapterid, 'ochapterid');
					if(!isset($query) || !is_a($query, 'JieqiQueryHandler')){
						jieqi_includedb();
						$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
					}
					$sql = "UPDATE  " . jieqi_dbprefix('obook_obook') . " SET chapters = chapters - 1 WHERE articleid = " . intval($articleid) . " AND chapters > 0";
					$query->execute($sql);
				}
				else{
					$ochapter->setVar('display', 2);
					$ochapter_handler->insert($ochapter);
				}
			}
			break;
		case 'vip':
			if($isvip > 0) return false;
			if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
			if(!isset($jieqiConfigs['obook'])) jieqi_getconfigs('obook', 'configs');
			$saleprice = intval($chapter->getVar('saleprice', 'n'));
			$chapterwords = intval($chapter->getVar('words', 'n'));
			$wordspricing = (isset($jieqiConfigs['obook']['wordspricing']) && is_numeric($jieqiConfigs['obook']['wordspricing']) && $jieqiConfigs['obook']['wordspricing'] > 0) ? intval($jieqiConfigs['obook']['wordspricing']) : 1; //开始计费字节数
			//设置vip状态时候按照默认方法计算价格
			//if($saleprice == 0 && $chapterwords >= $wordspricing && is_numeric($jieqiConfigs['obook']['wordsperegold']) && $jieqiConfigs['obook']['wordsperegold'] > 0){
			if($chapterwords >= $wordspricing && is_numeric($jieqiConfigs['obook']['wordsperegold']) && $jieqiConfigs['obook']['wordsperegold'] > 0){
				$wordsperegold = floatval($jieqiConfigs['obook']['wordsperegold']); //几个字1虚拟币
				//字数取整
				if(isset($jieqiConfigs['obook']['wordsstep']) && is_numeric($jieqiConfigs['obook']['wordsstep']) && $jieqiConfigs['obook']['wordsstep'] > 1){
					$wordsstep = intval($jieqiConfigs['obook']['wordsstep']);
					if($jieqiConfigs['obook']['priceround'] == 1) $saleprice = floor($chapterwords / $wordsstep) * round($wordsstep / $wordsperegold);
					elseif($jieqiConfigs['obook']['priceround'] == 2) $saleprice = ceil($chapterwords / $wordsstep) * round($wordsstep / $wordsperegold);
					else $saleprice = round($chapterwords / $wordsstep) * round($wordsstep / $wordsperegold);
				}
				else{
					if($jieqiConfigs['obook']['priceround'] == 1) $saleprice = floor($chapterwords / $wordsperegold);
					elseif($jieqiConfigs['obook']['priceround'] == 2) $saleprice = ceil($chapterwords / $wordsperegold);
					else $saleprice = round($chapterwords / $wordsperegold);
				}
				$chapter->setVar('saleprice', $saleprice);
			}
			$chapter->setVar('isvip', 1);
			$chapter_handler->insert($chapter);
			//免费章节内容保存成vip
			include_once($jieqiModules['article']['path'] . '/class/package.php');
			jieqi_convert_achapterc($articleid, $chapterid, 'vip');

			//保存vip章节信息
			if(!isset($obook_handler) || !is_a($obook_handler, 'JieqiObookHandler')){
				include_once($jieqiModules['obook']['path'] . '/class/obook.php');
				$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
			}
			if(!isset($ochapter_handler) || !is_a($ochapter_handler, 'JieqiOchapterHandler')){
				include_once($jieqiModules['obook']['path'] . '/class/ochapter.php');
				$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
			}

			$ochapter = $ochapter_handler->get($chapterid, 'chapterid');
			if(is_object($ochapter)){
				//VIP章节已存在
				if(!isset($query) || !is_a($query, 'JieqiQueryHandler')){
					jieqi_includedb();
					$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
				}
				$sql = "UPDATE " . jieqi_dbprefix('obook_ochapter') . " SET lastupdate = '" . intval($chapter->getVar('lastupdate', 'n')) . "', chaptername = '" . jieqi_dbslashes($chapter->getVar('chaptername', 'n')) . "', summary = '" . jieqi_dbslashes($chapter->getVar('summary', 'n')) . "', words = '" . intval($chapter->getVar('words', 'n')) . "', saleprice = '" . intval($chapter->getVar('saleprice', 'n')) . "', display = 0 WHERE chapterid = " . intval($chapter->getVar('chapterid', 'n'));
				$query->execute($sql);
			}
			else{
				//VIP章节不存在，自动创建
				$obookid = intval($chapter->getVar('vipid', 'n'));
				//vip作品信息是否存在，不存在自动创建
				if($obookid == 0){
					include_once($jieqiModules['obook']['path'] . '/include/actobook.php');
					$obook = jieqi_obook_autocreate($article, 1);
					if(!is_object($obook)) jieqi_printfail($obook);
					$obookid = intval($obook->getVar('obookid', 'n'));
				}

				$ochapter = $ochapter_handler->create();
				$ochapter->setVar('ochapterid', $chapter->getVar('chapterid', 'n'));
				$ochapter->setVar('siteid', $chapter->getVar('siteid', 'n'));

				$ochapter->setVar('sourceid', $chapter->getVar('sourceid', 'n'));
				$ochapter->setVar('sourcecid', $chapter->getVar('sourcecid', 'n'));

				$ochapter->setVar('obookid', $obookid);
				$ochapter->setVar('articleid', $chapter->getVar('articleid', 'n'));
				$ochapter->setVar('chapterid', $chapter->getVar('chapterid', 'n'));
				$ochapter->setVar('postdate', $chapter->getVar('postdate', 'n'));
				$ochapter->setVar('lastupdate', $chapter->getVar('lastupdate', 'n'));
				$ochapter->setVar('buytime', 0);
				$ochapter->setVar('obookname', $chapter->getVar('articlename', 'n'));
				$ochapter->setVar('chaptername', $chapter->getVar('chaptername', 'n'));
				$ochapter->setVar('chapterorder', $chapter->getVar('chapterorder', 'n'));
				$ochapter->setVar('summary', $chapter->getVar('summary', 'n'));
				$ochapter->setVar('words', $chapter->getVar('words', 'n'));
				$ochapter->setVar('posterid', $chapter->getVar('posterid', 'n'));
				$ochapter->setVar('poster', $chapter->getVar('poster', 'n'));
				$ochapter->setVar('toptime', 0);
				$ochapter->setVar('picflag', $chapter->getVar('isimage', 'n'));
				$ochapter->setVar('chaptertype', $chapter->getVar('chaptertype', 'n'));
				$ochapter->setVar('saleprice', $chapter->getVar('saleprice', 'n'));
				$ochapter->setVar('vipprice', $chapter->getVar('saleprice', 'n'));
				$ochapter->setVar('sumegold', 0);
				$ochapter->setVar('sumesilver', 0);
				$ochapter->setVar('normalsale', 0);
				$ochapter->setVar('vipsale', 0);
				$ochapter->setVar('freesale', 0);
				$ochapter->setVar('bespsale', 0);
				$ochapter->setVar('totalsale', 0);
				$ochapter->setVar('daysale', 0);
				$ochapter->setVar('weeksale', 0);
				$ochapter->setVar('monthsale', 0);
				$ochapter->setVar('allsale', 0);
				$ochapter->setVar('lastsale', 0);
				$ochapter->setVar('display', 0);
				$ochapter_handler->insert($ochapter);

				if(!isset($query) || !is_a($query, 'JieqiQueryHandler')){
					jieqi_includedb();
					$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
				}
				$sql = "UPDATE  " . jieqi_dbprefix('obook_obook') . " SET chapters = chapters + 1 WHERE articleid = " . intval($articleid);
				$query->execute($sql);
			}
			break;
		default:
			return false;
			break;
	}

	// 如果修改最后卷或者章节
	$updateblock = false;
	if($chapterid == $article->getVar('lastchapterid') || $chapterid == $article->getVar('lastvolumeid') || $chapterid == $article->getVar('vipchapterid') || $chapterid == $article->getVar('vipvolumeid')){
		$lastinfo = jieqi_article_searchlast($article, 'all');
		$article->setVar('lastchapter', $lastinfo['lastchapter']);
		$article->setVar('lastchapterid', $lastinfo['lastchapterid']);
		$article->setVar('lastsummary', $lastinfo['lastsummary']);
		$article->setVar('lastvolume', $lastinfo['lastvolume']);
		$article->setVar('lastvolumeid', $lastinfo['lastvolumeid']);
		$article->setVar('vipchapter', $lastinfo['vipchapter']);
		$article->setVar('vipchapterid', $lastinfo['vipchapterid']);
		$article->setVar('vipsummary', $lastinfo['vipsummary']);
		$article->setVar('vipvolume', $lastinfo['vipvolume']);
		$article->setVar('vipvolumeid', $lastinfo['vipvolumeid']);
		$updateblock = true;
	}
	$subwords = intval($chapter->getVar('words', 'n'));
	if($action == 'vip'){
		$article->setVar('vipchapters', $article->getVar('vipchapters') + 1);
		$article->setVar('vipwords', $article->getVar('vipwords') + $subwords);
		$article->setVar('freewords', $article->getVar('freewords') - $subwords);
	}
	elseif($action == 'free'){
		$article->setVar('vipchapters', $article->getVar('vipchapters') - 1);
		$article->setVar('vipwords', $article->getVar('vipwords') - $subwords);
		$article->setVar('freewords', $article->getVar('freewords') + $subwords);
	}

	$article_handler->insert($article);

	// 打包处理
	include_once($jieqiModules['article']['path'] . '/class/package.php');
	$package = new JieqiPackage($article->getVar('articleid'));
	$package->setChapter($chapter);
	return $updateblock;
}

//查找一本书最新章节和分卷 $lasttype = lastchapter lastvolume vipchapter vipvolume all
function jieqi_article_searchlast($article, $lasttype){
	global $jieqiModules;
	global $jieqiConfigs;
	global $article_handler;
	global $chapter_handler;

	$ret = array('id' => 0, 'name' => '', 'summary' => '');
	switch($lasttype){
		case 'lastchapter':
			$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid', 'n')));
			$criteria->add(new Criteria('chaptertype', 0, '='));
			$criteria->add(new Criteria('isvip', 0, '='));
			$criteria->setSort('chapterorder');
			$criteria->setOrder('DESC');
			$criteria->setStart(0);
			$criteria->setLimit(1);
			$chapter_handler->queryObjects($criteria);
			$tmpchapter = $chapter_handler->getObject();
			if($tmpchapter){
				$ret = array('id' => $tmpchapter->getVar('chapterid', 'n'), 'name' => $tmpchapter->getVar('chaptername', 'n'), 'summary' => $tmpchapter->getVar('summary', 'n'));
			}
			break;
		case 'lastvolume':
			$lastchapterorder = 0;
			$lastchapterid = intval($article->getVar('lastchapterid', 'n'));
			if($lastchapterid > 0){
				$tmpchapter = $chapter_handler->get($lastchapterid);
				if(is_object($tmpchapter)) $lastchapterorder = intval($tmpchapter->getVar('chapterorder', 'n'));
			}
			$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
			$criteria->add(new Criteria('chaptertype', 1, '='));
			if($lastchapterorder > 0) $criteria->add(new Criteria('chapterorder', $lastchapterorder, '<'));
			$criteria->setSort('chapterorder');
			$criteria->setOrder('DESC');
			$criteria->setStart(0);
			$criteria->setLimit(1);
			$chapter_handler->queryObjects($criteria);
			$tmpchapter = $chapter_handler->getObject();
			if($tmpchapter){
				$ret = array('id' => $tmpchapter->getVar('chapterid', 'n'), 'name' => $tmpchapter->getVar('chaptername', 'n'), 'summary' => $tmpchapter->getVar('summary', 'n'));
			}
			break;
		case 'vipchapter':
			$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
			$criteria->add(new Criteria('chaptertype', 0, '='));
			$criteria->add(new Criteria('isvip', 0, '>'));
			$criteria->setSort('chapterorder');
			$criteria->setOrder('DESC');
			$criteria->setStart(0);
			$criteria->setLimit(1);
			$chapter_handler->queryObjects($criteria);
			$tmpchapter = $chapter_handler->getObject();
			if($tmpchapter){
				$ret = array('id' => $tmpchapter->getVar('chapterid', 'n'), 'name' => $tmpchapter->getVar('chaptername', 'n'), 'summary' => $tmpchapter->getVar('summary', 'n'));
			}
			break;
		case 'vipvolume':
			$vipchapterorder = 0;
			$vipchapterid = intval($article->getVar('vipchapterid', 'n'));
			if($vipchapterid > 0){
				$tmpchapter = $chapter_handler->get($vipchapterid);
				if(is_object($tmpchapter)) $vipchapterorder = intval($tmpchapter->getVar('chapterorder', 'n'));
			}
			$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
			$criteria->add(new Criteria('chaptertype', 1, '='));
			if($vipchapterorder > 0) $criteria->add(new Criteria('chapterorder', $vipchapterorder, '<'));
			$criteria->setSort('chapterorder');
			$criteria->setOrder('DESC');
			$criteria->setStart(0);
			$criteria->setLimit(1);
			$chapter_handler->queryObjects($criteria);
			$tmpchapter = $chapter_handler->getObject();
			if($tmpchapter){
				$ret = array('id' => $tmpchapter->getVar('chapterid', 'n'), 'name' => $tmpchapter->getVar('chaptername', 'n'), 'summary' => $tmpchapter->getVar('summary', 'n'));
			}
			break;
		case 'all':
			$ret = array('lastchapterid' => 0, 'lastchapter' => '', 'lastsummary' => '', 'lastvolumeid' => 0, 'lastvolume' => '', 'vipchapterid' => 0, 'vipchapter' => '', 'vipsummary' => '', 'vipvolumeid' => 0, 'vipvolume' => '');
			$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
			$criteria->setSort('chapterorder');
			$criteria->setOrder('DESC');
			$chapter_handler->queryObjects($criteria);
			while($tmpchapter = $chapter_handler->getObject()){
				if($tmpchapter->getVar('chaptertype', 'n') == 0){
					if($tmpchapter->getVar('isvip', 'n') == 0){
						if($ret['lastchapterid'] == 0){
							$ret['lastchapterid'] = $tmpchapter->getVar('chapterid', 'n');
							$ret['lastchapter'] = $tmpchapter->getVar('chaptername', 'n');
							$ret['lastsummary'] = $tmpchapter->getVar('summary', 'n');
						}
					}
					else{
						if($ret['vipchapterid'] == 0){
							$ret['vipchapterid'] = $tmpchapter->getVar('chapterid', 'n');
							$ret['vipchapter'] = $tmpchapter->getVar('chaptername', 'n');
							$ret['vipsummary'] = $tmpchapter->getVar('summary', 'n');
						}
					}
				}
				else{
					if($ret['lastvolumeid'] == 0 && $ret['lastchapterid'] > 0){
						$ret['lastvolumeid'] = $tmpchapter->getVar('chapterid', 'n');
						$ret['lastvolume'] = $tmpchapter->getVar('chaptername', 'n');
					}
					if($ret['vipvolumeid'] == 0 && $ret['vipchapterid'] > 0){
						$ret['vipvolumeid'] = $tmpchapter->getVar('chapterid', 'n');
						$ret['vipvolume'] = $tmpchapter->getVar('chaptername', 'n');
					}
				}
				if($ret['lastchapterid'] > 0 && $ret['lastvolumeid'] && ($article->getVar('vipid', 'n') == 0 || ($ret['vipchapterid'] > 0 && $ret['vipvolumeid'] > 0))) break;
			}
			break;
		case 'full':
			$ret = array('lastchapterid' => 0, 'lastchapter' => '', 'lastsummary' => '', 'lastvolumeid' => 0, 'lastvolume' => '', 'words' => 0, 'chapters' => 0, 'freetime' => 0, 'freewords' => 0, 'isvip' => $article->getVar('isvip', 'n'), 'vipchapterid' => 0, 'vipchapter' => '', 'vipsummary' => '', 'vipvolumeid' => 0, 'vipvolume' => '', 'vipchapters' => 0, 'vipwords' => 0, 'viptime' => 0);
			$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
			$criteria->setSort('chapterorder');
			$criteria->setOrder('DESC');
			$chapter_handler->queryObjects($criteria);
			while($tmpchapter = $chapter_handler->getObject()){
				$ret['chapters'] += 1;
				if($tmpchapter->getVar('chaptertype', 'n') == 0){
					$ret['words'] += $tmpchapter->getVar('words', 'n');
					if($tmpchapter->getVar('isvip', 'n') == 0){
						if($ret['lastchapterid'] == 0){
							$ret['lastchapterid'] = $tmpchapter->getVar('chapterid', 'n');
							$ret['lastchapter'] = $tmpchapter->getVar('chaptername', 'n');
							$ret['lastsummary'] = $tmpchapter->getVar('summary', 'n');
						}
						if($tmpchapter->getVar('postdate', 'n') > $ret['freetime']) $ret['freetime'] = $tmpchapter->getVar('postdate', 'n');
						$ret['freewords'] += $tmpchapter->getVar('words', 'n');
					}
					else{
						if($ret['vipchapterid'] == 0){
							$ret['vipchapterid'] = $tmpchapter->getVar('chapterid', 'n');
							$ret['vipchapter'] = $tmpchapter->getVar('chaptername', 'n');
							$ret['vipsummary'] = $tmpchapter->getVar('summary', 'n');
						}
						if($ret['isvip'] == 0) $ret['isvip'] = 1;
						if($tmpchapter->getVar('postdate', 'n') > $ret['viptime']) $ret['viptime'] = $tmpchapter->getVar('postdate', 'n');
						$ret['vipwords'] += $tmpchapter->getVar('words', 'n');
						$ret['vipchapters'] += 1;
					}
				}
				else{
					if($ret['lastvolumeid'] == 0 && $ret['lastchapterid'] > 0){
						$ret['lastvolumeid'] = $tmpchapter->getVar('chapterid', 'n');
						$ret['lastvolume'] = $tmpchapter->getVar('chaptername', 'n');
					}
					if($ret['vipvolumeid'] == 0 && $ret['vipchapterid'] > 0){
						$ret['vipvolumeid'] = $tmpchapter->getVar('chapterid', 'n');
						$ret['vipvolume'] = $tmpchapter->getVar('chaptername', 'n');
					}
				}
			}
			if($ret['vipchapters'] == 0 && $ret['isvip'] > 0) $ret['isvip'] = 0;
			break;
	}
	return $ret;
}

/*
 * 发表变量规范
 * aid - 小说ID（必选）
 * chaptername - 章节名称（必选）
 * chaptercontent - 章节内容（必选）
 * chapterorder - 章节排序值（可选）
 * attachfile[] - 附件数组（可选）
 * isvip - 是否VIP章节（可选），0-免费，1-vip
 * saleprice - 章节价格（可选），-1-自动生成，>=0-自定义价格
 * fullflag - 是否完本章节（可选），0-连载，1-全本
 * typeset - 是否需要自动排版（可选），0-不需要排版，1-需要排版
 * posttype - 发表方式（可选），0-直接发表， 1-存为草稿， 2-定时发表
 * pubyear - 定时发表的“年”（可选）
 * pubmonth - 定时发表的“月”（可选）
 * pubday - 定时发表的“日”（可选）
 * pubhour - 定时发表的“时”（可选）
 * pubminute - 定时发表的“分”（可选）
 * draftid - 草稿ID（可选），如果章节来自草稿箱，发表后删除本草稿
 * chaptertype - 章节类型（可选），0-章节，1-分卷
 *
 * siteid - 内容提供方网站ID，采集时候用
 * sourceid - 内容提供方小说ID，采集时候用
 * sourcecid - 内容提供方章节ID，采集是有用
 * postdate - 发表时间，采集时候用
 * lastupdate - 更新时间，采集时候用
 *
 * id - 章节ID（可选），编辑章节时候必需
 * oldattach[] - 原有附件（可选），编辑时候用
 * uptiming - 是否定时发表（可选），0-否，1是
 * canupload - 是否允许上传附件（可选）
 * posterid - 发表者ID（可选），默认当前操作用户
 * poster - 发表者名称（可选），默认当前操作用户
 *
 */
// 检查发表章节的内容是否合法
function jieqi_article_chapterpcheck(&$postvars, &$attachvars){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiLang;
	global $article_handler;
	global $chapter_handler;
	$errors = array();
	if(empty($jieqiLang['article']['article'])) jieqi_loadlang('article', 'article');
	if(empty($jieqiLang['article']['draft'])) jieqi_loadlang('draft', 'article');

	$postvars['chaptertype'] = intval($postvars['chaptertype']);
	// 检查标题
	if(strlen($postvars['chaptername']) == 0) $errors[] = $jieqiLang['article']['need_chapter_title'];

	// 检查标题和内容有没有违禁单词
	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
	include_once(JIEQI_ROOT_PATH . '/include/checker.php');
	$checker = new JieqiChecker();
	if(!empty($jieqiConfigs['system']['postdenywords'])){
		$matchwords1 = $checker->deny_words($postvars['chaptername'], $jieqiConfigs['system']['postdenywords'], true);
		$matchwords2 = $checker->deny_words($postvars['chaptercontent'], $jieqiConfigs['system']['postdenywords'], true);
		if(is_array($matchwords1) || is_array($matchwords2)){
			if(!isset($jieqiLang['system']['post'])) jieqi_loadlang('post', 'system');
			$matchwords = array();
			if(is_array($matchwords1)) $matchwords = array_merge($matchwords, $matchwords1);
			if(is_array($matchwords2)) $matchwords = array_merge($matchwords, $matchwords2);
			$errors[] = sprintf($jieqiLang['system']['post_words_deny'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
		}
	}

	// 是否定时发表
	if($postvars['uptiming'] == 1){
		$postvars['pubyear'] = intval(trim($postvars['pubyear']));
		$postvars['pubmonth'] = intval(trim($postvars['pubmonth']));
		$postvars['pubday'] = intval(trim($postvars['pubday']));
		$postvars['pubhour'] = intval(trim($postvars['pubhour']));
		$postvars['pubminute'] = intval(trim($postvars['pubminute']));
		$postvars['pubsecond'] = intval(trim($postvars['pubsecond']));
		$postvars['pubtime'] = @mktime($postvars['pubhour'], $postvars['pubminute'], $postvars['pubsecond'], $postvars['pubmonth'], $postvars['pubday'], $postvars['pubyear']);
		if($postvars['pubtime'] <= JIEQI_NOW_TIME) $errors[] = $jieqiLang['article']['uptiming_time_low'];
	}

	// 检查附件
	$attachvars = array('id' => array(), 'info' => array());
	$attachnum = 0;

	// 检查上传文件
	if($postvars['canupload'] && is_numeric($jieqiConfigs['article']['maxattachnum']) && $jieqiConfigs['article']['maxattachnum'] > 0 && isset($_FILES['attachfile'])){

		$maxfilenum = intval($jieqiConfigs['article']['maxattachnum']);
		$typeary = explode(' ', trim($jieqiConfigs['article']['attachtype']));
		foreach($typeary as $k => $v){
			if(substr($v, 0, 1) == '.') $typeary[$k] = substr($typeary[$k], 1);
		}
		foreach($_FILES['attachfile']['name'] as $k => $v){
			if(!empty($v)){
				$tmpary = explode('.', $v);
				$tmpint = count($tmpary) - 1;
				$tmpary[$tmpint] = strtolower(trim($tmpary[$tmpint]));
				$denyary = array('htm', 'html', 'shtml', 'php', 'asp', 'aspx', 'jsp', 'pl', 'cgi');
				if(empty($tmpary[$tmpint]) || !in_array($tmpary[$tmpint], $typeary)){
					$errors[] = sprintf($jieqiLang['article']['upload_filetype_error'], $v);
				}
				elseif(in_array($tmpary[$tmpint], $denyary)){
					$errors[] = sprintf($jieqiLang['article']['upload_filetype_limit'], $tmpary[$tmpint]);
				}
				if(preg_match("/\.(gif|jpg|jpeg|png|bmp)$/i", $v)){
					$fclass = 'image';
					if($_FILES['attachfile']['size'][$k] > (intval($jieqiConfigs['article']['maximagesize']) * 1024)) $errors[] = sprintf($jieqiLang['article']['upload_filesize_toolarge'], $v, intval($jieqiConfigs['article']['maximagesize']));
				}
				else{
					$fclass = 'file';
					if($_FILES['attachfile']['size'][$k] > (intval($jieqiConfigs['article']['maxfilesize']) * 1024)) $errors[] = sprintf($jieqiLang['article']['upload_filesize_toolarge'], $v, intval($jieqiConfigs['article']['maxfilesize']));
				}
				if(!empty($errtext)){
					jieqi_delfile($_FILES['attachfile']['tmp_name'][$k]);
				}
				else{
					$attachvars['id'][$attachnum] = $k;
					$attachvars['info'][$attachnum] = array('name' => $v, 'class' => $fclass, 'postfix' => $tmpary[$tmpint], 'size' => $_FILES['attachfile']['size'][$k]);
					$attachnum++;
				}
			}
		}
	}

	// 有附件的话允许章节没内容，否则必须有
	if(count($postvars['oldattach']) == 0 && $attachnum == 0 && $postvars['chaptertype'] == 0 && strlen($postvars['chaptercontent']) == 0) $errors[] = $jieqiLang['article']['need_chapter_content'];

	if(empty($errors) && $postvars['chaptertype'] == 0){
		//替换单词
		if(isset($jieqiConfigs['system']['postreplacewords']) && !empty($jieqiConfigs['system']['postreplacewords'])){
			$checker->replace_words($postvars['chaptername'], $jieqiConfigs['system']['postreplacewords']);
			$checker->replace_words($postvars['chaptercontent'], $jieqiConfigs['system']['postreplacewords']);
		}
		// 内容排版
		if($jieqiConfigs['article']['authtypeset'] == 2 || ($jieqiConfigs['article']['authtypeset'] == 1 && $postvars['typeset'] == 1)){
			include_once(JIEQI_ROOT_PATH . '/lib/text/texttypeset.php');
			$texttypeset = new TextTypeset();
			$postvars['chaptercontent'] = $texttypeset->doTypeset($postvars['chaptercontent']);
		}
	}

	return $errors;
}

// $postvars: aid, draftid, chapterorder, chaptername,
// chaptercontent,
// attachfile[], fullflag, typeset, posttype, pubyear, pubmonth, pubday
// 保存章节信息到数据库
function jieqi_article_addchapter(&$postvars, &$attachvars, &$article, $batch = false){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiOption;
	global $jieqiLang;
	global $jieqiAction;
	global $article_handler;
	global $chapter_handler;
	global $attachs_handler;
	global $draft_handler;

	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
	if(!isset($jieqiConfigs['obook'])) jieqi_getconfigs('obook', 'configs');
	if(!isset($jieqiOption['article'])) jieqi_getconfigs('article', 'option', 'jieqiOption');

	$postvars['chaptertype'] = intval($postvars['chaptertype']);
	$postvars['articleid'] = !empty($postvars['articleid']) ? intval($postvars['articleid']) : intval($article->getVar('articleid', 'n'));

	// 如果是从草稿箱发表,检查有没有附件
	$oldattachary = array();
	if(!empty($postvars['draftid'])){
		$postvars['draftid'] = intval($postvars['draftid']);
		if(!isset($draft_handler) || !is_a($draft_handler, 'JieqiDraftHandler')){
			include_once($jieqiModules['article']['path'] . '/class/draft.php');
			$draft_handler = JieqiDraftHandler::getInstance('JieqiDraftHandler');
		}
		$draft = $draft_handler->get($postvars['draftid']);
		if(!is_object($draft)) $postvars['draftid'] = 0;
		$tmpattachary = @jieqi_unserialize($draft->getVar('attachment', 'n'));
		if(is_array($tmpattachary) && count($tmpattachary) > 0){
			if(!isset($attachs_handler) || !is_a($attachs_handler, 'JieqiArticleattachsHandler')){
				include_once($jieqiModules['article']['path'] . '/class/articleattachs.php');
				$attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
			}

			if(!is_array($postvars['oldattach'])){
				if(is_string($postvars['oldattach']) && strlen($postvars['oldattach']) > 0) $postvars['oldattach'] = array(
					$postvars['oldattach']
				);
				else $postvars['oldattach'] = array();
			}
			if(empty($postvars['oldattach']) && !empty($postvars['attachment'])){
				$tmpoldary = jieqi_unserialize($postvars['attachment']);
				if(is_array($tmpoldary) && count($tmpoldary) > 0){
					foreach($tmpoldary as $ot) $postvars['oldattach'][] = $ot['attachid'];
				}
			}
			$oldattachary = array();
			foreach($tmpattachary as $val){
				if(in_array($val['attachid'], $postvars['oldattach'])){
					$oldattachary[] = $val;
				}else{
					// 删除旧附件
					$attachs_handler->delete($val['attachid']);
					$afname = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article') . jieqi_getsubdir($postvars['articleid']) . '/' . $postvars['articleid'] . '/0' . $postvars['draftid'] . '/' . $val['attachid'] . '.' . $val['postfix'];
					jieqi_delfile($afname);
				}
			}
		}
	}
	$oldattachnum = count($oldattachary);

	// 附件入库
	if(!is_array($attachvars['info'])) $attachvars = array('id' => array(), 'info' => array());
	$attachnum = count($attachvars['info']);
	$allattachnum = $attachnum + $oldattachnum;
	if($attachnum > 0){
		if(!isset($attachs_handler) || !is_a($attachs_handler, 'JieqiArticleattachsHandler')){
			include_once($jieqiModules['article']['path'] . '/class/articleattachs.php');
			$attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
		}
		foreach($attachvars['info'] as $k => $v){
			if(empty($attachvars['info'][$k]['attachid'])){
				$newAttach = $attachs_handler->create();
				$newAttach->setVar('articleid', $article->getVar('articleid', 'n'));
				$newAttach->setVar('chapterid', 0);
				$newAttach->setVar('name', $v['name']);
				$newAttach->setVar('class', $v['class']);
				$newAttach->setVar('postfix', $v['postfix']);
				$newAttach->setVar('size', $v['size']);
				$newAttach->setVar('hits', 0);
				$newAttach->setVar('needexp', 0);
				$newAttach->setVar('uptime', JIEQI_NOW_TIME);
				if($attachs_handler->insert($newAttach)){
					$attachid = $newAttach->getVar('attachid');
					$attachvars['info'][$k]['attachid'] = $attachid;
				}
				else{
					$attachvars['info'][$k]['attachid'] = 0;
				}
			}
		}
	}

	// 章节排序
	$chaptercount = $article->getVar('chapters');
	$postvars['chapterorder'] = intval($postvars['chapterorder']);
	if(empty($postvars['chapterorder'])) $postvars['chapterorder'] = $chaptercount + 1;
	// 如果是插入章节，则原来章节的排序值加一位
	if($postvars['chapterorder'] <= $chaptercount){
		$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
		$criteria->add(new Criteria('chapterorder', $postvars['chapterorder'], '>='));
		$chapter_handler->updatefields('chapterorder = chapterorder + 1', $criteria);
		unset($criteria);
	}

	$newChapter = $chapter_handler->create();
	$newChapter->setVar('siteid', $article->getVar('siteid', 'n'));
	$newChapter->setVar('sourceid', $article->getVar('sourceid', 'n'));
	$newChapter->setVar('articleid', $article->getVar('articleid', 'n'));
	$newChapter->setVar('articlename', $article->getVar('articlename', 'n'));
	$newChapter->setVar('volumeid', 0);
	if(!empty($postvars['sourcecid'])) $newChapter->setVar('sourcecid', $postvars['sourcecid']);
	if(!empty($postvars['posterid'])){
		$newChapter->setVar('posterid', $postvars['posterid']);
		$newChapter->setVar('poster', $postvars['poster']);
	}
	elseif(!empty($_SESSION['jieqiUserId'])){
		$newChapter->setVar('posterid', $_SESSION['jieqiUserId']);
		$newChapter->setVar('poster', $_SESSION['jieqiUserName']);
	}
	else{
		$newChapter->setVar('posterid', 0);
		$newChapter->setVar('poster', '');
	}
	$postdate = empty($postvars['postdate']) ? JIEQI_NOW_TIME : intval($postvars['postdate']);
	$newChapter->setVar('postdate', $postdate);
	$lastupdate = empty($postvars['lastupdate']) ? JIEQI_NOW_TIME : intval($postvars['lastupdate']);
	$newChapter->setVar('lastupdate', $lastupdate);
	$newChapter->setVar('chaptername', $postvars['chaptername']);
	$newChapter->setVar('chapterorder', $postvars['chapterorder']);
	if($article->getVar('siteid', 'n') > 0 && !empty($postvars['words'])){
		$chapterwords = intval($postvars['words']);
		$postvars['saleprice'] = empty($postvars['saleprice']) ? 0 : intval($postvars['saleprice']);
	}else{
		//字数和价格
		$chapterwords = jieqi_strwords($postvars['chaptercontent']);
		$wordspricing = (isset($jieqiConfigs['obook']['wordspricing']) && is_numeric($jieqiConfigs['obook']['wordspricing']) && $jieqiConfigs['obook']['wordspricing'] > 0) ? intval($jieqiConfigs['obook']['wordspricing']) : 1; //开始计费字节数
		if($chapterwords > 0 && (!isset($postvars['saleprice']) || !is_numeric($postvars['saleprice']) || intval($postvars['saleprice']) < 0)){
			$postvars['saleprice'] = 0;
			if($chapterwords >= $wordspricing && is_numeric($jieqiConfigs['obook']['wordsperegold']) && $jieqiConfigs['obook']['wordsperegold'] > 0){
				$wordsperegold = floatval($jieqiConfigs['obook']['wordsperegold']); //几个字1虚拟币
				//字数取整
				if(isset($jieqiConfigs['obook']['wordsstep']) && is_numeric($jieqiConfigs['obook']['wordsstep']) && $jieqiConfigs['obook']['wordsstep'] > 1){
					$wordsstep = intval($jieqiConfigs['obook']['wordsstep']);
					if($jieqiConfigs['obook']['priceround'] == 1) $postvars['saleprice'] = floor($chapterwords / $wordsstep) * round($wordsstep / $wordsperegold);
					elseif($jieqiConfigs['obook']['priceround'] == 2) $postvars['saleprice'] = ceil($chapterwords / $wordsstep) * round($wordsstep / $wordsperegold);
					else $postvars['saleprice'] = round($chapterwords / $wordsstep) * round($wordsstep / $wordsperegold);
				}
				else{
					if($jieqiConfigs['obook']['priceround'] == 1) $postvars['saleprice'] = floor($chapterwords / $wordsperegold);
					elseif($jieqiConfigs['obook']['priceround'] == 2) $postvars['saleprice'] = ceil($chapterwords / $wordsperegold);
					else $postvars['saleprice'] = round($chapterwords / $wordsperegold);
				}
			}
		}
		else{
			$postvars['saleprice'] = $chapterwords == 0 ? 0 : intval($postvars['saleprice']);
		}
	}
	//一个附件当成一个汉字，免得只有附件认为没字数而不显示
	$newChapter->setVar('isimage', 0);
	if($allattachnum > 0){
		if($chapterwords == 0 && $postvars['chaptertype'] == 0) $newChapter->setVar('isimage', 1);
		$chapterwords += $allattachnum;
	}

	$newChapter->setVar('words', $chapterwords);
	$newChapter->setVar('saleprice', $postvars['saleprice']);
	$newChapter->setVar('salenum', 0);
	$newChapter->setVar('totalcost', 0);
	if(empty($oldattachary)) $newChapter->setVar('attachment', serialize($attachvars['info']));
	else{
		$newattachary = $oldattachary;
		foreach($attachvars['info'] as $val){
			$newattachary[] = $val;
		}
		$newChapter->setVar('attachment', serialize($newattachary));
	}
	if($chapterwords > 0) $newChapter->setVar('summary', jieqi_substr($postvars['chaptercontent'], 0, 500, '..'));
	else $newChapter->setVar('summary', '');
	if(isset($postvars['preface'])) $newChapter->setVar('preface', $postvars['preface']);
	if(isset($postvars['notice'])) $newChapter->setVar('notice', $postvars['notice']);
	//if(isset($postvars['foreword'])) $newChapter->setVar('foreword', $postvars['foreword']);
	if(isset($postvars['isbody'])) $newChapter->setVar('isbody', intval($postvars['isbody']));

	$newChapter->setVar('isvip', intval($postvars['isvip']));
	if($postvars['chaptertype'] > 0){
		$newChapter->setVar('chaptertype', 1);
	}
	else{
		$newChapter->setVar('chaptertype', 0);
	}
	$newChapter->setVar('power', 0);
	$newChapter->setVar('display', 0);
	if(!$chapter_handler->insert($newChapter)){
		if($attachnum > 0){
			foreach($attachvars['info'] as $k => $v){
				if($v['attachid'] > 0) $attachs_handler->delete($v['attachid']);
			}
		}
		return $jieqiLang['article']['add_chapter_failure'];
	}
	else{
		$chapterid = intval($newChapter->getVar('chapterid'));
		// 判断是否加水印
		$make_image_water = false;
		if(function_exists('gd_info') && $jieqiConfigs['article']['attachwater'] > 0 && JIEQI_MODULE_VTYPE != '' && JIEQI_MODULE_VTYPE != 'Free'){
			if(strpos($jieqiConfigs['article']['attachwimage'], '/') === false && strpos($jieqiConfigs['article']['attachwimage'], '\\') === false) $water_image_file = $jieqiModules['article']['path'] . '/images/' . $jieqiConfigs['article']['attachwimage'];
			else $water_image_file = $jieqiConfigs['article']['attachwimage'];
			if(is_file($water_image_file)){
				$make_image_water = true;
				include_once(JIEQI_ROOT_PATH . '/lib/image/imagewater.php');
			}
		}
		//如果有草稿附件，改成章节附件
		if(!empty($oldattachary)){
			$attachs_handler->execute("UPDATE " . jieqi_dbprefix('article_attachs') . " SET chapterid=" . $chapterid . " WHERE articleid=" . $article->getVar('articleid', 'n') . " AND chapterid = -" . $postvars['draftid']);

			$chapter_attach_dir = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article');
			$chapter_attach_dir .= jieqi_getsubdir($newChapter->getVar('articleid'));
			$chapter_attach_dir .= '/' . $newChapter->getVar('articleid');
			$draft_attach_dir = $chapter_attach_dir . '/0' . $postvars['draftid'];
			$chapter_attach_dir .= '/' . $newChapter->getVar('chapterid');
			jieqi_checkdir($chapter_attach_dir, true);
			foreach($oldattachary as $k => $v){
				jieqi_copyfile($draft_attach_dir . '/' . $v['attachid'] . '.' . $v['postfix'], $chapter_attach_dir . '/' . $v['attachid'] . '.' . $v['postfix'], 0777, true);
			}
			jieqi_delfolder($draft_attach_dir, true);
		}
		// 处理上传文件
		if($attachnum > 0){
			$attachs_handler->execute("UPDATE " . jieqi_dbprefix('article_attachs') . " SET chapterid=" . $chapterid . " WHERE articleid=" . $article->getVar('articleid', 'n') . " AND chapterid = 0");
			$attachdir = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article');
			$attachdir .= jieqi_getsubdir($newChapter->getVar('articleid'));
			$attachdir .= '/' . $newChapter->getVar('articleid');
			$attachdir .= '/' . $newChapter->getVar('chapterid');
			jieqi_checkdir($attachdir, true);
			foreach($attachvars['info'] as $k => $v){
				$fileid = $attachvars['id'][$k];
				$attach_save_path = $attachdir . '/' . $v['attachid'] . '.' . $v['postfix'];
				$tmp_attachfile = $attachdir . '/' . basename($_FILES['attachfile']['tmp_name'][$fileid]) . '.' . $v['postfix'];
				@move_uploaded_file($_FILES['attachfile']['tmp_name'][$fileid], $tmp_attachfile);
				// 图片加水印
				if($make_image_water && preg_match("/\.(gif|jpg|jpeg|png)$/i", $tmp_attachfile)){
					$img = new ImageWater();
					$img->save_image_file = $tmp_attachfile;
					$img->codepage = JIEQI_SYSTEM_CHARSET;
					$img->wm_image_pos = $jieqiConfigs['article']['attachwater'];
					$img->wm_image_name = $water_image_file;
					$img->wm_image_transition = $jieqiConfigs['article']['attachwtrans'];
					$img->jpeg_quality = $jieqiConfigs['article']['attachwquality'];
					$img->create($tmp_attachfile);
					unset($img);
				}
				jieqi_copyfile($tmp_attachfile, $attach_save_path, 0777, true);
			}
		}
		//如果是vip章节，生成vip快照
		if(!empty($postvars['isvip']) && $postvars['chaptertype'] == 0){
			global $obook_handler;
			global $ochapter_handler;

			if(!isset($obook_handler) || !is_a($obook_handler, 'JieqiObookHandler')){
				include_once($jieqiModules['obook']['path'] . '/class/obook.php');
				$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
			}
			if(!isset($ochapter_handler) || !is_a($ochapter_handler, 'JieqiOchapterHandler')){
				include_once($jieqiModules['obook']['path'] . '/class/ochapter.php');
				$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
			}

			include_once($jieqiModules['obook']['path'] . '/include/actobook.php');
			$obook = jieqi_obook_autocreate($article, 1);
			if(!is_object($obook)) return $obook;
			$article->setVar('isvip', 1);
			$article->setVar('vipid', $obook->getVar('obookid', 'n'));

			$ochapter = $ochapter_handler->create();
			$ochapter->setVar('ochapterid', $newChapter->getVar('chapterid', 'n'));
			$ochapter->setVar('siteid', $newChapter->getVar('siteid', 'n'));

			$ochapter->setVar('sourceid', $newChapter->getVar('sourceid', 'n'));
			$ochapter->setVar('sourcecid', $newChapter->getVar('sourcecid', 'n'));

			$ochapter->setVar('obookid', $obook->getVar('obookid', 'n'));
			$ochapter->setVar('articleid', $newChapter->getVar('articleid', 'n'));
			$ochapter->setVar('chapterid', $newChapter->getVar('chapterid', 'n'));
			$ochapter->setVar('postdate', $newChapter->getVar('postdate', 'n'));
			$ochapter->setVar('lastupdate', $newChapter->getVar('lastupdate', 'n'));
			$ochapter->setVar('buytime', 0);
			$ochapter->setVar('obookname', $newChapter->getVar('articlename', 'n'));
			$ochapter->setVar('chaptername', $newChapter->getVar('chaptername', 'n'));
			$ochapter->setVar('chapterorder', $newChapter->getVar('chapterorder', 'n'));
			$ochapter->setVar('summary', $newChapter->getVar('summary', 'n'));
			$ochapter->setVar('preface', $newChapter->getVar('preface', 'n'));
			$ochapter->setVar('notice', $newChapter->getVar('notice', 'n'));
			$ochapter->setVar('foreword', $newChapter->getVar('foreword', 'n'));
			$ochapter->setVar('isbody', $newChapter->getVar('isbody', 'n'));
			$ochapter->setVar('words', $newChapter->getVar('words', 'n'));
			$ochapter->setVar('posterid', $newChapter->getVar('posterid', 'n'));
			$ochapter->setVar('poster', $newChapter->getVar('poster', 'n'));
			$ochapter->setVar('toptime', 0);
			$ochapter->setVar('picflag', $newChapter->getVar('isimage', 'n'));
			$ochapter->setVar('chaptertype', $newChapter->getVar('chaptertype', 'n'));
			$ochapter->setVar('saleprice', $newChapter->getVar('saleprice', 'n'));
			$ochapter->setVar('vipprice', $newChapter->getVar('saleprice', 'n'));
			$ochapter->setVar('sumegold', 0);
			$ochapter->setVar('sumesilver', 0);
			$ochapter->setVar('normalsale', 0);
			$ochapter->setVar('vipsale', 0);
			$ochapter->setVar('freesale', 0);
			$ochapter->setVar('bespsale', 0);
			$ochapter->setVar('totalsale', 0);
			$ochapter->setVar('daysale', 0);
			$ochapter->setVar('weeksale', 0);
			$ochapter->setVar('monthsale', 0);
			$ochapter->setVar('allsale', 0);
			$ochapter->setVar('lastsale', 0);
			$ochapter->setVar('display', 0);
			$ochapter_handler->insert($ochapter);

			$obook->setVar('chapters', intval($obook->getVar('chapters', 'n')) + 1);
			$obook_handler->insert($obook);
		}

		// 如果是从草稿箱发表，则发表后删除草稿
		if(!empty($postvars['draftid'])){
			global $draft_handler;
			if(!isset($draft_handler) || !is_a($draft_handler, 'JieqiDraftHandler')){
				include_once($jieqiModules['article']['path'] . '/class/draft.php');
				$draft_handler = JieqiDraftHandler::getInstance('JieqiDraftHandler');
			}
			$draft_handler->delete($postvars['draftid']);
		}

		if(!empty($batch)){
			include_once($jieqiModules['article']['path'] . '/class/package.php');
			jieqi_save_achapterc(intval($article->getVar('articleid', 'n')), intval($newChapter->getVar('chapterid', 'n')), $postvars['chaptercontent'], intval($newChapter->getVar('isvip', 'n')), intval($newChapter->getVar('chaptertype', 'n')));
		}else{
			//更新小说信息
			if($postvars['chaptertype'] == 0){
				// 增加或插入章节，最新卷可能也会变化
				$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
				$criteria->add(new Criteria('chapterorder', $postvars['chapterorder'], '<'));
				$criteria->add(new Criteria('chaptertype', 1, '='));
				$criteria->setSort('chapterorder');
				$criteria->setOrder('DESC');
				$criteria->setLimit(1);
				$chapter_handler->queryObjects($criteria);
				$tmpchapter = $chapter_handler->getObject();
				if(is_object($tmpchapter)){
					$lastvolume = $tmpchapter->getVar('chaptername', 'n');
					$lastvolumeid = $tmpchapter->getVar('chapterid', 'n');
				}
				else{
					$lastvolume = '';
					$lastvolumeid = 0;
				}
				unset($tmpchapter);
				unset($criteria);

				if(!empty($postvars['isvip'])){
					$article->setVar('vipchapter', $postvars['chaptername']);
					$article->setVar('vipchapterid', $newChapter->getVar('chapterid', 'n'));
					$article->setVar('vipsummary', $newChapter->getVar('summary', 'n'));
					// 插入章节时，卷也可能变化
					if($article->getVar('vipvolumeid') != $lastvolumeid){
						$article->setVar('vipvolume', $lastvolume);
						$article->setVar('vipvolumeid', $lastvolumeid);
					}
				}
				else{
					$article->setVar('lastchapter', $postvars['chaptername']);
					$article->setVar('lastchapterid', $newChapter->getVar('chapterid', 'n'));
					$article->setVar('lastsummary', $newChapter->getVar('summary', 'n'));
					// 插入章节时，卷也可能变化
					if($article->getVar('lastvolumeid') != $lastvolumeid){
						$article->setVar('lastvolume', $lastvolume);
						$article->setVar('lastvolumeid', $lastvolumeid);
					}
				}
			}
			else{
				// 增加分卷，最新卷可能也会变化
				$criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid')));
				$criteria->add(new Criteria('chapterorder', $postvars['chapterorder'], '>'));
				$criteria->add(new Criteria('chaptertype', 0, '='));
				$criteria->setSort('chapterorder');
				$criteria->setOrder('DESC');
				$chapter_handler->queryObjects($criteria);
				$tmpchapter = $chapter_handler->getObject();
				if(is_object($tmpchapter)){
					if(!empty($postvars['isvip'])){
						if($tmpchapter->getVar('chapterid', 'n') == $article->getVar('vipchapterid', 'n')){
							$article->setVar('vipvolume', $postvars['chaptername']);
							$article->setVar('vipvolumeid', $newChapter->getVar('chapterid', 'n'));
						}
					}
					else{
						if($tmpchapter->getVar('chapterid', 'n') == $article->getVar('lastchapterid', 'n')){
							$article->setVar('lastvolume', $postvars['chaptername']);
							$article->setVar('lastvolumeid', $newChapter->getVar('chapterid', 'n'));
						}
					}
				}
				unset($tmpchapter);
				unset($criteria);
			}
			// 更新字数统计 日 周 月 总
			include_once(JIEQI_ROOT_PATH . '/include/funstat.php');
			$lasttime = $article->getVar('lastupdate', 'n');
			$addorup = jieqi_visit_addorup($lasttime);
			$daywords = $addorup['day'] ? $chapterwords : $article->getVar('daywords', 'n') + $chapterwords;
			$weekwords = $addorup['week'] ? $chapterwords : $article->getVar('weekwords', 'n') + $chapterwords;
			$monthwords = $addorup['month'] ? $chapterwords : $article->getVar('monthwords', 'n') + $chapterwords;
			$allwords = $article->getVar('words', 'n') + $chapterwords;
			if(floatval(JIEQI_VERSION) >= 2.2){
				//月更新天数，上月更新天数
				if($addorup['month'] > 1){
					$article->setVar('prewords', 0);
					$article->setVar('preupds', 0);
					$article->setVar('preupdt', 0);
					$article->setVar('monthupds', 0);
					$article->setVar('monthupdt', 0);
				}elseif($addorup['month'] == 1){
					$article->setVar('prewords', $article->getVar('monthwords', 'n'));
					$article->setVar('preupds', $article->getVar('monthupds', 'n'));
					$article->setVar('preupdt', $article->getVar('monthupdt', 'n'));
					$article->setVar('monthupds', 0);
					$article->setVar('monthupdt', 0);
				}
				if($addorup['day']){
					$article->setVar('monthupds', $article->getVar('monthupds', 'n') + 1);
					$tmpvar = intval($article->getVar('monthupdt', 'n')) | pow(2, intval(date('d', JIEQI_NOW_TIME)) - 1);
					$article->setVar('monthupdt', $tmpvar);
				}
			}
			$article->setVar('daywords', $daywords);
			$article->setVar('weekwords', $weekwords);
			$article->setVar('monthwords', $monthwords);
			$article->setVar('words', $allwords);
			if(!empty($postvars['isvip'])){
				$vipwords = $article->getVar('vipwords', 'n') + $chapterwords;
				$article->setVar('vipwords', $vipwords);
			}
			else{
				$freewords = $article->getVar('freewords', 'n') + $chapterwords;
				$article->setVar('freewords', $freewords);
			}

			if($postvars['fullflag'] == 1){
				$article->setVar('fullflag', 1);
				if(!empty($jieqiOption['article']['progress']['items'])){
					end($jieqiOption['article']['progress']['items']);
					$article->setVar('progress', key($jieqiOption['article']['progress']['items']));
					reset($jieqiOption['article']['progress']['items']);
				}
			}

			$article->setVar('lastupdate', JIEQI_NOW_TIME);

			$article->setVar('chapters', $article->getVar('chapters') + 1);
			if(!empty($postvars['isvip'])){
				$article->setVar('vipchapters', $article->getVar('vipchapters') + 1);
				$article->setVar('viptime', JIEQI_NOW_TIME);
			}
			else{
				$article->setVar('freetime', JIEQI_NOW_TIME);
			}
			$article_handler->insert($article);

			// 保存小说内容和生成html
			include_once($jieqiModules['article']['path'] . '/class/package.php');
			$package = new JieqiPackage($article->getVar('articleid', 'n'));
			$package->addChapter($newChapter, $postvars['chaptercontent'], $article);
			// 处理动作（积分、日志、贡献值）

			if($postvars['chaptertype'] == 0){
				include_once($jieqiModules['article']['path'] . '/include/funaction.php');
				$actions = array('actname' => 'chapteradd', 'actnum' => 1, 'uid' => intval($newChapter->getVar('posterid', 'n')), 'uname' => $newChapter->getVar('poster', 'n'));
				jieqi_article_actiondo($actions, $article);
			}

			// 触发更新（时间，缓存，静态页）
			if($postvars['chaptertype'] == 0) jieqi_article_updateinfo($article, 'chapternew');
		}
	}
	return $chapterid;
}

// $postvars: aid, draftid, chapterorder, chaptername,
// chaptercontent,
// attachfile[], fullflag, typeset, posttype, pubyear, pubmonth, pubday
// 保存章节信息到草稿箱
function jieqi_article_adddraft(&$postvars, &$attachvars){
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiOption;
	global $jieqiLang;
	global $jieqiAction;
	global $article_handler;
	global $chapter_handler;
	global $attachs_handler;
	global $draft_handler;

	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
	if(!isset($jieqiConfigs['obook'])) jieqi_getconfigs('obook', 'configs');
	if(!isset($jieqiOption['article'])) jieqi_getconfigs('article', 'option', 'jieqiOption');

	$postvars['chaptertype'] = intval($postvars['chaptertype']);

	if(!isset($draft_handler) || !is_a($draft_handler, 'JieqiDraftHandler')){
		include_once ($jieqiModules['article']['path'] . '/class/draft.php');
		$draft_handler = JieqiDraftHandler::getInstance('JieqiDraftHandler');
	}

	// 附件入库
	if(!is_array($attachvars['info'])) $attachvars = array('id' => array(), 'info' => array());
	$attachnum = count($attachvars['info']);
	if($attachnum > 0){
		if(!isset($attachs_handler) || !is_a($attachs_handler, 'JieqiArticleattachsHandler')){
			include_once($jieqiModules['article']['path'] . '/class/articleattachs.php');
			$attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
		}
		foreach($attachvars['info'] as $k => $v){
			if(empty($attachvars['info'][$k]['attachid'])){
				$newAttach = $attachs_handler->create();
				$newAttach->setVar('articleid', $postvars['articleid']);
				$newAttach->setVar('chapterid', 0);
				$newAttach->setVar('name', $v['name']);
				$newAttach->setVar('class', $v['class']);
				$newAttach->setVar('postfix', $v['postfix']);
				$newAttach->setVar('size', $v['size']);
				$newAttach->setVar('hits', 0);
				$newAttach->setVar('needexp', 0);
				$newAttach->setVar('uptime', JIEQI_NOW_TIME);
				if($attachs_handler->insert($newAttach)){
					$attachid = $newAttach->getVar('attachid');
					$attachvars['info'][$k]['attachid'] = $attachid;
				}
				else{
					$attachvars['info'][$k]['attachid'] = 0;
				}
			}
		}
	}

	//草稿入库
	$newDraft = $draft_handler->create();
	$newDraft->setVar('articleid', $postvars['articleid']);
	$newDraft->setVar('articlename', $postvars['articlename']);
	$newDraft->setVar('volumeid', intval($postvars['volumeid']));
	$newDraft->setVar('volumename', $postvars['volumename']);
	$newDraft->setVar('chapterid', 0);
	$newDraft->setVar('chapterorder', 0);
	$newDraft->setVar('chaptertype', 0);

	$newDraft->setVar('isvip', intval($postvars['isvip']));
	$newDraft->setVar('obookid', $postvars['obookid']);
	if(!empty($_SESSION['jieqiUserId'])){
		$newDraft->setVar('posterid', $_SESSION['jieqiUserId']);
		$newDraft->setVar('poster', $_SESSION['jieqiUserName']);
	}else{
		$newDraft->setVar('posterid', 0);
		$newDraft->setVar('poster', '');
	}
	$newDraft->setVar('postdate', JIEQI_NOW_TIME);
	$newDraft->setVar('lastupdate', JIEQI_NOW_TIME);
	if($postvars['uptiming'] == 1){
		$newDraft->setVar('ispub', 1);
		$newDraft->setVar('pubdate', $postvars['pubtime']);
	}else{
		$newDraft->setVar('ispub', 0);
		$newDraft->setVar('pubdate', 0);
	}
	$newDraft->setVar('chaptername', $postvars['chaptername']);
	$newDraft->setVar('chaptercontent', $postvars['chaptercontent']);
	$draftwords = jieqi_strwords($postvars['chaptercontent']);

	if($draftwords > 0) $newDraft->setVar('summary', jieqi_substr($postvars['chaptercontent'], 0, 500, '..'));
	else $newDraft->setVar('summary', '');
	if(isset($postvars['preface'])) $newDraft->setVar('preface', $postvars['preface']);
	if(isset($postvars['notice'])) $newDraft->setVar('notice', $postvars['notice']);
	//if(isset($postvars['foreword'])) $newDraft->setVar('foreword', $postvars['foreword']);
	if(isset($postvars['isbody'])) $newDraft->setVar('isbody', intval($postvars['isbody']));

	//一个附件当成一个汉字，免得只有附件认为没字数而不显示
	$newDraft->setVar('isimage', 0);
	if($attachnum > 0){
		if($draftwords == 0) $newDraft->setVar('isimage', 1);
		$draftwords += $attachnum;
	}else
	$newDraft->setVar('words', $draftwords);

	// 价格判断 >0 只对有自定义权限有效，=0 表示免费，都有效，<0 表示自动定价
	if(!isset($customprice)) $customprice = false;
	if(!isset($postvars['saleprice']) || !is_numeric($postvars['saleprice'])){
		$postvars['saleprice'] = -1; // 表示自动生成
	}else{
		$postvars['saleprice'] = intval($postvars['saleprice']);
		if($postvars['saleprice'] < 0 || ($postvars['saleprice'] > 0 && !$customprice)) $postvars['saleprice'] = -1; // 表示自动生成
	}
	if($postvars['isvip'] <= 0) $postvars['saleprice'] = -1;
	$newDraft->setVar('saleprice', $postvars['saleprice']);
	$newDraft->setVar('note', '');
	$newDraft->setVar('attachment', serialize($attachvars['info']));
	$newDraft->setVar('power', 0);
	if($postvars['needupaudit']) $newDraft->setVar('display', 1);
	else $newDraft->setVar('display', 0);
	$newDraft->setVar('draftflag', 0);

	if(!$draft_handler->insert($newDraft)){
		if($attachnum > 0){
			foreach($attachvars['info'] as $k => $v){
				if($v['attachid'] > 0) $attachs_handler->delete($v['attachid']);
			}
		}
		return $jieqiLang['article']['draft_add_failure'];
	}else{
		$draftid = intval($newDraft->getVar('draftid'));
		// 判断是否加水印
		$make_image_water = false;
		if(function_exists('gd_info') && $jieqiConfigs['article']['attachwater'] > 0 && JIEQI_MODULE_VTYPE != '' && JIEQI_MODULE_VTYPE != 'Free'){
			if(strpos($jieqiConfigs['article']['attachwimage'], '/') === false && strpos($jieqiConfigs['article']['attachwimage'], '\\') === false) $water_image_file = $jieqiModules['article']['path'] . '/images/' . $jieqiConfigs['article']['attachwimage'];
			else $water_image_file = $jieqiConfigs['article']['attachwimage'];
			if(is_file($water_image_file)){
				$make_image_water = true;
				include_once(JIEQI_ROOT_PATH . '/lib/image/imagewater.php');
			}
		}
		// 处理上传文件
		if($attachnum > 0){
			$attachs_handler->execute("UPDATE " . jieqi_dbprefix('article_attachs') . " SET chapterid = -" . $draftid . " WHERE articleid=" . $postvars['articleid'] . " AND chapterid = 0");
			$attachdir = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article');
			$attachdir .= jieqi_getsubdir($postvars['articleid']);
			$attachdir .= '/' . $postvars['articleid'];
			$attachdir .= '/0' . $draftid;
			jieqi_checkdir($attachdir, true);
			foreach($attachvars['info'] as $k => $v){
				$fileid = $attachvars['id'][$k];
				$attach_save_path = $attachdir . '/' . $v['attachid'] . '.' . $v['postfix'];
				$tmp_attachfile = $attachdir . '/' . basename($_FILES['attachfile']['tmp_name'][$fileid]) . '.' . $v['postfix'];
				@move_uploaded_file($_FILES['attachfile']['tmp_name'][$fileid], $tmp_attachfile);
				// 图片加水印
				if($make_image_water && preg_match("/\.(gif|jpg|jpeg|png)$/i", $tmp_attachfile)){
					$img = new ImageWater();
					$img->save_image_file = $tmp_attachfile;
					$img->codepage = JIEQI_SYSTEM_CHARSET;
					$img->wm_image_pos = $jieqiConfigs['article']['attachwater'];
					$img->wm_image_name = $water_image_file;
					$img->wm_image_transition = $jieqiConfigs['article']['attachwtrans'];
					$img->jpeg_quality = $jieqiConfigs['article']['attachwquality'];
					$img->create($tmp_attachfile);
					unset($img);
				}
				jieqi_copyfile($tmp_attachfile, $attach_save_path, 0777, true);
			}
		}
	}
	return $draftid;
}

// 小说更新后，触发更新信息
function jieqi_article_updateinfo($article = 0, $act = 'chapternew'){
	global $jieqiTpl;
	global $jieqiModules;
	global $jieqiConfigs;
	global $jieqiArticleuplog;
	global $article_handler;
	global $chapter_handler;

	//$article = 0 更新全部 -1 更新免费章节 -2 更新vip章节
	if(is_numeric($article) && $article <= 0){
		jieqi_getcachevars('article', 'articleuplog');
		if(!is_array($jieqiArticleuplog)) $jieqiArticleuplog = array('articleuptime' => 0, 'vipuptime' => 0, 'chapteruptime' => 0);
		$jieqiArticleuplog['articleuptime'] = JIEQI_NOW_TIME;
		if($article == 0 || $article == -1) $jieqiArticleuplog['chapteruptime'] = JIEQI_NOW_TIME;
		if($article == 0 || $article == -2) $jieqiArticleuplog['vipuptime'] = JIEQI_NOW_TIME;
		jieqi_setcachevars('articleuplog', 'jieqiArticleuplog', $jieqiArticleuplog, 'article');
		return true;
	}

	if(!is_object($article)){
		$article = $article_handler->get(intval($article));
		if(!$article) return false;
	}
	$aid = intval($article->getVar('articleid'));

	//隐藏作品时候删除静态信息页和阅读页
	if($act == 'articlehide'){
		// 清空本小说缓存
		if(JIEQI_USE_CACHE){
			if(!isset($jieqiTpl) || !is_a($jieqiTpl, 'JieqiTpl')){
				include_once(JIEQI_ROOT_PATH . '/lib/template/template.php');
				$jieqiTpl = JieqiTpl::getInstance();
			}
			$jieqiTpl->clear_cache($jieqiModules['article']['path'] . '/templates/articleinfo.html', intval($article->getVar('articleid')));
		}
		if($jieqiConfigs['article']['fakestatic'] > 0){
			include_once($jieqiModules['article']['path'] . '/include/funstatic.php');
			article_delete_sinfo($aid, false);
		}
		if($jieqiConfigs['article']['makehtml'] > 0){
			$htmldir = jieqi_uploadpath($jieqiConfigs['article']['htmldir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid;
			jieqi_article_filehide($htmldir, true);
		}
		if($jieqiConfigs['article']['makefull'] > 0){
			$htmlfile = jieqi_uploadpath($jieqiConfigs['article']['fulldir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid . $jieqiConfigs['article']['htmlfile'];
			jieqi_article_filehide($htmlfile, true);
		}
	}
	if(in_array($act, array('articlehide', 'articleshow'))){
		include_once($jieqiModules['article']['path'] . '/include/repack.php');
		article_repack($aid, array('makeopf' => 1), 0);
	}

	if($article->getVar('display') != '0') return true;

	$uparticle = false;
	$upchapter = false;
	$upstatic = '';
	switch($act){
		case 'articlenew':
			$uparticle = true;
			$upchapter = false;
			$upstatic = 'articlenew';
			break;
		case 'articleedit':
			$uparticle = true;
			$upchapter = false;
			$upstatic = 'articleedit';
			break;
		case 'articlehide':
			$uparticle = true;
			$upchapter = false;
			$upstatic = '';
			break;
		case 'articleshow':
			$uparticle = true;
			$upchapter = false;
			$upstatic = 'articleedit';
			break;
		case 'articledel':
			$uparticle = true;
			$upchapter = true;
			$upstatic = 'articledel';
			break;
		case 'chapternew':
			$uparticle = false;
			$upchapter = true;
			$upstatic = 'chapternew';
			break;
		case 'chapteredit':
			$uparticle = false;
			$upchapter = true;
			$upstatic = 'chapteredit';
			break;
		case 'chapterdel':
			$uparticle = false;
			$upchapter = true;
			$upstatic = 'chapterdel';
			break;
		case 'reviewnew':
			$uparticle = false;
			$upchapter = false;
			$upstatic = 'reviewnew';
			break;
	}

	// 更新最新小说
	if($uparticle == true || $upchapter == true){
		jieqi_getcachevars('article', 'articleuplog');
		if(!is_array($jieqiArticleuplog)) $jieqiArticleuplog = array('articleuptime' => 0, 'chapteruptime' => 0);
		if($uparticle) $jieqiArticleuplog['articleuptime'] = JIEQI_NOW_TIME;
		if($upchapter) $jieqiArticleuplog['chapteruptime'] = JIEQI_NOW_TIME;
		jieqi_setcachevars('articleuplog', 'jieqiArticleuplog', $jieqiArticleuplog, 'article');
	}

	// 清空本小说缓存
	if(JIEQI_USE_CACHE){
		if(!isset($jieqiTpl) || !is_a($jieqiTpl, 'JieqiTpl')){
			include_once(JIEQI_ROOT_PATH . '/lib/template/template.php');
			$jieqiTpl = JieqiTpl::getInstance();
		}
		$jieqiTpl->clear_cache($jieqiModules['article']['path'] . '/templates/articleinfo.html', intval($article->getVar('articleid')));
	}

	// 更新静态页
	if($upstatic != '' && $jieqiConfigs['article']['fakestatic'] > 0){
		include_once($jieqiModules['article']['path'] . '/include/funstatic.php');
		article_update_static($upstatic, intval($article->getVar('articleid')), intval($article->getVar('sortid')));
	}
	//更新静态阅读页
	if($act == 'articleshow'){
		if($jieqiConfigs['article']['makehtml'] > 0){
			$htmldir = jieqi_uploadpath($jieqiConfigs['article']['htmldir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid;
			if(!jieqi_article_filehide($htmldir, false)){
				include_once($jieqiModules['article']['path'] . '/include/repack.php');
				article_repack($aid, array('makehtml' => 1), 0);
			}
		}
		if($jieqiConfigs['article']['makefull'] > 0){
			$htmlfile = jieqi_uploadpath($jieqiConfigs['article']['fulldir'], 'article') . jieqi_getsubdir($aid) . '/' . $aid . $jieqiConfigs['article']['htmlfile'];
			if(!jieqi_article_filehide($htmlfile, false)){
				include_once($jieqiModules['article']['path'] . '/include/repack.php');
				article_repack($aid, array('makefull' => 1), 0);
			}
		}
	}
}

//隐藏或显示文件及目录，方法是文件名前面加 .
function jieqi_article_filehide($file, $hide = true){
	$hidechar = '.';
	$dirname = dirname($file);
	$basename = basename($file);
	$hidefile = $dirname . '/' . $hidechar . $basename;
	if($hide){
		//隐藏
		if(!file_exists($file)) return false;
		$ret = true;
		if(is_file($hidefile)) $ret = jieqi_delfile($hidefile);
		elseif(is_dir($hidefile)) $ret = jieqi_delfolder($hidefile, true);
		if($ret){
			return rename($file, $hidefile);
		}
		else{
			if(is_file($file)) jieqi_delfile($file);
			elseif(is_dir($file)) jieqi_delfolder($file, true);
			return true;
		}
	}
	else{
		//显示
		if(!file_exists($hidefile)){
			if(file_exists($file)) return true;
			else return false;
		}
		$ret = true;
		if(is_file($file)) $ret = jieqi_delfile($file);
		elseif(is_dir($file)) $ret = jieqi_delfolder($file, true);
		if($ret){
			return rename($hidefile, $file);
		}
		else{
			if(is_file($hidefile)) jieqi_delfile($hidefile);
			elseif(is_dir($hidefile)) jieqi_delfolder($hidefile, true);
			return true;
		}
	}
}

//批量更新opf里面的小说信息，比如display
function jieqi_article_updateopf($aids, $fields){
	global $jieqiConfigs;
	global $jieqi_file_postfix;

	if(!is_array($aids)) $aids = array($aids);
	if(empty($aids) || empty($fields)) return true;
	$repfrom = array();
	$repto = array();
	foreach($fields as $k => $v){
		$repfrom[] = '/<'.preg_quote($k).'>[^<>]*<\/'.preg_quote($k).'>/i';
		$repto[] = "<{$k}>{$v}</$k>";
	}

	$opfdir = jieqi_uploadpath($jieqiConfigs['article'][opfdir], 'article');
	foreach($aids as $aid){
		$aid = intval($aid);
		if($aid > 0){
			$opffile = $opfdir . jieqi_getsubdir($aid) . '/' . $aid . '/index' . $jieqi_file_postfix['opf'];
			if(is_file($opffile)){
				$opfdata = jieqi_readfile($opffile);
				$opfdata = preg_replace($repfrom, $repto, $opfdata);
				jieqi_writefile($opffile, $opfdata);
			}
		}
	}
}
?>