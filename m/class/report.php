<?php
jieqi_includedb();
class JieqiReport extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('reportid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('reporttime', JIEQI_TYPE_INT, 0, '报告时间', false, 11);
        $this->initVar('reportuid', JIEQI_TYPE_INT, 0, '报告人序号', true, 11);
        $this->initVar('reportname', JIEQI_TYPE_TXTBOX, '', '报告人名字', false, 30);
        $this->initVar('authtime', JIEQI_TYPE_INT, 0, '处理时间', false, 11);
        $this->initVar('authuid', JIEQI_TYPE_INT, 0, '处理人序号', false, 11);
        $this->initVar('authname', JIEQI_TYPE_TXTBOX, '', '处理人名字', false, 30);
        $this->initVar('reporttitle', JIEQI_TYPE_TXTBOX, '', '报告标题', false, 250);
        $this->initVar('reporttext', JIEQI_TYPE_TXTAREA, '', '报告内容', true, NULL);
        $this->initVar('reportsize', JIEQI_TYPE_INT, 0, '报告字数', false, 11);
        $this->initVar('reportfield', JIEQI_TYPE_TXTBOX, '', '报告提交字符串', false, 250);
        $this->initVar('authnote', JIEQI_TYPE_TXTAREA, '', '斑竹备注', true, NULL);
        $this->initVar('reportsort', JIEQI_TYPE_INT, 0, '报告主分类', false, 6);
        $this->initVar('reporttype', JIEQI_TYPE_INT, 0, '报告子分类', false, 6);
        $this->initVar('authflag', JIEQI_TYPE_INT, 0, '审核标志', false, 3);
    }
}
class JieqiReportHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'report';
        $this->autoid = 'reportid';
        $this->dbname = 'system_report';
    }
}
