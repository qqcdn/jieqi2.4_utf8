<?php

function jieqi_activity_actflag($joindate, $nowdate = 0)
{
    if (empty($nowdate)) {
        $nowdate = date('Ymd', JIEQI_NOW_TIME);
    }
    $actflag = 0;
    $diff = round((strtotime($nowdate) - strtotime($joindate)) / 86400);
    if (0 < $diff) {
        if ($diff == 1) {
            $actflag = 2;
        } else {
            if ($diff <= 3) {
                $actflag = 4;
            } else {
                if ($diff <= 7) {
                    $actflag = 8;
                } else {
                    if ($diff <= 15) {
                        $actflag = 16;
                    } else {
                        if ($diff <= 30) {
                            $actflag = 32;
                        } else {
                            if ($diff <= 90) {
                                $actflag = 64;
                            } else {
                                if ($diff <= 180) {
                                    $actflag = 128;
                                } else {
                                    if ($diff <= 365) {
                                        $actflag = 256;
                                    } else {
                                        if ($diff <= 730) {
                                            $actflag = 512;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $actflag;
}
function jieqi_activity_update($params)
{
    global $query;
    if (!isset($params['acttype']) || !in_array($params['acttype'], array('login', 'pay', 'buy', 'tip'))) {
        return false;
    }
    if (empty($params['userid'])) {
        if (!empty($_SESSION['jieqiUserId'])) {
            $params['userid'] = $_SESSION['jieqiUserId'];
        } else {
            return false;
        }
    }
    $params['userid'] = intval($params['userid']);
    if (empty($params['joindate'])) {
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = 'SELECT joindate FROM ' . jieqi_dbprefix('system_activity') . ' WHERE uiserid = ' . $params['userid'] . ' LIMIT 0, 1';
        $res = $query->execute($sql);
        $row = $query->getRow($res);
        if (is_array($row)) {
            $params['joindate'] = $row['joindate'];
        } else {
            return false;
        }
    }
    $params['joindate'] = intval($params['joindate']);
    if (!isset($params['nowdate'])) {
        $params['nowdate'] = date('Ymd', JIEQI_NOW_TIME);
    }
    $params['nowdate'] = intval($params['nowdate']);
    $actflag = jieqi_activity_actflag($params['joindate'], $params['nowdate']);
    if (0 < $actflag) {
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = 'UPDATE ' . jieqi_dbprefix('system_activity') . ' SET ' . $params['acttype'] . ' = ' . $params['acttype'] . ' | ' . $actflag;
        if ($params['acttype'] == 'login') {
            $sql .= ', lastdate = ' . $params['nowdate'] . ', days = days + 1';
        }
        $sql .= ' WHERE userid = ' . $params['userid'];
        return $query->execute($sql);
    }
    return true;
}