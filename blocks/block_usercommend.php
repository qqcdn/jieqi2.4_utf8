<?php

class BlockSystemUsercommend extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_usercommend.html';
    public $exevars = array();
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $tmpary = explode('|', trim($this->blockvars['vars']));
            foreach ($tmpary as $v) {
                $v = trim($v);
                if (is_numeric($v)) {
                    $this->exevars[] = intval($v);
                }
            }
            $this->exevars = array_unique($this->exevars);
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiModules;
        $userrows = array();
        if (0 < count($this->exevars)) {
            include_once JIEQI_ROOT_PATH . '/class/users.php';
            $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('uid', '(0,' . implode(',', $this->exevars) . ')', 'IN'));
            $criteria->setLimit(100);
            $criteria->setStart(0);
            $users_handler->queryObjects($criteria);
            $k = 0;
            include_once JIEQI_ROOT_PATH . '/include/funusers.php';
            while ($v = $users_handler->getObject()) {
                $userrows[$k] = jieqi_system_usersvars($v);
                $k++;
            }
        }
        $i = 0;
        $maxrow = count($userrows);
        $sortrows = array();
        foreach ($this->exevars as $uid) {
            $k = 0;
            while ($k < $maxrow && $userrows[$k]['uid'] != $uid) {
                $k++;
            }
            if ($k < $maxrow) {
                $userrows[$k]['order'] = $i + 1;
                $sortrows[$i] =& $userrows[$k];
                $i++;
            }
        }
        $jieqiTpl->assign_by_ref('userrows', $sortrows);
    }
}