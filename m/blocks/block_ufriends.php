<?php

class BlockSystemUfriends extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_ufriends.html';
    public $exevars = array('field' => 'friendsid', 'listnum' => 10, 'asc' => 0, 'uid' => 'uid', 'state' => 0, 'flag' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('friendsid', 'adddate'))) {
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
                if (is_numeric($varary[4])) {
                    $this->exevars['state'] = $varary[4];
                }
            }
            if (5 < $arynum) {
                $varary[5] = trim($varary[5]);
                if (is_numeric($varary[5])) {
                    $this->exevars['flag'] = $varary[5];
                }
            }
        }
        $this->getCacheid();
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        $friendrows = array();
        if (0 < $this->blockvars['cacheid']) {
            include_once JIEQI_ROOT_PATH . '/class/friends.php';
            $friends_handler = JieqiFriendsHandler::getInstance('JieqiFriendsHandler');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('myid', $this->blockvars['cacheid']));
            if ($this->exevars['state'] == 1) {
                $criteria->add(new Criteria('state', 1));
            } else {
                if ($this->exevars['state'] == 2) {
                    $criteria->add(new Criteria('state', 0));
                }
            }
            if ($this->exevars['flag'] == 1) {
                $criteria->add(new Criteria('flag', 1));
            } else {
                if ($this->exevars['flag'] == 2) {
                    $criteria->add(new Criteria('flag', 0));
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
            $friends_handler->queryObjects($criteria);
            $k = 0;
            while ($v = $friends_handler->getObject()) {
                $friendrows[$k]['friendsid'] = $v->getVar('friendsid');
                $friendrows[$k]['adddate'] = $v->getVar('adddate');
                $friendrows[$k]['myid'] = $v->getVar('myid');
                $friendrows[$k]['myname'] = $v->getVar('myname');
                $friendrows[$k]['yourid'] = $v->getVar('yourid');
                $friendrows[$k]['yourname'] = $v->getVar('yourname');
                $friendrows[$k]['teamid'] = $v->getVar('teamid');
                $friendrows[$k]['team'] = $v->getVar('team');
                $friendrows[$k]['fset'] = $v->getVar('fset');
                $friendrows[$k]['state'] = $v->getVar('state');
                $friendrows[$k]['flag'] = $v->getVar('flag');
                $k++;
            }
        }
        $jieqiTpl->assign_by_ref('friendrows', $friendrows);
        $jieqiTpl->assign('ownerid', $this->blockvars['cacheid']);
        $jieqiTpl->assign('url_more', JIEQI_URL . '/userfriends?uid=' . $this->blockvars['cacheid']);
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