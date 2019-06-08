<?php
jieqi_includedb();
class JieqiArticlelog extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('logid', JIEQI_TYPE_INT, 0, '日志序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('logtime', JIEQI_TYPE_INT, 0, '操作时间', false, 11);
        $this->initVar('userid', JIEQI_TYPE_INT, 0, '操作者id', false, 11);
        $this->initVar('username', JIEQI_TYPE_TXTBOX, '', '操作者', false, 30);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', false, 11);
        $this->initVar('articlename', JIEQI_TYPE_TXTBOX, '', '小说名', false, 255);
        $this->initVar('chapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节名', false, 255);
        $this->initVar('reason', JIEQI_TYPE_TXTAREA, '', '修改原因', false, NULL);
        $this->initVar('chginfo', JIEQI_TYPE_TXTAREA, '', '修改描述', false, NULL);
        $this->initVar('chglog', JIEQI_TYPE_TXTAREA, '', '修改记录', false, NULL);
        $this->initVar('ischapter', JIEQI_TYPE_INT, 0, '是否章节', false, 1);
        $this->initVar('isdel', JIEQI_TYPE_INT, 0, '是否删除', false, 1);
        $this->initVar('databak', JIEQI_TYPE_TXTAREA, '', '信息备份', false, NULL);
    }
}
class JieqiArticlelogHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'articlelog';
        $this->autoid = 'logid';
        $this->dbname = 'article_articlelog';
    }
}