<?php

class BlockForumTopiclist extends JieqiBlock
{
    public $module = 'forum';
    public $template = 'block_topiclist.html';
    public $blockvars = array();
    public $exevars = array('field' => 'replytime', 'listnum' => 10, 'ownerid' => '0', 'asc' => 0, 'flag' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('topicid', 'replytime', 'posttime', 'views', 'replies'))) {
                    $this->exevars['field'] = $varary[0];
                }
            }
            if (1 < $arynum) {
                $varary[1] = trim($varary[1]);
                if (is_numeric($varary[1]) && 0 < $varary[1]) {
                    $this->exevars['listnum'] = intval($varary[1]);
                }
            }
            if (2 < $arynum) {
                $varary[2] = trim($varary[2]);
                $tmpvar = str_replace('|', '', $varary[2]);
                if (is_numeric($tmpvar)) {
                    $this->exevars['ownerid'] = $varary[2];
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (in_array($varary[3], array('0', '1'))) {
                    $this->exevars['asc'] = $varary[3];
                }
            }
            if (4 < $arynum) {
                $varary[4] = trim($varary[4]);
                if (in_array($varary[4], array('0', '1', '2'))) {
                    $this->exevars['flag'] = $varary[4];
                }
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        include_once JIEQI_ROOT_PATH . '/include/funpost.php';
        include_once $GLOBALS['jieqiModules']['forum']['path'] . '/class/forumtopics.php';
        $topics_handler = JieqiForumtopicsHandler::getInstance('JieqiForumtopicsHandler');
        $criteria = new CriteriaCompo();
        if (!empty($this->exevars['ownerid'])) {
            $oidary = explode('|', $this->exevars['ownerid']);
            if (1 < count($oidary)) {
                foreach ($oidary as $k => $v) {
                    $oidary[$k] = intval($oidary[$k]);
                }
                $criteria->add(new Criteria('ownerid', '(' . implode(', ', $oidary) . ')', 'IN'));
            } else {
                $criteria->add(new Criteria('ownerid', intval($this->exevars['ownerid']), '='));
            }
        }
        if ($this->exevars['flag'] == 1) {
            $criteria->add(new Criteria('isgood', 0, '>'));
        } else {
            if ($this->exevars['flag'] == 2) {
                $criteria->add(new Criteria('istop', 0, '>'));
            }
        }
        $criteria->setSort($this->exevars['field']);
        if ($this->exevars['asc'] == 1) {
            $criteria->setOrder('ASC');
        } else {
            $criteria->setOrder('DESC');
        }
        $criteria->setLimit($this->exevars['listnum']);
        $criteria->setStart(0);
        $topics_handler->queryObjects($criteria);
        $topicrows = array();
        $k = 0;
        while ($topic = $topics_handler->getObject()) {
            $topicrows[$k] = jieqi_topic_vars($topic);
            $k++;
        }
        $jieqiTpl->assign_by_ref('topicrows', $topicrows);
        if (is_numeric($this->exevars['ownerid']) && 0 < intval($this->exevars['f' . "\r\n" . '		orumid'])) {
            $jieqiTpl->assign('url_more', jieqi_geturl('forum', 'topiclist', 1, intval($this->exevars['ownerid'])));
        } else {
            $jieqiTpl->assign('url_more', $GLOBALS['jieqiModules']['forum']['url'] . '/');
        }
    }
}