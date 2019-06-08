<?php

class BlockSystemUptopics extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_uptopics.html';
    public $exevars = array('field' => 'topicid', 'listnum' => 10, 'asc' => 0, 'uid' => 'uid', 'istop' => 0, 'isgood' => 0, 'islock' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('topicid', 'ownerid', 'posttime', 'replytime', 'views', 'replies', 'size'))) {
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
                    $this->exevars['istop'] = $varary[4];
                }
            }
            if (5 < $arynum) {
                $varary[5] = trim($varary[5]);
                if (in_array($varary[5], array('0', '1', '2'))) {
                    $this->exevars['isgood'] = $varary[5];
                }
            }
            if (6 < $arynum) {
                $varary[6] = trim($varary[6]);
                if (in_array($varary[6], array('0', '1', '2'))) {
                    $this->exevars['islock'] = $varary[6];
                }
            }
        }
        $this->getCacheid();
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        $ptopicrows = array();
        if (0 < $this->blockvars['cacheid']) {
            include_once JIEQI_ROOT_PATH . '/class/ptopics.php';
            $ptopics_handler = JieqiPtopicsHandler::getInstance('JieqiPtopicsHandler');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('ownerid', $this->blockvars['cacheid']));
            if ($this->exevars['istop'] == 1) {
                $criteria->add(new Criteria('istop', 1));
            } else {
                if ($this->exevars['istop'] == 2) {
                    $criteria->add(new Criteria('istop', 0));
                }
            }
            if ($this->exevars['isgood'] == 1) {
                $criteria->add(new Criteria('isgood', 1));
            } else {
                if ($this->exevars['isgood'] == 2) {
                    $criteria->add(new Criteria('isgood', 0));
                }
            }
            if ($this->exevars['islock'] == 1) {
                $criteria->add(new Criteria('islock', 1));
            } else {
                if ($this->exevars['islock'] == 2) {
                    $criteria->add(new Criteria('islock', 0));
                }
            }
            $criteria->setSort($this->exevars['field']);
            if ($this->exevars['asc'] == 1) {
                $criteria->setOrder('ASC');
            } else {
                $criteria->setOrder('DESC');
            }
            $criteria->setLimit($this->exevars['listnum']);
            $criteria->setStart(0);
            $ptopics_handler->queryObjects($criteria);
            $k = 0;
            while ($v = $ptopics_handler->getObject()) {
                $ptopicrows[$k]['istop'] = $v->getVar('istop');
                $ptopicrows[$k]['isgood'] = $v->getVar('isgood');
                $ptopicrows[$k]['islock'] = $v->getVar('islock');
                $ptopicrows[$k]['topicid'] = $v->getVar('topicid');
                $ptopicrows[$k]['posttime'] = $v->getVar('posttime');
                $ptopicrows[$k]['replytime'] = $v->getVar('replytime');
                $ptopicrows[$k]['posterid'] = $v->getVar('posterid');
                $ptopicrows[$k]['poster'] = $v->getVar('poster');
                $ptopicrows[$k]['title'] = $v->getVar('title');
                $ptopicrows[$k]['views'] = $v->getVar('views');
                $ptopicrows[$k]['replies'] = $v->getVar('replies');
                $ptopicrows[$k]['size'] = $v->getVar('size');
                $ptopicrows[$k]['size_c'] = ceil($v->getVar('size') / 2);
                $ptopicrows[$k]['ownerid'] = $v->getVar('ownerid');
                $k++;
            }
        }
        $jieqiTpl->assign_by_ref('ptopicrows', $ptopicrows);
        $jieqiTpl->assign('ownerid', $this->blockvars['cacheid']);
        $jieqiTpl->assign('url_more', JIEQI_URL . '/ptopics?oid=' . $this->blockvars['cacheid']);
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