<?php
jieqi_includedb();
class JieqiReview extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('reviewid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('postdate', JIEQI_TYPE_INT, 0, '发表日期', false, 11);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', true, 11);
        $this->initVar('articlename', JIEQI_TYPE_TXTBOX, '', '小说名称', false, 250);
        $this->initVar('userid', JIEQI_TYPE_INT, 0, '用户序号', false, 11);
        $this->initVar('username', JIEQI_TYPE_TXTBOX, '', '用户名', false, 30);
        $this->initVar('reviewtitle', JIEQI_TYPE_TXTBOX, '', '评论标题', false, 250);
        $this->initVar('reviewtext', JIEQI_TYPE_TXTAREA, '', '评论内容', true, NULL);
        $this->initVar('topflag', JIEQI_TYPE_INT, 0, '置顶', false, 1);
        $this->initVar('goodflag', JIEQI_TYPE_INT, 0, '精华', false, 1);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '显示', false, 1);
    }
}
class JieqiReviewHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'review';
        $this->autoid = 'reviewid';
        $this->dbname = 'article_review';
    }
}