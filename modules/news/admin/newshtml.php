<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower[JIEQI_MODULE_NAME]['newshtml'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
@set_time_limit(0);
@session_write_close();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
jieqi_loadlang('content', JIEQI_MODULE_NAME);
include_once $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/include/formcheck.php';
if (isset($_POST['method']) && $_POST['method'] == 'create') {
    if (!$_POST['newstemplate']) {
        jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['news_template_error']);
    }
    if (!file_exists($jieqiModules[JIEQI_MODULE_NAME]['path'] . '/templates/' . $_POST['newstemplate'])) {
        jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['file_not_found']);
    }
    include_once JIEQI_ROOT_PATH . '/header.php';
    if (intval($_POST['htmltype']) == 1) {
        if (!jieqinumericcheck(1, 5, $_POST['categoryid'])) {
            jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['category_id_error']);
        }
        if (intval($_POST['sort']) == '0') {
            include_once $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/include/classcategory.php';
            $sortobj = new JieqiSort(jieqi_dbprefix('news_category'));
            $category = $sortobj->getSorts($_POST['categoryid']);
            $filecount = count($category);
            include_once $jieqiModules['news']['path'] . '/include/funstatic.php';
            foreach ($category as $category) {
                $where = 'categoryid = ' . $category;
                if (news_make_binfo(true, false, $where) === false) {
                    jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['create_html_failure']);
                }
            }
        } else {
            if (intval($_POST['sort']) == '1') {
                include_once $jieqiModules['news']['path'] . '/include/funstatic.php';
                if (news_makescategory(intval($_POST['categoryid'])) === false) {
                    jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['create_html_failure']);
                }
            }
        }
    } else {
        if (intval($_POST['htmltype']) == 2) {
            $_POST['newsdate'] = jieqidatecheck($_POST['newsdate']) ? $_POST['newsdate'] : jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['news_newsdate_error']);
            include_once $jieqiModules['news']['path'] . '/include/funstatic.php';
            if (intval($_POST['sort']) == '0') {
                $where = ' newsdate = \'' . jieqi_dbslashes(trim($_POST['newsdate'])) . '\' ORDER BY `newsdate` ASC ';
                if (news_make_binfo(true, false, $where) === false) {
                    jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['create_html_failure']);
                }
            } else {
                if (intval($_POST['sort']) == '1') {
                    jieqi_includedb();
                    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
                    $sql = 'SELECT categoryid FROM ' . jieqi_dbprefix('news_topic') . ' WHERE ';
                    $sql .= ' newsdate = \'' . jieqi_dbslashes(trim($_POST['newsdate'])) . '\' ORDER BY `newsdate` ASC ';
                    $query->execute($sql);
                    $aids = array();
                    while ($row = $query->getRow()) {
                        $aids[] = $row['categoryid'];
                    }
                    $category = array_unique($aids);
                    $filecount = count($category);
                    include_once $jieqiModules['news']['path'] . '/include/funstatic.php';
                    foreach ($category as $category) {
                        if (news_makescategory($category) === false) {
                            jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['create_html_failure']);
                        }
                    }
                }
            }
        } else {
            exit;
        }
    }
    jieqi_jumppage($jieqiModules[JIEQI_MODULE_NAME]['url'] . '/admin/makefake.php', LANG_DO_SUCCESS, sprintf($jieqiLang[JIEQI_MODULE_NAME]['create_html_success'], $filecount));
}
$jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['news']));
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/templates/admin/makefake.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';