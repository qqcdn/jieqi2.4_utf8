<?php

if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
if (!isset($jieqiConfigs['article'])) {
    jieqi_getconfigs('article', 'configs');
}
$addnum = 1;
if (isset($jieqiConfigs['article']['visitstatnum']) && is_numeric($jieqiConfigs['article']['visitstatnum']) && 0 <= intval($jieqiConfigs['article']['visitstatnum'])) {
    $addnum = intval($jieqiConfigs['article']['visitstatnum']);
}
if (0 < $addnum) {
    $aid = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : (isset($_REQUEST['aid']) ? intval($_REQUEST['aid']) : 0);
    if (!empty($aid)) {
        include_once JIEQI_ROOT_PATH . '/include/funstat.php';
        $validid = $aid;
        if (jieqi_visit_valid($validid, 'article_articleviews')) {
            $lastvisit = is_object($article) ? $article->getVar('lastvisit', 'n') : -1;
            if ($ids = jieqi_visit_ids($aid, 'article_articleviews', $lastvisit)) {
                if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
                    jieqi_includedb();
                    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
                }
                $qids = array();
                foreach ($ids as $k => $v) {
                    if ($v['lastvisit'] < 0) {
                        $qids[] = intval($k);
                    }
                }
                if (0 < count($qids)) {
                    $sql = 'SELECT articleid, lastvisit FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid IN (' . implode(', ', $qids) . ')';
                    $query->execute($sql);
                    while ($row = $query->getRow()) {
                        if (isset($ids[$row['articleid']])) {
                            $ids[$row['articleid']]['lastvisit'] = intval($row['lastvisit']);
                        }
                    }
                }
                foreach ($ids as $k => $v) {
                    $addorup = jieqi_visit_addorup($v['lastvisit']);
                    $v['visitnum'] = intval($v['visitnum'] * $addnum);
                    $daystr = $addorup['day'] ? 'dayvisit = ' . $v['visitnum'] : 'dayvisit = dayvisit + ' . $v['visitnum'];
                    $weekstr = $addorup['week'] ? 'weekvisit = ' . $v['visitnum'] : 'weekvisit = weekvisit + ' . $v['visitnum'];
                    $monthstr = '';
                    if (2.3 <= floatval(JIEQI_VERSION)) {
                        if (1 < $addorup['month']) {
                            $monthstr = 'previsit = 0, ';
                        } else {
                            if ($addorup['month'] == 1) {
                                $monthstr = 'previsit = monthvisit, ';
                            }
                        }
                    }
                    $monthstr .= $addorup['month'] ? 'monthvisit = ' . $v['visitnum'] : 'monthvisit = monthvisit + ' . $v['visitnum'];
                    $allstr = 'allvisit = allvisit + ' . $v['visitnum'];
                    $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET lastvisit=' . intval(JIEQI_NOW_TIME) . ', ' . $daystr . ', ' . $weekstr . ', ' . $monthstr . ', ' . $allstr . ' WHERE articleid=' . intval($k);
                    $query->execute($sql);
                }
            }
        }
    }
}