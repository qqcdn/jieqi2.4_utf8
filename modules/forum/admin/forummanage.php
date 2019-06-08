<?php

define('JIEQI_MODULE_NAME', 'forum');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['forum']['manageforum'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
include_once $jieqiModules['forum']['path'] . '/class/forums.php';
$forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
$forum = $forums_handler->get($_REQUEST['id']);
if (!is_object($forum)) {
    jieqi_printfail($jieqiLang['forum']['forum_not_exists']);
} else {
    if (!isset($_POST['act'])) {
        $_POST['act'] = 'edit';
    }
    switch ($_POST['act']) {
        case 'update':
            $_POST['forumname'] = trim($_POST['forumname']);
            $_POST['forumdesc'] = trim($_POST['forumdesc']);
            $errtext = '';
            if (strlen($_POST['forumname']) == 0) {
                $errtext .= $jieqiLang['forum']['need_forum_name'] . '<br />';
            }
            if (empty($errtext)) {
                $forum->setVar('forumname', $_POST['forumname']);
                $forum->setVar('catid', $_POST['catid']);
                $forum->setVar('forumorder', $_POST['forumorder']);
                $forum->setVar('forumdesc', $_POST['forumdesc']);
                $forum->setVar('authview', serialize($_POST['authview']));
                $forum->setVar('authread', serialize($_POST['authread']));
                $forum->setVar('authpost', serialize($_POST['authpost']));
                $forum->setVar('authreply', serialize($_POST['authreply']));
                $forum->setVar('authupload', serialize($_POST['authupload']));
                $forum->setVar('authedit', serialize($_POST['authedit']));
                $forum->setVar('authdelete', serialize($_POST['authdelete']));
                $_POST['master'] = trim($_POST['master']);
                $masterstr = '';
                if ($_POST['master'] != '') {
                    $tmpary = explode(' ', $_POST['master']);
                    $qstr = '';
                    foreach ($tmpary as $v) {
                        if ($v != '') {
                            if ($qstr != '') {
                                $qstr .= ', ';
                            }
                            $qstr .= '\'' . jieqi_dbslashes($v) . '\'';
                        }
                    }
                    $masterary = array();
                    $i = 0;
                    if ($qstr != '') {
                        include_once JIEQI_ROOT_PATH . '/class/users.php';
                        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                        $sql = 'SELECT uid, uname, name FROM ' . jieqi_dbprefix('system_users') . ' WHERE uname IN (' . $qstr . ') OR name IN (' . $qstr . ')';
                        $res = $users_handler->db->query($sql);
                        while ($v = $users_handler->getObject($res)) {
                            $masterary[$i]['uid'] = $v->getVar('uid', 'n');
                            $masterary[$i]['uname'] = $v->getVar('name', 'n');
                            if (strlen($masterary[$i]['uname']) == 0) {
                                $masterary[$i]['uname'] = $v->getVar('uname', 'n');
                            }
                            $i++;
                        }
                    }
                    $masterstr = serialize($masterary);
                }
                $forum->setVar('master', $masterstr);
                if (!$forums_handler->insert($forum)) {
                    jieqi_printfail($jieqiLang['forum']['save_forum_failure']);
                } else {
                    include_once $jieqiModules['forum']['path'] . '/include/upforumset.php';
                    jieqi_jumppage($jieqiModules['forum']['url'] . '/admin/forumlist.php', LANG_DO_SUCCESS, $jieqiLang['forum']['save_forum_success']);
                }
            } else {
                jieqi_printfail($errtext);
            }
            break;
        case 'edit':
        default:
            include_once JIEQI_ROOT_PATH . '/admin/header.php';
            include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
            $edit_form = new JieqiThemeForm($jieqiLang['forum']['forum_manage'], 'forummanage', $jieqiModules['forum']['url'] . '/admin/forummanage.php');
            $edit_form->addElement(new JieqiFormText($jieqiLang['forum']['table_forums_forumname'], 'forumname', 25, 15, $forum->getVar('forumname', 'e')), true);
            include_once $jieqiModules['forum']['path'] . '/class/forumcat.php';
            $criteria = new CriteriaCompo();
            $criteria->setSort('catorder');
            $criteria->setOrder('ASC');
            $forumcat_handler = JieqiForumcatHandler::getInstance('JieqiForumcatHandler');
            $forumcat_handler->queryObjects($criteria);
            $forumcats = array();
            $i = 0;
            while ($v = $forumcat_handler->getObject()) {
                $forumcats[$i]['catid'] = $v->getVar('catid');
                $forumcats[$i]['cattitle'] = $v->getVar('cattitle');
                $i++;
            }
            unset($criteria);
            $sort_select = new JieqiFormSelect($jieqiLang['forum']['table_forums_catid'], 'catid', $forum->getVar('catid'));
            foreach ($forumcats as $forumcat) {
                $sort_select->addOption($forumcat['catid'], $forumcat['cattitle']);
            }
            $edit_form->addElement($sort_select);
            $edit_form->addElement(new JieqiFormText($jieqiLang['forum']['table_forums_forumorder'], 'forumorder', 25, 15, $forum->getVar('forumorder', 'e')), true);
            $edit_form->addElement(new JieqiFormTextArea($jieqiLang['forum']['table_forums_forumdesc'], 'forumdesc', $forum->getVar('forumdesc', 'e'), 6, 60));
            $masterstr = '';
            $masterary = jieqi_unserialize($forum->getVar('master', 'n'));
            if (is_array($masterary) && 0 < count($masterary)) {
                foreach ($masterary as $v) {
                    if ($v['uname'] != '') {
                        if (!empty($masterstr)) {
                            $masterstr .= ' ';
                        }
                        $masterstr .= $v['uname'];
                    }
                }
            }
            $masterelement = new JieqiFormText($jieqiLang['forum']['forum_master'], 'master', 45, 999, jieqi_htmlstr($masterstr, ENT_QUOTES));
            $masterelement->setDescription($jieqiLang['forum']['forum_master_note']);
            $edit_form->addElement($masterelement);
            $viewary = jieqi_unserialize($forum->getVar('authview', 'n'));
            if (!is_array($viewary)) {
                $viewary = array();
            }
            $authview = new JieqiFormCheckBox($jieqiLang['forum']['auth_view'], 'authview', $viewary);
            foreach ($jieqiGroups as $k => $v) {
                if ($k != JIEQI_GROUP_ADMIN) {
                    $authview->addOption($k, $v);
                }
            }
            $edit_form->addElement($authview);
            $readary = jieqi_unserialize($forum->getVar('authread', 'n'));
            if (!is_array($readary)) {
                $readary = array();
            }
            $authread = new JieqiFormCheckBox($jieqiLang['forum']['auth_read'], 'authread', $readary);
            foreach ($jieqiGroups as $k => $v) {
                if ($k != JIEQI_GROUP_ADMIN) {
                    $authread->addOption($k, $v);
                }
            }
            $edit_form->addElement($authread);
            $postary = jieqi_unserialize($forum->getVar('authpost', 'n'));
            if (!is_array($postary)) {
                $postary = array();
            }
            $authpost = new JieqiFormCheckBox($jieqiLang['forum']['auth_post'], 'authpost', $postary);
            foreach ($jieqiGroups as $k => $v) {
                if ($k != JIEQI_GROUP_ADMIN) {
                    $authpost->addOption($k, $v);
                }
            }
            $edit_form->addElement($authpost);
            $replyary = jieqi_unserialize($forum->getVar('authreply', 'n'));
            if (!is_array($replyary)) {
                $replyary = array();
            }
            $authreply = new JieqiFormCheckBox($jieqiLang['forum']['auth_reply'], 'authreply', $replyary);
            foreach ($jieqiGroups as $k => $v) {
                if ($k != JIEQI_GROUP_ADMIN) {
                    $authreply->addOption($k, $v);
                }
            }
            $edit_form->addElement($authreply);
            $uploadary = jieqi_unserialize($forum->getVar('authupload', 'n'));
            if (!is_array($uploadary)) {
                $uploadary = array();
            }
            $authupload = new JieqiFormCheckBox($jieqiLang['forum']['auth_upload'], 'authupload', $uploadary);
            foreach ($jieqiGroups as $k => $v) {
                if ($k != JIEQI_GROUP_ADMIN) {
                    $authupload->addOption($k, $v);
                }
            }
            $edit_form->addElement($authupload);
            $editary = jieqi_unserialize($forum->getVar('authedit', 'n'));
            if (!is_array($editary)) {
                $editary = array();
            }
            $authedit = new JieqiFormCheckBox($jieqiLang['forum']['auth_edit'], 'authedit', $editary);
            foreach ($jieqiGroups as $k => $v) {
                if ($k != JIEQI_GROUP_ADMIN) {
                    $authedit->addOption($k, $v);
                }
            }
            $edit_form->addElement($authedit);
            $deleteary = jieqi_unserialize($forum->getVar('authdelete', 'n'));
            if (!is_array($deleteary)) {
                $deleteary = array();
            }
            $authdelete = new JieqiFormCheckBox($jieqiLang['forum']['auth_delete'], 'authdelete', $deleteary);
            foreach ($jieqiGroups as $k => $v) {
                if ($k != JIEQI_GROUP_ADMIN) {
                    $authdelete->addOption($k, $v);
                }
            }
            $edit_form->addElement($authdelete);
            $edit_form->addElement(new JieqiFormHidden('id', $_REQUEST['id']));
            $edit_form->addElement(new JieqiFormHidden('act', 'update'));
            $edit_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
            $edit_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['forum']['save_auth_button'], 'submit'));
            $jieqiTpl->setCaching(0);
            $jieqiTpl->assign('jieqi_contents', $edit_form->render(JIEQI_FORM_MAX));
            include_once JIEQI_ROOT_PATH . '/admin/footer.php';
            break;
    }
}