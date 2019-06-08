<?php
function jieqi_article_vars($article, $isfull = false, $format = 's')
{
    global $jieqiModules;
    global $jieqiSort;
    global $jieqiConfigs;
    global $jieqiLang;
    global $article_static_url;
    global $article_dynamic_url;
    global $jieqiOption;
    if (!isset($jieqiSort['article'])) {
        jieqi_getconfigs('article', 'sort');
    }
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (!isset($jieqiLang['article']['article'])) {
        jieqi_loadlang('article', 'article');
    }
    if (!isset($jieqiLang['article']['list'])) {
        jieqi_loadlang('list', 'article');
    }
    if (!isset($jieqiOption['article'])) {
        jieqi_getconfigs('article', 'option', 'jieqiOption');
    }
    if (!isset($article_static_url)) {
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    }
    if (!isset($article_dynamic_url)) {
        $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    }
    $ret = array();
    $ret = jieqi_query_rowvars($article, $format, 'article');
    $ret['setting'] = is_object($article) ? jieqi_funtoarray('jieqi_htmlstr', jieqi_unserialize($article->getVar('setting', 'n'))) : jieqi_funtoarray('jieqi_htmlstr', jieqi_unserialize($article['setting']));
    if (!is_array($ret['setting'])) {
        $ret['setting'] = array();
    }
    if (isset($ret['siteid']) && !empty($ret['siteid'])) {
        global $jieqiSites;
        if (!isset($jieqiSites)) {
            jieqi_getconfigs('system', 'sites', 'jieqiSites');
        }
        if (isset($jieqiSites[$ret['siteid']])) {
            $ret['sitename'] = jieqi_htmlstr($jieqiSites[$ret['siteid']]['name']);
            $ret['siteurl'] = jieqi_htmlstr($jieqiSites[$ret['siteid']]['url']);
            $ret['firstflag'] = $ret['sitename'];
        }
    }
    $ret['sort'] = isset($jieqiSort['article'][$ret['sortid']]['caption']) ? $jieqiSort['article'][$ret['sortid']]['caption'] : '';
    $ret['sortname'] = $ret['sort'];
    $ret['type'] = isset($jieqiSort['article'][$ret['sortid']]['types'][$ret['typeid']]) ? $jieqiSort['article'][$ret['sortid']]['types'][$ret['typeid']] : '';
    $ret['typename'] = $ret['type'];
    $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
    $daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
    $monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
    $prestart = mktime(0, 0, 0, (int) $tmpvar[1] - 1, 1, (int) $tmpvar[0]);
    $tmpvar = date('w', JIEQI_NOW_TIME);
    if ($tmpvar == 0) {
        $tmpvar = 7;
    }
    $weekstart = $daystart;
    if (1 < $tmpvar) {
        $weekstart -= ($tmpvar - 1) * 86400;
    }
    $dwmary = array('visit' => 'lastvisit', 'vote' => 'lastvote', 'words' => 'lastupdate', 'down' => 'lastdown', 'flower' => 'lastflower', 'egg' => 'lastegg', 'vipvote' => 'lastvipvote');
    foreach ($dwmary as $k => $v) {
        if ($ret[$v] < $daystart && isset($ret['day' . $k])) {
            $ret['day' . $k] = 0;
        }
        if ($ret[$v] < $weekstart && isset($ret['week' . $k])) {
            $ret['week' . $k] = 0;
        }
        if (isset($ret['pre' . $k])) {
            if ($ret[$v] < $prestart) {
                $ret['pre' . $k] = 0;
            } else {
                if ($ret[$v] < $monthstart && isset($ret['month' . $k])) {
                    $ret['pre' . $k] = $ret['month' . $k];
                }
            }
        }
        if ($ret[$v] < $monthstart && isset($ret['month' . $k])) {
            $ret['month' . $k] = 0;
        }
    }
    if ($ret['lastupdate'] < $prestart) {
        if (isset($ret['preupds'])) {
            $ret['preupds'] = 0;
        }
        if (isset($ret['preupdt'])) {
            $ret['preupdt'] = 0;
        }
    } else {
        if ($ret['lastupdate'] < $monthstart) {
            if (isset($ret['preupds']) && isset($ret['monthupds'])) {
                $ret['preupds'] = $ret['monthupds'];
            }
            if (isset($ret['preupdt']) && isset($ret['monthupdt'])) {
                $ret['preupdt'] = $ret['monthupdt'];
            }
        }
    }
    if ($ret['lastupdate'] < $monthstart) {
        if (isset($ret['monthupds'])) {
            $ret['monthupds'] = 0;
        }
        if (isset($ret['monthupdt'])) {
            $ret['monthupdt'] = 0;
        }
    }
    $wordsary = array('words', 'freewords', 'vipwords', 'monthwords', 'prewords', 'weekwords', 'daywords');
    foreach ($wordsary as $v) {
        if (isset($ret[$v])) {
            $ret[str_replace('words', 'size', $v) . '_c'] = $ret[$v];
            $ret[$v . '_k'] = jieqi_wordsformat($ret[$v], 'k');
        }
    }
    $ret['monthdays'] = date('t', $monthstart);
    $ret['monthdate'] = date('j', JIEQI_NOW_TIME);
    $ret['monthwork'] = 0 < $ret['monthupds'] && $ret['monthdate'] <= $ret['monthupds'] ? 1 : 0;
    $ret['predays'] = date('t', $prestart);
    $ret['prework'] = 0 < $ret['preupds'] && $ret['predays'] <= $ret['preupds'] ? 1 : 0;
    if ($isfull) {
        $ret['maxdays'] = 31;
        $ret['maxupda'] = array();
        for ($d = 1; $d <= $ret['maxdays']; $d++) {
            $ret['maxupda'][$d] = 0;
        }
        $ret['monthupdt'] = intval($ret['monthupdt']);
        $ret['monthupda'] = array();
        $t = 1;
        for ($d = 1; $d <= $ret['monthdays']; $d++) {
            $ret['monthupda'][$d] = 0 < ($ret['monthupdt'] & $t) ? 1 : 0;
            $ret['maxupda'][$d] = $ret['monthupda'][$d];
            $t *= 2;
        }
        $ret['preupdt'] = intval($ret['preupdt']);
        $ret['preupda'] = array();
        $t = 1;
        for ($d = 1; $d <= $ret['predays']; $d++) {
            $ret['preupda'][$d] = 0 < ($ret['preupdt'] & $t) ? 1 : 0;
            $t *= 2;
        }
    }
    $ret['ratenum'] = intval($ret['ratenum']);
    $ret['ratesum'] = intval($ret['ratesum']);
    $rateavg = 0 < $ret['ratenum'] ? $ret['ratesum'] / $ret['ratenum'] : 0;
    $ret['rateavg'] = sprintf('%0.1f', $rateavg);
    $ret['rateavg_i'] = floor($rateavg);
    $ret['rateavg_d'] = round($rateavg, 1) * 10 % 10;
    if (isset($ret['rate5'])) {
        $ratecount = $ret['rate1'] + $ret['rate2'] + $ret['rate3'] + $ret['rate4'] + $ret['rate5'];
        $ret['rate1_p'] = 0 < $ret['rate1'] ? sprintf('%0.1f', $ret['rate1'] * 100 / $ratecount) : 0;
        $ret['rate2_p'] = 0 < $ret['rate2'] ? sprintf('%0.1f', $ret['rate2'] * 100 / $ratecount) : 0;
        $ret['rate3_p'] = 0 < $ret['rate3'] ? sprintf('%0.1f', $ret['rate3'] * 100 / $ratecount) : 0;
        $ret['rate4_p'] = 0 < $ret['rate4'] ? sprintf('%0.1f', $ret['rate4'] * 100 / $ratecount) : 0;
        $ret['rate5_p'] = 0 < $ret['rate5'] ? sprintf('%0.1f', $ret['rate5'] * 100 / $ratecount) : 0;
    }
    $ret['freestart'] = intval($ret['freestart']);
    $ret['freeend'] = intval($ret['freeend']);
    $ret['infree'] = $ret['freestart'] <= JIEQI_NOW_TIME && JIEQI_NOW_TIME <= $ret['freeend'] ? 1 : 0;
    if ($ret['freestart'] < $ret['freeend'] && JIEQI_NOW_TIME < $ret['freestart']) {
        $ret['freebefore'] = $ret['freestart'] - JIEQI_NOW_TIME;
        $ret['freeafter'] = $ret['freeend'] - $ret['freestart'];
    } else {
        $ret['freebefore'] = 0;
        $ret['freeafter'] = 0;
    }
    if (!$isfull) {
        $ret['intro'] = is_object($article) ? jieqi_htmlchars(jieqi_substr($article->getVar('intro', 'n'), 0, 500)) : jieqi_htmlchars(jieqi_substr($article['intro'], 0, 500));
        $ret['notice'] = is_object($article) ? jieqi_htmlchars(jieqi_substr($article->getVar('notice', 'n'), 0, 500)) : jieqi_htmlchars(jieqi_substr($article['notice'], 0, 500));
        $ret['foreword'] = is_object($article) ? jieqi_htmlchars(jieqi_substr($article->getVar('foreword', 'n'), 0, 500)) : jieqi_htmlchars(jieqi_substr($article['foreword'], 0, 500));
    }
    $ret['url_simage'] = jieqi_geturl('article', 'cover', $ret['articleid'], 's', $ret['imgflag']);
    $ret['url_image'] = $ret['url_simage'];
    $ret['hasimage'] = empty($ret['url_simage']) ? 0 : 1;
    $ret['url_limage'] = jieqi_geturl('article', 'cover', $ret['articleid'], 'l', $ret['imgflag']);
    $ret['articlesubdir'] = jieqi_getsubdir($ret['articleid']);
    $ret['url_articleinfo'] = jieqi_geturl('article', 'article', $ret['articleid'], 'info', $ret['articlecode']);
    $ret['url_articleindex'] = 0 < $ret['chapters'] ? jieqi_geturl('article', 'article', $ret['articleid'], 'index', $ret['articlecode']) : '#';
    $ret['url_index'] = $ret['url_articleindex'];
    $ret['url_read'] = $ret['url_articleindex'];
    $ret['url_fullpage'] = 0 < $ret['chapters'] ? jieqi_geturl('article', 'article', $ret['articleid'], 'full', $ret['articlecode']) : '#';
    $ret['url_lastchapter'] = 0 < $ret['lastchapterid'] ? jieqi_geturl('article', 'chapter', $ret['lastchapterid'], $ret['articleid'], 0, $ret['articlecode']) : '';
    $ret['url_vipchapter'] = 0 < $ret['vipchapterid'] ? jieqi_geturl('article', 'chapter', $ret['vipchapterid'], $ret['vipid'], 1, $ret['articlecode']) : '';
    $ret['url_manage'] = $article_static_url . '/articlemanage.php?id=' . $ret['articleid'];
    $ret['url_bookcase'] = $article_dynamic_url . '/addbookcase.php?bid=' . $ret['articleid'];
    $ret['url_uservote'] = $article_dynamic_url . '/uservote.php?id=' . $ret['articleid'];
    $author = is_object($article) ? $article->getVar('author', 'n') : $article['author'];
    $ret['url_authorpage'] = jieqi_geturl('article', 'author', $ret['authorid'], $author);
    $ret['url_authorarticle'] = jieqi_geturl('article', 'author', 0, $author);
    $url_article = $ret['url_articleinfo'];
    if (!preg_match('/^https?:\\/\\//i', $url_article)) {
        $url_article = JIEQI_LOCAL_URL . $url_article;
    }
    $ret['url_report'] = is_object($article) ? JIEQI_URL . '/newmessage.php?tosys=1&title=' . urlencode(sprintf($jieqiLang['article']['article_report_title'], $article->getVar('articlename', 'n'))) . '&content=' . urlencode(sprintf($jieqiLang['article']['article_report_reason'], $url_article)) : JIEQI_URL . '/newmessage.php?tosys=1&title=' . urlencode(sprintf($jieqiLang['article']['article_report_title'], $article['articlename'])) . '&content=' . urlencode(sprintf($jieqiLang['article']['article_report_reason'], $url_article));
    return $ret;
}
function jieqi_article_coverdo($file, $size = 's', $ext = '')
{
    global $jieqiConfigs;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    }
    if (empty($ext)) {
        $ext = strtolower(substr(strrchr(trim($file), '.'), 1));
    }
    if (substr($ext, 0, 1) == '.') {
        $ext = substr($ext, 1);
    }
    if (function_exists('gd_info')) {
        jieqi_article_coverchktype($file);
        jieqi_article_coverresize($file, $size);
    }
    return true;
}
function jieqi_article_coverchktype($imgname, $ext = '')
{
    global $jieqiConfigs;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    }
    if (strlen($ext) == 0) {
        $ext = strtolower(substr(strrchr(trim($imgname), '.'), 1));
    }
    if (!empty($jieqiConfigs['article']['imgquality']) && 0 < $jieqiConfigs['article']['imgquality'] && $jieqiConfigs['article']['imgquality'] <= 100) {
        $quality = $jieqiConfigs['article']['imgquality'];
    } else {
        $quality = 90;
    }
    $imginfo = false;
    if (function_exists('getimagesize')) {
        $imginfo = @getimagesize($imgname);
        if ($imginfo === false) {
            return false;
        }
        $typeary = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
        if (is_array($imginfo) && isset($typeary[$imginfo[2]])) {
            $imgtype = $typeary[$imginfo[2]];
        } else {
            $imgtype = '';
        }
        if (!empty($imgtype) && $imgtype != $ext && in_array($ext, $typeary)) {
            $tmp_img = false;
            switch ($imgtype) {
                case 'gif':
                    $tmp_img = @imagecreatefromgif($imgname);
                    break;
                case 'jpg':
                    $tmp_img = @imagecreatefromjpeg($imgname);
                    break;
                case 'png':
                    $tmp_img = @imagecreatefrompng($imgname);
                    break;
            }
            if (is_resource($tmp_img)) {
                switch ($ext) {
                    case 'gif':
                        imagegif($tmp_img, $imgname);
                        break;
                    case 'png':
                        imagepng($tmp_img, $imgname);
                        break;
                    case 'jpg':
                    default:
                        if (0 <= $quality) {
                            imagejpeg($tmp_img, $imgname, $quality);
                        } else {
                            imagejpeg($tmp_img, $imgname);
                        }
                        break;
                }
                imagedestroy($tmp_img);
            }
        }
    }
    return $imginfo;
}
function jieqi_article_coverwater($imgname)
{
    global $jieqiConfigs;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    }
    if (function_exists('gd_info') && 0 < $jieqiConfigs['article']['coverwater']) {
        if (strpos($jieqiConfigs['article']['coverwimg'], '/') === false && strpos($jieqiConfigs['article']['coverwimg'], '\\') === false) {
            $water_image_file = $GLOBALS['jieqiModules']['article']['path'] . '/images/' . $jieqiConfigs['article']['coverwimg'];
        } else {
            $water_image_file = $jieqiConfigs['article']['coverwimg'];
        }
        if (is_file($water_image_file)) {
            include_once JIEQI_ROOT_PATH . '/lib/image/imagewater.php';
            $img = new ImageWater();
            $img->save_image_file = $imgname;
            $img->codepage = JIEQI_SYSTEM_CHARSET;
            $img->wm_image_pos = $jieqiConfigs['article']['coverwater'];
            $img->wm_image_name = $water_image_file;
            $img->wm_image_transition = $jieqiConfigs['article']['coverwtrans'];
            $img->create($imgname);
            unset($img);
        } else {
            return false;
        }
    }
    return true;
}
function jieqi_article_coverresize($imgname, $size = 's')
{
    global $jieqiConfigs;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs', 'jieqiConfigs');
    }
    if (function_exists('gd_info')) {
        if ($size == 'l') {
            $coverwidth = !empty($jieqiConfigs['article']['coverlwidth']) ? intval($jieqiConfigs['article']['coverlwidth']) : 0;
            $coverheight = !empty($jieqiConfigs['article']['coverlheight']) ? intval($jieqiConfigs['article']['coverlheight']) : 0;
        } else {
            $coverwidth = !empty($jieqiConfigs['article']['coverwidth']) ? intval($jieqiConfigs['article']['coverwidth']) : 0;
            $coverheight = !empty($jieqiConfigs['article']['coverheight']) ? intval($jieqiConfigs['article']['coverheight']) : 0;
        }
        if ($coverwidth <= 0 || $coverheight <= 0) {
            return true;
        }
        $ext = strtolower(substr(strrchr(trim($imgname), '.'), 1));
        $imginfo = @getimagesize($imgname);
        $resize = true;
        if (is_array($imginfo)) {
            if (0 < $coverwidth && $imginfo[0] < $coverwidth || 0 < $coverheight && $imginfo[1] < $coverheight) {
                $resize = false;
            }
        }
        if ($resize) {
            include_once JIEQI_ROOT_PATH . '/lib/image/imageresize.php';
            $imgresize = new ImageResize();
            $imgresize->load($imgname);
            $imgresize->resize($coverwidth, $coverheight);
            $imgresize->save($imgname);
        }
    }
    return true;
}
function jieqi_article_getuptime()
{
    global $jieqiArticleuplog;
    jieqi_getcachevars('article', 'articleuplog');
    if (!is_array($jieqiArticleuplog)) {
        $jieqiArticleuplog = array('articleuptime' => 0, 'chapteruptime' => 0);
    }
    $uptime = $jieqiArticleuplog['chapteruptime'] < $jieqiArticleuplog['articleuptime'] ? $jieqiArticleuplog['articleuptime'] : $jieqiArticleuplog['chapteruptime'];
    return intval($uptime);
}