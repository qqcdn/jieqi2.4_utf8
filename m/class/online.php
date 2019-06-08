<?php
jieqi_includedb();
class JieqiOnline extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('uid', JIEQI_TYPE_INT, 0, '用户序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('sid', JIEQI_TYPE_TXTBOX, '', 'SESSION序号', false, 32);
        $this->initVar('uname', JIEQI_TYPE_TXTBOX, '', '用户名', false, 30);
        $this->initVar('name', JIEQI_TYPE_TXTBOX, '', '昵称', false, 30);
        $this->initVar('pass', JIEQI_TYPE_TXTBOX, '', '密码', false, 32);
        $this->initVar('email', JIEQI_TYPE_TXTBOX, '', 'Email', false, 60);
        $this->initVar('groupid', JIEQI_TYPE_INT, 0, '用户组序号', false, 3);
        $this->initVar('logintime', JIEQI_TYPE_INT, 0, '登陆时间', false, 11);
        $this->initVar('updatetime', JIEQI_TYPE_INT, 0, '更新时间', false, 11);
        $this->initVar('operate', JIEQI_TYPE_TXTBOX, '', '用户动作', false, 100);
        $this->initVar('ip', JIEQI_TYPE_TXTBOX, '', '用户IP', false, 25);
        $this->initVar('browser', JIEQI_TYPE_TXTBOX, '', '浏览器类型', false, 50);
        $this->initVar('os', JIEQI_TYPE_TXTBOX, '', '操作系统类型', false, 50);
        $this->initVar('location', JIEQI_TYPE_TXTBOX, '', '用户地理位置', false, 100);
        $this->initVar('state', JIEQI_TYPE_INT, 0, '状态', false, 1);
        $this->initVar('flag', JIEQI_TYPE_INT, 0, '标志', false, 1);
    }
}
class JieqiOnlineHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'online';
        $this->autoid = '';
        $this->dbname = 'system_online';
    }
}
