<?php
jieqi_includedb();
class JieqiRegisterip extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ip', JIEQI_TYPE_TXTBOX, 0, '注册人IP', false, 15);
        $this->initVar('regtime', JIEQI_TYPE_INT, 0, '注册时间', false, 11);
        $this->initVar('count', JIEQI_TYPE_INT, 0, '计数', false, 6);
    }
}
class JieqiRegisteripHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'registerip';
        $this->autoid = '';
        $this->dbname = 'system_registerip';
    }
}
