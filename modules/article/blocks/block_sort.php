<?php

class BlockArticleSort extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_sort.html';
    public function setContent($isreturn = false)
    {
        global $jieqiSort;
        global $jieqiTpl;
        global $jieqiConfigs;
        jieqi_getconfigs('article', 'sort');
        jieqi_getconfigs('article', 'configs');
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $sortrows = array();
        $jieqiTpl->assign('url_articlelist', jieqi_geturl('article', 'articlelist', 1, 0));
        $i = 0;
        foreach ($jieqiSort['article'] as $k => $v) {
            $sortrows[$i] = array('groupid' => intval($v['group']), 'sortid' => $k, 'sortname' => $v['caption'], 'sortcode' => $v['code'], 'types' => $v['types'], 'url_sort' => jieqi_geturl('article', 'articlelist', 1, $k));
            $i++;
        }
        $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $sortrows));
    }
}