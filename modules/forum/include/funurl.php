<?php

function jieqi_url_forum_showtopic($tid, $page = 1, $lpage = 1)
{
    global $jieqiConfigs;
    global $jieqiModules;
    if (!empty($page)) {
        $page = intval($page);
        if ($page < 1 && $page != -1) {
            $page = 1;
        }
    }
    $lpage = intval($lpage);
    if ($lpage < 1) {
        $lpage = 1;
    }
    if (!empty($jieqiConfigs['forum']['fakeshowtopic'])) {
        $repfrom = array('<{$jieqi_url}>', '<{$tid|subdirectory}>', '<{$tid}>');
        $repto = array(JIEQI_URL, jieqi_getsubdir($tid), $tid);
        if (!empty($page)) {
            $repfrom[] = '<{$page|subdirectory}>';
            $repfrom[] = '<{$page}>';
            $repfrom[] = '<{$lpage|subdirectory}>';
            $repfrom[] = '<{$lpage}>';
            $repto[] = jieqi_getsubdir($page);
            $repto[] = $page;
            $repto[] = jieqi_getsubdir($lpage);
            $repto[] = $lpage;
        }
        $ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['forum']['fakeshowtopic']));
        if (substr($ret, 0, 4) != 'http') {
            $ret = JIEQI_URL . $ret;
        }
        return $ret;
    } else {
        if (!empty($page)) {
            return $jieqiModules['forum']['url'] . '/showtopic.php?tid=' . $tid . '&lpage=' . $lpage . '&page=' . $page;
        } else {
            return $jieqiModules['forum']['url'] . '/showtopic.php?tid=' . $tid . '&lpage=' . $lpage . '&page=';
        }
    }
}
function jieqi_url_forum_topiclist($page = 1, $fid = 0)
{
    global $jieqiConfigs;
    global $jieqiModules;
    $fid = intval($fid);
    if (!empty($page)) {
        $page = intval($page);
        if ($page < 1) {
            $page = 1;
        }
    }
    if (!empty($jieqiConfigs['forum']['faketopiclist'])) {
        $repfrom = array('<{$jieqi_url}>', '<{$fid|subdirectory}>', '<{$fid}>');
        $repto = array(JIEQI_URL, jieqi_getsubdir($fid), $fid);
        if (!empty($page)) {
            $repfrom[] = '<{$page|subdirectory}>';
            $repfrom[] = '<{$page}>';
            $repto[] = jieqi_getsubdir($page);
            $repto[] = $page;
        }
        $ret = trim(str_replace($repfrom, $repto, $jieqiConfigs['forum']['faketopiclist']));
        if (substr($ret, 0, 4) != 'http') {
            $ret = JIEQI_URL . $ret;
        }
        return $ret;
    } else {
        if (!empty($page)) {
            return $jieqiModules['forum']['url'] . '/topiclist.php?fid=' . $fid . '&page=' . $page;
        } else {
            return $jieqiModules['forum']['url'] . '/topiclist.php?fid=' . $fid . '&page=';
        }
    }
}
global $jieqiConfigs;
if (!isset($jieqiConfigs['forum'])) {
    jieqi_getconfigs('forum', 'configs', 'jieqiConfigs');
}