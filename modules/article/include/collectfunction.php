<?php

function jieqi_collectptos($str)
{
    $str = trim($str);
    $middleary = array('****', '!!!!', '~~~~', '^^^^', '$$$$');
    while (list($k, $v) = each($middleary)) {
        if (strpos($str, $v) !== false) {
            $tmpary = explode($v, $str);
            return array('left' => strval($tmpary[0]), 'right' => strval($tmpary[1]), 'middle' => $v);
        }
    }
    return $str;
}
function jieqi_collectstop($str)
{
    if (is_array($str)) {
        return $str['left'] . $str['middle'] . $str['right'];
    } else {
        return $str;
    }
}
function jieqi_collectmtop($str)
{
    switch ($str) {
        case '!!!!':
            return '([^\\>\\<]*)';
            break;
        case '~~~~':
            return '([^\\<\\>\'"]*)';
            break;
        case '^^^^':
            return '([^\\<\\>\\d]*)';
            break;
        case '$$$$':
            return '([\\d]*)';
            break;
        case '****':
        default:
            return '(.*)';
            break;
    }
}
function jieqi_collectstoe($str)
{
    if (is_array($str)) {
        $pregstr = '/' . jieqi_pregconvert($str['left']) . jieqi_collectmtop($str['middle']) . jieqi_pregconvert($str['right']) . '/is';
        if ($str['middle'] == '****') {
            $pregstr .= 'U';
        }
    } else {
        $pregstr = trim($str);
        if (0 < strlen($pregstr) && substr($pregstr, 0, 1) != '/') {
            $pregstr = '/' . str_replace(array(' ', '/'), array('\\s', '\\/'), preg_quote($pregstr)) . '/is';
        }
    }
    return $pregstr;
}
function jieqi_cmatchone($pregstr, $source)
{
    $matches = array();
    preg_match($pregstr, $source, $matches);
    if (!is_array($matches) || count($matches) == 0) {
        return false;
    } else {
        return $matches[count($matches) - 1];
    }
}
function jieqi_cmatchall($pregstr, $source, $flags = 0)
{
    $matches = array();
    if ($flags == PREG_OFFSET_CAPTURE) {
        preg_match_all($pregstr, $source, $matches, PREG_OFFSET_CAPTURE + PREG_SET_ORDER);
    } else {
        preg_match_all($pregstr, $source, $matches, PREG_SET_ORDER);
    }
    if (!is_array($matches) || count($matches) == 0) {
        return false;
    } else {
        $ret = array();
        foreach ($matches as $v) {
            if (is_array($v)) {
                $ret[] = $v[count($v) - 1];
            } else {
                $ret[] = $v;
            }
        }
        return $ret;
    }
}
function jieqi_equichapter($chapter1, $chapter2)
{
    $retfrom = array(' ', '　', '<', '>', '【', '】', '[', ']', '［', '］', '（', '）', '(', ')', 'T', '图', '求票');
    if ($chapter1 == $chapter2) {
        return true;
    } else {
        if (str_replace($retfrom, '', $chapter1) == str_replace($retfrom, '', $chapter2)) {
            return true;
        } else {
            $tmpary1 = jieqi_splitchapter($chapter1);
            $tmpary2 = jieqi_splitchapter($chapter2);
            if ($tmpary1['pnum'] == $tmpary2['pnum'] && $tmpary1['cname'] == $tmpary2['cname']) {
                return true;
            } else {
                return false;
            }
        }
    }
}
function jieqi_splitchapter($str)
{
    $ret = array('vid' => 0, 'vname' => '', 'fcid' => 0, 'fcname' => '', 'cid' => 0, 'cname' => '', 'sid' => 0, 'sname' => '', 'pnum' => 0);
    $numary = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '百', '千', '万', '上', '中', '下');
    $vary = array('卷', '部', '集', '篇');
    $cary = array('章', '节', '回');
    $sary = array(')', '）', '＞');
    $aary = array('第', '：', '(', '（');
    $splitary = array_merge($vary, $cary, $sary, $aary);
    $str = trim($str);
    $str = str_replace(array('<', '>'), array('＜', '＞'), $str);
    $str = jieqi_textstr($str);
    $slen = strlen($str);
    $i = 0;
    $nstart = 0;
    while ($i < $slen) {
        $tmpstr = $str[$i];
        if (128 < ord($str[$i]) && $i < $slen - 1) {
            $tmpstr .= $str[$i + 1];
            $cl = 2;
        } else {
            $cl = 1;
        }
        $i += $cl;
        if (in_array($tmpstr, $vary)) {
            if ($i - $cl == 0) {
                $k = $i;
                $numstr = '';
                while ($k < $slen) {
                    $tmpstr = $str[$k];
                    if (128 < ord($str[$k]) && $k < $slen - 1) {
                        $tmpstr .= $str[$k + 1];
                        $k++;
                    }
                    $k++;
                    if (in_array($tmpstr, $numary)) {
                        $numstr .= $tmpstr;
                    } else {
                        if ($tmpstr == ' ') {
                        } else {
                            break;
                        }
                    }
                }
                $ret['vid'] = jieqi_numcntoab($numstr);
                $nstart = $k;
            } else {
                $k = $i - $cl;
                $numstr = '';
                while (0 < $k) {
                    if (2 <= $k && 128 < ord($str[$k - 1])) {
                        $tmpstr = $str[$k - 2] . $str[$k - 1];
                        $k -= 2;
                    } else {
                        $tmpstr = $str[$k - 1];
                        $k--;
                    }
                    if (in_array($tmpstr, $numary)) {
                        $numstr = $tmpstr . $numstr;
                    } else {
                        if ($tmpstr == ' ') {
                        } else {
                            break;
                        }
                    }
                }
                $ret['vid'] = jieqi_numcntoab($numstr);
                $nstart = $i;
            }
            break;
        }
    }
    if ($slen <= $i) {
        $i = 0;
    }
    if (0 < $i) {
        $j = 0;
        while ($j < $i) {
            $tmpstr = $str[$j];
            if (128 < ord($str[$j]) && $j < $slen - 1) {
                $tmpstr .= $str[$j + 1];
                $j++;
            }
            $j++;
            if (in_array($tmpstr, $cary)) {
                $i = 0;
                $nstart = 0;
                $ret['vid'] = 0;
                break;
            }
        }
    }
    while ($i < $slen) {
        $tmpstr = $str[$i];
        if (128 < ord($str[$i]) && $i < $slen - 1) {
            $tmpstr .= $str[$i + 1];
            $cl = 2;
        } else {
            $cl = 1;
        }
        $i += $cl;
        if (in_array($tmpstr, $cary)) {
            $k = $i - $cl;
            $numstr = '';
            while ($nstart < $k) {
                if (2 <= $k && 128 < ord($str[$k - 1])) {
                    $tmpstr = $str[$k - 2] . $str[$k - 1];
                    $j = 2;
                } else {
                    $tmpstr = $str[$k - 1];
                    $j = 1;
                }
                if (in_array($tmpstr, $numary)) {
                    $numstr = $tmpstr . $numstr;
                } else {
                    if ($tmpstr == ' ') {
                    } else {
                        break;
                    }
                }
                $k -= $j;
            }
            $ret['cid'] = jieqi_numcntoab($numstr);
            if ($tmpstr != '第' && $tmpstr != ' ') {
                $k -= $j;
                $numstr = '';
                while ($nstart < $k) {
                    if (2 <= $k && 128 < ord($str[$k - 1])) {
                        $tmpstr = $str[$k - 2] . $str[$k - 1];
                        $j = 2;
                    } else {
                        $tmpstr = $str[$k - 1];
                        $j = 1;
                    }
                    if (in_array($tmpstr, $numary)) {
                        $numstr = $tmpstr . $numstr;
                    } else {
                        if ($tmpstr == ' ') {
                        } else {
                            break;
                        }
                    }
                    $k -= $j;
                }
                if (!empty($numstr)) {
                    $ret['fcid'] = jieqi_numcntoab($numstr);
                }
            }
            if ($nstart < $k) {
                $ret['vname'] = jieqi_usefultitle(substr($str, $nstart, $k - $nstart));
            }
            $nstart = $i;
            break;
        }
    }
    if ($slen <= $i) {
        $i = 0;
    }
    $baki = $i;
    while ($i < $slen) {
        $tmpstr = $str[$i];
        if (128 < ord($str[$i]) && $i < $slen - 1) {
            $tmpstr .= $str[$i + 1];
            $cl = 2;
        } else {
            $cl = 1;
        }
        $i += $cl;
        if (in_array($tmpstr, $cary)) {
            $k = $i - $cl;
            $numstr = '';
            while ($nstart < $k) {
                if (2 <= $k && 128 < ord($str[$k - 1])) {
                    $tmpstr = $str[$k - 2] . $str[$k - 1];
                    $j = 2;
                } else {
                    $tmpstr = $str[$k - 1];
                    $j = 1;
                }
                if (in_array($tmpstr, $numary)) {
                    $numstr = $tmpstr . $numstr;
                } else {
                    if ($tmpstr == ' ') {
                    } else {
                        break;
                    }
                }
                $k -= $j;
            }
            if (!empty($numstr)) {
                $ret['fcid'] = $ret['cid'];
                $ret['cid'] = jieqi_numcntoab($numstr);
                if ($nstart < $k) {
                    $ret['fcname'] = jieqi_usefultitle(substr($str, $nstart, $k - $nstart));
                }
            }
            $nstart = $i;
            break;
        }
    }
    if ($slen <= $i) {
        $i = $baki;
    }
    $k = $slen;
    $tmpstr = '';
    while (2 <= $k && $nstart < $k) {
        if (128 < ord($str[$k - 1])) {
            $tmpstr = $str[$k - 2] . $str[$k - 1];
            $cl = 2;
        } else {
            $tmpstr = $str[$k - 1];
            $cl = 1;
        }
        $k -= $cl;
        if (in_array($tmpstr, $sary)) {
            $numstr = '';
            while ($i < $k) {
                if (2 <= $k && 128 < ord($str[$k - 1])) {
                    $tmpstr = $str[$k - 2] . $str[$k - 1];
                    $k -= 2;
                } else {
                    $tmpstr = $str[$k - 1];
                    $k--;
                }
                if (in_array($tmpstr, $numary)) {
                    $numstr = $tmpstr . $numstr;
                } else {
                    if ($tmpstr == ' ') {
                    } else {
                        break;
                    }
                }
            }
            if (!empty($numstr)) {
                $ret['sid'] = jieqi_numcntoab($numstr);
            } else {
                $k = $slen;
            }
            break;
        }
    }
    if ($k <= $nstart) {
        $k = $slen;
    }
    while ($nstart < $k) {
        if (2 <= $k && 128 < ord($str[$k - 1])) {
            $tmpstr = $str[$k - 2] . $str[$k - 1];
            $j = 2;
        } else {
            $tmpstr = $str[$k - 1];
            $j = 1;
        }
        if (!in_array($tmpstr, $aary)) {
            break;
        }
        $k -= $j;
    }
    if ($nstart < $k) {
        $ret['cname'] = jieqi_usefultitle(substr($str, $nstart, $k - $nstart));
    }
    if (100 <= $ret['vid']) {
        $ret['vid'] = 0;
    } else {
        if (substr($str, 0, 5) == '正文 ') {
            $ret['vid'] = 1;
        }
    }
    if ($ret['vid'] == 0 && 0 < $ret['cid'] && 0 < strpos($str, '章') && 0 < strpos($str, '节')) {
        $numstr1 = jieqi_getsnumbyid($str, '章');
        $numstr2 = jieqi_getsnumbyid($str, '节');
        if (!empty($numstr1) && !empty($numstr2)) {
            $ret['vid'] = jieqi_numcntoab($numstr1);
            $ret['cid'] = jieqi_numcntoab($numstr2);
        }
    }
    if ($ret['cid'] == 0 && 0 < $ret['sid']) {
        $ret['cid'] = $ret['sid'];
        $ret['sid'] = 0;
    }
    if ($ret['cid'] == 0) {
        $numstr = jieqi_getsnumbyid($str, array('、', '：', '.', ':', ' '));
        if (!empty($numstr)) {
            $ret['cid'] = jieqi_numcntoab($numstr);
        } else {
            if (!empty($ret['vid'])) {
                $numstr = jieqi_getsnumbyid($str, array('集', '卷'));
                if (!empty($numstr)) {
                    $ret['cid'] = jieqi_numcntoab($numstr);
                }
            }
        }
    }
    if ($ret['vid'] == 0 && $ret['cid'] == 0 && $ret['sid'] == 0) {
        $ret['cid'] = jieqi_numcntoab($str);
    }
    if (10000 <= $ret['cid']) {
        $ret['cid'] = $ret['cid'] % 10000;
    }
    if (100 <= $ret['sid']) {
        $ret['sid'] = $ret['sid'] % 100;
    }
    $ret['pnum'] = $ret['vid'] * 1000000 + $ret['cid'] * 100 + $ret['sid'];
    return $ret;
}
function jieqi_getsnumbyid($str, $id, $left = false, $start = 0)
{
    if (is_array($id)) {
        $idary = $id;
    } else {
        $idary[] = $id;
    }
    $numstr = '';
    $ret = '';
    $numary = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '百', '千', '万', '上', '中', '下');
    $slen = strlen($str);
    $i = $start;
    while ($i < $slen) {
        $tmpstr = $str[$i];
        if (128 < ord($str[$i]) && $i < $slen - 1) {
            $tmpstr .= $str[$i + 1];
            $cl = 2;
        } else {
            $cl = 1;
        }
        $i += $cl;
        if (in_array($tmpstr, $idary)) {
            if ($left) {
                $k = $i;
                while ($k < $slen) {
                    $tmpstr = $str[$k];
                    if (128 < ord($str[$k]) && $k < $slen - 1) {
                        $tmpstr .= $str[$k + 1];
                        $k++;
                    }
                    $k++;
                    if (in_array($tmpstr, $numary)) {
                        $numstr .= $tmpstr;
                    } else {
                        if ($tmpstr == ' ') {
                        } else {
                            break;
                        }
                    }
                }
            } else {
                $k = $i - $cl;
                $numstr = '';
                while (0 < $k) {
                    if (2 <= $k && 128 < ord($str[$k - 1])) {
                        $tmpstr = $str[$k - 2] . $str[$k - 1];
                        $k -= 2;
                    } else {
                        $tmpstr = $str[$k - 1];
                        $k--;
                    }
                    if (in_array($tmpstr, $numary)) {
                        $numstr = $tmpstr . $numstr;
                    } else {
                        if ($tmpstr == ' ') {
                        } else {
                            break;
                        }
                    }
                }
            }
            if (!empty($numstr)) {
                break;
            }
        }
    }
    return $numstr;
}
function jieqi_numcntoab($str)
{
    $ret = 0;
    $str = trim($str);
    if (is_numeric($str)) {
        $ret = intval($str);
    } else {
        $numary = array('0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '零' => '0', '一' => '1', '二' => '2', '三' => '3', '四' => '4', '五' => '5', '六' => '6', '七' => '7', '八' => '8', '九' => '9', '上' => '1', '中' => '2', '下' => '3');
        $splitary = array('十' => 1, '百' => 2, '千' => 3, '万' => 4);
        $slen = strlen($str);
        $numstr = '';
        $i = $slen - 1;
        $minlen = 0;
        while (0 <= $i) {
            if (0 < $i && 128 < ord($str[$i])) {
                $tmpstr = $str[$i - 1] . $str[$i];
                $i--;
            } else {
                $tmpstr = $str[$i];
            }
            $i--;
            if (isset($numary[$tmpstr])) {
                $numstr = $numary[$tmpstr] . $numstr;
            } else {
                if (isset($splitary[$tmpstr])) {
                    if ($splitary[$tmpstr] < strlen($numstr)) {
                        $numstr = substr($numstr, 0, $splitary[$tmpstr]);
                    } else {
                        if (strlen($numstr) < $splitary[$tmpstr]) {
                            $start = strlen($numstr);
                            for ($j = $start; $j < $splitary[$tmpstr]; $j++) {
                                $numstr = '0' . $numstr;
                            }
                        }
                    }
                    $minlen = $splitary[$tmpstr] + 1;
                } else {
                    $numstr = '0';
                    break;
                }
            }
        }
        if (empty($numstr)) {
            $numstr = '0';
        }
        if (strlen($numstr) < $minlen) {
            $start = strlen($numstr);
            for ($j = $start; $j < $minlen - 1; $j++) {
                $numstr = '0' . $numstr;
            }
            $numstr = '1' . $numstr;
        }
        $ret = intval($numstr);
    }
    return $ret;
}
function jieqi_usefultitle($str)
{
    $str = trim($str);
    $sary = array(' ', '第', '：', ':', '~', '～', '-', '－');
    $slen = strlen($str);
    $s = 0;
    $e = $slen;
    while ($s < $slen) {
        $tmpstr = $str[$s];
        if (128 < ord($str[$s]) && $s < $slen - 1) {
            $tmpstr .= $str[$s + 1];
            $j = 2;
        } else {
            $j = 1;
        }
        if (!in_array($tmpstr, $sary)) {
            break;
        }
        $s += $j;
    }
    while (0 < $e) {
        $tmpstr = $str[$e - 1];
        if (128 < ord($str[$e - 1]) && 1 < $e) {
            $tmpstr = $str[$e - 2] . $tmpstr;
            $j = 2;
        } else {
            $j = 1;
        }
        if (!in_array($tmpstr, $sary)) {
            break;
        }
        $e -= $j;
    }
    if ($s < $e) {
        $ret = substr($str, $s, $e - $s);
    } else {
        $ret = '';
    }
    return $ret;
}
if (!defined('JIEQI_ROOT_PATH')) {
    exit;
}