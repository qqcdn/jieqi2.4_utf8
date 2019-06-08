<?php

if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
if (!isset($jieqiConfigs['news'])) {
    jieqi_getconfigs('article', 'news');
}
$addnum = 1;
if (isset($jieqiConfigs['news']['visitstatnum']) && is_numeric($jieqiConfigs['news']['visitstatnum']) && 0 <= intval($jieqiConfigs['news']['visitstatnum'])) {
    $addnum = intval($jieqiConfigs['news']['visitstatnum']);
}
if (0 < $addnum) {
    if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        include_once JIEQI_ROOT_PATH . '/include/funstat.php';
        $validid = intval($_REQUEST['id']);
        if (jieqi_visit_valid($validid, 'news_newsviews')) {
            $lastvisit = -1;
            if ($ids = jieqi_visit_ids($_REQUEST['id'], 'news_newsviews', $lastvisit)) {
                if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
                    jieqi_includedb();
                    $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
                }
                foreach ($ids as $k => $v) {
                    $sql = 'UPDATE ' . jieqi_dbprefix('news_topic') . ' SET views = views + ' . intval($v['visitnum'] * $addnum) . ' WHERE topicid = ' . intval($k);
                    $query->execute($sql);
                }
            }
        }
    }
}