<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
if (JIEQI_VERSION_TYPE == '' || JIEQI_VERSION_TYPE == 'Free' || JIEQI_VERSION_TYPE == 'Popular') {
    exit('//error version type=' . JIEQI_VERSION_TYPE);
}
if (!isset($_GET['module']) || !preg_match('/^\\w+$/', $_GET['module']) || empty($jieqiModules[$_GET['module']])) {
    exit('//error parameter');
}
if (isset($_GET['configname'])) {
    if (!preg_match('/^\\w*$/', $_GET['configname']) || !is_numeric($_GET['blockindex'])) {
        exit('//error parameter');
    }
    jieqi_getconfigs($_GET['module'], $_GET['configname'], 'jieqiBlocks');
    if (!isset($jieqiBlocks[$_GET['blockindex']])) {
        exit('//error parameter');
    }
    $blockconfig = $jieqiBlocks[$_GET['blockindex']];
    if ($blockconfig['filename'] == 'block_commend' && isset($_GET['prows']) && isset($_GET['page'])) {
        $_GET['prows'] = intval($_GET['prows']);
        $_GET['page'] = intval($_GET['page']);
        $tmpary = explode('|', trim($blockconfig['vars']));
        $ids = array();
        foreach ($tmpary as $v) {
            $v = trim($v);
            if (is_numeric($v)) {
                $ids[] = intval($v);
            }
        }
        $ids = array_unique($ids);
        $idcount = count($ids);
        if (0 < $_GET['prows'] && 1 < $_GET['page'] && 0 < $idcount) {
            $idx = $_GET['prows'] * ($_GET['page'] - 1) % $idcount;
            if (0 < $idx) {
                for ($i = 0; $i < $idx; $i++) {
                    $id = array_shift($ids);
                    array_push($ids, $id);
                }
                $blockconfig['vars'] = implode('|', $ids);
            }
        }
    }
} else {
    if (isset($_GET['filename']) && !preg_match('/^\\w*$/', $_GET['filename']) || !isset($_GET['classname']) || !preg_match('/^\\w+$/', $_GET['classname']) || isset($_GET['template']) && !preg_match('/^[\\w\\.]*$/', $_GET['template'])) {
        exit('//error parameter');
    }
    $blockconfig = array();
    $blockconfig['bid'] = isset($_GET['bid']) && is_numeric($_GET['bid']) ? $_GET['bid'] : 0;
    $blockconfig['blockname'] = isset($_GET['blockname']) ? $_GET['blockname'] : '';
    $blockconfig['module'] = isset($_GET['module']) ? $_GET['module'] : 'system';
    $blockconfig['filename'] = isset($_GET['filename']) ? $_GET['filename'] : 'block_custom';
    $blockconfig['classname'] = isset($_GET['classname']) ? $_GET['classname'] : '';
    $blockconfig['side'] = isset($_GET['side']) ? intval($_GET['side']) : -1;
    $blockconfig['title'] = isset($_GET['title']) ? $_GET['title'] : '';
    $blockconfig['vars'] = isset($_GET['vars']) ? $_GET['vars'] : '';
    $blockconfig['template'] = isset($_GET['template']) ? $_GET['template'] : '';
    $blockconfig['contenttype'] = isset($_GET['contenttype']) ? intval($_GET['contenttype']) : JIEQI_CONTENT_PHP;
    $blockconfig['custom'] = $blockconfig['filename'] == 'block_custom' || $_GET['custom'] == 1 ? 1 : 0;
    $blockconfig['publish'] = isset($_GET['publish']) ? intval($_GET['publish']) : 3;
    $blockconfig['hasvars'] = isset($_GET['hasvars']) ? intval($_GET['hasvars']) : 0;
}
include_once JIEQI_ROOT_PATH . '/header.php';
$blockdata = jieqi_get_block($blockconfig, 1);
if (!empty($_REQUEST['ajax_request'])) {
    $_GET['showtype'] = 'html';
    header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
    header('Cache-Control:no-cache');
}
if ($_GET['showtype'] == 'html') {
    echo $blockdata;
} else {
    echo 'document.write(\'' . jieqi_setslashes(str_replace(array("\r", "\n"), '', $blockdata), '"') . '\');';
}