<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['delallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
@set_time_limit(0);
@session_write_close();
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (isset($_POST['act']) && $_POST['act'] == 'clean') {
    jieqi_checkpost();
    jieqi_includedb();
    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    $where = '';
    $badparm = false;
    if (is_numeric($_POST['startid']) && is_numeric($_POST['stopid'])) {
        $_POST['startid'] = intval($_POST['startid']);
        $_POST['stopid'] = intval($_POST['stopid']);
        if ($where != '') {
            $where .= ' AND';
        }
        if ($_POST['startid'] <= $_POST['stopid']) {
            $where .= ' articleid >= ' . $_POST['startid'] . ' AND articleid <= ' . $_POST['stopid'];
        } else {
            $where .= ' articleid >= ' . $_POST['stopid'] . ' AND articleid <= ' . $_POST['startid'];
        }
    } else {
        if (!empty($_POST['startid']) || !empty($_POST['stopid'])) {
            $badparm = true;
        }
    }
    if (!empty($_POST['upday']) && is_numeric($_POST['upday'])) {
        if ($where != '') {
            $where .= ' AND';
        }
        if ($_POST['upflag'] == 1) {
            $where .= ' lastupdate >= ' . (JIEQI_NOW_TIME - floatval($_POST['upday']) * 3600 * 24);
        } else {
            $where .= ' lastupdate < ' . (JIEQI_NOW_TIME - floatval($_POST['upday']) * 3600 * 24);
        }
    } else {
        if (!empty($_POST['upday'])) {
            $badparm = true;
        }
    }
    if (!empty($_POST['visitnum']) && is_numeric($_POST['visitnum'])) {
        $_POST['visitnum'] = intval($_POST['visitnum']);
        $fieldary = array('allvisit', 'monthvisit', 'weekvisit', 'allvote', 'monthvote', 'weekvote');
        if (in_array($_POST['visittype'], $fieldary)) {
            if ($where != '') {
                $where .= ' AND';
            }
            if ($_POST['visitflag'] == 1) {
                $where .= ' ' . $_POST['visittype'] . ' > ' . $_POST['visitnum'];
            } else {
                $where .= ' ' . $_POST['visittype'] . ' < ' . $_POST['visitnum'];
            }
        }
    } else {
        if (!empty($_POST['visitnum'])) {
            $badparm = true;
        }
    }
    if ($_POST['authorflag'] == 1) {
        if ($where != '') {
            $where .= ' AND';
        }
        $where .= ' authorid > 0';
    } else {
        if ($_POST['authorflag'] == 2) {
            if ($where != '') {
                $where .= ' AND';
            }
            $where .= ' authorid = 0';
        }
    }
    $sortids = array();
    if (is_array($_POST['sortid']) && 0 < count($_POST['sortid'])) {
        foreach ($_POST['sortid'] as $v) {
            if (is_numeric($v)) {
                $sortids[] = intval($v);
            }
        }
    }
    if (0 < count($sortids)) {
        $where .= ' sortid IN (' . implode(',', $sortids) . ')';
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
    if (!in_array($_POST['operate'], array('delarticle', 'delchapter', 'delattach', 'hidearticle', 'showarticle'))) {
        $badparm = true;
    }
    if ($badparm) {
        jieqi_printfail($jieqiLang['article']['clean_bad_parm']);
    }
    @set_time_limit(0);
    @session_write_close();
    header('Content-Type:text/html; charset=' . JIEQI_CHAR_SET);
    header('Cache-Control:no-cache');
    echo '                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              ';
    echo sprintf($jieqiLang['article']['clean_show_start'], $jieqiLang['article']['clean_operate_' . $_POST['operate']]);
    ob_flush();
    flush();
    if ($where == '') {
        $where = '1';
    }
    $sql = 'SELECT articleid FROM ' . jieqi_dbprefix('article_article') . ' WHERE ' . $where;
    $res = $query->execute($sql);
    $resnum = $query->db->getRowsNum($res);
    echo sprintf($jieqiLang['article']['clean_show_num'], $resnum);
    ob_flush();
    flush();
    include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
    $criteria = new CriteriaCompo(new Criteria('attachment', '', '!='));
    while ($row = $query->getRow($res)) {
        if ($_POST['operate'] == 'delarticle') {
            $ret = jieqi_article_delete($row['articleid'], true);
        } else {
            if ($_POST['operate'] == 'delchapter') {
                $ret = jieqi_article_clean($row['articleid'], true);
            } else {
                if ($_POST['operate'] == 'delattach') {
                    $ret = jieqi_article_delchapter($row['articleid'], $criteria, true);
                } else {
                    if ($_POST['operate'] == 'hidearticle') {
                        $query->execute('UPDATE ' . jieqi_dbprefix('article_article') . ' SET display = 2 WHERE articleid = ' . intval($row['articleid']));
                        $ret = jieqi_article_updateinfo($row['articleid'], 'articlehide');
                    } else {
                        if ($_POST['operate'] == 'showarticle') {
                            $query->execute('UPDATE ' . jieqi_dbprefix('article_article') . ' SET display = 0 WHERE articleid = ' . intval($row['articleid']));
                            $ret = jieqi_article_updateinfo($row['articleid'], 'articleshow');
                        }
                    }
                }
            }
        }
        if (is_object($ret)) {
            echo sprintf($jieqiLang['article']['clean_article_doing'], $ret->getVar('articlename'), $ret->getVar('articleid'));
            ob_flush();
            flush();
        }
    }
    jieqi_article_updateinfo(0);
    echo $jieqiLang['article']['clean_all_success'];
    ob_flush();
    flush();
} else {
    include_once JIEQI_ROOT_PATH . '/admin/header.php';
    $jieqiTpl->assign('article_static_url', $article_static_url);
    $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
    $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
    $jieqiTpl->assign('url_batchclean', $article_static_url . '/admin/batchclean.php?do=submit');
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/batchclean.html';
    include_once JIEQI_ROOT_PATH . '/admin/footer.php';
}