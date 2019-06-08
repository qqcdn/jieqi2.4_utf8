<?php

class BlockArticleUbookcase extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_ubookcase.html';
    public $exevars = array('field' => 'lastupdate', 'listnum' => 10, 'asc' => 0, 'uid' => 'uid', 'flag' => 0);
    public function __construct(&$vars)
    {
        global $jieqiTpl;
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('articleid', 'lastupdate', 'caseid', 'joindate', 'lastvisit'))) {
                    $this->exevars['field'] = $varary[0];
                }
            }
            if (1 < $arynum) {
                $varary[1] = trim($varary[1]);
                if (is_numeric($varary[1]) && 0 < $varary[1]) {
                    $this->exevars['listnum'] = intval($varary[1]);
                }
            }
            if (2 < $arynum) {
                $varary[2] = trim($varary[2]);
                if (in_array($varary[2], array('0', '1'))) {
                    $this->exevars['asc'] = $varary[2];
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (0 < strlen($varary[3])) {
                    $this->exevars['uid'] = $varary[3];
                }
            }
            if (4 < $arynum) {
                $varary[4] = trim($varary[4]);
                if (in_array($varary[4], array('0', '1', '2'))) {
                    $this->exevars['flag'] = $varary[4];
                }
            }
        }
        $this->getCacheid();
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiConfigs;
        global $jieqiSort;
        jieqi_getconfigs('article', 'configs');
        jieqi_getconfigs('article', 'sort');
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $bookcaserows = array();
        if (0 < $this->blockvars['cacheid']) {
            jieqi_includedb();
            $bookcase_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('c.userid', $this->blockvars['cacheid']));
            if ($this->exevars['flag'] == 1) {
                $criteria->add(new Criteria('flag', 1));
            } else {
                if ($this->exevars['flag'] == 2) {
                    $criteria->add(new Criteria('flag', 0));
                }
            }
            $criteria->setTables(jieqi_dbprefix('article_bookcase') . ' c LEFT JOIN ' . jieqi_dbprefix('article_article') . ' a ON c.articleid=a.articleid');
            $criteria->setFields('c.*, a.articleid, a.lastupdate, a.articlename, a.authorid, a.author, a.sortid, a.typeid, a.lastvolumeid, a.lastvolume, a.lastchapterid, a.lastchapter, a.lastsummary, a.freetime, a.viptime, a.isvip, a.vipid, a.vipvolumeid, a.vipvolume, a.vipchapterid, a.vipchapter, a.vipsummary');
            $tmpary = array('articleid' => 'a.articleid', 'lastupdate' => 'a.lastupdate', 'caseid' => 'c.caseid', 'joindate' => 'c.joindate', 'lastvisit' => 'c.lastvisit');
            $criteria->setSort($tmpary[$this->exevars['field']]);
            if ($this->exevars['asc'] == 1) {
                $criteria->setOrder('ASC');
            } else {
                $criteria->setOrder('DESC');
            }
            $criteria->setLimit($this->exevars['listnum']);
            $criteria->setStart(0);
            $bookcase_query->queryObjects($criteria);
            unset($criteria);
            $k = 0;
            while ($v = $bookcase_query->getObject()) {
                $bookcaserows[$k] = jieqi_query_rowvars($v, 's', 'article');
                $bookcaserows[$k]['sort'] = isset($jieqiSort['article'][$bookcaserows[$k]['sortid']]['caption']) ? $jieqiSort['article'][$bookcaserows[$k]['sortid']]['caption'] : '';
                $bookcaserows[$k]['sortname'] = $bookcaserows[$k]['sort'];
                $bookcaserows[$k]['type'] = isset($jieqiSort['article'][$bookcaserows[$k]['sortid']]['types'][$bookcaserows[$k]['typeid']]) ? $jieqiSort['article'][$bookcaserows[$k]['sortid']]['types'][$bookcaserows[$k]['typeid']] : '';
                $bookcaserows[$k]['typename'] = $bookcaserows[$k]['type'];
                if (!empty($bookcaserows[$k]['articlename'])) {
                    $bookcaserows[$k]['url_articleinfo'] = $article_dynamic_url . '/readbookcase.php?aid=' . $v->getVar('articleid') . '&bid=' . $v->getVar('caseid');
                    $bookcaserows[$k]['url_index'] = $bookcaserows[$k]['url_articleinfo'] . '&indexflag=1';
                } else {
                    $bookcaserows[$k]['url_articleinfo'] = '#';
                    $bookcaserows[$k]['url_index'] = '#';
                    $bookcaserows[$k]['articlename'] = $jieqiLang['article']['articlemark_has_deleted'];
                }
                if ($v->getVar('lastchapter') == '') {
                    $bookcaserows[$k]['url_lastchapter'] = '#';
                } else {
                    $bookcaserows[$k]['url_lastchapter'] = $article_dynamic_url . '/readbookcase.php?aid=' . $v->getVar('articleid') . '&bid=' . $v->getVar('caseid') . '&cid=' . $v->getVar('lastchapterid');
                }
                if ($v->getVar('vipchapter') == '') {
                    $bookcaserows[$k]['url_vipchapter'] = '#';
                } else {
                    $bookcaserows[$k]['url_vipchapter'] = $article_dynamic_url . '/readbookcase.php?aid=' . $v->getVar('articleid') . '&bid=' . $v->getVar('caseid') . '&cid=' . $v->getVar('vipchapterid');
                }
                if ($v->getVar('lastvisit') < $v->getVar('lastupdate')) {
                    $bookcaserows[$k]['hasnew'] = 1;
                } else {
                    $bookcaserows[$k]['hasnew'] = 0;
                }
                if ($v->getVar('chaptername') == '') {
                    $bookcaserows[$k]['articlemark'] = '';
                    $bookcaserows[$k]['url_articlemark'] = '#';
                } else {
                    $bookcaserows[$k]['articlemark'] = $v->getVar('chaptername');
                    if (0 < intval($v->getVar('flag', 'n'))) {
                        $bookcaserows[$k]['url_articlemark'] = $article_dynamic_url . '/readbookcase.php?oid=' . $v->getVar('vipid') . '&bid=' . $v->getVar('caseid') . '&ocid=' . $v->getVar('chapterid');
                    } else {
                        $bookcaserows[$k]['url_articlemark'] = $article_dynamic_url . '/readbookcase.php?aid=' . $v->getVar('articleid') . '&bid=' . $v->getVar('caseid') . '&cid=' . $v->getVar('chapterid');
                    }
                }
                $bookcaserows[$k]['url_delete'] = $article_dynamic_url . '/bookcase.php?bid=' . $v->getVar('caseid') . '&act=delete';
                $k++;
            }
        }
        $jieqiTpl->assign_by_ref('bookcaserows', $bookcaserows);
        $jieqiTpl->assign('ownerid', $this->blockvars['cacheid']);
    }
    public function getCacheid()
    {
        global $jieqiTpl;
        $this->blockvars['cacheid'] = 0;
        if (strval($this->exevars['uid']) != '0') {
            if ($this->exevars['uid'] == 'self') {
                $this->blockvars['cacheid'] = intval($_SESSION['jieqiUserId']);
            } else {
                if (is_numeric($this->exevars['uid'])) {
                    $this->blockvars['cacheid'] = intval($this->exevars['uid']);
                } else {
                    if (substr($this->exevars['uid'], 0, 1) == '$') {
                        $this->blockvars['cacheid'] = intval($jieqiTpl->get_assign(substr($this->exevars['uid'], 1)));
                    } else {
                        $this->blockvars['cacheid'] = intval($_REQUEST[$this->exevars['uid']]);
                    }
                }
            }
        }
        return $this->blockvars['cacheid'];
    }
}