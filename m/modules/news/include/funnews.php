<?php

function jieqi_news_vars($news)
{
    global $jieqiModules;
    global $jieqiSort;
    global $jieqiConfigs;
    global $jieqiLang;
    global $jieqiOption;
    if (!isset($jieqiSort['news'])) {
        jieqi_getconfigs('news', 'sort');
    }
    if (!isset($jieqiConfigs['news'])) {
        jieqi_getconfigs('news', 'configs');
    }
    if (!isset($jieqiLang['news'])) {
        jieqi_loadlang('news', JIEQI_MODULE_NAME);
    }
    $ret = $news->getVars('s');
    $ret['summary'] = str_replace('<br />', ' ', $ret['summary']);
    $ret['sortname'] = $jieqiSort['news'][$ret['sortid']]['sortname'];
    if (is_array($jieqiOption['news'])) {
        foreach ($jieqiOption['news'] as $k => $v) {
            if (isset($ret[$k])) {
                $ret[$k . '_s'] = $v['items'][$ret[$k]];
            }
        }
    }
    return $ret;
}