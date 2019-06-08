<?php
jieqi_includedb();

class JieqiUserlog extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('logid', JIEQI_TYPE_INT, 0, '日志序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('logtime', JIEQI_TYPE_INT, 0, '操作时间', false, 11);
        $this->initVar('fromid', JIEQI_TYPE_INT, 0, '操作者id', false, 11);
        $this->initVar('fromname', JIEQI_TYPE_TXTBOX, '', '操作者', false, 30);
        $this->initVar('toid', JIEQI_TYPE_INT, 0, '影响者id', false, 11);
        $this->initVar('toname', JIEQI_TYPE_TXTBOX, '', '影响者', false, 30);
        $this->initVar('reason', JIEQI_TYPE_TXTAREA, '', '修改原因', false, NULL);
        $this->initVar('chginfo', JIEQI_TYPE_TXTAREA, '', '修改描述', false, NULL);
        $this->initVar('chglog', JIEQI_TYPE_TXTAREA, '', '修改记录', false, NULL);
        $this->initVar('isdel', JIEQI_TYPE_INT, 0, '是否删除', false, 1);
        $this->initVar('userlog', JIEQI_TYPE_TXTAREA, '', '用户资料备份', false, NULL);
    }
}
class JieqiUserlogHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'userlog';
        $this->autoid = 'logid';
        $this->dbname = 'system_userlog';
    }
}
