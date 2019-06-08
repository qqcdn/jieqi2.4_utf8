<?php

class BlockNewsList extends JieqiBlock
{
    public $module = 'news';
    public $template = 'block_newslist.html';
    public $exevars = array('field' => 'topicid', 'listnum' => 15, 'sortid' => 0, 'asc' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('topicid', 'addtime', 'uptime', 'views', 'marknum', 'topnum', 'downnum', 'scorenum', 'sumscore', 'reviews', 'replies'))) {
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
                if (is_numeric($varary[2]) && 0 < $varary[2]) {
                    $this->exevars['sortid'] = intval($varary[2]);
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (in_array($varary[3], array('0', '1'))) {
                    $this->exevars['asc'] = $varary[3];
                }
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiConfigs;
        global $jieqiSort;
        global $jieqiModules;
        if (!isset($jieqiConfigs['news'])) {
            jieqi_getconfigs('news', 'configs');
        }
        if (!isset($jieqiSort['news'])) {
            jieqi_getconfigs('news', 'sort');
        }
        include_once $GLOBALS['jieqiModules']['news']['path'] . '/class/topic.php';
        $topic_handler = JieqiNewsTopicHandler::getInstance('JieqiNewsTopicHandler');
        $criteria = new CriteriaCompo();
        if (0 < $this->exevars['sortid'] && isset($jieqiSort['news'][$this->exevars['sortid']])) {
            include_once JIEQI_ROOT_PATH . '/include/funsort.php';
            $criteria->add(new Criteria('sortid', '(' . jieqi_sort_childs($jieqiSort['news'], $this->exevars['sortid']) . ')', 'IN'));
        }
        $criteria->add(new Criteria('display', 0, '='));
        $criteria->setSort($this->exevars['field']);
        if ($this->exevars['asc'] == 1) {
            $criteria->setOrder('ASC');
        } else {
            $criteria->setOrder('DESC');
        }
        $criteria->setLimit($this->exevars['listnum']);
        $criteria->setStart(0);
        $topic_handler->queryObjects($criteria);
        $newsrows = array();
        $k = 0;
        include_once $jieqiModules['news']['path'] . '/include/funnews.php';
        while ($v = $topic_handler->getObject()) {
            $newsrows[$k] = jieqi_news_vars($v);
            $k++;
        }
        $jieqiTpl->assign_by_ref('newsrows', $newsrows);
        if ($this->exevars['field'] == 'topicid') {
            $url_more = jieqi_geturl('news', 'newslist', 1, $this->exevars['sortid']);
        } else {
            $url_more = jieqi_geturl('news', 'newslist', 1, $this->exevars['sortid'], $this->exevars['field']);
        }
        $jieqiTpl->assign('url_more', $url_more);
    }
}