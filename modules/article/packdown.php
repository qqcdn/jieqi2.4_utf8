<?php

function pack_down_do($path, $score, $changescore = true, $fname = '')
{
    global $jieqiLang;
    $score = intval($score);
    $ftype = strrchr(trim(strtolower($path)), '.');
    if (0 < $score) {
        jieqi_checklogin();
    }
    if (empty($_REQUEST['id'])) {
        $_REQUEST['id'] = intval(basename($path));
    }
    $downvalid = jieqi_visit_valid($_REQUEST['id'], 'article_articledowns');
    if (0 < $score && $downvalid) {
        jieqi_checklogin();
        jieqi_loadlang('down', JIEQI_MODULE_NAME);
        if ($_SESSION['jieqiUserScore'] < $score) {
            jieqi_printfail(sprintf($jieqiLang['article']['low_down_score'], $score));
        } else {
            if (!is_file($path)) {
                return false;
            }
            if ($changescore) {
                include_once JIEQI_ROOT_PATH . '/class/users.php';
                $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
                $users_handler->changeScore($_SESSION['jieqiUserId'], $score, false, false);
                @session_write_close();
            }
            $filename = empty($fname) ? basename($path) : jieqi_htmlstr($fname) . $ftype;
            pack_down_file($path, $filename);
        }
    } else {
        if (!is_file($path)) {
            return false;
        }
        $filename = empty($fname) ? basename($path) : jieqi_htmlstr($fname) . $ftype;
        pack_down_file($path, $filename);
    }
    if ($downvalid) {
        global $article_handler;
        global $article;
        $addnum = 1;
        $lasttime = $article->getVar('lastdown', 'n');
        $addorup = jieqi_visit_addorup($lasttime);
        $upfields = array();
        $upfields['daydown'] = $addorup['day'] ? $addnum : $article->getVar('daydown', 'n') + $addnum;
        $upfields['weekdown'] = $addorup['week'] ? $addnum : $article->getVar('weekdown', 'n') + $addnum;
        if (2.3 <= floatval(JIEQI_VERSION)) {
            if (1 < $addorup['month']) {
                $upfields['predown'] = 0;
            } else {
                if ($addorup['month'] == 1) {
                    $upfields['predown'] = $article->getVar('monthdown', 'n');
                }
            }
        }
        $upfields['monthdown'] = $addorup['month'] ? $addnum : $article->getVar('monthdown', 'n') + $addnum;
        $upfields['alldown'] = $article->getVar('alldown', 'n') + $addnum;
        $upfields['lastdown'] = JIEQI_NOW_TIME;
        $criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['id']));
        $article_handler->updatefields($upfields, $criteria);
    }
    return true;
}
function pack_down_file($path, $filename)
{
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: application/x-octet-stream');
    header('Accept-Ranges: bytes');
    header('Content-Disposition: attachment; filename=' . jieqi_headstr($filename));
    $directout = false;
    $outdata = '';
    if (substr($filename, -4) == $jieqi_file_postfix['jad'] && filesize($path) < 2048) {
        $tmpvar = substr($filename, 0, -4);
        if (!is_numeric($tmpvar)) {
            $data = file_get_contents($path);
            if (!is_numeric($tmpvar) && preg_match('/MIDlet-Name:\\s*([^\\s\\r\\n]+)/is', $data, $matches)) {
                $directout = true;
                $outdata = preg_replace('/[0-9]+\\.jar/isU', trim($matches[1]) . '.jar', $data);
            }
        }
    }
    if ($directout) {
        $size = strlen($outdata);
    } else {
        $size = filesize($path);
    }
    $range = array();
    $range[0] = 'bytes';
    $range[1] = 0;
    $range[2] = $size - 1;
    if (isset($_SERVER['HTTP_RANGE'])) {
        $tmpary = explode('-', str_replace(array('=', ','), '-', $_SERVER['HTTP_RANGE']));
        if (3 <= count($tmpary)) {
            foreach ($tmpary as $k => $v) {
                $tmpary[$k] = trim($v);
            }
            if (strlen($tmpary[1]) == 0 && is_numeric($tmpary[2])) {
                $range[1] = $size - intval($tmpary[2]);
                if ($range[1] < 0 || $range[2] < $range[1]) {
                    $range[1] = 0;
                }
            } else {
                if (strlen($tmpary[2]) == 0 && is_numeric($tmpary[1])) {
                    $range[1] = intval($tmpary[1]);
                    if ($range[1] < 0 || $range[2] < $range[1]) {
                        $range[1] = 0;
                    }
                } else {
                    if (is_numeric($tmpary[1]) && is_numeric($tmpary[2])) {
                        $range[1] = intval($tmpary[1]);
                        $range[2] = intval($tmpary[2]);
                        if ($range[2] < 0 || $size - 1 < $range[2]) {
                            $range[2] = $range[2] = $size - 1;
                        }
                        if ($range[1] < 0 || $range[2] < $range[1]) {
                            $range[1] = 0;
                        }
                    }
                }
            }
        }
    }
    header('Content-Length:' . ($range[2] - $range[1] + 1));
    if ($range[1] != 0 || $range[2] != $size - 1) {
        header('HTTP/1.1 206 OK');
        header('Content-Range: bytes ' . $range[1] . '-' . $range[2] . '/' . $size);
    }
    if ($directout) {
        if ($range[1] == 0 && $range[2] == $size - 1) {
            echo $outdata;
        } else {
            echo substr($outdata, $range[1], $range[2] - $range[1] + 1);
        }
    } else {
        if ($range[1] == 0 && $range[2] == $size - 1) {
            readfile($path);
        } else {
            $fp = fopen($path, 'rb');
            $mpos = $range[2] + 1;
            $fpos = 0;
            if (0 < $range[1]) {
                fseek($fp, $range[1]);
                $fpos = $range[1];
            }
            $bsize = 1024;
            while (!feof($fp) && $bsize < $mpos - $fpos) {
                echo fread($fp, $bsize);
                ob_flush();
                flush();
                $fpos += $bsize;
            }
            if (0 < $mpos - $fpos) {
                echo fread($fp, $mpos - $fpos);
                ob_flush();
                flush();
            }
            fclose($fp);
        }
    }
    return true;
}
define('JIEQI_MODULE_NAME', 'article');
define('JIEQI_USE_GZIP', '0');
define('JIEQI_NOCONVERT_CHAR', '1');
@set_time_limit(600);
@ini_set('memory_limit', '128M');
require_once '../../global.php';
if (JIEQI_MODULE_VTYPE == '' || JIEQI_MODULE_VTYPE == 'Free') {
    exit;
}
if (empty($_REQUEST['id']) && empty($_REQUEST['name']) || empty($_REQUEST['type'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
jieqi_loadlang('down', JIEQI_MODULE_NAME);
include_once $jieqiModules['article']['path'] . '/class/article.php';
$article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
if (empty($_REQUEST['id']) && !empty($_REQUEST['name'])) {
    $criteria = new CriteriaCompo(new Criteria('articlename', $_REQUEST['name'], '='));
    $criteria->setLimit(1);
    $article_handler->queryObjects($criteria);
    $article = $article_handler->getObject();
} else {
    $article = $article_handler->get(intval($_REQUEST['id']));
}
if (!is_object($article)) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
if ($article->getVar('display') != 0) {
    jieqi_printfail($jieqiLang['article']['article_not_down']);
}
$_REQUEST['id'] = intval($article->getVar('articleid', 'n'));
if (empty($_REQUEST['id'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
}
$_REQUEST['id'] = intval($_REQUEST['id']);
if (isset($_REQUEST['cid'])) {
    $_REQUEST['cid'] = intval($_REQUEST['cid']);
}
if (isset($_REQUEST['vid'])) {
    $_REQUEST['vid'] = intval($_REQUEST['vid']);
}
$_REQUEST['fname'] = trim($_REQUEST['fname']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_getconfigs('article', 'action', 'jieqiAction');
$article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = empty($jieqiConfigs['article']['dynamicurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
$opf_file = jieqi_uploadpath($jieqiConfigs['article']['opfdir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/index' . $jieqi_file_postfix['opf'];
if (!is_file($opf_file)) {
    jieqi_printfail($jieqiLang['article']['article_not_exists']);
}
$lastupdate = filemtime($opf_file);
$vsflags = array('0' => 1, '64' => 2, '128' => 4, '256' => 8, '512' => 16, '1024' => 32);
include_once JIEQI_ROOT_PATH . '/include/funstat.php';
switch ($_REQUEST['type']) {
    case 'txt':
        if (empty($_REQUEST['cid'])) {
            if (empty($jieqiConfigs['article']['maketxtfull'])) {
                jieqi_printfail($jieqiLang['article']['down_file_notopen']);
            }
            $path = jieqi_uploadpath($jieqiConfigs['article']['txtfulldir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . $jieqi_file_postfix['txt'];
            if (!is_file($path) || filesize($path) == 0 || filemtime($path) + 600 < $lastupdate) {
                include_once $jieqiModules['article']['path'] . '/include/repack.php';
                article_repack($_REQUEST['id'], array('maketxtfull' => 1), 1);
            }
            $ret = pack_down_do($path, abs($jieqiAction['article']['down']['earnscore']), true, $_REQUEST['fname']);
        } else {
            if (empty($jieqiConfigs['article']['maketxt'])) {
                jieqi_printfail($jieqiLang['article']['down_file_notopen']);
            }
            $path = jieqi_uploadpath($jieqiConfigs['article']['txtdir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/' . $_REQUEST['cid'] . $jieqi_file_postfix['txt'];
            $ret = pack_down_do($path, 0, false, $_REQUEST['fname']);
        }
        break;
    case 'zip':
        if (empty($jieqiConfigs['article']['makezip'])) {
            jieqi_printfail($jieqiLang['article']['down_file_notopen']);
        }
        $path = jieqi_uploadpath($jieqiConfigs['article']['zipdir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . $jieqi_file_postfix['zip'];
        if (!is_file($path) || filesize($path) == 0 || filemtime($path) + 600 < $lastupdate) {
            include_once $jieqiModules['article']['path'] . '/include/repack.php';
            article_repack($_REQUEST['id'], array('makezip' => 1), 1);
        }
        $ret = pack_down_do($path, abs($jieqiAction['article']['down']['earnscore']), true, $_REQUEST['fname']);
        break;
    case 'umd':
        if (empty($jieqiConfigs['article']['makeumd'])) {
            jieqi_printfail($jieqiLang['article']['down_file_notopen']);
        }
        if (isset($_REQUEST['vsize'])) {
            $_REQUEST['vsize'] = intval($_REQUEST['vsize']);
        } else {
            $_REQUEST['vsize'] = 0;
        }
        if ($_REQUEST['vsize'] == 1) {
            $_REQUEST['vsize'] = 0;
        }
        if (!isset($vsflags[$_REQUEST['vsize']]) || ($jieqiConfigs['article']['makeumd'] & $vsflags[$_REQUEST['vsize']]) == 0) {
            jieqi_printfail($jieqiLang['article']['down_file_notopen']);
        }
        if (empty($_REQUEST['vsize'])) {
            $path = jieqi_uploadpath($jieqiConfigs['article']['umddir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/' . $_REQUEST['id'] . $jieqi_file_postfix['umd'];
            $checkfile = $path;
        } else {
            $path = jieqi_uploadpath($jieqiConfigs['article']['umddir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/' . $_REQUEST['id'] . '_' . intval($_REQUEST['vsize']) . '_' . intval($_REQUEST['vid']) . $jieqi_file_postfix['umd'];
            $checkfile = dirname($path) . '/' . $_REQUEST['id'] . '_' . intval($_REQUEST['vsize']) . '.xml';
        }
        if (!is_file($checkfile) || filesize($checkfile) == 0 || filemtime($checkfile) + 600 < $lastupdate) {
            include_once $jieqiModules['article']['path'] . '/include/repack.php';
            article_repack($_REQUEST['id'], array('makeumd' => 1), 1);
        }
        $ret = pack_down_do($path, abs($jieqiAction['article']['down']['earnscore']), true, $_REQUEST['fname']);
        break;
    case 'jar':
        if (empty($jieqiConfigs['article']['makejar'])) {
            jieqi_printfail($jieqiLang['article']['down_file_notopen']);
        }
        if (isset($_REQUEST['vsize'])) {
            $_REQUEST['vsize'] = intval($_REQUEST['vsize']);
        } else {
            $_REQUEST['vsize'] = 0;
        }
        if ($_REQUEST['vsize'] == 1) {
            $_REQUEST['vsize'] = 0;
        }
        if (!isset($vsflags[$_REQUEST['vsize']]) || ($jieqiConfigs['article']['makejar'] & $vsflags[$_REQUEST['vsize']]) == 0) {
            jieqi_printfail($jieqiLang['article']['down_file_notopen']);
        }
        if (empty($_REQUEST['vsize'])) {
            $path = jieqi_uploadpath($jieqiConfigs['article']['jardir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/' . $_REQUEST['id'] . $jieqi_file_postfix['jar'];
            $checkfile = $path;
        } else {
            $path = jieqi_uploadpath($jieqiConfigs['article']['jardir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/' . $_REQUEST['id'] . '_' . intval($_REQUEST['vsize']) . '_' . intval($_REQUEST['vid']) . $jieqi_file_postfix['jar'];
            $checkfile = dirname($path) . '/' . $_REQUEST['id'] . '_' . intval($_REQUEST['vsize']) . '.xml';
        }
        if (!is_file($checkfile) || filesize($checkfile) == 0 || filemtime($checkfile) + 600 < $lastupdate) {
            include_once $jieqiModules['article']['path'] . '/include/repack.php';
            article_repack($_REQUEST['id'], array('makejar' => 1), 1);
        }
        $ret = pack_down_do($path, abs($jieqiAction['article']['down']['earnscore']), true, $_REQUEST['fname']);
        break;
    case 'jad':
        if (empty($jieqiConfigs['article']['makejar'])) {
            jieqi_printfail($jieqiLang['article']['down_file_notopen']);
        }
        if (isset($_REQUEST['vsize'])) {
            $_REQUEST['vsize'] = intval($_REQUEST['vsize']);
        } else {
            $_REQUEST['vsize'] = 0;
        }
        if ($_REQUEST['vsize'] == 1) {
            $_REQUEST['vsize'] = 0;
        }
        if (!isset($vsflags[$_REQUEST['vsize']]) || ($jieqiConfigs['article']['makejar'] & $vsflags[$_REQUEST['vsize']]) == 0) {
            jieqi_printfail($jieqiLang['article']['down_file_notopen']);
        }
        if (empty($_REQUEST['vsize'])) {
            $path = jieqi_uploadpath($jieqiConfigs['article']['jardir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/' . $_REQUEST['id'] . $jieqi_file_postfix['jad'];
            $checkfile = $path;
        } else {
            $path = jieqi_uploadpath($jieqiConfigs['article']['jardir'], 'article') . jieqi_getsubdir($_REQUEST['id']) . '/' . $_REQUEST['id'] . '/' . $_REQUEST['id'] . '_' . intval($_REQUEST['vsize']) . '_' . intval($_REQUEST['vid']) . $jieqi_file_postfix['jad'];
            $checkfile = dirname($path) . '/' . $_REQUEST['id'] . '_' . intval($_REQUEST['vsize']) . '.xml';
        }
        $ret = pack_down_do($path, 0, false, $_REQUEST['fname']);
        break;
    default:
        jieqi_printfail(LANG_ERROR_PARAMETER);
        break;
}
if (!$ret) {
    jieqi_printfail($jieqiLang['article']['down_file_nocreate']);
}