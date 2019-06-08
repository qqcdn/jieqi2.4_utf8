<?php
jieqi_includedb();
class JieqiPaycard extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('cardid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('batchno', JIEQI_TYPE_TXTBOX, '', '批号', false, 30);
        $this->initVar('cardno', JIEQI_TYPE_TXTBOX, '', '卡号', true, 30);
        $this->initVar('cardpass', JIEQI_TYPE_TXTBOX, '', '密码', false, 30);
        $this->initVar('cardtype', JIEQI_TYPE_INT, 0, '卡类型', false, 1);
        $this->initVar('payemoney', JIEQI_TYPE_INT, 0, '充值虚拟货币量', false, 11);
        $this->initVar('emoneytype', JIEQI_TYPE_INT, 0, '虚拟货币类型', false, 1);
        $this->initVar('ispay', JIEQI_TYPE_INT, 0, '是否已使用', false, 1);
        $this->initVar('paytime', JIEQI_TYPE_INT, 0, '使用时间', false, 11);
        $this->initVar('payuid', JIEQI_TYPE_INT, 0, '使用人ID', false, 11);
        $this->initVar('payname', JIEQI_TYPE_TXTBOX, 0, '使用人名称', false, 30);
        $this->initVar('note', JIEQI_TYPE_TXTBOX, '', '备注', false, 255);
        $this->initVar('flag', JIEQI_TYPE_INT, 0, '标志', false, 1);
    }
}
class JieqiPaycardHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'paycard';
        $this->autoid = 'cardid';
        $this->dbname = 'pay_paycard';
    }
}
