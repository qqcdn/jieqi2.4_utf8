<?php
/**
 * 允许模板使用的函数
 *
 * 允许模板使用的函数，函数名必须 jieqi_tpl_ 开头
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: funurl.php 230 2008-11-27 08:46:07Z juny $
 */

//需要载入参数设置
global $jieqiConfigs;
global $article_dynamic_url;
global $article_static_url;

if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
if(!isset($article_static_url)) $article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
if(!isset($article_dynamic_url)) $article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
/**
 * 根据小说ID获得信息页面url
 *
 * @param int $aid
 *        	小说id
 * @param string $type
 *        	显示类型
 * @access public
 * @return string
 */
function jieqi_url_article_article($aid, $type = '', $acode = '', $page = 1, $order = ''){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(empty($acode)) $acode = $aid;
	switch($type){
		case 'index':
			//$page = 0 保留伪静态标签，用于翻页
			if(!empty($page)){
				$page = intval($page);
				if($page < 1) $page = 1;
			}
			if(!in_array($order, array('', '0', '1', 'asc', 'desc'))) $order = '';
			//默认的动态url
			$url_reader = $article_static_url . '/reader.php?aid=' . $aid;
			if(!empty($order)) $url_reader .= '&order=' . $order;
			if(empty($page)) $url_reader .= '&page=';
			elseif(!empty($page) && $page > 1) $url_reader .= '&page=' . $page;
			//使用url_rewrite
			if(!empty($jieqiConfigs['article']['fakearticle']) || (!empty($jieqiConfigs['article']['htmlurl']) && strpos($jieqiConfigs['article']['htmlurl'], '<{$aid}>') != false)){
				if(empty($jieqiConfigs['article']['fakearticle'])) $jieqiConfigs['article']['fakearticle'] = $jieqiConfigs['article']['htmlurl'];
				if($jieqiConfigs['article']['makehtml'] > 0 && JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR') && strpos($jieqiConfigs['article']['fakearticle'], '<{$newset}>') === false){
					return $url_reader;
				}else{
					if(JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) $newset = JIEQI_CHAR_SET;
					else $newset = '';
					$orderval = ($order == 'desc' || $order == 1) ? 'desc' : 'asc';
					$isdesc = ($order == 'desc' || $order == 1) ? '1' : '';
					$repfrom = array(
							'<{$jieqi_url}>',
							'<{$aid}>',
							'<{$aid|subdirectory}>',
							'<{$acode}>',
							'<{$order}>',
							'<{$order_s}>',
							'<{$order_f}>',
							'<{$isdesc}>',
							'<{$newset}>'
					);
					$repto = array(
							JIEQI_URL,
							$aid,
							jieqi_getsubdir($aid),
							$acode,
							$orderval,
							$order,
							$orderval,
							$isdesc,
							$newset
					);
					if(!empty($page)){
						$repfrom[] = '<{$page|subdirectory}>';
						$repfrom[] = '<{$page}>';
						$repto[] = jieqi_getsubdir($page);
						$repto[] = $page;
					}
					$ret = str_replace($repfrom, $repto, $jieqiConfigs['article']['fakearticle']);
					if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
					return $ret;
				}
			}elseif($jieqiConfigs['article']['makehtml'] == 0 || (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR'))){
				return $url_reader;
			}else{
				if(empty($page)) return jieqi_uploadurl($jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/index' . $order . '_<{$page}>' . $jieqiConfigs['article']['htmlfile'];
				elseif(!empty($page) && $page > 1) return jieqi_uploadurl($jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/index' . $order . '_' . $page . $jieqiConfigs['article']['htmlfile'];
				else return jieqi_uploadurl($jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/index' . $order . $jieqiConfigs['article']['htmlfile'];
			}
			break;
		case 'full':
			if($jieqiConfigs['article']['makefull'] == 0 || (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR'))){
				$ret = $article_static_url . '/reader.php?aid=' . $aid;
			}else{
				$ret = jieqi_uploadurl($jieqiConfigs['article']['fulldir'], $jieqiConfigs['article']['fullurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . $jieqiConfigs['article']['htmlfile'];
			}
			return $ret;
			break;
		case 'info':
		default:
			if(!empty($jieqiConfigs['article']['fakeinfo'])){
				$repfrom = array(
						'<{$jieqi_url}>',
						'<{$id|subdirectory}>',
						'<{$id}>',
						'<{$acode}>'
				);
				$repto = array(
						JIEQI_URL,
						jieqi_getsubdir($aid),
						$aid,
						$acode
				);
				$ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinfo']));
				if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
				return $ret;
			}else{
				return $article_dynamic_url . '/articleinfo.php?id=' . $aid;
			}
			break;
	}
}

/**
 * 根据小说ID获得小说封面图片url
 *
 * @param int $aid
 *        	小说id
 * @param string $type
 *        	显示类型 s - 小图， l - 大图
 * @param int $flag
 *        	图片类型标志 -1 则自动判断
 * @access public
 * @return string
 */
function jieqi_url_article_cover($aid, $type = 's', $flag = -1){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	$nocover = $article_static_url . '/images/nocover.jpg';
	if($flag < 0){
		global $article;
		if(!isset($article) || !is_a($article, 'JieqiArticle')){
			include_once ($GLOBALS['jieqiModules']['article']['path'] . '/class/article.php');
			$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
			$article = $article_handler->get($aid);
			if(is_object($article)) $flag = $article->getVar('imgflag', 'n');
		}
	}
	$flag = intval($flag);
	if($flag <= 0) return $nocover;
	
	$imageinfo = array(
			'stype' => '',
			'ltype' => ''
	);
	if(($flag & 1) > 0) $imageinfo['stype'] = $jieqiConfigs['article']['imagetype'];
	if(($flag & 2) > 0) $imageinfo['ltype'] = $jieqiConfigs['article']['imagetype'];
	$imgtype = $flag >> 2;
	if($imgtype > 0){
		$imgtary = array(
				1 => '.gif',
				2 => '.jpg',
				3 => '.jpeg',
				4 => '.png',
				5 => '.bmp'
		);
		$tmpvar = round($imgtype & 7);
		if(isset($imgtary[$tmpvar])) $imageinfo['stype'] = $imgtary[$tmpvar];
		$tmpvar = round($imgtype >> 3);
		if(isset($imgtary[$tmpvar])) $imageinfo['ltype'] = $imgtary[$tmpvar];
	}
	
	switch($type){
		case 'l':
			if(!empty($imageinfo['ltype'])){
				return jieqi_uploadurl($jieqiConfigs['article']['imagedir'], $jieqiConfigs['article']['imageurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $aid . 'l' . $imageinfo['ltype'];
			}elseif(!empty($imageinfo['stype'])){
				return jieqi_uploadurl($jieqiConfigs['article']['imagedir'], $jieqiConfigs['article']['imageurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $aid . 's' . $imageinfo['stype'];
			}else{
				return '';
			}
			break;
		case 's':
		default:
			if(!empty($imageinfo['stype'])){
				return jieqi_uploadurl($jieqiConfigs['article']['imagedir'], $jieqiConfigs['article']['imageurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $aid . 's' . $imageinfo['stype'];
			}else{
				return $nocover;
			}
			break;
	}
}

/**
 * 根据小说ID获得目录页面
 *
 * @param int $aid
 *        	小说id
 * @param string $acode
 *        	小说代码
 * @param int $page
 *        	页码
 * @access public
 * @return string
 */
function jieqi_url_article_index($aid, $acode = '', $page = 1, $order = ''){
	return jieqi_url_article_article($aid, 'index', $acode, $page, $order);
}

/**
 * 根据章节和小说ID获得章节阅读页面
 *
 * @param int $cid
 *        	章节id
 * @param int $aid
 *        	小说id
 * @param int $isvip
 *        	是否vip章节
 * @access public
 * @return string
 */
function jieqi_url_article_chapter($cid, $aid, $isvip = 0, $acode = ''){
	global $jieqiConfigs;
	global $jieqiModules;
	global $article_dynamic_url;
	global $article_static_url;
	if($isvip > 0){
		if(!isset($jieqiConfigs['obook'])) jieqi_getconfigs('obook', 'configs', 'jieqiConfigs');
		if(!empty($jieqiConfigs['obook']['fakechapter'])){
			if(JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) $newset = JIEQI_CHAR_SET;
			else $newset = '';
			$repfrom = array(
				'<{$jieqi_url}>',
				'<{$aid}>',
				'<{$cid}>',
				'<{$aid|subdirectory}>',
				'<{$cid|subdirectory}>',
				'<{$acode}>',
				'<{$newset}>'
			);
			$repto = array(
				JIEQI_URL,
				$aid,
				$cid,
				jieqi_getsubdir($aid),
				jieqi_getsubdir($cid),
				$acode,
				$newset
			);
			$ret = str_replace($repfrom, $repto, $jieqiConfigs['obook']['fakechapter']);
			if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
			return $ret;
		}else{
			return $jieqiModules['obook']['url'] . '/reader.php?cid=' . $cid . '&aid=' . $aid;
		}
	}
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(empty($acode)) $acode = $aid;
	//使用url_rewrite
	if(!empty($jieqiConfigs['article']['fakechapter']) || (!empty($jieqiConfigs['article']['htmlurl']) && strpos($jieqiConfigs['article']['htmlurl'], '<{$aid}>') != false)){
		if(empty($jieqiConfigs['article']['fakechapter'])){
			$jieqiConfigs['article']['fakechapter'] = $jieqiConfigs['article']['htmlurl'];
			if(strpos($jieqiConfigs['article']['fakechapter'], '<{$cid}>') === false){
				$jieqiConfigs['article']['fakechapter'] = str_replace(array(
						'index',
						$jieqiConfigs['article']['htmlfile']
				), '', $jieqiConfigs['article']['fakechapter']);
				if(substr($jieqiConfigs['article']['fakechapter'], -1) != '/') $jieqiConfigs['article']['fakechapter'] .= '/';
				$jieqiConfigs['article']['fakechapter'] .= '<{$cid}>' . $jieqiConfigs['article']['htmlfile'];
			}
		}
		if($jieqiConfigs['article']['makehtml'] > 0 && JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR') && strpos($jieqiConfigs['article']['fakechapter'], '<{$newset}>') === false){
			return $article_static_url . '/reader.php?aid=' . $aid . '&cid=' . $cid;
		}else{
			if(JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) $newset = JIEQI_CHAR_SET;
			else $newset = '';
			$repfrom = array(
					'<{$jieqi_url}>',
					'<{$aid}>',
					'<{$cid}>',
					'<{$aid|subdirectory}>',
					'<{$cid|subdirectory}>',
					'<{$acode}>',
					'<{$newset}>'
			);
			$repto = array(
					JIEQI_URL,
					$aid,
					$cid,
					jieqi_getsubdir($aid),
					jieqi_getsubdir($cid),
					$acode,
					$newset
			);
			$ret = str_replace($repfrom, $repto, $jieqiConfigs['article']['fakechapter']);
			if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
			return $ret;
		}
	}elseif($jieqiConfigs['article']['makehtml'] == 0 || (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR'))){
		return $article_static_url . '/reader.php?aid=' . $aid . '&cid=' . $cid;
	}else{
		return jieqi_uploadurl($jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $cid . $jieqiConfigs['article']['htmlfile'];
	}
}

/**
 * 条件筛选列表
 *
 * @param int $page
 *        	页码
 * @param string $sort
 *        	排行类型
 * @access public
 * @return string
 */
function jieqi_url_article_articlefilter($page = 1, $filter = array()){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	global $jieqiSort;
	global $jieqiOption;
	global $jieqiFilter;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(!isset($jieqiSort['article'])) jieqi_getconfigs('article', 'sort', 'jieqiSort');
	if(!isset($jieqiOption['article'])) jieqi_getconfigs('article', 'option', 'jieqiOption');
	if(!isset($jieqiFilter['article'])) jieqi_getconfigs('article', 'filter', 'jieqiFilter');
	
	//$page = 0 保留伪静态标签，用于翻页
	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}
	
	$vars = array();
	if(!empty($filter['sortid']) && isset($jieqiSort['article'][$filter['sortid']])) $vars['sortid'] = $filter['sortid'];
	if(!empty($filter['typeid']) && isset($jieqiSort['article'][$filter['sortid']]['types'][$filter['typeid']])) $vars['typeid'] = $filter['typeid'];
	if(isset($filter['initial']) && preg_match('/^[A-Z01]$/i', $filter['initial'])) $vars['initial'] = $filter['initial'];
	if(isset($filter['order']) && isset($jieqiFilter['article']['order'][$filter['order']])) $vars['order'] = $filter['order'];
	if(isset($filter['words']) && isset($jieqiFilter['article']['words'][$filter['words']])) $vars['words'] = $filter['words'];
	if(isset($filter['update']) && isset($jieqiFilter['article']['update'][$filter['update']])) $vars['update'] = $filter['update'];
	if(isset($filter['original']) && isset($jieqiFilter['article']['original'][$filter['original']])) $vars['original'] = $filter['original'];
	if(isset($filter['progress']) && isset($jieqiOption['article']['progress']['items'][$filter['progress']])) $vars['progress'] = $filter['progress'];
	if(!isset($filter['isfull']) && !empty($filter['fullflag'])){
		end($jieqiFilter['article']['isfull']);
		$maxid = key($jieqiFilter['article']['isfull']);
		if($filter['fullflag'] == 1){
			$filter['isfull'] = $maxid < 10 ? $maxid : 10;
		}elseif($filter['fullflag'] == 2){
			if($maxid >= 11) $filter['isfull'] = 11;
		}
	}
	if(isset($filter['isfull']) && isset($jieqiFilter['article']['isfull'][$filter['isfull']])) $vars['isfull'] = $filter['isfull'];
	if(isset($filter['isvip']) && isset($jieqiFilter['article']['isvip'][$filter['isvip']])) $vars['isvip'] = $filter['isvip'];
	if(isset($filter['rgroup']) && isset($jieqiFilter['article']['rgroup'][$filter['rgroup']])) $vars['rgroup'] = $filter['rgroup'];
	
	if(!empty($jieqiConfigs['article']['fakefilter'])){
		$repfrom = array(
				'<{$jieqi_url}>'
		);
		$repto = array(
				JIEQI_URL
		);
		foreach($vars as $k => $v){
			$repfrom[] = '<{$'.$k.'}>';
			$repto[] = empty($v) ? 0 : $v;
		}
		if(!empty($page)){
			$repfrom[] = '<{$page|subdirectory}>';
			$repfrom[] = '<{$page}>';
			$repto[] = jieqi_getsubdir($page);
			$repto[] = $page;
		}
		$ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakefilter']));
		$ret = str_replace(array('<{$sortid}>', '<{$typeid}>', '<{$initial}>', '<{$order}>', '<{$words}>', '<{$update}>', '<{$original}>', '<{$progress}>', '<{$isfull}>', '<{$isvip}>', '<{$rgroup}>'), '0', $ret);
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
		return $ret;
	}else{
		if(!empty($page)) $vars['page'] = $page;
		$ret = $article_dynamic_url . '/articlefilter.php';
		if(count($vars) > 0){
			ksort($vars);
			$qstr = '';
			foreach($vars as $k=>$v){
				$qstr .= empty($qstr) ? '?' : '&';
				$qstr .= urlencode($k) . '=' . urlencode($v);
			}
			$ret .= $qstr;
		}
		if(empty($page)) $ret .= ($ret == '') ? '?page=' : '&page=';
		return $ret;
	}
}

/**
 * 显示排行榜url
 *
 * @param int $page
 *        	页码
 * @param string $sort
 *        	排行类型
 * @access public
 * @return string
 */
function jieqi_url_article_toplist($page = 1, $order = '', $sortid = '', $fullflag = ''){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	global $jieqiSort;
	global $jieqiTop;
	if(!isset($jieqiTop['article'])) jieqi_getconfigs('article', 'top');
	
	if(!isset($jieqiTop['article'][$order])) $order = 'allvisit';
	
	//$page = 0 保留伪静态标签，用于翻页
	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}
	
	$use_sortcode = false;
	if(!empty($sortid)){
		if(!isset($jieqiSort['article'])) jieqi_getconfigs('article', 'sort', 'jieqiSort');
		if(is_numeric($sortid)){
			if(!isset($jieqiSort['article'][$sortid])) $sortid = '';
		}else{
			foreach($jieqiSort['article'] as $k => $v){
				if(isset($v['code']) && $v['code'] == $sortid){
					$sortid = intval($k);
					$use_sortcode = true;
					break;
				}
			}
		}
	}
	if(empty($sortid)){
		$sortid = '';
		$sortcode = '';
	}else{
		$sortcode = isset($jieqiSort['article'][$sortid]['code']) ? $jieqiSort['article'][$sortid]['code'] : '';
	}
	
	if(!empty($fullflag)) $fullflag = 1;
	if(empty($fullflag)) $fullflag = '';
	
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	$use_fake = false;
	if(!empty($fullflag) && !empty($jieqiConfigs['article']['fakefulltop'])) $fake_rule = $jieqiConfigs['article']['fakefulltop'];
	else $fake_rule = empty($jieqiConfigs['article']['faketoplist']) ? '' : $jieqiConfigs['article']['faketoplist'];
	if(!empty($fake_rule)){
		if(!empty($sortid) && strpos($fake_rule, '<{$sortid}>') === false && strpos($fake_rule, '<{$class}>') === false && strpos($fake_rule, '<{$sortcode}>') === false){
		}elseif(!empty($fullflag) && empty($jieqiConfigs['article']['fakefulltop']) && strpos($fake_rule, '<{$fullflag}>') === false){
		}else{
			$use_fake = true;
			$sortid = intval($sortid);
			$fullflag = intval($fullflag);
			if($sortcode == '') $sortcode = '0';
			$repfrom = array(
					'<{$jieqi_url}>',
					'<{$order}>',
					'<{$sort}>',
					'<{$sortid}>',
					'<{$class}>',
					'<{$sortcode}>',
					'<{$fullflag}>'
			);
			$repto = array(
					JIEQI_URL,
					$order,
					$order,
					$sortid,
					$sortid,
					$sortcode,
					$fullflag
			);
			
			if(!empty($page)){
				$repfrom[] = '<{$page|subdirectory}>';
				$repfrom[] = '<{$page}>';
				$repto[] = jieqi_getsubdir($page);
				$repto[] = $page;
			}
			$ret = trim(str_replace($repfrom, $repto, $fake_rule));
			if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
			return $ret;
		}
	}
	if(!$use_fake){
		$ret = '';
		if(!empty($order)) $ret .= ($ret == '') ? '?order=' . $order : '&order=' . $order;
		if(!empty($sortid)) $ret .= ($ret == '') ? '?sortid=' . $sortid : '&sortid=' . $sortid;
		if(!empty($fullflag)) $ret .= ($ret == '') ? '?fullflag=' . $fullflag : '&fullflag=' . $fullflag;
		if(!empty($page)) $ret .= ($ret == '') ? '?page=' . $page : '&page=' . $page;
		else $ret .= ($ret == '') ? '?page=' : '&page=';
		$ret = $article_dynamic_url . '/toplist.php' . $ret;
		return $ret;
	}
}

/**
 * 显示分类列表url
 *
 * @param int $page
 *        	页码
 * @param string $class
 *        	类型id
 * @access public
 * @return string
 */
function jieqi_url_article_articlelist($page = 1, $sortid = '', $fullflag = ''){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	global $jieqiSort;
	
	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}
	
	$use_sortcode = false;
	if(!empty($sortid)){
		if(!isset($jieqiSort['article'])) jieqi_getconfigs('article', 'sort', 'jieqiSort');
		if(is_numeric($sortid)){
			if(!isset($jieqiSort['article'][$sortid])) $sortid = '';
		}else{
			foreach($jieqiSort['article'] as $k => $v){
				if(isset($v['code']) && $v['code'] == $sortid){
					$sortid = intval($k);
					$use_sortcode = true;
					break;
				}
			}
		}
	}
	if(empty($sortid)){
		$sortid = '';
		$sortcode = '';
	}else{
		$sortcode = isset($jieqiSort['article'][$sortid]['code']) ? $jieqiSort['article'][$sortid]['code'] : '';
	}
	
	if(!empty($fullflag)) $fullflag = 1;
	if(empty($fullflag)) $fullflag = '';
	
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	$use_fake = false;
	if(!empty($fullflag) && !empty($jieqiConfigs['article']['fakefullsort'])) $fake_rule = $jieqiConfigs['article']['fakefullsort'];
	else $fake_rule = empty($jieqiConfigs['article']['fakesort']) ? '' : $jieqiConfigs['article']['fakesort'];
	if(!empty($fake_rule)){
		if(!empty($sortid) && strpos($fake_rule, '<{$sortid}>') === false && strpos($fake_rule, '<{$class}>') === false && strpos($fake_rule, '<{$sortcode}>') === false){
		}elseif(!empty($fullflag) && empty($jieqiConfigs['article']['fakefullsort']) && strpos($fake_rule, '<{$fullflag}>') === false){
		}else{
			$use_fake = true;
			$sortid = intval($sortid);
			$fullflag = intval($fullflag);
			if($sortcode == '') $sortcode = '0';
			$repfrom = array(
					'<{$jieqi_url}>',
					'<{$sortid}>',
					'<{$class}>',
					'<{$sortcode}>',
					'<{$fullflag}>'
			);
			$repto = array(
					JIEQI_URL,
					$sortid,
					$sortid,
					$sortcode,
					$fullflag
			);
			
			if(!empty($page)){
				$repfrom[] = '<{$page|subdirectory}>';
				$repfrom[] = '<{$page}>';
				$repto[] = jieqi_getsubdir($page);
				$repto[] = $page;
			}
			$ret = trim(str_replace($repfrom, $repto, $fake_rule));
			if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
			return $ret;
		}
	}
	if(!$use_fake){
		$ret = '';
		if(!$use_sortcode){
			if(!empty($sortid)) $ret .= ($ret == '') ? '?sortid=' . $sortid : '&sortid=' . $sortid;
		}else{
			if(!empty($sortcode)) $ret .= ($ret == '') ? '?sortcode=' . $sortcode : '&sortcode=' . $sortcode;
		}
		
		if(!empty($fullflag)) $ret .= ($ret == '') ? '?fullflag=' . $fullflag : '&fullflag=' . $fullflag;
		if(!empty($page)) $ret .= ($ret == '') ? '?page=' . $page : '&page=' . $page;
		else $ret .= ($ret == '') ? '?page=' : '&page=';
		$ret = $article_dynamic_url . '/articlelist.php' . $ret;
		return $ret;
	}
}

/**
 * 显示首字母分类列表url
 *
 * @param int $page
 *        	页码
 * @param string $initial
 *        	首字母
 * @access public
 * @return string
 */
function jieqi_url_article_initial($page = 1, $initial = 'A', $fullflag = ''){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	//$page = 0 保留伪静态标签，用于翻页
	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}
	$initial = strtoupper($initial);
	$initials = array(
			'A',
			'B',
			'C',
			'D',
			'E',
			'F',
			'G',
			'H',
			'I',
			'J',
			'K',
			'L',
			'M',
			'N',
			'O',
			'P',
			'Q',
			'R',
			'S',
			'T',
			'U',
			'V',
			'W',
			'X',
			'Y',
			'Z',
			'1'
	);
	if(!in_array($initial, $initials)) $initial = 'A';
	
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	$use_fake = false;
	if(!empty($jieqiConfigs['article']['fakeinitial'])){
		if(!empty($fullflag) && strpos($jieqiConfigs['article']['fakeinitial'], '<{$fullflag}>') === false){
		}else{
			$use_fake = true;
			$repfrom = array(
					'<{$jieqi_url}>',
					'<{$initial}>',
					'<{$fullflag}>'
			);
			$repto = array(
					JIEQI_URL,
					$initial,
					intval($fullflag)
			);
			if(!empty($page)){
				$repfrom[] = '<{$page|subdirectory}>';
				$repfrom[] = '<{$page}>';
				$repto[] = jieqi_getsubdir($page);
				$repto[] = $page;
			}
			$ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinitial']));
			if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
			return $ret;
		}
	}
	if(!$use_fake){
		$ret = $article_dynamic_url . '/articlelist.php?initial=' . $initial;
		if(!empty($fullflag)) $ret .= '&fullflag=' . $fullflag;
		if(!empty($page)) $ret .= '&page=' . $page;
		else $ret .= '&page=';
		return $ret;
	}
}

/**
 * 根据小说ID获得下载文件url
 *
 * @param int $aid
 *        	小说id
 * @param string $type
 *        	文件类型
 * @param int $direct
 *        	是否直接指向文件，默认否
 * @param string $fname
 *        	是否指定文件名
 * @access public
 * @return string
 */
function jieqi_url_article_down($aid, $type = '', $direct = 0, $fname = ''){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	$setary = array();
	switch($type){
		case 'zip':
			$setary = array(
					'type' => 'zip',
					'fake' => 'fakezip',
					'dir' => 'zipdir',
					'url' => 'zipurl',
					'postfix' => $GLOBALS['jieqi_file_postfix']['zip']
			);
			break;
		case 'txt':
			$setary = array(
					'type' => 'txt',
					'fake' => 'faketxt',
					'dir' => 'txtfulldir',
					'url' => 'txtfullurl',
					'postfix' => $GLOBALS['jieqi_file_postfix']['txt']
			);
			break;
		case 'umd':
			$setary = array(
					'type' => 'umd',
					'fake' => 'fakeumd',
					'dir' => 'umddir',
					'url' => 'umdurl',
					'postfix' => $GLOBALS['jieqi_file_postfix']['umd']
			);
			break;
		case 'jar':
			$setary = array(
					'type' => 'jar',
					'fake' => 'fakejar',
					'dir' => 'jardir',
					'url' => 'jarurl',
					'postfix' => $GLOBALS['jieqi_file_postfix']['jar']
			);
			break;
		case 'jad':
			$setary = array(
					'type' => 'jad',
					'fake' => 'fakejar',
					'dir' => 'jardir',
					'url' => 'jarurl',
					'postfix' => $GLOBALS['jieqi_file_postfix']['jad']
			);
			break;
		default:
			return false;
	}
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(!empty($jieqiConfigs['article'][$setary['fake']]) || (!empty($jieqiConfigs['article'][$setary['url']]) && strpos($jieqiConfigs['article'][$setary['url']], '<{$aid}>') != false)){
		if(empty($jieqiConfigs['article'][$setary['fake']])) $jieqiConfigs['article'][$setary['fake']] = $jieqiConfigs['article'][$setary['url']];
		$repfrom = array(
				'<{$jieqi_url}>',
				'<{$aid}>',
				'<{$aid|subdirectory}>'
		);
		$repto = array(
				JIEQI_URL,
				$aid,
				jieqi_getsubdir($aid)
		);
		$ret = str_replace($repfrom, $repto, $jieqiConfigs['article'][$setary['fake']]);
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
		return $ret;
	}elseif($direct == 0){
		$ret = $article_static_url . '/packdown.php?id=' . $aid . '&type=' . $setary['type'];
		if(!empty($fname)) $ret .= '&fname=' . urlencode($fname);
		return $ret;
	}else{
		return jieqi_uploadurl($jieqiConfigs['article'][$setary['dir']], $jieqiConfigs['article'][$setary['url']], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . $setary['postfix'];
	}
}

/**
 * 根据小说附件url
 *
 * @param int $aid
 *        	小说id
 * @param int $cid
 *        	章节id
 * @param int $tid
 *        	附件id
 * @param string $postfix
 *        	附件后缀名
 * @access public
 * @return string
 */
function jieqi_url_article_attach($aid = 0, $cid = 0, $tid = 0, $postfix = ''){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(!empty($jieqiConfigs['article']['fakeattach']) || (!empty($jieqiConfigs['article']['attachurl']) && strpos($jieqiConfigs['article']['attachurl'], '<{$') != false)){
		if(empty($jieqiConfigs['article']['fakeattach'])) $jieqiConfigs['article']['fakeattach'] = $jieqiConfigs['article']['attachurl'];
		if(JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) $newset = JIEQI_CHAR_SET;
		else $newset = '';
		$repfrom = array(
				'<{$jieqi_url}>',
				'<{$aid}>',
				'<{$cid}>',
				'<{$aid|subdirectory}>',
				'<{$cid|subdirectory}>',
				'<{$newset}>'
		);
		$repto = array(
				JIEQI_URL,
				$aid,
				$cid,
				jieqi_getsubdir($aid),
				jieqi_getsubdir($cid),
				$newset
		);
		$ret = str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeattach']);
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
	}else{
		$ret = jieqi_uploadurl($jieqiConfigs['article']['attachdir'], $jieqiConfigs['article']['attachurl'], 'article');
		if(!empty($aid)) $ret .= jieqi_getsubdir($aid) . '/' . $aid;
		if(!empty($cid)) $ret .= '/' . $cid;
		if(!empty($tid)) $ret .= '/' . $tid;
		if(!empty($postfix)) $ret .= '.' . $postfix;
	}
	return $ret;
}

/**
 * 根据作者id获取作者专栏url
 *
 * @param int $id
 *        	作者id
 * @param string $author
 *        	作者名
 * @access public
 * @return string
 */
function jieqi_url_article_author($id = 0, $author = ''){
	global $jieqiConfigs;
	global $jieqiModules;
	global $article_dynamic_url;
	global $article_static_url;
	
	$id = intval($id);
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(!empty($id) && !empty($jieqiConfigs['article']['fakeauthor'])){
		$repfrom = array(
				'<{$jieqi_url}>',
				'<{$id|subdirectory}>',
				'<{$id}>'
		);
		$repto = array(
				JIEQI_URL,
				jieqi_getsubdir($id),
				$id
		);
		$ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeauthor']));
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
		return $ret;
	}else{
		if(empty($id) && strlen($author) > 0) return $article_dynamic_url . '/authorarticle.php?author=' . urlencode($author);
		else return $article_dynamic_url . '/authorpage.php?id=' . $id;
	}
	
}

/**
 * 显示搜索结果列表url
 *
 * @param int $page
 *        	页码
 * @param string $searchkey
 *        	搜索关键字
 * @param string $searchtype
 *        	搜索类型
 * @access public
 * @return string
 */
function jieqi_url_article_search($page = 1, $searchkey = '', $searchtype = 'articlename'){
	global $jieqiConfigs;
	global $charset_convert_out;
	global $article_dynamic_url;
	global $article_static_url;
	//参数检查
	if(strlen($searchkey) == 0) return $article_dynamic_url . '/search.php';
	$searchkey = empty($charset_convert_out) ? urlencode($searchkey) : urlencode($charset_convert_out($searchkey));
	if(!in_array($searchtype, array('all', 'articlename', 'author', 'keywords'))) $searchtype = 'articlename';

	//$page = 0 保留伪静态标签，用于翻页
	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}

	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(!empty($jieqiConfigs['article']['fakesearch'])){
		$repfrom = array(
			'<{$jieqi_url}>',
			'<{$searchkey}>',
			'<{$searchtype}>'
		);
		$repto = array(
			JIEQI_URL,
			$searchkey,
			$searchtype
		);
		if(!empty($page)){
			$repfrom[] = '<{$page|subdirectory}>';
			$repfrom[] = '<{$page}>';
			$repto[] = jieqi_getsubdir($page);
			$repto[] = $page;
		}
		$ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakesearch']));
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
		return $ret;
	}else{
		$ret = $article_dynamic_url . '/search.php?searchkey=' . $searchkey . '&searchtype=' . $searchtype;
		if(!empty($page)) $ret .= '&page=' . $page;
		else $ret .= '&page=';
		return $ret;
	}
}

/**
 * 显示标签列表url
 *
 * @param int $page
 *        	页码
 * @param string $sort
 *        	排序字段
 * @param string $order
 *        	正序倒序
 * @access public
 * @return string
 */
function jieqi_url_article_taglist($page = 1, $sort = '', $order = 'desc'){
	global $jieqiConfigs;
	global $charset_convert_out;
	global $article_dynamic_url;
	global $article_static_url;
	//参数检查
	$sortary = array('tagid', 'linknum', 'dayvisit', 'weekvisit', 'monthvisit', 'allvisit');
	if(empty($sort) || !in_array($sort, $sortary)) $sort = 'tagid';
	if(strtolower($order) == 'asc') $_REQUEST['order'] = 'asc';
	else $order = 'desc';

	//$page = 0 保留伪静态标签，用于翻页
	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}

	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(!empty($jieqiConfigs['article']['faketaglist'])){
		$repfrom = array(
			'<{$jieqi_url}>',
			'<{$sort}>',
			'<{$order}>'
		);
		$repto = array(
			JIEQI_URL,
			$sort,
			$order
		);
		if(!empty($page)){
			$repfrom[] = '<{$page|subdirectory}>';
			$repfrom[] = '<{$page}>';
			$repto[] = jieqi_getsubdir($page);
			$repto[] = $page;
		}
		$ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['faketaglist']));
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
		return $ret;
	}else{
		$ret = $article_dynamic_url . '/taglist.php?sort=' . $sort;
		if(strtolower($order) == 'asc') $ret .= '&order=asc';
		if(!empty($page)) $ret .= '&page=' . $page;
		else $ret .= '&page=';
		return $ret;
	}
}

/**
 * 显示标签小说列表url
 *
 * @param int $page
 *        	页码
 * @param string $tagname
 *        	标签名
 * @param string $tagid
 *        	标签ID
 * @access public
 * @return string
 */
function jieqi_url_article_tagarticle($page = 1, $tagname = '', $tagid = 0){
	global $jieqiConfigs;
	global $charset_convert_out;
	global $article_dynamic_url;
	global $article_static_url;
	//参数检查
	if(strlen($tagname) == 0 && $tagid == 0) return $article_dynamic_url . '/taglist.php';
	$tagname = empty($charset_convert_out) ? urlencode($tagname) : urlencode($charset_convert_out($tagname));
	$tagid = intval($tagid);

	//$page = 0 保留伪静态标签，用于翻页
	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}

	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
	if(!empty($jieqiConfigs['article']['faketagarticle'])){
		$repfrom = array(
			'<{$jieqi_url}>',
			'<{$tagname}>',
			'<{$tagid}>'
		);
		$repto = array(
			JIEQI_URL,
			$tagname,
			$tagid
		);
		if(!empty($page)){
			$repfrom[] = '<{$page|subdirectory}>';
			$repfrom[] = '<{$page}>';
			$repto[] = jieqi_getsubdir($page);
			$repto[] = $page;
		}
		$ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['faketagarticle']));
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
		return $ret;
	}else{
		$ret = $article_dynamic_url . '/tagarticle.php';
		if(strlen($tagname) > 0) $ret .= '?tag=' . $tagname;
		else $ret .= '?tagid=' . $tagid;
		if(!empty($page)) $ret .= '&page=' . $page;
		else $ret .= '&page=';
		return $ret;
	}
}

/**
 * 书评列表url
 *
 * @param int $page
 *        	页码
 * @param string $class
 *        	类型id
 * @access public
 * @return string
 */
function jieqi_url_article_reviews($page = 1, $aid = 0, $type = ''){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;

	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}

	if($type != 'good') $type = '';
	if(empty($aid) || !is_numeric($aid)) $aid = 0;

	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');

	$fake_rule = empty($jieqiConfigs['article']['fakereviews']) ? '' : $jieqiConfigs['article']['fakereviews'];
	if(!empty($type) && strpos($jieqiConfigs['article']['htmlurl'], '<{$type}>') == false) $fake_rule = '';

	if(!empty($fake_rule)){
			$repfrom = array(
				'<{$jieqi_url}>',
				'<{$aid}>',
				'<{$aid|subdirectory}>',
				'<{$type}>'
			);
			$repto = array(
				JIEQI_URL,
				$aid,
				jieqi_getsubdir($aid),
				$type
			);

			if(!empty($page)){
				$repfrom[] = '<{$page|subdirectory}>';
				$repfrom[] = '<{$page}>';
				$repto[] = jieqi_getsubdir($page);
				$repto[] = $page;
			}
			$ret = trim(str_replace($repfrom, $repto, $fake_rule));
			if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
			return $ret;

	}else{
		$ret = $article_dynamic_url . '/reviews.php?aid=' . $aid;
		if(!empty($type)) $ret .= '&type=' . $type;
		if(!empty($page)) $ret .= '&page=' . $page;
		else $ret .= '&page=';
		return $ret;
	}
}

/**
 * 书评回复url
 *
 * @param int $page
 *        	页码
 * @param string $class
 *        	类型id
 * @access public
 * @return string
 */
function jieqi_url_article_reviewshow($page = 1, $tid = 0){
	global $jieqiConfigs;
	global $article_dynamic_url;
	global $article_static_url;

	if(!empty($page)){
		$page = intval($page);
		if($page < 1) $page = 1;
	}

	if(empty($tid) || !is_numeric($tid)) $tid = 0;

	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs', 'jieqiConfigs');

	$fake_rule = empty($jieqiConfigs['article']['fakereviewshow']) ? '' : $jieqiConfigs['article']['fakereviewshow'];

	if(!empty($fake_rule)){
		$repfrom = array(
			'<{$jieqi_url}>',
			'<{$tid}>',
			'<{$tid|subdirectory}>'
		);
		$repto = array(
			JIEQI_URL,
			$tid,
			jieqi_getsubdir($tid)
		);

		if(!empty($page)){
			$repfrom[] = '<{$page|subdirectory}>';
			$repfrom[] = '<{$page}>';
			$repto[] = jieqi_getsubdir($page);
			$repto[] = $page;
		}
		$ret = trim(str_replace($repfrom, $repto, $fake_rule));
		if(substr($ret, 0, 4) != 'http') $ret = JIEQI_URL . $ret;
		return $ret;
	}else{
		$ret = $article_dynamic_url . '/reviewshow.php?tid=' . $tid;
		if(!empty($page)) $ret .= '&page=' . $page;
		else $ret .= '&page=';
		return $ret;
	}
}
?>