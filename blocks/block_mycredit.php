<?php

class BlockSystemMycredit extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_mycredit.html';
    public $cachetime = -1;
    public $exevars = array('tid' => 'id');
    public function __construct(&$vars)
    {
        global $jieqiTpl;
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (is_numeric($varary[0])) {
                    $this->exevars['tid'] = intval($varary[0]);
                } else {
                    if (substr($varary[0], 0, 1) == '$') {
                        $tmpvar1 = $jieqiTpl->get_assign(substr($varary[0], 1));
                        $this->exevars['tid'] = intval($tmpvar1);
                    } else {
                        if (isset($_REQUEST[$varary[0]]) && is_numeric($_REQUEST[$varary[0]])) {
                            $this->exevars['tid'] = intval($_REQUEST[$varary[0]]);
                        }
                    }
                }
            }
        }
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiModules;
        global $jieqiConfigs;
        global $jieqiCredit;
        global $query;
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        if (!isset($jieqiCredit['system'])) {
            jieqi_getconfigs('system', 'credit', 'jieqiCredit');
        }
        $mycredits = array('uid' => $_SESSION['jieqiUserid'], 'credit' => 0, 'rank' => '', 'upcredit' => 0, 'nextcedit' => 0, 'nextrank' => '');
        if (is_numeric($this->exevars['tid']) && 0 < $this->exevars['tid'] && 0 < $_SESSION['jieqiUserId']) {
            $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_credit') . ' WHERE uid = ' . intval($_SESSION['jieqiUserId']) . ' AND tid = ' . intval($this->exevars['tid']) . ' LIMIT 0, 1';
            $query->execute($sql);
            $row = $query->getRow();
            if (is_array($row)) {
                $mycredits['credit'] = intval($row['point']);
            }
        }
        foreach ($jieqiCredit['system'] as $v) {
            $mincredit = 0;
            if ($v['minnum'] <= $mycredits['credit'] && $mincredit <= $v['minnum']) {
                $mycredits['rank'] = $v['caption'];
                $mincredit = $v['minnum'];
            }
            if ($mycredits['credit'] < $v['minnum'] && ($v['minnum'] < $mycredits['nextcedit'] || $mycredits['nextcedit'] == 0)) {
                $mycredits['nextcedit'] = $v['minnum'];
                $mycredits['nextrank'] = $v['caption'];
            }
        }
        $mycredits['upcredit'] = $mycredits['nextcedit'] - $mycredits['credit'];
        $jieqiTpl->assign_by_ref('mycredits', $mycredits);
    }
}