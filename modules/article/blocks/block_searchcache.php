<?php

class BlockArticleSearchcache extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_searchcache.html';
    public $exevars = array('listnum' => 5, 'order' => 'hot', 'asc' => '0', 'results' => '1', 'searchtype' => '0');
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (is_numeric($varary[0])) {
                    $this->exevars['listnum'] = intval($varary[0]);
                }
            }
            if (1 < $arynum) {
                $varary[1] = trim($varary[1]);
                if (in_array($varary[1], array('searchnum', 'cacheid', 'addtime', 'lasttime', 'uptime', 'lasttime', 'hot'))) {
                    $this->exevars['order'] = $varary[1];
                }
            }
            if (2 < $arynum) {
                $varary[2] = trim($varary[3]);
                if (in_array($varary[2], array('0', '1'))) {
                    $this->exevars['asc'] = $varary[2];
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (in_array($varary[3], array('0', '1', '2', '3', '4'))) {
                    $this->exevars['results'] = $varary[3];
                }
            }
            if (4 < $arynum) {
                $varary[4] = trim($varary[4]);
                if (in_array($varary[4], array('-1', '0', '1', '2', '4'))) {
                    $this->exevars['searchtype'] = $varary[4];
                }
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiConfigs;
        if (!isset($jieqiConfigs['article'])) {
            jieqi_getconfigs('article', 'configs');
        }
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_searchcache');
        switch ($this->exevars['results']) {
            case '1':
                $sql .= ' WHERE results = 1';
                break;
            case '2':
                $sql .= ' WHERE results > 1';
                break;
            case '3':
                $sql .= ' WHERE results > 0';
                break;
            case '4':
                $sql .= ' WHERE results = 0';
                break;
            default:
                $sql .= ' WHERE 1';
                break;
        }
        if ($this->exevars['searchtype'] != '-1') {
            $sql .= ' AND searchtype = ' . $this->exevars['searchtype'];
        }
        if ($this->exevars['order'] == 'hot') {
            $sql .= ' ORDER BY searchnum / (lasttime - addtime + 600)';
        } else {
            $sql .= ' ORDER BY ' . $this->exevars['order'];
        }
        if ($this->exevars['asc'] == '1') {
            $sql .= ' ASC';
        } else {
            $sql .= ' DESC';
        }
        $sql .= ' LIMIT 0, ' . $this->exevars['listnum'];
        $query->execute($sql);
        $searchcacherows = array();
        $i = 0;
        while ($row = $query->getRow()) {
            $searchcacherows[$i] = jieqi_query_rowvars($row);
            $i++;
        }
        $jieqiTpl->assign_by_ref('searchcacherows', $searchcacherows);
    }
}