<?php

define('JIEQI_MODULE_NAME', 'obook');
define('JIEQI_USE_GZIP', '0');
define('JIEQI_NOCONVERT_CHAR', '1');
@set_time_limit(600);
@ini_set('memory_limit', '32M');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['obook']['manageallobook'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
jieqi_loadlang('obook', JIEQI_MODULE_NAME);
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler =& JieqiObookHandler::getInstance('JieqiObookHandler');
$obook = $obook_handler->get($_REQUEST['id']);
if (!is_object($obook)) {
    jieqi_printfail($jieqiLang['obook']['obook_not_exists']);
}
if (0 < $obook->getVar('sourceid', 'n')) {
    jieqi_printfail($jieqiLang['obook']['agentbook_cant_txtdown']);
}
header('Content-type: text/plain');
header('Accept-Ranges: bytes');
if ($_REQUEST['fname'] == 'id') {
    header('Content-Disposition: attachment; filename=' . $_REQUEST['id'] . '.txt');
} else {
    header('Content-Disposition: attachment; filename=' . jieqi_headstr($obook->getVar('obookname')) . '.txt');
}
$br = "\r\n";
if (!empty($jieqiConfigs['obook']['txtobookhead'])) {
    echo $jieqiConfigs['obook']['txtobookhead'] . $br . $br;
}
echo '《' . $obook->getVar('obookname') . '》' . $br;
$volume = '';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$criteria = new CriteriaCompo(new Criteria('o.obookid', $_REQUEST['id']));
$criteria->setTables(jieqi_dbprefix('obook_ochapter') . ' o LEFT JOIN ' . jieqi_dbprefix('obook_ocontent') . ' c ON o.ochapterid=c.ochapterid');
$criteria->setFields('o.*, c.*');
$criteria->setSort('o.chapterorder');
$criteria->setOrder('ASC');
$query->queryObjects($criteria);
while ($v = $query->getObject()) {
    if ($v->getVar('chaptertype', 'n') == 1) {
        $volume = $v->getVar('chaptername', 'n');
    } else {
        echo $br . $br . $volume . ' ' . $v->getVar('chaptername', 'n') . $br . $br;
        echo $v->getVar('ocontent', 'n');
        ob_flush();
        flush();
    }
}
if (!empty($jieqiConfigs['obook']['txtobookfoot'])) {
    echo $jieqiConfigs['obook']['txtobookfoot'];
}