<?php

jieqi_includedb();
include_once JIEQI_ROOT_PATH . '/class/attachs.php';
class JieqiForumattachs extends JieqiAttachs
{
    public function __construct()
    {
        parent::__construct();
    }
}
class JieqiForumattachsHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'forumattachs';
        $this->autoid = 'attachid';
        $this->dbname = 'forum_attachs';
    }
}
