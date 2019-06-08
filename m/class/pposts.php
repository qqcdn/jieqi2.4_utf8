<?php
jieqi_includedb();
include_once JIEQI_ROOT_PATH . '/class/posts.php';
class JieqiPposts extends JieqiPosts
{
    public function __construct()
    {
        parent::__construct();
    }
}
class JieqiPpostsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'pposts';
        $this->autoid = 'postid';
        $this->dbname = 'system_pposts';
    }
}
