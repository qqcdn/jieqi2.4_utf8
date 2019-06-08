<?php

class BlockArticleSearch extends JieqiBlock
{
    public $module = 'article';
    public $template = 'block_search.html';
    public function setContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiConfigs;
        jieqi_getconfigs('article', 'configs');
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $GLOBALS['jieqiModules']['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $jieqiTpl->assign('url_articlesearch', $article_dynamic_url . '/search.php');
    }
}