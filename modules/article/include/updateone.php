<?php

if (!defined('JIEQI_ROOT_PATH')) {
    exit;
}
include_once $GLOBALS['jieqiModules']['article']['path'] . '/include/collectfunction.php';
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
if (!isset($jieqiDeny['article'])) {
    jieqi_getconfigs('article', 'deny', 'jieqiDeny');
    if (!empty($jieqiDeny['article'])) {
        include_once JIEQI_ROOT_PATH . '/include/checker.php';
        $checker = new JieqiChecker();
    } else {
        $jieqiDeny['article'] = '';
    }
}
$aid = jieqi_textstr($aid);
$url = str_replace('<{articleid}>', $aid, $jieqiCollect['urlarticle']);
if (!empty($jieqiCollect['subarticleid'])) {
    $subarticleid = 0;
    $articleid = $aid;
    $tmpstr = '$subarticleid = ' . $jieqiCollect['subarticleid'] . ';';
    eval($tmpstr);
    $url = str_replace('<{subarticleid}>', $subarticleid, $url);
}
$colary = array('repeat' => 2, 'referer' => $jieqiCollect['referer'], 'proxy_host' => $jieqiCollect['proxy_host'], 'proxy_port' => $jieqiCollect['proxy_port'], 'proxy_user' => $jieqiCollect['proxy_user'], 'proxy_pass' => $jieqiCollect['proxy_pass']);
if (!empty($jieqiCollect['pagecharset'])) {
    $colary['charset'] = $jieqiCollect['pagecharset'];
}
$source = jieqi_urlcontents($url, $colary);
if (empty($source)) {
    echo sprintf($jieqiLang['article']['collect_articleinfo_failure'], $url, $url);
    ob_flush();
    flush();
} else {
    $pregstr = jieqi_collectstoe($jieqiCollect['articletitle']);
    $matchvar = jieqi_cmatchone($pregstr, $source);
    if (!empty($matchvar)) {
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
                $keyword = trim(jieqi_textstr($matchvar));
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
        if (!empty($articleimage) && !in_array(strrchr($articleimage, '.'), array('.gif', '.jpg', '.jpeg', '.bmp', '.png'))) {
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
        $errtext = '';
        if (strlen($articletitle) == 0) {
            $errtext .= $jieqiLang['article']['collect_title_empty'] . '<br />';
        } else {
            if (!jieqi_safestring($articletitle)) {
                $errtext .= $jieqiLang['article']['collect_title_formaterr'] . '<br />';
            }
        }
        if (!empty($jieqiDeny['article']) && isset($checker)) {
            $matchwords = $checker->deny_words($articletitle, $jieqiDeny['article'], true, true);
            if (is_array($matchwords)) {
                $errtext .= sprintf($jieqiLang['article']['collect_deny_articlename'], implode(' ', jieqi_funtoarray('jieqi_htmlchars', $matchwords)));
            }
        }
        if (!empty($errtext)) {
            echo $errtext;
            ob_flush();
            flush();
        } else {
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $criteria = new CriteriaCompo(new Criteria('articlename', $articletitle, '='));
            $res = $article_handler->queryObjects($criteria);
            $article = $article_handler->getObject($res);
            $toid = 0;
            if (!empty($article)) {
                if ($article->getVar('display') != 0) {
                    echo sprintf($jieqiLang['article']['collect_article_notaudit'], $articletitle);
                    ob_flush();
                    flush();
                } else {
                    $uparticleinfo = false;
                    if (!empty($author) && $article->getVar('author', 'n') == '') {
                        $article->setVar('author', $author);
                        $uparticleinfo = true;
                    }
                    if (!empty($sort) && $sort != 'default' && isset($jieqiCollect['sortid'][$sort]) && 0 < intval($jieqiCollect['sortid'][$sort]) && $article->getVar('sortid', 'n') == intval($jieqiCollect['sortid']['default'])) {
                        $article->setVar('sortid', intval($jieqiCollect['sortid'][$sort]));
                        $uparticleinfo = true;
                    }
                    if (!empty($keyword) && $article->getVar('keywords', 'n') == '') {
                        $article->setVar('keywords', $keyword);
                        $uparticleinfo = true;
                    }
                    if (!empty($intro) && $article->getVar('intro', 'n') == '') {
                        $article->setVar('intro', $intro);
                        $uparticleinfo = true;
                    }
                    if (0 < $fullarticle && $article->getVar('fullflag', 'n') < $fullarticle) {
                        $article->setVar('fullflag', $fullarticle);
                        $uparticleinfo = true;
                    }
                    if ($uparticleinfo) {
                        $article_handler->insert($article);
                    }
                    $toid = $article->getVar('articleid');
                }
            } else {
                if ($_REQUEST['notaddnew'] == 1) {
                    echo sprintf($jieqiLang['article']['collect_article_notexists'], $articletitle);
                    ob_flush();
                    flush();
                } else {
                    $updatecode = false;
                    $articlecode = jieqi_getpinyin($articletitle);
                    if (180 < strlen($articlecode)) {
                        $articlecode = substr($articlecode, 0, 180);
                    }
                    if (!preg_match('/^[a-z]/i', $articlecode)) {
                        $articlecode = 'i' . $articlecode;
                    }
                    if (0 < $article_handler->getCount(new Criteria('articlecode', $articlecode, '='))) {
                        $updatecode = true;
                    }
                    $newArticle = $article_handler->create();
                    $newArticle->setVar('siteid', JIEQI_SITE_ID);
                    $newArticle->setVar('postdate', JIEQI_NOW_TIME);
                    $newArticle->setVar('lastupdate', JIEQI_NOW_TIME);
                    $newArticle->setVar('articlename', $articletitle);
                    if (!$updatecode) {
                        $newArticle->setVar('articlecode', $articlecode);
                    }
                    $newArticle->setVar('keywords', trim($keywords));
                    $newArticle->setVar('initial', jieqi_getinitial($articletitle));
                    $newArticle->setVar('agentid', 0);
                    $newArticle->setVar('agent', '');
                    $newArticle->setVar('authorid', 0);
                    $newArticle->setVar('author', $author);
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
                    $newArticle->setVar('words', 0);
                    $newArticle->setVar('fullflag', $fullarticle);
                    if (!empty($sort) && isset($jieqiCollect['sortid'][$sort])) {
                        $sortid = $jieqiCollect['sortid'][$sort];
                    } else {
                        if (isset($jieqiCollect['sortid']['default'])) {
                            $sortid = $jieqiCollect['sortid']['default'];
                        } else {
                            $sortid = 0;
                        }
                    }
                    $newArticle->setVar('sortid', $sortid);
                    $newArticle->setVar('intro', $intro);
                    $newArticle->setVar('notice', '');
                    $newArticle->setVar('setting', '');
                    $newArticle->setVar('power', 0);
                    $newArticle->setVar('unionid', 0);
                    $newArticle->setVar('permission', 0);
                    $newArticle->setVar('firstflag', 0);
                    $imgflag = 0;
                    $articleimage = trim($articleimage);
                    $imgtary = array(1 => '.gif', 2 => '.jpg', 3 => '.jpeg', 4 => '.png', 5 => '.bmp');
                    if (!empty($articleimage)) {
                        $simage_postfix = strrchr(trim(strtolower($articleimage)), '.');
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
                        echo sprintf($jieqiLang['article']['collect_addarticle_failure'], $articletitle);
                        ob_flush();
                        flush();
                    } else {
                        $id = $newArticle->getVar('articleid');
                        if ($updatecode) {
                            $articlecode .= '_' . $id;
                            $article_handler->updatefields(array('articlecode' => $articlecode), new Criteria('articleid', $id, '='));
                        }
                        $package = new JieqiPackage($id);
                        $package->initPackage($newArticle->getVars('n'), true);
                        $toid = $id;
                        if (!empty($articleimage) && 0 < $imgflag) {
                            $colary = array('repeat' => 2, 'referer' => $jieqiCollect['referer'], 'proxy_host' => $jieqiCollect['proxy_host'], 'proxy_port' => $jieqiCollect['proxy_port'], 'proxy_user' => $jieqiCollect['proxy_user'], 'proxy_pass' => $jieqiCollect['proxy_pass']);
                            if (!empty($colary['referer'])) {
                                $colary['referer'] = $url;
                            }
                            $tmpstr = jieqi_urlcontents($articleimage, $colary);
                            if (!empty($tmpstr)) {
                                $imagefile = $package->getDir('imagedir') . '/' . $id . 's' . $imagetype;
                                @jieqi_writefile($imagefile, $tmpstr);
                                @chmod($imagefile, 511);
                            }
                        }
                    }
                }
            }
            if (!empty($toid)) {
                $fromid = $aid;
                $_REQUEST['fromid'] = $fromid;
                $_REQUEST['toid'] = $toid;
                $error_continue = true;
                include $GLOBALS['jieqiModules']['article']['path'] . '/include/collectarticle.php';
            }
        }
    } else {
        echo sprintf($jieqiLang['article']['parse_articleinfo_failure'], $url, $url);
        ob_flush();
        flush();
    }
}
echo '<hr />';