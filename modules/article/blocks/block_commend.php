<?php

class BlockArticleCommend extends JieqiBlock
{
    public $module = 'article';
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
        include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/article.php';
        include_once $GLOBALS['jieqiModules']['article']['path'] . '/include/funarticle.php';
        jieqi_getconfigs('article', 'configs');
        jieqi_getconfigs('article', 'sort');
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $articlerows = array();
        if (0 < count($this->exevars)) {
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid IN (0,' . implode(',', $this->exevars) . ')';
            $sql .= ' LIMIT 0, 100';
            $res = $article_handler->execute($sql);
            $k = 0;
            while ($v = $article_handler->getObject($res)) {
                $articlerows[$k] = jieqi_article_vars($v);
                $articlerows[$k]['order'] = $k + 1;
                $k++;
            }
        }
        $i = 0;
        $maxrow = count($articlerows);
        $sortrows = array();
        foreach ($this->exevars as $aid) {
            $k = 0;
            while ($k < $maxrow && $articlerows[$k]['articleid'] != $aid) {
                $k++;
            }
            if ($k < $maxrow) {
                $articlerows[$k]['order'] = $i + 1;
                $sortrows[$i] =& $articlerows[$k];
                $i++;
            }
        }
        $jieqiTpl->assign_by_ref('articlerows', $sortrows);
    }
}