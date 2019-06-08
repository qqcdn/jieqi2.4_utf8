<?php

function jieqi_url_news_info($id)
{
    global $jieqiConfigs;
    global $jieqiModules;
    if (!empty($jieqiConfigs['news']['fakeinfo'])) {
        $repfrom = array('<{$jieqi_url}>', '<{$id|subdirectory}>', '<{$id}>');
        $repto = array(JIEQI_URL, jieqi_getsubdir($id), $id);
        $ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['news']['fakeinfo']));
        if (substr($ret, 0, 4) != 'http') {
            $ret = JIEQI_URL . $ret;
        }
        return $ret;
    } else {
        return $jieqiModules['news']['url'] . '/newsinfo.php?id=' . $id;
    }
}
function jieqi_url_news_newslist($page = 1, $sortid = 0, $order = '')
{
    global $jieqiConfigs;
    global $jieqiSort;
    global $jieqiModules;
    $sortid = intval($sortid);
    if (!empty($page)) {
        $page = intval($page);
        if ($page < 1) {
            $page = 1;
        }
    }
    if (!isset($jieqiSort['news'])) {
        jieqi_getconfigs('news', 'sort', 'jieqiSort');
    }
    if (!isset($jieqiSort['news'][$sortid])) {
        $sortid = 0;
    }
    if (!empty($jieqiConfigs['news']['fakesort'])) {
        $repfrom = array('<{$jieqi_url}>', '<{$sortid}>', '<{$order}>');
        $repto = array(JIEQI_URL, $sortid, $order);
        if (!empty($page)) {
            $repfrom[] = '<{$page|subdirectory}>';
            $repfrom[] = '<{$page}>';
            $repto[] = jieqi_getsubdir($page);
            $repto[] = $page;
        }
        $ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['news']['fakesort']));
        if (substr($ret, 0, 4) != 'http') {
            $ret = JIEQI_URL . $ret;
        }
        return $ret;
    } else {
        $ret = $jieqiModules['news']['url'] . '/newslist.php?sortid=' . $sortid;
        if (!empty($order)) {
            $ret .= '&order=' . $order;
        }
        if (!empty($page)) {
            $ret .= '&page=' . $page;
        } else {
            $ret .= '&page=';
        }
        return $ret;
    }
}
function jieqi_url_news_cover($id, $flag = -1)
{
    global $jieqiConfigs;
    global $jieqiModules;
    global $jieqi_image_type;
    if (!isset($jieqiConfigs['news'])) {
        jieqi_getconfigs('news', 'configs', 'jieqiConfigs');
    }
    if (empty($jieqiConfigs['news']['coverdir'])) {
        $jieqiConfigs['news']['coverdir'] = $jieqiConfigs['news']['imagedir'];
    }
    $id = intval($id);
    $nocover = $jieqiModules['news']['url'] . '/images/nocover.jpg';
    if ($flag < 0) {
        global $topic_handler;
        if (!is_object($topic_handler)) {
            include_once $jieqiModules['news']['path'] . '/class/topic.php';
            $topic_handler = JieqiNewstopicHandler::getInstance('JieqiNewstopicHandler');
        }
        $news = $topic_handler->get($id);
        if (is_object($news)) {
            $flag = $news->getVar('cover', 'n');
        }
    }
    $flag = intval($flag);
    if ($flag <= 0 || !isset($jieqi_image_type[$flag])) {
        return $jieqiModules['news']['url'] . '/images/nocover.jpg';
    }
    return jieqi_uploadurl($jieqiConfigs['news']['coverdir'], $jieqiConfigs['news']['coverurl'], 'news') . jieqi_getsubdir($id) . '/' . $id . $jieqi_image_type[$flag];
}
global $jieqiConfigs;
if (!isset($jieqiConfigs['news'])) {
    jieqi_getconfigs('news', 'configs', 'jieqiConfigs');
}