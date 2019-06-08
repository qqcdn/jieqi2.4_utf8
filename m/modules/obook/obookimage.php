<?php

$logstart = explode(' ', microtime());
define('JIEQI_MODULE_NAME', 'obook');
define('JIEQI_NOCONVERT_CHAR', '1');
@ini_set('memory_limit', '128M');
require_once '../../global.php';
if (!jieqi_checklogin(true)) {
    exit;
}
if (empty($_REQUEST['cid'])) {
    exit;
}
$_REQUEST['cid'] = intval($_REQUEST['cid']);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$jieqiConfigs['obook']['obklinewidth'] = isset($jieqiConfigs['obook']['obklinewidth']) ? intval($jieqiConfigs['obook']['obklinewidth']) : 80;
if ($jieqiConfigs['obook']['obklinewidth'] < 2) {
    $jieqiConfigs['obook']['obklinewidth'] = 80;
}
$_REQUEST['pic'] = isset($_REQUEST['pic']) ? intval($_REQUEST['pic']) : 0;
$jieqiConfigs['obook']['obkpictxt'] = isset($jieqiConfigs['obook']['obkpictxt']) ? intval($jieqiConfigs['obook']['obkpictxt']) : 0;
$jieqiConfigs['obook']['obkpicline'] = isset($jieqiConfigs['obook']['obkpicline']) ? intval($jieqiConfigs['obook']['obkpicline']) : 0;
$obook_static_url = empty($jieqiConfigs['obook']['staticurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['staticurl'];
$obook_dynamic_url = empty($jieqiConfigs['obook']['dynamicurl']) ? $jieqiModules['obook']['url'] : $jieqiConfigs['obook']['dynamicurl'];
if (isset($_SESSION['jieqiVisitedObooks'])) {
    $arysession = jieqi_unserialize($_SESSION['jieqiVisitedObooks']);
} else {
    $arysession = array();
}
if (!is_array($arysession)) {
    $arysession = array();
}
if (!isset($arysession[$_REQUEST['cid']]) || $arysession[$_REQUEST['cid']] != 1) {
    exit;
}
@session_write_close();
include_once $jieqiModules['obook']['path'] . '/class/ocontent.php';
$content_handler = JieqiOcontentHandler::getInstance('JieqiOcontentHandler');
$criteria = new CriteriaCompo(new Criteria('ochapterid', $_REQUEST['cid']));
$criteria->setLimit(1);
$content_handler->queryObjects($criteria);
unset($criteria);
$content = $content_handler->getObject();
if (!is_object($content)) {
    exit;
} else {
    include_once JIEQI_ROOT_PATH . '/include/changecode.php';
    include_once JIEQI_ROOT_PATH . '/lib/text/textfunction.php';
    include_once JIEQI_ROOT_PATH . '/lib/image/imagetext.php';
    $outstr = $content->getVar('ocontent', 'n');
    if (0 < $_REQUEST['pic'] && 0 < $jieqiConfigs['obook']['obkpictxt'] && $jieqiConfigs['obook']['obkpicline'] <= 0) {
        $outstr = jieqi_substr($outstr, ($_REQUEST['pic'] - 1) * $jieqiConfigs['obook']['obkpictxt'], $jieqiConfigs['obook']['obkpictxt'], '');
    }
    if (!empty($jieqiConfigs['obook']['obookreadhead'])) {
        $outstr = $jieqiConfigs['obook']['obookreadhead'] . "\r\n" . $outstr;
    }
    if (!empty($jieqiConfigs['obook']['obookreadfoot'])) {
        $outstr .= "\r\n" . $jieqiConfigs['obook']['obookreadfoot'];
    }
    if (0 < $_REQUEST['pic'] && 0 < $jieqiConfigs['obook']['obkpicline']) {
        $tmpstr = '';
        $strlen = strlen($outstr);
        $point = 0;
        $lstart = ($_REQUEST['pic'] - 1) * $jieqiConfigs['obook']['obkpicline'];
        $lend = $_REQUEST['pic'] * $jieqiConfigs['obook']['obkpicline'];
        $lorder = 0;
        for ($i = 0; $i < $strlen; $i++) {
            if ($jieqiConfigs['obook']['obklinewidth'] <= $point) {
                $lorder++;
                if ($lend <= $lorder) {
                    break;
                }
                if ($lstart < $lorder) {
                    $tmpstr .= "\n";
                }
                $point = 0;
            }
            if (128 < ord($outstr[$i])) {
                if ($lstart <= $lorder) {
                    $tmpstr .= $outstr[$i] . $outstr[$i + 1];
                }
                $i++;
                $point += 2;
            } else {
                if ($outstr[$i] == "\n") {
                    $lorder++;
                    if ($lend <= $lorder) {
                        break;
                    }
                    if ($lstart < $lorder) {
                        $tmpstr .= $outstr[$i];
                    }
                    $point = 0;
                } else {
                    if ($lstart <= $lorder) {
                        $tmpstr .= $outstr[$i];
                    }
                    $point += 1;
                }
            }
        }
        $len = strlen($tmpstr);
        if (0 < $len) {
            if ($jieqiConfigs['obook']['obklinewidth'] <= $len) {
                $outstr = $tmpstr;
            } else {
                $outstr = $tmpstr . str_repeat(' ', $jieqiConfigs['obook']['obklinewidth'] - $len);
            }
        } else {
            $outstr = '';
        }
    } else {
        $outstr = jieqi_limitwidth($outstr, $jieqiConfigs['obook']['obklinewidth']);
    }
    if (isset($jieqiConfigs['obook']['obkwaterformat'])) {
        $watertext = str_replace(array('<{$userid}>', '<{$username}>', '<{$date}>', '<{$time}>'), array($_SESSION['jieqiUserId'], $_SESSION['jieqiUserName'], date(JIEQI_DATE_FORMAT, JIEQI_NOW_TIME), date(JIEQI_TIME_FORMAT, JIEQI_NOW_TIME)), $jieqiConfigs['obook']['obkwaterformat']);
    } else {
        $watertext = $_SESSION['jieqiUserId'];
    }
    if (strlen($watertext) < 10) {
        $watertext = sprintf('%10s', $watertext);
    }
    $jieqi_charset_map = array('gb2312' => 'gb', 'gbk' => 'gb', 'gb' => 'gb', 'big5' => 'big5', 'utf-8' => 'utf8', 'utf8' => 'utf8');
    $fontcharset = JIEQI_SYSTEM_CHARSET;
    if (JIEQI_SYSTEM_CHARSET == 'gb2312' || JIEQI_SYSTEM_CHARSET == 'gbk') {
        $outstr = str_replace('    ', chr(161) . chr(161) . chr(161) . chr(161), $outstr);
    } else {
        if (JIEQI_SYSTEM_CHARSET == 'big5') {
            $outstr = str_replace('    ', chr(161) . chr(64) . chr(161) . chr(64), $outstr);
        }
    }
    if (JIEQI_SYSTEM_CHARSET != JIEQI_CHAR_SET) {
        if ((JIEQI_SYSTEM_CHARSET == 'gb2312' || JIEQI_SYSTEM_CHARSET == 'gbk') && JIEQI_CHAR_SET == 'big5') {
            if (!empty($jieqiConfigs['obook']['obkcharconvert'])) {
                $outstr = jieqi_gb2big5($outstr);
                $watertext = jieqi_gb2big5($watertext);
                $fontcharset = JIEQI_CHAR_SET;
            }
        } else {
            if (JIEQI_SYSTEM_CHARSET == 'big5' && (JIEQI_CHAR_SET == 'gb2312' || JIEQI_CHAR_SET == 'gbk')) {
                if (!empty($jieqiConfigs['obook']['obkcharconvert'])) {
                    $outstr = jieqi_big52gb($outstr);
                    $watertext = jieqi_big52gb($watertext);
                    $fontcharset = JIEQI_CHAR_SET;
                }
            }
        }
    }
    $changefun = '';
    if (isset($jieqi_charset_map[$fontcharset])) {
        $changefun = 'jieqi_' . $jieqi_charset_map[$fontcharset] . '2utf8';
    }
    if (function_exists($changefun)) {
        $outstr = call_user_func($changefun, $outstr);
        $watertext = call_user_func($changefun, $watertext);
    }
    $img = new ImageText();
    $img->set('text', $outstr);
    $img->set('startx', $jieqiConfigs['obook']['obkstartx']);
    $img->set('starty', $jieqiConfigs['obook']['obkstarty']);
    $img->set('fontsize', $jieqiConfigs['obook']['obkfontsize']);
    if (JIEQI_CHAR_SET == 'big5') {
        $img->set('fontfile', $jieqiConfigs['obook']['obkfontft']);
    } else {
        $img->set('fontfile', $jieqiConfigs['obook']['obkfontjt']);
    }
    $img->set('angle', $jieqiConfigs['obook']['obkangle']);
    $img->set('imagecolor', $jieqiConfigs['obook']['obkimagecolor']);
    $img->set('textcolor', $jieqiConfigs['obook']['obktextcolor']);
    $img->set('shadowcolor', $jieqiConfigs['obook']['obkshadowcolor']);
    $img->set('shadowdeep', $jieqiConfigs['obook']['obkshadowdeep']);
    $img->set('imagetype', $jieqiConfigs['obook']['obkimagetype']);
    if (isset($jieqiConfigs['obook']['obkwatertext'])) {
        $img->set('watertplace', intval($jieqiConfigs['obook']['obkwatertext']));
    } else {
        $img->set('watertplace', 2);
    }
    $img->set('watertext', $watertext);
    $img->set('watercolor', $jieqiConfigs['obook']['obkwatercolor']);
    $img->set('watersize', $jieqiConfigs['obook']['obkwatersize']);
    $img->set('waterangle', $jieqiConfigs['obook']['obkwaterangle']);
    $img->set('waterpct', $jieqiConfigs['obook']['obkwaterpct']);
    $jieqiConfigs['obook']['jpegquality'] = intval($jieqiConfigs['obook']['jpegquality']);
    if (0 <= $jieqiConfigs['obook']['jpegquality'] && $jieqiConfigs['obook']['jpegquality'] <= 100) {
        $img->set('jpegquality', $jieqiConfigs['obook']['jpegquality']);
    }
    $jieqiConfigs['obook']['obookwater'] = intval($jieqiConfigs['obook']['obookwater']);
    if (0 < $jieqiConfigs['obook']['obookwater']) {
        $img->set('wateriplace', $jieqiConfigs['obook']['obookwater']);
    }
    $jieqiConfigs['obook']['obookwtrans'] = intval($jieqiConfigs['obook']['obookwtrans']);
    if (1 <= $jieqiConfigs['obook']['obookwtrans'] && $jieqiConfigs['obook']['obookwtrans'] <= 100) {
        $img->set('wateritrans', $jieqiConfigs['obook']['obookwtrans']);
    }
    if (!empty($jieqiConfigs['obook']['obookwimage']) && is_file($jieqiModules['obook']['path'] . '/images/' . $jieqiConfigs['obook']['obookwimage'])) {
        $img->set('waterimage', $jieqiModules['obook']['path'] . '/images/' . $jieqiConfigs['obook']['obookwimage']);
    }
    $img->display();
}