<?php

function jieqi_topic_vars($topic, $enableubb = true)
{
    global $jieqiTxtcvt;
    global $jieqiHonors;
    global $jieqiGroups;
    global $jieqiModules;
    if (!isset($jieqiHonors)) {
        jieqi_getconfigs('system', 'honors', 'jieqiHonors');
    }
    if (!defined('JIEQI_SHOW_BADGE')) {
        if (!empty($jieqiModules['badge']['publish']) && is_file($GLOBALS['jieqiModules']['badge']['path'] . '/include/badgefunction.php')) {
            include_once $jieqiModules['badge']['path'] . '/include/badgefunction.php';
            define('JIEQI_SHOW_BADGE', 1);
        } else {
            define('JIEQI_SHOW_BADGE', 0);
        }
    }
    $ret = jieqi_query_rowvars($topic);
    $ret['content'] = $topic->getVar('content', 'n');
    if ($ret['content'] === false) {
        $ret['content'] = $topic->getVar('posttext', 'n');
    }
    if ($ret['content'] !== false) {
        if ($enableubb) {
            if (!is_object($jieqiTxtcvt)) {
                include_once JIEQI_ROOT_PATH . '/lib/text/textconvert.php';
                $jieqiTxtcvt = TextConvert::getInstance('TextConvert');
            }
            $ret['content'] = $jieqiTxtcvt->displayTarea($ret['content'], 0, 1, 1, 1, 1);
        } else {
            if (!is_object($jieqiTxtcvt)) {
                include_once JIEQI_ROOT_PATH . '/lib/text/textconvert.php';
                $jieqiTxtcvt = TextConvert::getInstance('TextConvert');
            }
            $ret['content'] = jieqi_htmlstr(preg_replace(array('/\\[\\/?(code|url|color|font|align|email|b|i|u|d|img|quote|size)[^\\[\\]]*\\]/is'), '', $ret['content']));
            $ret['content'] = $jieqiTxtcvt->smile(preg_replace('/https?:\\/\\/[^\\s\\r\\n\\t\\f<>]+/i', '<a href="\\0">\\0</a>', $ret['content']));
        }
    }
    $ret['posttext'] =& $ret['content'];
    $tmpary = jieqi_unserialize($topic->getVar('lastinfo', 'n'));
    if (is_array($tmpary)) {
        $ret['replyflag'] = 1;
        if (empty($ret['replierid'])) {
            $ret['replierid'] = $tmpary['uid'];
        }
        if (strlen($ret['replier']) == 0) {
            $ret['replier'] = jieqi_htmlstr($tmpary['uname']);
        }
        if (empty($ret['replytime'])) {
            $ret['replytime'] = $tmpary['time'];
        }
    }
    if (!empty($ret['uid'])) {
        $ret['userid'] = $ret['uid'];
        $ret['useruname'] = $topic->getVar('uname');
        $ret['username'] = $topic->getVar('name') == '' ? $topic->getVar('uname') : $topic->getVar('name');
        $ret['groupname'] = $jieqiGroups[$topic->getVar('groupid')];
        $honorid = intval(jieqi_gethonorid($topic->getVar('score'), $jieqiHonors));
        $ret['honor'] = isset($jieqiHonors[$honorid]['name'][intval($topic->getVar('workid', 'n'))]) ? $jieqiHonors[$honorid]['name'][intval($topic->getVar('workid', 'n'))] : $jieqiHonors[$honorid]['caption'];
        if (0 < $ret['avatar']) {
            $tmpary = jieqi_geturl('system', 'avatar', $ret['userid'], 'a', $ret['avatar']);
            $ret['base_avatar'] = $tmpary['d'];
            $ret['url_avatar'] = $tmpary['l'];
            $ret['url_avatars'] = $tmpary['s'];
            $ret['url_avatari'] = $tmpary['i'];
        }
        if (JIEQI_SHOW_BADGE == 1) {
            $checkfile = false;
            $ret['groupurl'] = getbadgeurl(1, $topic->getVar('groupid'), 0, $checkfile);
            $ret['honorurl'] = getbadgeurl(2, $honorid, 0, $checkfile);
            $badgeary = jieqi_unserialize($topic->getVar('badges', 'n'));
            $ret['badgerows'] = array();
            if (is_array($badgeary)) {
                $m = 0;
                foreach ($badgeary as $badge) {
                    $ret['badgerows'][$m]['imageurl'] = getbadgeurl($badge['btypeid'], $badge['linkid'], $badge['imagetype']);
                    $ret['badgerows'][$m]['caption'] = jieqi_htmlstr($badge['caption']);
                    $m++;
                }
            }
        }
    }
    return $ret;
}
function jieqi_topic_addviews($tid, $table)
{
    global $query;
    include_once JIEQI_ROOT_PATH . '/include/funstat.php';
    return jieqi_visit_stat($tid, $table, 'views', 'topicid', $query);
}
function jieqi_post_vars($post, $configs = array(), $addvars = array(), $enableubb = true)
{
    global $jieqiTxtcvt;
    global $jieqiHonors;
    global $jieqiGroups;
    global $jieqiModules;
    if (!isset($jieqiHonors)) {
        jieqi_getconfigs('system', 'honors', 'jieqiHonors');
    }
    if (!defined('JIEQI_SHOW_BADGE')) {
        if (!empty($jieqiModules['badge']['publish']) && is_file($GLOBALS['jieqiModules']['badge']['path'] . '/include/badgefunction.php')) {
            include_once $jieqiModules['badge']['path'] . '/include/badgefunction.php';
            define('JIEQI_SHOW_BADGE', 1);
        } else {
            define('JIEQI_SHOW_BADGE', 0);
        }
    }
    $ret = jieqi_query_rowvars($post);
    $ret['userid'] = intval($post->getVar('uid'));
    if (is_array($addvars)) {
        $ret = array_merge($ret, $addvars);
    }
    $ret['attachimages'] = array();
    $ret['attachfiles'] = array();
    $tmpvar = $post->getVar('attachment', 'n');
    if (!empty($tmpvar)) {
        $attachs = jieqi_unserialize($tmpvar);
        foreach ($attachs as $key => $val) {
            $url = jieqi_uploadurl($configs['attachdir'], $configs['attachurl'], JIEQI_MODULE_NAME) . '/' . date('Ymd', $post->getVar('posttime', 'n')) . '/' . $post->getVar('postid', 'n') . '_' . $val['attachid'] . '.' . $val['postfix'];
            if ($val['class'] == 'image') {
                $ret['attachimages'][] = array('id' => $val['attachid'], 'name' => jieqi_htmlstr($val['name']), 'url' => $url, 'posttime' => $post->getVar('posttime', 'n'), 'postid' => $post->getVar('postid', 'n'), 'postfix' => $val['postfix'], 'class' => $val['class'], 'size' => $val['size'], 'size_k' => ceil($val['size'] / 1024));
            } else {
                $ret['attachfiles'][] = array('id' => $val['attachid'], 'name' => jieqi_htmlstr($val['name']), 'url' => $url, 'posttime' => $post->getVar('posttime', 'n'), 'postid' => $post->getVar('postid', 'n'), 'postfix' => $val['postfix'], 'class' => $val['class'], 'size' => $val['size'], 'size_k' => ceil($val['size'] / 1024));
            }
        }
    }
    if ($enableubb) {
        if (!is_object($jieqiTxtcvt)) {
            include_once JIEQI_ROOT_PATH . '/lib/text/textconvert.php';
            $jieqiTxtcvt = TextConvert::getInstance('TextConvert');
        }
        $ret['posttext'] = $jieqiTxtcvt->displayTarea($post->getVar('posttext', 'n'), 0, 1, 1, 1, 1);
    } else {
        if (!is_object($jieqiTxtcvt)) {
            include_once JIEQI_ROOT_PATH . '/lib/text/textconvert.php';
            $jieqiTxtcvt = TextConvert::getInstance('TextConvert');
        }
        $ret['posttext'] = jieqi_htmlstr(preg_replace(array('/\\[\\/?(code|url|color|font|align|email|b|i|u|d|img|quote|size)[^\\[\\]]*\\]/is'), '', $post->getVar('posttext', 'n')));
        $ret['posttext'] = $jieqiTxtcvt->smile(preg_replace('/https?:\\/\\/[^\\s\\r\\n\\t\\f<>]+/i', '<a href="\\0">\\0</a>', $ret['posttext']));
    }
    if (!empty($configs['textwatermark'])) {
        $contentary = explode('<br />' . "\r\n" . '<br />', $ret['posttext']);
        $ret['posttext'] = '';
        foreach ($contentary as $v) {
            if (empty($ret['posttext'])) {
                $ret['posttext'] .= $v;
            } else {
                srand((double) microtime() * 1000000);
                $randstr = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randlen = rand(10, 20);
                $randtext = '';
                $l = strlen($randstr) - 1;
                for ($i = 0; $i < $randlen; $i++) {
                    $num = rand(0, $l);
                    $randtext .= $randstr[$num];
                }
                $textwatermark = str_replace('<{$randtext}>', $randtext, $configs['textwatermark']);
                $ret['posttext'] .= '<br />' . "\r\n" . '' . $textwatermark . $v;
            }
        }
    }
    if (0 < $ret['userid']) {
        $ret['useruname'] = $post->getVar('uname');
        $ret['username'] = $post->getVar('name') == '' ? $post->getVar('uname') : $post->getVar('name');
        $ret['groupname'] = $jieqiGroups[$post->getVar('groupid')];
        $honorid = intval(jieqi_gethonorid($post->getVar('score'), $jieqiHonors));
        $ret['honor'] = isset($jieqiHonors[$honorid]['name'][intval($post->getVar('workid', 'n'))]) ? $jieqiHonors[$honorid]['name'][intval($post->getVar('workid', 'n'))] : $jieqiHonors[$honorid]['caption'];
        if (0 < $ret['avatar']) {
            $tmpary = jieqi_geturl('system', 'avatar', $ret['userid'], 'a', $ret['avatar']);
            $ret['base_avatar'] = $tmpary['d'];
            $ret['url_avatar'] = $tmpary['l'];
            $ret['url_avatars'] = $tmpary['s'];
            $ret['url_avatari'] = $tmpary['i'];
        }
        if (JIEQI_SHOW_BADGE == 1) {
            $checkfile = JIEQI_LOCAL_URL == JIEQI_MAIN_URL ? true : false;
            $checkfile = false;
            $ret['groupurl'] = getbadgeurl(1, $post->getVar('groupid'), 0, $checkfile);
            $ret['honorurl'] = getbadgeurl(2, $honorid, 0, $checkfile);
            $badgeary = jieqi_unserialize($post->getVar('badges', 'n'));
            $ret['badgerows'] = array();
            if (is_array($badgeary)) {
                $m = 0;
                foreach ($badgeary as $badge) {
                    $ret['badgerows'][$m]['imageurl'] = getbadgeurl($badge['btypeid'], $badge['linkid'], $badge['imagetype']);
                    $ret['badgerows'][$m]['caption'] = jieqi_htmlstr($badge['caption']);
                    $m++;
                }
            }
        }
    }
    return $ret;
}
function jieqi_post_checkpre($configs, &$check_errors)
{
    global $jieqiLang;
    global $jieqiConfigs;
    if (!isset($jieqiLang['system']['post'])) {
        jieqi_loadlang('post', 'system');
    }
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!is_array($check_errors)) {
        $check_errors = array();
    }
    $num_errors = count($check_errors);
    include_once JIEQI_ROOT_PATH . '/include/checker.php';
    $checker = new JieqiChecker();
    if (!empty($jieqiConfigs['system']['postdenytimes'])) {
        if (!$checker->deny_time($jieqiConfigs['system']['postdenytimes'])) {
            $check_errors[] = sprintf($jieqiLang['system']['post_deny_times'], jieqi_htmlstr($jieqiConfigs['system']['postdenytimes']));
        }
    }
    if (!empty($jieqiConfigs['system']['postintervaltime']) && !empty($post_set['isnew'])) {
        if (!$checker->interval_time($jieqiConfigs['system']['postintervaltime'], $post_set['sname'], 'jieqiVisitTime')) {
            $check_errors[] = sprintf($jieqiLang['system']['post_time_limit'], $jieqiConfigs['system']['postintervaltime']);
        }
    }
    return $num_errors < count($check_errors) ? false : true;
}
function jieqi_post_checkvar(&$post_set, $configs, &$check_errors)
{
    global $jieqiLang;
    global $jieqiConfigs;
    if (!isset($jieqiLang['system']['post'])) {
        jieqi_loadlang('post', 'system');
    }
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (!is_array($check_errors)) {
        $check_errors = array();
    }
    $num_errors = count($check_errors);
    include_once JIEQI_ROOT_PATH . '/include/checker.php';
    $checker = new JieqiChecker();
    if (isset($jieqiConfigs['system']['posttitlemax'])) {
        $jieqiConfigs['system']['posttitlemax'] = intval($jieqiConfigs['system']['posttitlemax']);
    }
    if (empty($jieqiConfigs['system']['posttitlemax']) || $jieqiConfigs['system']['posttitlemax'] <= 10) {
        $jieqiConfigs['system']['posttitlemax'] = 60;
    }
    $post_set['topictitle'] = jieqi_substr(trim($post_set['topictitle']), 0, $jieqiConfigs['system']['posttitlemax'], '...');
    if (!empty($jieqiConfigs['system']['postdenytimes'])) {
        if (!$checker->deny_time($jieqiConfigs['system']['postdenytimes'])) {
            $check_errors[] = sprintf($jieqiLang['system']['post_deny_times'], jieqi_htmlstr($jieqiConfigs['system']['postdenytimes']));
        }
    }
    if (!empty($jieqiConfigs['system']['postminexperience'])) {
        if (!isset($_SESSION['jieqiUserExperience']) || intval($_SESSION['jieqiUserExperience']) < intval($jieqiConfigs['system']['postminexperience'])) {
            $check_errors[] = sprintf($jieqiLang['system']['post_min_experience'], jieqi_htmlstr($jieqiConfigs['system']['postminexperience']));
        }
    }
    if (!empty($jieqiConfigs['system']['postemailverify'])) {
        if (empty($_SESSION['jieqiUserVerify']['email'])) {
            $check_errors[] = $jieqiLang['system']['post_email_verify'];
        }
    }
    if (0 < $jieqiConfigs['system']['postcheckcode']) {
        if (empty($post_set['checkcode']) || empty($_SESSION['jieqiCheckCode']) || strtolower($post_set['checkcode']) != strtolower($_SESSION['jieqiCheckCode'])) {
            $check_errors[] = $jieqiLang['system']['post_checkcode_error'];
        }
    }
    if (!empty($jieqiConfigs['system']['postdenywords'])) {
        $matchwords1 = $checker->deny_words($post_set['topictitle'], $jieqiConfigs['system']['postdenywords'], true);
        $matchwords2 = $checker->deny_words($post_set['posttext'], $jieqiConfigs['system']['postdenywords'], true);
        if (is_array($matchwords1) || is_array($matchwords2)) {
            $matchwords = array();
            if (is_array($matchwords1)) {
                $matchwords = array_merge($matchwords, $matchwords1);
            }
            if (is_array($matchwords2)) {
                $matchwords = array_merge($matchwords, $matchwords2);
            }
            $check_errors[] = sprintf($jieqiLang['system']['post_words_deny'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
        }
    }
    if (!empty($jieqiConfigs['system']['postdenyrubbish'])) {
        if (!$checker->deny_rubbish($post_set['posttext'], $jieqiConfigs['system']['postdenyrubbish'])) {
            $check_errors[] = $jieqiLang['system']['post_words_water'];
        }
    }
    if (!empty($post_set['istopic']) && $checker->is_required($post_set['topictitle']) == false) {
        if ($post_set['emptytitle']) {
            $post_set['topictitle'] = jieqi_substr(str_replace(array("\r", "\n", '	'), '', preg_replace('/\\[[^\\[\\]]+\\]([^\\[\\]]*)\\[\\/[^\\[\\]]+\\]/isU', '\\1', $post_set['posttext'])), 0, 60);
            if (strlen($post_set['emptytitle']) == 0) {
                $post_set['emptytitle'] = '--';
            }
        } else {
            $check_errors[] = $jieqiLang['system']['post_need_title'];
        }
    }
    if (!$checker->is_required($post_set['posttext'])) {
        $check_errors[] = $jieqiLang['system']['post_need_content'];
    }
    if (!empty($jieqiConfigs['system']['postminsize']) && !$checker->str_min($post_set['posttext'], $jieqiConfigs['system']['postminsize'])) {
        $check_errors[] = sprintf($jieqiLang['system']['post_min_content'], $jieqiConfigs['system']['postminsize']);
    }
    if (!empty($jieqiConfigs['system']['postmaxsize']) && !$checker->str_max($post_set['posttext'], $jieqiConfigs['system']['postmaxsize'])) {
        $check_errors[] = sprintf($jieqiLang['system']['post_max_content'], $jieqiConfigs['system']['postmaxsize']);
    }
    if (isset($jieqiConfigs['system']['postreplacewords']) && !empty($jieqiConfigs['system']['postreplacewords'])) {
        $checker->replace_words($post_set['topictitle'], $jieqiConfigs['system']['postreplacewords']);
        $checker->replace_words($post_set['posttext'], $jieqiConfigs['system']['postreplacewords']);
    }
    if (isset($jieqiConfigs['system']['authtypeset']) && $jieqiConfigs['system']['authtypeset'] == 2 || $post_set['typeset'] == 1 && (!isset($jieqiConfigs['system']['authtypeset']) || $jieqiConfigs['system']['authtypeset'] == 1)) {
        include_once JIEQI_ROOT_PATH . '/lib/text/texttypeset.php';
        $texttypeset = new TextTypeset();
        $post_set['posttext'] = $texttypeset->doTypeset($post_set['posttext']);
    }
    if (count($check_errors) <= $num_errors) {
        if (!empty($jieqiConfigs['system']['postintervaltime']) && !empty($post_set['isnew'])) {
            if (!$checker->interval_time($jieqiConfigs['system']['postintervaltime'], $post_set['sname'], 'jieqiVisitTime')) {
                $check_errors[] = sprintf($jieqiLang['system']['post_time_limit'], $jieqiConfigs['system']['postintervaltime']);
            }
        }
    }
    return $num_errors < count($check_errors) ? false : true;
}
function jieqi_post_checkattach(&$post_set, $configs, &$check_errors, &$attachary)
{
    global $jieqiLang;
    if (!isset($jieqiLang['system']['post'])) {
        jieqi_loadlang('post', 'system');
    }
    $attachary = array();
    $attachnum = 0;
    if (!is_array($check_errors)) {
        $check_errors = array();
    }
    $num_errors = count($check_errors);
    if (is_numeric($configs['maxattachnum']) && 0 < $configs['maxattachnum'] && empty($check_errors)) {
        $maxfilenum = intval($configs['maxattachnum']);
        $typeary = explode(' ', trim($configs['attachtype']));
        if (!empty($post_set['attachfile']['name'])) {
            foreach ($post_set['attachfile']['name'] as $k => $v) {
                if (!empty($v)) {
                    $tmpary = explode('.', $v);
                    $tmpint = count($tmpary) - 1;
                    $tmpary[$tmpint] = strtolower(trim($tmpary[$tmpint]));
                    $denyary = array('htm', 'html', 'shtml', 'php', 'asp', 'aspx', 'jsp', 'pl', 'cgi');
                    if (empty($tmpary[$tmpint]) || !in_array($tmpary[$tmpint], $typeary)) {
                        $check_errors[] = sprintf($jieqiLang['system']['post_uptype_error'], $v);
                    } else {
                        if (in_array($tmpary[$tmpint], $denyary)) {
                            $check_errors[] = sprintf($jieqiLang['system']['post_uptype_safe'], $tmpary[$tmpint]);
                        }
                    }
                    if (preg_match('/\\.(gif|jpg|jpeg|png|bmp)$/i', $v)) {
                        $fclass = 'image';
                        if (intval($configs['maximagesize']) * 1024 < $post_set['attachfile']['size'][$k]) {
                            $check_errors[] = sprintf($jieqiLang['system']['post_upsize_over'], $v, intval($configs['maximagesize']));
                        }
                    } else {
                        $fclass = 'file';
                        if (intval($configs['maxfilesize']) * 1024 < $post_set['attachfile']['size'][$k]) {
                            $check_errors[] = sprintf($jieqiLang['system']['post_upsize_over'], $v, intval($configs['maxfilesize']));
                        }
                    }
                    $attachary[$attachnum] = array('name' => $v, 'class' => $fclass, 'postfix' => $tmpary[$tmpint], 'size' => $post_set['attachfile']['size'][$k], 'order' => $k);
                    $attachnum++;
                }
            }
        }
    }
    if (!empty($check_errors) && !empty($post_set['attachfile']['name'])) {
        foreach ($post_set['attachfile']['name'] as $k => $v) {
            jieqi_delfile($post_set['attachfile']['tmp_name'][$k]);
        }
    }
    return $num_errors < count($check_errors) ? false : true;
}
function jieqi_topic_newset(&$post_set, &$newTopic)
{
    global $jieqiConfigs;
    global $jieqiPower;
    global $jieqiUsersStatus;
    global $jieqiUsersGroup;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (empty($_SESSION['jieqiUserId'])) {
        $tmpuid = 0;
        $tmpuname = '';
    } else {
        $tmpuid = $_SESSION['jieqiUserId'];
        $tmpuname = $_SESSION['jieqiUserName'];
    }
    $newTopic->setVar('siteid', JIEQI_SITE_ID);
    $newTopic->setVar('ownerid', $post_set['ownerid']);
    if (isset($post_set['ownername'])) {
        $newTopic->setVar('ownername', $post_set['ownername']);
    }
    if (isset($post_set['ownercode'])) {
        $newTopic->setVar('ownercode', $post_set['ownercode']);
    }
    if (isset($post_set['targetid'])) {
        $newTopic->setVar('targetid', $post_set['targetid']);
    }
    if (isset($post_set['targetname'])) {
        $newTopic->setVar('targetname', $post_set['targetname']);
    }
    if (isset($post_set['targetflag'])) {
        $newTopic->setVar('targetflag', $post_set['targetflag']);
    }
    $newTopic->setVar('title', $post_set['topictitle']);
    $newTopic->setVar('content', $post_set['posttext']);
    $newTopic->setVar('posterid', $tmpuid);
    $newTopic->setVar('poster', $tmpuname);
    $newTopic->setVar('posttime', JIEQI_NOW_TIME);
    $newTopic->setVar('replierid', 0);
    $newTopic->setVar('replier', $tmpuname);
    $newTopic->setVar('replytime', JIEQI_NOW_TIME);
    $newTopic->setVar('views', 0);
    $newTopic->setVar('replies', 0);
    $newTopic->setVar('islock', 0);
    if (isset($post_set['istop'])) {
        $newTopic->setVar('istop', intval($post_set['istop']));
    } else {
        $newTopic->setVar('istop', 0);
    }
    $newTopic->setVar('isgood', 0);
    $newTopic->setVar('rate', 0);
    $newTopic->setVar('attachment', 0);
    $newTopic->setVar('needperm', 0);
    $newTopic->setVar('needscore', 0);
    $newTopic->setVar('needexp', 0);
    $newTopic->setVar('needprice', 0);
    $newTopic->setVar('sortid', 0);
    $newTopic->setVar('iconid', 0);
    $newTopic->setVar('typeid', 0);
    $newTopic->setVar('linkurl', '');
    $newTopic->setVar('size', strlen($post_set['posttext']));
    if (!empty($jieqiConfigs['system']['postaudittimes'])) {
        if (!isset($jieqiPower['system'])) {
            jieqi_getconfigs('system', 'power');
        }
        if (!empty($post_set['autopost']) || jieqi_checkpower($jieqiPower['system']['postnoaudit'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
            $newTopic->setVar('display', 0);
        } else {
            include_once JIEQI_ROOT_PATH . '/include/checker.php';
            $checker = new JieqiChecker();
            if ($checker->deny_time($jieqiConfigs['system']['postaudittimes'])) {
                $newTopic->setVar('display', 0);
            } else {
                $newTopic->setVar('display', 1);
            }
        }
    } else {
        $newTopic->setVar('display', 0);
    }
    $lastinfo = serialize(array('time' => JIEQI_NOW_TIME, 'uid' => $tmpuid, 'uname' => $tmpuname));
    $newTopic->setVar('lastinfo', $lastinfo);
}
function jieqi_post_attachdb(&$post_set, &$attachary, &$attachs_handler)
{
    foreach ($attachary as $k => $v) {
        $newAttach = $attachs_handler->create();
        $newAttach->setVar('siteid', JIEQI_SITE_ID);
        $newAttach->setVar('topicid', $post_set['topicid']);
        if (isset($post_set['postid'])) {
            $newAttach->setVar('postid', $post_set['postid']);
        } else {
            $newAttach->setVar('postid', 0);
        }
        $newAttach->setVar('name', $attachary[$k]['name']);
        $newAttach->setVar('description', '');
        $newAttach->setVar('class', $attachary[$k]['class']);
        $newAttach->setVar('postfix', $attachary[$k]['postfix']);
        $newAttach->setVar('size', $attachary[$k]['size']);
        $newAttach->setVar('hits', 0);
        $newAttach->setVar('needperm', 0);
        $newAttach->setVar('needscore', 0);
        $newAttach->setVar('needexp', 0);
        $newAttach->setVar('needprice', 0);
        if (isset($post_set['posttime'])) {
            $newAttach->setVar('uptime', $post_set['posttime']);
        } else {
            $newAttach->setVar('uptime', JIEQI_NOW_TIME);
        }
        $newAttach->setVar('uid', intval($_SESSION['jieqiUserId']));
        $newAttach->setVar('remote', 0);
        if ($attachs_handler->insert($newAttach)) {
            $attachid = $newAttach->getVar('attachid');
            $attachary[$k]['attachid'] = $attachid;
        } else {
            $attachary[$k]['attachid'] = 0;
        }
    }
}
function jieqi_post_attachfile(&$post_set, &$attachary, $configs)
{
    $make_image_water = false;
    if (function_exists('gd_info') && 0 < $configs['attachwater'] && JIEQI_MODULE_VTYPE != '' && JIEQI_MODULE_VTYPE != 'Free') {
        if (strpos($configs['attachwimage'], '/') === false && strpos($configs['attachwimage'], '\\') === false) {
            $water_image_file = $GLOBALS['jieqiModules'][$post_set['module']]['path'] . '/images/' . $configs['attachwimage'];
        } else {
            $water_image_file = $configs['attachwimage'];
        }
        if (is_file($water_image_file)) {
            $make_image_water = true;
            include_once JIEQI_ROOT_PATH . '/lib/image/imagewater.php';
        }
    }
    $attachdir = jieqi_uploadpath($configs['attachdir'], $post_set['module']);
    if (!file_exists($attachdir)) {
        jieqi_createdir($attachdir);
    }
    $attachdir .= '/' . date('Ymd', $post_set['posttime']);
    if (!file_exists($attachdir)) {
        jieqi_createdir($attachdir);
    }
    foreach ($attachary as $k => $v) {
        $attach_save_path = $attachdir . '/' . $post_set['postid'] . '_' . $attachary[$k]['attachid'] . '.' . $attachary[$k]['postfix'];
        $tmp_attachfile = dirname($_FILES['attachfile']['tmp_name'][$v['order']]) . '/' . basename($attach_save_path);
        @move_uploaded_file($_FILES['attachfile']['tmp_name'][$v['order']], $tmp_attachfile);
        if ($make_image_water && preg_match('/\\.(gif|jpg|jpeg|png)$/i', $tmp_attachfile)) {
            $img = new ImageWater();
            $img->save_image_file = $tmp_attachfile;
            $img->codepage = JIEQI_SYSTEM_CHARSET;
            $img->wm_image_pos = $configs['attachwater'];
            $img->wm_image_name = $water_image_file;
            $img->wm_image_transition = $configs['attachwtrans'];
            $img->jpeg_quality = $configs['attachwquality'];
            $img->create($tmp_attachfile);
            unset($img);
        }
        jieqi_copyfile($tmp_attachfile, $attach_save_path, 511, true);
    }
}
function jieqi_post_attachold(&$post_set, $configs, &$attachs_handler)
{
    $tmpattachs = $post_set['attachment'];
    $attacholds = array();
    if (!empty($tmpattachs)) {
        $tmpattachary = jieqi_unserialize($tmpattachs);
        if (!is_array($tmpattachary)) {
            $tmpattachary = array();
        }
        if (!is_array($post_set['oldattach'])) {
            if (is_string($post_set['oldattach'])) {
                $post_set['oldattach'] = array($post_set['oldattach']);
            } else {
                $post_set['oldattach'] = array();
            }
        }
        foreach ($tmpattachary as $val) {
            if (in_array($val['attachid'], $post_set['oldattach'])) {
                $attacholds[] = $val;
            } else {
                $attachs_handler->delete($val['attachid']);
                $afname = jieqi_uploadpath($configs['attachdir'], JIEQI_MODULE_NAME) . '/' . date('Ymd', $post_set['posttime']) . '/' . $post_set['postid'] . '_' . $val['attachid'] . '.' . $val['postfix'];
                if (file_exists($afname)) {
                    jieqi_delfile($afname);
                }
            }
        }
    }
    return $attacholds;
}
function jieqi_post_newset(&$post_set, &$newPost)
{
    global $jieqiConfigs;
    global $jieqiPower;
    global $jieqiUsersStatus;
    global $jieqiUsersGroup;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (empty($_SESSION['jieqiUserId'])) {
        $tmpuid = 0;
        $tmpuname = '';
    } else {
        $tmpuid = $_SESSION['jieqiUserId'];
        $tmpuname = $_SESSION['jieqiUserName'];
    }
    $newPost->setVar('siteid', JIEQI_SITE_ID);
    $newPost->setVar('topicid', $post_set['topicid']);
    $istopic = isset($post_set['istopic']) ? $post_set['istopic'] : 0;
    $newPost->setVar('istopic', $istopic);
    $newPost->setVar('replypid', 0);
    $newPost->setVar('ownerid', $post_set['ownerid']);
    $newPost->setVar('posterid', $tmpuid);
    $newPost->setVar('poster', $tmpuname);
    $newPost->setVar('posttime', JIEQI_NOW_TIME);
    $newPost->setVar('posterip', jieqi_userip());
    $newPost->setVar('editorid', 0);
    $newPost->setVar('editor', '');
    $newPost->setVar('edittime', JIEQI_NOW_TIME);
    $newPost->setVar('editorip', '');
    $newPost->setVar('editnote', '');
    $newPost->setVar('iconid', 0);
    $newPost->setVar('attachment', $post_set['attachment']);
    $newPost->setVar('subject', $post_set['topictitle']);
    $newPost->setVar('posttext', $post_set['posttext']);
    $newPost->setVar('size', strlen($post_set['posttext']));
    if (!empty($jieqiConfigs['system']['postaudittimes'])) {
        if (!isset($jieqiPower['system'])) {
            jieqi_getconfigs('system', 'power');
        }
        if (!empty($post_set['autopost']) || jieqi_checkpower($jieqiPower['system']['postnoaudit'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
            $newPost->setVar('display', 0);
        } else {
            include_once JIEQI_ROOT_PATH . '/include/checker.php';
            $checker = new JieqiChecker();
            if ($checker->deny_time($jieqiConfigs['system']['postaudittimes'])) {
                $newPost->setVar('display', 0);
            } else {
                $newPost->setVar('display', 1);
            }
        }
    } else {
        $newPost->setVar('display', 0);
    }
}
function jieqi_post_finish()
{
    global $jieqiConfigs;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs');
    }
    if (0 < $jieqiConfigs['system']['postcheckcode']) {
        if (isset($_SESSION['jieqiCheckCode'])) {
            unset($_SESSION['jieqiCheckCode']);
        }
    }
}
function jieqi_post_upedit(&$post_set, $table)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $sql = 'UPDATE ' . $table . ' SET editorid=' . intval($_SESSION['jieqiUserId']) . ', editor=\'' . jieqi_dbslashes(strval($_SESSION['jieqiUserName'])) . '\', edittime=' . intval(JIEQI_NOW_TIME) . ', subject=\'' . jieqi_dbslashes($post_set['topictitle']) . '\', posttext=\'' . jieqi_dbslashes($post_set['posttext']) . '\', attachment=\'' . jieqi_dbslashes($post_set['attachment']) . '\' WHERE postid=' . intval($post_set['postid']);
    return $query->execute($sql);
}
function jieqi_topic_upedit(&$post_set, $table)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $sql = 'UPDATE ' . $table . ' SET title=\'' . jieqi_dbslashes($post_set['topictitle']) . '\', content=\'' . jieqi_dbslashes($post_set['posttext']) . '\' WHERE topicid=' . intval($post_set['topicid']);
    return $query->execute($sql);
}
function jieqi_topic_uppostadd(&$post, $table)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $postary = is_object($post) ? $post->getVars('n') : (is_array($post) ? $post : array());
    $lastary = array();
    $lastary['time'] = isset($postary['posttime']) ? intval($postary['posttime']) : intval(JIEQI_NOW_TIME);
    $lastary['uid'] = isset($postary['posterid']) ? intval($postary['posterid']) : intval($_SESSION['jieqiUserId']);
    $lastary['uname'] = isset($postary['poster']) ? $postary['poster'] : $_SESSION['jieqiUserName'];
    $sql = 'UPDATE ' . $table . ' SET views = views + 1, replies = replies + 1,  replierid = ' . $lastary['uid'] . ', replier = \'' . jieqi_dbslashes($lastary['uname']) . '\', replytime = ' . $lastary['time'] . ', lastinfo = \'' . jieqi_dbslashes(serialize($lastary)) . '\' WHERE topicid = ' . intval($postary['topicid']);
    return $query->execute($sql);
}
function jieqi_topic_uppostdel(&$post, $table)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $postary = is_object($post) ? $post->getVars('n') : (is_array($post) ? $post : array());
    $sql = 'UPDATE ' . $table . ' SET views = views + 1, replies = replies - 1 WHERE topicid = ' . intval($postary['topicid']);
    return $query->execute($sql);
}