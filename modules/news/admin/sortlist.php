<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['news']['managecategory'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
include_once JIEQI_ROOT_PATH . '/include/clssort.php';
$sortobj = new JieqiSort(jieqi_dbprefix('news_sort'));
if (!empty($_POST['act'])) {
    jieqi_checkpost();
    switch ($_POST['act']) {
        case 'del':
            if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
                $sortobj->deleteSort($_REQUEST['id']);
            }
            jieqi_jumppage($jieqiModules['news']['url'] . '/admin/sortlist.php', '', '', true);
            break;
        case 'order':
            if (!empty($_REQUEST['sortorder']) && is_array($_REQUEST['sortorder'])) {
                $sortobj->updateOrder($_REQUEST['sortorder']);
            }
            jieqi_jumppage($jieqiModules['news']['url'] . '/admin/sortlist.php', '', '', true);
            break;
    }
}
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$sortrows = $sortobj->getChilds(0, false);
foreach ($sortrows as $k => $v) {
    foreach ($v as $field => $value) {
        if (!is_numeric($value)) {
            $sortrows[$k][$field] = jieqi_htmlstr($sortrows[$k][$field]);
        }
    }
}
$jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $sortrows));
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['news']['path'] . '/templates/admin/sortlist.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';