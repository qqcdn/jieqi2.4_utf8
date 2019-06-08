<?php

function jieqi_article_getexportrow($row)
{
    $row = jieqi_article_vars($row, false, 'n');
    $row['postday'] = date('Y-m-d', $row['postdate']);
    $row['posthour'] = date('Y-m-d H', $row['postdate']);
    $row['postminute'] = date('Y-m-d H:i', $row['postdate']);
    $row['postsecond'] = date('Y-m-d H:i:s', $row['postdate']);
    $row['lastupday'] = date('Y-m-d', $row['lastupdate']);
    $row['lastuphour'] = date('Y-m-d H', $row['lastupdate']);
    $row['lastupminute'] = date('Y-m-d H:i', $row['lastupdate']);
    $row['lastupsecond'] = date('Y-m-d H:i:s', $row['lastupdate']);
    return $row;
}
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs('article', 'configs');
jieqi_getconfigs('article', 'sort');
jieqi_getconfigs('article', 'option', 'jieqiOption');
jieqi_getconfigs('article', 'export', 'jieqiExport');
if (empty($_POST['act']) || $_POST['act'] != 'export') {
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/articleexport.html';
    include_once JIEQI_ROOT_PATH . '/admin/header.php';
    $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
    foreach ($jieqiOption['article'] as $k => $v) {
        $jieqiTpl->assign($k, $jieqiOption['article'][$k]);
    }
    $jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
    $jieqiTpl->setCaching(0);
    include_once JIEQI_ROOT_PATH . '/admin/footer.php';
} else {
    jieqi_checkpost();
    @ini_set('memory_limit', '256M');
    @set_time_limit(300);
    @session_write_close();
    jieqi_includedb();
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    $where = '';
    $badparm = false;
    if (!empty($_POST['sortid'])) {
        if ($where != '') {
            $where .= ' AND';
        }
        $where .= ' sortid = ' . intval($_POST['sortid']);
    }
    if (!empty($_POST['typeid'])) {
        if ($where != '') {
            $where .= ' AND';
        }
        $where .= ' typeid = ' . intval($_POST['typeid']);
    }
    if (isset($_POST['issign']) && is_numeric($_POST['issign']) && 0 <= intval($_POST['issign'])) {
        $_POST['issign'] = intval($_POST['issign']);
        if ($where != '') {
            $where .= ' AND';
        }
        if ($_POST['issign'] == 0) {
            $where .= ' issign = 0';
        } else {
            $where .= ' issign >= ' . $_POST['issign'];
        }
    }
    if (!empty($_POST['upday']) && is_numeric($_POST['upday'])) {
        $upfield = !empty($_POST['upfield']) ? 'postdate' : 'lastupdate';
        if ($where != '') {
            $where .= ' AND';
        }
        $where .= ' ' . $upfield . ' >= ' . (JIEQI_NOW_TIME - floatval($_POST['upday']) * 3600 * 24);
    } else {
        if (!empty($_POST['upday'])) {
            $badparm = true;
        }
    }
    if (!empty($_POST['articles'])) {
        if ($_POST['idname'] == 0) {
            $_POST['articles'] = trim($_POST['articles']);
            $aidary = explode(',', $_POST['articles']);
            $aidstr = '';
            foreach ($aidary as $aid) {
                $aid = intval(trim($aid));
                if ($aid) {
                    if ($aidstr != '') {
                        $aidstr .= ',';
                    }
                    $aidstr .= $aid;
                }
            }
            if ($aidstr != '') {
                if ($where != '') {
                    $where .= ' AND';
                }
                $where .= ' articleid IN (' . $aidstr . ')';
            } else {
                $badparm = true;
            }
        } else {
            $_POST['articles'] = trim($_POST['articles']);
            $anameary = explode("\n", $_POST['articles']);
            $anamestr = '';
            foreach ($anameary as $aname) {
                $aname = trim($aname);
                if (!empty($aname)) {
                    if ($anamestr != '') {
                        $anamestr .= ',';
                    }
                    $anamestr .= '\'' . jieqi_dbslashes($aname) . '\'';
                }
            }
            if ($anamestr != '') {
                if ($where != '') {
                    $where .= ' AND';
                }
                $where .= ' articlename IN (' . $anamestr . ')';
            } else {
                $badparm = true;
            }
        }
    }
    if ($badparm) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
    if (empty($where)) {
        $where = '1';
    }
    include_once JIEQI_ROOT_PATH . '/admin/header.php';
    include_once $jieqiModules['article']['path'] . '/include/funarticle.php';
    $orderary = array('articleid', 'articlename', 'postdate', 'lastupdaye', 'toptime', 'goodnum', 'hotnum', 'ratenum', 'words', 'monthwords', 'weekwords', 'daywords', 'prewords', 'allvisit', 'monthvisit', 'weekvisit', 'dayvisit', 'allvote', 'monthvote', 'weekvote', 'dayvote', 'allvipvote', 'monthvipvote', 'weekvipvote', 'dayvipvote', 'previpvote', 'allflower', 'monthflower', 'weekflower', 'dayflower', 'preflower', 'allegg', 'monthegg', 'weekegg', 'dayegg', 'preegg');
    if (isset($_REQUEST['order']) && in_array($_REQUEST['order'], $orderary)) {
        $order = $_REQUEST['order'];
    } else {
        $order = 'articlename';
    }
    if (!isset($_REQUEST['asc']) || !empty($_REQUEST['asc'])) {
        $asc = 'ASC';
    } else {
        $asc = 'DESC';
    }
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE ' . $where . ' ORDER BY ' . $order . ' ' . $asc . ' LIMIT 0, 10000';
    $res = $query->execute($sql);
    include_once JIEQI_ROOT_PATH . '/include/funexport.php';
    $params = array('res' => $res, 'format' => $_POST['format'], 'fields' => $jieqiExport['article'], 'filename' => 'article_' . date('Ymd') . '.xls', 'funrow' => 'jieqi_article_getexportrow');
    $ret = jieqi_system_exportfile($params);
    if ($ret === false) {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
}