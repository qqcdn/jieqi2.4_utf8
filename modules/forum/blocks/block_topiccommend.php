<?php

class BlockForumTopiccommend extends JieqiBlock
{
    public $module = 'forum';
    public $template = 'block_topiccommend.html';
    public $exevars = array();
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $tmpary = explode('|', trim($this->blockvars['vars']));
            foreach ($tmpary as $v) {
                $v = trim($v);
                if (is_numeric($v)) {
                    $this->exevars[] = intval($v);
                }
            }
            $this->exevars = array_unique($this->exevars);
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        $topicrows = array();
        if (0 < count($this->exevars)) {
            include_once JIEQI_ROOT_PATH . '/include/funpost.php';
            include_once $GLOBALS['jieqiModules']['forum']['path'] . '/class/forumtopics.php';
            $topics_handler = JieqiForumtopicsHandler::getInstance('JieqiForumtopicsHandler');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('topicid', '(0,' . implode(',', $this->exevars) . ')', 'IN'));
            $criteria->setLimit(100);
            $criteria->setStart(0);
            $topics_handler->queryObjects($criteria);
            $k = 0;
            while ($topic = $topics_handler->getObject()) {
                $topicrows[$k] = jieqi_topic_vars($topic);
                $k++;
            }
        }
        $jieqiTpl->assign_by_ref('topicrows', $topicrows);
        if (is_numeric($this->exevars['forumid']) && 0 < intval($this->exevars['f' . "\n" . '		orumid'])) {
            $jieqiTpl->assign('url_more', jieqi_geturl('forum', 'topiclist', 1, intval($this->exevars['forumid'])));
        } else {
            $jieqiTpl->assign('url_more', $GLOBALS['jieqiModules']['forum']['url'] . '/index.php');
        }
    }
}