<?php
jieqi_includedb();
class JieqiObook extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('obookid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('sourceid', JIEQI_TYPE_INT, 0, '来源序号', false, 11);
        $this->initVar('postdate', JIEQI_TYPE_INT, 0, '加入日期', false, 11);
        $this->initVar('lastupdate', JIEQI_TYPE_INT, 0, '更新日期', false, 11);
        $this->initVar('obookname', JIEQI_TYPE_TXTBOX, '', '电子书名', true, 100);
        $this->initVar('backupname', JIEQI_TYPE_TXTBOX, '', '备用标题', true, 50);
        $this->initVar('keywords', JIEQI_TYPE_TXTBOX, '', '关键字', false, 250);
        $this->initVar('roles', JIEQI_TYPE_TXTBOX, '', '人物', false, 200);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '相关小说序号', false, 11);
        $this->initVar('initial', JIEQI_TYPE_TXTBOX, '', '书名首字母', false, 1);
        $this->initVar('sortid', JIEQI_TYPE_INT, 0, '分类序号', false, 6);
        $this->initVar('typeid', JIEQI_TYPE_INT, 0, '子类序号', false, 3);
        $this->initVar('libid', JIEQI_TYPE_INT, 0, '所属书库', false, 3);
        $this->initVar('intro', JIEQI_TYPE_TXTAREA, '', '内容简介', false, NULL);
        $this->initVar('notice', JIEQI_TYPE_TXTAREA, '', '本书公告', false, NULL);
        $this->initVar('foreword', JIEQI_TYPE_TXTAREA, '', '编辑点评', false, NULL);
        $this->initVar('setting', JIEQI_TYPE_TXTAREA, '', '小说参数', false, NULL);
        $this->initVar('lastvolumeid', JIEQI_TYPE_INT, 0, '最新分卷序号', false, 11);
        $this->initVar('lastvolume', JIEQI_TYPE_TXTBOX, '', '最新分卷', false, 255);
        $this->initVar('lastchapterid', JIEQI_TYPE_INT, 0, '最新章节序号', false, 11);
        $this->initVar('lastchapter', JIEQI_TYPE_TXTBOX, '', '最新章节', false, 255);
        $this->initVar('lastsummary', JIEQI_TYPE_TXTAREA, '', '最新章节摘要', false, NULL);
        $this->initVar('chapters', JIEQI_TYPE_INT, 0, '章节数', false, 6);
        $this->initVar('words', JIEQI_TYPE_INT, 0, '字数', false, 11);
        $this->initVar('authorid', JIEQI_TYPE_INT, 0, '作者序号', false, 11);
        $this->initVar('author', JIEQI_TYPE_TXTBOX, '', '作者', false, 50);
        $this->initVar('agentid', JIEQI_TYPE_INT, 0, '责任编辑序号', false, 11);
        $this->initVar('agent', JIEQI_TYPE_TXTBOX, '', '责任编辑人', false, 50);
        $this->initVar('reviewerid', JIEQI_TYPE_INT, 0, '书评版主序号', false, 11);
        $this->initVar('reviewer', JIEQI_TYPE_TXTBOX, '', '书评版主', false, 30);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表者序号', false, 11);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表者', false, 50);
        $this->initVar('unionid', JIEQI_TYPE_INT, 0, '书盟网站ID', false, 1);
        $this->initVar('permission', JIEQI_TYPE_INT, 0, '授权类型', false, 1);
        $this->initVar('firstflag', JIEQI_TYPE_INT, 0, '首发标志', false, 1);
        $this->initVar('fullflag', JIEQI_TYPE_INT, 0, '书籍发全标志', false, 1);
        $this->initVar('imgflag', JIEQI_TYPE_INT, 0, '图片标志', false, 1);
        $this->initVar('monthly', JIEQI_TYPE_INT, 0, '是否包月', false, 1);
        $this->initVar('rgroup', JIEQI_TYPE_INT, 0, '男生女生', false, 1);
        $this->initVar('saleprice', JIEQI_TYPE_INT, 0, '销售价格', false, 11);
        $this->initVar('vipprice', JIEQI_TYPE_INT, 0, '优惠价格', false, 11);
        $this->initVar('freestart', JIEQI_TYPE_INT, 0, '限免开始时间', false, 11);
        $this->initVar('freeend', JIEQI_TYPE_INT, 0, '限免结束时间', false, 11);
        $this->initVar('sumegold', JIEQI_TYPE_INT, 0, '金币总销售额', false, 11);
        $this->initVar('sumesilver', JIEQI_TYPE_INT, 0, '银币总销售额', false, 11);
        $this->initVar('sumtip', JIEQI_TYPE_INT, 0, '打赏总额', false, 11);
        $this->initVar('sumhurry', JIEQI_TYPE_INT, 0, '催更总额', false, 11);
        $this->initVar('sumbesp', JIEQI_TYPE_INT, 0, '包月收入总额', false, 11);
        $this->initVar('sumaward', JIEQI_TYPE_INT, 0, '奖金总额', false, 11);
        $this->initVar('sumagent', JIEQI_TYPE_INT, 0, '代理销售总额', false, 11);
        $this->initVar('sumgift', JIEQI_TYPE_INT, 0, '礼品收入总额', false, 11);
        $this->initVar('sumother', JIEQI_TYPE_INT, 0, '其它收入总额', false, 11);
        $this->initVar('sumemoney', JIEQI_TYPE_INT, 0, '虚拟币收入总额', false, 11);
        $this->initVar('summoney', JIEQI_TYPE_INT, 0, '作者总提成金额', false, 11);
        $this->initVar('paidmoney', JIEQI_TYPE_INT, 0, '已付提成金额', false, 11);
        $this->initVar('paidemoney', JIEQI_TYPE_INT, 0, '已付提成虚拟币', false, 11);
        $this->initVar('paytime', JIEQI_TYPE_INT, 0, '支付时间', false, 11);
        $this->initVar('normalsale', JIEQI_TYPE_INT, 0, '普通价格销售量', false, 11);
        $this->initVar('vipsale', JIEQI_TYPE_INT, 0, 'VIP价格销售量', false, 11);
        $this->initVar('freesale', JIEQI_TYPE_INT, 0, '免费阅读销售量', false, 11);
        $this->initVar('bespsale', JIEQI_TYPE_INT, 0, '包月阅读销售量', false, 11);
        $this->initVar('totalsale', JIEQI_TYPE_INT, 0, '代理销售量', false, 11);
        $this->initVar('daysale', JIEQI_TYPE_INT, 0, '本日销售量', false, 11);
        $this->initVar('weeksale', JIEQI_TYPE_INT, 0, '本周销售量', false, 11);
        $this->initVar('monthsale', JIEQI_TYPE_INT, 0, '本月销售量', false, 11);
        $this->initVar('allsale', JIEQI_TYPE_INT, 0, '总销售量', false, 11);
        $this->initVar('lastsale', JIEQI_TYPE_INT, 0, '最后销售时间', false, 11);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '是否显示', false, 1);
    }
}
class JieqiObookHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'obook';
        $this->autoid = 'obookid';
        $this->dbname = 'obook_obook';
    }
}