<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs('article', 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_getconfigs('article', 'configs');
@set_time_limit(0);
@session_write_close();
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
include_once $jieqiModules['obook']['path'] . '/include/actobook.php';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
$sql = 'SELECT * FROM ' . jieqi_dbprefix('article_draft') . ' WHERE display = 0 AND ispub > 0 AND pubdate <= ' . JIEQI_NOW_TIME . ' ORDER BY pubdate ASC, draftid ASC';
$res = $query->execute($sql);
$aflag = '';
$uparticle = false;
$upobook = false;
while ($row = $query->getRow($res)) {
    if ($aflag != $row['articleid']) {
        $aflag = $row['articleid'];
        $article = $article_handler->get($row['articleid']);
    }
    if (!is_object($article)) {
        continue;
    }
    if (0 < $row['isvip'] && 10 <= intval($article->getVar('issign', 'n'))) {
        $row['isvip'] = 1;
        $upobook = true;
    } else {
        $row['isvip'] = 0;
        $uparticle = true;
    }
    $postvars = $row;
    $attachvars = array();
    jieqi_article_addchapter($postvars, $attachvars, $article);
}
if ($uparticle || $upobook) {
    if ($uparticle == true && $upobook == false) {
        $upflag = -1;
    } else {
        if ($uparticle == false && $upobook == true) {
            $upflag = -2;
        } else {
            $upflag = 0;
        }
    }
    jieqi_article_updateinfo($upflag);
}
jieqi_msgwin(LANG_DO_SUCCESS, LANG_DO_SUCCESS . '<script type="text/javascript"> setTimeout("if(navigator.userAgent.indexOf(\'Firefox\')==-1){window.opener=null;window.open(\'\',\'_self\');window.close();}else{var opened=window.open(\'about:blank\',\'_self\');opened.close();}", 3000); </script>');