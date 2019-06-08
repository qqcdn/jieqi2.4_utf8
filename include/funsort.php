<?php

function jieqi_sort_get(&$sorts, $sortid)
{
    return isset($sorts[$sortid]) ? $sorts[$sortid] : false;
}
function jieqi_sort_subs(&$sorts, $sortid = 0)
{
    $ret = array();
    foreach ($sorts as $k => $v) {
        if ($v['parentid'] == $sortid) {
            $ret[] = $v;
        }
    }
    return $ret;
}
function jieqi_sort_childs(&$sorts, $sortid = 0)
{
    $ret = $sortid;
    if (!empty($sorts[$sortid]['childs']) && preg_match('/^[\\s\\d\\.,]+$/', $sorts[$sortid]['childs'])) {
        $ret .= ',' . $sorts[$sortid]['childs'];
    }
    return $ret;
}
function jieqi_sort_routes(&$sorts, $sortid = 0)
{
    $ret = array();
    if (isset($sorts[$sortid])) {
        $ret[] = $sorts[$sortid];
        while (0 < $sorts[$sortid]['parentid']) {
            $sortid = $sorts[$sortid]['parentid'];
            array_unshift($ret, $sorts[$sortid]);
        }
    }
    return $ret;
}
function jieqi_sort_layer(&$sorts)
{
    foreach ($sorts as $k => $v) {
        if (0 < $v['parentid']) {
            if (isset($sorts[$v['parentid']]['layer'])) {
                $sorts[$k]['layer'] = intval($sorts[$v['parentid']]['layer']) + 1;
            } else {
                $sorts[$k]['layer'] = 0;
            }
        } else {
            $sorts[$k]['layer'] = 0;
        }
    }
}