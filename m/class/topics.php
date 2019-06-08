<?php

class JieqiTopics extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('topicid', JIEQI_TYPE_INT, 0, '序号', false, 8);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('ownerid', JIEQI_TYPE_INT, 0, '所属序号', false, 10);
        $this->initVar('ownername', JIEQI_TYPE_TXTBOX, '', '所属名称', false, 100);
        $this->initVar('ownercode', JIEQI_TYPE_TXTBOX, '', '所属代码', false, 200);
        $this->initVar('targetid', JIEQI_TYPE_INT, 0, '目标序号', false, 10);
        $this->initVar('targetname', JIEQI_TYPE_TXTBOX, '', '目标名称', false, 100);
        $this->initVar('targetflag', JIEQI_TYPE_INT, 0, '目标标志', false, 10);
        $this->initVar('title', JIEQI_TYPE_TXTBOX, '', '标题', true, 100);
        $this->initVar('content', JIEQI_TYPE_TXTAREA, '', '内容', true, NULL);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表人序号', false, 10);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表人', false, 30);
        $this->initVar('posttime', JIEQI_TYPE_INT, 0, '发表时间', false, 10);
        $this->initVar('replierid', JIEQI_TYPE_INT, 0, '回复人序号', false, 10);
        $this->initVar('replier', JIEQI_TYPE_TXTBOX, '', '回复人', false, 30);
        $this->initVar('replytime', JIEQI_TYPE_INT, 0, '回复时间', false, 10);
        $this->initVar('views', JIEQI_TYPE_INT, 0, '点击数', false, 8);
        $this->initVar('replies', JIEQI_TYPE_INT, 0, '回复数', false, 8);
        $this->initVar('islock', JIEQI_TYPE_INT, 0, '是否锁定', false, 1);
        $this->initVar('istop', JIEQI_TYPE_INT, 0, '是否置顶', false, 1);
        $this->initVar('isgood', JIEQI_TYPE_INT, 0, '是否精华', false, 1);
        $this->initVar('rate', JIEQI_TYPE_INT, 0, '帖子等级', false, 1);
        $this->initVar('attachment', JIEQI_TYPE_INT, 0, '是否有附件', false, 1);
        $this->initVar('needperm', JIEQI_TYPE_INT, 0, '访问需要权限', false, 10);
        $this->initVar('needscore', JIEQI_TYPE_INT, 0, '访问需要积分', false, 10);
        $this->initVar('needexp', JIEQI_TYPE_INT, 0, '访问需要经验值', false, 10);
        $this->initVar('needprice', JIEQI_TYPE_INT, 0, '访问需要价格', false, 10);
        $this->initVar('sortid', JIEQI_TYPE_INT, 0, '分类ID', false, 3);
        $this->initVar('iconid', JIEQI_TYPE_INT, 0, '图标ID', false, 3);
        $this->initVar('typeid', JIEQI_TYPE_INT, 0, '类型ID', false, 3);
        $this->initVar('lastinfo', JIEQI_TYPE_TXTBOX, '', '最后更新', false, 250);
        $this->initVar('replyinfo', JIEQI_TYPE_TXTAREA, '', '回复信息', false, NULL);
        $this->initVar('linkurl', JIEQI_TYPE_TXTBOX, '', '链接URL', false, 100);
        $this->initVar('size', JIEQI_TYPE_INT, 0, '帖子大小', false, 11);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '是否显示', false, 1);
    }
}