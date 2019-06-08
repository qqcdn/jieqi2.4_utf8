<?php
jieqi_includedb();
class JieqiPaylog extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('payid', JIEQI_TYPE_INT, 0, '序号', false);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('buytime', JIEQI_TYPE_INT, 0, '购买时间', false);
        $this->initVar('buydate', JIEQI_TYPE_INT, 0, '购买日期', false);
        $this->initVar('buymonth', JIEQI_TYPE_INT, 0, '购买月份', false);
        $this->initVar('rettime', JIEQI_TYPE_INT, 0, '返回时间', false);
        $this->initVar('buyid', JIEQI_TYPE_INT, 0, '购买者序号', false);
        $this->initVar('buyname', JIEQI_TYPE_TXTBOX, '', '购买人', false, 30);
        $this->initVar('buyinfo', JIEQI_TYPE_TXTAREA, '', '购买人信息', false);
        $this->initVar('moneytype', JIEQI_TYPE_INT, 0, '金额类型', false);
        $this->initVar('money', JIEQI_TYPE_INT, 0, '金额', false);
        $this->initVar('egoldtype', JIEQI_TYPE_INT, 0, '虚拟货币类型', false);
        $this->initVar('egold', JIEQI_TYPE_INT, 0, '虚拟货币', false);
        $this->initVar('paytype', JIEQI_TYPE_TXTBOX, '', '支付类型', false);
        $this->initVar('subtype', JIEQI_TYPE_TXTBOX, '', '支付子类型', false);
        $this->initVar('fromtype', JIEQI_TYPE_TXTBOX, '', '来源类型', false);
        $this->initVar('typename', JIEQI_TYPE_TXTBOX, '', '类型名称', false);
        $this->initVar('acttype', JIEQI_TYPE_INT, 0, '充值动作类型', false);
        $this->initVar('actid', JIEQI_TYPE_INT, 0, '充值动作ID', false);
        $this->initVar('actname', JIEQI_TYPE_TXTBOX, '', '充值动作名称', false, 30);
        $this->initVar('actlog', JIEQI_TYPE_TXTAREA, '', '充值动作日志', false);
        $this->initVar('channel', JIEQI_TYPE_TXTBOX, '', '会员来源渠道', false, 30);
        $this->initVar('device', JIEQI_TYPE_TXTBOX, '', '会员使用设备', false, 30);
        $this->initVar('retserialno', JIEQI_TYPE_TXTAREA, '', '返回流水号', false);
        $this->initVar('retaccount', JIEQI_TYPE_TXTAREA, '', '返回账号', false);
        $this->initVar('retinfo', JIEQI_TYPE_TXTAREA, '', '返回信息', false);
        $this->initVar('masterid', JIEQI_TYPE_INT, 0, '管理员序号', false);
        $this->initVar('mastername', JIEQI_TYPE_TXTBOX, '', '管理员', false, 30);
        $this->initVar('masterinfo', JIEQI_TYPE_TXTAREA, '', '管理员信息', false);
        $this->initVar('note', JIEQI_TYPE_TXTAREA, '', '备注', false);
        $this->initVar('payflag', JIEQI_TYPE_INT, 0, '支付标志', false);
    }
}
class JieqiPaylogHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'paylog';
        $this->autoid = 'payid';
        $this->dbname = 'pay_paylog';
    }
}
