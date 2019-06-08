<?php

class BlockArticleReadlog extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_readlog.html';
    public $exevars = array('listnum' => 10, 'commend' => '');
    public $cachetime = -1;
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
                if (0 < strlen($varary[1])) {
                    $this->exevars['commend'] = $varary[1];
                }
            }
        }
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiConfigs;
        global $jieqiSort;
        global $jieqi_file_postfix;
        global $jieqiUserdata;
        if (!isset($jieqiConfigs['system'])) {
            jieqi_getconfigs('system', 'configs');
        }
        if (!isset($jieqiConfigs['article'])) {
            jieqi_getconfigs('article', 'configs');
        }
        if (!isset($jieqiSort['system'])) {
            jieqi_getconfigs('article', 'sort');
        }
        if (!isset($jieqiConfigs['system']['usersetpath'])) {
            $jieqiConfigs['system']['usersetpath'] = 'userdata';
        }
        include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/article.php';
        include_once $GLOBALS['jieqiModules']['article']['path'] . '/include/funarticle.php';
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $articlerows = array();
        jieqi_getfilevars('system', $jieqiConfigs['system']['usersetpath'], $_SESSION['jieqiUserId'], 'jieqiUserdata');
        if (!isset($jieqiUserdata['article']['readlog'])) {
            $jieqiUserdata['article']['readlog'] = array();
        }
        $aids = array();
        $aidt = array();
        $k = 0;
        foreach ($jieqiUserdata['article']['readlog'] as $k => $v) {
            $v['articleid'] = intval($v['articleid']);
            $aids[$k] = $v['articleid'];
            $aidt[$v['articleid']] = $v;
            $k++;
            if ($this->exevars['listnum'] <= $k) {
                break;
            }
        }
        if (count($aids) < $this->exevars['listnum'] && 0 < strlen($this->exevars['commend'])) {
            $addnum = $this->exevars['listnum'] - count($aids);
            $tmpary = explode('|', $this->exevars['commend']);
            foreach ($tmpary as $v) {
                $v = intval(trim($v));
                if (0 < $v && !in_array($v, $aids)) {
                    $aids[] = $v;
                    $addnum--;
                    if ($addnum <= 0) {
                        break;
                    }
                }
            }
        }
        if (0 < count($aids)) {
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid IN (0,' . implode(',', $aids) . ')';
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
        foreach ($aids as $aid) {
            $k = 0;
            while ($k < $maxrow && $articlerows[$k]['articleid'] != $aid) {
                $k++;
            }
            if ($k < $maxrow) {
                $articlerows[$k]['order'] = $i + 1;
                $sortrows[$i] =& $articlerows[$k];
                if (isset($aidt[$sortrows[$i]['articleid']])) {
                    $sortrows[$i]['readtime'] = $aidt[$sortrows[$i]['articleid']]['time'];
                } else {
                    $sortrows[$i]['readtime'] = 0;
                }
                $i++;
            }
        }
        $jieqiTpl->assign_by_ref('articlerows', $sortrows);
    }
}