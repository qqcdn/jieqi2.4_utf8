<?php
jieqi_includedb();
class JieqiObuyinfo extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('obuyinfoid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('osaleid', JIEQI_TYPE_INT, 0, '订单序号', false, 11);
        $this->initVar('buytime', JIEQI_TYPE_INT, 0, '购买日期', false, 11);
        $this->initVar('userid', JIEQI_TYPE_INT, 0, '用户序号', false, 11);
        $this->initVar('username', JIEQI_TYPE_TXTBOX, '', '用户名称', false, 30);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', false, 11);
        $this->initVar('obookid', JIEQI_TYPE_INT, 0, '电子书序号', false, 11);
        $this->initVar('ochapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('obookname', JIEQI_TYPE_TXTBOX, '', '电子书名', true, 100);
        $this->initVar('chaptername', JIEQI_TYPE_TXTBOX, '', '章节名', true, 100);
        $this->initVar('lastread', JIEQI_TYPE_INT, 0, '最后阅读', false, 11);
        $this->initVar('readnum', JIEQI_TYPE_INT, 0, '阅读次数', false, 11);
        $this->initVar('checkcode', JIEQI_TYPE_TXTBOX, '', '校验码', false, 10);
        $this->initVar('buyprice', JIEQI_TYPE_INT, 0, '单价', false, 11);
        $this->initVar('buynum', JIEQI_TYPE_INT, 0, '购买数量', false, 11);
        $this->initVar('buypay', JIEQI_TYPE_INT, 0, '总价', false, 11);
        $this->initVar('state', JIEQI_TYPE_INT, 0, '状态', false, 1);
        $this->initVar('flag', JIEQI_TYPE_INT, 0, '标志', false, 1);
    }
}
class JieqiObuyinfoHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'obuyinfo';
        $this->autoid = 'obuyinfoid';
        $this->dbname = 'obook_obuyinfo';
    }
}
