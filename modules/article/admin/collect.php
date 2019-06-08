<?php

define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_loadlang('collect', JIEQI_MODULE_NAME);
jieqi_loadlang('article', JIEQI_MODULE_NAME);
@set_time_limit(0);
@session_write_close();
$self_filename = 'collect.php';
if (!empty($_SERVER['SCRIPT_NAME']) && substr($_SERVER['SCRIPT_NAME'], -4) == '.php') {
    $tmpary = explode('/', $_SERVER['SCRIPT_NAME']);
    if (!empty($tmpary[count($tmpary) - 1])) {
        $self_filename = $tmpary[count($tmpary) - 1];
    }
}
jieqi_getconfigs(JIEQI_MODULE_NAME, 'collectsite');
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs', 'jieqiConfigs');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'option', 'jieqiOption');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort', 'jieqiSort');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
if (!isset($_REQUEST['act'])) {
    $_REQUEST['act'] = 'show';
}
switch ($_REQUEST['act']) {
    case 'newarticle':
        jieqi_checkpost();
        $_REQUEST['articlename'] = trim($_REQUEST['articlename']);
        $_REQUEST['author'] = trim($_REQUEST['author']);
        $_REQUEST['agent'] = trim($_REQUEST['agent']);
        $errtext = '';
        if (strlen($_REQUEST['articlename']) == 0) {
            $errtext .= $jieqiLang['article']['need_article_title'] . '<br />';
        } else {
            if (!jieqi_safestring($_REQUEST['articlename'])) {
                $errtext .= $jieqiLang['article']['limit_article_title'] . '<br />';
            }
        }
        jieqi_getconfigs('article', 'deny', 'jieqiDeny');
        if (!empty($jieqiDeny['article'])) {
            include_once JIEQI_ROOT_PATH . '/include/checker.php';
            $checker = new JieqiChecker();
            $matchwords = $checker->deny_words($_REQUEST['articlename'], $jieqiDeny['article'], true, true);
            if (is_array($matchwords)) {
                $errtext .= sprintf($jieqiLang['article']['article_deny_articlename'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
            }
        }
        if (empty($errtext)) {
            include_once $jieqiModules['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $criteria = new CriteriaCompo(new Criteria('articlename', $_REQUEST['articlename'], '='));
            $res = $article_handler->queryObjects($criteria);
            $articleobj = $article_handler->getObject($res);
            if (is_object($articleobj)) {
                jieqi_msgwin($jieqiLang['article']['article_already_exists'], sprintf($jieqiLang['article']['collect_exists_note'], jieqi_htmlstr($_REQUEST['articlename']), $article_static_url . '/admin/updatecollect.php?act=collect&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&siteid=' . urlencode($_REQUEST['siteid']) . '&fromid=' . urlencode($_REQUEST['fromid']) . '&toid=' . urlencode($articleobj->getVar('articleid', 'n')), jieqi_geturl('article', 'article', $articleobj->getVar('articleid', 'n'), 'info', $articleobj->getVar('articlecode', 'n'))));
                exit;
            }
            $updatecode = false;
            $_REQUEST['articlecode'] = jieqi_getpinyin($_REQUEST['articlename']);
            if (180 < strlen($_REQUEST['articlecode'])) {
                $_REQUEST['articlecode'] = substr($_REQUEST['articlecode'], 0, 180);
            }
            if (!preg_match('/^[a-z]/i', $_REQUEST['articlecode'])) {
                $_REQUEST['articlecode'] = 'i' . $_REQUEST['articlecode'];
            }
            if (0 < $article_handler->getCount(new Criteria('articlecode', $_REQUEST['articlecode'], '='))) {
                $updatecode = true;
            }
            include_once JIEQI_ROOT_PATH . '/class/users.php';
            $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
            $newArticle = $article_handler->create();
            $newArticle->setVar('siteid', JIEQI_SITE_ID);
            $newArticle->setVar('postdate', JIEQI_NOW_TIME);
            $newArticle->setVar('lastupdate', JIEQI_NOW_TIME);
            $newArticle->setVar('articlename', $_REQUEST['articlename']);
            if (!$updatecode) {
                $newArticle->setVar('articlecode', $_REQUEST['articlecode']);
            }
            if (2 <= floatval(JIEQI_VERSION)) {
                include_once JIEQI_ROOT_PATH . '/include/funtag.php';
                $tagary = jieqi_tag_clean(trim($_REQUEST['keywords']));
                $_REQUEST['keywords'] = implode(' ', $tagary);
            }
            $newArticle->setVar('keywords', $_REQUEST['keywords']);
            $newArticle->setVar('initial', jieqi_getinitial($_REQUEST['articlename']));
            $agentobj = false;
            if (!empty($_REQUEST['agent'])) {
                $agentobj = $users_handler->getByname($_REQUEST['agent']);
            }
            if (is_object($agentobj)) {
                $newArticle->setVar('agentid', $agentobj->getVar('uid'));
                $newArticle->setVar('agent', $agentobj->getVar('uname', 'n'));
            } else {
                $newArticle->setVar('agentid', 0);
                $newArticle->setVar('agent', '');
            }
            if (jieqi_checkpower($jieqiPower['article']['transarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
                if (empty($_REQUEST['author']) || !empty($_SESSION['jieqiUserId']) && $_REQUEST['author'] == $_SESSION['jieqiUserName']) {
                    if (!empty($_SESSION['jieqiUserId'])) {
                        $newArticle->setVar('authorid', $_SESSION['jieqiUserId']);
                        $newArticle->setVar('author', $_SESSION['jieqiUserName']);
                    } else {
                        $newArticle->setVar('authorid', 0);
                        $newArticle->setVar('author', '');
                    }
                } else {
                    $newArticle->setVar('author', $_REQUEST['author']);
                    if ($_REQUEST['authorflag']) {
                        $authorobj = $users_handler->getByname($_REQUEST['author']);
                        if (is_object($authorobj)) {
                            $newArticle->setVar('authorid', $authorobj->getVar('uid'));
                        }
                    } else {
                        $newArticle->setVar('authorid', 0);
                    }
                }
            } else {
                if (!empty($_SESSION['jieqiUserId'])) {
                    $newArticle->setVar('authorid', $_SESSION['jieqiUserId']);
                    $newArticle->setVar('author', $_SESSION['jieqiUserName']);
                } else {
                    $newArticle->setVar('authorid', 0);
                    $newArticle->setVar('author', '');
                }
            }
            if (!empty($_SESSION['jieqiUserId'])) {
                $newArticle->setVar('posterid', $_SESSION['jieqiUserId']);
                $newArticle->setVar('poster', $_SESSION['jieqiUserName']);
            } else {
                $newArticle->setVar('posterid', 0);
                $newArticle->setVar('poster', '');
            }
            $newArticle->setVar('lastchapterid', 0);
            $newArticle->setVar('lastchapter', '');
            $newArticle->setVar('lastvolumeid', 0);
            $newArticle->setVar('lastvolume', '');
            $newArticle->setVar('chapters', 0);
            $newArticle->setVar('notice', '');
            $newArticle->setVar('setting', '');
            $_REQUEST['fullflag'] = empty($_REQUEST['fullflag']) ? 0 : 1;
            $newArticle->setVar('fullflag', $_REQUEST['fullflag']);
            $_REQUEST['sortid'] = intval($_REQUEST['sortid']);
            if (!isset($jieqiSort['article'][$_REQUEST['sortid']])) {
                $_REQUEST['sortid'] = 0;
            }
            $newArticle->setVar('sortid', $_REQUEST['sortid']);
            $_REQUEST['typeid'] = intval($_REQUEST['typeid']);
            if (!isset($jieqiSort['article'][$_REQUEST['sortid']]['types'][$_REQUEST['typeid']])) {
                $_REQUEST['typeid'] = 0;
            }
            $newArticle->setVar('typeid', $_REQUEST['typeid']);
            $newArticle->setVar('intro', $_REQUEST['intro']);
            if (isset($_REQUEST['firstflag']) && isset($jieqiOption['article']['firstflag']['items'][$_REQUEST['firstflag']])) {
                $newArticle->setVar('firstflag', $_REQUEST['firstflag']);
            }
            if (isset($_REQUEST['permission']) && isset($jieqiOption['article']['permission']['items'][$_REQUEST['permission']])) {
                $newArticle->setVar('permission', $_REQUEST['permission']);
            }
            if (isset($_REQUEST['isshort']) && isset($jieqiOption['article']['isshort']['items'][$_REQUEST['isshort']])) {
                $newArticle->setVar('isshort', $_REQUEST['isshort']);
            }
            if (isset($_REQUEST['inmatch']) && isset($jieqiOption['article']['inmatch']['items'][$_REQUEST['inmatch']])) {
                $newArticle->setVar('inmatch', $_REQUEST['inmatch']);
            }
            $rgroup = 0;
            if (isset($jieqiSort['article'][$_REQUEST['sortid']]['group']) && 0 <= $jieqiSort['article'][$_REQUEST['sortid']]['group']) {
                $rgroup = intval($jieqiSort['article'][$_REQUEST['sortid']]['group']);
            } else {
                if (isset($_REQUEST['rgroup'])) {
                    $rgroup = intval($_REQUEST['rgroup']);
                }
            }
            if (isset($jieqiOption['article']['rgroup']['items'][$rgroup])) {
                $newArticle->setVar('rgroup', $rgroup);
            }
            $imgflag = 0;
            $_REQUEST['articleimage'] = trim($_REQUEST['articleimage']);
            $imgtary = array(1 => '.gif', 2 => '.jpg', 3 => '.jpeg', 4 => '.png', 5 => '.bmp');
            if (!empty($_REQUEST['articleimage'])) {
                $simage_postfix = strrchr(trim(strtolower($_REQUEST['articleimage'])), '.');
                $tmpvar = intval(array_search($simage_postfix, $imgtary));
                if (0 < $tmpvar) {
                    $imgflag = $imgflag | 1;
                    $imgflag = $imgflag | $tmpvar * 4;
                }
            }
            $newArticle->setVar('imgflag', $imgflag);
            if (jieqi_checkpower($jieqiPower['article']['needcheck'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
                $newArticle->setVar('display', 0);
            } else {
                $newArticle->setVar('display', 1);
            }
            if (!$article_handler->insert($newArticle)) {
                jieqi_printfail($jieqiLang['article']['collect_newarticle_failure']);
            } else {
                $id = $newArticle->getVar('articleid');
                if ($updatecode) {
                    ${$_REQUEST}['articlecode'] .= '_' . $id;
                    $article_handler->updatefields(array('articlecode' => $_REQUEST['articlecode']), new Criteria('articleid', $id, '='));
                }
                if (2 <= floatval(JIEQI_VERSION)) {
                    jieqi_tag_save($tagary, $id, array('tag' => jieqi_dbprefix('article_tag'), 'taglink' => jieqi_dbprefix('article_taglink')));
                }
                include_once $jieqiModules['article']['path'] . '/class/package.php';
                $package = new JieqiPackage($id);
                $package->initPackage($newArticle->getVars('n'), true);
                if (!empty($_REQUEST['articleimage']) && 0 < $imgflag) {
                    include_once JIEQI_ROOT_PATH . '/configs/article/site_' . $jieqiCollectsite[$_REQUEST['siteid']]['config'] . '.php';
                    $colary = array('repeat' => 2, 'referer' => $jieqiCollect['referer'], 'proxy_host' => $jieqiCollect['proxy_host'], 'proxy_port' => $jieqiCollect['proxy_port'], 'proxy_user' => $jieqiCollect['proxy_user'], 'proxy_pass' => $jieqiCollect['proxy_pass']);
                    if (!empty($colary['referer']) && !empty($_REQUEST['collecturl'])) {
                        $colary['referer'] = $_REQUEST['collecturl'];
                    }
                    $tmpstr = jieqi_urlcontents($_REQUEST['articleimage'], $colary);
                    if (!empty($tmpstr)) {
                        $imagefile = $package->getDir('imagedir') . '/' . $id . 's' . $simage_postfix;
                        @jieqi_writefile($imagefile, $tmpstr);
                        @chmod($imagefile, 511);
                    }
                }
                include_once JIEQI_ROOT_PATH . '/admin/header.php';
                $jieqiTpl->assign('article_static_url', $article_static_url);
                $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
                $jieqiTpl->assign('jieqi_contents', '<br />' . jieqi_msgbox(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['collect_newarticle_success'], $article_static_url . '/admin/updatecollect.php?act=collect&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&siteid=' . urlencode($_REQUEST['siteid']) . '&fromid=' . urlencode($_REQUEST['fromid']) . '&toid=' . urlencode($id))) . '<br />');
                include_once JIEQI_ROOT_PATH . '/admin/footer.php';
                exit;
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'collect':
        jieqi_checkpost();
        $errtext = '';
        if (empty($_REQUEST['siteid'])) {
            $errtext .= $jieqiLang['article']['need_source_site'] . '<br />';
        }
        if (empty($_REQUEST['fromid'])) {
            $errtext .= $jieqiLang['article']['need_source_articleid'] . '<br />';
        }
        if (empty($errtext)) {
            if (!empty($_REQUEST['toid'])) {
                header('Location: ' . jieqi_headstr($article_static_url . '/admin/updatecollect.php?act=collect&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&siteid=' . urlencode($_REQUEST['siteid']) . '&fromid=' . urlencode($_REQUEST['fromid']) . '&toid=' . urlencode($_REQUEST['toid'])));
                exit;
            }
            if (array_key_exists($_REQUEST['siteid'], $jieqiCollectsite) && $jieqiCollectsite[$_REQUEST['siteid']]['enable'] == '1') {
                include_once $jieqiModules['article']['path'] . '/include/collectfunction.php';
                include_once JIEQI_ROOT_PATH . '/configs/article/site_' . $jieqiCollectsite[$_REQUEST['siteid']]['config'] . '.php';
                if (empty($jieqiCollect['articletitle'])) {
                    jieqi_printfail($jieqiLang['article']['collect_rule_notfull']);
                }
                $url = str_replace('<{articleid}>', $_REQUEST['fromid'], $jieqiCollect['urlarticle']);
                if (!empty($jieqiCollect['subarticleid'])) {
                    $subarticleid = 0;
                    $articleid = $_REQUEST['fromid'];
                    $tmpstr = '$subarticleid = ' . $jieqiCollect['subarticleid'] . ';';
                    eval($tmpstr);
                    $url = str_replace('<{subarticleid}>', $subarticleid, $url);
                }
                $colary = array('repeat' => 2, 'referer' => isset($jieqiCollect['referer']) ? $jieqiCollect['referer'] : 0, 'proxy_host' => isset($jieqiCollect['proxy_host']) ? $jieqiCollect['proxy_host'] : '', 'proxy_port' => isset($jieqiCollect['proxy_port']) ? $jieqiCollect['proxy_port'] : '', 'proxy_user' => isset($jieqiCollect['proxy_user']) ? $jieqiCollect['proxy_user'] : '', 'proxy_pass' => isset($jieqiCollect['proxy_pass']) ? $jieqiCollect['proxy_pass'] : '');
                if (!empty($jieqiCollect['pagecharset'])) {
                    $colary['charset'] = $jieqiCollect['pagecharset'];
                }
                $source = jieqi_urlcontents($url, $colary);
                if (empty($source)) {
                    jieqi_printfail(sprintf($jieqiLang['article']['collect_url_failure'], $url, jieqi_htmlstr($url)));
                }
                $pregstr = jieqi_collectstoe($jieqiCollect['articletitle']);
                $matchvar = jieqi_cmatchone($pregstr, $source);
                if (empty($matchvar)) {
                    jieqi_printfail(sprintf($jieqiLang['article']['parse_articletitle_failure'], jieqi_htmlstr($url), jieqi_htmlstr($source)));
                }
                $articletitle = trim(jieqi_textstr($matchvar));
                $author = '';
                $pregstr = jieqi_collectstoe($jieqiCollect['author']);
                if (!empty($pregstr)) {
                    $matchvar = jieqi_cmatchone($pregstr, $source);
                    if (!empty($matchvar)) {
                        $author = trim(jieqi_textstr($matchvar));
                    }
                }
                $sort = '';
                $pregstr = jieqi_collectstoe($jieqiCollect['sort']);
                if (!empty($pregstr)) {
                    $matchvar = jieqi_cmatchone($pregstr, $source);
                    if (!empty($matchvar)) {
                        $sort = trim(jieqi_textstr($matchvar));
                    }
                }
                $keyword = '';
                $pregstr = jieqi_collectstoe($jieqiCollect['keyword']);
                if (!empty($pregstr)) {
                    $matchvar = jieqi_cmatchone($pregstr, $source);
                    if (!empty($matchvar)) {
                        $keyword = trim(str_replace(array(',', '，', '、', '　'), ' ', jieqi_textstr($matchvar)));
                    }
                }
                $intro = '';
                $pregstr = jieqi_collectstoe($jieqiCollect['intro']);
                if (!empty($pregstr)) {
                    $matchvar = jieqi_cmatchone($pregstr, $source);
                    if (!empty($matchvar)) {
                        $intro = '    ' . trim(jieqi_textstr($matchvar));
                    }
                }
                $articleimage = '';
                $pregstr = jieqi_collectstoe($jieqiCollect['articleimage']);
                if (substr($pregstr, 0, 4) == 'http') {
                    $articleimage = str_replace('<{articleid}>', $_REQUEST['fromid'], $pregstr);
                    $pregstr = '';
                }
                if (!empty($pregstr)) {
                    $matchvar = jieqi_cmatchone($pregstr, $source);
                    if (!empty($matchvar)) {
                        $articleimage = trim(jieqi_textstr($matchvar));
                    }
                }
                if (!empty($articleimage) && !empty($jieqiCollect['filterimage'])) {
                    if (strpos($articleimage, $jieqiCollect['filterimage']) !== false) {
                        $articleimage = '';
                    }
                }
                if (!empty($articleimage) && !in_array(strrchr(strtolower($articleimage), '.'), array('.gif', '.jpg', '.jpeg', '.bmp', '.png'))) {
                    $articleimage = '';
                }
                if (!empty($articleimage) && strpos($articleimage, 'http') !== 0) {
                    if (substr($articleimage, 0, 1) == '/') {
                        $matches = array();
                        preg_match('/https?:\\/\\/[^\\/]+/is', $url, $matches);
                        if (!empty($matches[0])) {
                            $articleimage = $matches[0] . $articleimage;
                        } else {
                            $articleimage = $jieqiCollect['siteurl'] . $articleimage;
                        }
                    } else {
                        $tmpdir = dirname($url);
                        while (strpos($articleimage, '../') === 0) {
                            $tmpdir = dirname($tmpdir);
                            $articleimage = substr($articleimage, 3);
                        }
                        $articleimage = $tmpdir . '/' . $articleimage;
                    }
                }
                $pregstr = jieqi_collectstoe($jieqiCollect['fullarticle']);
                if (!empty($pregstr)) {
                    $matchvar = jieqi_cmatchone($pregstr, $source);
                    if (!empty($matchvar)) {
                        $fullarticle = 1;
                    } else {
                        $fullarticle = 0;
                    }
                } else {
                    if (!empty($jieqiCollect['defaultfull'])) {
                        $fullarticle = 1;
                    } else {
                        $fullarticle = 0;
                    }
                }
                include_once $jieqiModules['article']['path'] . '/class/article.php';
                $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
                $criteria = new CriteriaCompo(new Criteria('articlename', $articletitle, '='));
                $res = $article_handler->queryObjects($criteria);
                $articleobj = $article_handler->getObject($res);
                if (is_object($articleobj)) {
                    $uparticleinfo = false;
                    if (!empty($author) && $articleobj->getVar('author', 'n') == '') {
                        $articleobj->setVar('author', $author);
                        $uparticleinfo = true;
                    }
                    $defaultsid = isset($jieqiCollect['sortid']['default']) ? intval($jieqiCollect['sortid']['default']) : 0;
                    if (!empty($sort) && $sort != 'default' && isset($jieqiCollect['sortid'][$sort]) && 0 < intval($jieqiCollect['sortid'][$sort]) && $articleobj->getVar('sortid', 'n') == $defaultsid) {
                        $articleobj->setVar('sortid', intval($jieqiCollect['sortid'][$sort]));
                        $uparticleinfo = true;
                    }
                    if (!empty($keyword) && $articleobj->getVar('keywords', 'n') == '') {
                        $articleobj->setVar('keywords', $keyword);
                        $uparticleinfo = true;
                    }
                    if (!empty($intro) && $articleobj->getVar('intro', 'n') == '') {
                        $articleobj->setVar('intro', $intro);
                        $uparticleinfo = true;
                    }
                    if (0 < $fullarticle && $articleobj->getVar('fullflag', 'n') != $fullarticle) {
                        $articleobj->setVar('fullflag', $fullarticle);
                        $uparticleinfo = true;
                    }
                    if ($uparticleinfo) {
                        $article_handler->insert($articleobj);
                    }
                    jieqi_msgwin($jieqiLang['article']['article_already_exists'], sprintf($jieqiLang['article']['collect_exists_note'], jieqi_htmlstr($articletitle), $article_static_url . '/admin/updatecollect.php?act=collect&' . JIEQI_TOKEN_NAME . '=' . urlencode($_REQUEST[JIEQI_TOKEN_NAME]) . '&siteid=' . urlencode($_REQUEST['siteid']) . '&fromid=' . urlencode($_REQUEST['fromid']) . '&toid=' . urlencode($articleobj->getVar('articleid', 'n')), jieqi_geturl('article', 'article', $articleobj->getVar('articleid', 'n'), 'info')));
                    exit;
                }
                include_once JIEQI_ROOT_PATH . '/admin/header.php';
                $jieqiTpl->assign('article_static_url', $article_static_url);
                $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
                jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');
                include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
                $article_form = new JieqiThemeForm($jieqiLang['article']['collect_add_new'], 'newarticle', $article_static_url . '/admin/' . $self_filename);
                if (!empty($sort) && isset($jieqiCollect['sortid'][$sort])) {
                    $sortid = $jieqiCollect['sortid'][$sort];
                } else {
                    if (isset($jieqiCollect['sortid']['default'])) {
                        $sortid = $jieqiCollect['sortid']['default'];
                    } else {
                        $sortid = 0;
                    }
                }
                $sort_select = new JieqiFormSelect($jieqiLang['article']['table_article_sortid'], 'sortid', $sortid);
                $sort_select->setExtra('onchange="showtypes(this)"');
                $typeselect = '';
                $typeshtml = '<script type="text/javascript">' . "\r\n" . '  function showtypes(obj){' . "\r\n" . '    var typeselect=document.getElementById("typeselect");' . "\r\n" . '    typeselect.innerHTML="";' . "\r\n" . '						' . "\r\n" . '';
                $firstsort = true;
                foreach ($jieqiSort['article'] as $key => $val) {
                    $tmpstr = '';
                    if (!empty($val['layer'])) {
                        for ($i = 0; $i < $val['layer']; $i++) {
                            $tmpstr .= '&nbsp;&nbsp;';
                        }
                        $tmpstr .= '├';
                    }
                    $tmpstr .= $val['caption'];
                    $sort_select->addOption($key, $tmpstr);
                    if (!empty($val['types'])) {
                        $typeshtml .= 'if(obj.options[obj.selectedIndex].value == ' . $key . ') typeselect.innerHTML=\'<select class="select" size="1" name="typeid" id="typeid">';
                        foreach ($val['types'] as $kt => $vt) {
                            $typeshtml .= '<option value="' . $kt . '">' . $vt . '</option>';
                        }
                        $typeshtml .= '</select>\';' . "\r\n" . '';
                        if ($sortid == $key || !isset($jieqiSort['article'][$sortid]) && $firstsort) {
                            $typeselect .= '<select class="select" size="1" name="typeid" id="typeid">';
                            foreach ($val['types'] as $kt => $vt) {
                                $typeselect .= '<option value="' . $kt . '">' . $vt . '</option>';
                            }
                            $typeselect .= '</select>';
                        }
                    }
                    $firstsort = false;
                }
                $typeshtml .= '}' . "\r\n" . '</script>';
                $typeselect = '<span id="typeselect" name="typeselect">' . $typeselect . '</span>';
                $sort_select->setDescription($typeselect . $typeshtml . $jieqiLang['article']['collect_sort_guide'] . jieqi_htmlstr($sort));
                $article_form->addElement($sort_select, true);
                $article_form->addElement(new JieqiFormText($jieqiLang['article']['table_article_articlename'], 'articlename', 30, 50, $articletitle), true);
                $keywords = new JieqiFormText($jieqiLang['article']['table_article_keywords'], 'keywords', 30, 50, $keyword);
                $keywords->setDescription($jieqiLang['article']['note_keywords']);
                $article_form->addElement($keywords);
                jieqi_getconfigs('article', 'option', 'jieqiOption');
                if (jieqi_checkpower($jieqiPower['article']['transarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true)) {
                    $authorname = new JieqiFormText($jieqiLang['article']['table_article_author'], 'author', 30, 30, $author);
                    $article_form->addElement($authorname);
                    $authorflag = new JieqiFormRadio($jieqiLang['article']['article_author_flag'], 'authorflag', $jieqiOption['article']['authorflag']['default']);
                    foreach ($jieqiOption['article']['authorflag']['items'] as $k => $v) {
                        $authorflag->addOption($k, $v);
                    }
                    $article_form->addElement($authorflag);
                }
                $tmpvar = '';
                $agent = new JieqiFormText($jieqiLang['article']['table_article_agent'], 'agent', 30, 30, $tmpvar);
                $agent->setDescription($jieqiLang['article']['note_agent']);
                $article_form->addElement($agent);
                $permission = new JieqiFormRadio($jieqiLang['article']['table_article_permission'], 'permission', '1');
                foreach ($jieqiOption['article']['permission']['items'] as $k => $v) {
                    $permission->addOption($k, $v);
                }
                $article_form->addElement($permission);
                $firstflag = new JieqiFormRadio($jieqiLang['article']['table_article_firstflag'], 'firstflag', '0');
                foreach ($jieqiOption['article']['firstflag']['items'] as $k => $v) {
                    $firstflag->addOption($k, $v);
                }
                $article_form->addElement($firstflag);
                $fullflag = new JieqiFormRadio($jieqiLang['article']['table_article_fullflag'], 'fullflag', $fullarticle);
                foreach ($jieqiOption['article']['fullflag']['items'] as $k => $v) {
                    $fullflag->addOption($k, $v);
                }
                $article_form->addElement($fullflag);
                $article_form->addElement(new JieqiFormText($jieqiLang['article']['article_image_url'], 'articleimage', 60, 250, $articleimage));
                $article_form->addElement(new JieqiFormTextArea($jieqiLang['article']['table_article_intro'], 'intro', $intro, 6, 60));
                $article_form->addElement(new JieqiFormHidden('act', 'newarticle'));
                $article_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
                $article_form->addElement(new JieqiFormHidden('siteid', $_REQUEST['siteid']));
                $article_form->addElement(new JieqiFormHidden('fromid', $_REQUEST['fromid']));
                $article_form->addElement(new JieqiFormHidden('collecturl', $url));
                $article_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['article']['collect_next_button'], 'submit'));
                $jieqiTpl->assign('jieqi_contents', '<br />' . $article_form->render(JIEQI_FORM_MAX) . '<br />');
                include_once JIEQI_ROOT_PATH . '/admin/footer.php';
            } else {
                jieqi_printfail($jieqiLang['article']['not_support_collectsite']);
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/admin/header.php';
        $jieqiTpl->assign('article_static_url', $article_static_url);
        $jieqiTpl->assign('article_dynamic_url', $article_dynamic_url);
        include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
        $collect_form = new JieqiThemeForm($jieqiLang['article']['article_collect_title'], 'frmcollect', $article_static_url . '/admin/' . $self_filename);
        if (empty($_REQUEST['siteid'])) {
            $_REQUEST['siteid'] = 1;
        }
        $siteobj = new JieqiFormSelect($jieqiLang['article']['collect_source_site'], 'siteid', $_REQUEST['siteid']);
        foreach ($jieqiCollectsite as $k => $v) {
            $siteobj->addOption($k, $v['name']);
        }
        $collect_form->addElement($siteobj);
        if (empty($_REQUEST['fromid'])) {
            $_REQUEST['fromid'] = '';
        }
        $collect_form->addElement(new JieqiFormText($jieqiLang['article']['source_article_id'], 'fromid', 30, 100, $_REQUEST['fromid']), true);
        if (empty($_REQUEST['toid'])) {
            $_REQUEST['toid'] = '';
        }
        $toidobj = new JieqiFormText($jieqiLang['article']['target_article_id'], 'toid', 30, 11, $_REQUEST['toid']);
        $toidobj->setDescription($jieqiLang['article']['target_article_note']);
        $collect_form->addElement($toidobj);
        $collect_form->addElement(new JieqiFormHidden('act', 'collect'));
        $collect_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $collect_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['article']['collect_next_button'], 'submit'));
        $jieqiTpl->assign('jieqi_contents', '<br />' . $collect_form->render(JIEQI_FORM_MAX) . '<br />');
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}