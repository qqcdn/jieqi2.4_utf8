<?php
jieqi_includedb();
class JieqiMessage extends JieqiObjectData
{
    public function __construct()
    {
        $this->initVar('messageid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 6);
        $this->initVar('postdate', JIEQI_TYPE_INT, 0, '发送日期', false, 11);
        $this->initVar('fromid', JIEQI_TYPE_INT, 0, '发送人序号', false, 11);
        $this->initVar('fromname', JIEQI_TYPE_TXTBOX, '', '发送人名', false, 30);
        $this->initVar('toid', JIEQI_TYPE_INT, 0, '接收人序号', false, 11);
        $this->initVar('toname', JIEQI_TYPE_TXTBOX, '', '接收人名', false, 30);
        $this->initVar('title', JIEQI_TYPE_TXTBOX, '', '消息标题', true, 100);
        $this->initVar('content', JIEQI_TYPE_TXTAREA, '', '消息内容', false, NULL);
        $this->initVar('messagetype', JIEQI_TYPE_INT, 0, '消息类型', false, 1);
        $this->initVar('isread', JIEQI_TYPE_INT, 0, '是否已读', false, 1);
        $this->initVar('fromdel', JIEQI_TYPE_INT, 0, '发送人删除', false, 1);
        $this->initVar('todel', JIEQI_TYPE_INT, 0, '接收人删除', false, 1);
        $this->initVar('enablebbcode', JIEQI_TYPE_INT, 0, '允许bbcode', false, 1);
        $this->initVar('enablehtml', JIEQI_TYPE_INT, 0, '允许html', false, 1);
        $this->initVar('enablesmilies', JIEQI_TYPE_INT, 0, '允许表情', false, 1);
        $this->initVar('attachsig', JIEQI_TYPE_INT, 0, '显示签名', false, 1);
        $this->initVar('attachment', JIEQI_TYPE_INT, 0, '是否有附件', false, 1);
    }
}
class JieqiMessageHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'message';
        $this->autoid = 'messageid';
        $this->dbname = 'system_message';
    }
}
