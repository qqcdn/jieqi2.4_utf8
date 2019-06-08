<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_loadlang('users', 'system');
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT * FROM ' . jieqi_dbprefix('system_persons') . ' WHERE uid = ' . intval($_SESSION['jieqiUserId']) . ' LIMIT 0, 1';
$res = $query->execute($sql);
$persons = $query->getRow($res);
if (!$persons) {
    jieqi_jumppage(JIEQI_LOCAL_URL . '/personedit.php', LANG_DO_FAILURE, $jieqiLang['system']['persons_not_set'], true);
} else {
    include_once JIEQI_ROOT_PATH . '/header.php';
    include_once JIEQI_ROOT_PATH . '/include/funpersons.php';
    $personsvars = jieqi_system_personsvars($persons, 's');
    $jieqiTpl->assign_by_ref('personsvars', $personsvars);
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/persondetail.html';
    include_once JIEQI_ROOT_PATH . '/footer.php';
}