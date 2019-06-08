<?php

define('JIEQI_MODULE_NAME', 'link');
require_once '../../../global.php';
jieqi_checklogin();
if ($jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail(LANG_NEED_ADMIN);
}
jieqi_loadlang('link', JIEQI_MODULE_NAME);
include_once $jieqiModules['link']['path'] . '/class/link.php';
$link_handler = JieqiLinkHandler::getInstance('JieqiLinkHandler');
$jieqiTset['jieqi_contents_template'] = $jieqiModules['link']['path'] . '/templates/admin/linklist.html';
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$jieqiPset = jieqi_get_pageset();
if (isset($_POST['act']) && (!empty($_REQUEST['id']) || !empty($_REQUEST['checkid']))) {
    jieqi_checkpost();
    $criteria = new CriteriaCompo(new Criteria('linkid', $_REQUEST['id']));
    switch ($_POST['act']) {
        case 'show':
            $link_handler->updatefields(array('passed' => 1), $criteria);
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'hide':
            $link_handler->updatefields(array('passed' => 0), $criteria);
            if (!empty($_REQUEST['ajax_request'])) {
                jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS);
            }
            break;
        case 'batchchg':
            foreach ($_REQUEST['checkid'] as $id) {
                $criteria = new CriteriaCompo(new Criteria('linkid', $id));
                $link_handler->updatefields(array('listorder' => $_REQUEST['listorder' . $id], 'mastername' => $_REQUEST['mastername' . $id], 'mastertell' => $_REQUEST['mastertell' . $id]), $criteria);
            }
            jieqi_jumppage($jieqiModules['link']['url'] . '/admin/link.php', '', '', true);
            break;
        case 'batchdel':
            foreach ($_REQUEST['checkid'] as $id) {
                $criteria = new CriteriaCompo(new Criteria('linkid', $id));
                $link_handler->delete($criteria);
            }
            jieqi_jumppage($jieqiModules['link']['url'] . '/admin/link.php', '', '', true);
            break;
        case 'del':
            $link = $link_handler->get($_REQUEST['id']);
            if (is_object($link)) {
                $link_handler->delete($criteria);
            }
            jieqi_jumppage($jieqiModules['link']['url'] . '/admin/link.php', '', '', true);
            break;
    }
}
$criteria = new CriteriaCompo();
$jieqiTpl->assign('url_link', $jieqiModules['link']['url'] . '/admin/link.php');
$jieqiTpl->assign('url_jump', jieqi_addurlvars(array()));
$jieqiTpl->assign('checkall', '<input type="checkbox" id="checkall" name="checkall" value="checkall" onclick="javascript: for (var i=0;i<this.form.elements.length;i++){ if (this.form.elements[i].name != \'checkkall\') this.form.elements[i].checked = form.checkall.checked; }">');
$criteria->setSort('listorder asc,addtime');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiPset['rows']);
$criteria->setStart($jieqiPset['start']);
$link_handler->queryObjects($criteria);
$linkrows = array();
$k = 0;
while ($v = $link_handler->getObject()) {
    $linkrows[$k]['checkbox'] = '<input type="checkbox" id="checkid[]" name="checkid[]" value="' . $v->getVar('linkid') . '">';
    $linkrows[$k]['checkid'] = $k;
    $linkrows[$k]['linkid'] = $v->getVar('linkid');
    $linkrows[$k]['linktype'] = $v->getVar('linktype');
    $linkrows[$k]['name'] = $v->getVar('name');
    $linkrows[$k]['namecolor'] = $v->getVar('namecolor');
    $linkrows[$k]['url'] = $v->getVar('url');
    $linkrows[$k]['logo'] = $v->getVar('logo');
    $linkrows[$k]['username'] = $v->getVar('username');
    $linkrows[$k]['mastername'] = $v->getVar('mastername');
    $linkrows[$k]['mastertell'] = $v->getVar('mastertell');
    $linkrows[$k]['listorder'] = $v->getVar('listorder');
    $linkrows[$k]['passed'] = $v->getVar('passed');
    $linkrows[$k]['hits'] = $v->getVar('hits');
    $linkrows[$k]['addtime'] = date('y-m-d', $v->getVar('addtime'));
    $k++;
}
$jieqiTpl->assign_by_ref('linkrows', $linkrows);
include_once JIEQI_ROOT_PATH . '/lib/html/page.php';
$jieqiPset['count'] = $link_handler->getCount($criteria);
$jumppage = new JieqiPage($jieqiPset);
$pagelink = '';
if (empty($pagelink)) {
    $pagelink .= '?page=';
} else {
    $pagelink .= '&page=';
}
$jumppage->setlink($jieqiModules['link']['url'] . '/admin/link.php' . $pagelink, false, true);
$jieqiTpl->assign('url_jumppage', $jumppage->whole_bar());
$jieqiTpl->setCaching(0);
include_once JIEQI_ROOT_PATH . '/admin/footer.php';