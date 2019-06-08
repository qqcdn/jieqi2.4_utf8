<?php
jieqi_includedb();
class JieqiBookcase extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('caseid', JIEQI_TYPE_INT, 0, '书架序号', false, 11);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', true, 11);
        $this->initVar('articlename', JIEQI_TYPE_TXTBOX, '', '小说名称', false, 250);
        $this->initVar('classid', JIEQI_TYPE_INT, 0, '分类序号', false, 3);
        $this->initVar('userid', JIEQI_TYPE_INT, 0, '用户序号', true, 11);
        $this->initVar('username', JIEQI_TYPE_TXTBOX, '', '用户名', false, 30);
        $this->initVar('chapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节名称', false, 250);
        $this->initVar('chapterorder', JIEQI_TYPE_INT, 0, '章节次序', false, 6);
        $this->initVar('joindate', JIEQI_TYPE_INT, 0, '收藏日期', false, 11);
        $this->initVar('lastvisit', JIEQI_TYPE_INT, 0, '最后访问', false, 11);
        $this->initVar('flag', JIEQI_TYPE_INT, 0, '标志', false, 1);
    }
}
class JieqiBookcaseHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'bookcase';
        $this->autoid = 'caseid';
        $this->dbname = 'article_bookcase';
    }
}