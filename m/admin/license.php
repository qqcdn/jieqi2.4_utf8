<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiTpl->assign('license_domain', str_replace('|', ' ', $jieqi_license_ary[1]));
if (defined('JIEQI_VERSION_TYPE') && defined('LANG_VERSION_' . strtoupper(JIEQI_VERSION_TYPE))) {
    $jieqiTpl->assign('jieqi_vtype', constant('LANG_VERSION_' . strtoupper(JIEQI_VERSION_TYPE)));
} else {
    $jieqiTpl->assign('jieqi_vtype', '');
}
include_once JIEQI_ROOT_PATH . '/class/modules.php';
$modules_handler = JieqiModulesHandler::getInstance('JieqiModulesHandler');
$criteria = new CriteriaCompo(new Criteria('publish', 1, '='));
$criteria->setSort('weight');
$criteria->setOrder('ASC');
$modules_handler->queryObjects($criteria);
unset($criteria);
$jieqiModary = array();
while ($v = $modules_handler->getObject()) {
    $jieqiModary[$v->getVar('name', 'n')] = array('name' => $v->getVar('name', 'n'), 'caption' => $v->getVar('caption', 'n'), 'description' => $v->getVar('description', 'n'), 'version' => sprintf('%0.2f', intval($v->getVar('version', 'n')) / 100), 'vtype' => $v->getVar('vtype', 'n'), 'publish' => $v->getVar('publish', 'n'));
}
if (!isset($license_ary)) {
    $license_ary = jieqi_strtosary($jieqi_license_ary[2], '=', '|');
}
$licenses = array();
$i = 0;
foreach ($jieqiModary as $k => $v) {
    $licenses[$i]['modname'] = jieqi_htmlstr($jieqiModary[$k]['caption']);
    $licenses[$i]['modversion'] = jieqi_htmlstr($jieqiModary[$k]['version']);
    if (isset($license_ary[$k])) {
        $vtype = $license_ary[$k];
    } else {
        $vtype = 'Free';
    }
    if (defined('LANG_VERSION_' . strtoupper($vtype))) {
        $licenses[$i]['modvtype'] = constant('LANG_VERSION_' . strtoupper($vtype));
    } else {
        $licenses[$i]['modvtype'] = '';
    }
    $i++;
}
$jieqiTpl->assign_by_ref('licenses', $licenses);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/license.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';