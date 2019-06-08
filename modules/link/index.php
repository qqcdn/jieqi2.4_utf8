<?php

define('JIEQI_MODULE_NAME', 'link');
if (!defined('JIEQI_GLOBAL_INCLUDE')) {
    include_once '../../global.php';
}
if (!empty($jieqiModules['link']['caption'])) {
    $jieqi_pagetitle = $jieqiModules['link']['caption'] . '-' . JIEQI_SITE_NAME;
}
include_once JIEQI_ROOT_PATH . '/header.php';
if (!$jieqiTpl->is_cached($jieqiModules['link']['path'] . '/templates/linklist.html')) {
    include_once $jieqiModules['link']['path'] . '/class/link.php';
    $link_handler = JieqiLinkHandler::getInstance('JieqiLinkHandler');
    $criteria = new CriteriaCompo(new Criteria('passed', '1', '='));
    $criteria->setSort('listorder ASC,addtime');
    $criteria->setOrder('DESC');
    $link_handler->queryObjects($criteria);
    $linkrows = array();
    $tlinkrows = array();
    $k = 0;
    while ($v = $link_handler->getObject()) {
        if (!$v->getVar('linktype')) {
            $linkrows[$k]['linkid'] = $v->getVar('linkid');
            $linkrows[$k]['linktype'] = $v->getVar('linktype');
            $linkrows[$k]['name'] = $v->getVar('name');
            $linkrows[$k]['namecolor'] = $v->getVar('namecolor');
            $linkrows[$k]['url'] = $v->getVar('url');
            $linkrows[$k]['logo'] = $v->getVar('logo');
            $linkrows[$k]['username'] = $v->getVar('username');
            $linkrows[$k]['mastername'] = $v->getVar('mastername');
            $linkrows[$k]['mastertell'] = $v->getVar('mastertell');
            $linkrows[$k]['hits'] = $v->getVar('hits');
            $linkrows[$k]['addtime'] = date('y-m-d', $v->getVar('addtime'));
        } else {
            $tlinkrows[$k]['linkid'] = $v->getVar('linkid');
            $tlinkrows[$k]['linktype'] = $v->getVar('linktype');
            $tlinkrows[$k]['name'] = $v->getVar('name');
            $tlinkrows[$k]['namecolor'] = $v->getVar('namecolor');
            $tlinkrows[$k]['url'] = $v->getVar('url');
            $tlinkrows[$k]['logo'] = $v->getVar('logo');
            $tlinkrows[$k]['username'] = $v->getVar('username');
            $tlinkrows[$k]['mastername'] = $v->getVar('mastername');
            $tlinkrows[$k]['mastertell'] = $v->getVar('mastertell');
            $tlinkrows[$k]['hits'] = $v->getVar('hits');
            $tlinkrows[$k]['addtime'] = date('y-m-d', $v->getVar('addtime'));
        }
        $k++;
    }
    $jieqiTpl->assign_by_ref('linkrows', $linkrows);
    $jieqiTpl->assign_by_ref('tlinkrows', $tlinkrows);
}
$jieqiTset['jieqi_contents_template'] = $jieqiModules['link']['path'] . '/templates/linklist.html';
include_once JIEQI_ROOT_PATH . '/footer.php';