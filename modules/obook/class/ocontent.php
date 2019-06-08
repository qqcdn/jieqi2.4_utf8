<?php
jieqi_includedb();
class JieqiOcontent extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ochapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('ocontent', JIEQI_TYPE_TXTAREA, '', '章节内容', true, NULL);
    }
}
class JieqiOcontentHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'ocontent';
        $this->autoid = 'ochapterid';
        $this->dbname = 'obook_ocontent';
    }
}