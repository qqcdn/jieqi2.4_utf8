<?php

if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
$addnum = 1;
if (0 < $addnum) {
    if (!empty($_REQUEST['tagid']) && is_numeric($_REQUEST['tagid'])) {
        include_once JIEQI_ROOT_PATH . '/include/funstat.php';
        if (jieqi_visit_valid($_REQUEST['tagid'], 'article_tagviews')) {
            if (!is_array($tagrow)) {
                jieqi_includedb();
                $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
                $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_tag') . ' WHERE tagid = ' . intval($_REQUEST['tagid']) . '\' LIMIT 0,1';
                $query->execute($sql);
                $tagrow = $query->getRow();
            }
            if (is_array($tagrow)) {
                $lastvisit = $tagrow['lastvisit'];
                if ($ids = jieqi_visit_ids($_REQUEST['tagid'], 'article_tagviews', $lastvisit)) {
                    foreach ($ids as $k => $v) {
                        $addorup = jieqi_visit_addorup($v['lastvisit']);
                        $v['visitnum'] = intval($v['visitnum'] * $addnum);
                        $daystr = $addorup['day'] ? 'dayvisit = ' . $v['visitnum'] : 'dayvisit = dayvisit + ' . $v['visitnum'];
                        $weekstr = $addorup['week'] ? 'weekvisit = ' . $v['visitnum'] : 'weekvisit = weekvisit + ' . $v['visitnum'];
                        $monthstr = $addorup['month'] ? 'monthvisit = ' . $v['visitnum'] : 'monthvisit = monthvisit + ' . $v['visitnum'];
                        $allstr = 'allvisit = allvisit + ' . $v['visitnum'];
                        $sql = 'UPDATE ' . jieqi_dbprefix('article_tag') . ' SET lastvisit=' . intval(JIEQI_NOW_TIME) . ', ' . $daystr . ', ' . $weekstr . ', ' . $monthstr . ', ' . $allstr . ' WHERE tagid=' . intval($k);
                        $query->execute($sql);
                    }
                }
            }
        }
    }
}