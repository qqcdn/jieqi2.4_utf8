<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminblock'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('blocks', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/admin/header.php';
include_once JIEQI_ROOT_PATH . '/class/blocks.php';
$blocks_handler = JieqiBlocksHandler::getInstance('JieqiBlocksHandler');
$updatefile = false;
if (isset($_POST['act']) && !empty($_POST['act'])) {
    switch ($_POST['act']) {
        case 'new':
            jieqi_checkpost();
            $_POST['blockname'] = trim($_POST['blockname']);
            $_POST['modname'] = trim($_POST['modname']);
            $errtext = '';
            if (strlen($_POST['blockname']) == 0) {
                $errtext .= $jieqiLang['system']['need_block_name'] . '<br />';
            }
            if (strlen($_POST['modname']) == 0) {
                $errtext .= $jieqiLang['system']['need_block_modname'] . '<br />';
            }
            if (empty($errtext)) {
                $newblock = $blocks_handler->create();
                $newblock->setVar('blockname', $_POST['blockname']);
                $newblock->setVar('modname', $_POST['modname']);
                $newblock->setVar('filename', '');
                $newblock->setVar('classname', 'BlockSystemCustom');
                $newblock->setVar('side', $_POST['side']);
                $newblock->setVar('title', $_POST['title']);
                $newblock->setVar('description', '');
                $newblock->setVar('content', $_POST['content']);
                $newblock->setVar('vars', '');
                $newblock->setVar('template', '');
                $newblock->setVar('cachetime', 0);
                $newblock->setVar('contenttype', JIEQI_CONTENT_HTML);
                $newblock->setVar('weight', $_POST['weight']);
                $newblock->setVar('showstatus', 0);
                $newblock->setVar('custom', 1);
                $newblock->setVar('canedit', 1);
                $newblock->setVar('publish', $_POST['publish']);
                $newblock->setVar('hasvars', 0);
                if (!$blocks_handler->insert($newblock)) {
                    jieqi_printfail($jieqiLang['system']['block_add_failure']);
                }
                $blocks_handler->saveContent($newblock->getVar('bid'), $_POST['modname'], JIEQI_CONTENT_HTML, $_POST['content']);
                $updatefile = true;
                $_REQUEST['modname'] = '1';
            } else {
                jieqi_printfail($errtext);
            }
            break;
        case 'update':
            jieqi_checkpost();
            if (empty($_REQUEST['id'])) {
                jieqi_printfail($jieqiLang['system']['block_not_exists']);
            }
            if (isset($_POST['modname']) && !preg_match('/^[\\w]*$/', $_POST['modname'])) {
                jieqi_printfail($jieqiLang['system']['block_modname_error']);
            }
            if (isset($_POST['blocktemplate']) && !preg_match('/^[\\w\\.]*$/', $_POST['blocktemplate'])) {
                jieqi_printfail($jieqiLang['system']['block_template_errorformat']);
            }
            $block = $blocks_handler->get($_REQUEST['id']);
            if (is_object($block)) {
                $block->setVar('side', $_POST['side']);
                $block->setVar('title', $_POST['title']);
                $stype = 0;
                $block->setVar('weight', $_POST['weight']);
                $block->setVar('publish', $_POST['publish']);
                $_POST['blockname'] = trim($_POST['blockname']);
                if (!empty($_POST['blockname'])) {
                    $block->setVar('blockname', $_POST['blockname']);
                }
                if ($block->getVar('custom') == 1) {
                    $modename = trim($_POST['modname']);
                    if (!empty($_POST['modname'])) {
                        $block->setVar('modname', $_POST['modname']);
                    }
                    $block->setVar('contenttype', JIEQI_CONTENT_HTML);
                }
                if ($block->getVar('canedit') == 1) {
                    $block->setVar('content', $_POST['content']);
                }
                if (0 < $block->getVar('hasvars')) {
                    $block->setVar('vars', trim($_POST['blockvars']));
                    $block->setVar('template', trim($_POST['blocktemplate']));
                    if (isset($_POST['savetype']) && $_POST['savetype'] == 1) {
                        $block->setNew();
                        $block->setVar('showstatus', 0);
                        $block->setVar('bid', 0);
                    }
                }
                if (!$blocks_handler->insert($block)) {
                    jieqi_printfail($jieqiLang['system']['block_edit_failure']);
                }
                if ($block->getVar('custom') == 1) {
                    $blocks_handler->saveContent($block->getVar('bid'), $block->getVar('modname'), JIEQI_CONTENT_HTML, $_POST['content']);
                }
                if (isset($_POST['cacheupdate']) && $_POST['cacheupdate'] == 1) {
                    $modname = $block->getVar('modname', 'n');
                    $filename = $block->getVar('filename', 'n');
                    if (preg_match('/^\\w+$/is', $filename)) {
                        if ($modname == 'system') {
                            include JIEQI_ROOT_PATH . '/blocks/' . $filename . '.php';
                        } else {
                            include $jieqiModules[$modname]['path'] . '/blocks/' . $filename . '.php';
                        }
                    }
                    $classname = $block->getVar('classname', 'n');
                    $vars = array('bid' => $block->getVar('bid'), 'blockname' => $block->getVar('blockname'), 'module' => $block->getVar('modname'), 'filename' => $block->getVar('filename', 'n'), 'classname' => $block->getVar('classname', 'n'), 'side' => $block->getVar('side', 'n'), 'title' => $block->getVar('title', 'n'), 'vars' => $block->getVar('vars', 'n'), 'template' => $block->getVar('template', 'n'), 'contenttype' => $block->getVar('contenttype', 'n'), 'custom' => $block->getVar('custom', 'n'), 'publish' => $block->getVar('publish', 'n'), 'hasvars' => $block->getVar('hasvars', 'n'));
                    $cblock = new $classname($vars);
                    $cblock->updateContent();
                    unset($cblock);
                    unset($vars);
                }
                $updatefile = true;
                if ($block->getVar('custom') == 1) {
                    $_REQUEST['modname'] = '1';
                } else {
                    $_REQUEST['modname'] = $block->getVar('modname', 'n');
                }
            } else {
                jieqi_printfail($jieqiLang['system']['block_not_exists']);
            }
            break;
        case 'delete':
            jieqi_checkpost();
            if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                $block = $blocks_handler->get($_REQUEST['id']);
                if (is_object($block)) {
                    if ($block->getVar('custom') == 1) {
                        if ($blocks_handler->delete($_REQUEST['id'])) {
                            $updatefile = true;
                        }
                        $_REQUEST['modname'] = '1';
                    } else {
                        if (0 < $block->getVar('hasvars')) {
                            $criteria = new CriteriaCompo(new Criteria('modname', $block->getVar('modname', 'n')));
                            $criteria->add(new Criteria('classname', $block->getVar('classname', 'n')));
                            if (1 < $blocks_handler->getCount($criteria)) {
                                if ($blocks_handler->delete($_REQUEST['id'])) {
                                    $updatefile = true;
                                }
                            } else {
                                jieqi_printfail($jieqiLang['system']['block_less_one']);
                            }
                            unset($criteria);
                            $_REQUEST['modname'] = $block->getVar('modname', 'n');
                        }
                    }
                }
            }
            break;
    }
}
include_once JIEQI_ROOT_PATH . '/class/modules.php';
$modules_handler = JieqiModulesHandler::getInstance('JieqiModulesHandler');
$criteria = new CriteriaCompo(new Criteria('publish', 1, '='));
$criteria->setSort('weight');
$criteria->setOrder('ASC');
$modules_handler->queryObjects($criteria);
unset($criteria);
$modules = array();
while ($v = $modules_handler->getObject()) {
    $modules[$v->getVar('name', 'n')] = $v->getVar('caption', 'n');
}
$modules['system'] = LANG_MODULE_SYSTEM;
$_REQUEST['modname'] = trim($_REQUEST['modname']);
$jieqiTpl->assign('modules', $_REQUEST['modname']);
$jieqiTpl->assign('modname', $_REQUEST['modname']);
$criteria = new CriteriaCompo();
if (isset($_REQUEST['modname']) && !empty($_REQUEST['modname'])) {
    if ($_REQUEST['modname'] == 1) {
        $criteria->add(new Criteria('custom', 1, '='));
    } else {
        $criteria->add(new Criteria('modname', $_REQUEST['modname'], '='));
        $criteria->add(new Criteria('custom', 0, '='));
    }
}
$criteria->setSort('modname ASC, weight');
$criteria->setOrder('ASC');
$blocks_handler->queryObjects($criteria);
$blockary = array();
$k = 0;
if (JIEQI_URL == '') {
    $site_url = 'http://' . $_SERVER['HTTP_HOST'];
} else {
    $site_url = JIEQI_URL;
}
while ($v = $blocks_handler->getObject()) {
    $blockary[$k]['bid'] = $v->getVar('bid');
    $blockary[$k]['blockname'] = $v->getVar('blockname');
    $blockary[$k]['modname'] = $modules[$v->getVar('modname', 'n')];
    $blockary[$k]['side'] = $blocks_handler->getSide($v->getVar('side', 'n'));
    $blockary[$k]['weight'] = $v->getVar('weight');
    $blockary[$k]['publish'] = $blocks_handler->getPublish($v->getVar('publish', 'n'));
    $blockary[$k]['action'] = '<a href="' . JIEQI_URL . '/admin/blockedit.php?id=' . $v->getVar('bid') . '" target="_self">' . $jieqiLang['system']['block_action_edit'] . '</a>';
    if ($v->getVar('custom') == 1) {
        $blockary[$k]['action'] .= ' <a id="act_delete_' . $v->getVar('bid') . '" href="javascript:;" onclick="act_delete(\'' . JIEQI_URL . '/admin/blocks.php?act=delete&id=' . $v->getVar('bid') . '&' . JIEQI_TOKEN_NAME . '=' . urlencode($_SESSION['jieqiUserToken']) . '\');">' . $jieqiLang['system']['block_action_delete'] . '</a>';
    } else {
        $blockary[$k]['action'] .= ' <a href="' . JIEQI_URL . '/admin/blockupdate.php?id=' . $v->getVar('bid') . '" target="_blank">' . $jieqiLang['system']['block_action_refresh'] . '</a>';
        if ($v->getVar('custom')) {
            $blockary[$k]['action'] .= ' <a href="javascript:if(confirm(\'' . $jieqiLang['system']['block_delete_cofirm'] . '\')) document.location=\'' . JIEQI_URL . '/admin/blocks.php?action=delete&id=' . $v->getVar('bid') . '\';" target="_self">' . $jieqiLang['system']['block_action_delete'] . '</a>';
        }
    }
    if (0 < $v->getVar('custom', 'n')) {
        $cfgbid = $v->getVar('bid', 'n');
    } else {
        $cfgbid = 0;
    }
    $blockary[$k]['configtext'] = jieqi_htmlchars('$jieqiBlocks[]=array(\'bid\'=>' . $cfgbid . ', \'blockname\'=>\'' . $v->getVar('blockname') . '\', \'module\'=>\'' . $v->getVar('modname', 'n') . '\', \'filename\'=>\'' . $v->getVar('filename', 'n') . '\', \'classname\'=>\'' . $v->getVar('classname', 'n') . '\', \'side\'=>' . $v->getVar('side', 'n') . ', \'title\'=>\'' . $v->getVar('title', 'n') . '\', \'vars\'=>\'' . $v->getVar('vars', 'n') . '\', \'template\'=>\'' . $v->getVar('template', 'n') . '\', \'contenttype\'=>' . $v->getVar('contenttype', 'n') . ', \'custom\'=>' . $v->getVar('custom', 'n') . ', \'publish\'=>3, \'hasvars\'=>' . $v->getVar('hasvars', 'n') . ');');
    $blockary[$k]['jstext'] = jieqi_htmlchars('<script type="text/javascript" src="' . $site_url . '/blockshow.php?bid=' . $cfgbid . '&module=' . urlencode($v->getVar('modname', 'n')) . '&filename=' . urlencode($v->getVar('filename', 'n')) . '&classname=' . urlencode($v->getVar('classname', 'n')) . '&vars=' . urlencode($v->getVar('vars', 'n')) . '&template=' . urlencode($v->getVar('template', 'n')) . '&contenttype=' . urlencode($v->getVar('contenttype', 'n')) . '&custom=' . $v->getVar('custom', 'n') . '&publish=3&hasvars=' . urlencode($v->getVar('hasvars', 'n')) . '"></script>');
    $k++;
}
$jieqiTpl->assign_by_ref('blocks', $blockary);
if ($updatefile) {
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('publish', 0, '>'));
    $criteria->setSort('weight');
    $criteria->setOrder('ASC');
    $blocks_handler->queryObjects($criteria);
    $k = 0;
    $jieqisaveBlocks = array();
    while ($v = $blocks_handler->getObject()) {
        $jieqisaveBlocks[$k] = array('bid' => $v->getVar('bid'), 'blockname' => $v->getVar('blockname'), 'module' => $v->getVar('modname'), 'filename' => $v->getVar('filename', 'n'), 'classname' => $v->getVar('classname', 'n'), 'side' => $v->getVar('side', 'n'), 'title' => $v->getVar('title', 'n'), 'vars' => $v->getVar('vars', 'n'), 'template' => $v->getVar('template', 'n'), 'contenttype' => $v->getVar('contenttype', 'n'), 'custom' => $v->getVar('custom', 'n'), 'publish' => $v->getVar('publish', 'n'), 'hasvars' => $v->getVar('hasvars', 'n'));
        $k++;
    }
    jieqi_setconfigs('blocks', 'jieqiBlocks', $jieqisaveBlocks, 'system');
}
include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
$post_url = JIEQI_URL . '/admin/blocks.php';
if (isset($_REQUEST['modname'])) {
    $post_url .= '?modname=' . urlencode($_REQUEST['modname']);
}
$blocks_form = new JieqiThemeForm($jieqiLang['system']['add_custom_block'], 'blocksnew', $post_url);
$blocks_form->addElement(new JieqiFormText($jieqiLang['system']['table_blocks_blockname'], 'blockname', 30, 50, ''), true);
$sideary = $blocks_handler->getSideary();
$sideselect = new JieqiFormSelect($jieqiLang['system']['table_blocks_side'], 'side');
$sideselect->addOptionArray($sideary);
$blocks_form->addElement($sideselect);
$blocks_form->addElement(new JieqiFormText($jieqiLang['system']['table_blocks_weight'], 'weight', 8, 8, '0'));
$showradio = new JieqiFormRadio($jieqiLang['system']['table_blocks_publish'], 'publish', 3);
$showradio->addOption(0, $jieqiLang['system']['block_show_no']);
$showradio->addOption(1, $jieqiLang['system']['block_show_logout']);
$showradio->addOption(2, $jieqiLang['system']['block_show_login']);
$showradio->addOption(3, $jieqiLang['system']['block_show_both']);
$blocks_form->addElement($showradio);
$blocks_form->addElement(new JieqiFormTextArea($jieqiLang['system']['table_blocks_title'] . '(HTML)', 'title', '', 3, 60));
$blocks_form->addElement(new JieqiFormTextArea($jieqiLang['system']['table_blocks_content'] . '(HTML格式)', 'content', '', 10, 60));
$blocks_form->addElement(new JieqiFormHidden('act', 'new'));
$blocks_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
$blocks_form->addElement(new JieqiFormHidden('modname', 'system'));
$blocks_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['system']['add_block'], 'submit'));
$jieqiTpl->assign('form_addblock', '<br />' . $blocks_form->render(JIEQI_FORM_MAX) . '<br />');
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/blocks.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';