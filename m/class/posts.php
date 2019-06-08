<?php

class JieqiPosts extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('postid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('topicid', JIEQI_TYPE_INT, 0, '主题序号', false, 11);
        $this->initVar('istopic', JIEQI_TYPE_INT, 0, '是否主题', false, 1);
        $this->initVar('replypid', JIEQI_TYPE_INT, 0, '回复帖子序号', false, 11);
        $this->initVar('ownerid', JIEQI_TYPE_INT, 0, '论坛序号', false, 11);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表者序号', false, 11);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表者', false, 30);
        $this->initVar('posttime', JIEQI_TYPE_INT, 0, '发表时间', false, 11);
        $this->initVar('posterip', JIEQI_TYPE_TXTBOX, '', '发表者IP', false, 25);
        $this->initVar('editorid', JIEQI_TYPE_INT, 0, '编辑人序号', false, 11);
        $this->initVar('editor', JIEQI_TYPE_TXTBOX, '', '编辑人', false, 30);
        $this->initVar('edittime', JIEQI_TYPE_INT, 0, '编辑时间', false, 11);
        $this->initVar('editorip', JIEQI_TYPE_TXTBOX, '', '编辑人IP', false, 25);
        $this->initVar('editnote', JIEQI_TYPE_TXTBOX, '', '编辑人备注', false, 250);
        $this->initVar('iconid', JIEQI_TYPE_INT, 0, '图标', false, 3);
        $this->initVar('attachment', JIEQI_TYPE_TXTAREA, '', '附件信息', false, NULL);
        $this->initVar('subject', JIEQI_TYPE_TXTBOX, '', '帖子主题', false, 80);
        $this->initVar('posttext', JIEQI_TYPE_TXTAREA, '', '帖子内容', true, NULL);
        $this->initVar('size', JIEQI_TYPE_INT, 0, '帖子大小', false, 10);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '是否显示', false, 1);
    }
}