<?php

class BlockArticleArticlestat extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_articlestat.html';
    public $exevars = array('field' => 'articles', 'update' => 0, 'sortid' => 0, 'isfull' => 0, 'isvip' => 0, 'rgroup' => -1);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('articles', 'lastupdate', 'postdate'))) {
                    $this->exevars['field'] = $varary[0];
                }
            }
            if (1 < $arynum) {
                $varary[1] = trim($varary[1]);
                if (is_numeric($varary[1]) || in_array($varary[1], array('day', 'week', 'month', 'all'))) {
                    $this->exevars['update'] = $varary[1];
                }
            }
            if (2 < $arynum) {
                $varary[2] = trim($varary[2]);
                $tmpvar = str_replace('|', '', $varary[2]);
                if (is_numeric($tmpvar)) {
                    $this->exevars['sortid'] = $varary[2];
                } else {
                    if (substr($varary[2], 0, 1) == '$') {
                        $tmpvar1 = $jieqiTpl->get_assign(substr($varary[2], 1));
                        if (is_numeric(str_replace('|', '', $tmpvar1))) {
                            $this->exevars['sortid'] = $tmpvar1;
                        }
                    } else {
                        if (isset($_REQUEST[$tmpvar]) && is_numeric($_REQUEST[$tmpvar])) {
                            $this->exevars['sortid'] = $_REQUEST[$tmpvar];
                        }
                    }
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (in_array($varary[3], array('0', '1', '2'))) {
                    $this->exevars['isfull'] = $varary[3];
                }
            }
            if (4 < $arynum) {
                $varary[4] = trim($varary[4]);
                if (in_array($varary[4], array('0', '1', '2', '3'))) {
                    $this->exevars['isvip'] = $varary[4];
                }
            }
            if (5 < $arynum) {
                $varary[5] = trim($varary[5]);
                if (is_numeric($varary[5])) {
                    $this->exevars['rgroup'] = intval($varary[5]);
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
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        jieqi_getconfigs('article', 'configs');
        jieqi_getconfigs('article', 'sort');
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
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
        $sql = 'SELECT count(*) AS cot, sum(words) AS words, sum(daywords) AS daywords, sum(weekwords) AS weekwords, sum(monthwords) AS monthwords FROM ' . jieqi_dbprefix('article_article') . ' WHERE display = 0 AND words > 0';
        if (!empty($this->exevars['sortid'])) {
            $sortary = explode('|', $this->exevars['sortid']);
            if (0 < count($sortary)) {
                $ssql = '';
                foreach ($sortary as $ss) {
                    if ($ssql != '') {
                        $ssql .= ' OR ';
                    }
                    if (strrchr($ss, '.') == false) {
                        $ssql .= 'sortid = ' . intval($ss);
                    } else {
                        $ssql .= 'typeid = ' . intval(substr(strrchr($ss, '.'), 1));
                    }
                }
                if (1 < count($sortary)) {
                    $ssql = '(' . $ssql . ')';
                }
                $sql .= ' AND ' . $ssql;
            }
        }
        if ($this->exevars['isfull'] == 1) {
            $sql .= ' AND fullflag = 1';
        } else {
            if ($this->exevars['isfull'] == 2) {
                $sql .= ' AND fullflag = 0';
            }
        }
        if ($this->exevars['isvip'] == 1) {
            $sql .= ' AND isvip > 0';
        } else {
            if ($this->exevars['isvip'] == 2) {
                $sql .= ' AND isvip = 0';
            } else {
                if ($this->exevars['isvip'] == 3) {
                    $sql .= ' AND isvip > 0 AND monthly > 0';
                }
            }
        }
        if (is_numeric($this->exevars['rgroup']) && 0 <= $this->exevars['rgroup']) {
            $sql .= ' AND rgroup = ' . intval($this->exevars['rgroup']);
        }
        if (in_array($this->exevars['field'], array('lastupdate', 'postdate'))) {
            if (is_numeric($this->exevars['update']) && 0 < $this->exevars['update']) {
                $sql .= ' AND ' . $this->exevars['field'] . ' >= ' . (time() - round(86400 * $this->exevars['update']));
            } else {
                switch ($this->exevars['update']) {
                    case 'day':
                        $sql .= ' AND ' . $this->exevars['field'] . ' >= ' . $daystart;
                        break;
                    case 'week':
                        $sql .= ' AND ' . $this->exevars['field'] . ' >= ' . $weekstart;
                        break;
                    case 'month':
                        $sql .= ' AND ' . $this->exevars['field'] . ' >= ' . $monthstart;
                        break;
                }
            }
        }
        $res = $query->execute($sql);
        $articlestat = $query->getRow($res);
        $wordsary = array('words', 'daywords', 'weekwords', 'monthwords');
        foreach ($wordsary as $s) {
            $articlestat[str_replace('words', 'size', $s) . '_c'] = $articlestat[$s];
        }
        $articlestat = jieqi_funtoarray('jieqi_htmlstr', $articlestat);
        $jieqiTpl->assign_by_ref('articlestat', $articlestat);
    }
}