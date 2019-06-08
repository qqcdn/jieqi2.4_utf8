<?php
/**
 * 小说编辑
 *
 * 编辑一篇小说信息
 *
 * 调用模板：无
 *
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: articleedit.php 339 2009-06-23 03:03:24Z juny $
 */

define('JIEQI_MODULE_NAME', 'article');
require_once('../../global.php');
if(empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) jieqi_printfail(LANG_ERROR_PARAMETER);
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_loadlang('article', JIEQI_MODULE_NAME);
include_once($jieqiModules['article']['path'].'/class/article.php');
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if(!$article) jieqi_printfail($jieqiLang['article']['article_not_exists']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
//管理别人小说权限
$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
//转载小说权限
$allowtrans = jieqi_checkpower($jieqiPower['article']['transarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$canedit = $ismanager;
if(!$canedit && !empty($_SESSION['jieqiUserId'])){
	//除了斑竹，作者、发表者和代理人可以修改小说
	if($_SESSION['jieqiUserId'] > 0 && ($article->getVar('authorid') == $_SESSION['jieqiUserId'] || $article->getVar('posterid') == $_SESSION['jieqiUserId'] || $article->getVar('agentid') == $_SESSION['jieqiUserId'])){
		$canedit = true;
	}
}
if(!$canedit) jieqi_printfail($jieqiLang['article']['noper_edit_article']);
//载入作者编辑规则函数
jieqi_getconfigs('article', 'rule');
$actrule = true;
if(function_exists('jieqi_rule_article_articleedit')){
	$actrule = jieqi_rule_article_articleedit($article);
	if($actrule === false) jieqi_printfail($jieqiLang['article']['deny_edit_article']);
}
//禁止编辑字段
$denyfields = is_array($actrule) ? $actrule : array();
//是否允许修改点击统计
$allowmodify = jieqi_checkpower($jieqiPower['article']['articlemodify'], $jieqiUsersStatus, $jieqiUsersGroup, true);

if(!isset($_POST['act'])) $_POST['act'] = 'edit';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs', 'jieqiConfigs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
jieqi_getconfigs('system', 'sites', 'jieqiSites');
if(!is_numeric($jieqiConfigs['article']['eachlinknum'])) $jieqiConfigs['article']['eachlinknum'] = 0;else $jieqiConfigs['article']['eachlinknum'] = intval($jieqiConfigs['article']['eachlinknum']);
$article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
switch($_POST['act']){
	case 'update':
		jieqi_checkpost(); //POST数据验证
		//禁止编辑的字段保持原样
		if(!empty($denyfields)){
			foreach($denyfields as $k=>$v){
				if(isset($_POST[$k])) $_POST[$k] = $article->getVar($k, 'n');
			}
		}
		include_once ($jieqiModules['article']['path'] . '/include/actarticle.php');
		$options = array('action' => 'edit', 'ismanager' => $ismanager, 'allowtrans' => $allowtrans, 'allowmodify' => $allowmodify);
		$errors = jieqi_article_articlepcheck($_POST, $options, $article);

		if(empty($errors)){
			$article = jieqi_article_articleadd($_POST, $options, $article);
			if(is_object($article)){
				$id = intval($article->getVar('articleid', 'n'));
				//清空本小说缓存
				if(JIEQI_USE_CACHE){
					if(!isset($jieqiTpl) || !is_a($jieqiTpl, 'JieqiTpl')){
						include_once(JIEQI_ROOT_PATH.'/lib/template/template.php');
						$jieqiTpl = JieqiTpl::getInstance();
					}
					$jieqiTpl->clear_cache($jieqiModules['article']['path'].'/templates/articleinfo.html', $id);
				}
				//更新静态页
				if($jieqiConfigs['article']['fakestatic'] > 0){
					include_once($jieqiModules['article']['path'].'/include/funstatic.php');
					article_update_static('articleedit', $id, $article->getVar('sortid', 'n'));
				}
				jieqi_jumppage($article_static_url.'/articlemanage.php?id='.$id, LANG_DO_SUCCESS, $jieqiLang['article']['article_edit_success']);
			}else{
				jieqi_printfail($article);
			}
		}else{
			jieqi_printfail(implode('<br />', $errors));
		}
		break;
	case 'edit':
	default:
		include_once(JIEQI_ROOT_PATH.'/header.php');
		include_once($jieqiModules['article']['path'].'/include/funarticle.php');
		$jieqiTpl->assign('article_static_url', $article_static_url);
		$jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
		$jieqiTpl->assign('url_articleedit', $article_static_url.'/articleedit.php?do=submit');
		//分类配置
		jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
		$jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));

		foreach($jieqiOption['article'] as $k => $v){
			$jieqiTpl->assign($k, $jieqiOption['article'][$k]);
		}
		//小说编辑信息
		$articlevals = jieqi_article_vars($article, true, 'e');
		//是否授权给作者
		if($article->getVar('authorid') > 0) $articlevals['authorflag'] = 1;else $articlevals['authorflag'] = 0;
		//互换链接信息
		$jieqiTpl->assign('eachlinknum', $jieqiConfigs['article']['eachlinknum']);
		if($jieqiConfigs['article']['eachlinknum'] > 0){
			$setting = jieqi_unserialize($article->getVar('setting', 'n'));
			if(!empty($setting['eachlink']['ids'])) $articlevals['eachlinkids'] = implode(' ', $setting['eachlink']['ids']);else $articlevals['eachlinkids'] = '';
		}
		$jieqiTpl->assign_by_ref('articlevals', $articlevals);
		//标签选择
		if(floatval(JIEQI_VERSION) >= 2){
			include_once(JIEQI_ROOT_PATH.'/include/funtag.php');
			$oldtags = jieqi_tag_clean($article->getVar('keywords', 'n'));
			$jieqiTpl->assign('taglimit', intval($jieqiConfigs['article']['taglimit']));
			$tagwords = array();
			$tmpary = preg_split('/\s+/s', $jieqiConfigs['article']['tagwords']);
			$k = 0;
			foreach($tmpary as $v){
				if(strlen($v) > 0){
					$tagwords[$k]['name'] = jieqi_htmlstr($v);
					$tagwords[$k]['use'] = in_array($v, $oldtags) ? 1 : 0;
					$k++;
				}
			}
			$jieqiTpl->assign_by_ref('tagwords', $tagwords);
			$jieqiTpl->assign('tagnum', count($tagwords));
		}

		//封面图片格式
		$jieqiTpl->assign('imagetype', $jieqiConfigs['article']['imagetype']);
		//是否允许转载
		$jieqiTpl->assign('allowtrans', intval($allowtrans));
		//是否允许修改点击统计
		$jieqiTpl->assign('allowmodify', intval($allowmodify));
		//是否管理权限
		$jieqiTpl->assign('ismanager', intval($ismanager));
		//是否开启征文比赛
		$ismatch = empty($jieqiConfigs['article']['ismatch']) ? 0 : 1;
		$jieqiTpl->assign('ismatch', $ismatch);
		//是否允许整本订阅
		$wholebuy = empty($jieqiConfigs['article']['wholebuy']) ? 0 : intval($jieqiConfigs['article']['wholebuy']);
		$jieqiTpl->assign('wholebuy', $wholebuy);
		//来源网站
		if(floatval(JIEQI_VERSION) >= 2){
			$customsites = array();
			foreach($jieqiSites as $k => $v){
				if(!empty($v['custom'])) $customsites[$k] = $v;
			}
			$jieqiTpl->assign('customsites', jieqi_funtoarray('jieqi_htmlstr', $customsites));
			$jieqiTpl->assign('customsitenum', count($customsites));
			$jieqiTpl->assign('jieqisites', jieqi_funtoarray('jieqi_htmlstr', $jieqiSites));
		}

		//是否作家栏目
		$jieqiTpl->assign('authorarea', 1);
		//禁止编辑字段
		$jieqiTpl->assign('denyfields', $denyfields);

		$jieqiTpl->setCaching(0);
		$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'].'/templates/articleedit.html';
		include_once(JIEQI_ROOT_PATH.'/footer.php');
		break;
}

?>