<?php

class BlockArticleUarticles extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_uarticles.html';
    public $exevars = array('order' => 'lastupdate', 'listnum' => 10, 'asc' => 0, 'uid' => 'uid', 'isfull' => 0);
    public function __construct(&$vars)
    {
        global $jieqiTpl;
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('articleid', 'postdate', 'lastupdate', 'allvisit', 'monthvisit', 'weekvisit', 'dayvisit', 'allvote', 'monthvote', 'weekvote', 'dayvote', 'words', 'goodnum', 'reviewsnum'))) {
                    $this->exevars['order'] = $varary[0];
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
                    $this->exevars['isfull'] = $varary[4];
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
        global $jieqiModules;
        jieqi_getconfigs('article', 'configs');
        jieqi_getconfigs('article', 'sort');
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $articlerows = array();
        if (0 < $this->blockvars['cacheid']) {
            include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/article.php';
            include_once $GLOBALS['jieqiModules']['article']['path'] . '/include/funarticle.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('authorid', $this->blockvars['cacheid']));
            if ($this->exevars['isfull'] == 1) {
                $criteria->add(new Criteria('isfull', 1));
            } else {
                if ($this->exevars['isfull'] == 2) {
                    $criteria->add(new Criteria('isfull', 0));
                }
            }
            $criteria->setSort($this->exevars['order']);
            if ($this->exevars['asc'] == 1) {
                $criteria->setOrder('ASC');
            } else {
                $criteria->setOrder('DESC');
            }
            $criteria->setLimit($this->exevars['listnum']);
            $criteria->setStart(0);
            $article_handler->queryObjects($criteria);
            $k = 0;
            while ($v = $article_handler->getObject()) {
                $articlerows[$k] = jieqi_article_vars($v);
                $articlerows[$k]['order'] = $k + 1;
                if (!isset($articlerows[$k]['ordervalue'])) {
                    $articlerows[$k]['ordervalue'] = $v->getVar($this->exevars['order']);
                }
                if ($articlerows[$k]['ordervalue'] === false) {
                    $articlerows[$k]['ordervalue'] = '';
                }
                if (is_numeric($articlerows[$k]['ordervalue'])) {
                    $articlerows[$k]['ordervalue'] = round($articlerows[$k]['ordervalue']);
                }
                if ($this->exevars['order'] == 'lastupdate' || $this->exevars['order'] == 'postdate' || $this->exevars['order'] == 'toptime' || $this->exevars['order'] == 'lastvote') {
                    $articlerows[$k]['ordervalue'] = date('m-d', $articlerows[$k]['ordervalue']);
                }
                $articlerows[$k]['visitnum'] = $articlerows[$k]['ordervalue'];
                $k++;
            }
        }
        $jieqiTpl->assign_by_ref('articlerows', $articlerows);
        $jieqiTpl->assign('ownerid', $this->blockvars['cacheid']);
        if (!empty($this->blockvars['cacheid'])) {
            $jieqiTpl->assign('url_more', $jieqiModules['article']['url'] . '/authorarticle.php?authorid=' . intval($this->blockvars['cacheid']));
        } else {
            $jieqiTpl->assign('url_more', jieqi_geturl('article', 'articlefilter', 1, array()));
        }
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