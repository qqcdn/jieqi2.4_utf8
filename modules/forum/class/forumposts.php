<?php
jieqi_includedb();
include_once JIEQI_ROOT_PATH . '/class/posts.php';
class JieqiForumposts extends JieqiPosts
{
    public function __construct()
    {
        parent::__construct();
    }
}
class JieqiForumpostsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'forumposts';
        $this->autoid = 'postid';
        $this->dbname = 'forum_forumposts';
    }
}