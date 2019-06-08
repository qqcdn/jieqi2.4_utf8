<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../global.php';
if (empty($_REQUEST['pid']) || !is_numeric($_REQUEST['pid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('post', JIEQI_MODULE_NAME);
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('p.postid', $_REQUEST['pid']));
$criteria->setTables(jieqi_dbprefix('forum_forumposts') . ' p LEFT JOIN ' . jieqi_dbprefix('forum_forums') . ' f ON p.ownerid=f.forumid');
$query->queryObjects($criteria);
$post = $query->getObject();
unset($criteria);
if (!$post) {
    jieqi_printfail($jieqiLang['forum']['post_not_exists']);
}
include_once $jieqiModules['forum']['path'] . '/include/funforum.php';
if (!jieqi_forum_checkpower($post, 'authedit', true)) {
    jieqi_printfail($jieqiLang['forum']['noper_edit_post']);
}
$authupload = jieqi_forum_checkpower($post, 'authupload', true);
$forum_type = intval($post->getVar('forumtype', 'n'));
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'update':
        jieqi_checkpost();
        include_once JIEQI_ROOT_PATH . '/include/funpost.php';
        $check_errors = array();
        $istopic = intval($post->getVar('istopic'));
        $istop = $forum_type == 1 ? 2 : 0;
        $post_set = array('module' => JIEQI_MODULE_NAME, 'ownerid' => intval($post->getVar('ownerid')), 'topicid' => intval($post->getVar('topicid')), 'postid' => intval($_REQUEST['pid']), 'posttime' => intval($post->getVar('posttime', 'n')), 'topictitle' => &$_POST['topictitle'], 'posttext' => &$_POST['posttext'], 'attachment' => $post->getVar('attachment', 'n'), 'emptytitle' => false, 'isnew' => false, 'istopic' => $istopic, 'istop' => $istop, 'sname' => 'jieqiForumPostTime', 'attachfile' => &$_FILES['attachfile'], 'oldattach' => &$_POST['oldattach'], 'checkcode' => $_POST['checkcode']);
        jieqi_post_checkvar($post_set, $jieqiConfigs['forum'], $check_errors);
        $attachary = array();
        if ($authupload) {
            jieqi_post_checkattach($post_set, $jieqiConfigs['forum'], $check_errors, $attachary);
        }
        $attachnum = count($attachary);
        if (empty($check_errors)) {
            include_once $jieqiModules['forum']['path'] . '/class/forumattachs.php';
            $attachs_handler = JieqiForumattachsHandler::getInstance('JieqiForumattachsHandler');
            $attacholds = jieqi_post_attachold($post_set, $jieqiConfigs['forum'], $attachs_handler);
            if (0 < $attachnum) {
                include_once $jieqiModules['forum']['path'] . '/class/forumattachs.php';
                if (!is_object($attachs_handler)) {
                    $attachs_handler = JieqiForumattachsHandler::getInstance('JieqiForumattachsHandler');
                }
                jieqi_post_attachdb($post_set, $attachary, $attachs_handler);
                jieqi_post_attachfile($post_set, $attachary, $jieqiConfigs['forum']);
            }
            foreach ($attachary as $val) {
                $attacholds[] = $val;
            }
            $post_set['attachment'] = serialize($attacholds);
            if (!jieqi_post_upedit($post_set, jieqi_dbprefix('forum_forumposts'))) {
                jieqi_printfail($jieqiLang['forum']['edit_post_failure']);
            }
            if ($post->getVar('istopic') == 1) {
                if (!jieqi_topic_upedit($post_set, jieqi_dbprefix('forum_forumtopics'))) {
                    jieqi_printfail($jieqiLang['forum']['edit_post_failure']);
                }
            }
            jieqi_post_finish();
            if ($post->getVar('istopic') == 1 && $forum_type == 1) {
                jieqi_forum_uptoptopic();
            }
            jieqi_jumppage(jieqi_geturl('forum', 'showtopic', $post->getVar('topicid')), LANG_DO_SUCCESS, $jieqiLang['forum']['edit_post_success']);
        } else {
            jieqi_printfail(implode('<br />', $check_errors));
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        include_once $jieqiModules['forum']['path'] . '/class/forumtopics.php';
        $topic_handler = JieqiForumtopicsHandler::getInstance('JieqiForumtopicsHandler');
        $topic = $topic_handler->get($post->getVar('topicid', 'n'));
        if (!$topic) {
            jieqi_printfail($jieqiLang['forum']['topic_not_exists']);
        }
        $jieqiTpl->assign('forumid', $post->getVar('forumid'));
        $jieqiTpl->assign('forumname', $post->getVar('forumname'));
        $jieqiTpl->assign('topicid', $topic->getVar('topicid'));
        $jieqiTpl->assign('topictitle', $topic->getVar('title'));
        $jieqiTpl->assign('postid', $post->getVar('postid', 'n'));
        $jieqiTpl->assign('subject', $post->getVar('subject', 'e'));
        $jieqiTpl->assign('posttext', $post->getVar('posttext', 'e'));
        $jieqiTpl->assign('istopic', $post->getVar('istopic', 'n'));
        $jieqiTpl->assign('authupload', $authupload);
        $jieqiTpl->assign('canupload', $authupload);
        if (!empty($jieqiConfigs['forum']['authtypeset'])) {
            $jieqiTpl->assign('authtypeset', intval($jieqiConfigs['forum']['authtypeset']));
        } else {
            $jieqiTpl->assign('authtypeset', 0);
        }
        $jieqiTpl->assign('maxattachnum', intval($jieqiConfigs['forum']['maxattachnum']));
        $jieqiTpl->assign('attachtype', $jieqiConfigs['forum']['attachtype']);
        $jieqiTpl->assign('maximagesize', $jieqiConfigs['forum']['maximagesize']);
        $jieqiTpl->assign('maxfilesize', $jieqiConfigs['forum']['maxfilesize']);
        $tmpvar = $post->getVar('attachment', 'n');
        $attachnum = 0;
        $attachrows = array();
        if (!empty($tmpvar)) {
            $attachrows = jieqi_unserialize($tmpvar);
            if (!is_array($attachrows)) {
                $attachrows = array();
            }
            $attachurl = jieqi_uploadurl($configs['attachdir'], $configs['attachurl'], JIEQI_MODULE_NAME) . '/' . date('Ymd', $post->getVar('posttime', 'n')) . '/' . $post->getVar('postid', 'n');
            foreach ($attachrows as $k => $v) {
                $attachrows[$k]['url'] = $attachurl . '_' . $v['attachid'] . '.' . $v['postfix'];
            }
            $attachnum = count($attachrows);
            $attachrows = jieqi_funtoarray('jieqi_htmlstr', $attachrows);
        }
        $jieqiTpl->assign('attachnum', $attachnum);
        $jieqiTpl->assign_by_ref('attachrows', $attachrows);
        include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
        if ($post->getVar('istopic') == 1) {
            $tmpvar = true;
        } else {
            $tmpvar = false;
        }
        $post_form = new JieqiThemeForm($jieqiLang['forum']['post_edit'], 'postedit', $jieqiModules['forum']['url'] . '/postedit.php');
        $post_form->setExtra('enctype="multipart/form-data"');
        $post_form->addElement(new JieqiFormText($jieqiLang['forum']['table_forumtopics_topictitle'], 'topictitle', 60, 60, $post->getVar('subject', 'e')), $tmpvar);
        $post_form->addElement(new JieqiFormDhtmlTextArea($jieqiLang['forum']['table_forumposts_posttext'], 'posttext', $post->getVar('posttext', 'e'), 12, 60), true);
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs');
        }
        $jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
        if (0 < $jieqiConfigs['system']['postcheckcode']) {
            if (!isset($jieqiLang['system']['post'])) {
                jieqi_loadlang('post', 'system');
            }
            $checkcode = new JieqiFormText($jieqiLang['system']['post_checkcode_label'], 'checkcode', 8, 8);
            $checkcode->setExtra('onfocus="if($_(\'p_imgccode\').style.display == \'none\'){$_(\'p_imgccode\').src = \'' . JIEQI_URL . '/checkcode.php\';$_(\'p_imgccode\').style.display = \'\';}" title="' . $jieqiLang['system']['post_checkcode_ctitle'] . '"');
            $checkcode->setDescription(sprintf($jieqiLang['system']['post_checkcode_code'], JIEQI_URL, JIEQI_URL));
            $post_form->addElement($checkcode, true);
        }
        if (0 < $attachnum) {
            foreach ($attachrows as $val) {
                $selectattach[] = $val['attachid'];
            }
            $attachelement = new JieqiFormCheckBox($jieqiLang['forum']['now_attach'], 'oldattach', $selectattach);
            $attachelement->setIntro($jieqiLang['forum']['attach_edit_note']);
            foreach ($attachrows as $key => $val) {
                $attachelement->addOption($val['attachid'], jieqi_htmlstr($val['name']) . '&nbsp;&nbsp;');
            }
            $post_form->addElement($attachelement, false);
        }
        if ($authupload && is_numeric($jieqiConfigs['forum']['maxattachnum']) && 0 < $jieqiConfigs['forum']['maxattachnum']) {
            $post_form->addElement(new JieqiFormLabel($jieqiLang['forum']['attach_limit'], $jieqiLang['forum']['attach_filetype'] . $jieqiConfigs['forum']['attachtype'] . ', ' . $jieqiLang['forum']['attach_image_max'] . $jieqiConfigs['forum']['maximagesize'] . 'K, ' . $jieqiLang['forum']['attach_file_max'] . $jieqiConfigs['forum']['maxfilesize'] . 'K'));
            $maxfilenum = intval($jieqiConfigs['forum']['maxattachnum']);
            for ($i = 1; $i <= $maxfilenum; $i++) {
                $post_form->addElement(new JieqiFormFile($jieqiLang['forum']['post_attach'] . $i, 'attachfile[]', 60));
            }
        }
        $post_form->addElement(new JieqiFormHidden('pid', $_REQUEST['pid']));
        $post_form->addElement(new JieqiFormHidden('act', 'update'));
        $post_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $post_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['forum']['edit_post_button'], 'submit'));
        $jieqiTpl->assign('postform', $post_form->render(JIEQI_FORM_MAX));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/postedit.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}