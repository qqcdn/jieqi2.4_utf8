<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../../global.php';
if (empty($_REQUEST['sortid']) || !is_numeric($_REQUEST['sortid'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['news']['managecategory'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
$_REQUEST['sortid'] = intval($_REQUEST['sortid']);
include_once JIEQI_ROOT_PATH . '/include/clssort.php';
$sortobj = new JieqiSort(jieqi_dbprefix('news_sort'));
switch ($_POST['act']) {
    case 'sortedit':
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
            $params = array('sortid' => $_REQUEST['sortid'], 'parentid' => $_POST['parentid'], 'sortname' => $_POST['sortname']);
            if ($sortobj->editSort($params)) {
                jieqi_jumppage($jieqiModules['news']['url'] . '/admin/sortlist.php', '', '', true);
            } else {
                jieqi_printfail($jieqiLang['news']['sort_edit_failure']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        jieqi_loadlang('sort', JIEQI_MODULE_NAME);
        $sortdata = $sortobj->getSort($_REQUEST['sortid'], false);
        if (!is_array($sortdata)) {
            jieqi_printfail($jieqiLang['news']['sort_not_exists']);
        }
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        foreach ($sortdata as $k => $v) {
            $sortdata[$k] = jieqi_htmlchars($sortdata[$k], ENT_QUOTES);
        }
        $jieqiTpl->assign_by_ref('sortdata', $sortdata);
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
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['news']['path'] . '/templates/admin/sortedit.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}