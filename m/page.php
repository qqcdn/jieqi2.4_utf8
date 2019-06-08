<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
include_once JIEQI_ROOT_PATH . '/header.php';
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = '';
if (empty($_GET['module']) || !preg_match('/^\\w+$/', $_GET['module']) || !isset($jieqiModules[$_GET['module']])) {
    $_GET['module'] = 'system';
}
if (!isset($_GET['template'])) {
    $_GET['template'] = 'page';
}
if (isset($_GET['template']) && preg_match('/^\\w+$/', $_GET['template']) && is_file($jieqiModules[$_GET['module']]['path'] . '/templates/' . $_GET['template'] . '.html')) {
    $jieqiTset['jieqi_contents_template'] = $jieqiModules[$_GET['module']]['path'] . '/templates/' . $_GET['template'] . '.html';
}
if (!empty($_GET['bid'])) {
    $_GET['bid'] = intval($_GET['bid']);
    include_once JIEQI_ROOT_PATH . '/class/blocks.php';
    $blocks_handler = JieqiBlocksHandler::getInstance('JieqiBlocksHandler');
    $block = $blocks_handler->get($_GET['bid']);
    if (is_object($block)) {
        $blockvars = $block->getVars('n');
        $blockvars['side'] = -1;
        $blockvars['publish'] = 3;
        $blockval = jieqi_get_block($blockvars);
        $jieqiTpl->assign('blockval', $blockval);
    }
}
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
include_once JIEQI_ROOT_PATH . '/footer.php';