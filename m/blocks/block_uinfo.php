<?php

class BlockSystemUinfo extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_uinfo.html';
    public $exevars = array('uid' => 'uid');
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (0 < strlen($varary[0])) {
                    $this->exevars['uid'] = $varary[0];
                }
            }
        }
        $this->getCacheid();
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiGroups;
        global $jieqiConfigs;
        global $jieqiHonors;
        global $jieqi_image_type;
        global $jieqiModules;
        if (0 < $this->blockvars['cacheid']) {
            include_once JIEQI_ROOT_PATH . '/class/users.php';
            $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
            $userobj = $users_handler->get($this->blockvars['cacheid']);
            if (is_object($userobj)) {
                include_once JIEQI_ROOT_PATH . '/include/funusers.php';
                $uservals = jieqi_system_usersvars($userobj);
                $jieqiTpl->assign_by_ref('uservals', $uservals);
                foreach ($uservals as $k => $v) {
                    $jieqiTpl->assign_by_ref($k, $uservals[$k]);
                }
            }
        }
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