<?php
jieqi_includedb();
class JieqiTransfer extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('transid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('transtime', JIEQI_TYPE_INT, 0, '操作时间', false, 11);
        $this->initVar('fromid', JIEQI_TYPE_INT, 0, '转出人序号', false, 11);
        $this->initVar('fromname', JIEQI_TYPE_TXTBOX, '', '转出人', false, 30);
        $this->initVar('toid', JIEQI_TYPE_INT, 0, '转入人序号', false, 11);
        $this->initVar('toname', JIEQI_TYPE_TXTBOX, '', '转入人', false, 30);
        $this->initVar('translog', JIEQI_TYPE_TXTAREA, '', '转帐说明', false, NULL);
        $this->initVar('transegold', JIEQI_TYPE_INT, 0, '转出金额', false, 11);
        $this->initVar('receiveegold', JIEQI_TYPE_INT, 0, '收到金额', false, 11);
        $this->initVar('mastertime', JIEQI_TYPE_INT, 0, '管理时间', false, 11);
        $this->initVar('masterid', JIEQI_TYPE_INT, 0, '管理员序号', false, 11);
        $this->initVar('mastername', JIEQI_TYPE_TXTBOX, '', '管理员', false, 30);
        $this->initVar('masterlog', JIEQI_TYPE_TXTAREA, '', '管理说明', false, NULL);
        $this->initVar('transtype', JIEQI_TYPE_INT, 0, '转帐方式', false, 1);
        $this->initVar('errflag', JIEQI_TYPE_INT, 0, '错误标志', false, 1);
        $this->initVar('transflag', JIEQI_TYPE_INT, 0, '转帐状态', false, 1);
    }
}
class JieqiTransferHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'transfer';
        $this->autoid = 'transid';
        $this->dbname = 'pay_transfer';
    }
}
