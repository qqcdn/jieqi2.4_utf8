<?php

class BlockNewsSortroutes extends JieqiBlock
{
    public $module = 'news';
    public $template = 'block_sortroutes.html';
    public $exevars = array('sortid' => 0);
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (is_numeric($varary[0])) {
                    $this->exevars['sortid'] = intval($varary[0]);
                } else {
                    if (substr($varary[0], 0, 1) == '$') {
                        $tmpvar1 = $jieqiTpl->get_assign(substr($varary[0], 1));
                        if (is_numeric($tmpvar1)) {
                            $this->exevars['sortid'] = intval($tmpvar1);
                        }
                    } else {
                        if (isset($_REQUEST[$varary[0]]) && is_numeric($_REQUEST[$varary[0]])) {
                            $this->exevars['sortid'] = intval($_REQUEST[$varary[0]]);
                        }
                    }
                }
            }
        } else {
            if (isset($_REQUEST['sortid']) && is_numeric($_REQUEST['sortid'])) {
                $this->exevars['sortid'] = intval($_REQUEST['sortid']);
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function setContent($isreturn = false)
    {
        global $jieqiSort;
        global $jieqiTpl;
        global $jieqiConfigs;
        global $jieqiModules;
        if (!isset($jieqiSort['news'])) {
            jieqi_getconfigs('news', 'sort', 'jieqiSort');
        }
        include_once JIEQI_ROOT_PATH . '/include/funsort.php';
        $sortroutes = jieqi_sort_routes($jieqiSort['news'], $this->exevars['sortid']);
        $jieqiTpl->assign_by_ref('sortroutes', $sortroutes);
    }
}