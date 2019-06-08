<?php
jieqi_includedb();
class JieqiNewsattach extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('attachid', JIEQI_TYPE_INT, 0, '附件序号', false, 11);
        $this->initVar('ownerid', JIEQI_TYPE_INT, 0, '所有者序号', false, 11);
        $this->initVar('addtime', JIEQI_TYPE_INT, 0, '发布时间', false, 11);
        $this->initVar('uptime', JIEQI_TYPE_INT, 0, '更新时间', false, 11);
        $this->initVar('userid', JIEQI_TYPE_INT, 0, '发表者ID', false, 11);
        $this->initVar('username', JIEQI_TYPE_TXTBOX, '', '发表者', false, 30);
        $this->initVar('attachname', JIEQI_TYPE_TXTBOX, '', '附件名称', true, 50);
        $this->initVar('description', JIEQI_TYPE_TXTBOX, '', '附件描述', false, 100);
        $this->initVar('attachtype', JIEQI_TYPE_TXTBOX, '', '附件类型', false, 30);
        $this->initVar('attachflag', JIEQI_TYPE_INT, 0, '附件标志', false, 1);
        $this->initVar('attachpath', JIEQI_TYPE_TXTBOX, '', '附件路径', false, 100);
        $this->initVar('attachsize', JIEQI_TYPE_INT, 0, '附件大小', false, 11);
        $this->initVar('downloads', JIEQI_TYPE_INT, 0, '下载次数', false, 11);
    }
}
class JieqiNewsattachHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'newsattach';
        $this->autoid = 'attachid';
        $this->dbname = 'news_attachment';
    }
    public function JieqiNewsattachPath($attachid = NULL)
    {
        $sql = 'SELECT attachpath FROM ' . jieqi_dbprefix($this->dbname) . ' WHERE attachid=' . intval($attachid);
        if ($result = $this->execute($sql)) {
            if ($rs = $this->getRow($result)) {
                return $rs['attachpath'];
            }
        } else {
            return false;
        }
    }
}