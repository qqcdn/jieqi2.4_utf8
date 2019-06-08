<?php

if (!defined('JIEQI_ROOT_PATH')) {
    exit;
}
if (empty($_REQUEST['siteid']) || empty($_REQUEST['fromid']) || empty($_REQUEST['toid'])) {
    exit;
}
$_REQUEST['fromid'] = trim($_REQUEST['fromid']);
$_REQUEST['toid'] = intval($_REQUEST['toid']);
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
include_once $jieqiModules['article']['path'] . '/class/package.php';
include_once $jieqiModules['article']['path'] . '/include/collectfunction.php';
if (!isset($jieqiConfigs['system'])) {
    jieqi_getconfigs('system', 'configs');
}
include_once JIEQI_ROOT_PATH . '/include/checker.php';
$retflag = 0;
$retchapinfo = array();
$retlogs = array();
$jieqi_collect_time = time();
jieqi_getconfigs(JIEQI_MODULE_NAME, 'collectsite');
if (array_key_exists($_REQUEST['siteid'], $jieqiCollectsite) && $jieqiCollectsite[$_REQUEST['siteid']]['enable'] == '1') {
    include_once JIEQI_ROOT_PATH . '/configs/article/site_' . $jieqiCollectsite[$_REQUEST['siteid']]['config'] . '.php';
    if (empty($jieqiCollect['articletitle'])) {
        jieqi_printfail($jieqiLang['article']['collect_rule_notfull']);
    }
    $colary = array('repeat' => 2, 'referer' => $jieqiCollect['referer'], 'proxy_host' => $jieqiCollect['proxy_host'], 'proxy_port' => $jieqiCollect['proxy_port'], 'proxy_user' => $jieqiCollect['proxy_user'], 'proxy_pass' => $jieqiCollect['proxy_pass']);
    if (!empty($jieqiCollect['pagecharset'])) {
        $colary['charset'] = $jieqiCollect['pagecharset'];
    }
    $indexlink = '';
    if (strpos($jieqiCollect['urlindex'], '<{indexlink}>') !== false && !empty($jieqiCollect['indexlink'])) {
        $url = str_replace('<{articleid}>', $_REQUEST['fromid'], $jieqiCollect['urlarticle']);
        if (!empty($jieqiCollect['subarticleid'])) {
            $subarticleid = 0;
            $articleid = $_REQUEST['fromid'];
            $tmpstr = '$subarticleid = ' . $jieqiCollect['subarticleid'] . ';';
            eval($tmpstr);
            $url = str_replace('<{subarticleid}>', $subarticleid, $url);
        }
        $source = jieqi_urlcontents($url, $colary);
        if (empty($source)) {
            jieqi_printfail(sprintf($jieqiLang['article']['collect_articleinfo_failure'], $url, $url));
        }
        $pregstr = jieqi_collectstoe($jieqiCollect['indexlink']);
        if (!empty($pregstr)) {
            $matchvar = jieqi_cmatchone($pregstr, $source);
            if (0 < strlen($matchvar)) {
                $indexlink = trim(jieqi_textstr($matchvar));
            }
        }
    }
    if (0 < strlen($indexlink)) {
        $tmpstr = str_replace('<{indexlink}>', $indexlink, $jieqiCollect['urlindex']);
    } else {
        $tmpstr = $jieqiCollect['urlindex'];
    }
    $url = str_replace('<{articleid}>', $_REQUEST['fromid'], $tmpstr);
    if (!empty($jieqiCollect['subarticleid'])) {
        $subarticleid = 0;
        $articleid = $_REQUEST['fromid'];
        $tmpstr = '$subarticleid = ' . $jieqiCollect['subarticleid'] . ';';
        eval($tmpstr);
        $url = str_replace('<{subarticleid}>', $subarticleid, $url);
    }
    $source = jieqi_urlcontents($url, $colary);
    if (empty($source)) {
        if ($error_continue == true) {
            echo sprintf($jieqiLang['article']['collect_index_failure'], $url, $url);
            ob_flush();
            flush();
            $retflag = 3;
        } else {
            jieqi_printfail(sprintf($jieqiLang['article']['collect_index_failure'], $url, $url));
        }
    } else {
        $newCollect = array();
        $newCollect['chapter'] = $jieqiCollect['chapter'];
        $newCollect['volume'] = $jieqiCollect['volume'];
        $newCollect['chapterid'] = $jieqiCollect['chapterid'];
        $newCollect['content'] = $jieqiCollect['content'];
        $pregstr = jieqi_collectstoe($jieqiCollect['chapter']);
        $matchvar = jieqi_cmatchall($pregstr, $source, PREG_OFFSET_CAPTURE);
        if (empty($matchvar)) {
            if ($error_continue == true) {
                echo sprintf($jieqiLang['article']['parse_chapter_failure'], $url, $url);
                ob_flush();
                flush();
                $retflag = 3;
            } else {
                jieqi_printfail(sprintf($jieqiLang['article']['parse_chapter_failure'], $url, $url));
            }
        } else {
            if (is_array($matchvar)) {
                $chapterary = $matchvar;
            } else {
                $chapterary = array();
            }
            $pregstr = jieqi_collectstoe($jieqiCollect['chapterid']);
            $matchvar = jieqi_cmatchall($pregstr, $source, PREG_OFFSET_CAPTURE);
            if (is_array($matchvar)) {
                $chapteridary = $matchvar;
            } else {
                $chapteridary = array();
            }
            $volumeary = array();
            $pregstr = jieqi_collectstoe($jieqiCollect['volume']);
            if (!empty($pregstr)) {
                $matchvar = jieqi_cmatchall($pregstr, $source, PREG_OFFSET_CAPTURE);
                if (is_array($matchvar)) {
                    $volumeary = $matchvar;
                } else {
                    $volumeary = array();
                }
            }
            $fromrows = array();
            $i = 0;
            $j = 0;
            $k = 0;
            $chapternum = count($chapterary);
            $volumenum = count($volumeary);
            $volumename = '';
            while ($j < $chapternum || $k < $volumenum) {
                if ($j < $chapternum) {
                    $a = $chapterary[$j][1];
                } else {
                    $a = 99999999;
                }
                if ($k < $volumenum) {
                    $b = $volumeary[$k][1];
                } else {
                    $b = 99999999;
                }
                if ($a < $b) {
                    $tmpvar = trim(jieqi_textstr($chapterary[$j][0]));
                    if ($tmpvar != '') {
                        $fromrows[$i]['title'] = $tmpvar;
                        $fromrows[$i]['type'] = 0;
                        $fromrows[$i]['id'] = $chapteridary[$j][0];
                        $fromrows[$i]['vname'] = $volumename;
                        $i++;
                    }
                    $j++;
                } else {
                    $tmpvar = trim(jieqi_textstr($volumeary[$k][0]));
                    if ($tmpvar != '') {
                        $fromrows[$i]['title'] = $tmpvar;
                        $fromrows[$i]['type'] = 1;
                        $fromrows[$i]['id'] = 0;
                        $fromrows[$i]['vname'] = $tmpvar;
                        $volumename = $tmpvar;
                        $i++;
                    }
                    $k++;
                }
            }
            include_once $jieqiModules['article']['path'] . '/class/article.php';
            $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
            $article = $article_handler->get($_REQUEST['toid']);
            if (!is_object($article)) {
                jieqi_printfail($jieqiLang['article']['article_not_exists']);
            }
            $myarticlename = $article->getVar('articlename', 'n');
            if ($article->getVar('fullflag') == 1) {
                $fromisfull = true;
            } else {
                $fromisfull = false;
            }
            include_once $jieqiModules['article']['path'] . '/class/chapter.php';
            $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
            $criteria = new CriteriaCompo(new Criteria('articleid', $article->getVar('articleid'), '='));
            $criteria->setSort('chapterorder');
            $criteria->setOrder('ASC');
            $chapter_handler->queryObjects($criteria, true);
            $torows = array();
            $i = 0;
            $volumename = '';
            while ($row = $chapter_handler->getRow()) {
                $torows[$i]['title'] = trim(jieqi_textstr($row['chaptername']));
                $torows[$i]['type'] = $row['chaptertype'];
                if ($row['chaptertype'] == 0) {
                    $torows[$i]['vname'] = $volumename;
                } else {
                    $torows[$i]['vname'] = $torows[$i]['title'];
                    $volumename = $torows[$i]['title'];
                }
                $i++;
            }
            $checkvolume = false;
            $vorder = 0;
            $corder = 0;
            foreach ($fromrows as $key => $value) {
                $fromrows[$key]['vname'] = trim(str_replace($myarticlename, '', $fromrows[$key]['vname']));
                if (0 < $value['type']) {
                    $fromrows[$key]['title'] = $fromrows[$key]['vname'];
                    $vorder++;
                    $corder = 0;
                } else {
                    $corder++;
                    if (1 < $vorder && $corder == 1) {
                        $tempary = jieqi_splitchapter($fromrows[$key]['vname'] . ' ' . $fromrows[$key]['title']);
                        if (1 < $tempary['vid'] && $tempary['cid'] == 1) {
                            $checkvolume = true;
                        }
                    }
                }
            }
            foreach ($torows as $key => $value) {
                $torows[$key]['vname'] = trim(str_replace($myarticlename, '', $torows[$key]['vname']));
                if (0 < $value['type']) {
                    $torows[$key]['title'] = $torows[$key]['vname'];
                }
            }
            $fromnum = count($fromrows);
            $tonum = count($torows);
            $maxchapterorder = $tonum;
            if ($tonum == 0) {
                $fp = 0;
                $tp = 0;
            } else {
                $fp = 0;
                $tp = 0;
                while ($fp < $fromnum && $tp < $tonum) {
                    if (jieqi_equichapter($fromrows[$fp]['title'], $torows[$tp]['title']) && $fromrows[$fp]['type'] == $torows[$tp]['type']) {
                        $fp++;
                        $tp++;
                    } else {
                        if ($fp < $fromnum - 1 && $tp < $tonum - 1 && jieqi_equichapter($fromrows[$fp + 1]['title'], $torows[$tp + 1]['title']) && $fromrows[$fp + 1]['type'] == $torows[$tp + 1]['type']) {
                            $retchapinfo[] = array('fchapter' => $fromrows[$fp]['type'] == 0 ? $fromrows[$fp]['vname'] . ' ' . $fromrows[$fp]['title'] : $fromrows[$fp]['vname'], 'tchapter' => $torows[$tp]['type'] == 0 ? $torows[$tp]['vname'] . ' ' . $torows[$tp]['title'] : $torows[$tp]['vname']);
                            $fp += 2;
                            $tp += 2;
                        } else {
                            $retchapinfo[] = array('fchapter' => $fromrows[$fp]['type'] == 0 ? $fromrows[$fp]['vname'] . ' ' . $fromrows[$fp]['title'] : $fromrows[$fp]['vname'], 'tchapter' => $torows[$tp]['type'] == 0 ? $torows[$tp]['vname'] . ' ' . $torows[$tp]['title'] : $torows[$tp]['vname']);
                            break;
                        }
                    }
                }
                if ($tp < $tonum) {
                    $j = $tp;
                    $k = $tp;
                    while ($j < $tonum) {
                        while ($k < $fromnum) {
                            if (!jieqi_equichapter($fromrows[$k]['title'], $torows[$j]['title']) || $fromrows[$k]['type'] != $torows[$j]['type']) {
                                if ($k < $fromnum - 1 && $j < $tonum - 1 && jieqi_equichapter($fromrows[$k + 1]['title'], $torows[$j + 1]['title']) && $fromrows[$k + 1]['type'] == $torows[$j + 1]['type']) {
                                    $k++;
                                    $j++;
                                    break;
                                } else {
                                    $k++;
                                }
                            } else {
                                break;
                            }
                        }
                        if ($k < $fromnum) {
                            $j++;
                        } else {
                            break;
                        }
                    }
                    if ($fromnum <= $k) {
                        $j = $tp;
                        $mn = $fromnum - $j;
                        $j = $fromnum;
                        $m = 1;
                        while ($m <= $mn) {
                            if (jieqi_equichapter($fromrows[$fromnum - $m]['title'], $torows[$tonum - 1]['title']) && $fromrows[$fromnum - $m]['type'] == $torows[$tonum - 1]['type'] && ($checkvolume == false || $fromrows[$fromnum - $m]['vname'] == $torows[$tonum - 1]['vname'])) {
                                $j = $fromnum - $m;
                                break;
                            }
                            $m++;
                        }
                        $setting = jieqi_unserialize($article->getVar('setting', 'n'));
                        if (!is_array($setting)) {
                            $setting = array();
                        }
                        if ($j < $fromnum && ($jieqiCollect['autoclear'] != 1 || $setting['fromsite'] != $_REQUEST['siteid'] || $setting['fromarticle'] != $_REQUEST['fromid'])) {
                            $fp = $j + 1;
                            $tp = $tonum;
                        } else {
                            if ($jieqiCollect['autoclear'] == 1 && $fromisfull == false) {
                                echo sprintf($jieqiLang['article']['article_collect_clean'], jieqi_htmlstr($article->getVar('articlename')));
                                $oldchapters = $article->getVar('chapters');
                                $article->setVar('lastchapter', '');
                                $article->setVar('lastchapterid', 0);
                                $article->setVar('lastvolume', '');
                                $article->setVar('lastvolumeid', 0);
                                $article->setVar('chapters', 0);
                                $article->setVar('words', 0);
                                $article_handler->insert($article);
                                $package = new JieqiPackage($_REQUEST['toid']);
                                $package->delete();
                                $package->initPackage($article->getVars('n'), true);
                                unset($package);
                                $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['toid'], '='));
                                $chapter_handler->delete($criteria);
                                unset($criteria);
                                $fp = 0;
                                $tp = 0;
                                $torows = array();
                                $tonum = 0;
                                $maxchapterorder = 0;
                            } else {
                                $fp = $fromnum;
                                $tp = $fromnum;
                                if ($error_continue == true) {
                                    $errchapter = '';
                                    foreach ($retchapinfo as $v) {
                                        $errchapter .= $v['fchapter'] . ' => ' . $v['tchapter'] . '<br />';
                                    }
                                    echo sprintf($jieqiLang['article']['collect_cant_update'], $errchapter, $article_static_url . '/articlemanage.php?id=' . $_REQUEST['toid'], $article_static_url . '/articleclean.php?id=' . $_REQUEST['toid'] . '&collecturl=' . urlencode($article_static_url . '/admin/updatecollect.php?siteid=' . $_REQUEST['siteid'] . '&fromid=' . $_REQUEST['fromid'] . '&toid=' . $_REQUEST['toid']), $article_static_url . '/admin/collect.php');
                                    ob_flush();
                                    flush();
                                }
                                $retflag = 4;
                            }
                        }
                    }
                }
            }
            $upcorders = array();
            if ($fp < $fromnum && $tp <= $tonum) {
                $chapterlink = '';
                if (strpos($jieqiCollect['urlchapter'], '<{chapterlink}>') !== false && !empty($jieqiCollect['chapterlink'])) {
                    $pregstr = jieqi_collectstoe($jieqiCollect['chapterlink']);
                    if (!empty($pregstr)) {
                        $matchvar = jieqi_cmatchone($pregstr, $source);
                        if (!empty($matchvar)) {
                            $chapterlink = trim(jieqi_textstr($matchvar));
                        }
                    }
                }
                if (!isset($jieqiConfigs['article'])) {
                    jieqi_getconfigs('article', 'configs');
                }
                if (!isset($jieqiConfigs['system'])) {
                    jieqi_getconfigs('system', 'configs');
                }
                include_once JIEQI_ROOT_PATH . '/lib/text/texttypeset.php';
                $texttypeset = new TextTypeset();
                include_once $jieqiModules['article']['path'] . '/class/chapter.php';
                $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
                $words = $article->getVar('words');
                $lastchapter = $article->getVar('lastchapter', 'n');
                $lastchapterid = $article->getVar('lastchapterid', 'n');
                $lastvolume = $article->getVar('lastvolume', 'n');
                $lastvolumeid = $article->getVar('lastvolumeid', 'n');
                $lastchapterorder = $tp + 1;
                if (isset($jieqiConfigs['system']['postreplacewords']) && !empty($jieqiConfigs['system']['postreplacewords'])) {
                }
                echo str_repeat(' ', 4096);
                $tmpvar = $fromnum - $fp;
                echo sprintf($jieqiLang['article']['collect_chapter_doing'], jieqi_htmlstr($article->getVar('articlename')), $tmpvar);
                ob_flush();
                flush();
                $c = 1;
                $k = $fp;
                $q = $tp;
                while ($k < $fromnum) {
                    if ($q < $tonum && jieqi_equichapter($fromrows[$k]['title'], $torows[$q]['title']) && $fromrows[$k]['type'] == $torows[$q]['type']) {
                        $k++;
                        $q++;
                        continue;
                    } else {
                        if ($k < $fromnum - 1 && $q < $tonum - 1 && jieqi_equichapter($fromrows[$k + 1]['title'], $torows[$q + 1]['title']) && $fromrows[$k + 1]['type'] == $torows[$q + 1]['type']) {
                            $k += 2;
                            $q += 2;
                            continue;
                        }
                    }
                    if (isset($chaptercontent)) {
                        unset($chaptercontent);
                    }
                    if (isset($url)) {
                        unset($url);
                    }
                    if ($fromrows[$k]['type'] == 0) {
                        if (0 < strlen($indexlink)) {
                            $tmpstr = str_replace('<{indexlink}>', $indexlink, $jieqiCollect['urlchapter']);
                        } else {
                            $tmpstr = $jieqiCollect['urlchapter'];
                        }
                        if (!empty($chapterlink)) {
                            $tmpstr = str_replace('<{chapterlink}>', $chapterlink, $tmpstr);
                        }
                        $url = str_replace('<{articleid}>', $_REQUEST['fromid'], $tmpstr);
                        $url = str_replace('<{chapterid}>', $fromrows[$k]['id'], $url);
                        if (!empty($jieqiCollect['subarticleid'])) {
                            $subarticleid = 0;
                            $articleid = $_REQUEST['fromid'];
                            $chapterid = $fromrows[$k]['id'];
                            $tmpstr = '$subarticleid = ' . $jieqiCollect['subarticleid'] . ';';
                            eval($tmpstr);
                            $url = str_replace('<{subarticleid}>', $subarticleid, $url);
                        }
                        if (!empty($jieqiCollect['subchapterid'])) {
                            $subchapterid = 0;
                            $articleid = $_REQUEST['fromid'];
                            $chapterid = $fromrows[$k]['id'];
                            $tmpstr = '$subchapterid = ' . $jieqiCollect['subchapterid'] . ';';
                            eval($tmpstr);
                            $url = str_replace('<{subchapterid}>', $subchapterid, $url);
                        }
                        $chaptercontent = jieqi_urlcontents($url, $colary);
                        if (!empty($colary['referer'])) {
                            $colary['referer'] = $url;
                        }
                        if (!$chaptercontent) {
                            $chaptercontent = '';
                        }
                    } else {
                        $chaptercontent = '';
                    }
                    $pregstr = jieqi_collectstoe($jieqiCollect['content']);
                    $matchvar = jieqi_cmatchone($pregstr, $chaptercontent);
                    if (!empty($matchvar)) {
                        $chaptercontent = $matchvar;
                        if (3 < strlen($chaptercontent) && strlen($chaptercontent) < 200) {
                            $urlcontent = trim($chaptercontent);
                            $matches = array();
                            preg_match('/\\<script[^\\<\\>]*src=(\'|")([^\\<\\>\'"]*)(\'|")[^\\<\\>]*\\>/is', $urlcontent, $matches);
                            if (!empty($matches[2])) {
                                $urlcontent = $matches[2];
                            }
                            $tmpstr = strtolower(strrchr($urlcontent, '.'));
                            if ($tmpstr == '.txt' || $tmpstr == '.js') {
                                if (strpos($urlcontent, 'http') !== 0) {
                                    if (substr($urlcontent, 0, 1) == '/') {
                                        $urlmatches = array();
                                        preg_match('/https?:\\/\\/[^\\/]+/is', $url, $urlmatches);
                                        if (!empty($urlmatches[0])) {
                                            $urlcontent = $urlmatches[0] . $urlcontent;
                                        } else {
                                            $urlcontent = $jieqiCollect['siteurl'] . $urlcontent;
                                        }
                                    } else {
                                        $tmpdir = dirname($url);
                                        while (strpos($urlcontent, '../') === 0) {
                                            $tmpdir = dirname($tmpdir);
                                            $urlcontent = substr($urlcontent, 3);
                                        }
                                        $urlcontent = $tmpdir . '/' . $urlcontent;
                                    }
                                }
                                $newcontent = jieqi_urlcontents($urlcontent, $colary);
                                if (!empty($newcontent)) {
                                    $matches = array();
                                    preg_match('/document.write\\((\'|")(.*)(\'|")\\);/is', $newcontent, $matches);
                                    if (!empty($matches[2])) {
                                        $chaptercontent = $matches[2];
                                    }
                                }
                            }
                        }
                        if (!empty($jieqiCollect['contentfilter'])) {
                            $filterary = explode("\n", $jieqiCollect['contentfilter']);
                            $repfrom = array();
                            foreach ($filterary as $filterstr) {
                                $filterstr = trim($filterstr);
                                if (!empty($filterstr)) {
                                    if (preg_match('/^\\/[^\\/\\\\]*(?:\\\\.[^\\/\\\\]*)*\\/[imsu]*$/is', $filterstr)) {
                                        $repfrom[] = $filterstr;
                                    } else {
                                        $repfrom[] = '/' . jieqi_pregconvert($filterstr) . '/is';
                                    }
                                }
                            }
                            $repto = '';
                            if (!empty($jieqiCollect['contentreplace'])) {
                                $repto = explode("\n", str_replace("\r\n", "\n", $jieqiCollect['contentreplace']));
                            }
                            if (0 < count($repfrom)) {
                                $chaptercontent = preg_replace($repfrom, $repto, $chaptercontent);
                            }
                        }
                        if (isset($jieqiConfigs['system']['postreplacewords']) && !empty($jieqiConfigs['system']['postreplacewords'])) {
                            $checker = new JieqiChecker();
                            $checker->replace_words($chaptercontent, $jieqiConfigs['system']['postreplacewords']);
                        }
                    } else {
                        $chaptercontent = '';
                    }
                    if ($fromrows[$k]['type'] == 0 && strlen(trim($chaptercontent)) == 0) {
                        echo sprintf($jieqiLang['article']['chapter_collect_failure'], $c, jieqi_htmlstr($fromrows[$k]['title']), $url, $url);
                        ob_flush();
                        flush();
                    } else {
                        $imagecontentary = array();
                        $infoary = array();
                        $attachnum = 0;
                        $attachinfo = '';
                        if ($jieqiCollect['collectimage'] == 1) {
                            $matches = array();
                            preg_match_all('/\\<img[^\\<\\>]+src=[\'"]?((https?:\\/\\/|www\\.)?[a-z0-9\\/\\-_+=.~!%@?#%&;:$\\│]+(\\.gif|\\.jpg|\\.jpeg|\\.png|\\.bmp))[^\\<\\>]*\\>/is', $chaptercontent, $matches);
                            if (!empty($matches[1])) {
                                $imageurls = array();
                                foreach ($matches[1] as $s => $v) {
                                    $imageurls[] = $v;
                                    $imageurl = $v;
                                    if (strpos($imageurl, 'http') !== 0) {
                                        if (substr($imageurl, 0, 1) == '/') {
                                            $urlmatches = array();
                                            preg_match('/https?:\\/\\/[^\\/]+/is', $url, $urlmatches);
                                            if (!empty($urlmatches[0])) {
                                                $imageurl = $urlmatches[0] . $imageurl;
                                            } else {
                                                $imageurl = $jieqiCollect['siteurl'] . $imageurl;
                                            }
                                        } else {
                                            $tmpdir = dirname($url);
                                            while (strpos($imageurl, '../') === 0) {
                                                $tmpdir = dirname($tmpdir);
                                                $imageurl = substr($imageurl, 3);
                                            }
                                            $imageurl = $tmpdir . '/' . $imageurl;
                                        }
                                    }
                                    $img_colary = $colary;
                                    $img_colary['charset'] = 'image';
                                    $imagecontentary[$attachnum] = jieqi_urlcontents($imageurl, $img_colary);
                                    if ($s == 0 && empty($imagecontentary[$attachnum])) {
                                        break;
                                    }
                                    $infoary[$attachnum] = array('name' => basename($imageurl), 'class' => 'image', 'postfix' => substr(strrchr($imageurl, '.'), 1), 'size' => strlen($imagecontentary[$attachnum]));
                                    include_once $jieqiModules['article']['path'] . '/class/articleattachs.php';
                                    $attachs_handler = JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
                                    $newAttach = $attachs_handler->create();
                                    $newAttach->setVar('articleid', $_REQUEST['toid']);
                                    $newAttach->setVar('chapterid', 0);
                                    $newAttach->setVar('name', $infoary[$attachnum]['name']);
                                    $newAttach->setVar('class', $infoary[$attachnum]['class']);
                                    $newAttach->setVar('postfix', $infoary[$attachnum]['postfix']);
                                    $newAttach->setVar('size', $infoary[$attachnum]['size']);
                                    $newAttach->setVar('hits', 0);
                                    $newAttach->setVar('needexp', 0);
                                    $newAttach->setVar('uptime', $jieqi_collect_time);
                                    if ($attachs_handler->insert($newAttach)) {
                                        $attachid = $newAttach->getVar('attachid');
                                        $infoary[$attachnum]['attachid'] = $attachid;
                                    } else {
                                        $infoary[$attachnum]['attachid'] = 0;
                                    }
                                    $attachnum++;
                                }
                                if (0 < $attachnum) {
                                    $chaptercontent = str_replace($imageurls, '', $chaptercontent);
                                    $attachinfo = serialize($infoary);
                                }
                            }
                        } else {
                            $matches = array();
                            preg_match_all('/\\<img[^\\<\\>]+src=[\'"]?((https?:\\/\\/|www\\.)?[a-z0-9\\/\\-_+=.~!%@?#%&;:$\\│]+(\\.gif|\\.jpg|\\.jpeg|\\.png|\\.bmp))[^\\<\\>]*\\>/is', $chaptercontent, $matches);
                            if (!empty($matches[1])) {
                                $imageurls = array();
                                foreach ($matches[1] as $s => $v) {
                                    $imageurl = $v;
                                    if (strpos($imageurl, 'http') !== 0) {
                                        if (substr($imageurl, 0, 1) == '/') {
                                            $urlmatches = array();
                                            preg_match('/https?:\\/\\/[^\\/]+/is', $url, $urlmatches);
                                            if (!empty($urlmatches[0])) {
                                                $imageurl = $urlmatches[0] . $imageurl;
                                            } else {
                                                $imageurl = $jieqiCollect['siteurl'] . $imageurl;
                                            }
                                        } else {
                                            $tmpdir = dirname($url);
                                            while (strpos($imageurl, '../') === 0) {
                                                $tmpdir = dirname($tmpdir);
                                                $imageurl = substr($imageurl, 3);
                                            }
                                            $imageurl = $tmpdir . '/' . $imageurl;
                                        }
                                        $chaptercontent = str_replace($v, $imageurl, $chaptercontent);
                                    }
                                }
                            }
                        }
                        $chaptercontent = jieqi_textstr($chaptercontent, true);
                        $chaptercontent = $texttypeset->doTypeset($chaptercontent);
                        if ($q < $tonum) {
                            $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['toid']));
                            $criteria->add(new Criteria('chapterorder', $q, '>'));
                            $chapter_handler->updatefields('chapterorder=chapterorder+1', $criteria);
                            unset($criteria);
                        }
                        $newChapter = $chapter_handler->create();
                        $newChapter->setVar('siteid', JIEQI_SITE_ID);
                        $chapterwords = jieqi_strwords($chaptercontent);
                        $newChapter->setVar('articleid', $_REQUEST['toid']);
                        $newChapter->setVar('articlename', $article->getVar('articlename', 'n'));
                        $newChapter->setVar('volumeid', 0);
                        if (!empty($_SESSION['jieqiUserId'])) {
                            $newChapter->setVar('posterid', $_SESSION['jieqiUserId']);
                            $newChapter->setVar('poster', $_SESSION['jieqiUserName']);
                        } else {
                            $newChapter->setVar('posterid', 0);
                            $newChapter->setVar('poster', '');
                        }
                        $newChapter->setVar('postdate', $jieqi_collect_time);
                        $newChapter->setVar('lastupdate', $jieqi_collect_time);
                        $newChapter->setVar('chaptername', $fromrows[$k]['title']);
                        $newChapter->setVar('chapterorder', $q + 1);
                        $upcorders[] = $q + 1;
                        $newChapter->setVar('words', $chapterwords);
                        $newChapter->setVar('chaptertype', $fromrows[$k]['type']);
                        $newChapter->setVar('saleprice', 0);
                        $newChapter->setVar('salenum', 0);
                        $newChapter->setVar('totalcost', 0);
                        $newChapter->setVar('attachment', $attachinfo);
                        if (0 < $chapterwords) {
                            $newChapter->setVar('summary', jieqi_substr($chaptercontent, 0, 500, '..'));
                        } else {
                            $newChapter->setVar('summary', '');
                        }
                        if (strlen($chaptercontent) == 0 && 0 < $attachnum && $fromrows[$k]['type'] == 0) {
                            $newChapter->setVar('isimage', 1);
                        }
                        $newChapter->setVar('isimage', 0);
                        $newChapter->setVar('isvip', 0);
                        $newChapter->setVar('power', 0);
                        $newChapter->setVar('display', 0);
                        if (!$chapter_handler->insert($newChapter)) {
                            jieqi_printfail($jieqiLang['article']['add_chapter_failure']);
                        } else {
                            $newid = $newChapter->getVar('chapterid');
                            if ($fromrows[$k]['type'] == 1) {
                                jieqi_save_achapterc($_REQUEST['toid'], $newid, $chaptercontent, 0, 1);
                                $lastvolume = $fromrows[$k]['title'];
                                $lastvolumeid = $newid;
                            } else {
                                jieqi_save_achapterc($_REQUEST['toid'], $newid, $chaptercontent, 0, 0);
                                $lastchapter = $fromrows[$k]['title'];
                                $lastchapterid = $newid;
                                $words += $chapterwords;
                            }
                            if (0 < $attachnum && is_object($attachs_handler)) {
                                $attachs_handler->execute('UPDATE ' . jieqi_dbprefix('article_attachs') . ' SET chapterid=' . $newChapter->getVar('chapterid') . ' WHERE articleid=' . $_REQUEST['toid'] . ' AND chapterid=0');
                                $attachdir = jieqi_uploadpath($jieqiConfigs['article']['attachdir'], 'article');
                                if (!file_exists($attachdir)) {
                                    jieqi_createdir($attachdir);
                                }
                                $attachdir .= jieqi_getsubdir($newChapter->getVar('articleid'));
                                if (!file_exists($attachdir)) {
                                    jieqi_createdir($attachdir);
                                }
                                $attachdir .= '/' . $newChapter->getVar('articleid');
                                if (!file_exists($attachdir)) {
                                    jieqi_createdir($attachdir);
                                }
                                $attachdir .= '/' . $newChapter->getVar('chapterid');
                                if (!file_exists($attachdir)) {
                                    jieqi_createdir($attachdir);
                                }
                                if ($jieqiCollect['imagetranslate'] && function_exists('gd_info') && JIEQI_MODULE_VTYPE != '' && JIEQI_MODULE_VTYPE != 'Free') {
                                    $canimagetrans = true;
                                } else {
                                    $canimagetrans = false;
                                }
                                $make_image_water = false;
                                if ($jieqiCollect['addimagewater'] == 1) {
                                    if (strpos($jieqiConfigs['article']['attachwimage'], '/') === false && strpos($jieqiConfigs['article']['attachwimage'], '\\') === false) {
                                        $water_image_file = $jieqiModules['article']['path'] . '/images/' . $jieqiConfigs['article']['attachwimage'];
                                    } else {
                                        $water_image_file = $jieqiConfigs['article']['attachwimage'];
                                    }
                                    if (is_file($water_image_file)) {
                                        $make_image_water = true;
                                        include_once JIEQI_ROOT_PATH . '/lib/image/imagewater.php';
                                    }
                                }
                                foreach ($infoary as $s => $v) {
                                    $imgattach_save_path = $attachdir . '/' . $infoary[$s]['attachid'] . '.' . $infoary[$s]['postfix'];
                                    @jieqi_writefile($imgattach_save_path, $imagecontentary[$s]);
                                    $imagetype = '';
                                    if (preg_match('/\\.(jpg|jpeg|gif|png)$/i', $imgattach_save_path, $itmatches)) {
                                        $imagetype = strtolower($itmatches[1]);
                                    }
                                    if ($imagetype == 'jpg') {
                                        $imagetype = 'jpeg';
                                    }
                                    if ($canimagetrans && !empty($imagetype)) {
                                        $funname = 'imagecreatefrom' . $imagetype;
                                        $imageres = $funname($imgattach_save_path);
                                        $imagewidth = imagesx($imageres);
                                        $imageheight = imagesy($imageres);
                                        if (!preg_match('/^#[a-f0-9]{6}$/i', $jieqiCollect['imagebgcolor'], $tmpmatches)) {
                                            $tmpary = array();
                                            $tmpvar = imagecolorat($imageres, 1, 1);
                                            $tmpary[$tmpvar] = isset($tmpary[$tmpvar]) ? $tmpary[$tmpvar] + 1 : 1;
                                            $tmpvar = imagecolorat($imageres, 1, $imageheight - 1);
                                            $tmpary[$tmpvar] = isset($tmpary[$tmpvar]) ? $tmpary[$tmpvar] + 1 : 1;
                                            $tmpvar = imagecolorat($imageres, $imagewidth - 1, 1);
                                            $tmpary[$tmpvar] = isset($tmpary[$tmpvar]) ? $tmpary[$tmpvar] + 1 : 1;
                                            $tmpvar = imagecolorat($imageres, $imagewidth - 1, $imageheight - 1);
                                            $tmpary[$tmpvar] = isset($tmpary[$tmpvar]) ? $tmpary[$tmpvar] + 1 : 1;
                                            $tmpvar = imagecolorat($imageres, 1, floor($imageheight / 2));
                                            $tmpary[$tmpvar] = isset($tmpary[$tmpvar]) ? $tmpary[$tmpvar] + 1 : 1;
                                            $tmpvar = imagecolorat($imageres, $imagewidth - 1, floor($imageheight / 2));
                                            $tmpary[$tmpvar] = isset($tmpary[$tmpvar]) ? $tmpary[$tmpvar] + 1 : 1;
                                            arsort($tmpary);
                                            reset($tmpary);
                                            $imagebgcolor = key($tmpary);
                                        } else {
                                            $imagebgcolor = imagecolorclosest($imageres, hexdec(substr($jieqiCollect['imagebgcolor'], 1, 2)), hexdec(substr($jieqiCollect['imagebgcolor'], 3, 2)), hexdec(substr($jieqiCollect['imagebgcolor'], 1, 5)));
                                        }
                                        $filterwater = false;
                                        if (!empty($jieqiCollect['imageareaclean'])) {
                                            $imageareaary = explode('|', $jieqiCollect['imageareaclean']);
                                            foreach ($imageareaary as $area) {
                                                $xyary = explode(',', $area);
                                                if (4 <= count($xyary)) {
                                                    $x1 = intval(trim($xyary[0]));
                                                    if ($x1 < 0) {
                                                        $x1 = $imagewidth + $x1;
                                                    }
                                                    $y1 = intval(trim($xyary[1]));
                                                    if ($y1 < 0) {
                                                        $y1 = $imageheight + $y1;
                                                    }
                                                    $x2 = intval(trim($xyary[2]));
                                                    if ($x2 <= 0) {
                                                        $x2 = $imagewidth + $x2;
                                                    }
                                                    $y2 = intval(trim($xyary[3]));
                                                    if ($y2 <= 0) {
                                                        $y2 = $imageheight + $y2;
                                                    }
                                                    imagefilledrectangle($imageres, $x1, $y1, $x2, $y2, $imagebgcolor);
                                                    $filterwater = true;
                                                }
                                            }
                                        }
                                        if (!empty($jieqiCollect['imagecolorclean'])) {
                                            $imagecolorary = explode('|', $jieqiCollect['imagecolorclean']);
                                            foreach ($imagecolorary as $fcolor) {
                                                $fcolor = trim($fcolor);
                                                if (preg_match('/^#[a-f0-9]{6}$/i', $fcolor, $tmpmatches)) {
                                                    $filtercolor = imagecolorexact($imageres, hexdec(substr($fcolor, 1, 2)), hexdec(substr($fcolor, 3, 2)), hexdec(substr($fcolor, 5, 2)));
                                                    if (0 <= $filtercolor) {
                                                        $cindexary = imagecolorsforindex($imageres, $imagebgcolor);
                                                        imagecolorset($imageres, $filtercolor, $cindexary['red'], $cindexary['green'], $cindexary['blue']);
                                                        $filterwater = true;
                                                    }
                                                }
                                            }
                                        }
                                        if ($filterwater) {
                                            $funname = 'image' . $imagetype;
                                            $funname($imageres, $imgattach_save_path);
                                        }
                                        if ($make_image_water && preg_match('/\\.(gif|jpg|jpeg|png)$/i', $imgattach_save_path)) {
                                            $img = new ImageWater();
                                            $img->save_image_file = $imgattach_save_path;
                                            $img->codepage = JIEQI_SYSTEM_CHARSET;
                                            $img->wm_image_pos = $jieqiConfigs['article']['attachwater'];
                                            $img->wm_image_name = $water_image_file;
                                            $img->wm_image_transition = $jieqiConfigs['article']['attachwtrans'];
                                            $img->jpeg_quality = $jieqiConfigs['article']['attachwquality'];
                                            $img->create($imgattach_save_path);
                                            unset($img);
                                        }
                                    }
                                    @chmod($imgattach_save_path, 511);
                                }
                            }
                        }
                        unset($newChapter);
                        $article->setVar('chapters', $maxchapterorder + 1);
                        $article->setVar('words', $words);
                        $article->setVar('freewords', $words);
                        $article->setVar('lastupdate', $jieqi_collect_time);
                        $article->setVar('freetime', $jieqi_collect_time);
                        $article->setVar('lastchapter', $lastchapter);
                        $article->setVar('lastchapterid', $lastchapterid);
                        $article->setVar('lastvolume', $lastvolume);
                        $article->setVar('lastvolumeid', $lastvolumeid);
                        $article_handler->insert($article);
                        $lastchapterorder = $q + 1;
                        $maxchapterorder++;
                        for ($n = $tonum; $q < $n; $n--) {
                            $torows[$n] = $torows[$n - 1];
                        }
                        $torows[$q]['title'] = $fromrows[$k]['title'];
                        $torows[$q]['type'] = $fromrows[$k]['type'];
                        $tonum++;
                        $q++;
                        echo $c . '.' . jieqi_htmlstr($fromrows[$k]['title']) . ' ';
                        ob_flush();
                        flush();
                    }
                    $k++;
                    $c++;
                }
                $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['toid']));
                $criteria->add(new Criteria('chapterorder', $lastchapterorder, '<'));
                $criteria->add(new Criteria('chaptertype', 1, '='));
                $criteria->setSort('chapterorder');
                $criteria->setOrder('DESC');
                $criteria->setLimit(1);
                $chapter_handler->queryObjects($criteria, true);
                $tmpchapter = $chapter_handler->getObject();
                if (is_object($tmpchapter)) {
                    $article->setVar('lastvolume', $tmpchapter->getVar('chaptername', 'n'));
                    $article->setVar('lastvolumeid', $tmpchapter->getVar('chapterid', 'n'));
                } else {
                    $article->setVar('lastvolume', '');
                    $article->setVar('lastvolumeid', 0);
                }
                unset($tmpchapter);
                unset($criteria);
                $setting = jieqi_unserialize($article->getVar('setting', 'n'));
                if (!is_array($setting)) {
                    $setting = array();
                }
                $setting['fromsite'] = $_REQUEST['siteid'];
                $setting['fromarticle'] = $_REQUEST['fromid'];
                $article->setVar('setting', serialize($setting));
                $article_handler->insert($article);
                $k = $c - 1;
                echo $jieqiLang['article']['chapter_collect_success'];
                ob_flush();
                flush();
                echo $jieqiLang['article']['collect_create_readfile'];
                ob_flush();
                flush();
                include_once $jieqiModules['article']['path'] . '/include/repack.php';
                $upordernum = count($upcorders);
                if (count($upcorders) < $maxchapterorder) {
                    sort($upcorders);
                    for ($j = 0; $j < $upordernum; $j++) {
                        if (1 < $upcorders[$j] && ($j == 0 || $upcorders[$j] - 1 != $upcorders[$j - 1])) {
                            $upcorders[] = $upcorders[$j] - 1;
                        }
                        if ($upcorders[$j] < $maxchapterorder && ($upordernum - 1 <= $j || $upcorders[$j] + 1 != $upcorders[$j + 1])) {
                            $upcorders[] = $upcorders[$j] + 1;
                        }
                    }
                    sort($upcorders);
                }
                if ($maxchapterorder <= count($upcorders)) {
                    article_repack($_REQUEST['toid'], array('makeopf' => 1, 'makehtml' => $jieqiConfigs['article']['makehtml'], 'maketxtjs' => $jieqiConfigs['article']['maketxtjs'], 'makezip' => $jieqiConfigs['article']['makezip'], 'makefull' => $jieqiConfigs['article']['makefull'], 'maketxtfull' => $jieqiConfigs['article']['maketxtfull'], 'makeumd' => $jieqiConfigs['article']['makeumd'], 'makejar' => $jieqiConfigs['article']['makejar']), 1);
                } else {
                    article_repack($_REQUEST['toid'], array('makeopf' => 1), 1);
                    $package = new JieqiPackage($_REQUEST['toid']);
                    $package->loadOPF();
                    $upoids = array();
                    foreach ($upcorders as $corder) {
                        $upoids[] = $corder;
                        if ($jieqiConfigs['article']['maketxtjs']) {
                            $package->makeTxtjs($corder);
                        }
                    }
                    if (!empty($upoids)) {
                        $package->makeRead('editchapter', implode('|', $upoids));
                    }
                    article_repack($_REQUEST['toid'], array('makezip' => $jieqiConfigs['article']['makezip'], 'makefull' => $jieqiConfigs['article']['makefull'], 'maketxtfull' => $jieqiConfigs['article']['maketxtfull'], 'makeumd' => $jieqiConfigs['article']['makeumd'], 'makejar' => $jieqiConfigs['article']['makejar']), 1);
                }
                if (0 < $jieqiConfigs['article']['fakestatic']) {
                    include_once $jieqiModules['article']['path'] . '/include/funstatic.php';
                    article_update_static('articleedit', $_REQUEST['toid'], 0);
                }
                $retflag = 1;
            } else {
                if ($retflag == 0) {
                    $retflag = 2;
                }
            }
        }
    }
} else {
    $retflag = 0;
    jieqi_printfail($jieqiLang['article']['not_support_collectsite']);
}