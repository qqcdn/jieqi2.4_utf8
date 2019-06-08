<?php

function jieqi_article_actiondo($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $article_handler;
    if (is_numeric($article)) {
        if (!isset($article_handler) || !is_a($article_handler, 'JieqiArticleHandler')) {
            include_once $jieqiModules['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
        }
        $article = $article_handler->get(intval($article));
    }
    if (!is_object($article)) {
        return false;
    }
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (empty($actions['actnum'])) {
        $actions['actnum'] = 1;
    }
    if (!isset($actions['tid'])) {
        $actions['tid'] = 0 < $article->getVar('authorid', 'n') ? intval($article->getVar('authorid', 'n')) : intval($article->getVar('posterid', 'n'));
    }
    if (!isset($actions['tname'])) {
        $actions['tname'] = 0 < $article->getVar('authorid', 'n') ? $article->getVar('author', 'n') : $article->getVar('poster', 'n');
    }
    if (!isset($actions['no_earn']) || $actions['no_earn'] == false) {
        jieqi_article_actionearn($actions, $article);
    }
    if (!isset($actions['no_record']) || $actions['no_record'] == false) {
        jieqi_article_actionrecord($actions, $article);
    }
    if (!isset($actions['no_message']) || $actions['no_message'] == false) {
        jieqi_article_actionmessage($actions, $article);
    }
}
function jieqi_article_actionearn($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $article_handler;
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (0 < $jieqiAction['article'][$actions['actname']]['earnscore']) {
        jieqi_article_actionscore($actions, $article);
    }
    if (0 < $jieqiAction['article'][$actions['actname']]['earncredit']) {
        jieqi_article_actioncredit($actions, $article);
    }
    if (isset($jieqiAction['article'][$actions['actname']]['earnvipvote']) && 0 < $jieqiAction['article'][$actions['actname']]['earnvipvote']) {
        jieqi_article_actionvipvote($actions, $article);
    }
}
function jieqi_article_actionrecord($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $article_handler;
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (!empty($jieqiAction['article'][$actions['actname']]['islog'])) {
        jieqi_article_actionlog($actions, $article);
    }
    if (!empty($jieqiAction['article'][$actions['actname']]['isreview'])) {
        jieqi_article_actionreview($actions, $article);
    }
}
function jieqi_article_actionmessage($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $article_handler;
    global $jieqiLang;
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiLang['article']['action'])) {
        jieqi_loadlang('action', 'article');
    }
    if (!empty($jieqiAction['article'][$actions['actname']]['ismessage'])) {
        $authorid = $article->getVar('authorid', 'n');
        $author = $article->getVar('author', 'n');
        $title = isset($actions['message_title']) ? $actions['message_title'] : $jieqiLang['article'][$actions['actname'] . '_message_title'];
        $content = isset($actions['message_content']) ? $actions['message_content'] : $jieqiLang['article'][$actions['actname'] . '_message_content'];
        if (0 < $authorid && 0 < strlen($content)) {
            include_once JIEQI_ROOT_PATH . '/include/funmessage.php';
            jieqi_sendmessage(array('toid' => $authorid, 'toname' => $author, 'title' => $title, 'content' => $content));
        }
    }
}
function jieqi_article_actionscore($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $users_handler;
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['article'][$actions['actname']]['earnscore'])) {
        return false;
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $uid = isset($actions['uid']) ? intval($actions['uid']) : intval($_SESSION['jieqiUserId']);
    if (empty($jieqiAction['article'][$actions['actname']]['paybase'])) {
        $jieqiAction['article'][$actions['actname']]['paybase'] = 1;
    }
    if (0 < $jieqiAction['article'][$actions['actname']]['earnscore']) {
        $earnscore = floor(intval($actions['actnum']) * $jieqiAction['article'][$actions['actname']]['earnscore'] / $jieqiAction['article'][$actions['actname']]['paybase']);
    } else {
        $earnscore = 0;
    }
    if ($earnscore != 0) {
        $users_handler->changeScore($uid, $earnscore, true);
    }
}
function jieqi_article_actioncredit($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['article'][$actions['actname']]['earncredit'])) {
        return false;
    }
    $earncredit = 0;
    if (empty($jieqiAction['article'][$actions['actname']]['paybase'])) {
        $jieqiAction['article'][$actions['actname']]['paybase'] = 1;
    }
    if (0 < $jieqiAction['article'][$actions['actname']]['earncredit']) {
        $earncredit = floor(intval($actions['actnum']) * $jieqiAction['article'][$actions['actname']]['earncredit'] / $jieqiAction['article'][$actions['actname']]['paybase']);
    }
    if ($earncredit <= 0) {
        return false;
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $uid = isset($actions['uid']) ? intval($actions['uid']) : intval($_SESSION['jieqiUserId']);
    $uname = isset($actions['uname']) ? $actions['uname'] : $_SESSION['jieqiUserName'];
    $articleid = intval($article->getVar('articleid', 'n'));
    $articlename = $article->getVar('articlename', 'n');
    $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_credit') . ' WHERE uid = ' . $uid . ' AND articleid = ' . $articleid . ' LIMIT 0, 1';
    $ret = $query->execute($sql);
    $row = $query->getRow();
    if (is_array($row)) {
        $vars = jieqi_unserialize($row['vars']);
        $vars[$actions['actname']] = isset($vars[$actions['actname']]) ? $vars[$actions['actname']] + $earncredit : $earncredit;
        $sql = 'UPDATE ' . jieqi_dbprefix('article_credit') . ' SET point = point + ' . $earncredit;
        if (!empty($actions['actegold'])) {
            $sql .= ', payegold = payegold + ' . intval($actions['actegold']);
            if (!empty($actions['actbuy'])) {
                $sql .= ', buyegold = buyegold + ' . intval($actions['actegold']);
            }
        }
        $sql .= ', upnum = upnum + 1, uptime = ' . intval(JIEQI_NOW_TIME) . ', vars = \'' . jieqi_dbslashes(serialize($vars)) . '\' WHERE creditid = ' . intval($row['creditid']);
        $ret = $query->execute($sql);
    } else {
        $fieldrows = array();
        $fieldrows['articleid'] = $articleid;
        $fieldrows['articlename'] = $articlename;
        $fieldrows['uid'] = $uid;
        $fieldrows['uname'] = $uname;
        $fieldrows['point'] = $earncredit;
        $fieldrows['payegold'] = 0;
        $fieldrows['buyegold'] = 0;
        if (!empty($actions['actegold'])) {
            $fieldrows['payegold'] = intval($actions['actegold']);
            if (!empty($actions['actbuy'])) {
                $fieldrows['buyegold'] = intval($actions['actegold']);
            }
        }
        $fieldrows['upnum'] = 1;
        $fieldrows['uptime'] = JIEQI_NOW_TIME;
        $vars = array();
        $vars[$actions['actname']] = $earncredit;
        $fieldrows['vars'] = serialize($vars);
        $sql = $query->makeupsql(jieqi_dbprefix('article_credit'), $fieldrows, 'INSERT');
        $ret = $query->execute($sql);
    }
    return $ret;
}
function jieqi_article_actionvipvote($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $users_handler;
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['article'][$actions['actname']]['earnvipvote'])) {
        return false;
    }
    if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/users.php';
        $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
    }
    $uid = isset($actions['uid']) ? intval($actions['uid']) : intval($_SESSION['jieqiUserId']);
    if (empty($jieqiAction['article'][$actions['actname']]['paybase'])) {
        $jieqiAction['article'][$actions['actname']]['paybase'] = 1;
    }
    if (0 < $jieqiAction['article'][$actions['actname']]['earnvipvote']) {
        $earnvipvote = floor(intval($actions['actnum']) * $jieqiAction['article'][$actions['actname']]['earnvipvote'] / $jieqiAction['article'][$actions['actname']]['paybase']);
    } else {
        $earnvipvote = 0;
    }
    if (0 < $earnvipvote) {
        $user = $users_handler->get($uid);
        if (is_object($user)) {
            $userset = jieqi_unserialize($user->getVar('setting', 'n'));
            $userset['gift']['vipvote'] = intval($userset['gift']['vipvote']) + $earnvipvote;
            $user->setVar('setting', serialize($userset));
            if (!empty($_SESSION['jieqiUserId']) && $uid == $_SESSION['jieqiUserId']) {
                $user->saveToSession();
            }
            $users_handler->insert($user);
        }
    }
}
function jieqi_article_actionlog($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['article'][$actions['actname']]['islog'])) {
        return false;
    }
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $uid = isset($actions['uid']) ? intval($actions['uid']) : intval($_SESSION['jieqiUserId']);
    $uname = isset($actions['uname']) ? $actions['uname'] : $_SESSION['jieqiUserName'];
    $articleid = intval($article->getVar('articleid', 'n'));
    $articlename = $article->getVar('articlename', 'n');
    $fieldrows = array();
    $fieldrows['articleid'] = $articleid;
    $fieldrows['articlename'] = $articlename;
    $fieldrows['uid'] = $uid;
    $fieldrows['uname'] = $uname;
    $fieldrows['tid'] = isset($actions['tid']) ? intval($actions['tid']) : 0;
    $fieldrows['tname'] = isset($actions['tname']) ? $actions['tname'] : '';
    $fieldrows['linkid'] = isset($actions['linkid']) ? intval($actions['linkid']) : 0;
    $fieldrows['acttype'] = isset($actions['acttype']) ? intval($actions['acttype']) : 0;
    $fieldrows['addtime'] = JIEQI_NOW_TIME;
    $fieldrows['actname'] = $actions['actname'];
    $fieldrows['actnum'] = intval($actions['actnum']);
    $fieldrows['islog'] = empty($jieqiAction['article'][$actions['actname']]['islog']) ? 0 : intval($jieqiAction['article'][$actions['actname']]['islog']);
    $fieldrows['isvip'] = empty($jieqiAction['article'][$actions['actname']]['isvip']) ? 0 : intval($jieqiAction['article'][$actions['actname']]['isvip']);
    if (empty($jieqiAction['article'][$actions['actname']]['paybase'])) {
        $jieqiAction['article'][$actions['actname']]['paybase'] = 1;
    }
    if (0 < $jieqiAction['article'][$actions['actname']]['earncredit']) {
        $earncredit = floor(intval($actions['actnum']) * $jieqiAction['article'][$actions['actname']]['earncredit'] / $jieqiAction['article'][$actions['actname']]['paybase']);
    } else {
        $earncredit = 0;
    }
    $fieldrows['credit'] = $earncredit;
    if (0 < $jieqiAction['article'][$actions['actname']]['earnscore']) {
        $earnscore = floor(intval($actions['actnum']) * $jieqiAction['article'][$actions['actname']]['earnscore'] / $jieqiAction['article'][$actions['actname']]['paybase']);
    } else {
        $earnscore = 0;
    }
    $fieldrows['score'] = $earnscore;
    $fieldrows['egold'] = empty($actions['actegold']) ? 0 : intval($actions['actegold']);
    if (isset($_SESSION['jieqiUserChannel'])) {
        $fieldrows['channel'] = $_SESSION['jieqiUserChannel'];
    }
    if (defined('JIEQI_DEVICE_FOR')) {
        $fieldrows['device'] = JIEQI_DEVICE_FOR;
    }
    $sql = $query->makeupsql(jieqi_dbprefix('article_actlog'), $fieldrows, 'INSERT');
    $ret = $query->execute($sql);
    if ($actions['actname'] == 'tip') {
        $setting = jieqi_unserialize($article->getVar('setting', 'n'));
        if (!isset($setting['tipinfo'])) {
            $setting['tipinfo'] = array();
        }
        if (isset($setting['tipinfo'][$actions['acttype']])) {
            $setting['tipinfo'][$actions['acttype']] += $actions['actnum'];
        } else {
            $setting['tipinfo'][$actions['acttype']] = $actions['actnum'];
        }
        $sql = 'UPDATE ' . jieqi_dbprefix('article_article') . ' SET setting = \'' . jieqi_dbslashes(serialize($setting)) . '\' WHERE articleid = ' . intval($article->getVar('articleid', 'n'));
        $ret = $query->execute($sql);
    }
    return $ret;
}
function jieqi_article_autoreview($articleid, $title, $content)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    include_once JIEQI_ROOT_PATH . '/include/funpost.php';
    $check_errors = array();
    $post_set = array('module' => 'article', 'ownerid' => intval($articleid), 'topicid' => 0, 'postid' => 0, 'posttime' => JIEQI_NOW_TIME, 'topictitle' => $title, 'posttext' => $content, 'attachment' => '', 'emptytitle' => true, 'isnew' => true, 'istopic' => 1, 'istop' => 0, 'sname' => 'jieqiArticleReviewTime', 'attachfile' => '', 'oldattach' => '', 'checkcode' => false, 'autopost' => 1);
    include_once $jieqiModules['article']['path'] . '/class/reviews.php';
    $reviews_handler = JieqiReviewsHandler::getInstance('JieqiReviewsHandler');
    $newReview = $reviews_handler->create();
    jieqi_topic_newset($post_set, $newReview);
    $reviews_handler->insert($newReview);
    $post_set['topicid'] = $newReview->getVar('topicid', 'n');
    include_once $jieqiModules['article']['path'] . '/class/replies.php';
    $replies_handler = JieqiRepliesHandler::getInstance('JieqiRepliesHandler');
    $newReply = $replies_handler->create();
    jieqi_post_newset($post_set, $newReply);
    $replies_handler->insert($newReply);
    return true;
}
function jieqi_article_actionreview($actions, $article)
{
    global $jieqiModules;
    global $jieqiAction;
    global $query;
    global $jieqiLang;
    if (empty($jieqiLang['article']['action'])) {
        jieqi_loadlang('action', 'article');
    }
    if (!isset($jieqiAction['article'])) {
        jieqi_getconfigs('article', 'action', 'jieqiAction');
    }
    if (!isset($jieqiAction['article'][$actions['actname']])) {
        return false;
    }
    if (empty($jieqiAction['article'][$actions['actname']]['isreview'])) {
        return false;
    }
    $articleid = $article->getVar('articleid', 'n');
    $title = isset($actions['review_title']) ? $actions['review_title'] : $jieqiLang['article'][$actions['actname'] . '_review_title'];
    $content = isset($actions['review_content']) ? $actions['review_content'] : $jieqiLang['article'][$actions['actname'] . '_review_content'];
    if (0 < strlen($content)) {
        return jieqi_article_autoreview($articleid, $title, $content);
    } else {
        return true;
    }
}