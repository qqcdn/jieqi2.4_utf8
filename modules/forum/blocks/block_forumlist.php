<?php

class BlockForumForumlist extends JieqiBlock
{
    public $module = 'forum';
    public $template = 'block_forumlist.html';
    public $cachetime = -1;
    public $blockvars = array();
    public $exevars = array('catid' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (is_numeric($varary[0]) && 0 < $varary[0]) {
                    $this->exevars['catid'] = intval($varary[0]);
                }
            }
        }
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiUsersStatus;
        global $jieqiUsersGroup;
        include_once $jieqiModules['forum']['path'] . '/class/forumcat.php';
        include_once $jieqiModules['forum']['path'] . '/class/forums.php';
        $criteria = new CriteriaCompo();
        if (!empty($this->exevars['catid'])) {
            $criteria->add(new Criteria('catid', $this->exevars['catid'], '='));
        }
        $criteria->setSort('catorder');
        $criteria->setOrder('ASC');
        $forumcat_handler = JieqiForumcatHandler::getInstance('JieqiForumcatHandler');
        $forumcat_handler->queryObjects($criteria);
        $forumcats = array();
        $i = 0;
        while ($v = $forumcat_handler->getObject()) {
            $forumcats[$i]['catid'] = $v->getVar('catid');
            $forumcats[$i]['cattitle'] = $v->getVar('cattitle');
            $i++;
        }
        unset($criteria);
        $criteria = new CriteriaCompo();
        if (!empty($this->exevars['catid'])) {
            $criteria->add(new Criteria('catid', $this->exevars['catid'], '='));
        }
        $criteria->setSort('catid ASC, forumorder');
        $criteria->setOrder('ASC');
        $forums_handler = JieqiForumsHandler::getInstance('JieqiForumsHandler');
        $forums_handler->queryObjects($criteria);
        $forums = array();
        $i = 0;
        while ($v = $forums_handler->getObject()) {
            $forums[$i]['catid'] = $v->getVar('catid');
            $forums[$i]['authview_n'] = $v->getVar('authview', 'n');
            $forums[$i]['forumid'] = $v->getVar('forumid');
            $forums[$i]['forumname'] = $v->getVar('forumname');
            $forums[$i]['forumdesc'] = $v->getVar('forumdesc');
            $forums[$i]['forumtopics'] = $v->getVar('forumtopics');
            $forums[$i]['forumposts'] = $v->getVar('forumposts');
            $forums[$i]['master_n'] = $v->getVar('master', 'n');
            $forums[$i]['forumlastinfo_n'] = $v->getVar('forumlastinfo', 'n');
            $i++;
        }
        $forumcatary = array();
        $forumary = array();
        $i = 0;
        foreach ($forumcats as $forumcat) {
            $forumcatary[$i] = $forumcat['cattitle'];
            $j = 0;
            foreach ($forums as $forum) {
                if ($forum['catid'] == $forumcat['catid']) {
                    $viewpower['groups'] = jieqi_unserialize($forum['authview_n']);
                    if (!is_array($viewpower['groups'])) {
                        $viewpower['groups'] = array();
                    }
                    if (jieqi_checkpower($viewpower, $jieqiUsersStatus, $jieqiUsersGroup, true)) {
                        $forumary[$i][$j]['forumname'] = $forum['forumname'];
                        $forumary[$i][$j]['forumid'] = $forum['forumid'];
                        $forumary[$i][$j]['desc'] = $forum['forumdesc'];
                        $forumary[$i][$j]['topics'] = $forum['forumtopics'];
                        $forumary[$i][$j]['posts'] = $forum['forumposts'];
                        $masterstr = '';
                        $masterary = jieqi_unserialize($forum['master_n']);
                        if (!is_array($masterary)) {
                            $masterary = array();
                        }
                        foreach ($masterary as $k => $v) {
                            $masterary[$k]['uname'] = jieqi_htmlstr($masterary[$k]['uname']);
                            $masterary[$k]['uid'] = intval($masterary[$k]['uid']);
                        }
                        $forumary[$i][$j]['masters'] = $masterary;
                        $tmpary = jieqi_unserialize($forum['forumlastinfo_n']);
                        if (is_array($tmpary)) {
                            $forumary[$i][$j]['lastuid'] = intval($tmpary['uid']);
                            $forumary[$i][$j]['lastuname'] = jieqi_htmlstr($tmpary['uname']);
                            $forumary[$i][$j]['lasttime'] = intval($tmpary['time']);
                            if (isset($tmpary['uid']) && 0 < $tmpary['uid']) {
                                $tmpvar = sprintf($jieqiLang['forum']['last_post_info'], date(JIEQI_DATE_FORMAT . ' ' . JIEQI_TIME_FORMAT, $tmpary['time']), '<a href="' . jieqi_geturl('system', 'user', $tmpary['uid']) . '" target="_blank">' . $tmpary['uname'] . '</a>');
                            } else {
                                $tmpvar = sprintf($jieqiLang['forum']['last_post_info'], date(JIEQI_DATE_FORMAT . ' ' . JIEQI_TIME_FORMAT, $tmpary['time']), '<em>' . $jieqiLang['forum']['user_guest'] . '</em>');
                            }
                        } else {
                            $forumary[$i][$j]['lastuid'] = 0;
                            $forumary[$i][$j]['lastuname'] = '';
                            $forumary[$i][$j]['lasttime'] = 0;
                        }
                        $forumary[$i][$j]['lastinfo'] = $tmpvar;
                        $j++;
                    }
                }
            }
            $i++;
        }
        $jieqiTpl->assign('url_search', $jieqiModules['forum']['url'] . '/search.php');
        $jieqiTpl->assign_by_ref('forumcats', $forumcatary);
        $jieqiTpl->assign_by_ref('forums', $forumary);
    }
}