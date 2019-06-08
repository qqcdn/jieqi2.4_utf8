<?php

class JieqiAttachs extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('attachid', JIEQI_TYPE_INT, 0, '附件序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 11);
        $this->initVar('topicid', JIEQI_TYPE_INT, 0, '主题序号', false, 11);
        $this->initVar('postid', JIEQI_TYPE_INT, 0, '帖子序号', false, 11);
        $this->initVar('name', JIEQI_TYPE_TXTBOX, '', '附件名称', true, 100);
        $this->initVar('description', JIEQI_TYPE_TXTBOX, '', '附件描述', true, 100);
        $this->initVar('class', JIEQI_TYPE_TXTBOX, '', '附件类型', true, 30);
        $this->initVar('postfix', JIEQI_TYPE_TXTBOX, '', '附件后缀', true, 30);
        $this->initVar('size', JIEQI_TYPE_INT, 0, '文件大小', false, 10);
        $this->initVar('hits', JIEQI_TYPE_INT, 0, '点击数', false, 8);
        $this->initVar('needperm', JIEQI_TYPE_INT, 0, '需要权限', false, 10);
        $this->initVar('needscore', JIEQI_TYPE_INT, 0, '需要积分', false, 10);
        $this->initVar('needexp', JIEQI_TYPE_INT, 0, '需要经验值', false, 10);
        $this->initVar('needprice', JIEQI_TYPE_INT, 0, '需要价格', false, 10);
        $this->initVar('uptime', JIEQI_TYPE_INT, 0, '上传时间', false, 10);
        $this->initVar('uid', JIEQI_TYPE_INT, 0, '发表用户ID', false, 10);
        $this->initVar('remote', JIEQI_TYPE_INT, 0, '是否远程附件', false, 1);
    }
}