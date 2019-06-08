<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminconfig'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('honors', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
include_once JIEQI_ROOT_PATH . '/class/honors.php';
$honors_handler = JieqihonorsHandler::getInstance('JieqihonorsHandler');
if (!isset($_POST['act']) && isset($_GET['act']) && in_array($_GET['act'], array('show', 'edit'))) {
    $_POST['act'] = $_GET['act'];
}
if (empty($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'new':
        jieqi_checkpost();
        $errtext = '';
        if (empty($_POST['caption'])) {
            $errtext .= $jieqiLang['system']['need_honor_caption'] . '<br />';
        }
        if (!is_numeric($_POST['minscore'])) {
            $errtext .= $jieqiLang['system']['need_minscore_num'] . '<br />';
        }
        if (!is_numeric($_POST['maxscore'])) {
            $errtext .= $jieqiLang['system']['need_maxscore_num'] . '<br />';
        }
        $_POST['minscore'] = intval($_POST['minscore']);
        $_POST['maxscore'] = intval($_POST['maxscore']);
        if ($_POST['maxscore'] < $_POST['minscore']) {
            $errtext .= $jieqiLang['system']['max_than_min'] . '<br />';
        }
        if (empty($errtext)) {
            $honors = $honors_handler->create();
            $honors->setVar('caption', $_POST['caption']);
            $honors->setVar('minscore', $_POST['minscore']);
            $honors->setVar('maxscore', $_POST['maxscore']);
            $honors->setVar('setting', '');
            $honors->setVar('honortype', '0');
            if (!$honors_handler->insert($honors)) {
                jieqi_printfail($jieqiLang['system']['add_honor_failure']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'delete':
        jieqi_checkpost();
        if (!empty($_REQUEST['id'])) {
            $honors_handler->delete($_REQUEST['id']);
        }
        jieqi_jumppage(JIEQI_URL . '/admin/honors.php', '', '', true);
        break;
    case 'update':
        jieqi_checkpost();
        if (!empty($_REQUEST['id']) && !empty($_POST['caption'])) {
            $honors = $honors_handler->get($_REQUEST['id']);
            if (is_object($honors)) {
                $errtext = '';
                if (empty($_POST['caption'])) {
                    $errtext .= $jieqiLang['system']['need_honor_caption'] . '<br />';
                }
                if (!is_numeric($_POST['minscore'])) {
                    $errtext .= $jieqiLang['system']['need_minscore_num'] . '<br />';
                }
                if (!is_numeric($_POST['maxscore'])) {
                    $errtext .= $jieqiLang['system']['need_maxscore_num'] . '<br />';
                }
                $_POST['minscore'] = intval($_POST['minscore']);
                $_POST['maxscore'] = intval($_POST['maxscore']);
                if ($_POST['maxscore'] < $_POST['minscore']) {
                    $errtext .= $jieqiLang['system']['max_than_min'] . '<br />';
                }
                if (empty($errtext)) {
                    $honors->setVar('caption', $_POST['caption']);
                    $honors->setVar('minscore', $_POST['minscore']);
                    $honors->setVar('maxscore', $_POST['maxscore']);
                    if (!$honors_handler->insert($honors)) {
                        jieqi_printfail($jieqiLang['system']['edit_honor_failure']);
                    }
                } else {
                    jieqi_printfail($errtext);
                }
            }
        }
        break;
    case 'edit':
        if (!empty($_REQUEST['id'])) {
            $honors = $honors_handler->get($_REQUEST['id']);
            if (is_object($honors)) {
                include_once JIEQI_ROOT_PATH . '/admin/header.php';
                $honors_form = new JieqiThemeForm($jieqiLang['system']['edit_honor'], 'honorsedit', JIEQI_URL . '/admin/honors.php');
                $honors_form->addElement(new JieqiFormText($jieqiLang['system']['table_honors_caption'], 'caption', 30, 250, $honors->getVar('caption', 'e')), true);
                $honors_form->addElement(new JieqiFormTextArea($jieqiLang['system']['table_honors_minscore'], 'minscore', $honors->getVar('minscore', 'e'), 5, 50));
                $honors_form->addElement(new JieqiFormTextArea($jieqiLang['system']['table_honors_maxscore'], 'maxscore', $honors->getVar('maxscore', 'e'), 5, 50));
                $honors_form->addElement(new JieqiFormHidden('act', 'update'));
                $honors_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
                $honors_form->addElement(new JieqiFormHidden('id', $_REQUEST['id']));
                $honors_form->addElement(new JieqiFormButton('&nbsp;', 'submit', LANG_SAVE, 'submit'));
                $jieqiTpl->assign('jieqi_contents', '<br />' . $honors_form->render(JIEQI_FORM_MAX) . '<br />');
                include_once JIEQI_ROOT_PATH . '/admin/footer.php';
                exit;
            }
        }
        break;
}
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$criteria = new CriteriaCompo();
$criteria->setSort('minscore');
$criteria->setOrder('ASC');
$honors_handler->queryObjects($criteria);
$honors = array();
$honorary = array();
$i = 0;
while ($v = $honors_handler->getObject()) {
    $nameary = explode(' ', $v->getVar('caption'));
    $honorary[$v->getVar('honorid')] = array('caption' => $nameary[0], 'name' => $nameary, 'minscore' => $v->getVar('minscore'), 'maxscore' => $v->getVar('maxscore'));
    $honors[$i]['honorid'] = $v->getVar('honorid');
    $honors[$i]['caption'] = implode('<br />', $nameary);
    $honors[$i]['minscore'] = $v->getVar('minscore');
    $honors[$i]['maxscore'] = $v->getVar('maxscore');
    $honors[$i]['honortype'] = $v->getVar('honortype');
    $i++;
}
$jieqiTpl->assign_by_ref('honors', $honors);
$honors_form = new JieqiThemeForm($jieqiLang['system']['add_honor'], 'honorsnew', JIEQI_URL . '/admin/honors.php');
$honors_form->addElement(new JieqiFormText($jieqiLang['system']['table_honors_caption'], 'caption', 30, 250, ''), true);
$honors_form->addElement(new JieqiFormText($jieqiLang['system']['table_honors_minscore'], 'minscore', 30, 50, ''), true);
$honors_form->addElement(new JieqiFormText($jieqiLang['system']['table_honors_maxscore'], 'maxscore', 30, 50, ''), true);
$honors_form->addElement(new JieqiFormHidden('act', 'new'));
$honors_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
$honors_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['system']['add_honor'], 'submit'));
$jieqiTpl->assign('form_addhonor', '<br />' . $honors_form->render(JIEQI_FORM_MAX) . '<br />');
if ((!empty($_REQUEST['id']) || !empty($_POST['caption'])) && 0 < count($honorary)) {
    jieqi_setconfigs('honors', 'jieqiHonors', $honorary, 'system');
}
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/honors.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';