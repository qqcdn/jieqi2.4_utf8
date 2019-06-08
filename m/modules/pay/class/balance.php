<?php
jieqi_includedb();
class JieqiBalance extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('balid', JIEQI_TYPE_INT, 0, '序号', false);
        $this->initVar('baltime', JIEQI_TYPE_INT, 0, '操作时间', false);
        $this->initVar('fromid', JIEQI_TYPE_INT, 0, '管理员序号', false);
        $this->initVar('fromname', JIEQI_TYPE_TXTBOX, '', '管理员', false, 30);
        $this->initVar('toid', JIEQI_TYPE_INT, 0, '结算人序号', false);
        $this->initVar('toname', JIEQI_TYPE_TXTBOX, '', '结算人', false, 30);
        $this->initVar('baltype', JIEQI_TYPE_TXTAREA, '', '结算方式', false);
        $this->initVar('ballog', JIEQI_TYPE_TXTAREA, '', '结算说明', false);
        $this->initVar('balegold', JIEQI_TYPE_INT, 0, '结算虚拟货币', false);
        $this->initVar('moneytype', JIEQI_TYPE_INT, 0, '货币类型', false);
        $this->initVar('balmoney', JIEQI_TYPE_INT, 0, '结算金额', false);
        $this->initVar('balflag', JIEQI_TYPE_INT, 0, '结算标志', false);
    }
}
class JieqiBalanceHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'balance';
        $this->autoid = 'balid';
        $this->dbname = 'pay_balance';
    }
}
