<?php

class BlockLinkLinklist extends JieqiBlock
{
    public $module = 'link';
    public $template = 'block_linklist.html';
    public $exevars = array('listnum' => 10, 'flag' => '0', 'order' => '0', 'width' => 64);
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
                    $this->exevars['flag'] = $varary[1];
                }
            }
            if (2 < $arynum) {
                $varary[2] = trim($varary[2]);
                if (in_array($varary[2], array('0', '1', '2'))) {
                    $this->exevars['order'] = $varary[2];
                }
            }
            if (3 < $arynum) {
                $varary[3] = trim($varary[3]);
                if (is_numeric($varary[3])) {
                    $this->exevars['width'] = intval($varary[3]);
                }
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiConfigs;
        if (!is_object($jieqiTpl)) {
            $jieqiTpl = JieqiTpl::getInstance();
        }
        include_once $GLOBALS['jieqiModules']['link']['path'] . '/class/link.php';
        $link_handler = JieqiLinkHandler::getInstance('JieqiLinkHandler');
        $criteria = new CriteriaCompo(new Criteria('passed', '1', '='));
        if ($this->exevars['flag'] == 0) {
            $criteria->add(new Criteria('linktype', '0'));
        } else {
            if ($this->exevars['flag'] == 1) {
                $criteria->add(new Criteria('linktype', '1'));
            }
        }
        if ($this->exevars['order'] == 0) {
            $criteria->setSort('listorder asc,addtime');
            $criteria->setOrder('DESC');
        } else {
            if ($this->exevars['order'] == 1) {
                $criteria->setSort('listorder');
                $criteria->setOrder('asc');
            } else {
                $criteria->setSort('addtime');
                $criteria->setOrder('DESC');
            }
        }
        $criteria->setLimit($this->exevars['listnum']);
        $criteria->setStart(0);
        $link_handler->queryObjects($criteria);
        $linkrows = array();
        $k = 0;
        while ($v = $link_handler->getObject()) {
            $linkrows[$k]['linkid'] = $v->getVar('linkid');
            $linkrows[$k]['linktype'] = $v->getVar('linktype');
            $linkrows[$k]['name'] = $v->getVar('name');
            $linkrows[$k]['namecolor'] = $v->getVar('namecolor');
            $linkrows[$k]['url'] = $v->getVar('url');
            $linkrows[$k]['logo'] = $v->getVar('logo');
            $linkrows[$k]['username'] = $v->getVar('username');
            $linkrows[$k]['mastername'] = $v->getVar('mastername');
            $linkrows[$k]['mastertell'] = $v->getVar('mastertell');
            $linkrows[$k]['hits'] = $v->getVar('hits');
            $linkrows[$k]['addtime'] = date('y-m-d', $v->getVar('addtime'));
            $k++;
        }
        $jieqiTpl->assign_by_ref('linkrows', $linkrows);
        $jieqiTpl->assign('url_more', $GLOBALS['jieqiModules']['link']['url'] . '/index.php');
    }
}