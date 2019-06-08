<?php

class BlockSystemGrouplist extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_grouplist.html';
    public function setContent($isreturn = false)
    {
        global $jieqiSort;
        global $jieqiTpl;
        global $jieqiGroups;
        $grouprows = array();
        $i = 0;
        foreach ($jieqiGroups as $k => $v) {
            if ($k != JIEQI_GROUP_GUEST) {
                $grouprows[$i]['groupid'] = $k;
                $grouprows[$i]['groupname'] = $v;
                $i++;
            }
        }
        $jieqiTpl->assign_by_ref('grouprows', $grouprows);
    }
}