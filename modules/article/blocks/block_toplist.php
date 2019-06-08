<?php

class BlockArticleToplist extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_toplist.html';
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiConfigs;
        global $jieqiTop;
        if (!isset($jieqiConfigs['article'])) {
            jieqi_getconfigs('article', 'configs');
        }
        if (!isset($jieqiTop['article'])) {
            jieqi_getconfigs('article', 'top');
        }
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $toprows = array();
        foreach ($jieqiTop['article'] as $k => $v) {
            $url = jieqi_geturl('article', 'toplist', 1, $k);
            $toprows[$k] = array('caption' => jieqi_htmlstr($v['caption']), 'url' => $url, 'publish' => intval($v['publish']));
            $jieqiTpl->assign('url_' . $k, $url);
        }
        $jieqiTpl->assign_by_ref('toprows', $toprows);
    }
}