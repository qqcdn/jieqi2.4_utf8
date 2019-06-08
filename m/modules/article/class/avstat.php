<?php
jieqi_includedb();
class JieqiAvstat extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('statid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('voteid', JIEQI_TYPE_INT, 0, '投票序号', false, 11);
        $this->initVar('statall', JIEQI_TYPE_INT, 0, '总票数', false, 11);
        $this->initVar('stat1', JIEQI_TYPE_INT, 0, '得票1', false, 11);
        $this->initVar('stat2', JIEQI_TYPE_INT, 0, '得票2', false, 11);
        $this->initVar('stat3', JIEQI_TYPE_INT, 0, '得票3', false, 11);
        $this->initVar('stat4', JIEQI_TYPE_INT, 0, '得票4', false, 11);
        $this->initVar('stat5', JIEQI_TYPE_INT, 0, '得票5', false, 11);
        $this->initVar('stat6', JIEQI_TYPE_INT, 0, '得票6', false, 11);
        $this->initVar('stat7', JIEQI_TYPE_INT, 0, '得票7', false, 11);
        $this->initVar('stat8', JIEQI_TYPE_INT, 0, '得票8', false, 11);
        $this->initVar('stat9', JIEQI_TYPE_INT, 0, '得票9', false, 11);
        $this->initVar('stat10', JIEQI_TYPE_INT, 0, '得票10', false, 11);
        $this->initVar('canstat', JIEQI_TYPE_INT, 0, '是否统计', false, 1);
    }
}
class JieqiAvstatHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'avstat';
        $this->autoid = 'voteid';
        $this->dbname = 'article_avstat';
    }
}