<?php
jieqi_includedb();
class JieqiPower extends JieqiObjectData
{
    public function __construct()
    {
        $this->initVar('pid', JIEQI_TYPE_INT, 0, '序号', false, 8);
        $this->initVar('modname', JIEQI_TYPE_TXTBOX, '', '模块名称', true, 50);
        $this->initVar('pname', JIEQI_TYPE_TXTBOX, '', '权限名称', true, 50);
        $this->initVar('ptitle', JIEQI_TYPE_TXTBOX, '', '权限标题', false, 50);
        $this->initVar('pdescription', JIEQI_TYPE_TXTAREA, '', '权限描述', false, NULL);
        $this->initVar('pgroups', JIEQI_TYPE_TXTAREA, '', '权限描述', false, NULL);
    }
}
class JieqiPowerHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'power';
        $this->autoid = 'pid';
        $this->dbname = 'system_power';
    }
    public function getSavedVars($modname)
    {
        global $jieqiPower;
        $criteria = new CriteriaCompo(new Criteria('modname', $modname, '='));
        $criteria->setSort('pid');
        $criteria->setOrder('ASC');
        $this->queryObjects($criteria);
        while ($v = $this->getObject()) {
            $jieqiPower[$modname][$v->getVar('pname', 'n')] = array('caption' => $v->getVar('ptitle'), 'groups' => jieqi_unserialize($v->getVar('pgroups', 'n')), 'description' => $v->getVar('pdescription'));
        }
    }
}
