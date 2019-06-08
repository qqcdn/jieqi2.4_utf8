<?php

class BlockArticleTaglist extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_taglist.html';
    public $exevars = array('field' => 'tagid', 'listnum' => 15, 'asc' => 0);
    public function __construct(&$vars)
    {
        global $jieqiTpl;
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('tagid', 'linknum', 'dayvisit', 'weekvisit', 'monthvisit', 'allvisit'))) {
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
                if (in_array($varary[2], array('0', '1'))) {
                    $this->exevars['asc'] = $varary[2];
                }
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $query;
        global $jieqiModules;
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
        $daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
        $monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
        $tmpvar = date('w', JIEQI_NOW_TIME);
        if ($tmpvar == 0) {
            $tmpvar = 7;
        }
        $weekstart = $daystart;
        if (1 < $tmpvar) {
            $weekstart -= ($tmpvar - 1) * 86400;
        }
        $where = '1';
        switch ($this->exevars['field']) {
            case 'monthvisit':
                $where = 'lastvisit >= ' . $monthstart;
                break;
            case 'weekvisit':
                $where = 'lastvisit >= ' . $weekstart;
                break;
            case 'dayvisit':
                $where = 'lastvisit >= ' . $daystart;
                break;
        }
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_tag') . ' WHERE ' . $where . ' ORDER BY ' . $this->exevars['field'];
        if ($this->exevars['asc'] == 1) {
            $sql .= ' ASC';
        } else {
            $sql .= ' DESC';
        }
        $sql .= ' LIMIT 0, ' . $this->exevars['listnum'];
        $query->execute($sql);
        $tagrows = array();
        $i = 0;
        while ($row = $query->getRow()) {
            foreach ($row as $k => $v) {
                $tagrows[$i][$k] = jieqi_htmlstr($v);
            }
            $tagrows[$i]['sortnum'] = $tagrows[$i][$this->exevars['field']];
            $i++;
        }
        $jieqiTpl->assign_by_ref('tagrows', $tagrows);
        $url_more = $jieqiModules['article']['url'] . '/taglist.php?sort=' . $this->exevars['field'];
        if ($this->exevars['asc'] == 1) {
            $url_more .= '&order=ASC';
        }
        $jieqiTpl->assign('url_more', $url_more);
    }
}