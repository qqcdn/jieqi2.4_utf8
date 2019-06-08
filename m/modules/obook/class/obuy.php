<?php
jieqi_includedb();
class JieqiObuy extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('obuyid', JIEQI_TYPE_INT, 0, '订单序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('osaleid', JIEQI_TYPE_INT, 0, '订单序号', false, 11);
        $this->initVar('buytime', JIEQI_TYPE_INT, 0, '开始购买时间', false, 11);
        $this->initVar('lastbuy', JIEQI_TYPE_INT, 0, '最后购买时间', false, 11);
        $this->initVar('lastread', JIEQI_TYPE_INT, 0, '最后阅读时间', false, 11);
        $this->initVar('readnum', JIEQI_TYPE_INT, 0, '阅读次数', false, 11);
        $this->initVar('userid', JIEQI_TYPE_INT, 0, '帐号id', false, 11);
        $this->initVar('username', JIEQI_TYPE_TXTBOX, '', '帐号名称', false, 30);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', false, 11);
        $this->initVar('obookid', JIEQI_TYPE_INT, 0, '电子书序号', false, 11);
        $this->initVar('ochapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('obookname', JIEQI_TYPE_TXTBOX, '', '电子书名', true, 100);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节名', true, 100);
        $this->initVar('chapternum', JIEQI_TYPE_INT, 0, '订阅章节数', false, 11);
        $this->initVar('buynum', JIEQI_TYPE_INT, 0, '总购买数量', false, 11);
        $this->initVar('buypay', JIEQI_TYPE_INT, 0, '总消费额', false, 11);
        $this->initVar('isread', JIEQI_TYPE_INT, 0, '是否已经全部阅读', false, 1);
        $this->initVar('isfull', JIEQI_TYPE_INT, 0, '是否已全本购买', false, 1);
        $this->initVar('autobuy', JIEQI_TYPE_INT, 0, '是否自动购买', false, 1);
        $this->initVar('buymode', JIEQI_TYPE_INT, 0, '购买方式', false, 1);
        $this->initVar('starlevel', JIEQI_TYPE_INT, 0, '星级标识', false, 1);
        $this->initVar('oflag', JIEQI_TYPE_INT, 0, '标志', false, 1);
    }
}
class JieqiObuyHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'obuy';
        $this->autoid = 'obuyid';
        $this->dbname = 'obook_obuy';
    }
}
