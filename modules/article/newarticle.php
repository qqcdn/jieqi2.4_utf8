<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['newarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false);
$ismanager = jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$allowtrans = jieqi_checkpower($jieqiPower['article']['transarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
$allowmodify = jieqi_checkpower($jieqiPower['article']['articlemodify'], $jieqiUsersStatus, $jieqiUsersGroup, true);
jieqi_loadlang('article', JIEQI_MODULE_NAME);
if (!isset($_POST['act'])) {
    $_POST['act'] = 'article';
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs', 'jieqiConfigs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
jieqi_getconfigs('system', 'sites', 'jieqiSites');
jieqi_getconfigs('article', 'action', 'jieqiAction');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
switch ($_POST['act']) {
    case 'newarticle':
        jieqi_checkpost();
        include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
        $options = array('action' => 'add', 'ismanager' => $ismanager, 'allowtrans' => $allowtrans);
        $errors = jieqi_article_articlepcheck($_POST, $options);
        if (empty($errors)) {
            $article = jieqi_article_articleadd($_POST, $options);
            if (is_object($article)) {
                include_once $jieqiModules['article']['path'] . '/include/funaction.php';
                $actions = array('actname' => 'articleadd', 'actnum' => 1);
                jieqi_article_actiondo($actions, $article);
                jieqi_article_updateinfo($article, 'articlenew');
                jieqi_jumppage($article_static_url . '/articlemanage.php?id=' . intval($article->getVar('articleid', 'n')), LANG_DO_SUCCESS, $jieqiLang['article']['article_add_success']);
            } else {
                jieqi_printfail($article);
            }
        } else {
            jieqi_printfail(implode('<br />', $errors));
        }
        break;
    case 'article':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        $jieqiTpl->assign('url_newarticle', $article_static_url . '/newarticle.php?do=submit');
        jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
        $jieqiTpl->assign('sortrows', jieqi_funtoarray('jieqi_htmlstr', $jieqiSort['article']));
        foreach ($jieqiOption['article'] as $k => $v) {
            $jieqiTpl->assign($k, $jieqiOption['article'][$k]);
        }
        if (2 <= floatval(JIEQI_VERSION)) {
            $jieqiTpl->assign('taglimit', intval($jieqiConfigs['article']['taglimit']));
            $tagwords = array();
            $tmpary = preg_split('/\\s+/s', $jieqiConfigs['article']['tagwords']);
            $k = 0;
            foreach ($tmpary as $v) {
                if (0 < strlen($v)) {
                    $tagwords[$k]['name'] = jieqi_htmlstr($v);
                    $k++;
                }
            }
            $jieqiTpl->assign_by_ref('tagwords', $tagwords);
            $jieqiTpl->assign('tagnum', count($tagwords));
        }
        $jieqiTpl->assign('imagetype', $jieqiConfigs['article']['imagetype']);
        $jieqiTpl->assign('allowtrans', intval($allowtrans));
        $jieqiTpl->assign('allowmodify', intval($allowmodify));
        $jieqiTpl->assign('ismanager', intval($ismanager));
        $ismatch = empty($jieqiConfigs['article']['ismatch']) ? 0 : 1;
        $jieqiTpl->assign('ismatch', $ismatch);
        $wholebuy = empty($jieqiConfigs['article']['wholebuy']) ? 0 : intval($jieqiConfigs['article']['wholebuy']);
        $jieqiTpl->assign('wholebuy', $wholebuy);
        if (2 <= floatval(JIEQI_VERSION)) {
            $customsites = array();
            foreach ($jieqiSites as $k => $v) {
                if (!empty($v['custom'])) {
                    $customsites[$k] = $v;
                }
            }
            $jieqiTpl->assign('customsites', jieqi_funtoarray('jieqi_htmlstr', $customsites));
            $jieqiTpl->assign('customsitenum', count($customsites));
            $jieqiTpl->assign('jieqisites', jieqi_funtoarray('jieqi_htmlstr', $jieqiSites));
        }
        $jieqiTpl->assign('authorarea', 1);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/newarticle.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}