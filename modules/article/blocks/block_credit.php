<?php

class BlockArticleCredit extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_credit.html';
    public $cachetime = -1;
    public $exevars = array('field' => 'point', 'listnum' => 10, 'asc' => 0, 'articleid' => 'id');
    public function __construct(&$vars)
    {
        global $jieqiTpl;
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('point', 'articleid', 'uid', 'uptime'))) {
                    $this->exevars['field'] = $varary[0];
                }
            }
            if (1 < $arynum) {
                $varary[1] = trim($varary[1]);
                if (is_numeric($varary[1])) {
                    $this->exevars['listnum'] = intval($varary[1]);
                }
            }
            if (2 < $arynum) {
                $varary[2] = trim($varary[2]);
                if (in_array($varary[2], array('0', '1'))) {
                    $this->exevars['asc'] = $varary[2];
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (is_numeric($varary[3])) {
                    $this->exevars['articleid'] = intval($varary[3]);
                } else {
                    if (substr($varary[3], 0, 1) == '$') {
                        $tmpvar1 = $jieqiTpl->get_assign(substr($varary[3], 1));
                        $this->exevars['articleid'] = intval($tmpvar1);
                    } else {
                        if (isset($_REQUEST[$varary[3]]) && is_numeric($_REQUEST[$varary[3]])) {
                            $this->exevars['articleid'] = intval($_REQUEST[$varary[3]]);
                        }
                    }
                }
            }
        }
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiModules;
        global $jieqiConfigs;
        global $jieqiCredit;
        global $query;
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        if (!isset($jieqiConfigs['article'])) {
            jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
        }
        if (!isset($jieqiCredit['article'])) {
            jieqi_getconfigs('article', 'credit', 'jieqiCredit');
        }
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $creditrows = array();
        $slimit = '1';
        $articleid = 0;
        if (is_numeric($this->exevars['articleid']) && 0 < $this->exevars['articleid']) {
            $articleid = intval($this->exevars['articleid']);
            $slimit = 'articleid = ' . $articleid;
        }
        $jieqiTpl->assign('articleid', $articleid);
        $sort = $this->exevars['field'];
        if ($this->exevars['asc'] == 0) {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }
        $limitstart = 0;
        $listpnum = intval($this->exevars['listnum']);
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_credit') . ' WHERE ' . $slimit . ' ORDER BY ' . $sort . ' ' . $order . ' LIMIT ' . $limitstart . ',' . $listpnum;
        $creditrows = array();
        $query->execute($sql);
        $k = 0;
        while ($row = $query->getRow()) {
            $creditrows[$k] = jieqi_funtoarray('jieqi_htmlstr', $row);
            $mincredit = 0;
            $creditrows[$k]['rank'] = '';
            foreach ($jieqiCredit['article'] as $v) {
                if ($v['minnum'] <= $creditrows[$k]['point'] && $mincredit <= $v['minnum']) {
                    $creditrows[$k]['rank'] = $v['caption'];
                    $mincredit = $v['minnum'];
                }
            }
            $k++;
        }
        $jieqiTpl->assign_by_ref('creditrows', $creditrows);
    }
}