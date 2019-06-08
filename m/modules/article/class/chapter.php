<?php
jieqi_includedb();
class JieqiChapter extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('chapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 11);
        $this->initVar('sourceid', JIEQI_TYPE_INT, 0, '来源小说序号', false, 11);
        $this->initVar('sourcecid', JIEQI_TYPE_INT, 0, '来源章节序号', false, 11);
        $this->initVar('sourcecorder', JIEQI_TYPE_INT, 0, '来源章节排序', false, 11);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', true, 11);
        $this->initVar('articlename', JIEQI_TYPE_TXTBOX, '', '小说名称', false, 250);
        $this->initVar('volumeid', JIEQI_TYPE_INT, 0, '卷序号', true, 11);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表者序号', false, 11);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表者', false, 30);
        $this->initVar('postdate', JIEQI_TYPE_INT, 0, '发表日期', false, 11);
        $this->initVar('lastupdate', JIEQI_TYPE_INT, 0, '最后更新', false, 11);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节标题', true, 250);
        $this->initVar('chapterorder', JIEQI_TYPE_INT, 0, '章节排序', false, 6);
        $this->initVar('words', JIEQI_TYPE_INT, 0, '字节数', false, 11);
        $this->initVar('saleprice', JIEQI_TYPE_INT, 0, '销售价格', false, 11);
        $this->initVar('salenum', JIEQI_TYPE_INT, 0, '销售量', false, 11);
        $this->initVar('totalcost', JIEQI_TYPE_INT, 0, '总销售额', false, 11);
        $this->initVar('attachment', JIEQI_TYPE_TXTAREA, '', '附件', false, NULL);
        $this->initVar('summary', JIEQI_TYPE_TXTAREA, '', '摘要', false, NULL);
        $this->initVar('preface', JIEQI_TYPE_TXTAREA, '', '序言', false, NULL);
        $this->initVar('notice', JIEQI_TYPE_TXTAREA, '', '公告', false, NULL);
        $this->initVar('foreword', JIEQI_TYPE_TXTAREA, '', '点评', false, NULL);
        $this->initVar('isbody', JIEQI_TYPE_TXTAREA, '', '是否正文', false, NULL);
        $this->initVar('isimage', JIEQI_TYPE_INT, 0, '是否图片章节', false, 1);
        $this->initVar('isvip', JIEQI_TYPE_INT, 0, '是否VIP', false, 1);
        $this->initVar('pages', JIEQI_TYPE_INT, 0, '章节页数', false, 1);
        $this->initVar('chaptertype', JIEQI_TYPE_INT, 0, '章节类型', false, 1);
        $this->initVar('power', JIEQI_TYPE_INT, 0, '访问级别', false, 1);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '显示', false, 1);
    }
}
class JieqiChapterHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'chapter';
        $this->autoid = 'chapterid';
        $this->dbname = 'article_chapter';
    }
}