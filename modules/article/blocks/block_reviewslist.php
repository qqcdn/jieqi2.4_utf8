<?php

class BlockArticleReviewslist extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_reviewslist.html';
    public $exevars = array('listnum' => 10, 'istop' => 0, 'isgood' => 0, 'articleid' => 0, 'chapterid' => 0);
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
                if (in_array($varary[1], array('0', '1', '2'))) {
                    $this->exevars['istop'] = $varary[1];
                }
            }
            if (2 < $arynum) {
                $varary[2] = trim($varary[2]);
                if (in_array($varary[2], array('0', '1', '2'))) {
                    $this->exevars['isgood'] = $varary[2];
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
            if (4 < $arynum) {
                $varary[4] = trim($varary[4]);
                if (is_numeric($varary[4])) {
                    $this->exevars['chapterid'] = intval($varary[4]);
                } else {
                    if (substr($varary[4], 0, 1) == '$') {
                        $tmpvar1 = $jieqiTpl->get_assign(substr($varary[4], 1));
                        $this->exevars['chapterid'] = intval($tmpvar1);
                    } else {
                        if (isset($_REQUEST[$varary[4]]) && is_numeric($_REQUEST[$varary[4]])) {
                            $this->exevars['chapterid'] = intval($_REQUEST[$varary[4]]);
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
        global $jieqiConfigs;
        global $jieqiSort;
        global $jieqiOption;
        if (!isset($jieqiConfigs['article'])) {
            jieqi_getconfigs('article', 'configs');
        }
        if (!isset($jieqiSort['article'])) {
            jieqi_getconfigs('article', 'sort');
        }
        if (!isset($jieqiOption['article'])) {
            jieqi_getconfigs('article', 'option', 'jieqiOption');
        }
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        $criteria = new CriteriaCompo();
        $criteria->setFields('r.*,a.articleid,a.articlename,a.articlecode,a.authorid,a.author,a.sortid,a.typeid,a.imgflag,a.isvip');
        $criteria->setTables(jieqi_dbprefix('article_reviews') . ' AS r LEFT JOIN ' . jieqi_dbprefix('article_article') . ' AS a ON r.ownerid=a.articleid');
        $criteria->add(new Criteria('r.display', '0'));
        if (0 < $this->exevars['chapterid']) {
            $criteria->add(new Criteria('r.targetid', intval($this->exevars['chapterid'])));
        } else {
            if (0 < $this->exevars['articleid']) {
                $criteria->add(new Criteria('r.ownerid', intval($this->exevars['articleid'])));
                if ($this->exevars['chapterid'] == 0) {
                    $criteria->add(new Criteria('r.targetid', 0));
                }
            }
        }
        if ($this->exevars['istop'] == 1) {
            $criteria->add(new Criteria('r.istop', '1'));
        } else {
            if ($this->exevars['istop'] == 2) {
                $criteria->add(new Criteria('r.istop', '0'));
            }
        }
        if ($this->exevars['isgood'] == 1) {
            $criteria->add(new Criteria('r.isgood', '1'));
        } else {
            if ($this->exevars['isgood'] == 2) {
                $criteria->add(new Criteria('r.isgood', '0'));
            }
        }
        $criteria->setSort('r.topicid');
        $criteria->setOrder('DESC');
        $criteria->setLimit($this->exevars['listnum']);
        $criteria->setStart(0);
        $query->queryObjects($criteria);
        $reviewrows = array();
        $i = 0;
        while ($v = $query->getObject()) {
            $reviewrows[$i] = jieqi_query_rowvars($v);
            $reviewrows[$i]['reviewtitle'] = jieqi_htmlstr(str_replace(array("\r", "\n"), array('', ' '), $v->getVar('title', 'n')));
            if (isset($jieqiSort['article'][$reviewrows[$i]['sortid']]['caption'])) {
                $reviewrows[$i]['sort'] = $jieqiSort['article'][$reviewrows[$i]['sortid']]['caption'];
            } else {
                $reviewrows[$i]['sort'] = '';
            }
            if (0 < $reviewrows[$i]['typeid'] && isset($jieqiSort['article'][$reviewrows[$i]['sortid']]['types'][$reviewrows[$i]['typeid']])) {
                $reviewrows[$i]['type'] = $jieqiSort['article'][$reviewrows[$i]['sortid']]['types'][$reviewrows[$i]['typeid']];
            } else {
                $reviewrows[$i]['type'] = '';
            }
            $reviewrows[$i]['postdate'] = date('m-d H:i', $v->getVar('posttime'));
            $reviewrows[$i]['url_review'] = $article_dynamic_url . '/reviews.php?aid=' . $v->getVar('ownerid');
            $reviewrows[$i]['url_articleinfo'] = jieqi_geturl('article', 'article', $v->getVar('articleid'), 'info', $v->getVar('articlecode', 'n'));
            $reviewrows[$i]['url_articleindex'] = jieqi_geturl('article', 'article', $v->getVar('articleid'), 'index', $v->getVar('articlecode', 'n'));
            $reviewrows[$i]['url_articleread'] = $reviewrows[$i]['url_articleindex'];
            $reviewrows[$i]['url_image'] = jieqi_geturl('article', 'cover', $v->getVar('articleid'), 's', $v->getVar('imgflag', 'n'));
            $i++;
        }
        $jieqiTpl->assign_by_ref('reviewrows', $reviewrows);
        $jieqiTpl->assign('url_more', $article_dynamic_url . '/reviewslist.php');
    }
}