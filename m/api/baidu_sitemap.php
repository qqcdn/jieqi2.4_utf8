<?php

function jieqi_apis_xmltext($text)
{
    $entities = array('&' => '&amp;', '<' => '&lt;', '>' => '&gt;', '\'' => '&apos;', '"' => '&quot;');
    $text = strtr($text, $entities);
    $text = preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/', '', $text);
    return $text;
}
function jieqi_url_article($aid, $type = '', $acode = '')
{
    global $jieqiConfigs;
    global $article_dynamic_url;
    global $article_static_url;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    }
    if (empty($acode)) {
        $acode = $aid;
    }
    switch ($type) {
        case 'index':
            if (!empty($jieqiConfigs['article']['fakearticle']) || !empty($jieqiConfigs['article']['htmlurl']) && strpos($jieqiConfigs['article']['htmlurl'], '<{$aid}>') != false) {
                if (empty($jieqiConfigs['article']['fakearticle'])) {
                    $jieqiConfigs['article']['fakearticle'] = $jieqiConfigs['article']['htmlurl'];
                }
                if (0 < $jieqiConfigs['article']['makehtml'] && JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR') && strpos($jieqiConfigs['article']['fakearticle'], '<{$newset}>') === false) {
                    return $article_static_url . '/reader.php?aid=' . $aid;
                } else {
                    if (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) {
                        $newset = JIEQI_CHAR_SET;
                    } else {
                        $newset = '';
                    }
                    $repfrom = array('<{$jieqi_url}>', '<{$aid}>', '<{$aid|subdirectory}>', '<{$acode}>', '<{$newset}>');
                    $repto = array(JIEQI_URL, $aid, jieqi_getsubdir($aid), $acode, $newset);
                    $ret = str_replace($repfrom, $repto, $jieqiConfigs['article']['fakearticle']);
                    if (substr($ret, 0, 4) != 'http') {
                        $ret = JIEQI_URL . $ret;
                    }
                    return $ret;
                }
            } else {
                if ($jieqiConfigs['article']['makehtml'] == 0 || JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) {
                    return $article_static_url . '/reader.php?aid=' . $aid;
                } else {
                    return jieqi_uploadurl($jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/index' . $jieqiConfigs['article']['htmlfile'];
                }
            }
            break;
        case 'full':
            if ($jieqiConfigs['article']['makefull'] == 0 || JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) {
                $ret = $article_static_url . '/reader.php?aid=' . $aid;
            } else {
                $ret = jieqi_uploadurl($jieqiConfigs['article']['fulldir'], $jieqiConfigs['article']['fullurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . $jieqiConfigs['article']['htmlfile'];
            }
            return $ret;
            break;
        case 'info':
        default:
            if (!empty($jieqiConfigs['article']['fakeinfo'])) {
                $repfrom = array('<{$jieqi_url}>', '<{$id|subdirectory}>', '<{$id}>', '<{$acode}>');
                $repto = array(JIEQI_URL, jieqi_getsubdir($aid), $aid, $acode);
                $ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinfo']));
                if (substr($ret, 0, 4) != 'http') {
                    $ret = JIEQI_URL . $ret;
                }
                return $ret;
            } else {
                return $article_dynamic_url . '/articleinfo.php?id=' . $aid;
            }
            break;
    }
}
function jieqi_url_chapter($cid, $aid, $isvip = 0, $acode = '')
{
    global $jieqiConfigs;
    global $jieqiModules;
    global $article_dynamic_url;
    global $article_static_url;
    if (0 < $isvip) {
        if (!isset($jieqiConfigs['obook'])) {
            jieqi_getconfigs('obook', 'configs', 'jieqiConfigs');
        }
        return $jieqiModules['obook']['url'] . '/reader.php?cid=' . $cid . '&aid=' . $aid;
    }
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    }
    if (empty($acode)) {
        $acode = $aid;
    }
    if (!empty($jieqiConfigs['article']['fakechapter']) || !empty($jieqiConfigs['article']['htmlurl']) && strpos($jieqiConfigs['article']['htmlurl'], '<{$aid}>') != false) {
        if (empty($jieqiConfigs['article']['fakechapter'])) {
            $jieqiConfigs['article']['fakechapter'] = $jieqiConfigs['article']['htmlurl'];
            if (strpos($jieqiConfigs['article']['fakechapter'], '<{$cid}>') === false) {
                $jieqiConfigs['article']['fakechapter'] = str_replace(array('index', $jieqiConfigs['article']['htmlfile']), '', $jieqiConfigs['article']['fakechapter']);
                if (substr($jieqiConfigs['article']['fakechapter'], -1) != '/') {
                    $jieqiConfigs['article']['fakechapter'] .= '/';
                }
                $jieqiConfigs['article']['fakechapter'] .= '<{$cid}>' . $jieqiConfigs['article']['htmlfile'];
            }
        }
        if (0 < $jieqiConfigs['article']['makehtml'] && JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR') && strpos($jieqiConfigs['article']['fakechapter'], '<{$newset}>') === false) {
            return $article_static_url . '/reader.php?aid=' . $aid . '&cid=' . $cid;
        } else {
            if (JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) {
                $newset = JIEQI_CHAR_SET;
            } else {
                $newset = '';
            }
            $repfrom = array('<{$jieqi_url}>', '<{$aid}>', '<{$cid}>', '<{$aid|subdirectory}>', '<{$cid|subdirectory}>', '<{$acode}>', '<{$newset}>');
            $repto = array(JIEQI_URL, $aid, $cid, jieqi_getsubdir($aid), jieqi_getsubdir($cid), $acode, $newset);
            $ret = str_replace($repfrom, $repto, $jieqiConfigs['article']['fakechapter']);
            if (substr($ret, 0, 4) != 'http') {
                $ret = JIEQI_URL . $ret;
            }
            return $ret;
        }
    } else {
        if ($jieqiConfigs['article']['makehtml'] == 0 || JIEQI_CHAR_SET != JIEQI_SYSTEM_CHARSET && !defined('JIEQI_NOCONVERT_CHAR')) {
            return $article_static_url . '/reader.php?aid=' . $aid . '&cid=' . $cid;
        } else {
            return jieqi_uploadurl($jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $cid . $jieqiConfigs['article']['htmlfile'];
        }
    }
}
function jieqi_url_cover($aid, $type = 's', $flag = -1)
{
    global $jieqiConfigs;
    global $article_dynamic_url;
    global $article_static_url;
    $nocover = $article_static_url . '/images/nocover.jpg';
    if ($flag < 0) {
        global $article;
        if (!isset($article) || !is_a($article, 'JieqiArticle')) {
            include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $article = $article_handler->get($aid);
            if (is_object($article)) {
                $flag = $article->getVar('imgflag', 'n');
            }
        }
    }
    $flag = intval($flag);
    if ($flag <= 0) {
        return $nocover;
    }
    $imageinfo = array('stype' => '', 'ltype' => '');
    if (0 < ($flag & 1)) {
        $imageinfo['stype'] = $jieqiConfigs['article']['imagetype'];
    }
    if (0 < ($flag & 2)) {
        $imageinfo['ltype'] = $jieqiConfigs['article']['imagetype'];
    }
    $imgtype = $flag >> 2;
    if (0 < $imgtype) {
        $imgtary = array(1 => '.gif', 2 => '.jpg', 3 => '.jpeg', 4 => '.png', 5 => '.bmp');
        $tmpvar = round($imgtype & 7);
        if (isset($imgtary[$tmpvar])) {
            $imageinfo['stype'] = $imgtary[$tmpvar];
        }
        $tmpvar = round($imgtype >> 3);
        if (isset($imgtary[$tmpvar])) {
            $imageinfo['ltype'] = $imgtary[$tmpvar];
        }
    }
    switch ($type) {
        case 'l':
            if (!empty($imageinfo['ltype'])) {
                return jieqi_uploadurl($jieqiConfigs['article']['imagedir'], $jieqiConfigs['article']['imageurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $aid . 'l' . $imageinfo['ltype'];
            } else {
                if (!empty($imageinfo['stype'])) {
                    return jieqi_uploadurl($jieqiConfigs['article']['imagedir'], $jieqiConfigs['article']['imageurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $aid . 's' . $imageinfo['stype'];
                } else {
                    return '';
                }
            }
            break;
        case 's':
        default:
            if (!empty($imageinfo['stype'])) {
                return jieqi_uploadurl($jieqiConfigs['article']['imagedir'], $jieqiConfigs['article']['imageurl'], 'article', $article_static_url) . jieqi_getsubdir($aid) . '/' . $aid . '/' . $aid . 's' . $imageinfo['stype'];
            } else {
                return $nocover;
            }
            break;
    }
}
define('JIEQI_MODULE_NAME', 'article');
define('JIEQI_CHAR_SET', 'utf-8');
define('JIEQI_CHARSET_CONVERT', 0);
require_once '../global.php';
define('JIEQI_NOCONVERT_CHAR', '1');
$pagerows = 500;
$filepath = !empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (!empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/api/baidu_sitemap.php');
$indexstyle = 'info';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$_GET['update'] = empty($_GET['update']) || intval($_GET['update']) <= 0 ? 0 : intval($_GET['update']);
$_GET['page'] = empty($_GET['page']) || intval($_GET['page']) <= 0 ? 0 : intval($_GET['page']);
$where = 0 < $_GET['update'] ? 'lastupdate > ' . (time() - 3600 * $_GET['update']) . ' ORDER BY lastupdate DESC' : '1 ORDER BY articleid ASC';
header('Content-type: text/xml');
if ($_GET['page'] == 0) {
    $sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_article') . ' WHERE ' . $where;
    $query->execute($sql);
    $row = $query->getRow();
    $rowcount = intval($row['cot']);
    $pages = ceil($rowcount / $pagerows);
    echo '<?xml version="1.0"  encoding="UTF-8" ?>' . "\r\n" . '<sitemapindex>' . "\r\n" . '';
    for ($p = 1; $p <= $pages; $p++) {
        $url = JIEQI_LOCAL_URL . $filepath . '?page=' . $p;
        if (!empty($_GET['update'])) {
            $url .= '&update=' . $_GET['update'];
        }
        $url = jieqi_apis_xmltext($url);
        echo '<sitemap>' . "\r\n" . '<loc>' . $url . '</loc>' . "\r\n" . '</sitemap>' . "\r\n" . '';
    }
    echo '</sitemapindex>';
} else {
    include_once $jieqiModules['article']['path'] . '/include/funurl.php';
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE ' . $where . ' LIMIT ' . ($_GET['page'] - 1) * $pagerows . ', ' . $pagerows;
    $query->execute($sql);
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n" . '<urlset>';
    jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    jieqi_getconfigs('article', 'sort', 'jieqiSort');
    if (!isset($article_static_url)) {
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    }
    if (!isset($article_dynamic_url)) {
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    }
    while ($row = $query->getRow()) {
        $item = array();
        if (!isset($row['articlecode'])) {
            $row['articlecode'] = '';
        }
        $item['loc'] = @jieqi_url_article($row['articleid'], $indexstyle, $row['articlecode']);
        if (!preg_match('/^https?:\\/\\//i', $item['loc'])) {
            $item['loc'] = JIEQI_LOCAL_URL . $item['loc'];
        }
        $item['lastmod'] = date('Y-m-d', $row['lastupdate']) . 'T' . date('H:i:s', $row['lastupdate']);
        $item['name'] = $row['articlename'];
        $item['author_name'] = $row['author'];
        $item['image'] = jieqi_geturl('article', 'cover', $row['articleid'], 's', $row['imgflag']);
        if (!preg_match('/^http/i', $item['image'])) {
            $item['image'] = JIEQI_LOCAL_URL . $item['image'];
        }
        $item['description'] = $row['intro'];
        $item['alternativeHeadline'] = empty($row['backupname']) ? '' : $row['backupname'];
        $item['genre'] = isset($jieqiSort['article'][$row['sortid']]['caption']) ? $jieqiSort['article'][$row['sortid']]['caption'] : '';
        $item['wordCount'] = $row['words'];
        $item['url'] = @jieqi_url_article($row['articleid'], 'index', $row['articlecode']);
        if (!preg_match('/^https?:\\/\\//i', $item['url'])) {
            $item['url'] = JIEQI_LOCAL_URL . $item['url'];
        }
        $item['updateStatus'] = empty($row['fullflag']) ? '更新中' : '完结';
        $item['trialStatus'] = empty($row['isvip']) ? '免费' : '收费';
        $item['keywords'] = $row['keywords'];
        $item['weeklyClick'] = $row['weekvisit'];
        $item['monthlyClick'] = $row['monthvisit'];
        $item['totalClick'] = $row['allvisit'];
        $item['weeklyRecommend'] = $row['weekvote'];
        $item['monthlyRecommend'] = $row['monthvote'];
        $item['totalRecommend'] = $row['allvote'];
        if (empty($row['vipchapterid'])) {
            $item['newestChapter_headline'] = $row['lastchapter'];
            $item['newestChapter_acticleBody'] = empty($row['lastsummary']) ? '' : $row['lastsummary'];
            $item['newestChapter_url'] = @jieqi_url_chapter($row['lastchapterid'], $row['articleid'], 0, $row['articlecode']);
        } else {
            $item['newestChapter_headline'] = $row['vipchapter'];
            $item['newestChapter_acticleBody'] = empty($row['vipsummary']) ? '' : $row['vipsummary'];
            $item['newestChapter_url'] = @jieqi_url_chapter($row['vipchapterid'], $row['articleid'], 1, $row['articlecode']);
        }
        if (!preg_match('/^https?:\\/\\//i', $item['newestChapter_url'])) {
            $item['newestChapter_url'] = JIEQI_LOCAL_URL . $item['newestChapter_url'];
        }
        $item['newestChapter_dateModified'] = date('Y-m-d', $row['lastupdate']);
        $item['collectedCount'] = $row['goodnum'];
        $item['dateModified'] = date('Y-m-d', $row['lastupdate']);
        $item['discussionUrl'] = $jieqiModules['article']['url'] . '/reviews.php?aid=' . $row['articleid'];
        if (!preg_match('/^https?:\\/\\//i', $item['discussionUrl'])) {
            $item['discussionUrl'] = JIEQI_LOCAL_URL . $item['discussionUrl'];
        }
        $item['listPage_url'] = @jieqi_url_article($row['articleid'], 'index', $row['articlecode']);
        if (!preg_match('/^https?:\\/\\//i', $item['listPage_url'])) {
            $item['listPage_url'] = JIEQI_LOCAL_URL . $item['listPage_url'];
        }
        $item = jieqi_funtoarray('jieqi_apis_xmltext', $item);
        echo '	<url>' . "\r\n" . '		<loc><![CDATA[' . $item['loc'] . ']]></loc>' . "\r\n" . '		<lastmod>' . $item['lastmod'] . '</lastmod>' . "\r\n" . '		<changefreq>always</changefreq>' . "\r\n" . '		<priority>1.0</priority>' . "\r\n" . '		<data>' . "\r\n" . '			<name>' . $item['name'] . '</name>' . "\r\n" . '			<author>' . "\r\n" . '				<name>' . $item['author_name'] . '</name>' . "\r\n" . '			</author>' . "\r\n" . '			<image><![CDATA[' . $item['image'] . ']]></image>' . "\r\n" . '			<description><![CDATA[' . $item['description'] . ']]></description>' . "\r\n" . '			<alternativeHeadline>' . $item['alternativeHeadline'] . '</alternativeHeadline>' . "\r\n" . '			<genre>' . $item['genre'] . '</genre>' . "\r\n" . '			<wordCount>' . $item['wordCount'] . '</wordCount>' . "\r\n" . '			<url><![CDATA[' . $item['url'] . ']]></url>' . "\r\n" . '			<updateStatus>' . $item['updateStatus'] . '</updateStatus>' . "\r\n" . '			<trialStatus>' . $item['trialStatus'] . '</trialStatus>' . "\r\n" . '			<keywords>' . $item['keywords'] . '</keywords>' . "\r\n" . '			<weeklyClick>' . $item['weeklyClick'] . '</weeklyClick>' . "\r\n" . '			<monthlyClick>' . $item['monthlyClick'] . '</monthlyClick>' . "\r\n" . '			<totalClick>' . $item['totalClick'] . '</totalClick>' . "\r\n" . '			<weeklyRecommend>' . $item['weeklyRecommend'] . '</weeklyRecommend>' . "\r\n" . '			<monthlyRecommend>' . $item['monthlyRecommend'] . '</monthlyRecommend>' . "\r\n" . '			<totalRecommend>' . $item['totalRecommend'] . '</totalRecommend>' . "\r\n" . '			<newestChapter>' . "\r\n" . '				<articleSection>' . $item['name'] . '</articleSection>' . "\r\n" . '				<headline>' . $item['newestChapter_headline'] . '</headline>' . "\r\n" . '				<acticleBody><![CDATA[' . $item['newestChapter_acticleBody'] . ']]></acticleBody>' . "\r\n" . '				<url><![CDATA[' . $item['newestChapter_url'] . ']]></url>' . "\r\n" . '				<dateModified>' . $item['newestChapter_dateModified'] . '</dateModified>' . "\r\n" . '			</newestChapter>' . "\r\n" . '			<collectedCount>' . $item['collectedCount'] . '</collectedCount>' . "\r\n" . '			<dateModified>' . $item['dateModified'] . '</dateModified>' . "\r\n" . '			<discussionUrl><![CDATA[' . $item['discussionUrl'] . ']]></discussionUrl>' . "\r\n" . '			<listPage>' . "\r\n" . '				<headline>' . $item['name'] . '</headline>' . "\r\n" . '				<url><![CDATA[' . $item['listPage_url'] . ']]></url>' . "\r\n" . '				<itemCount>1</itemCount>' . "\r\n" . '			</listPage>' . "\r\n" . '		</data>' . "\r\n" . '	</url>';
    }
    echo '</urlset>';
}