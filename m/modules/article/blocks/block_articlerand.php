<?php

class BlockArticleRand extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_articlerand.html';
    public $exevars = array('listnum' => 10, 'articleid' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (is_numeric($varary[0]) && 0 < $varary[0]) {
                    $this->exevars['listnum'] = intval($varary[0]);
                }
            }
            if (1 < $arynum) {
                $varary[1] = trim($varary[1]);
                if (is_numeric($varary[1])) {
                    $this->exevars['articleid'] = intval($varary[1]);
                } else {
                    if (substr($varary[1], 0, 1) == '$') {
                        $tmpvar1 = $jieqiTpl->get_assign(substr($varary[1], 1));
                        $this->exevars['articleid'] = intval($tmpvar1);
                    } else {
                        if (isset($_REQUEST[$varary[1]]) && is_numeric($_REQUEST[$varary[1]])) {
                            $this->exevars['articleid'] = intval($_REQUEST[$varary[1]]);
                        }
                    }
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
        include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/article.php';
        include_once $GLOBALS['jieqiModules']['article']['path'] . '/include/funarticle.php';
        jieqi_getconfigs('article', 'configs');
        jieqi_getconfigs('article', 'sort');
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $articlerows = array();
        if (0 < $this->exevars['listnum']) {
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            if (empty($this->exevars['articleid'])) {
                $sql = 'SELECT MAX(articleid) AS maxid FROM ' . jieqi_dbprefix('article_article') . ' WHERE 1';
                $res = $article_handler->execute($sql);
                $row = $article_handler->getRow($res);
                if (is_array($row)) {
                    $maxid = $row['maxid'];
                } else {
                    $maxid = 0;
                }
                if (0 < $maxid) {
                    $randid = rand(0, $maxid);
                } else {
                    $randid = 0;
                }
            } else {
                $randid = $this->exevars['articleid'];
            }
            $before = true;
            if ($randid <= $this->exevars['listnum']) {
                $before = false;
            }
            if ($before) {
                $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid < ' . $randid . ' ORDER BY articleid DESC LIMIT 0, ' . $this->exevars['listnum'];
            } else {
                $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid > ' . $randid . ' ORDER BY articleid ASC LIMIT 0, ' . $this->exevars['listnum'];
            }
            $res = $article_handler->execute($sql);
            $k = 0;
            while ($v = $article_handler->getObject($res)) {
                $articlerows[$k] = jieqi_article_vars($v);
                $articlerows[$k]['order'] = $k + 1;
                $k++;
            }
        }
        $jieqiTpl->assign_by_ref('articlerows', $articlerows);
    }
}