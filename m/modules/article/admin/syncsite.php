<?php

define('JIEQI_USE_GZIP', '0');
define('JIEQI_MODULE_NAME', 'article');
require_once '../../../global.php';
jieqi_getconfigs('article', 'power');
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false);
jieqi_loadlang('site', 'article');
jieqi_getconfigs('article', 'configs');
jieqi_getconfigs('system', 'sites', 'jieqiSites');
if ($_REQUEST['confirm'] != 1) {
    include_once JIEQI_ROOT_PATH . '/admin/header.php';
    $siterows = jieqi_funtoarray('jieqi_htmlstr', $jieqiSites);
    $jieqiTpl->assign_by_ref('siterows', $siterows);
    $jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'] . '/templates/admin/syncsite.html';
    include_once JIEQI_ROOT_PATH . '/admin/footer.php';
}
if (empty($_REQUEST['siteid']) || empty($jieqiSites[$_REQUEST['siteid']])) {
    jieqi_printfail($jieqiLang['article']['site_no_siteid']);
}
$_REQUEST['siteid'] = intval($_REQUEST['siteid']);
if (empty($jieqiSites[$_REQUEST['siteid']]['interface']) || !preg_match('/^\\w+$/i', $jieqiSites[$_REQUEST['siteid']]['interface']) || !is_dir($jieqiModules['article']['path'] . '/apic/' . $jieqiSites[$_REQUEST['siteid']]['interface'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
@ignore_user_abort(true);
@set_time_limit(3600);
@session_write_close();
if (empty($_REQUEST['order'])) {
    $_REQUEST['order'] = 1;
}
$_REQUEST['order'] = intval($_REQUEST['order']);
if ($_REQUEST['order'] < 1) {
    $_REQUEST['order'] = 1;
}
if (empty($_REQUEST['errstop'])) {
    $_REQUEST['errstop'] = 1;
}
$_REQUEST['errstop'] = intval($_REQUEST['errstop']);
if ($_REQUEST['errstop'] < 1) {
    $_REQUEST['errstop'] = 1;
}
if (empty($_REQUEST['errnum'])) {
    $_REQUEST['errnum'] = 0;
}
$_REQUEST['errnum'] = intval($_REQUEST['errnum']);
if ($_REQUEST['errnum'] < 0) {
    $_REQUEST['errnum'] = 0;
}
if (isset($_REQUEST['articleid'])) {
    $_REQUEST['articleid'] = intval($_REQUEST['articleid']);
}
if (isset($_REQUEST['sourceid'])) {
    $_REQUEST['sourceid'] = intval($_REQUEST['sourceid']);
}
$_REQUEST['unionid'] = empty($jieqiSites[$_REQUEST['siteid']]['unionid']) ? 0 : intval($jieqiSites[$_REQUEST['siteid']]['unionid']);
echo str_repeat(' ', 4096);
echo $jieqiLang['article']['site_articlelist_start'] . '<br />';
ob_flush();
flush();
include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
include_once $jieqiModules['article']['path'] . '/apic/' . $jieqiSites[$_REQUEST['siteid']]['interface'] . '/apiclient.php';
include_once $jieqiModules['article']['path'] . '/include/funsync.php';
include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
jieqi_includedb();
$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
if (!empty($_REQUEST['articleid']) && empty($_REQUEST['sourceid'])) {
    $sql = 'SELECT sourceid FROM ' . jieqi_dbprefix('article_article') . ' WHERE articleid = ' . intval($_REQUEST['articleid']) . ' LIMIT 0, 1';
    $query->execute($sql);
    $arow = $query->getRow();
    if (is_array($arow)) {
        $_REQUEST['sourceid'] = intval($arow['sourceid']);
    }
}
if (!empty($_REQUEST['syncid']) && !empty($_REQUEST['order']) || !empty($_REQUEST['sourceid'])) {
    $without_update = false;
    $check_allchapters = true;
    if (!empty($_REQUEST['sourceid'])) {
        $sourceid = intval($_REQUEST['sourceid']);
    } else {
        $_REQUEST['syncid'] = intval($_REQUEST['syncid']);
        $_REQUEST['order'] = intval($_REQUEST['order']);
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_syncsite') . ' WHERE syncid = ' . $_REQUEST['syncid'];
        $query->execute($sql);
        $logrow = $query->getRow();
        if (!$logrow) {
            jieqi_printfail($jieqiLang['article']['site_log_notexists']);
        }
        $synccfile = JIEQI_ROOT_PATH . '/files/article/syncsite' . jieqi_getsubdir($_REQUEST['syncid']) . '/' . $_REQUEST['syncid'] . $jieqi_file_postfix['txt'];
        if (!is_file($synccfile)) {
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_syncsite') . ' where syncid = ' . $_REQUEST['syncid'];
            $query->execute($sql);
            jieqi_printfail($jieqiLang['article']['site_cfile_notexists']);
        }
        $crows = file($synccfile);
        $tmax = count($crows);
        if ($tmax < $_REQUEST['order']) {
            $prows = array();
            $prows['finishnum'] = $tmax;
            $prows['finishtime'] = time();
            $prows['issuccess'] = $tmax == $logrow['articlenum'] ? 1 : 2;
            $sql = $query->makeupsql(jieqi_dbprefix('article_syncsite'), $prows, 'UPDATE', array('syncid' => $_REQUEST['syncid']));
            $query->execute($sql);
            if (!empty($jieqiSites[$_REQUEST['siteid']]['syncokgo'])) {
                jieqi_jumppage($jieqiSites[$_REQUEST['siteid']]['syncokgo'], LANG_DO_SUCCESS, $jieqiLang['article']['site_allarticle_finish']);
            } else {
                jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['site_allarticle_success'], $tmax));
            }
        }
        $tmpary = explode(' ', trim($crows[$_REQUEST['order'] - 1]));
        if (count($tmpary) < 2 || !is_numeric($tmpary[0]) || !is_numeric($tmpary[1])) {
            $sql = 'DELETE FROM ' . jieqi_dbprefix('article_syncsite') . ' where syncid = ' . $_REQUEST['syncid'];
            $query->execute($sql);
            jieqi_delfile($synccfile);
            jieqi_printfail($jieqiLang['article']['site_cfile_formaterror']);
        }
        $sourceid = intval($tmpary[0]);
        $sourceupdate = intval($tmpary[1]);
        if (0 < $_REQUEST['unionid']) {
            $sql = 'SELECT articleid, articlename, lastupdate, fullflag, display FROM ' . jieqi_dbprefix('article_article') . ' WHERE unionid = ' . $_REQUEST['unionid'] . ' AND sourceid = ' . $sourceid . ' LIMIT 0, 1';
        } else {
            $sql = 'SELECT articleid, articlename, lastupdate, fullflag, display FROM ' . jieqi_dbprefix('article_article') . ' WHERE siteid = ' . $_REQUEST['siteid'] . ' AND sourceid = ' . $sourceid . ' LIMIT 0, 1';
        }
        $query->execute($sql);
        $arow = $query->getRow();
        if (is_array($arow) && (0 < $sourceupdate && $sourceupdate <= $arow['lastupdate'] || $sourceupdate == 0 && 0 < $arow['fullflag'])) {
            $without_update = true;
            echo sprintf($jieqiLang['article']['site_article_notneed'], jieqi_htmlstr($arow['articlename'])) . '<br />';
            ob_flush();
            flush();
        }
    }
    if (!$without_update) {
        $jieqiapi = new JieqiApiClient($jieqiSites[$_REQUEST['siteid']]);
        $params = array('aid' => $sourceid);
        $ret = $jieqiapi->api('articleinfo', $params);
        $errstr = false;
        if ($ret['ret'] < 0) {
            $errstr = jieqi_htmlstr($ret['msg']);
        } else {
            if (!is_array($ret['msg'])) {
                $errstr = $jieqiLang['article']['site_return_formaterror'];
            }
        }
        if (isset($ret['msg'][0])) {
            $ret['msg'] = $ret['msg'][0];
        }
        $sync_article = $jieqiapi->format('articleinfo', $ret['msg']);
        if (empty($sync_article['articlename'])) {
            $errstr = $jieqiLang['article']['site_return_formaterror'];
        }
        if ($errstr === false) {
            unset($ret);
            include_once JIEQI_ROOT_PATH . '/header.php';
            echo sprintf($jieqiLang['article']['site_article_begin'], jieqi_htmlstr($sync_article['articlename'])) . '<br />';
            ob_flush();
            flush();
            $sync_article['postdate'] = empty($sync_article['postdate']) || JIEQI_NOW_TIME < $sync_article['postdate'] ? JIEQI_NOW_TIME : intval($sync_article['postdate']);
            $article_lastupdate = empty($sync_article['lastupdate']) ? 0 : intval($sync_article['lastupdate']);
            if (JIEQI_NOW_TIME < $article_lastupdate) {
                $article_lastupdate = JIEQI_NOW_TIME;
            }
            $sync_article['lastupdate'] = 1 < $article_lastupdate ? $article_lastupdate - 1 : JIEQI_NOW_TIME;
            if (!empty($jieqiSites[$_REQUEST['siteid']]['display'])) {
                $sync_article['display'] = 1;
            }
            $myarticle = jieqi_sync_articleinfo($sync_article);
            if (!is_object($myarticle)) {
                jieqi_printfail($jieqiLang['article']['site_savearticle_failure']);
            }
            $myarticleid = intval($myarticle->getVar('articleid', 'n'));
            $params = array('aid' => $sourceid);
            $ret = $jieqiapi->api('articlechapter', $params);
            $errstr = false;
            if ($ret['ret'] < 0) {
                $errstr = jieqi_htmlstr($ret['msg']);
            } else {
                if (!is_array($ret['msg'])) {
                    $errstr = $jieqiLang['article']['site_return_formaterror'];
                }
            }
            if ($errstr === false) {
                $sync_chapters = array();
                $k = 0;
                foreach ($ret['msg'] as $chapterone) {
                    $sync_chapters[$k] = $jieqiapi->format('articlechapter', $chapterone);
                    $sync_chapters[$k]['aid'] = $myarticleid;
                    $sync_chapters[$k]['articleid'] = $myarticleid;
                    $sync_chapters[$k]['sourceid'] = $sourceid;
                    $sync_chapters[$k]['chapterorder'] = $k + 1;
                    $sync_chapters[$k]['chaptercontent'] = '';
                    $sync_chapters[$k]['postdate'] = empty($sync_chapters[$k]['postdate']) || JIEQI_NOW_TIME < $sync_chapters[$k]['postdate'] ? JIEQI_NOW_TIME : intval($sync_chapters[$k]['postdate']);
                    $sync_chapters[$k]['lastupdate'] = empty($sync_chapters[$k]['lastupdate']) ? 0 : intval($sync_chapters[$k]['lastupdate']);
                    if (JIEQI_NOW_TIME < $sync_chapters[$k]['lastupdate']) {
                        $sync_chapters[$k]['lastupdate'] = JIEQI_NOW_TIME;
                    }
                    $k++;
                }
                $old_chapters = array();
                $map_cids = array();
                $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_chapter') . ' WHERE articleid = ' . $myarticleid . ' ORDER BY chapterorder ASC';
                $query->execute($sql);
                $k = 0;
                while ($row = $query->getRow()) {
                    if ($row['sourcecid'] == 0) {
                        $cidx = 'i' . $row['chapterid'];
                    } else {
                        if (0 < $row['chaptertype']) {
                            $cidx = 'v' . $row['sourcecid'];
                        } else {
                            $cidx = 'c' . $row['sourcecid'];
                        }
                    }
                    $map_cids[$cidx] = array('key' => $k, 'check' => 0, 'chapterid' => $row['chapterid'], 'isvip' => $row['isvip'], 'chaptertype' => $row['chaptertype']);
                    $old_chapters[$k] = $row;
                    $k++;
                }
                $add_chaps = array();
                $up_chaps = array();
                $del_chaps = array();
                $up_corders = array();
                $up_chapternum = 0;
                foreach ($sync_chapters as $sk => $sv) {
                    $cidx = 0 < $sv['chaptertype'] ? 'v' . intval($sv['sourcecid']) : 'c' . intval($sv['sourcecid']);
                    if (isset($map_cids[$cidx])) {
                        if ($old_chapters[$map_cids[$cidx]['key']]['lastupdate'] < $sv['lastupdate'] || $sv['chaptername'] != $old_chapters[$map_cids[$cidx]['key']]['chaptername']) {
                            $up_chaps[$map_cids[$cidx]['key']] = $sk;
                            $up_chapternum++;
                            $up_corders[] = $sv['chapterorder'];
                        }
                        $map_cids[$cidx]['check'] = 1;
                    } else {
                        $add_chaps[] = $sk;
                        $up_chapternum++;
                        $up_corders[] = $sv['chapterorder'];
                    }
                }
                foreach ($map_cids as $v) {
                    if ($v['check'] == 0) {
                        $del_chaps[] = $v;
                        $up_chapternum++;
                    }
                }
                $reterror = '';
                if (empty($reterror) && !empty($add_chaps)) {
                    if ($article_lastupdate == 0) {
                        $article_lastupdate = JIEQI_NOW_TIME;
                    }
                    $batcount = count($add_chaps);
                    $batorder = 0;
                    $batmax = 10;
                    $batkey = 0;
                    $scripts = array();
                    $params = array();
                    $skeys = array();
                    $ckeys = array();
                    foreach ($add_chaps as $sk) {
                        if ($sync_chapters[$sk]['chaptertype'] == 0) {
                            $scripts[$batkey] = 'chaptercontent';
                            $params[$batkey] = array('aid' => $sourceid, 'cid' => $sync_chapters[$sk]['sourcecid']);
                            $skeys[$batkey] = $sk;
                            $batkey++;
                        }
                        $ckeys[] = $sk;
                        $batorder++;
                        if ($batmax <= $batkey || $batcount <= $batorder) {
                            if (0 < count($scripts)) {
                                $ret = $jieqiapi->api($scripts, $params);
                                foreach ($ret as $rk => $rv) {
                                    $errstr = false;
                                    if ($ret[$rk]['ret'] < 0) {
                                        $errstr = jieqi_htmlstr($ret[$rk]['msg']);
                                    } else {
                                        if (!is_array($ret[$rk]['msg'])) {
                                            $errstr = 'fotmat:' . $jieqiLang['article']['site_return_formaterror'];
                                        }
                                    }
                                    if ($errstr === false) {
                                        $chapterary = $jieqiapi->format('chaptercontent', $ret[$rk]['msg']);
                                        $sync_chapters[$skeys[$rk]]['chaptercontent'] = $chapterary['chaptercontent'];
                                    } else {
                                        jieqi_printfail($errstr);
                                    }
                                }
                                if ($errstr !== false) {
                                    break;
                                }
                            }
                            foreach ($ckeys as $ck) {
                                if (isset($sync_chapters[$ck]['lastupdate']) && $sync_chapters[$ck]['lastupdate'] == 0) {
                                    $sync_chapters[$ck]['lastupdate'] = JIEQI_NOW_TIME;
                                }
                                $retc = jieqi_sync_chapternew($sync_chapters[$ck], $myarticle);
                                if (empty($retc)) {
                                    $errstr = $retc;
                                    break;
                                }
                            }
                            if ($errstr !== false) {
                                break;
                            }
                            $batkey = 0;
                            $scripts = array();
                            $params = array();
                            $skeys = array();
                            $ckeys = array();
                        }
                    }
                }
                if (empty($reterror) && !empty($up_chaps)) {
                    $batcount = count($up_chaps);
                    $batorder = 0;
                    $batmax = 10;
                    $batkey = 0;
                    $scripts = array();
                    $params = array();
                    $skeys = array();
                    $okeys = array();
                    $ckeys = array();
                    foreach ($up_chaps as $ok => $sk) {
                        if ($sync_chapters[$sk]['chaptertype'] == 0) {
                            $scripts[$batkey] = 'chaptercontent';
                            $params[$batkey] = array('aid' => $sourceid, 'cid' => $sync_chapters[$sk]['sourcecid']);
                            $skeys[$batkey] = $sk;
                            $okeys[$batkey] = $ok;
                            $batkey++;
                        }
                        $ckeys[$ok] = $sk;
                        $batorder++;
                        if ($batmax <= $batkey || $batcount <= $batorder) {
                            if (0 < count($scripts)) {
                                $ret = $jieqiapi->api($scripts, $params);
                                foreach ($ret as $rk => $rv) {
                                    $errstr = false;
                                    if ($ret[$rk]['ret'] < 0) {
                                        $errstr = jieqi_htmlstr($ret[$rk]['msg']);
                                    } else {
                                        if (!is_array($ret[$rk]['msg'])) {
                                            $errstr = 'fotmat:' . $jieqiLang['article']['site_return_formaterror'];
                                        }
                                    }
                                    if ($errstr === false) {
                                        $chapterary = $jieqiapi->format('chaptercontent', $ret[$rk]['msg']);
                                        $sync_chapters[$skeys[$rk]]['chaptercontent'] = $chapterary['chaptercontent'];
                                    } else {
                                        jieqi_printfail($errstr);
                                    }
                                }
                                if ($errstr !== false) {
                                    break;
                                }
                            }
                            foreach ($ckeys as $kf => $kt) {
                                if (isset($sync_chapters[$kt]['lastupdate']) && $sync_chapters[$kt]['lastupdate'] == 0) {
                                    $sync_chapters[$kt]['lastupdate'] = JIEQI_NOW_TIME;
                                }
                                $retc = jieqi_sync_chapterupdate($sync_chapters[$kt], $old_chapters[$kf]);
                                if ($retc !== true) {
                                    $errstr = $retc;
                                    break;
                                }
                            }
                            if ($errstr !== false) {
                                break;
                            }
                            $batkey = 0;
                            $scripts = array();
                            $params = array();
                            $skeys = array();
                            $okeys = array();
                            $ckeys = array();
                        }
                    }
                }
                if (empty($reterror) && !empty($del_chaps)) {
                    jieqi_sync_delchapters($del_chaps, $myarticle);
                }
                if (0 < $up_chapternum) {
                    include_once $jieqiModules['article']['path'] . '/include/actarticle.php';
                    $lastinfo = jieqi_article_searchlast($myarticle, 'full');
                    if (!empty($article_lastupdate)) {
                        $lastinfo['lastupdate'] = intval($article_lastupdate);
                    }
                    $sql = $query->makeupsql(jieqi_dbprefix('article_article'), $lastinfo, 'UPDATE', array('articleid' => $myarticle->getVar('articleid', 'n')));
                    $query->execute($sql);
                    if (0 < $myarticle->getVar('vipid', 'n') || 0 < $lastinfo['isvip']) {
                        $lastobook = array('lastupdate' => $lastinfo['viptime'], 'chapters' => $lastinfo['vipchapters'], 'words' => $lastinfo['vipwords'], 'lastvolumeid' => $lastinfo['vipvolumeid'], 'lastvolume' => $lastinfo['vipvolume'], 'lastchapterid' => $lastinfo['vipchapterid'], 'lastchapter' => $lastinfo['vipchapter'], 'lastsummary' => $lastinfo['vipsummary']);
                        $sql = $query->makeupsql(jieqi_dbprefix('obook_obook'), $lastobook, 'UPDATE', array('articleid' => $myarticle->getVar('articleid', 'n')));
                        $query->execute($sql);
                    }
                    include_once $jieqiModules['article']['path'] . '/include/repack.php';
                    article_repack($myarticleid, array('makeopf' => 1), 1);
                    $package = new JieqiPackage($myarticleid);
                    $package->loadOPF();
                    $makeparams = array('makezip' => intval($jieqiConfigs['article']['makezip']), 'makefull' => intval($jieqiConfigs['article']['makefull']), 'maketxtfull' => intval($jieqiConfigs['article']['maketxtfull']), 'makeumd' => intval($jieqiConfigs['article']['makeumd']), 'makejar' => intval($jieqiConfigs['article']['makejar']), 'makeindex' => intval($jieqiConfigs['article']['makehtml']));
                    if (empty($del_chaps) && !empty($up_corders)) {
                        $make_orders = array();
                        $max_order = count($sync_chapters);
                        foreach ($up_corders as $o) {
                            $o = intval($o);
                            if (0 < $o - 1) {
                                $make_orders[$o - 1] = 1;
                            }
                            $make_orders[$o] = 1;
                            if ($o + 1 <= $max_order) {
                                $make_orders[$o + 1] = 1;
                            }
                        }
                        $upoids = array();
                        foreach ($make_orders as $corder => $v) {
                            $upoids[] = $corder;
                            if ($jieqiConfigs['article']['maketxtjs']) {
                                $package->makeTxtjs($corder, true);
                            }
                        }
                        if (!empty($upoids)) {
                            $package->makeRead('updatechapter', implode('|', $upoids));
                        }
                    } else {
                        $makeparams['makechapter'] = intval($jieqiConfigs['article']['makehtml']);
                        $makeparams['maketxtjs'] = intval($jieqiConfigs['article']['maketxtjs']);
                    }
                    article_repack($myarticleid, $makeparams, 1);
                    if (0 < $jieqiConfigs['article']['fakestatic']) {
                        include_once $jieqiModules['article']['path'] . '/include/funstatic.php';
                        article_update_static('articleedit', $myarticleid, 0);
                    }
                }
            } else {
                ${$_REQUEST}['errnum']++;
                if ($_REQUEST['errstop'] <= $_REQUEST['errnum']) {
                    jieqi_printfail($errstr);
                } else {
                    echo '<span class="red">' . $errstr . '</span><br />';
                    ob_flush();
                    flush();
                }
            }
        } else {
            ${$_REQUEST}['errnum']++;
            if ($_REQUEST['errstop'] <= $_REQUEST['errnum']) {
                jieqi_printfail($errstr);
            } else {
                echo '<span class="red">' . $errstr . '</span><br />';
                ob_flush();
                flush();
            }
        }
    }
    if ($check_allchapters !== true) {
        ${$_REQUEST}['errnum']++;
        if ($_REQUEST['errstop'] <= $_REQUEST['errnum']) {
            jieqi_printfail(strval($check_allchapters));
        } else {
            echo '<span class="red">' . strval($check_allchapters) . '</span><br />';
            ob_flush();
            flush();
        }
    }
    if (!empty($_REQUEST['sourceid'])) {
        jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['site_onearticle_success'], jieqi_geturl('article', 'article', $myarticleid, 'info')));
    } else {
        $prows = array();
        $prows['finishnum'] = $_REQUEST['order'];
        $prows['finishtime'] = time();
        if ($tmax <= $_REQUEST['order'] || $logrow['articlenum'] <= $_REQUEST['order']) {
            $prows['issuccess'] = $tmax == $logrow['articlenum'] ? 1 : 2;
        }
        $sql = $query->makeupsql(jieqi_dbprefix('article_syncsite'), $prows, 'UPDATE', array('syncid' => $_REQUEST['syncid']));
        $query->execute($sql);
        if ($tmax <= $_REQUEST['order'] || $logrow['articlenum'] <= $_REQUEST['order']) {
            if (is_file($synccfile)) {
                jieqi_delfile($synccfile);
            }
            if (!empty($jieqiSites[$_REQUEST['siteid']]['syncokgo'])) {
                jieqi_jumppage($jieqiSites[$_REQUEST['siteid']]['syncokgo'], LANG_DO_SUCCESS, $jieqiLang['article']['site_allarticle_finish']);
            } else {
                jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['article']['site_allarticle_success'], $_REQUEST['order']));
            }
        } else {
            ${$_REQUEST}['order'] += 1;
            $self_name = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);
            $url = $self_name . '?confirm=1&siteid=' . $_REQUEST['siteid'] . '&syncid=' . $_REQUEST['syncid'] . '&order=' . $_REQUEST['order'] . '&errstop=' . $_REQUEST['errstop'] . '&errnum=' . $_REQUEST['errnum'];
            if (isset($_REQUEST['jieqi_username']) && isset($_REQUEST['jieqi_userpassword'])) {
                $url .= '&jieqi_username=' . urlencode($_REQUEST['jieqi_username']) . '&jieqi_userpassword=' . urlencode($_REQUEST['jieqi_userpassword']);
            }
            echo sprintf($jieqiLang['article']['site_next_html'], JIEQI_CHAR_SET, $tmax, $_REQUEST['order'], $url, $url);
            exit;
        }
    }
} else {
    if ($_REQUEST['synctype'] == 1) {
        $logrow = '';
    } else {
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('article_syncsite') . ' WHERE siteid = ' . $_REQUEST['siteid'] . ' ORDER BY syncid DESC LIMIT 0, 1';
        $query->execute($sql);
        $logrow = $query->getRow();
        if (is_array($logrow) && $logrow['issuccess'] == 0) {
            if (time() - $logrow['finishtime'] < 180) {
                jieqi_printfail($jieqiLang['article']['site_maybe_doing']);
            }
            $_REQUEST['syncid'] = $logrow['syncid'];
            $_REQUEST['order'] = $logrow['finishnum'] + 1;
            $self_name = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);
            $url = $self_name . '?confirm=1&siteid=' . $_REQUEST['siteid'] . '&syncid=' . $_REQUEST['syncid'] . '&order=' . $_REQUEST['order'] . '&errstop=' . $_REQUEST['errstop'] . '&errnum=' . $_REQUEST['errnum'];
            if (isset($_REQUEST['jieqi_username']) && isset($_REQUEST['jieqi_userpassword'])) {
                $url .= '&jieqi_username=' . urlencode($_REQUEST['jieqi_username']) . '&jieqi_userpassword=' . urlencode($_REQUEST['jieqi_userpassword']);
            }
            echo sprintf($jieqiLang['article']['site_next_html'], JIEQI_CHAR_SET, $logrow['articlenum'], $_REQUEST['order'], $url, $url);
            exit;
        }
    }
    $jieqiapi = new JieqiApiClient($jieqiSites[$_REQUEST['siteid']]);
    $params = array('uptime' => 0);
    if (is_array($logrow) && !empty($logrow)) {
        $params['uptime'] = 3600 < $logrow['starttime'] ? $logrow['starttime'] - 3600 : 0;
    }
    $prows = array();
    $prows['siteid'] = $_REQUEST['siteid'];
    $prows['userid'] = intval($_SESSION['jieqiUserId']);
    $prows['starttime'] = JIEQI_NOW_TIME;
    $prows['finishtime'] = time();
    $prows['fromtime'] = $params['uptime'];
    if (!empty($params['uptime'])) {
        $params['uptime'] = date('YmdHis', $params['uptime']);
    } else {
        if (isset($params['uptime'])) {
            unset($params['uptime']);
        }
    }
    $updateanum = 0;
    $listdata = '';
    $ret = $jieqiapi->api('articlecount', $params);
    if ($ret['ret'] < 0) {
        $aids = array();
        $reps = 0;
        $page = 1;
        $dobreak = false;
        do {
            $params['page'] = $page;
            $ret = $jieqiapi->api('articlelist', $params);
            if ($ret['ret'] < 0) {
                jieqi_printfail(jieqi_htmlstr($ret['msg']));
            }
            if (empty($ret['msg'])) {
                break;
            }
            $sync_alist = $jieqiapi->format('articlelist', $ret['msg']);
            if (!is_array($sync_alist) && is_array($ret['msg'])) {
                $sync_alist = $ret['msg'];
            }
            foreach ($sync_alist as $v) {
                if (isset($aids[$v['articleid']])) {
                    $reps++;
                    if (5 <= $reps) {
                        $dobreak = true;
                        break;
                    }
                } else {
                    $aids[$v['articleid']] = 1;
                    $updateanum++;
                    $listdata .= $v['articleid'] . ' ' . $jieqiapi->ftime($v['lastupdate']) . "\r\n";
                }
            }
            if ($dobreak) {
                break;
            }
            $page++;
        } while (true);
    } else {
        $articlecount = intval($ret['msg']['articlecount']);
        $pagecount = intval($ret['msg']['pagecount']);
        if (0 < $articlecount && 0 < $pagecount) {
            for ($page = 1; $page <= $pagecount; $page++) {
                $params['page'] = $page;
                $ret = $jieqiapi->api('articlelist', $params);
                if ($ret['ret'] < 0) {
                    jieqi_printfail(jieqi_htmlstr($ret['msg']));
                }
                $sync_alist = $jieqiapi->format('articlelist', $ret['msg']);
                if (!is_array($sync_alist) && is_array($ret['msg'])) {
                    $sync_alist = $ret['msg'];
                }
                foreach ($sync_alist as $v) {
                    $updateanum++;
                    $listdata .= $v['articleid'] . ' ' . $jieqiapi->ftime($v['lastupdate']) . "\r\n";
                }
            }
        }
    }
    if ($updateanum == 0) {
        $prows['articlenum'] = 0;
        $prows['finishnum'] = 0;
        $prows['retcode'] = 1;
        $prows['issuccess'] = 1;
        $sql = $query->makeupsql(jieqi_dbprefix('article_syncsite'), $prows, 'INSERT');
        $query->execute($sql);
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['site_article_noupdate']);
    }
    $prows['articlenum'] = $updateanum;
    $prows['finishnum'] = 0;
    $prows['retcode'] = 0;
    $prows['issuccess'] = 0;
    $sql = $query->makeupsql(jieqi_dbprefix('article_syncsite'), $prows, 'INSERT');
    $query->execute($sql);
    $syncsyncid = intval($query->db->getInsertId());
    $synccfile = JIEQI_ROOT_PATH . '/files/article/syncsite' . jieqi_getsubdir($syncsyncid);
    jieqi_checkdir($synccfile, true);
    $synccfile .= '/' . $syncsyncid . $jieqi_file_postfix['txt'];
    $cfileres = @fopen($synccfile, 'wb');
    if (!$cfileres) {
        jieqi_printfail(sprintf($jieqiLang['article']['site_cachefile_openfailed'], $synccfile));
    }
    @flock($cfileres, LOCK_EX);
    @fwrite($cfileres, $listdata);
    @flock($cfileres, LOCK_UN);
    @fclose($cfileres);
    @chmod($cfileres, 511);
    echo sprintf($jieqiLang['article']['site_article_updatenum'], $updateanum) . '<br />';
    ob_flush();
    flush();
    $_REQUEST['syncid'] = $syncsyncid;
    $_REQUEST['order'] = 1;
    $self_name = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);
    $url = $self_name . '?confirm=1&siteid=' . $_REQUEST['siteid'] . '&syncid=' . $_REQUEST['syncid'] . '&order=' . $_REQUEST['order'] . '&errstop=' . $_REQUEST['errstop'] . '&errnum=' . $_REQUEST['errnum'];
    if (isset($_REQUEST['jieqi_username']) && isset($_REQUEST['jieqi_userpassword'])) {
        $url .= '&jieqi_username=' . urlencode($_REQUEST['jieqi_username']) . '&jieqi_userpassword=' . urlencode($_REQUEST['jieqi_userpassword']);
    }
    echo sprintf($jieqiLang['article']['site_next_html'], JIEQI_CHAR_SET, $updateanum, $_REQUEST['order'], $url, $url);
    exit;
}