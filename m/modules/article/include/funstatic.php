<?php

function article_make_binfo($fid = 1, $toid = 0, $static = true, $output = false)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $where = '';
    if (1 < $fid) {
        $where .= empty($where) ? ' articleid >= ' . $fid : ' AND articleid >= ' . $fid;
    }
    if (0 < $toid) {
        $where .= empty($where) ? ' articleid <= ' . $toid : ' AND articleid <= ' . $toid;
    }
    $sql = 'SELECT articleid FROM ' . jieqi_dbprefix('article_article');
    $sql .= empty($where) ? ' WHERE 1' : ' WHERE' . $where;
    $query->execute($sql);
    $aids = array();
    while ($row = $query->getRow()) {
        $aids[] = $row['articleid'];
    }
    foreach ($aids as $aid) {
        article_make_sinfo($aid, $static, $output);
    }
}
function article_make_sinfo($id, $static = true, $output = false)
{
    global $jieqiConfigs;
    global $jieqiModules;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (!preg_match('/\\.(htm|html|xhtml)$/i', $jieqiConfigs['article']['fakeinfo'])) {
        return false;
    }
    $jieqiConfigs['article']['fakeinfo'] = preg_replace('/https?:\\/\\/[^\\/]+/is', '', $jieqiConfigs['article']['fakeinfo']);
    if (substr($jieqiConfigs['article']['fakeinfo'], 0, 1) != '/') {
        $jieqiConfigs['article']['fakeinfo'] = '/' . $jieqiConfigs['article']['fakeinfo'];
    }
    $tmpary = explode('/', $jieqiConfigs['article']['fakeinfo']);
    $tmpcot = count($tmpary) - 2;
    if (0 < strpos($jieqiConfigs['article']['fakeinfo'], '<{$id|subdirectory}>')) {
        $tmpcot++;
    }
    $globalfile = str_repeat('../', $tmpcot) . 'global.php';
    $repfrom = array('<{$id|subdirectory}>', '<{$id}>');
    $repto = array(jieqi_getsubdir($id), $id);
    $fname = JIEQI_ROOT_PATH . trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinfo']));
    jieqi_checkdir(dirname($fname), true);
    if ($static) {
        $url = $jieqiModules['article']['url'];
        if (strtolower(substr($url, 0, 7)) != 'http://') {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
        }
        $content = file_get_contents($url . '/articleinfo.php?id=' . $id);
    } else {
        $content = '<?php' . "\n" . 'define(\'JIEQI_MODULE_NAME\', \'article\');' . "\n" . '$jieqi_fake_state = 1;' . "\n" . 'include_once(\'' . $globalfile . '\');' . "\n" . '$_REQUEST[\'id\'] = ' . $id . ';' . "\n" . 'include_once($jieqiModules[\'article\'][\'path\'].\'/articleinfo.php\');' . "\n" . '?>';
    }
    jieqi_writefile($fname, $content);
    if ($output) {
        echo $id . '.    ';
        ob_flush();
        flush();
    }
}
function article_delete_sinfo($id, $output = false)
{
    global $jieqiConfigs;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    $jieqiConfigs['article']['fakeinfo'] = preg_replace('/https?:\\/\\/[^\\/]+/is', '', $jieqiConfigs['article']['fakeinfo']);
    if (substr($jieqiConfigs['article']['fakeinfo'], 0, 1) != '/') {
        $jieqiConfigs['article']['fakeinfo'] = '/' . $jieqiConfigs['article']['fakeinfo'];
    }
    $repfrom = array('<{$id|subdirectory}>', '<{$id}>');
    $repto = array(jieqi_getsubdir($id), $id);
    $fname = JIEQI_ROOT_PATH . trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinfo']));
    if (is_file($fname)) {
        jieqi_delfile($fname);
    }
    if ($output) {
        echo $id . ' ';
        ob_flush();
        flush();
    }
}
function article_make_asort($fid = 1, $tid = 0, $static = true, $output = false)
{
    global $jieqiSort;
    if (!isset($jieqiSort['article'])) {
        jieqi_getconfigs('article', 'sort');
    }
    article_make_ssort(0, $fid, $tid, $static, $output);
    foreach ($jieqiSort['article'] as $k => $v) {
        if ($output) {
            echo '<br />' . $v['caption'] . '<br />';
            ob_flush();
            flush();
        }
        article_make_ssort($k, $fid, $tid, $static, $output);
    }
}
function article_make_ssort($class = 0, $fid = 1, $tid = 0, $static = true, $output = false)
{
    global $jieqiConfigs;
    global $query;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (empty($tid) && 0 < JIEQI_MAX_PAGES) {
        $tid = JIEQI_MAX_PAGES;
    }
    if (empty($tid)) {
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_article') . ' WHERE display=0 AND words>0';
        if (0 < $class) {
            $sql .= ' AND sortid=' . intval($class);
        }
        $query->execute($sql);
        if ($row = $query->getRow()) {
            $cot = intval($row['cot']);
            $pnum = 0;
            if (isset($jieqiConfigs['article']['pagenum'])) {
                $pnum = intval($jieqiConfigs['article']['pagenum']);
            } else {
                if (defined('JIEQI_PAGE_ROWS')) {
                    $pnum = intval(JIEQI_PAGE_ROWS);
                }
            }
            if ($pnum <= 0) {
                $pnum = 30;
            }
            $tid = ceil($cot / $pnum);
        }
        if ($tid < 1) {
            $tid = 1;
        }
    }
    if ($tid < $fid) {
        return false;
    }
    for ($page = $fid; $page <= $tid; $page++) {
        article_make_psort($class, $page, $static, $output);
    }
}
function article_make_psort($class = 0, $page = 1, $static = true, $output = false)
{
    global $jieqiConfigs;
    global $jieqiModules;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (!preg_match('/\\.(htm|html|xhtml)$/i', $jieqiConfigs['article']['fakesort'])) {
        return false;
    }
    $jieqiConfigs['article']['fakesort'] = preg_replace('/https?:\\/\\/[^\\/]+/is', '', $jieqiConfigs['article']['fakesort']);
    if (substr($jieqiConfigs['article']['fakesort'], 0, 1) != '/') {
        $jieqiConfigs['article']['fakesort'] = '/' . $jieqiConfigs['article']['fakesort'];
    }
    $tmpary = explode('/', $jieqiConfigs['article']['fakesort']);
    $tmpcot = count($tmpary) - 2;
    if (0 < strpos($jieqiConfigs['article']['fakesort'], '<{$page|subdirectory}>')) {
        $tmpcot++;
    }
    $globalfile = str_repeat('../', $tmpcot) . 'global.php';
    $repfrom = array('<{$class}>', '<{$page|subdirectory}>', '<{$page}>');
    $class = intval($class);
    if (empty($class)) {
        $repc = '';
    } else {
        $repc = $class;
    }
    $repto = array($repc, jieqi_getsubdir($page), $page);
    $fname = JIEQI_ROOT_PATH . trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakesort']));
    jieqi_checkdir(dirname($fname), true);
    if ($static) {
        $url = $jieqiModules['article']['url'];
        if (strtolower(substr($url, 0, 7)) != 'http://') {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
        }
        $content = file_get_contents($url . '/articlelist.php?class=' . $class . '&page=' . $page);
    } else {
        $content = '<?php' . "\n" . 'define(\'JIEQI_MODULE_NAME\', \'article\');' . "\n" . '$jieqi_fake_state = 1;' . "\n" . 'include_once(\'' . $globalfile . '\');' . "\n" . '$_REQUEST[\'class\'] = ' . $class . ';' . "\n" . '$_REQUEST[\'page\'] = ' . $page . ';' . "\n" . 'include_once($jieqiModules[\'article\'][\'path\'].\'/articlelist.php\');' . "\n" . '?>';
    }
    jieqi_writefile($fname, $content);
    if ($output) {
        echo $page . ' ';
        ob_flush();
        flush();
    }
}
function article_make_ainitial($fid = 1, $tid = 0, $static = true, $output = false)
{
    for ($i = 65; $i <= 90; $i++) {
        $tmpvar = chr($i);
        $initary[$tmpvar] = $tmpvar;
    }
    $initary['1'] = '1';
    foreach ($initary as $k => $v) {
        if ($output) {
            echo '<br />[' . strtoupper($v) . ']<br />';
            ob_flush();
            flush();
        }
        article_make_sinitial($v, $fid, $tid, $static, $output);
    }
}
function article_make_sinitial($initial, $fid = 1, $tid = 0, $static = true, $output = false)
{
    global $jieqiConfigs;
    global $query;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (empty($tid) && 0 < JIEQI_MAX_PAGES) {
        $tid = JIEQI_MAX_PAGES;
    }
    if (empty($tid)) {
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_article') . ' WHERE display=0 AND words>0 AND initial =\'' . jieqi_dbslashes(strtoupper($initial)) . '\'';
        $query->execute($sql);
        if ($row = $query->getRow()) {
            $cot = intval($row['cot']);
            $pnum = 0;
            if (isset($jieqiConfigs['article']['pagenum'])) {
                $pnum = intval($jieqiConfigs['article']['pagenum']);
            } else {
                if (defined('JIEQI_PAGE_ROWS')) {
                    $pnum = intval(JIEQI_PAGE_ROWS);
                }
            }
            if ($pnum <= 0) {
                $pnum = 30;
            }
            $tid = ceil($cot / $pnum);
        }
        if ($tid < 1) {
            $tid = 1;
        }
    }
    if ($tid < $fid) {
        return false;
    }
    for ($page = $fid; $page <= $tid; $page++) {
        article_make_pinitial($initial, $page, $static, $output);
    }
}
function article_make_pinitial($initial, $page = 1, $static = true, $output = false)
{
    global $jieqiConfigs;
    global $jieqiModules;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (!preg_match('/\\.(htm|html|xhtml)$/i', $jieqiConfigs['article']['fakeinitial'])) {
        return false;
    }
    $jieqiConfigs['article']['fakeinitial'] = preg_replace('/https?:\\/\\/[^\\/]+/is', '', $jieqiConfigs['article']['fakeinitial']);
    if (substr($jieqiConfigs['article']['fakeinitial'], 0, 1) != '/') {
        $jieqiConfigs['article']['fakeinitial'] = '/' . $jieqiConfigs['article']['fakeinitial'];
    }
    $tmpary = explode('/', $jieqiConfigs['article']['fakeinitial']);
    $tmpcot = count($tmpary) - 2;
    if (0 < strpos($jieqiConfigs['article']['fakeinitial'], '<{$page|subdirectory}>')) {
        $tmpcot++;
    }
    $globalfile = str_repeat('../', $tmpcot) . 'global.php';
    $repfrom = array('<{$initial}>', '<{$page|subdirectory}>', '<{$page}>');
    $repto = array($initial, jieqi_getsubdir($page), $page);
    $fname = JIEQI_ROOT_PATH . trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinitial']));
    jieqi_checkdir(dirname($fname), true);
    if ($static) {
        $url = $jieqiModules['article']['url'];
        if (strtolower(substr($url, 0, 7)) != 'http://') {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
        }
        $content = file_get_contents($url . '/articlelist.php?initial=' . $initial . '&page=' . $page);
    } else {
        $content = '<?php' . "\n" . 'define(\'JIEQI_MODULE_NAME\', \'article\');' . "\n" . '$jieqi_fake_state = 1;' . "\n" . 'include_once(\'' . $globalfile . '\');' . "\n" . '$_REQUEST[\'initial\'] = "' . $initial . '";' . "\n" . '$_REQUEST[\'page\'] = ' . $page . ';' . "\n" . 'include_once($jieqiModules[\'article\'][\'path\'].\'/articlelist.php\');' . "\n" . '?>';
    }
    jieqi_writefile($fname, $content);
    if ($output) {
        echo $page . ' ';
        ob_flush();
        flush();
    }
}
function article_make_atoplist($fid = 1, $tid = 0, $static = true, $output = false)
{
    global $jieqiLang;
    jieqi_loadlang('manage', 'article');
    $topary = array('allvisit' => $jieqiLang['article']['top_allvisit'], 'monthvisit' => $jieqiLang['article']['top_monthvisit'], 'weekvisit' => $jieqiLang['article']['top_weekvisit'], 'dayvisit' => $jieqiLang['article']['top_dayvisit'], 'allauthorvisit' => $jieqiLang['article']['top_avall'], 'monthauthorvisit' => $jieqiLang['article']['top_avmonth'], 'weekauthorvisit' => $jieqiLang['article']['top_avweek'], 'dayauthorvisit' => $jieqiLang['article']['top_avday'], 'allvote' => $jieqiLang['article']['top_voteall'], 'monthvote' => $jieqiLang['article']['top_votemonth'], 'weekvote' => $jieqiLang['article']['top_voteweek'], 'dayvote' => $jieqiLang['article']['top_voteday_titile'], 'postdate' => $jieqiLang['article']['top_postdate'], 'toptime' => $jieqiLang['article']['top_toptime'], 'goodnum' => $jieqiLang['article']['top_goodnum'], 'words' => $jieqiLang['article']['top_words'], 'authorupdate' => $jieqiLang['article']['top_authorupdate'], 'masterupdate' => $jieqiLang['article']['top_masterupdate'], 'lastupdate' => $jieqiLang['article']['top_lastupdate']);
    foreach ($topary as $k => $v) {
        if ($output) {
            echo '<br />' . $v . '<br />';
            ob_flush();
            flush();
        }
        article_make_stoplist($k, $fid, $tid, $static, $output);
    }
}
function article_make_stoplist($sort, $fid = 1, $tid = 0, $static = true, $output = false)
{
    global $jieqiConfigs;
    global $query;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (empty($tid) && 0 < JIEQI_MAX_PAGES) {
        $tid = JIEQI_MAX_PAGES;
    }
    if (empty($tid)) {
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = 'SELECT count(*) AS cot FROM ' . jieqi_dbprefix('article_article') . ' WHERE display=0 AND words>0';
        $tmpvar = explode('-', date('Y-m-d', JIEQI_NOW_TIME));
        $daystart = mktime(0, 0, 0, (int) $tmpvar[1], (int) $tmpvar[2], (int) $tmpvar[0]);
        $monthstart = mktime(0, 0, 0, (int) $tmpvar[1], 1, (int) $tmpvar[0]);
        $tmpvar = date('w', JIEQI_NOW_TIME);
        if ($tmpvar == 0) {
            $tmpvar = 7;
        }
        $weekstart = $daystart;
        if (1 < $tmpvar) {
            $weekstart -= ($tmpvar - 1) * 86400;
        }
        switch ($sort) {
            case 'monthvisit':
            case 'mouthvisit':
                $sql .= ' AND lastvisit >= ' . $monthstart;
                break;
            case 'weekvisit':
                $sql .= ' AND lastvisit >= ' . $weekstart;
                break;
            case 'dayvisit':
                $sql .= ' AND lastvisit >= ' . $daystart;
                break;
            case 'allauthorvisit':
                $sql .= ' AND authorid > 0';
                break;
            case 'monthauthorvisit':
            case 'mouthauthorvisit':
                $sql .= ' AND authorid > 0 AND lastvisit >= ' . $monthstart;
                break;
            case 'weekauthorvisit':
                $sql .= ' AND authorid > 0 AND lastvisit >= ' . $weekstart;
                break;
            case 'dayauthorvisit':
                $sql .= ' AND authorid > 0 AND lastvisit >= ' . $daystart;
                break;
            case 'monthvote':
            case 'mouthvote':
                $sql .= ' AND lastvote >= ' . $monthstart;
                break;
            case 'weekvote':
                $sql .= ' AND lastvote >= ' . $weekstart;
                break;
            case 'dayvote':
                $sql .= ' AND lastvote >= ' . $daystart;
                break;
            case 'authorupdate':
                $sql .= ' AND authorid > 0';
                break;
            case 'masterupdate':
                $sql .= ' AND authorid = 0';
                break;
        }
        $query->execute($sql);
        if ($row = $query->getRow()) {
            $cot = intval($row['cot']);
            $pnum = 0;
            if (isset($jieqiConfigs['article']['pagenum'])) {
                $pnum = intval($jieqiConfigs['article']['pagenum']);
            } else {
                if (defined('JIEQI_PAGE_ROWS')) {
                    $pnum = intval(JIEQI_PAGE_ROWS);
                }
            }
            if ($pnum <= 0) {
                $pnum = 30;
            }
            $tid = ceil($cot / $pnum);
        }
        if ($tid < 1) {
            $tid = 1;
        }
    }
    if ($tid < $fid) {
        return false;
    }
    for ($page = $fid; $page <= $tid; $page++) {
        article_make_ptoplist($sort, $page, $static, $output);
    }
}
function article_make_ptoplist($sort, $page = 1, $static = true, $output = false)
{
    global $jieqiConfigs;
    global $jieqiModules;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    if (!preg_match('/\\.(htm|html|xhtml)$/i', $jieqiConfigs['article']['faketoplist'])) {
        return false;
    }
    $jieqiConfigs['article']['faketoplist'] = preg_replace('/https?:\\/\\/[^\\/]+/is', '', $jieqiConfigs['article']['faketoplist']);
    if (substr($jieqiConfigs['article']['faketoplist'], 0, 1) != '/') {
        $jieqiConfigs['article']['faketoplist'] = '/' . $jieqiConfigs['article']['faketoplist'];
    }
    $tmpary = explode('/', $jieqiConfigs['article']['faketoplist']);
    $tmpcot = count($tmpary) - 2;
    if (0 < strpos($jieqiConfigs['article']['faketoplist'], '<{$page|subdirectory}>')) {
        $tmpcot++;
    }
    $globalfile = str_repeat('../', $tmpcot) . 'global.php';
    $repfrom = array('<{$sort}>', '<{$page|subdirectory}>', '<{$page}>');
    $repto = array($sort, jieqi_getsubdir($page), $page);
    $fname = JIEQI_ROOT_PATH . trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['faketoplist']));
    jieqi_checkdir(dirname($fname), true);
    if ($static) {
        $url = $jieqiModules['article']['url'];
        if (strtolower(substr($url, 0, 7)) != 'http://') {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
        }
        $content = file_get_contents($url . '/toplist.php?sort=' . $sort . '&page=' . $page);
    } else {
        $content = '<?php' . "\n" . 'define(\'JIEQI_MODULE_NAME\', \'article\');' . "\n" . '$jieqi_fake_state = 1;' . "\n" . 'include_once(\'' . $globalfile . '\');' . "\n" . '$_REQUEST[\'sort\'] = "' . $sort . '";' . "\n" . '$_REQUEST[\'page\'] = ' . $page . ';' . "\n" . 'include_once($jieqiModules[\'article\'][\'path\'].\'/toplist.php\');' . "\n" . '?>';
    }
    jieqi_writefile($fname, $content);
    if ($output) {
        echo $page . ' ';
        ob_flush();
        flush();
    }
}
function article_update_static($action, $id, $sortid)
{
    global $jieqiConfigs;
    global $jieqiModules;
    if (!isset($jieqiConfigs['article'])) {
        jieqi_getconfigs('article', 'configs');
    }
    $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
    $article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
    $url = $article_dynamic_url . '/makestatic.php?key=' . urlencode(md5(JIEQI_DB_USER . JIEQI_DB_PASS . JIEQI_DB_NAME)) . '&action=' . urldecode($action) . '&id=' . intval($id) . '&sortid=' . intval($sortid);
    $url = trim($url);
    if (strtolower(substr($url, 0, 7)) != 'http://') {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
    }
    $ret = jieqi_socket_url1($url);
    return $ret;
}
function jieqi_socket_url1($url)
{
    $ret = @file_get_contents($url);
}
function jieqi_socket_url2($url)
{
    if (!function_exists('fsockopen')) {
        return false;
    }
    $method = 'GET';
    $url_array = parse_url($url);
    $port = isset($url_array['port']) ? $url_array['port'] : 80;
    $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
    if (!$fp) {
        return false;
    }
    $getPath = $url_array['path'];
    if (!empty($url_array['query'])) {
        $getPath .= '?' . $url_array['query'];
    }
    $header = $method . ' ' . $getPath;
    $header .= ' HTTP/1.1' . "\r\n" . '';
    $header .= 'Host: ' . $url_array['host'] . "\r\n";
    $header .= 'Connection:Close' . "\r\n" . '' . "\r\n" . '';
    fwrite($fp, $header);
    if (!feof($fp)) {
        fgets($fp, 8);
    }
    fclose($fp);
    return true;
}