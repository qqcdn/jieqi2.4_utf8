<?php
jieqi_includedb();
class JieqiOchapter extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ochapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 11);
        $this->initVar('sourceid', JIEQI_TYPE_INT, 0, '来源序号', false, 11);
        $this->initVar('sourcecid', JIEQI_TYPE_INT, 0, '来源章节序号', false, 11);
        $this->initVar('sourcecorder', JIEQI_TYPE_INT, 0, '来源章节排序', false, 11);
        $this->initVar('obookid', JIEQI_TYPE_INT, 0, '电子书序号', false, 11);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', false, 11);
        $this->initVar('chapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('postdate', JIEQI_TYPE_INT, 0, '加入日期', false, 11);
        $this->initVar('lastupdate', JIEQI_TYPE_INT, 0, '更新日期', false, 11);
        $this->initVar('buytime', JIEQI_TYPE_INT, 0, '最后购买日期', false, 11);
        $this->initVar('obookname', JIEQI_TYPE_TXTBOX, '', '电子书名', true, 100);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节名', true, 100);
        $this->initVar('chaptertype', JIEQI_TYPE_INT, 0, '章节类型', false, 1);
        $this->initVar('chapterorder', JIEQI_TYPE_INT, 0, '章节排序', false, 6);
        $this->initVar('volumeid', JIEQI_TYPE_INT, 0, '分卷序号', false, 11);
        $this->initVar('summary', JIEQI_TYPE_TXTAREA, '', '内容简介', false, NULL);
        $this->initVar('preface', JIEQI_TYPE_TXTAREA, '', '序言', false, NULL);
        $this->initVar('notice', JIEQI_TYPE_TXTAREA, '', '公告', false, NULL);
        $this->initVar('foreword', JIEQI_TYPE_TXTAREA, '', '点评', false, NULL);
        $this->initVar('isbody', JIEQI_TYPE_TXTAREA, '', '是否正文', false, NULL);
        $this->initVar('words', JIEQI_TYPE_INT, 0, '字数', false, 11);
        $this->initVar('pages', JIEQI_TYPE_INT, 0, '章节页数', false, 1);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表者序号', false, 11);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表者', false, 50);
        $this->initVar('toptime', JIEQI_TYPE_INT, 0, '置顶时间', false, 11);
        $this->initVar('picflag', JIEQI_TYPE_INT, 0, '图片标志', false, 1);
        $this->initVar('saleprice', JIEQI_TYPE_INT, 0, '销售价格', false, 11);
        $this->initVar('vipprice', JIEQI_TYPE_INT, 0, '优惠价格', false, 11);
        $this->initVar('sumegold', JIEQI_TYPE_INT, 0, '金币总销售额', false, 11);
        $this->initVar('sumesilver', JIEQI_TYPE_INT, 0, '银币总销售额', false, 11);
        $this->initVar('normalsale', JIEQI_TYPE_INT, 0, '普通价格销售量', false, 11);
        $this->initVar('vipsale', JIEQI_TYPE_INT, 0, 'VIP价格销售量', false, 11);
        $this->initVar('freesale', JIEQI_TYPE_INT, 0, '免费阅读销售量', false, 11);
        $this->initVar('bespsale', JIEQI_TYPE_INT, 0, '包月阅读销售量', false, 11);
        $this->initVar('totalsale', JIEQI_TYPE_INT, 0, '合计销售量', false, 11);
        $this->initVar('daysale', JIEQI_TYPE_INT, 0, '本日销售量', false, 11);
        $this->initVar('weeksale', JIEQI_TYPE_INT, 0, '本周销售量', false, 11);
        $this->initVar('monthsale', JIEQI_TYPE_INT, 0, '本月销售量', false, 11);
        $this->initVar('allsale', JIEQI_TYPE_INT, 0, '总销售量', false, 11);
        $this->initVar('lastsale', JIEQI_TYPE_INT, 0, '最后销售时间', false, 11);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '是否显示', false, 1);
    }
}
class JieqiOchapterHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'ochapter';
        $this->autoid = 'ochapterid';
        $this->dbname = 'obook_ochapter';
    }
}
