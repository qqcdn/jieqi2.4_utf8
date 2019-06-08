<?php

function jieqi_importtxt_matchtitle($rule, $line, $tagmatch = false)
{
    $ret = false;
    if (!empty($rule['maxlen']) && $rule['maxlen'] < strlen($line)) {
        return $ret;
    }
    $matches = array();
    if (preg_match($rule['preg'], $line, $matches)) {
        if (is_numeric($rule['no']) && isset($matches[$rule['no']])) {
            $ret = trim($matches[$rule['no']]);
        } else {
            if (is_array($rule['no'])) {
                foreach ($rule['no'] as $no) {
                    if (isset($matches[$no]) && 0 < strlen(trim($matches[$no]))) {
                        $ret = trim($matches[$no]);
                        break;
                    }
                }
            }
        }
    }
    if ($tagmatch && $ret !== false && !empty($rule['tagmatch'])) {
        if (!preg_match($rule['tagmatch'], $ret)) {
            $ret = false;
        }
    }
    return $ret;
}
function jieqi_importtxt_matchreplace($rule, $text)
{
    return trim(preg_replace($rule['preg'], $rule['replace'], $text));
}
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
$ismanager = true;
$allowtrans = true;
$allowmodify = true;
jieqi_loadlang('manage', 'article');
jieqi_loadlang('list', 'article');
jieqi_loadlang('importtxt', 'article');
jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
jieqi_getconfigs('article', 'option', 'jieqiOption');
jieqi_getconfigs('article', 'sort', 'jieqiSort');
jieqi_getconfigs('system', 'sites', 'jieqiSites');
jieqi_getconfigs('article', 'action', 'jieqiAction');
jieqi_getconfigs('article', 'importtxt', 'jieqiImporttxt');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'import':
        jieqi_checkpost();
        @set_time_limit(900);
        @session_write_close();
        if (empty($jieqiImporttxt['chapter'])) {
            jieqi_printfail($jieqiLang['article']['import_chapter_norule']);
        }
        if (empty($_FILES['articletxt']['name'])) {
            jieqi_printfail($jieqiLang['article']['import_no_txt']);
        }
        if (!empty($_POST['isupdate'])) {
            if (!empty($_POST['articleid'])) {
                $_POST['articleid'] = intval($_POST['articleid']);
                $article = $article_handler->get($_POST['articleid']);
                if (!$article) {
                    jieqi_printfail($jieqiLang['article']['import_article_notexists']);
                } else {
                    $_POST['articlename'] = $article->getVar('articlename', 'n');
                }
            } else {
                if (!empty($_POST['articlename'])) {
                    $_POST['articlename'] = trim($_POST['articlename']);
                    $article = $article_handler->get($_POST['articlename'], 'articlename');
                    if (!$article) {
                        jieqi_printfail($jieqiLang['article']['import_article_notexists']);
                    } else {
                        $_POST['articleid'] = $article->getVar('articleid', 'n');
                    }
                } else {
                    jieqi_printfail(LANG_ERROR_PARAMETER);
                }
            }
        }
        $_POST['vipstart'] = isset($_POST['vipstart']) ? intval($_POST['vipstart']) : 0;
        if (0 < $_POST['vipstart'] && (empty($_POST['issign']) || $_POST['issign'] < 10)) {
            $_POST['issign'] = 10;
        }
        include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
        if (empty($_POST['isupdate'])) {
            $_POST['articlename'] = trim($_POST['articlename']);
            if (isset($_POST['backupname'])) {
                $_POST['backupname'] = trim($_POST['backupname']);
            }
            $_POST['author'] = trim($_POST['author']);
            $_POST['agent'] = trim($_POST['agent']);
            $_POST['sortid'] = isset($_POST['sortid']) ? intval($_POST['sortid']) : 0;
            $_POST['typeid'] = isset($_POST['typeid']) ? intval($_POST['typeid']) : 0;
            if (!isset($jieqiSort['article'][$_POST['sortid']]['types'][$_POST['typeid']])) {
                $_POST['typeid'] = 0;
            }
            if (!isset($jieqiSort['article'][$_POST['sortid']])) {
                $_POST['sortid'] = 0;
            }
            $_POST['postdate'] = JIEQI_NOW_TIME;
            $_POST['lastupdate'] = JIEQI_NOW_TIME;
            $options = array('action' => 'add', 'ismanager' => $ismanager, 'allowtrans' => $allowtrans);
            $errors = jieqi_article_articlepcheck($_POST, $options);
            if (!empty($errors)) {
                jieqi_printfail(implode('<br />', $errors));
            }
        }
        $filedata = file_get_contents($_FILES['articletxt']['tmp_name']);
        if (!empty($jieqiImporttxt['txtfilter'])) {
            $filedata = jieqi_importtxt_matchreplace($jieqiImporttxt['txtfilter'], $filedata);
        }
        $line_explode_tag = "\n";
        $txtlines = explode($line_explode_tag, $filedata);
        if ($txtlines === false) {
            jieqi_printfail(sprintf($jieqiLang['article']['import_read_failure'], jieqi_htmlstr($_FILES['articletxt']['tmp_name'])));
        }
        jieqi_delfile($_FILES['articletxt']['tmp_name']);
        unset($filedata);
        $chapters = array();
        $k = -1;
        $iscn = false;
        $iscc = false;
        foreach ($txtlines as $line) {
            if ($iscn && !$iscc) {
                $tagmatch = true;
            } else {
                $tagmatch = false;
            }
            $matchstr = jieqi_importtxt_matchtitle($jieqiImporttxt['chapter'], $line, $tagmatch);
            if ($matchstr !== false) {
                $k++;
                $chapters[$k]['chaptertype'] = 0;
                $chapters[$k]['chaptername'] = $matchstr;
                $chapters[$k]['chaptercontent'] = '';
                $iscn = true;
                $iscc = false;
            } else {
                if (!empty($jieqiImporttxt['volume'])) {
                    $matchstr = jieqi_importtxt_matchtitle($jieqiImporttxt['volume'], $line);
                }
                if ($matchstr !== false) {
                    $k++;
                    $chapters[$k]['chaptertype'] = 1;
                    $chapters[$k]['chaptername'] = $matchstr;
                    $chapters[$k]['chaptercontent'] = '';
                    $iscn = false;
                } else {
                    if ($iscn) {
                        $chapters[$k]['chaptercontent'] .= $line . $line_explode_tag;
                        if (trim($line) != '') {
                            $iscc = true;
                        }
                    }
                }
            }
        }
        if (empty($chapters)) {
            jieqi_printfail($jieqiLang['article']['import_error_nochapter']);
        }
        $chaptercount = $k + 1;
        if (isset($jieqiImporttxt['footfilter'])) {
            $jieqiImporttxt['chapterfilter'] = $jieqiImporttxt['footfilter'];
        }
        if (!empty($jieqiImporttxt['chapterfilter']) && !empty($chapters[$k]['chaptercontent'])) {
            $chapters[$k]['chaptercontent'] = jieqi_importtxt_matchreplace($jieqiImporttxt['chapterfilter'], $chapters[$k]['chaptercontent']);
        }
        if (empty($_POST['isupdate'])) {
            $article = jieqi_article_articleadd($_POST, $options);
            if (!is_object($article)) {
                jieqi_printfail($article);
            }
        }
        include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
        $newaid = intval($article->getVar('articleid', 'n'));
        include_once JIEQI_ROOT_PATH . '/lib/text/texttypeset.php';
        $texttypeset = new TextTypeset();
        $chapterorder = intval($article->getVar('chapters', 'n'));
        $chapternum = 0;
        $setvip = false;
        foreach ($chapters as $k => $v) {
            $chapterorder++;
            if ($v['chaptertype'] == 1) {
                $chapvars = array();
                $chapvars['canupload'] = 0;
                $chapvars['uptiming'] = 0;
                $chapvars['posttype'] = 0;
                $chapvars['draftid'] = 0;
                $chapvars['articleid'] = $newaid;
                $chapvars['aid'] = $newaid;
                $chapvars['chaptertype'] = 1;
                $chapvars['isvip'] = 0;
                $chapvars['saleprice'] = -1;
                $chapvars['chapterorder'] = $chapterorder;
                $chapvars['chaptername'] = $v['chaptername'];
                $chapvars['chaptercontent'] = '';
                $chapvars['typeset'] = 0;
                $chapvars['fullflag'] = !empty($_POST['fullflag']) && $chaptercount - 1 == $k ? 1 : 0;
                $attachvars = array();
                jieqi_article_addchapter($chapvars, $attachvars, $article, true);
            } else {
                $chapvars = array();
                $chapvars['canupload'] = 0;
                $chapvars['uptiming'] = 0;
                $chapvars['posttype'] = 0;
                $chapvars['draftid'] = 0;
                $chapvars['articleid'] = $newaid;
                $chapvars['aid'] = $newaid;
                $chapvars['chaptertype'] = 0;
                $chapvars['isvip'] = 0 < $_POST['vipstart'] && $_POST['vipstart'] <= $chapternum + 1 ? 1 : 0;
                if ($setvip == false && 0 < $chapvars['isvip']) {
                    $setvip = true;
                    if (intval($article->getVar('issign', 'n')) < 10) {
                        $article->setVar('issign', 10);
                    }
                }
                $chapvars['saleprice'] = -1;
                $chapvars['chapterorder'] = $chapterorder;
                $chapvars['chaptername'] = $v['chaptername'];
                $chapvars['chaptercontent'] = $v['chaptercontent'];
                $chapvars['chaptercontent'] = $texttypeset->doTypeset($chapvars['chaptercontent']);
                $chapvars['typeset'] = 1;
                $chapvars['fullflag'] = !empty($_POST['fullflag']) && $chaptercount - 1 == $k ? 1 : 0;
                $attachvars = array();
                jieqi_article_addchapter($chapvars, $attachvars, $article, true);
                $chapternum++;
            }
        }
        $lastinfo = jieqi_article_searchlast($article, 'full');
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = $query->makeupsql(jieqi_dbprefix('article_article'), $lastinfo, 'UPDATE', array('articleid' => $article->getVar('articleid', 'n')));
        $query->execute($sql);
        if (0 < $article->getVar('vipid', 'n') || 0 < $lastinfo['isvip']) {
            $lastobook = array('lastupdate' => $lastinfo['viptime'], 'chapters' => $lastinfo['vipchapters'], 'words' => $lastinfo['vipwords'], 'lastvolumeid' => $lastinfo['vipvolumeid'], 'lastvolume' => $lastinfo['vipvolume'], 'lastchapterid' => $lastinfo['vipchapterid'], 'lastchapter' => $lastinfo['vipchapter'], 'lastsummary' => $lastinfo['vipsummary']);
            $sql = $query->makeupsql(jieqi_dbprefix('obook_obook'), $lastobook, 'UPDATE', array('articleid' => $article->getVar('articleid', 'n')));
            $query->execute($sql);
        }
        include_once $jieqiModules['article']['path'] . '/include/repack.php';
        $makeparams = array('makeopf' => 1, 'makezip' => intval($jieqiConfigs['article']['makezip']), 'makefull' => intval($jieqiConfigs['article']['makefull']), 'maketxtfull' => intval($jieqiConfigs['article']['maketxtfull']), 'makeumd' => intval($jieqiConfigs['article']['makeumd']), 'makejar' => intval($jieqiConfigs['article']['makejar']), 'makeindex' => intval($jieqiConfigs['article']['makehtml']));
        $makeparams['makechapter'] = intval($jieqiConfigs['article']['makehtml']);
        $makeparams['maketxtjs'] = intval($jieqiConfigs['article']['maketxtjs']);
        article_repack(intval($article->getVar('articleid', 'n')), $makeparams, 1);
        jieqi_article_updateinfo($article, 'articlenew');
        $jumpurl = $article_static_url . '/admin/importtxt.php?before_articleid=' . $newaid . '&before_articlename=' . urlencode($article->getVar('articlename', 'n'));
        if (!empty($_POST['isupdate'])) {
            $jumpurl .= '&articleid=' . intval($_POST['articleid']);
        }
        jieqi_jumppage($jumpurl, LANG_DO_SUCCESS, $jieqiLang['article']['import_txt_success']);
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $jieqiTpl->assign('url_importtxt', $article_static_url . '/admin/importtxt.php?do=submit');
        $article = NULL;
        if (!empty($_REQUEST['articleid'])) {
            $_REQUEST['articleid'] = intval($_REQUEST['articleid']);
            $article = $article_handler->get($_REQUEST['articleid']);
            if (!$article) {
                jieqi_printfail($jieqiLang['article']['import_article_notexists']);
            }
        } else {
            if (!empty($_REQUEST['articlename'])) {
                $_REQUEST['articlename'] = trim($_REQUEST['articlename']);
                $article = $article_handler->get($_REQUEST['articlename'], 'articlename');
                if (!$article) {
                    jieqi_printfail($jieqiLang['article']['import_article_notexists']);
                }
            }
        }
        if (is_object($article)) {
            $jieqiTpl->assign('articleid', $article->getVar('articleid'));
            $jieqiTpl->assign('articlename', $article->getVar('articlename'));
        } else {
            $jieqiTpl->assign('articleid', 0);
            $jieqiTpl->assign('articlename', '');
        }
        jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
        $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
        foreach ($jieqiOption['article'] as $k => $v) {
            $jieqiTpl->assign($k, $jieqiOption['article'][$k]);
        }
        if (2 <= floatval(JIEQI_VERSION)) {
            $jieqiTpl->assign('taglimit', intval($jieqiConfigs['article']['taglimit']));
            $tagwords = array();
            $tmpary = preg_split('/\\s+/s', $jieqiConfigs['article']['tagwords']);
            $k = 0;
            foreach ($tmpary as $v) {
                if (0 < strlen($v)) {
                    $tagwords[$k]['name'] = jieqi_htmlstr($v);
                    $k++;
                }
            }
            $jieqiTpl->assign_by_ref('tagwords', $tagwords);
            $jieqiTpl->assign('tagnum', count($tagwords));
        }
        $jieqiTpl->assign('imagetype', $jieqiConfigs['article']['imagetype']);
        $jieqiTpl->assign('allowtrans', intval($allowtrans));
        $jieqiTpl->assign('allowmodify', intval($allowmodify));
        $jieqiTpl->assign('ismanager', intval($ismanager));
        $ismatch = empty($jieqiConfigs['article']['ismatch']) ? 0 : 1;
        $jieqiTpl->assign('ismatch', $ismatch);
        $wholebuy = empty($jieqiConfigs['article']['wholebuy']) ? 0 : intval($jieqiConfigs['article']['wholebuy']);
        $jieqiTpl->assign('wholebuy', $wholebuy);
        if (2 <= floatval(JIEQI_VERSION)) {
            $customsites = array();
            foreach ($jieqiSites as $k => $v) {
                if (!empty($v['custom'])) {
                    $customsites[$k] = $v;
                }
            }
            $jieqiTpl->assign('customsites', jieqi_funtoarray('jieqi_htmlstr', $customsites));
            $jieqiTpl->assign('customsitenum', count($customsites));
            $jieqiTpl->assign('jieqisites', jieqi_funtoarray('jieqi_htmlstr', $jieqiSites));
        }
        if (jieqi_checkpower($jieqiPower['article']['transarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
            $jieqiTpl->assign('allowtrans', 1);
        } else {
            $jieqiTpl->assign('allowtrans', 0);
        }
        $jieqiTpl->assign('authorarea', 1);
        $before_articleid = empty($_REQUEST['before_articleid']) ? 0 : intval($_REQUEST['before_articleid']);
        $before_articlename = empty($_REQUEST['before_articlename']) ? 0 : trim($_REQUEST['before_articlename']);
        $jieqiTpl->assign('before_articleid', $before_articleid);
        $jieqiTpl->assign('before_articlename', jieqi_htmlstr($before_articlename));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/importtxt.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}