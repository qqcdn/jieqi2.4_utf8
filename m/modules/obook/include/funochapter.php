<?php

function jieqi_obook_ochaptervars($ochapter)
{
    global $jieqiModules;
    global $jieqiConfigs;
    global $jieqiLang;
    global $obook_static_url;
    global $obook_dynamic_url;
    global $article_static_url;
    global $article_dynamic_url;
    global $jieqiOption;
    if (!isset($jieqiConfigs['obook'])) {
        jieqi_getconfigs('obook', 'configs');
    }
    if (!isset($jieqiOption['obook'])) {
        jieqi_getconfigs('obook', 'option', 'jieqiOption');
    }
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (!isset($obook_static_url)) {
        $obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
    }
    if (!isset($obook_dynamic_url)) {
        $obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
    }
    if (!isset($article_static_url)) {
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    }
    if (!isset($article_dynamic_url)) {
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    }
    $ret = jieqi_query_rowvars($ochapter);
    $ret['size_c'] = $ret['words'];
    $ret['sumemoney'] = $ret['sumegold'] + $ret['sumesilver'];
    $ret['url_articleinfo'] = jieqi_geturl('article', 'article', $ret['articleid'], 'info');
    $ret['url_articleindex'] = jieqi_geturl('article', 'article', $ret['articleid'], 'index');
    $ret['url_obookinfo'] = $obook_dynamic_url . '/obookinfo.php?id=' . $ret['obookid'];
    $ret['url_chapter'] = $obook_static_url . '/reader.php?oid=' . $ret['obookid'] . '&cid=' . $ret['ochapterid'];
    $ret['url_chapterread'] = jieqi_geturl('article', 'chapter', $ret['chapterid'], $ret['articleid'], 1);
    $ret['url_chapteredit'] = $article_static_url . '/chapteredit.php?id=' . $ret['chapterid'] . '&chaptertype=' . $ret['chaptertype'];
    $ret['url_chapterdelete'] = $article_static_url . '/chapterdel.php?id=' . $ret['chapterid'] . '&chaptertype=' . $ret['chaptertype'];
    $ret['url_chapterset'] = $article_static_url . '/chapterset.php?id=' . $ret['chapterid'] . '&chaptertype=' . $ret['chaptertype'];
    $ret['url_chaptersetfree'] = $article_static_url . '/chapterset.php?id=' . $ret['chapterid'] . '&action=free&chaptertype=' . $ret['chaptertype'];
    $ret['url_chaptersetvip'] = $article_static_url . '/chapterset.php?id=' . $ret['chapterid'] . '&action=vip&chaptertype=' . $ret['chaptertype'];
    return $ret;
}