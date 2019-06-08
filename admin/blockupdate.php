<?php

if (empty($_GET['mod'])) {
    $_GET['mod'] = 'system';
}
define('JIEQI_MODULE_NAME', $_GET['mod']);
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars($_GET['mod']);
jieqi_checkpower($jieqiPower[$_GET['mod']]['adminblock'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('blocks', JIEQI_MODULE_NAME);
if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    include_once JIEQI_ROOT_PATH . '/class/blocks.php';
    $blocks_handler = JieqiBlocksHandler::getInstance('JieqiBlocksHandler');
    $block = $blocks_handler->get($_REQUEST['id']);
    if (!is_object($block)) {
        jieqi_printfail($jieqiLang['system']['block_not_exists']);
    }
    $blockSet = array('bid' => $block->getVar('bid'), 'blockname' => $block->getVar('blockname'), 'module' => $block->getVar('modname'), 'filename' => $block->getVar('filename', 'n'), 'classname' => $block->getVar('classname', 'n'), 'side' => $block->getVar('side', 'n'), 'title' => $block->getVar('title', 'n'), 'vars' => $block->getVar('vars', 'n'), 'template' => $block->getVar('template', 'n'), 'contenttype' => $block->getVar('contenttype', 'n'), 'custom' => $block->getVar('custom', 'n'), 'publish' => $block->getVar('publish', 'n'), 'hasvars' => $block->getVar('hasvars', 'n'));
} else {
    if (!empty($_REQUEST['module']) && preg_match('/^\\w+$/', $_REQUEST['module']) && !empty($_REQUEST['filename']) && preg_match('/^\\w+$/', $_REQUEST['filename']) && !empty($_REQUEST['key']) && is_numeric($_REQUEST['key'])) {
        unset($jieqiBlocks);
        jieqi_getconfigs($_REQUEST['module'], $_REQUEST['filename'], 'jieqiBlocks');
        if (!isset($jieqiBlocks)) {
            jieqi_printfail($jieqiLang['system']['block_config_notexists']);
        }
        if (!isset($jieqiBlocks[$_REQUEST['key']])) {
            jieqi_printfail($jieqiLang['system']['block_not_exists']);
        }
        $blockSet = $jieqiBlocks[$_REQUEST['key']];
    } else {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
}
include_once JIEQI_ROOT_PATH . '/header.php';
$modname = $blockSet['module'];
$filename = $blockSet['filename'];
if ($blockSet['custom'] == 1) {
    include_once JIEQI_ROOT_PATH . '/blocks/block_custom.php';
} else {
    if (preg_match('/^\\w+$/is', $filename)) {
        if ($modname == 'system') {
            include_once JIEQI_ROOT_PATH . '/blocks/' . $filename . '.php';
        } else {
            include_once $jieqiModules[$modname]['path'] . '/blocks/' . $filename . '.php';
        }
    }
}
$classname = $blockSet['classname'];
include_once JIEQI_ROOT_PATH . '/lib/template/template.php';
$jieqiTpl = JieqiTpl::getInstance();
$cblock = new $classname($blockSet);
$cblock->updateContent();
jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['system']['block_edit_success'], jieqi_htmlstr($blockSet['blockname'])));