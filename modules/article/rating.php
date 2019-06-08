<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
if (2.2 <= floatval(JIEQI_VERSION)) {
    $_REQUEST['id'] = isset($_POST['id']) ? $_POST['id'] : 0;
}
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id']) || empty($_REQUEST['score']) || !is_numeric($_REQUEST['score'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_checklogin();
jieqi_loadlang('rate', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if (!$jieqiUsers) {
    jieqi_printfail(LANG_NO_USER);
}
$userset = jieqi_unserialize($jieqiUsers->getVar('setting', 'n'));
$today = date('Y-m-d');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'action', 'jieqiAction');
jieqi_getconfigs('system', 'honors');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'right');
$maxrate = $jieqiConfigs['article']['dayrates'];
$honorid = jieqi_gethonorid($_SESSION['jieqiUserScore'], $jieqiHonors);
if ($honorid && isset($jieqiRight['article']['dayrates']['honors'][$honorid]) && is_numeric($jieqiRight['article']['dayrates']['honors'][$honorid])) {
    $maxrate = intval($jieqiRight['article']['dayrates']['honors'][$honorid]);
}
if (isset($userset['ratedate']) && $userset['ratedate'] == $today && (int) $maxrate <= (int) $userset['ratenum']) {
    jieqi_printfail(sprintf($jieqiLang['article']['rate_times_limit'], $maxrate));
}
$_REQUEST['id'] = intval($_REQUEST['id']);
$_REQUEST['score'] = intval($_REQUEST['score']);
if ($_REQUEST['score'] < 1) {
    $_REQUEST['score'] = 1;
}
if (empty($jieqiConfigs['article']['maxrates']) || !is_numeric($jieqiConfigs['article']['maxrates'])) {
    $jieqiConfigs['article']['maxrates'] = 10;
}
if ($jieqiConfigs['article']['maxrates'] < $_REQUEST['score']) {
    $_REQUEST['score'] = $jieqiConfigs['article']['maxrates'];
}
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article = $article_handler->get($_REQUEST['id']);
if (!$article) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
if (is_numeric($jieqiConfigs['article']['rateminwords']) && $article->getVar('words') < intval($jieqiConfigs['article']['rateminwords'])) {
    jieqi_printfail(sprintf($jieqiLang['article']['rate_min_articlewords'], $jieqiConfigs['article']['rateminwords']));
}
$ratenum = $article->getVar('ratenum', 'n') + 1;
$ratesum = $article->getVar('ratesum', 'n') + $_REQUEST['score'];
$upfield = array('ratenum' => $ratenum, 'ratesum' => $ratesum);
$star = ceil($_REQUEST['score'] * 5 / $jieqiConfigs['article']['maxrates']);
if (in_array($star, array(1, 2, 3, 4, 5))) {
    if ($article->getVar('rate' . $star, 'n') !== false) {
        $upfield['rate' . $star] = $article->getVar('rate' . $star, 'n') + 1;
    }
}
$criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['id']));
$article_handler->updatefields($upfield, $criteria);
$taskmodule = 'article';
$taskname = 'rate';
jieqi_getconfigs('system', 'tasks', 'jieqiTasks');
if (!empty($jieqiTasks[$taskmodule][$taskname]['score']) && empty($_SESSION['jieqiUserSet']['tasks'][$taskmodule][$taskname])) {
    $userset['tasks'][$taskmodule][$taskname] = 1;
    $jieqiUsers->setVar('score', intval($jieqiUsers->getVar('score', 'n')) + intval($jieqiTasks[$taskmodule][$taskname]['score']));
}
if (isset($userset['ratedate']) && $userset['ratedate'] == $today) {
    $userset['ratenum'] = (int) $userset['ratenum'] + 1;
} else {
    $userset['ratedate'] = $today;
    $userset['ratenum'] = 1;
}
$jieqiUsers->setVar('setting', serialize($userset));
$jieqiUsers->saveToSession();
$users_handler->insert($jieqiUsers);
include_once $jieqiModules['article']['path'] . '/include/funaction.php';
$actions = array('actname' => 'rate', 'actnum' => 1);
jieqi_article_actiondo($actions, $article);
jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['rate_success'], $maxrate, $userset['ratenum']));