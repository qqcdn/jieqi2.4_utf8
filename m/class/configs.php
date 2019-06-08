<?php
jieqi_includedb();
class JieqiConfigs extends JieqiObjectData
{
    public function __construct()
    {
        $this->initVar('cid', JIEQI_TYPE_INT, 0, '序号', false, 8);
        $this->initVar('modname', JIEQI_TYPE_TXTBOX, '', '模块名称', true, 50);
        $this->initVar('cname', JIEQI_TYPE_TXTBOX, '', '配置名称', true, 50);
        $this->initVar('ctitle', JIEQI_TYPE_TXTBOX, '', '配置标题', false, 50);
        $this->initVar('cvalue', JIEQI_TYPE_TXTAREA, '', '配置值', false, NULL);
        $this->initVar('cdescription', JIEQI_TYPE_TXTAREA, '', '配置描述', false, NULL);
        $this->initVar('cdefine', JIEQI_TYPE_INT, 0, '是否定义', false, 1);
        $this->initVar('ctype', JIEQI_TYPE_INT, 0, '变量类型', false, 1);
        $this->initVar('options', JIEQI_TYPE_TXTAREA, '', '可用选项', false, NULL);
    }
}
class JieqiConfigsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'configs';
        $this->autoid = 'cid';
        $this->dbname = 'system_configs';
    }
}
