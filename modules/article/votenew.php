<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_checklogin();
if (empty($_REQUEST['aid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('avote', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$_REQUEST['aid'] = intval($_REQUEST['aid']);
$article = $article_handler->get($_REQUEST['aid']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
$canedit = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if (!$canedit && !empty($_SESSION['jieqiUserId'])) {
    $tmpvar = $_SESSION['jieqiUserId'];
    if (0 < $tmpvar && ($article->getVar('authorid') == $tmpvar || $article->getVar('posterid') == $tmpvar || $article->getVar('agentid') == $tmpvar)) {
        $canedit = true;
    }
}
if (!$canedit) {
    jieqi_printfail($jieqiLang['article']['noper_article_votenew']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiConfigs['article']['articlevote'] = intval($jieqiConfigs['article']['articlevote']);
if ($jieqiConfigs['article']['articlevote'] <= 0) {
    jieqi_printfail($jieqiLang['article']['article_vote_close']);
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'vote';
}
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
switch ($_POST['act']) {
    case 'newvote':
        jieqi_checkpost();
        $errtext = '';
        $_POST['title'] = trim($_POST['title']);
        $useitem = 0;
        $itemary = array();
        for ($i = 1; $i <= $jieqiConfigs['article']['articlevote']; $i++) {
            $_POST['item' . $i] = trim($_POST['item' . $i]);
            if ($_POST['item' . $i] != '') {
                $itemary[$useitem] = $_POST['item' . $i];
                $useitem++;
            }
        }
        if (strlen($_POST['title']) == 0) {
            $errtext .= $jieqiLang['article']['avote_need_title'] . '<br />';
        }
        if ($useitem < 2) {
            $errtext .= $jieqiLang['article']['avote_need_moreitem'] . '<br />';
        }
        if (empty($errtext)) {
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            include_once $jieqiModules['article']['path'] . '/class/avote.php';
            $avote_handler = JieqiAvoteHandler::getInstance('JieqiAvoteHandler');
            $newVote = $avote_handler->create();
            $newVote->setVar('articleid', $_REQUEST['aid']);
            $newVote->setVar('posterid', $_SESSION['jieqiUserId']);
            $newVote->setVar('poster', $_REQUEST['jieqiUserName']);
            $newVote->setVar('posttime', JIEQI_NOW_TIME);
            $newVote->setVar('title', $_POST['title']);
            foreach ($itemary as $k => $v) {
                $newVote->setVar('item' . ($k + 1), $v);
            }
            $newVote->setVar('useitem', $useitem);
            if ($_POST['ispublish'] == 1) {
                $newVote->setVar('ispublish', 1);
                $newVote->setVar('starttime', JIEQI_NOW_TIME);
                $sql = 'UPDATE ' . jieqi_dbprefix('article_avote') . ' SET ispublish=0, endtime=' . intval(JIEQI_NOW_TIME) . ' WHERE articleid=' . $_REQUEST['aid'] . ' AND ispublish=1';
                $query->execute($sql);
            } else {
                $newVote->setVar('ispublish', 0);
                $newVote->setVar('starttime', 0);
            }
            if ($_POST['mulselect'] == 1) {
                $newVote->setVar('mulselect', 1);
            } else {
                $newVote->setVar('mulselect', 0);
            }
            $newVote->setVar('timelimit', 0);
            $newVote->setVar('needlogin', 0);
            $newVote->setVar('endtime', 0);
            if (!$avote_handler->insert($newVote)) {
                jieqi_printfail($jieqiLang['article']['avote_add_failure']);
            } else {
                $voteid = $newVote->getVar('voteid');
                include_once $jieqiModules['article']['path'] . '/class/avstat.php';
                $avstat_handler = JieqiAvstatHandler::getInstance('JieqiAvstatHandler');
                $newAvstat = $avstat_handler->create();
                $newAvstat->setVar('voteid', $voteid);
                $newAvstat->setVar('statall', 0);
                for ($i = 1; $i <= 10; $i++) {
                    $newAvstat->setVar('stat' . $i, 0);
                }
                $newAvstat->setVar('canstat', 0);
                if (!$avstat_handler->insert($newAvstat)) {
                    $avote_handler->delete($voteid);
                    jieqi_printfail($jieqiLang['article']['avote_add_failure']);
                }
                if ($_POST['ispublish'] == 1) {
                    $setting = jieqi_unserialize($article->getVar('setting', 'n'));
                    $setting['avoteid'] = $voteid;
                    $article->setVar('setting', serialize($setting));
                    $article_handler->insert($article);
                }
                jieqi_jumppage($article_static_url . '/votearticle.php?id=' . $_REQUEST['aid'], LANG_DO_SUCCESS, $jieqiLang['article']['avote_add_success']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'vote':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $voterows = array();
        for ($i = 1; $i <= $jieqiConfigs['article']['articlevote']; $i++) {
            $voterows[] = $i;
        }
        $jieqiTpl->assign_by_ref('voterows', $voterows);
        $jieqiTpl->assign('aid', $_REQUEST['aid']);
        $jieqiTpl->assign('authorarea', 1);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/votenew.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}