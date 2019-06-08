<?php
jieqi_includedb();
class JieqiNewstopic extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('topicid', JIEQI_TYPE_INT, 0, '新闻ID', false, 11);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表者ID', false, 11);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表者', false, 30);
        $this->initVar('posterip', JIEQI_TYPE_TXTBOX, '', '发表者IP', false, 30);
        $this->initVar('masterid', JIEQI_TYPE_INT, 0, '管理者ID', false, 11);
        $this->initVar('master', JIEQI_TYPE_TXTBOX, '', '管理者', false, 30);
        $this->initVar('masterip', JIEQI_TYPE_TXTBOX, '', '管理者IP', false, 30);
        $this->initVar('addtime', JIEQI_TYPE_INT, 0, '发表时间', false, 11);
        $this->initVar('uptime', JIEQI_TYPE_INT, 0, '更新时间', false, 11);
        $this->initVar('sortid', JIEQI_TYPE_INT, 0, '类别ID', false, 11);
        $this->initVar('areaid', JIEQI_TYPE_INT, 0, '地域ID', false, 11);
        $this->initVar('title', JIEQI_TYPE_TXTBOX, '', '标题', true, 80);
        $this->initVar('subhead', JIEQI_TYPE_TXTBOX, '', '副标题', false, 80);
        $this->initVar('tags', JIEQI_TYPE_TXTBOX, '', '标签', false, 50);
        $this->initVar('author', JIEQI_TYPE_TXTBOX, '', '作者', false, 30);
        $this->initVar('aurl', JIEQI_TYPE_TXTBOX, '', '作者url', false, 100);
        $this->initVar('source', JIEQI_TYPE_TXTBOX, '', '来源', false, 100);
        $this->initVar('surl', JIEQI_TYPE_TXTBOX, '', '来源url', false, 100);
        $this->initVar('gourl', JIEQI_TYPE_TXTBOX, '', '跳转到url', false, 100);
        $this->initVar('summary', JIEQI_TYPE_TXTAREA, '', '摘要', false, NULL);
        $this->initVar('style', JIEQI_TYPE_INT, 0, '显示类型', false, 1);
        $this->initVar('cover', JIEQI_TYPE_INT, 0, '缩略图标志', false, 1);
        $this->initVar('attach', JIEQI_TYPE_INT, 0, '附件标志', false, 1);
        $this->initVar('review', JIEQI_TYPE_INT, 0, '是否允许评论', false, 1);
        $this->initVar('vote', JIEQI_TYPE_INT, 0, '是否发起投票', false, 1);
        $this->initVar('login', JIEQI_TYPE_INT, 0, '是否登录阅读', false, 1);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '是否显示', false, 1);
        $this->initVar('views', JIEQI_TYPE_INT, 0, '浏览次数', false, 11);
        $this->initVar('marknum', JIEQI_TYPE_INT, 0, '收藏数', false, 11);
        $this->initVar('topnum', JIEQI_TYPE_INT, 0, '我顶次数', false, 11);
        $this->initVar('downnum', JIEQI_TYPE_INT, 0, '我踩次数', false, 11);
        $this->initVar('scorenum', JIEQI_TYPE_INT, 0, '评分次数', false, 11);
        $this->initVar('sumscore', JIEQI_TYPE_INT, 0, '总分数', false, 11);
        $this->initVar('reviews', JIEQI_TYPE_INT, 0, '评论主题数', false, 11);
        $this->initVar('replies', JIEQI_TYPE_INT, 0, '评论帖子数', false, 11);
    }
}
class JieqiNewstopicHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'newstopic';
        $this->autoid = 'topicid';
        $this->dbname = 'news_topic';
    }
    public function JieqiNewsIDBySecondID($secondid = NULL)
    {
        $newsid_array = array();
        $sql = 'SELECT newsid FROM ' . jieqi_dbprefix($this->dbname) . ' WHERE secondid = ' . intval($secondid);
        if ($result = $this->execute($sql)) {
            while ($rs = $this->getRow($result)) {
                $newsid_array[] = $rs['newsid'];
            }
            return $newsid_array;
        }
    }
    public function JieqiFirstIDByNewsID($newsid = NULL)
    {
        $sql = 'SELECT firstid FROM ' . jieqi_dbprefix($this->dbname) . ' WHERE newsid = ' . intval($newsid);
        if ($result = $this->execute($sql)) {
            if ($rs = $this->getRow($result)) {
                return $rs['firstid'];
            }
        }
    }
}