<?php

class BlockArticleOsale extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_osale.html';
    public $exevars = array('field' => 'osaleid', 'listnum' => 10, 'asc' => 0, 'articleid' => '0');
    public function __construct(&$vars)
    {
        global $jieqiTpl;
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (in_array($varary[0], array('osaleid', 'articleid', 'obookid', 'accountid', 'buytime'))) {
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
        if ($this->exevars['articleid'] == 0) {
            $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
        } else {
            $this->cachetime = -1;
            $this->blockvars['cachetime'] = -1;
        }
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiModules;
        global $jieqiConfigs;
        global $jieqiOsale;
        global $query;
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        if (!isset($jieqiConfigs['article'])) {
            jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
        }
        if (!isset($jieqiConfigs['obook'])) {
            jieqi_getconfigs('obook', 'configs', 'jieqiConfigs');
        }
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $osalerows = array();
        $slimit = '1';
        if (is_numeric($this->exevars['articleid']) && 0 < $this->exevars['articleid']) {
            $slimit = 'articleid = ' . intval($this->exevars['articleid']);
        }
        $sort = $this->exevars['field'];
        if ($this->exevars['asc'] == 0) {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }
        $limitstart = 0;
        $listpnum = intval($this->exevars['listnum']);
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('obook_osale') . ' WHERE ' . $slimit . ' ORDER BY ' . $sort . ' ' . $order . ' LIMIT ' . $limitstart . ',' . $listpnum;
        $osalerows = array();
        $query->execute($sql);
        $k = 0;
        while ($row = $query->getRow()) {
            $osalerows[$k] = jieqi_funtoarray('jieqi_htmlstr', $row);
            $k++;
        }
        $jieqiTpl->assign_by_ref('osalerows', $osalerows);
    }
}