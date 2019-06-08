<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../global.php';
if (empty($_REQUEST['fid']) || !is_numeric($_REQUEST['fid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
if (isset($_REQUEST['tid'])) {
    $_REQUEST['tid'] = intval($_REQUEST['tid']);
}
jieqi_loadlang('post', JIEQI_MODULE_NAME);
include_once $jieqiModules['forum']['path'] . '/class/forums.php';
$forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
$forum = $forums_handler->get($_REQUEST['fid']);
if (!$forum) {
    jieqi_printfail($jieqiLang['forum']['forum_not_exists']);
}
include_once $jieqiModules['forum']['path'] . '/include/funforum.php';
if (isset($_REQUEST['tid']) && !empty($_REQUEST['tid'])) {
    if (!jieqi_forum_checkpower($forum, 'authreply', true)) {
        jieqi_printfail($jieqiLang['forum']['noper_reply_post']);
    }
} else {
    if (!jieqi_forum_checkpower($forum, 'authpost', true)) {
        jieqi_printfail($jieqiLang['forum']['noper_new_post']);
    }
}
$authupload = jieqi_forum_checkpower($forum, 'authupload', true);
$forum_type = intval($forum->getVar('forumtype', 'n'));
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'newpost':
        jieqi_checkpost();
        include_once JIEQI_ROOT_PATH . '/include/funpost.php';
        $check_errors = array();
        $istopic = empty($_REQUEST['tid']) ? 1 : 0;
        $istop = $forum_type == 1 ? 2 : 0;
        $post_set = array('module' => JIEQI_MODULE_NAME, 'ownerid' => intval($_REQUEST['fid']), 'ownername' => $forum->getVar('forumname', 'n'), 'topicid' => intval($_REQUEST['tid']), 'postid' => 0, 'posttime' => JIEQI_NOW_TIME, 'topictitle' => &$_POST['topictitle'], 'posttext' => &$_POST['posttext'], 'attachment' => '', 'emptytitle' => false, 'isnew' => true, 'istopic' => $istopic, 'istop' => $istop, 'sname' => 'jieqiForumPostTime', 'attachfile' => &$_FILES['attachfile'], 'oldattach' => '', 'checkcode' => $_POST['checkcode']);
        jieqi_post_checkvar($post_set, $jieqiConfigs['forum'], $check_errors);
        $attachary = array();
        if ($authupload) {
            jieqi_post_checkattach($post_set, $jieqiConfigs['forum'], $check_errors, $attachary);
        }
        $attachnum = count($attachary);
        if (empty($check_errors)) {
            $addnewreply = 0;
            include_once $jieqiModules['forum']['path'] . '/class/forumtopics.php';
            $topic_handler = JieqiForumtopicsHandler::getInstance('JieqiForumtopicsHandler');
            if (empty($_REQUEST['tid'])) {
                $newTopic = $topic_handler->create();
                jieqi_topic_newset($post_set, $newTopic);
                if (!$topic_handler->insert($newTopic)) {
                    jieqi_printfail($jieqiLang['forum']['post_topic_failure']);
                }
                $_REQUEST['tid'] = $newTopic->getVar('topicid', 'n');
                $post_set['topicid'] = $_REQUEST['tid'];
            } else {
                $topic = $topic_handler->get($_REQUEST['tid']);
                if (!$topic) {
                    jieqi_printfail($jieqiLang['forum']['topic_not_exists']);
                } else {
                    if (0 < $topic->getVar('islock', 'n')) {
                        jieqi_printfail($jieqiLang['forum']['topic_is_locked']);
                    }
                }
                $addnewreply = 1;
            }
            if (0 < $attachnum) {
                include_once $jieqiModules['forum']['path'] . '/class/forumattachs.php';
                $attachs_handler = JieqiForumattachsHandler::getInstance('JieqiForumattachsHandler');
                jieqi_post_attachdb($post_set, $attachary, $attachs_handler);
                $post_set['attachment'] = serialize($attachary);
            }
            include_once $jieqiModules['forum']['path'] . '/class/forumposts.php';
            $post_handler = JieqiForumpostsHandler::getInstance('JieqiForumpostsHandler');
            $newPost = $post_handler->create();
            jieqi_post_newset($post_set, $newPost);
            if (!$post_handler->insert($newPost)) {
                jieqi_printfail($jieqiLang['forum']['post_faliure']);
            } else {
                $postid = $newPost->getVar('postid', 'n');
                $post_set['postid'] = $postid;
                $post_set['posttime'] = JIEQI_NOW_TIME;
                $postdisplay = intval($newPost->getVar('display', 'n'));
                $postresult = 0 < $postdisplay ? $jieqiLang['forum']['post_needaudit'] : $jieqiLang['forum']['post_success'];
                jieqi_post_finish();
                jieqi_forum_upnewpost($_REQUEST['fid'], array('time' => JIEQI_NOW_TIME, 'uid' => intval($_SESSION['jieqiUserId']), 'uname' => strval($_SESSION['jieqiUserName']), 'tid' => $_REQUEST['tid'], 'istopic' => $istopic));
                include_once JIEQI_ROOT_PATH . '/class/users.php';
                $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                if (!empty($istopic)) {
                    if (!empty($jieqiConfigs['forum']['scoretopic'])) {
                        $users_handler->changeScore($_SESSION['jieqiUserId'], $jieqiConfigs['forum']['scoretopic'], true);
                    }
                } else {
                    if (!empty($jieqiConfigs['forum']['scorereply'])) {
                        $users_handler->changeScore($_SESSION['jieqiUserId'], $jieqiConfigs['forum']['scorereply'], true);
                    }
                }
                if (0 < $attachnum) {
                    $attachs_handler->db->query('UPDATE ' . jieqi_dbprefix('forum_attachs') . ' SET postid=' . $postid . ' WHERE topicid=' . $_REQUEST['tid'] . ' AND postid=0');
                    jieqi_post_attachfile($post_set, $attachary, $jieqiConfigs['forum']);
                }
                if (0 < $addnewreply) {
                    jieqi_topic_uppostadd($newPost, jieqi_dbprefix('forum_forumtopics'));
                }
                if ($forum_type == 1 && $istopic == 1) {
                    jieqi_forum_uptoptopic();
                }
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, $postresult);
                } else {
                    if (0 < $postdisplay && is_object($newTopic)) {
                        jieqi_jumppage(jieqi_geturl('forum', 'topiclist', 1, $newTopic->getVar('ownerid')), LANG_DO_SUCCESS, $postresult);
                    } else {
                        jieqi_jumppage(jieqi_geturl('forum', 'showtopic', $newPost->getVar('topicid'), -1), LANG_DO_SUCCESS, $postresult);
                    }
                }
            }
        } else {
            jieqi_printfail(implode('<br />', $check_errors));
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/include/funpost.php';
        $check_errors = array();
        jieqi_post_checkpre($jieqiConfigs['forum'], $check_errors);
        if (!empty($check_errors)) {
            jieqi_printfail(implode('<br />', $check_errors));
        }
        include_once JIEQI_ROOT_PATH . '/header.php';
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
        $jieqiTpl->assign('forumid', $forum->getVar('forumid'));
        $jieqiTpl->assign('forumname', $forum->getVar('forumname'));
        if (empty($_REQUEST['tid'])) {
            $forumtitle = $jieqiLang['forum']['post_new'];
            $tmpvar = true;
            $jieqiTpl->assign('topicid', 0);
            $jieqiTpl->assign('topictitle', '');
        } else {
            $forumtitle = $jieqiLang['forum']['post_reply'];
            $tmpvar = false;
            include_once $jieqiModules['forum']['path'] . '/class/forumtopics.php';
            $topic_handler = JieqiForumtopicsHandler::getInstance('JieqiForumtopicsHandler');
            $topic = $topic_handler->get($_REQUEST['tid']);
            if (!$topic) {
                jieqi_printfail($jieqiLang['forum']['topic_not_exists']);
            }
            $jieqiTpl->assign('topicid', $topic->getVar('topicid'));
            $jieqiTpl->assign('topictitle', $topic->getVar('title'));
        }
        include_once $jieqiModules['forum']['path'] . '/class/forumposts.php';
        $post_handler = JieqiForumpostsHandler::getInstance('JieqiForumpostsHandler');
        $quote = '';
        if (isset($_REQUEST['pid']) && !empty($_REQUEST['pid'])) {
            $post = $post_handler->get($_REQUEST['pid']);
            $tmpstr = $post->getVar('posttext', 'e');
            $i = strpos($tmpstr, '[/quote]');
            if ($i != false) {
                $tmpstr = substr($tmpstr, $i + 8);
            }
            if (is_object($post)) {
                $quote = '[quote]' . jieqi_substr($tmpstr, 0, $jieqiConfigs['forum']['quotelength']) . '[/quote]';
            }
        }
        $jieqiTpl->assign('posttext', $quote);
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs');
        }
        $jieqiTpl->assign('postcheckcode', $jieqiConfigs['system']['postcheckcode']);
        include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
        $post_form = new JieqiThemeForm($forumtitle, 'frmpost', $jieqiModules['forum']['url'] . '/newpost.php');
        $post_form->setExtra('enctype="multipart/form-data"');
        $post_form->addElement(new JieqiFormText($jieqiLang['forum']['table_forumtopics_topictitle'], 'topictitle', 60, 60), $tmpvar);
        $post_form->addElement(new JieqiFormDhtmlTextArea($jieqiLang['forum']['table_forumposts_posttext'], 'posttext', $quote, 12, 60), true);
        if (0 < $jieqiConfigs['system']['postcheckcode']) {
            if (!isset($jieqiLang['system']['post'])) {
                jieqi_loadlang('post', 'system');
            }
            $checkcode = new JieqiFormText($jieqiLang['system']['post_checkcode_label'], 'checkcode', 8, 8);
            $checkcode->setExtra('onfocus="if($_(\'p_imgccode\').style.display == \'none\'){$_(\'p_imgccode\').src = \'' . JIEQI_URL . '/checkcode.php\';$_(\'p_imgccode\').style.display = \'\';}" title="' . $jieqiLang['system']['post_checkcode_ctitle'] . '"');
            $checkcode->setDescription(sprintf($jieqiLang['system']['post_checkcode_code'], JIEQI_URL, JIEQI_URL));
            $post_form->addElement($checkcode, true);
        }
        if ($authupload && is_numeric($jieqiConfigs['forum']['maxattachnum']) && 0 < $jieqiConfigs['forum']['maxattachnum']) {
            $post_form->addElement(new JieqiFormLabel($jieqiLang['forum']['attach_limit'], $jieqiLang['forum']['attach_filetype'] . $jieqiConfigs['forum']['attachtype'] . ', ' . $jieqiLang['forum']['attach_image_max'] . $jieqiConfigs['forum']['maximagesize'] . 'K, ' . $jieqiLang['forum']['attach_file_max'] . $jieqiConfigs['forum']['maxfilesize'] . 'K'));
            $maxfilenum = intval($jieqiConfigs['forum']['maxattachnum']);
            for ($i = 1; $i <= $maxfilenum; $i++) {
                $post_form->addElement(new JieqiFormFile($jieqiLang['forum']['post_attach'] . $i, 'attachfile[]', 60));
            }
        }
        $post_form->addElement(new JieqiFormHidden('fid', $_REQUEST['fid']));
        if (!empty($_REQUEST['tid'])) {
            $post_form->addElement(new JieqiFormHidden('tid', $_REQUEST['tid']));
        }
        $post_form->addElement(new JieqiFormHidden('act', 'newpost'));
        $post_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $post_form->addElement(new JieqiFormButton('&nbsp;', 'btnpost', $jieqiLang['forum']['post_button'], 'submit'));
        $jieqiTpl->assign('postform', $post_form->render(JIEQI_FORM_MAX));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['forum']['path'] . '/templates/newpost.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}