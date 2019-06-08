<?php
jieqi_includedb();
class JieqiForums extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('forumid', JIEQI_TYPE_INT, 0, '序号', false, 6);
        $this->initVar('catid', JIEQI_TYPE_INT, 0, '类别序号', false, 6);
        $this->initVar('forumname', JIEQI_TYPE_TXTBOX, '', '论坛名称', true, 60);
        $this->initVar('forumdesc', JIEQI_TYPE_TXTAREA, '', '论坛描述', false, 255);
        $this->initVar('forumstatus', JIEQI_TYPE_INT, 0, '论坛状态', false, 4);
        $this->initVar('forumorder', JIEQI_TYPE_INT, 0, '论坛排序', false, 6);
        $this->initVar('forumtype', JIEQI_TYPE_INT, 0, '论坛类型', false, 1);
        $this->initVar('forumtopics', JIEQI_TYPE_INT, 0, '论坛主题数', false, 11);
        $this->initVar('forumposts', JIEQI_TYPE_INT, 0, '论坛帖子数', false, 11);
        $this->initVar('forumlastinfo', JIEQI_TYPE_TXTBOX, '', '最后发表', false, 255);
        $this->initVar('authview', JIEQI_TYPE_TXTBOX, '', '是否可见', false, 255);
        $this->initVar('authread', JIEQI_TYPE_TXTBOX, '', '允许阅读', false, 255);
        $this->initVar('authpost', JIEQI_TYPE_TXTBOX, '', '允许发表', false, 255);
        $this->initVar('authreply', JIEQI_TYPE_TXTBOX, '', '允许回复', false, 255);
        $this->initVar('authupload', JIEQI_TYPE_TXTBOX, '', '允许上传', false, 255);
        $this->initVar('authedit', JIEQI_TYPE_TXTBOX, '', '允许编辑', false, 255);
        $this->initVar('authdelete', JIEQI_TYPE_TXTBOX, '', '允许删除', false, 255);
        $this->initVar('master', JIEQI_TYPE_TXTBOX, '', '论坛斑竹', false, 255);
    }
}
class JieqiForumsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'forums';
        $this->autoid = 'forumid';
        $this->dbname = 'forum_forums';
    }
}