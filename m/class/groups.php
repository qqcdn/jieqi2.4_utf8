<?php
jieqi_includedb();
class JieqiGroups extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('groupid', JIEQI_TYPE_INT, 0, '序号', false, 5);
        $this->initVar('name', JIEQI_TYPE_TXTBOX, '', '用户组名称', true, 50);
        $this->initVar('description', JIEQI_TYPE_TXTAREA, '', '描述', false, NULL);
        $this->initVar('grouptype', JIEQI_TYPE_INT, 0, '类型', false, 1);
    }
}
class JieqiGroupsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'groups';
        $this->autoid = 'groupid';
        $this->dbname = 'system_groups';
    }
}
