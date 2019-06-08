<?php

function jieqi_special_postcheck(&$post_set, $configs, &$check_errors)
{
    global $jieqiLang;
    global $query;
    if (!isset($jieqiLang['system']['special'])) {
        jieqi_loadlang('special', 'system');
    }
    if (!is_array($check_errors)) {
        $check_errors = array();
    }
    $num_errors = count($check_errors);
    if (!isset($post_set['act']) && isset($post_set['action'])) {
        $post_set['act'] = $post_set['action'];
    }
    if ($post_set['act'] == 'edit' && empty($post_set['speid'])) {
        $check_errors[] = LANG_ERROR_PARAMETER;
    }
    if (isset($post_set['speid'])) {
        $post_set['speid'] = intval($post_set['speid']);
    }
    if (strlen($post_set['spename']) == 0) {
        $check_errors[] = $jieqiLang['system']['special_need_name'];
    }
    if (empty($configs['spesamename'])) {
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = 'SELECT speid FROM ' . $configs['spetable'] . ' WHERE spename = \'' . jieqi_dbslashes($post_set['spename']) . '\' LIMIT 1';
        $query->execute($sql);
        if ($query->getRow()) {
            $check_errors[] = $jieqiLang['system']['special_same_name'];
        }
    }
    $post_set['sortid'] = intval($post_set['sortid']);
    if (!empty($post_set['speimage']['name'])) {
        if (!preg_match('/\\.(gif|jpg|jpeg|png|bmp)$/i', $_FILES['speimage']['name'])) {
            $check_errors[] = $jieqiLang['system']['special_image_errtype'];
        }
    }
    return $num_errors < count($check_errors) ? false : true;
}
function jieqi_special_add(&$post_set, $configs)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $imgflag = 0;
    $sql = 'INSERT INTO ' . $configs['spetable'] . ' (`sortid` ,`addtime` ,`edittime` ,`userid` ,`username` ,`spename` ,`spedesc` ,`linknum` ,`toptime` ,`lastvisit` ,`dayvisit` ,`weekvisit` ,`monthvisit` ,`allvisit` ,`lastvote` ,`dayvote` ,`weekvote` ,`monthvote` ,`allvote` ,`imgflag` ,`isgood` ,`display` ) VALUES (\'' . $post_set['sortid'] . '\', \'' . JIEQI_NOW_TIME . '\', \'0\', \'' . intval($_SESSION['jieqiUserId']) . '\', \'' . jieqi_dbslashes($_SESSION['jieqiUserName']) . '\', \'' . jieqi_dbslashes($post_set['spename']) . '\', \'' . jieqi_dbslashes($post_set['spedesc']) . '\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'0\', \'' . intval($imgflag) . '\', \'0\', \'' . intval($configs['display']) . '\');';
    if ($query->execute($sql)) {
        return $query->db->getInsertId();
    } else {
        return 0;
    }
}