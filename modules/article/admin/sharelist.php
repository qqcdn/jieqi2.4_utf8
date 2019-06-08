<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('share', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('system', 'shares', 'jieqiShares');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/sharelist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
if (empty($_REQUEST['ssid']) || !is_numeric($_REQUEST['ssid']) || !isset($jieqiShares[$_REQUEST['ssid']])) {
    $_REQUEST['ssid'] = 1;
} else {
    $_REQUEST['ssid'] = intval($_REQUEST['ssid']);
}
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$innames = '';
$addnum = 0;
$cleannum = 0;
if (!empty($_REQUEST['act'])) {
    switch ($_REQUEST['act']) {
        case 'add':
            jieqi_checkpost();
            if (!empty($_REQUEST['anames']) && !empty($_REQUEST['ssid'])) {
                $tmpary = explode(' ', $_REQUEST['anames']);
                foreach ($tmpary as $a) {
                    $a = trim($a);
                    if (0 < strlen($a)) {
                        if (0 < strlen($innames)) {
                            $innames .= ', ';
                        }
                        $innames .= '\'' . jieqi_dbslashes($a) . '\'';
                    }
                }
                if (0 < strlen($innames)) {
                    $sql = 'SELECT articleid, articlename, isvip, siteid, display FROM ' . jieqi_dbprefix('article_article') . ' WHERE articlename IN (' . $innames . ');';
                    $res = $query->execute($sql);
                    while ($row = $query->getRow($res)) {
                        if ($row['siteid'] == 0 && $row['display'] == 0) {
                            $fieldrows = array();
                            $fieldrows['ssid'] = $_REQUEST['ssid'];
                            $fieldrows['articleid'] = $row['articleid'];
                            $fieldrows['articlename'] = $row['articlename'];
                            $fieldrows['sflag'] = 0 < $row['isvip'] ? 1 : 0;
                            $sql = $query->makeupsql(jieqi_dbprefix('article_share'), $fieldrows, 'INSERT');
                            $query->execute($sql);
                            $addnum++;
                        }
                    }
                }
            }
            break;
        case 'delete':
            echo $_REQUEST['shareid'];
            if (!empty($_REQUEST['shareid']) && is_numeric($_REQUEST['shareid'])) {
                $sql = 'DELETE FROM ' . jieqi_dbprefix('article_share') . ' WHERE shareid = ' . intval($_REQUEST['shareid']);
                $query->execute($sql);
            }
            break;
        case 'clean':
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_share') . ' WHERE articleid IN (SELECT s.articleid FROM (SELECT * FROM ' . jieqi_dbprefix('article_share') . ') s LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON s.articleid = a.articleid WHERE a.articleid IS NULL)';
            $query->execute($sql);
            $cleannum = $query->db->getAffectedRows();
            break;
    }
}
$slimit = '';
if (!empty($_REQUEST['ssid'])) {
    $slimit .= strlen($slimit) == 0 ? 'ssid = ' . $_REQUEST['ssid'] : ' AND ssid = ' . $_REQUEST['ssid'];
}
if (isset($_REQUEST['aid']) && is_numeric($_REQUEST['aid'])) {
    $slimit .= strlen($slimit) == 0 ? 'articleid = ' . intval($_REQUEST['aid']) : ' AND articleid = ' . intval($_REQUEST['aid']);
} else {
    if (isset($_REQUEST['aname']) && 0 < strlen($_REQUEST['aname'])) {
        $slimit .= strlen($slimit) == 0 ? 'articlename = \'' . jieqi_dbslashes($_REQUEST['aname']) . '\'' : ' AND articlename = \'' . jieqi_dbslashes($_REQUEST['aname']) . '\'';
    }
}
if (strlen($slimit) == 0) {
    $slimit = 1;
}
$sql = 'SELECT * FROM ' . jieqi_dbprefix('article_share') . ' WHERE ' . $slimit . ' ORDER BY shareid DESC LIMIT ' . $jieqiPset['start'] . ',' . $jieqiPset['rows'];
$query->execute($sql);
$sharerows = array();
$k = 0;
while ($row = $query->getRow()) {
    $sharerows[$k] = jieqi_query_rowvars($row);
    $k++;
}
$jieqiTpl->assign_by_ref('sharerows', $sharerows);
$jieqiTpl->assign_by_ref('sharesite', $jieqiShares[$_REQUEST['ssid']]);
$jieqiTpl->assign_by_ref('jieqishares', $jieqiShares);
$jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
$jieqiTpl->assign('addnum', $addnum);
$jieqiTpl->assign('cleannum', $cleannum);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_share') . ' WHERE ' . $slimit;
$query->execute($sql);
$row = $query->getRow();
$jieqiPset['count'] = intval($row['cot']);
$jumppage = new JieqiPage($jieqiPset);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';