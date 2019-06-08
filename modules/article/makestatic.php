<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (empty($_REQUEST['id'])) {
    exit('error id');
}
$_REQUEST['id'] = intval($_REQUEST['id']);
if ($_REQUEST['id'] <= 0) {
    exit('error id');
}
if (empty($_REQUEST['key'])) {
    exit('no key');
} else {
    if (defined('JIEQI_SITE_KEY') && $_REQUEST['key'] != JIEQI_SITE_KEY) {
        exit('error key');
    } else {
        if ($_REQUEST['key'] != md5(JIEQI_DB_USER . JIEQI_DB_PASS . JIEQI_DB_NAME)) {
            exit('error key');
        }
    }
}
@set_time_limit(0);
@session_write_close();
include_once $jieqiModules['article']['path'] . '/include/funstatic.php';
switch ($_REQUEST['action']) {
    case 'articlenew':
        article_make_sinfo($_REQUEST['id']);
        article_make_ptoplist('lastupdate', 1);
        article_make_psort(intval($_REQUEST['sortid']), 1);
        article_make_psort(0, 1);
        break;
    case 'articleedit':
        article_make_sinfo($_REQUEST['id']);
        break;
    case 'articledel':
        article_delete_sinfo($_REQUEST['id']);
        break;
    case 'chapternew':
        article_make_sinfo($_REQUEST['id']);
        article_make_ptoplist('lastupdate', 1);
        article_make_psort(intval($_REQUEST['sortid']), 1);
        article_make_psort(0, 1);
        break;
    case 'chapteredit':
        article_delete_sinfo($_REQUEST['id']);
        break;
    case 'chapterdel':
        article_delete_sinfo($_REQUEST['id']);
        break;
    case 'reviewnew':
        article_make_sinfo($_REQUEST['id']);
        break;
    default:
        article_make_sinfo($_REQUEST['id']);
        break;
}