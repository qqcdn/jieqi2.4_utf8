<?php
jieqi_includedb();
class JieqiRight extends JieqiObjectData
{
    public function __construct()
    {
        $this->initVar('rid', JIEQI_TYPE_INT, 0, '序号', false, 8);
        $this->initVar('modname', JIEQI_TYPE_TXTBOX, '', '模块名称', true, 50);
        $this->initVar('rname', JIEQI_TYPE_TXTBOX, '', '权利名称', true, 50);
        $this->initVar('rtitle', JIEQI_TYPE_TXTBOX, '', '权利标题', false, 50);
        $this->initVar('rdescription', JIEQI_TYPE_TXTAREA, '', '权利描述', false, NULL);
        $this->initVar('rhonors', JIEQI_TYPE_TXTAREA, '', '权利描述', false, NULL);
    }
}
class JieqiRightHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'right';
        $this->autoid = 'bid';
        $this->dbname = 'system_right';
    }
    public function getSavedVars($modname)
    {
        global $jieqiRight;
        $criteria = new CriteriaCompo(new Criteria('modname', $modname, '='));
        $criteria->setSort('rid');
        $criteria->setOrder('ASC');
        $this->queryObjects($criteria);
        while ($v = $this->getObject()) {
            $jieqiRight[$modname][$v->getVar('rname', 'n')] = array('caption' => $v->getVar('rtitle'), 'honors' => jieqi_unserialize($v->getVar('rhonors', 'n')), 'rescription' => $v->getVar('rdescription'));
        }
    }
}
