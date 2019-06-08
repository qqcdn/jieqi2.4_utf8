<?php

function replace_path($articleid, $dirtype, $filetype, $idisdir = true)
{
    global $showinfo;
    global $fromary;
    global $toary;
    global $jieqiLang;
    $dirname = jieqi_uploadpath($dirtype, 'article') . jieqi_getsubdir($articleid);
    if ($idisdir) {
        $dirname .= '/' . $articleid;
    } else {
        $dirname .= '/' . $articleid . $filetype;
    }
    if (file_exists($dirname)) {
        echo str_repeat(' ', 4096);
        echo sprintf($jieqiLang['article']['replace_id_doing'], $articleid);
        ob_flush();
        flush();
        if (is_dir($dirname)) {
            $handle = @opendir($dirname);
            while ($handle !== false && ($file = @readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $filename = $dirname . '/' . $file;
                    if (is_file($filename) && is_writable($filename) && substr($filename, -strlen($filetype)) == $filetype) {
                        if (empty($_REQUEST['filesize']) || $_REQUEST['filesize'] == 'sizeunlimit' || $_REQUEST['filesize'] == 'sizeless' && filesize($filename) <= 1024 || $_REQUEST['filesize'] == 'sizemore' && 1024 <= filesize($filename)) {
                            $filedata = jieqi_readfile($filename);
                            if ($_REQUEST['replacetype'] == 1) {
                                $filedata = jieqi_mbreplace($fromary, $toary, $filedata);
                            } else {
                                $filedata = jieqi_mbreplace($_REQUEST['txtsearch'], $_REQUEST['txtreplace'], $filedata);
                            }
                            jieqi_writefile($filename, $filedata);
                        }
                    }
                }
            }
        } else {
            if (is_file($dirname)) {
                $filename = $dirname;
                if (is_file($filename) && is_writable($filename) && substr($filename, -strlen($filetype)) == $filetype) {
                    if (empty($_REQUEST['filesize']) || $_REQUEST['filesize'] == 'sizeunlimit' || $_REQUEST['filesize'] == 'sizeless' && filesize($filename) <= 1024 || $_REQUEST['filesize'] == 'sizemore' && 1024 <= filesize($filename)) {
                        $filedata = jieqi_readfile($filename);
                        if ($_REQUEST['replacetype'] == 1) {
                            $filedata = jieqi_mbreplace($fromary, $toary, $filedata);
                        } else {
                            $filedata = jieqi_mbreplace($_REQUEST['txtsearch'], $_REQUEST['txtreplace'], $filedata);
                        }
                        jieqi_writefile($filename, $filedata);
                    }
                }
            }
        }
        $showinfo = $jieqiLang['article']['replace_success_next'];
    } else {
        $showinfo = $jieqiLang['article']['replace_noid_next'];
    }
}
define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$fromary = array();
$toary = array();
if (isset($_REQUEST['act'])) {
    if (isset($_REQUEST['txtsearch'])) {
        $_REQUEST['txtsearch'] = trim($_REQUEST['txtsearch']);
        $_SESSION['tmp_txtsearch'] = $_REQUEST['txtsearch'];
    } else {
        if (isset($_SESSION['tmp_txtsearch'])) {
            $_REQUEST['txtsearch'] = $_SESSION['tmp_txtsearch'];
        } else {
            $_REQUEST['txtsearch'] = '';
        }
    }
    if (isset($_REQUEST['txtreplace'])) {
        $_REQUEST['txtreplace'] = trim($_REQUEST['txtreplace']);
        $_SESSION['tmp_txtreplace'] = $_REQUEST['txtreplace'];
    } else {
        if (isset($_SESSION['tmp_txtreplace'])) {
            $_REQUEST['txtreplace'] = $_SESSION['tmp_txtreplace'];
        } else {
            $_REQUEST['txtreplace'] = '';
        }
    }
}
$sortids = array();
if (is_array($_REQUEST['sortid']) && 0 < count($_REQUEST['sortid'])) {
    foreach ($_REQUEST['sortid'] as $v) {
        if (is_numeric($v)) {
            $sortids[] = intval($v);
        }
    }
}
if (isset($_REQUEST[JIEQI_TOKEN_NAME]) && !isset($_POST[JIEQI_TOKEN_NAME])) {
    $_POST[JIEQI_TOKEN_NAME] = $_REQUEST[JIEQI_TOKEN_NAME];
}
if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'replacewithid') {
    jieqi_checkpost();
    if (empty($_REQUEST['txtsearch'])) {
        jieqi_printfail($jieqiLang['article']['need_replace_from']);
    }
    if ($_REQUEST['replacetype'] == 1) {
        $fromary = explode("\n", $_REQUEST['txtsearch']);
        $toary = explode("\n", $_REQUEST['txtreplace']);
        if (count($fromary) != count($toary)) {
            jieqi_printfail($jieqiLang['article']['replace_lines_difference']);
        } else {
            foreach ($fromary as $k => $v) {
                $fromary[$k] = trim($fromary[$k]);
                if ($fromary[$k] == '') {
                    jieqi_printfail($jieqiLang['article']['replace_lines_empay']);
                }
            }
            foreach ($toary as $k => $v) {
                $toary[$k] = trim($toary[$k]);
            }
        }
    }
    if (!empty($_REQUEST['flagary'])) {
        $_REQUEST['flagary'] = jieqi_unserialize(urldecode($_REQUEST['flagary']));
    } else {
        $_REQUEST['flagary'] = $_REQUEST['replaceflag'];
    }
    if (!is_array($_REQUEST['flagary']) || count($_REQUEST['flagary']) < 1) {
        jieqi_printfail($jieqiLang['article']['need_replace_filetype']);
    }
    if (empty($_REQUEST['fromid']) || !is_numeric($_REQUEST['fromid'])) {
        jieqi_printfail($jieqiLang['article']['need_replace_startid']);
    }
    if (empty($_REQUEST['toid'])) {
        $_REQUEST['toid'] = 0;
    }
    if ($_REQUEST['toid'] < $_REQUEST['fromid']) {
        jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['batch_replace_success'], $article_static_url . '/admin/batchreplace.php'));
        if (isset($_SESSION['tmp_txtsearch'])) {
            unset($_SESSION['tmp_txtsearch']);
        }
        if (isset($_SESSION['tmp_txtreplace'])) {
            unset($_SESSION['tmp_txtreplace']);
        }
        exit;
    }
    $replacefile = true;
    if (!empty($sortids)) {
        include_once $jieqiModules['article']['path'] . '/class/article.php';
        $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
        $article = $article_handler->get($_REQUEST['fromid']);
        if (!$article || !in_array($article->getVar('sortid', 'n'), $sortids)) {
            $replacefile = false;
        }
    }
    if ($replacefile) {
        if (in_array('filetxt', $_REQUEST['flagary'])) {
            replace_path($_REQUEST['fromid'], $jieqiConfigs['article']['txtdir'], $jieqi_file_postfix['txt']);
        }
        if (in_array('filetxtjs', $_REQUEST['flagary'])) {
            replace_path($_REQUEST['fromid'], $jieqiConfigs['article']['txtjsdir'], $jieqi_file_postfix['js']);
        }
        if (in_array('filehtml', $_REQUEST['flagary'])) {
            replace_path($_REQUEST['fromid'], $jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlfile']);
        }
        if (in_array('filefull', $_REQUEST['flagary'])) {
            replace_path($_REQUEST['fromid'], $jieqiConfigs['article']['fulldir'], $jieqiConfigs['article']['htmlfile'], false);
        }
    } else {
        $showinfo = $jieqiLang['article']['replace_noid_next'];
    }
    ${$_REQUEST}['fromid']++;
    $url = $article_static_url . '/admin/batchreplace.php?fromid=' . $_REQUEST['fromid'] . '&toid=' . $_REQUEST['toid'];
    foreach ($_REQUEST['replaceflag'] as $k => $v) {
        $url .= '&replaceflag[' . $k . ']=' . $v;
    }
    foreach ($sortids as $k => $v) {
        $url .= '&sortid[' . $k . ']=' . $v;
    }
    $url .= '&act=replacewithid&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&filesize=' . urlencode($_REQUEST['filesize']) . '&replacetype=' . urlencode($_REQUEST['replacetype']);
    echo sprintf($jieqiLang['article']['replace_next_html'], JIEQI_CHAR_SET, $showinfo, $url, $url);
} else {
    if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'replacewithtime') {
        jieqi_checkpost();
        if (empty($_REQUEST['txtsearch'])) {
            jieqi_printfail($jieqiLang['article']['need_replace_from']);
        }
        if ($_REQUEST['replacetype'] == 1) {
            $fromary = explode("\n", $_REQUEST['txtsearch']);
            $toary = explode("\n", $_REQUEST['txtreplace']);
            if (count($fromary) != count($toary)) {
                jieqi_printfail($jieqiLang['article']['replace_lines_difference']);
            } else {
                foreach ($fromary as $k => $v) {
                    $fromary[$k] = trim($fromary[$k]);
                    if ($fromary[$k] == '') {
                        jieqi_printfail($jieqiLang['article']['replace_lines_empay']);
                    }
                }
                foreach ($toary as $k => $v) {
                    $toary[$k] = trim($toary[$k]);
                }
            }
        }
        if (!empty($_REQUEST['flagary'])) {
            $_REQUEST['flagary'] = jieqi_unserialize(urldecode($_REQUEST['flagary']));
        } else {
            $_REQUEST['flagary'] = $_REQUEST['replaceflag'];
        }
        if (!is_array($_REQUEST['flagary']) || count($_REQUEST['flagary']) < 1) {
            jieqi_printfail($jieqiLang['article']['need_replace_filetype']);
        }
        $_REQUEST['starttime'] = trim($_REQUEST['starttime']);
        $_REQUEST['stoptime'] = trim($_REQUEST['stoptime']);
        if (empty($_REQUEST['starttime'])) {
            jieqi_printfail($jieqiLang['article']['need_replace_starttime']);
        }
        if (!is_numeric($_REQUEST['starttime'])) {
            $_REQUEST['starttime'] = mktime((int) substr($_REQUEST['starttime'], 11, 2), (int) substr($_REQUEST['starttime'], 14, 2), (int) substr($_REQUEST['starttime'], 17, 2), (int) substr($_REQUEST['starttime'], 5, 2), (int) substr($_REQUEST['starttime'], 8, 2), (int) substr($_REQUEST['starttime'], 0, 5));
        }
        if (empty($_REQUEST['stoptime'])) {
            $_REQUEST['stoptime'] = JIEQI_NOW_TIME;
        }
        if (!is_numeric($_REQUEST['stoptime'])) {
            $_REQUEST['stoptime'] = mktime((int) substr($_REQUEST['stoptime'], 11, 2), (int) substr($_REQUEST['stoptime'], 14, 2), (int) substr($_REQUEST['stoptime'], 17, 2), (int) substr($_REQUEST['stoptime'], 5, 2), (int) substr($_REQUEST['stoptime'], 8, 2), (int) substr($_REQUEST['stoptime'], 0, 5));
        }
        include_once $jieqiModules['article']['path'] . '/class/article.php';
        $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
        if (empty($_REQUEST['startlimit'])) {
            $_REQUEST['startlimit'] = 0;
        }
        $criteria = new CriteriaCompo(new Criteria('lastupdate', $_REQUEST['starttime'], '>='));
        $criteria->add(new Criteria('lastupdate', $_REQUEST['stoptime'], '<='));
        if (0 < count($sortids)) {
            $criteria->add(new Criteria('sortid', '(' . implode(',', $sortids) . ')', 'IN'));
        }
        $criteria->setSort('lastupdate');
        $criteria->setOrder('ASC');
        $criteria->setStart($_REQUEST['startlimit']);
        $criteria->setLimit(1);
        $article_handler->queryObjects($criteria);
        $article = $article_handler->getObject();
        if (is_object($article)) {
            if (in_array('filetxt', $_REQUEST['flagary'])) {
                replace_path($article->getVar('articleid'), $jieqiConfigs['article']['txtdir'], $jieqi_file_postfix['txt']);
            }
            if (in_array('filehtml', $_REQUEST['flagary'])) {
                replace_path($article->getVar('articleid'), $jieqiConfigs['article']['htmldir'], $jieqiConfigs['article']['htmlfile']);
            }
            if (in_array('filefull', $_REQUEST['flagary'])) {
                replace_path($article->getVar('articleid'), $jieqiConfigs['article']['fulldir'], $jieqiConfigs['article']['htmlfile'], false);
            }
        } else {
            jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['batch_replace_success'], $article_static_url . '/admin/batchreplace.php'));
            if (isset($_SESSION['tmp_txtsearch'])) {
                unset($_SESSION['tmp_txtsearch']);
            }
            if (isset($_SESSION['tmp_txtreplace'])) {
                unset($_SESSION['tmp_txtreplace']);
            }
            exit;
        }
        ${$_REQUEST}['startlimit']++;
        $url = $article_static_url . '/admin/batchreplace.php?starttime=' . $_REQUEST['starttime'] . '&stoptime=' . $_REQUEST['stoptime'] . '&startlimit=' . $_REQUEST['startlimit'];
        foreach ($_REQUEST['replaceflag'] as $k => $v) {
            $url .= '&replaceflag[' . $k . ']=' . $v;
        }
        foreach ($sortids as $k => $v) {
            $url .= '&sortid[' . $k . ']=' . $v;
        }
        $url .= '&act=replacewithtime&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&filesize=' . urlencode($_REQUEST['filesize']) . '&replacetype=' . urlencode($_REQUEST['replacetype']);
        echo sprintf($jieqiLang['article']['replace_next_html'], JIEQI_CHAR_SET, $showinfo, $url, $url);
    } else {
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'SELECT MIN(articleid) AS minaid, MAX(articleid) AS maxaid FROM ' . jieqi_dbprefix('article_article') . ' WHERE 1';
        $query->execute($sql);
        $row = $query->getRow();
        $jieqiTpl->assign('minaid', $row['minaid']);
        $jieqiTpl->assign('maxaid', $row['maxaid']);
        $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
        $jieqiTpl->assign('url_batchreplace', $article_static_url . '/admin/batchreplace.php?do=submit');
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/batchreplace.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
    }
}