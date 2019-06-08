<?php
jieqi_includedb();
class JieqiArticleattachs extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('attachid', JIEQI_TYPE_INT, 0, '附件序号', false, 11);
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '小说序号', false, 11);
        $this->initVar('chapterid', JIEQI_TYPE_INT, 0, '章节序号', false, 11);
        $this->initVar('name', JIEQI_TYPE_TXTBOX, '', '附件名称', true, 80);
        $this->initVar('class', JIEQI_TYPE_TXTBOX, '', '附件类型', true, 30);
        $this->initVar('postfix', JIEQI_TYPE_TXTBOX, '', '附件后缀', true, 30);
        $this->initVar('size', JIEQI_TYPE_INT, 0, '文件大小', false, 11);
        $this->initVar('hits', JIEQI_TYPE_INT, 0, '点击数', false, 11);
        $this->initVar('needexp', JIEQI_TYPE_INT, 0, '需要经验值', false, 11);
        $this->initVar('uptime', JIEQI_TYPE_INT, 0, '上传时间', false, 11);
    }
}
class JieqiArticleattachsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'articleattachs';
        $this->autoid = 'attachid';
        $this->dbname = 'article_attachs';
    }
}
