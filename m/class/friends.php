<?php
jieqi_includedb();
class JieqiFriends extends JieqiObjectData
{
    public function __construct()
    {
        $this->initVar('friendsid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('adddate', JIEQI_TYPE_INT, 0, '加入日期', false, 11);
        $this->initVar('myid', JIEQI_TYPE_INT, 0, '我的序号', false, 11);
        $this->initVar('myname', JIEQI_TYPE_TXTBOX, '', '我的名称', false, 30);
        $this->initVar('yourid', JIEQI_TYPE_INT, 0, '朋友序号', false, 11);
        $this->initVar('yourname', JIEQI_TYPE_TXTBOX, '', '朋友名称', false, 30);
        $this->initVar('teamid', JIEQI_TYPE_INT, 0, '分组序号', false, 11);
        $this->initVar('team', JIEQI_TYPE_TXTBOX, '', '分组名称', false, 50);
        $this->initVar('fset', JIEQI_TYPE_TXTAREA, '', '朋友设置', false, NULL);
        $this->initVar('state', JIEQI_TYPE_INT, 0, '状态', false, 1);
        $this->initVar('flag', JIEQI_TYPE_INT, 0, '标志', false, 1);
    }
}
class JieqiFriendsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'friends';
        $this->autoid = 'friendsid';
        $this->dbname = 'system_friends';
    }
}
