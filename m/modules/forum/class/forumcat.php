<?php
jieqi_includedb();
class JieqiForumcat extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('catid', JIEQI_TYPE_INT, 0, '序号', false, 6);
        $this->initVar('cattitle', JIEQI_TYPE_TXTBOX, '', '类别名称', true, 100);
        $this->initVar('catorder', JIEQI_TYPE_INT, 0, '排序', false, 6);
    }
}
class JieqiForumcatHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'forumcat';
        $this->autoid = 'catid';
        $this->dbname = 'forum_forumcat';
    }
}