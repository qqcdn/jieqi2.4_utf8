<?php

class BlockSystemSql extends JieqiBlock
{
    public $module = 'system';
    public $template = 'block_sql.html';
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        $this->blockvars['vars'] = trim($this->blockvars['vars']);
        $this->blockvars['cacheid'] = md5($this->blockvars['vars']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiModules;
        global $jieqiConfigs;
        global $jieqiLang;
        global $jieqiOption;
        global $jieqiSort;
        $sqlerror = '';
        if (!preg_match('/^select\\s+/i', $this->blockvars['vars'])) {
            $sqlerror = 'sql format error!';
        }
        if (preg_match('/(insert|update|delete|create|drop|truncate|replace|show|index|use|source|grant|into|file|out|load|data)/i', $this->blockvars['vars'])) {
            $sqlerror = 'sql function disabled!';
        }
        $jieqiTpl->assign('sqlerror', $sqlerror);
        $sqlrows = array();
        if ($sqlerror == '') {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $sql = $this->blockvars['vars'];
            if (!preg_match('/limit/i', $sql)) {
                $sql .= ' LIMIT 0, 10';
            }
            $matches = array();
            $module = '';
            if (preg_match('/' . JIEQI_DB_PREFIX . '_([a-z0-9]+)_/i', $sql, $matches)) {
                $module = $matches[1];
            }
            if ($module == 'article') {
                jieqi_getconfigs('article', 'sort');
                $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
                $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
                $jieqiTpl->assign('article_static_url', $article_static_url);
                $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
                $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
                $daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
                $monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
                $monthdays = date('t', $monthstart);
                $prestart = mktime(0, 0, 0, (int) $tmpvar[1] - 1, 1, (int) $tmpvar[0]);
                $predays = date('t', $prestart);
                $tmpvar = date('w', JIEQI_NOW_TIME);
                if ($tmpvar == 0) {
                    $tmpvar = 7;
                }
                $weekstart = $daystart;
                if (1 < $tmpvar) {
                    $weekstart -= ($tmpvar - 1) * 86400;
                }
                $repfrom = array('<{$daystart}>', '<{$weekstart}>', '<{$monthstart}>', '<{$prestart}>', '<{$monthdays}>', '<{$predays}>');
                $repto = array($daystart, $weekstart, $monthstart, $prestart, $monthdays, $predays);
                $sql = str_replace($repfrom, $repto, $sql);
            }
            if ($query->execute($sql)) {
                if ($module == 'article') {
                    include_once $GLOBALS['jieqiModules']['article']['path'] . '/include/funarticle.php';
                    $k = 0;
                    while ($v = $query->getObject($res)) {
                        $sqlrows[$k] = jieqi_article_vars($v);
                        $k++;
                    }
                } else {
                    $k = 0;
                    while ($row = $query->getRow()) {
                        $sqlrows[$k] = jieqi_query_rowvars($row, 's', $module);
                        $k++;
                    }
                }
            }
        }
        $jieqiTpl->assign_by_ref('sqlrows', $sqlrows);
    }
}