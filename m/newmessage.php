<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
if (!isset($_REQUEST['tosys']) || $_REQUEST['tosys'] != 1) {
    jieqi_checkpower($jieqiPower['system']['sendmessage'], $jieqiUsersStatus, $jieqiUsersGroup, false);
}
jieqi_loadlang('message', JIEQI_MODULE_NAME);
jieqi_getconfigs('system', 'action', 'jieqiAction');
if (0 < $jieqiAction['system']['newmessage']['minscore'] && $_SESSION['jieqiUserScore'] < $jieqiAction['system']['newmessage']['minscore']) {
    jieqi_printfail(sprintf($jieqiLang['system']['message_send_minscore'], $jieqiAction['system']['newmessage']['minscore']));
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('system', 'honors');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
$maxdaymsg = intval($jieqiConfigs['system']['maxdaymsg']);
$honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
if ($honorid && isset($jieqiRight['system']['maxdaymsg']['honors'][$honorid]) && is_numeric($jieqiRight['system']['maxdaymsg']['honors'][$honorid])) {
    $maxdaymsg = intval($jieqiRight['system']['maxdaymsg']['honors'][$honorid]);
}
if (!empty($maxdaymsg)) {
    include_once JIEQI_ROOT_PATH . '/class/users.php';
    $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    $jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
    if (!$jieqiUsers) {
        jieqi_printfail(LANG_NO_USER);
    }
    $userset = jieqi_unserialize($jieqiUsers->getVar('setting', 'n'));
    $today = date('Y-m-d');
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'input';
}
switch ($_POST['act']) {
    case 'add':
        jieqi_checkpost();
        $_REQUEST['receiver'] = trim($_REQUEST['receiver']);
        $_REQUEST['title'] = trim($_REQUEST['title']);
        $errtext = '';
        if (!isset($_REQUEST['tosys']) || empty($_REQUEST['tosys'])) {
            $_REQUEST['tosys'] = false;
        } else {
            $_REQUEST['tosys'] = true;
        }
        if (strlen($_REQUEST['receiver']) == 0 && !$_REQUEST['tosys']) {
            $errtext .= $jieqiLang['system']['message_need_receiver'] . '<br />';
        }
        if (strlen($_REQUEST['title']) == 0) {
            $errtext .= $jieqiLang['system']['message_need_title'] . '<br />';
        }
        if (empty($errtext)) {
            if (empty($_REQUEST['tosys'])) {
                if ($_REQUEST['receiver'] == $_SESSION['jieqiUserUname'] || $_REQUEST['receiver'] == $_SESSION['jieqiUserName']) {
                    jieqi_printfail($jieqiLang['system']['message_nosend_self']);
                }
                include_once JIEQI_ROOT_PATH . '/class/users.php';
                $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                $touser = $users_handler->getByname($_REQUEST['receiver'], 3);
                if (!$touser) {
                    jieqi_printfail($jieqiLang['system']['message_no_receiver']);
                }
            }
            include_once JIEQI_ROOT_PATH . '/include/funmessage.php';
            $message = array();
            $message['fromid'] = $_SESSION['jieqiUserId'];
            $message['fromname'] = $_SESSION['jieqiUserName'];
            if (empty($_REQUEST['tosys'])) {
                $message['toid'] = $touser->getVar('uid', 'n');
                $message['toname'] = $touser->getVar('name', 'n');
                $message['messagetype'] = 0;
            } else {
                $message['toid'] = 0;
                $message['toname'] = '';
                $message['messagetype'] = 1;
            }
            $message['title'] = $_REQUEST['title'];
            $message['content'] = $_REQUEST['content'];
            if (!jieqi_sendmessage($message)) {
                jieqi_printfail($jieqiLang['system']['message_send_failure']);
            } else {
                if (!empty($maxdaymsg)) {
                    if (isset($userset['msgdate']) && $userset['msgdate'] == $today) {
                        $userset['msgnum'] = (int) $userset['msgnum'] + 1;
                    } else {
                        $userset['msgdate'] = $today;
                        $userset['msgnum'] = 1;
                    }
                    $jieqiUsers->setVar('setting', serialize($userset));
                    $jieqiUsers->saveToSession();
                    $users_handler->insert($jieqiUsers);
                }
                include_once JIEQI_ROOT_PATH . '/include/funaction.php';
                $actions = array('actname' => 'newmessage', 'actnum' => 1);
                if (is_object($jieqiUsers)) {
                    jieqi_system_actiondo($actions, $jieqiUsers);
                } else {
                    jieqi_system_actiondo($actions, $_SESSION['jieqiUserId']);
                }
                if (!empty($_REQUEST['ajax_request'])) {
                    jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['message_send_seccess']);
                } else {
                    jieqi_jumppage(JIEQI_URL . '/message.php?box=outbox', LANG_DO_SUCCESS, $jieqiLang['system']['message_send_seccess']);
                }
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'input':
    default:
        if (!empty($maxdaymsg) && isset($userset['msgdate']) && $userset['msgdate'] == $today && (int) $maxdaymsg <= (int) $userset['msgnum']) {
            jieqi_printfail(sprintf($jieqiLang['system']['day_message_limit'], $maxdaymsg));
        }
        include_once JIEQI_ROOT_PATH . '/header.php';
        jieqi_getconfigs('system', 'honors');
        jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
        $maxmessage = isset($jieqiConfigs['system']['maxmessages']) ? intval($jieqiConfigs['system']['maxmessages']) : 0;
        $honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
        if ($honorid && isset($jieqiRight['system']['maxmessages']['honors'][$honorid]) && is_numeric($jieqiRight['system']['maxmessages']['honors'][$honorid])) {
            $maxmessage = intval($jieqiRight['system']['maxmessages']['honors'][$honorid]);
        }
        include_once JIEQI_ROOT_PATH . '/class/message.php';
        $message_handler = JieqiMessageHandler::getInstance('JieqiMessageHandler');
        $sql = 'SELECT COUNT(*) AS msgnum FROM ' . jieqi_dbprefix('system_message') . ' WHERE (fromid=' . $_SESSION['jieqiUserId'] . ' AND fromdel=0) OR (toid=' . $_SESSION['jieqiUserId'] . ' AND todel=0)';
        $res = $message_handler->execute($sql);
        $row = $message_handler->getRow($res);
        $nowmessage = (int) $row['msgnum'];
        if ($maxmessage <= $nowmessage) {
            $jieqiTpl->setCaching(0);
            $jieqiTpl->assign('jieqi_contents', jieqi_msgbox($jieqiLang['system']['message_is_full'], $jieqiLang['system']['message_box_full']));
        } else {
            $jieqiTpl->assign('maxdaymsg', $maxdaymsg);
            $jieqiTpl->assign('nowmessage', $nowmessage);
            $jieqiTpl->assign('maxmessage', $maxmessage);
            $message = false;
            if (!empty($_REQUEST['reid']) || !empty($_REQUEST['fwid'])) {
                include_once JIEQI_ROOT_PATH . '/class/message.php';
                $message_handler = JieqiMessageHandler::getInstance('JieqiMessageHandler');
                if (!empty($_REQUEST['reid'])) {
                    $message = $message_handler->get($_REQUEST['reid']);
                } else {
                    if (!empty($_REQUEST['fwid'])) {
                        $message = $message_handler->get($_REQUEST['fwid']);
                    }
                }
            }
            if (is_object($message)) {
                $_REQUEST['receiver'] = $message->getVar('fromname', 'e');
                $_REQUEST['title'] = $message->getVar('title', 'e');
                if (!empty($_REQUEST['reid'])) {
                    $_REQUEST['title'] = 'Re:' . $_REQUEST['title'];
                    $_REQUEST['content'] = '';
                } else {
                    if (!empty($_REQUEST['fwid'])) {
                        $_REQUEST['title'] = 'Fw:' . $_REQUEST['title'];
                        $_REQUEST['content'] = $message->getVar('content', 'e');
                    }
                }
            } else {
                if (!empty($_REQUEST['event']) && $_REQUEST['event'] == 'applywriter') {
                    if (empty($_REQUEST['title'])) {
                        $_REQUEST['title'] = $jieqiLang['system']['message_appay_writer'];
                    }
                    if (empty($_REQUEST['content'])) {
                        $_REQUEST['content'] = $jieqiLang['system']['message_apply_reason'];
                    }
                }
            }
            if (!isset($_REQUEST['receiver'])) {
                $_REQUEST['receiver'] = '';
            }
            if (!isset($_REQUEST['title'])) {
                $_REQUEST['title'] = '';
            }
            if (!isset($_REQUEST['content'])) {
                $_REQUEST['content'] = '';
            }
            if (isset($_REQUEST['tosys']) && $_REQUEST['tosys'] == 1) {
                $jieqiTpl->assign('tosys', 1);
                $jieqiTpl->assign('receiver', $jieqiLang['system']['message_site_admin']);
            } else {
                $jieqiTpl->assign('tosys', 0);
                $jieqiTpl->assign('receiver', $_REQUEST['receiver']);
            }
            $jieqiTpl->assign('title', $_REQUEST['title']);
            $jieqiTpl->assign('content', $_REQUEST['content']);
            $jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
            $jieqiTpl->setCaching(0);
            $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/newmessage.html';
        }
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}