<?php
jieqi_includedb();

class JieqiHonors extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('honorid', JIEQI_TYPE_INT, 0, '序号', false, 5);
        $this->initVar('caption', JIEQI_TYPE_TXTBOX, '', '头衔名称', true, 50);
        $this->initVar('minscore', JIEQI_TYPE_INT, 0, '最小积分', false, 11);
        $this->initVar('maxscore', JIEQI_TYPE_INT, 0, '最大积分', false, 11);
        $this->initVar('setting', JIEQI_TYPE_TXTAREA, '', '设置', false, NULL);
        $this->initVar('honortype', JIEQI_TYPE_INT, 0, '类型', false, 1);
    }
}
class JieqiHonorsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'honors';
        $this->autoid = 'honorid';
        $this->dbname = 'system_honors';
    }
}
