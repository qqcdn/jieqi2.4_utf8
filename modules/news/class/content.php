<?php
jieqi_includedb();
class JieqiNewscontent extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('topicid', JIEQI_TYPE_INT, 0, '新闻ID', false, 11);
        $this->initVar('contents', JIEQI_TYPE_TXTAREA, '', '新闻内容', true, NULL);
    }
}
class JieqiNewscontentHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'newscontent';
        $this->autoid = 'topicid';
        $this->dbname = 'news_content';
    }
    public function JieqiNewscontentExsit($id = NULL)
    {
        $sql = 'SELECT newsid FROM ' . jieqi_dbprefix($this->dbname) . ' WHERE newsid=' . $id;
        if ($result = $this->execute($sql)) {
            return $this->db->getRowsNum($result);
        }
    }
    public function JieqiNewscontentByID($id = NULL)
    {
        $sql = 'SELECT newscontent FROM ' . jieqi_dbprefix($this->dbname) . ' WHERE newsid=' . $id;
        if ($result = $this->execute($sql)) {
            if ($rs = $this->getRow($result)) {
                return $rs['newscontent'];
            }
        }
        return false;
    }
    public function JieqiNewscontentUpdate($id = NULL, $value = NULL)
    {
        $sql = 'UPDATE ' . jieqi_dbprefix($this->dbname) . ' SET newscontent=\'' . jieqi_dbslashes($value) . '\' WHERE newsid=' . $id;
        return $this->execute($sql) ? true : false;
    }
    public function JieqiNewscontentDelete($id = NULL)
    {
        $sql = 'DELETE FROM ' . jieqi_dbprefix($this->dbname) . ' WHERE newsid=' . $id;
        return $this->execute($sql) ? true : false;
    }
}