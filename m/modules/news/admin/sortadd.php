<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['news']['managecategory'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
include_once JIEQI_ROOT_PATH . '/include/clssort.php';
$sortobj = new JieqiSort(jieqi_dbprefix('news_sort'));
switch ($_POST['act']) {
    case 'sortadd':
        jieqi_checkpost();
        jieqi_loadlang('sort', JIEQI_MODULE_NAME);
        $_POST['parentid'] = intval($_POST['parentid']);
        $_POST['sortname'] = trim($_POST['sortname']);
        $errtext = '';
        if (strlen($_POST['sortname']) == 0) {
            $errtext .= $jieqiLang['news']['sort_need_name'] . '<br />';
        }
        if ($_POST['parentid'] != 0) {
            if (!$sortobj->getSort($_POST['parentid'])) {
                $errtext .= $jieqiLang['news']['sort_parent_notexists'] . '<br />';
            }
        }
        if (empty($errtext)) {
            $params = array('parentid' => $_POST['parentid'], 'sortname' => $_POST['sortname']);
            if ($sortobj->addSort($params)) {
                jieqi_jumppage($jieqiModules['news']['url'] . '/admin/sortlist.php', '', '', true);
            } else {
                jieqi_printfail($jieqiLang['news']['sort_add_failure']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
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
        if (!isset($_REQUEST['parentid']) || !is_numeric($_REQUEST['parentid'])) {
            $_REQUEST['parentid'] = 0;
        } else {
            $_REQUEST['parentid'] = intval($_REQUEST['parentid']);
        }
        $jieqiTpl->assign('parentid', $_REQUEST['parentid']);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['news']['path'] . '/templates/admin/sortadd.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}