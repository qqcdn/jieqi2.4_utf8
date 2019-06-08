<?php

function jieqi_obook_autocreate($article, $isvip = 1)
{
    global $jieqiModules;
    global $jieqiConfigs;
    global $jieqiLang;
    global $jieqiPower;
    global $obook_handler;
    global $ochapter_handler;
    global $article_handler;
    global $jieqiUsersStatus;
    global $jieqiUsersGroup;
    if (!isset($jieqiLang['obook'])) {
        jieqi_loadlang('manage', 'obook');
    }
    if (!isset($jieqiPower['obook'])) {
        jieqi_getconfigs('obook', 'power', 'jieqiPower');
    }
    if (!isset($obook_handler) || !is_a($obook_handler, 'JieqiObookHandler')) {
        include_once $jieqiModules['obook']['path'] . '/class/obook.php';
        $obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
    }
    if (!isset($ochapter_handler) || !is_a($ochapter_handler, 'JieqiOchapterHandler')) {
        include_once $jieqiModules['obook']['path'] . '/class/ochapter.php';
        $ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
    }
    if (!isset($article_handler) || !is_a($article_handler, 'JieqiArticleHandler')) {
        include_once $jieqiModules['article']['path'] . '/class/article.php';
        $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
    }
    if (is_object($article)) {
        $articleid = intval($article->getVar('articleid', 'n'));
    } else {
        $articleid = intval($article);
    }
    $obook = $obook_handler->get($articleid, 'articleid');
    if (is_object($obook)) {
        return $obook;
    } else {
        if (!is_object($article)) {
            include_once $jieqiModules['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $article = $article_handler->get($articleid);
        }
        if (!$article) {
            return LANG_ERROR_PARAMETER;
        }
        if (intval($article->getVar('siteid', 'n')) == 0 && intval($article->getVar('issign', 'n')) < 10 && 0 < $isvip) {
            return $jieqiLang['obook']['obook_isnot_vipsign'];
        }
        $tmpobook = $obook_handler->get($articleid, 'obookid');
        $newObook = $obook_handler->create();
        if (!is_object($tmpobook)) {
            $newObook->setVar('obookid', $article->getVar('articleid', 'n'));
        }
        $newObook->setVar('siteid', $article->getVar('siteid', 'n'));
        $newObook->setVar('sourceid', $article->getVar('sourceid', 'n'));
        $newObook->setVar('postdate', JIEQI_NOW_TIME);
        $newObook->setVar('lastupdate', JIEQI_NOW_TIME);
        $newObook->setVar('obookname', $article->getVar('articlename', 'n'));
        $newObook->setVar('backupname', $article->getVar('backupname', 'n'));
        $newObook->setVar('keywords', $article->getVar('keywords', 'n'));
        $newObook->setVar('roles', $article->getVar('roles', 'n'));
        $newObook->setVar('articleid', $article->getVar('articleid', 'n'));
        $newObook->setVar('initial', $article->getVar('initial', 'n'));
        $newObook->setVar('sortid', $article->getVar('sortid', 'n'));
        $newObook->setVar('typeid', $article->getVar('typeid', 'n'));
        $newObook->setVar('libid', $article->getVar('libid', 'n'));
        $newObook->setVar('intro', $article->getVar('intro', 'n'));
        $newObook->setVar('notice', $article->getVar('notice', 'n'));
        $newObook->setVar('foreword', $article->getVar('foreword', 'n'));
        $newObook->setVar('setting', '');
        $newObook->setVar('lastvolumeid', 0);
        $newObook->setVar('lastvolume', '');
        $newObook->setVar('lastchapterid', 0);
        $newObook->setVar('lastchapter', '');
        $newObook->setVar('lastsummary', '');
        $newObook->setVar('chapters', 0);
        $newObook->setVar('words', 0);
        $newObook->setVar('authorid', $article->getVar('authorid', 'n'));
        $newObook->setVar('author', $article->getVar('author', 'n'));
        $newObook->setVar('agentid', $article->getVar('agentid', 'n'));
        $newObook->setVar('agent', $article->getVar('agent', 'n'));
        $newObook->setVar('reviewerid', $article->getVar('reviewerid', 'n'));
        $newObook->setVar('reviewer', $article->getVar('reviewer', 'n'));
        $newObook->setVar('posterid', $article->getVar('posterid', 'n'));
        $newObook->setVar('poster', $article->getVar('poster', 'n'));
        $newObook->setVar('unionid', $article->getVar('unionid', 'n'));
        $newObook->setVar('permission', $article->getVar('permission', 'n'));
        $newObook->setVar('firstflag', $article->getVar('firstflag', 'n'));
        $newObook->setVar('fullflag', $article->getVar('fullflag', 'n'));
        $newObook->setVar('imgflag', $article->getVar('imgflag', 'n'));
        $newObook->setVar('monthly', $article->getVar('monthly', 'n'));
        $newObook->setVar('rgroup', $article->getVar('rgroup', 'n'));
        $newObook->setVar('saleprice', $article->getVar('saleprice', 'n'));
        $newObook->setVar('vipprice', 0);
        $newObook->setVar('freestart', $article->getVar('freestart', 'n'));
        $newObook->setVar('freeend', $article->getVar('freeend', 'n'));
        $newObook->setVar('sumegold', 0);
        $newObook->setVar('sumesilver', 0);
        $newObook->setVar('sumtip', 0);
        $newObook->setVar('sumhurry', 0);
        $newObook->setVar('sumbesp', 0);
        $newObook->setVar('sumaward', 0);
        $newObook->setVar('sumagent', 0);
        $newObook->setVar('sumgift', 0);
        $newObook->setVar('sumother', 0);
        $newObook->setVar('sumemoney', 0);
        $newObook->setVar('summoney', 0);
        $newObook->setVar('paidmoney', 0);
        $newObook->setVar('paidemoney', 0);
        $newObook->setVar('paytime', 0);
        $newObook->setVar('normalsale', 0);
        $newObook->setVar('vipsale', 0);
        $newObook->setVar('freesale', 0);
        $newObook->setVar('bespsale', 0);
        $newObook->setVar('totalsale', 0);
        $newObook->setVar('daysale', 0);
        $newObook->setVar('weeksale', 0);
        $newObook->setVar('monthsale', 0);
        $newObook->setVar('allsale', 0);
        $newObook->setVar('lastsale', 0);
        if (intval($article->getVar('issign', 'n')) < 10) {
            $newObook->setVar('display', 1);
        } else {
            $newObook->setVar('display', 0);
        }
        if (!$obook_handler->insert($newObook)) {
            return $jieqiLang['obook']['add_obook_failure'];
        } else {
            if ($article->getVar('vipid', 'n') != $newObook->getVar('obookid', 'n')) {
                $article->setVar('isvip', $isvip);
                $article->setVar('vipid', $newObook->getVar('obookid', 'n'));
                $article->unsetNew();
                $article_handler->insert($article);
            }
            $newObook->unsetNew();
            return $newObook;
        }
    }
}
include_once $jieqiModules['obook']['path'] . '/class/obook.php';
$obook_handler = JieqiObookHandler::getInstance('JieqiObookHandler');
include_once $jieqiModules['obook']['path'] . '/class/ochapter.php';
$ochapter_handler = JieqiOchapterHandler::getInstance('JieqiOchapterHandler');