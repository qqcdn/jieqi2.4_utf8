<?php
jieqi_includedb();
class JieqiDraft extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('draftid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', true, 11);
        $this->initVar('articlename', JIEQI_TYPE_TXTBOX, '', '小说名称', false, 250);
        $this->initVar('volumeid', JIEQI_TYPE_INT, 0, '分卷序号', false, 11);
        $this->initVar('volumename', JIEQI_TYPE_TXTBOX, '', '分卷名称', false, 100);
        $this->initVar('chapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('chapterorder', JIEQI_TYPE_INT, 0, '章节次序', false, 11);
        $this->initVar('chaptertype', JIEQI_TYPE_INT, 0, '章节类型', false, 1);
        $this->initVar('isvip', JIEQI_TYPE_INT, 0, '是否vip', false, 1);
        $this->initVar('obookid', JIEQI_TYPE_INT, 0, '电子书序号', false, 11);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表者序号', false, 11);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表者', false, 30);
        $this->initVar('postdate', JIEQI_TYPE_INT, 0, '发表日期', false, 11);
        $this->initVar('lastupdate', JIEQI_TYPE_INT, 0, '最后更新', false, 11);
        $this->initVar('ispub', JIEQI_TYPE_INT, 0, '是否发布', false, 1);
        $this->initVar('pubdate', JIEQI_TYPE_INT, 0, '定时发表时间', false, 11);
        $this->initVar('saleprice', JIEQI_TYPE_INT, 0, '销售价格', false, 11);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节标题', true, 250);
        $this->initVar('chaptercontent', JIEQI_TYPE_TXTAREA, '', '章节内容', true, NULL);
        $this->initVar('words', JIEQI_TYPE_INT, 0, '字数', false, 11);
        $this->initVar('note', JIEQI_TYPE_TXTAREA, '', '备注', false, NULL);
        $this->initVar('attachment', JIEQI_TYPE_TXTAREA, '', '附件信息', false, NULL);
        $this->initVar('summary', JIEQI_TYPE_TXTAREA, '', '摘要', false, NULL);
        $this->initVar('preface', JIEQI_TYPE_TXTAREA, '', '序言', false, NULL);
        $this->initVar('notice', JIEQI_TYPE_TXTAREA, '', '公告', false, NULL);
        $this->initVar('foreword', JIEQI_TYPE_TXTAREA, '', '点评', false, NULL);
        $this->initVar('isbody', JIEQI_TYPE_TXTAREA, '', '是否正文', false, NULL);
        $this->initVar('isimage', JIEQI_TYPE_INT, 0, '是否图片', false, 1);
        $this->initVar('power', JIEQI_TYPE_INT, 0, '阅读权限', false, 1);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '是否显示', false, 1);
        $this->initVar('draftflag', JIEQI_TYPE_INT, 0, '草稿标志', false, 1);
    }
}
class JieqiDraftHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'draft';
        $this->autoid = 'draftid';
        $this->dbname = 'article_draft';
    }
}