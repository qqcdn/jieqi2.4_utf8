<?php
jieqi_includedb();
include_once JIEQI_ROOT_PATH . '/class/topics.php';
class JieqiPtopics extends JieqiTopics
{
    public function __construct()
    {
        parent::__construct();
    }
}
class JieqiPtopicsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'ptopics';
        $this->autoid = 'topicid';
        $this->dbname = 'system_ptopics';
    }
}
