<?php
jieqi_includedb();
class JieqiModules extends JieqiObjectData
{
    public function __construct()
    {
        $this->initVar('mid', JIEQI_TYPE_INT, 0, '序号', false, 5);
        $this->initVar('name', JIEQI_TYPE_TXTBOX, '', '模块名称', true, 50);
        $this->initVar('caption', JIEQI_TYPE_TXTBOX, '', '模块标题', false, 50);
        $this->initVar('description', JIEQI_TYPE_TXTAREA, '', '模块描述', false, NULL);
        $this->initVar('version', JIEQI_TYPE_INT, 0, '版本', false, 3);
        $this->initVar('vtype', JIEQI_TYPE_TXTBOX, '', '版本类型', false, 30);
        $this->initVar('lastupdate', JIEQI_TYPE_INT, 0, '最后更新', false, 10);
        $this->initVar('weight', JIEQI_TYPE_INT, 0, '排列顺序', false, 8);
        $this->initVar('publich', JIEQI_TYPE_INT, 0, '是否激活', false, 1);
        $this->initVar('modtype', JIEQI_TYPE_INT, 0, '模块类型', false, 1);
    }
}
class JieqiModulesHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'modules';
        $this->autoid = 'mid';
        $this->dbname = 'system_modules';
    }
}
