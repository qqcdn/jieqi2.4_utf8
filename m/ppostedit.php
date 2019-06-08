<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
if (empty($_REQUEST['pid']) || !is_numeric($_REQUEST['pid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('parlar', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/pposts.php';
$pposts_handler = JieqiPpostsHandler::getInstance('JieqiPpostsHandler');
$ppost = $pposts_handler->get($_REQUEST['pid']);
if (!$ppost) {
    jieqi_printfail($jieqiLang['system']['ppost_not_exists']);
}
if ($ppost->getVar('posterid') != $_SESSION['jieqiUserId']) {
    jieqi_printfail($jieqiLang['system']['ppost_edit_noper']);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
include_once JIEQI_ROOT_PATH . '/include/funpost.php';
if (!isset($_POST['act'])) {
    $_POST['act'] = 'edit';
}
switch ($_POST['act']) {
    case 'update':
        jieqi_checkpost();
        $check_errors = array();
        $post_set = array('module' => JIEQI_MODULE_NAME, 'ownerid' => intval($ppost->getVar('ownerid')), 'topicid' => intval($ppost->getVar('topicid')), 'postid' => intval($ppost->getVar('postid')), 'posttime' => intval($ppost->getVar('posttime', 'n')), 'topictitle' => &$_POST['ptitle'], 'posttext' => &$_POST['pcontent'], 'attachment' => '', 'emptytitle' => true, 'isnew' => false, 'istopic' => intval($ppost->getVar('istopic')), 'istop' => 0, 'sname' => 'jieqiSystemParlorTime', 'attachfile' => '', 'oldattach' => '', 'checkcode' => $_POST['checkcode']);
        jieqi_post_checkvar($post_set, $jieqiConfigs['system'], $check_errors);
        if (empty($check_errors)) {
            jieqi_post_finish();
            jieqi_post_upedit($post_set, jieqi_dbprefix('system_pposts'));
            if ($ppost->getVar('istopic') == 1) {
                jieqi_topic_upedit($post_set, jieqi_dbprefix('system_ptopics'));
            }
            jieqi_jumppage(JIEQI_URL . '/ptopicshow.php?tid=' . $ppost->getVar('topicid'), LANG_DO_SUCCESS, $jieqiLang['system']['ppost_edit_success']);
        } else {
            jieqi_printfail(implode('<br />', $check_errors));
        }
        break;
    case 'edit':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('subject', $ppost->getVar('subject'));
        $jieqiTpl->assign('topicid', $ppost->getVar('topicid'));
        $jieqiTpl->assign('postid', $ppost->getVar('postid'));
        $jieqiTpl->assign('url_ppostedit', 'ppostedit.php?do=submit');
        $jieqiTpl->assign('ptitle', $ppost->getVar('subject', 'e'));
        $jieqiTpl->assign('pcontent', $ppost->getVar('posttext', 'e'));
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs');
        }
        $jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
        $jieqiTpl->assign('pid', $_REQUEST['pid']);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/ppostedit.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}