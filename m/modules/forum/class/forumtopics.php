<?php
jieqi_includedb();
include_once JIEQI_ROOT_PATH . '/class/topics.php';
class JieqiForumtopics extends JieqiTopics
{
    public function __construct()
    {
        parent::__construct();
    }
}
class JieqiForumtopicsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'forumtopics';
        $this->autoid = 'topicid';
        $this->dbname = 'forum_forumtopics';
    }
}