<?php
jieqi_includedb();
class JieqiApplywriter extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('applyid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('applytime', JIEQI_TYPE_INT, 0, '申请时间', false, 11);
        $this->initVar('applyuid', JIEQI_TYPE_INT, 0, '申请人序号', true, 11);
        $this->initVar('applyname', JIEQI_TYPE_TXTBOX, '', '申请人名字', false, 30);
        $this->initVar('penname', JIEQI_TYPE_TXTBOX, '', '申请昵称', false, 30);
        $this->initVar('authtime', JIEQI_TYPE_INT, 0, '审核时间', false, 11);
        $this->initVar('authuid', JIEQI_TYPE_INT, 0, '审核人序号', false, 11);
        $this->initVar('authname', JIEQI_TYPE_TXTBOX, '', '审核人名字', false, 30);
        $this->initVar('applytitle', JIEQI_TYPE_TXTBOX, '', '申请标题', false, 250);
        $this->initVar('applytext', JIEQI_TYPE_TXTAREA, '', '申请内容', true, NULL);
        $this->initVar('applywords', JIEQI_TYPE_INT, 0, '申请样章字数', false, 11);
        $this->initVar('authnote', JIEQI_TYPE_TXTAREA, '', '斑竹备注', true, NULL);
        $this->initVar('applyflag', JIEQI_TYPE_INT, 0, '审核标志', false, 1);
    }
}
class JieqiApplywriterHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'applywriter';
        $this->autoid = 'applyid';
        $this->dbname = 'article_applywriter';
    }
}
