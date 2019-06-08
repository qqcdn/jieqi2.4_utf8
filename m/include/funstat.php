<?php

function jieqi_visit_valid($id, $vname, $save = true)
{
    if (!is_numeric($id) || intval($id) <= 0) {
        return false;
    }
    if (isset($_SESSION[$vname])) {
        $arysession = jieqi_unserialize($_SESSION[$vname]);
    } else {
        $arysession = array();
    }
    if (!is_array($arysession)) {
        $arysession = array();
    }
    $tmpary = array();
    $arycookie = array();
    if (isset($_COOKIE['jieqiVisitId'])) {
        $tmpary = jieqi_strtosary($_COOKIE['jieqiVisitId'], '=', ',');
        if (isset($tmpary[$vname])) {
            $arycookie = explode('|', $tmpary[$vname]);
        }
    }
    if (!is_array($arycookie)) {
        $arycookie = array();
    }
    if (in_array($id, $arysession) || in_array($id, $arycookie)) {
        return false;
    }
    if ($save) {
        if (!in_array($id, $arysession) && isset($_SESSION)) {
            $arysession[] = $id;
            $_SESSION[$vname] = serialize($arysession);
        }
        if (!in_array($id, $arycookie)) {
            $arycookie[] = $id;
            $tmpary[$vname] = implode('|', $arycookie);
            setcookie('jieqiVisitId', jieqi_sarytostr($tmpary, '=', ','), JIEQI_NOW_TIME + 3600, '/', JIEQI_COOKIE_DOMAIN, 0);
        }
    }
    return true;
}
function jieqi_visit_ids($id, $vname, $lastvisit = -1)
{
    if (!is_numeric($id) || intval($id) <= 0) {
        return false;
    }
    if (!preg_match('/^\\w+$/is', $vname)) {
        return false;
    }
    $vname = strtolower($vname);
    $ret = array();
    if (JIEQI_ENABLE_CACHE) {
        $logfile = JIEQI_CACHE_PATH . '/cachevars/cachevisit/' . $vname . '.php';
        jieqi_checkdir(dirname($logfile), true);
        if (rand(1, 100) == 1) {
            $visitary = @file($logfile);
            if ($fp = @fopen($logfile, 'w')) {
                @fclose($fp);
            }
            $visitary[] = 0 <= $lastvisit ? $id . '|' . $lastvisit : $id;
            foreach ($visitary as $v) {
                $v = trim($v);
                $tmpary = explode('|', $v);
                $tmpary[0] = intval($tmpary[0]);
                if (!empty($tmpary[0])) {
                    if (array_key_exists($tmpary[0], $ret)) {
                        $ret[$tmpary[0]]['visitnum']++;
                    } else {
                        $ret[$tmpary[0]]['visitnum'] = 1;
                    }
                    if (isset($tmpary[1])) {
                        $ret[$tmpary[0]]['lastvisit'] = intval($tmpary[1]);
                    } else {
                        $ret[$tmpary[0]]['lastvisit'] = -1;
                    }
                }
            }
        } else {
            if ($fp = @fopen($logfile, 'a')) {
                @flock($filenum, LOCK_EX);
                if (0 <= $lastvisit) {
                    @fwrite($fp, $id . '|' . $lastvisit . "\r\n");
                } else {
                    @fwrite($fp, $id . "\r\n");
                }
                @flock($filenum, LOCK_UN);
                @fclose($fp);
                @chmod($logfile, 511);
            }
        }
    } else {
        $ret[$id] = array('visitnum' => 1, 'lastvisit' => $lastvisit);
    }
    return empty($ret) ? false : $ret;
}
function jieqi_visit_stat($id, $table, $fieldstat, $fieldid, $query = NULL, $addnum = 1)
{
    if (jieqi_visit_valid($id, $table . '_' . $fieldstat)) {
        if ($ids = jieqi_visit_ids($id, $table . '_' . $fieldstat)) {
            global $query;
            if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
                jieqi_includedb();
                $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            }
            foreach ($ids as $k => $v) {
                $v['visitnum'] = intval($v['visitnum'] * $addnum);
                $sql = 'UPDATE ' . $table . ' SET ' . $fieldstat . '=' . $fieldstat . '+' . $v['visitnum'] . ' WHERE ' . $fieldid . '=' . intval($k);
                $query->execute($sql);
            }
        }
        return true;
    } else {
        return false;
    }
}
function jieqi_visit_addorup($lasttime, $nowtime = -1)
{
    if ($nowtime < 0) {
        $nowtime = defined('JIEQI_NOW_TIME') ? JIEQI_NOW_TIME : time();
    }
    $lastdate = date('Y-m-d', $lasttime);
    $nowdate = date('Y-m-d', $nowtime);
    $ret = array('day' => 0, 'week' => 0, 'month' => 0, 'all' => 0);
    if ($nowdate != $lastdate && $lasttime < $nowtime) {
        $last_arr = explode('-', $lastdate);
        $now_arr = explode('-', $nowdate);
        $ret['day'] = round((mktime(0, 0, 0, $now_arr[1], $now_arr[2], $now_arr[0]) - mktime(0, 0, 0, $last_arr[1], $last_arr[2], $last_arr[0])) / 3600 / 24);
        if ($ret['day'] < 0) {
            $ret['day'] = 0;
        }
        $lastweek = intval(date('w', $lasttime));
        if ($lastweek == 0) {
            $lastweek = 7;
        }
        $nowweek = intval(date('w', $nowtime));
        if ($nowweek == 0) {
            $nowweek = 7;
        }
        $ret['week'] = ceil(($ret['day'] - $nowweek + $lastweek) / 7);
        if ($ret['week'] < 0) {
            $ret['week'] = 0;
        }
        $ret['month'] = $now_arr[0] * 12 + $now_arr[1] - ($last_arr[0] * 12 + $last_arr[1]);
        if ($ret['month'] < 0) {
            $ret['month'] = 0;
        }
    }
    return $ret;
}