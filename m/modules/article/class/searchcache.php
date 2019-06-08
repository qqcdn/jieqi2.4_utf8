<?php
jieqi_includedb();
class JieqiSearchcache extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('cacheid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('addtime', JIEQI_TYPE_INT, 0, '最早搜索时间', false, 11);
        $this->initVar('lasttime', JIEQI_TYPE_INT, 0, '最后搜索时间', false, 11);
        $this->initVar('uptime', JIEQI_TYPE_INT, 0, '内容更新时间', false, 11);
        $this->initVar('searchnum', JIEQI_TYPE_INT, 0, '搜索次数', false, 11);
        $this->initVar('hashid', JIEQI_TYPE_TXTBOX, '', '搜索序号', false, 32);
        $this->initVar('keywords', JIEQI_TYPE_TXTBOX, '', '搜索关键字', false, 60);
        $this->initVar('searchtype', JIEQI_TYPE_INT, 0, '搜索方式', false, 1);
        $this->initVar('results', JIEQI_TYPE_INT, 0, '搜索结果数', false, 11);
        $this->initVar('aids', JIEQI_TYPE_TXTAREA, '', '搜索结果小说id', false, NULL);
    }
}
class JieqiSearchcacheHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'searchcache';
        $this->autoid = 'cacheid';
        $this->dbname = 'article_searchcache';
    }
}