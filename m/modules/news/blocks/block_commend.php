<?php

class BlockNewsCommend extends JieqiBlock
{
    public $module = 'news';
    public $template = 'block_commend.html';
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
        $newsrows = array();
        if (0 < count($this->exevars)) {
            $topic_handler = JieqiNewsTopicHandler::getInstance('JieqiNewsTopicHandler');
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('topicid', '(' . implode(',', $this->exevars) . ')', 'IN'));
            $criteria->setLimit(100);
            $criteria->setStart(0);
            $topic_handler->queryObjects($criteria);
            $k = 0;
            include_once $jieqiModules['news']['path'] . '/include/funnews.php';
            while ($v = $topic_handler->getObject()) {
                $newsrows[$k] = jieqi_news_vars($v);
                $newsrows[$k]['order'] = $k + 1;
                $k++;
            }
        }
        $i = 0;
        $maxrow = count($newsrows);
        $sortrows = array();
        foreach ($this->exevars as $tid) {
            $k = 0;
            while ($k < $maxrow && $newsrows[$k]['topicid'] != $tid) {
                $k++;
            }
            if ($k < $maxrow) {
                $newsrows[$k]['order'] = $i + 1;
                $sortrows[$i] =& $newsrows[$k];
                $i++;
            }
        }
        $jieqiTpl->assign_by_ref('newsrows', $sortrows);
    }
}