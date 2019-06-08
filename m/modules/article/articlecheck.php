<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_loadlang('article', JIEQI_MODULE_NAME);
header('Content-Type:text/html;charset=' . JIEQI_CHAR_SET);
if (isset($_REQUEST['articlename'])) {
    $_REQUEST['articlename'] = trim($_REQUEST['articlename']);
}
$imageright = sprintf($jieqiLang['article']['article_check_right'], JIEQI_URL);
$imageerror = sprintf($jieqiLang['article']['article_check_error'], JIEQI_URL);
if (isset($_REQUEST['articlename']) && 0 < strlen($_REQUEST['articlename'])) {
    include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
    if (!jieqi_safestring($_REQUEST['articlename'])) {
        exit($imageerror . $jieqiLang['article']['limit_article_title']);
    }
    jieqi_getconfigs('article', 'deny', 'jieqiDeny');
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
    }
    if (!isset($jieqiDeny['article'])) {
        $jieqiDeny['article'] = $jieqiConfigs['system']['postdenywords'];
    }
    if (!empty($jieqiDeny['article']) || !empty($jieqiConfigs['system']['postdenywords'])) {
        include_once JIEQI_ROOT_PATH . '/include/checker.php';
        $checker = new JieqiChecker();
        if (!empty($jieqiDeny['article'])) {
            $matchwords = $checker->deny_words($_REQUEST['articlename'], $jieqiDeny['article'], true, true);
            if (is_array($matchwords)) {
                exit($imageerror . sprintf($jieqiLang['article']['article_deny_articlename'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords))));
            }
        }
        if (!empty($jieqiConfigs['system']['postdenywords'])) {
            $matchwords = $checker->deny_words($_POST['intro'], $jieqiConfigs['system']['postdenywords'], true);
            if (is_array($matchwords)) {
                exit($imageerror . sprintf($jieqiLang['article']['article_deny_intro'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords))));
            }
        }
    }
    include_once $jieqiModules['article']['path'] . '/class/article.php';
    $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
    if ($jieqiConfigs['article']['samearticlename'] != 1) {
        if (0 < $article_handler->getCount(new Criteria('articlename', $_REQUEST['articlename'], '='))) {
            exit($imageerror . sprintf($jieqiLang['article']['articletitle_has_exists'], jieqi_htmlstr($_REQUEST['articlename'])));
        }
    }
    exit($imageright);
} else {
    exit($imageerror . $jieqiLang['article']['need_article_title']);
}