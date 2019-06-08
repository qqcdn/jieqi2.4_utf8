<?php

function jieqi_obook_obookvars($obook)
{
    global $jieqiModules;
    global $jieqiSort;
    global $jieqiConfigs;
    global $jieqiLang;
    global $jieqiOption;
    global $obook_static_url;
    global $obook_dynamic_url;
    global $article_static_url;
    global $article_dynamic_url;
    if (!isset($jieqiSort['article'])) {
        jieqi_getconfigs('article', 'sort');
    }
    if (!isset($jieqiConfigs['obook'])) {
        jieqi_getconfigs('obook', 'configs');
    }
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (!isset($jieqiLang['obook']['obook'])) {
        jieqi_loadlang('obook', 'obook');
    }
    if (!isset($jieqiOption['obook'])) {
        jieqi_getconfigs('obook', 'option', 'jieqiOption');
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
    $ret = jieqi_query_rowvars($obook);
    if (isset($ret['siteid']) && !empty($ret['siteid'])) {
        global $jieqiSites;
        if (!isset($jieqiSites)) {
            jieqi_getconfigs('system', 'sites', 'jieqiSites');
        }
        if (isset($jieqiSites[$ret['siteid']])) {
            $ret['sitename'] = jieqi_htmlstr($jieqiSites[$ret['siteid']]['name']);
            $ret['siteurl'] = jieqi_htmlstr($jieqiSites[$ret['siteid']]['url']);
            $ret['firstflag'] = $ret['sitename'];
        }
    }
    $ret['size_c'] = $ret['words'];
    $ret['remainemoney'] = $ret['sumemoney'] - $ret['paidemoney'];
    $ret['sort'] = isset($jieqiSort['article'][$ret['sortid']]['caption']) ? $jieqiSort['article'][$ret['sortid']]['caption'] : '';
    $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
    $daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
    $monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
    $tmpvar = date('w', JIEQI_NOW_TIME);
    if ($tmpvar == 0) {
        $tmpvar = 7;
    }
    $weekstart = $daystart;
    if (1 < $tmpvar) {
        $weekstart -= ($tmpvar - 1) * 86400;
    }
    $dwmary = array('sale' => 'lastsale');
    foreach ($dwmary as $k => $v) {
        if ($ret[$v] < $daystart) {
            $ret['day' . $k] = 0;
        }
        if ($ret[$v] < $weekstart) {
            $ret['week' . $k] = 0;
        }
        if ($ret[$v] < $monthstart) {
            $ret['month' . $k] = 0;
        }
    }
    $ret['url_obookinfo'] = $obook_dynamic_url . '/obookinfo.php?id=' . $ret['obookid'];
    $ret['url_obookindex'] = $ret['url_obookinfo'];
    if ($ret['lastchapter'] == '') {
        $ret['url_lastchapter'] = '';
    } else {
        if ($ret['lastvolume'] != '') {
            $ret['lastchapter'] = $ret['lastvolume'] . ' ' . $ret['lastchapter'];
        }
        $ret['url_lastchapter'] = $obook_static_url . '/reader.php?oid=' . $ret['obookid'] . '&cid=' . $ret['lastchapterid'];
    }
    $ret['url_articleinfo'] = jieqi_geturl('article', 'article', $ret['articleid'], 'info');
    $ret['url_articleindex'] = jieqi_geturl('article', 'article', $ret['articleid'], 'index');
    $ret['url_index'] = $ret['url_articleindex'];
    $ret['url_read'] = $ret['url_articleindex'];
    $ret['url_manage'] = $obook_static_url . '/obookmanage.php?id=' . $ret['obookid'];
    $ret['url_amanage'] = $article_static_url . '/articlemanage.php?id=' . $ret['articleid'];
    $ret['url_bookcase'] = $article_dynamic_url . '/addbookcase.php?bid=' . $ret['articleid'];
    $ret['url_uservote'] = $article_dynamic_url . '/uservote.php?id=' . $ret['articleid'];
    $ret['url_authorpage'] = 0 < $ret['authorid'] ? $article_dynamic_url . '/authorpage.php?id=' . $ret['authorid'] : '#';
    $ret['url_authorarticle'] = is_object($obook) ? $article_dynamic_url . '/authorarticle.php?author=' . urlencode($obook->getVar('author', 'n')) : $article_dynamic_url . '/authorarticle.php?author=' . urlencode($obook['author']);
    $ret['url_report'] = is_object($obook) ? $obook_dynamic_url . '/newmessage.php?tosys=1&title=' . urlencode(sprintf($jieqiLang['obook']['obook_report_title'], $obook->getVar('obookname', 'n'))) . '&content=' . urlencode(sprintf($jieqiLang['obook']['obook_report_reason'], $ret['url_obookinfo'])) : $obook_dynamic_url . '/newmessage.php?tosys=1&title=' . urlencode(sprintf($jieqiLang['obook']['obook_report_title'], $obook['obookname'])) . '&content=' . urlencode(sprintf($jieqiLang['obook']['obook_report_reason'], $ret['url_obookinfo']));
    return $ret;
}
function jieqi_obook_getuptime()
{
    global $jieqiArticleuplog;
    jieqi_getcachevars('article', 'articleuplog');
    if (!is_array($jieqiArticleuplog)) {
        $jieqiArticleuplog = array('articleuptime' => 0, 'chapteruptime' => 0, 'vipuptime' => 0);
    }
    $uptime = $jieqiArticleuplog['vipuptime'] < $jieqiArticleuplog['articleuptime'] ? $jieqiArticleuplog['articleuptime'] : $jieqiArticleuplog['vipuptime'];
    return intval($uptime);
}