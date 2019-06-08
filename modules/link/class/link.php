<?php
jieqi_includedb();
class JieqiLink extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('linkid', JIEQI_TYPE_INT, 0, '序号', false, 5);
        $this->initVar('linktype', JIEQI_TYPE_INT, 0, '链接类型', false, 1);
        $this->initVar('namecolor', JIEQI_TYPE_TXTBOX, 0, '链接文字颜色', false, 10);
        $this->initVar('name', JIEQI_TYPE_TXTBOX, 0, '链接名称', true, 50);
        $this->initVar('url', JIEQI_TYPE_TXTBOX, '', '链接地址', true, 255);
        $this->initVar('logo', JIEQI_TYPE_TXTBOX, '', 'LOGO地址', false, 255);
        $this->initVar('introduce', JIEQI_TYPE_TXTAREA, '', '简介', false, NULL);
        $this->initVar('userid', JIEQI_TYPE_INT, 0, '添加人ID', false, 10);
        $this->initVar('username', JIEQI_TYPE_TXTBOX, '', '添加人', false, 30);
        $this->initVar('mastername', JIEQI_TYPE_TXTBOX, '', '联系人', false, 50);
        $this->initVar('mastertell', JIEQI_TYPE_TXTBOX, '', '联系方式', false, 30);
        $this->initVar('listorder', JIEQI_TYPE_INT, 0, '排列顺序', false, 5);
        $this->initVar('passed', JIEQI_TYPE_INT, 0, '是否显示', false, 1);
        $this->initVar('addtime', JIEQI_TYPE_TXTBOX, '', '添加时间', false, 20);
        $this->initVar('hits', JIEQI_TYPE_INT, 0, '浏览量', false, 10);
    }
}
class JieqiLinkHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'link';
        $this->autoid = 'linkid';
        $this->dbname = 'link_link';
    }
}