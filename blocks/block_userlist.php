<?php

class BlockSystemUserlist extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_userlist.html';
    public $exevars = array('field' => 'score', 'listnum' => 15, 'groupid' => '0', 'asc' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('uid', 'score', 'monthscore', 'weekscore', 'dayscore', 'experience', 'regdate', 'lastlogin', 'credit', 'goodnum', 'badnum', 'expenses', 'overtime'))) {
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
                $tmpvar = str_replace('|', '', $varary[2]);
                if (is_numeric($tmpvar)) {
                    $this->exevars['groupid'] = $varary[2];
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (in_array($varary[3], array('0', '1'))) {
                    $this->exevars['asc'] = $varary[3];
                }
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiModules;
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
        $criteria = new CriteriaCompo();
        if (!empty($this->exevars['groupid'])) {
            $groupary = explode('|', $this->exevars['groupid']);
            foreach ($groupary as $v) {
                if (is_numeric($v)) {
                    $criteria->add(new Criteria('groupid', intval($v), '='), 'OR');
                }
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
        $users_handler->queryObjects($criteria);
        $userrows = array();
        $k = 0;
        include_once JIEQI_ROOT_PATH . '/include/funusers.php';
        while ($v = $users_handler->getObject()) {
            $userrows[$k] = jieqi_system_usersvars($v);
            $k++;
        }
        $jieqiTpl->assign_by_ref('userrows', $userrows);
        $jieqiTpl->assign('sort', $this->exevars['field']);
        $jieqiTpl->assign('url_more', JIEQI_URL . '/topuser.php?sort=' . $this->exevars['field']);
    }
}