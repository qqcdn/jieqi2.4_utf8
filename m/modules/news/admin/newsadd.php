<?php

define('JIEQI_MODULE_NAME', 'news');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower[JIEQI_MODULE_NAME]['newsaddback'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs', 'jieqiConfigs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
jieqi_loadlang('news', JIEQI_MODULE_NAME);
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'add':
        jieqi_checkpost();
        $errtext = '';
        $_POST = jieqi_funtoarray('trim', $_POST);
        $_POST['news_sortid'] = intval($_POST['news_sortid']);
        if (strlen($_POST['news_title']) == 0) {
            $errtext .= $jieqiLang['news']['need_news_title'] . '<br />';
        }
        if ($_POST['news_sortid'] == 0) {
            $errtext .= $jieqiLang['news']['need_news_sortid'] . '<br />';
        }
        if (strlen($_POST['news_gourl']) == 0 && strlen($_POST['news_contents']) == 0) {
            $errtext .= $jieqiLang['news']['need_news_body'] . '<br />';
        }
        $cover = 0;
        if (!empty($_FILES['news_cover']['name'])) {
            $cover_postfix = strrchr(trim(strtolower($_FILES['news_cover']['name'])), '.');
            $cover = intval(array_search($cover_postfix, $jieqi_image_type));
            if (!preg_match('/\\.(gif|jpg|jpeg|png|bmp)$/i', $_FILES['news_cover']['name'])) {
                $errtext .= sprintf($jieqiLang['news']['cover_not_image'], $_FILES['news_cover']['name']) . '<br />';
            }
            if (!empty($errtext)) {
                jieqi_delfile($_FILES['news_cover']['tmp_name']);
            }
        }
        if (empty($errtext)) {
            include_once $jieqiModules['news']['path'] . '/class/topic.php';
            $topic_handler = JieqiNewstopicHandler::getInstance('JieqiNewstopicHandler');
            $topic = $topic_handler->create();
            $topic->setVar('posterid', intval($_SESSION['jieqiUserId']));
            $topic->setVar('poster', $_SESSION['jieqiUserName']);
            $topic->setVar('posterip', jieqi_userip());
            $topic->setVar('masterid', 0);
            $topic->setVar('master', '');
            $topic->setVar('masterip', '');
            $addtime = intval(@strtotime($_POST['news_addtime']));
            $topic->setVar('addtime', $addtime);
            $topic->setVar('uptime', JIEQI_NOW_TIME);
            $topic->setVar('sortid', intval($_POST['news_sortid']));
            $topic->setVar('areaid', intval($_POST['news_areaid']));
            $topic->setVar('title', $_POST['news_title']);
            $topic->setVar('subhead', $_POST['news_subhead']);
            include_once JIEQI_ROOT_PATH . '/include/funtag.php';
            $tagary = jieqi_tag_clean($_POST['news_tags']);
            $_POST['news_tags'] = implode(' ', $tagary);
            $topic->setVar('tags', $_POST['news_tags']);
            $topic->setVar('author', $_POST['news_author']);
            $topic->setVar('aurl', $_POST['news_aurl']);
            $topic->setVar('source', $_POST['news_source']);
            $topic->setVar('surl', $_POST['news_surl']);
            $topic->setVar('gourl', $_POST['news_gourl']);
            if (strlen($_POST['news_summary']) == 0) {
                include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
                $_POST['news_summary'] = jieqi_substr(jieqi_textstr(strip_tags($_POST['news_contents'])), 0, 200);
            }
            $topic->setVar('summary', $_POST['news_summary']);
            $topic->setVar('style', $_POST['news_style']);
            $topic->setVar('cover', $cover);
            $attach = 0;
            $topic->setVar('attach', $attach);
            $topic->setVar('review', intval($_POST['news_review']));
            $topic->setVar('vote', intval($_POST['news_vote']));
            $topic->setVar('login', intval($_POST['news_login']));
            $display = 0;
            $topic->setVar('display', $display);
            $topic->setVar('views', 0);
            $topic->setVar('marknum', 0);
            $topic->setVar('topnum', 0);
            $topic->setVar('downnum', 0);
            $topic->setVar('scorenum', 0);
            $topic->setVar('sumscore', 0);
            $topic->setVar('reviews', 0);
            $topic->setVar('replies', 0);
            if (!$topic_handler->insert($topic)) {
                jieqi_printfail($jieqiLang['news']['news_add_failure']);
            } else {
                $topicid = $topic->getVar('topicid', 'n');
                include_once $jieqiModules['news']['path'] . '/class/content.php';
                $content_handler = JieqiNewscontentHandler::getInstance('JieqiNewscontentHandler');
                $content = $content_handler->create();
                $content->setVar('topicid', $topicid);
                $content->setVar('contents', $_POST['news_contents']);
                if (!$content_handler->insert($content)) {
                    jieqi_printfail($jieqiLang['news']['content_add_failure']);
                } else {
                    jieqi_tag_save($tagary, $topicid, array('tag' => jieqi_dbprefix('news_tag'), 'taglink' => jieqi_dbprefix('news_taglink')));
                    if (!empty($_FILES['news_cover']['name'])) {
                        if (empty($jieqiConfigs['news']['coverdir'])) {
                            $jieqiConfigs['news']['coverdir'] = $jieqiConfigs['news']['imagedir'];
                        }
                        $coverdir = jieqi_uploadpath($jieqiConfigs['news']['coverdir'], 'news') . jieqi_getsubdir($topicid) . '/' . $topicid . $cover_postfix;
                        jieqi_checkdir(dirname($coverdir), true);
                        jieqi_copyfile($_FILES['news_cover']['tmp_name'], $coverdir, 511, true);
                    }
                }
            }
            jieqi_jumppage($jieqiModules['news']['url'] . '/admin/newsadd.php', LANG_DO_SUCCESS, $jieqiLang[JIEQI_MODULE_NAME]['news_add_success']);
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['news']));
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules[JIEQI_MODULE_NAME]['path'] . '/templates/admin/newsadd.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}