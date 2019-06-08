<?php
jieqi_includedb();
class JieqiOsale extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('osaleid', JIEQI_TYPE_INT, 0, '订单序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('buytime', JIEQI_TYPE_INT, 0, '购买日期', false, 11);
        $this->initVar('accountid', JIEQI_TYPE_INT, 0, '帐号id', false, 11);
        $this->initVar('account', JIEQI_TYPE_TXTBOX, '', '帐号名称', false, 30);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', false, 11);
        $this->initVar('obookid', JIEQI_TYPE_INT, 0, '电子书序号', false, 11);
        $this->initVar('ochapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('obookname', JIEQI_TYPE_TXTBOX, '', '电子书名', true, 100);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节名', true, 100);
        $this->initVar('saleprice', JIEQI_TYPE_INT, 0, '销售价格', false, 11);
        $this->initVar('salenum', JIEQI_TYPE_INT, 0, '销售数量', false, 11);
        $this->initVar('sumprice', JIEQI_TYPE_INT, 0, '销售总价', false, 11);
        $this->initVar('pricetype', JIEQI_TYPE_INT, 0, '价格类型', false, 1);
        $this->initVar('paytype', JIEQI_TYPE_INT, 0, '支付方式', false, 1);
        $this->initVar('payflag', JIEQI_TYPE_INT, 0, '支付标志', false, 1);
        $this->initVar('paynote', JIEQI_TYPE_TXTAREA, '', '备注', false, NULL);
        $this->initVar('state', JIEQI_TYPE_INT, 0, '状态', false, 1);
        $this->initVar('flag', JIEQI_TYPE_INT, 0, '标志', false, 1);
    }
}
class JieqiOsaleHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'osale';
        $this->autoid = 'osaleid';
        $this->dbname = 'obook_osale';
    }
}