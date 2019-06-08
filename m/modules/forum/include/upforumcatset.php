<?php

if (!defined('JIEQI_ROOT_PATH')) {
    exit;
}
if (!is_object($forumcat_handler)) {
    include_once $jieqiModules['forum']['path'] . '/class/forumcat.php';
    $forumcat_handler = JieqiForumcatHandler::getInstance('JieqiForumcatHandler');
}
$criteria = new CriteriaCompo();
$criteria->setSort('catorder');
$criteria->setOrder('ASC');
$jieqiForumForumcat = array();
$forumcat_handler->queryObjects($criteria);
while ($v = $forumcat_handler->getObject()) {
    $jieqiForumForumcat[] = array('catid' => $v->getVar('catid'), 'cattitle' => $v->getVar('cattitle'), 'catorder' => $v->getVar('catorder'));
}
jieqi_setconfigs('forumcatset', 'jieqiForumForumcat', $jieqiForumForumcat, 'forum');