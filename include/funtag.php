<?php

function jieqi_tag_clean($tags, $split = array(' ', '　', ',', '，', ';', '；', '/', '、'))
{
    $tagary = array();
    if (is_array($tags)) {
        foreach ($tags as $v) {
            $v = trim($v);
            if (is_array($split)) {
                $v = isset($split[$v]) ? $split[$v] : '';
            }
            if (0 < strlen($v) && !in_array($v, $tagary)) {
                $tagary[] = $v;
            }
        }
    } else {
        if (!is_array($split)) {
            $split = array($split);
        }
        if (!function_exists('mb_split')) {
            foreach ($split as $k => $v) {
                $split[$k] = preg_quote($v);
            }
            $tmpary = mb_split(implode('|', $split), $tags);
        } else {
            $tmpary = array();
            $len = strlen($tags);
            $slary = array();
            foreach ($split as $k => $v) {
                $slary[$k] = strlen($v);
            }
            $utf8 = JIEQI_SYSTEM_CHARSET == 'utf-8' ? true : false;
            $tmpstr = '';
            $i = 0;
            while ($i < $len) {
                foreach ($split as $k => $v) {
                    if (substr($tags, $i, $slary[$k]) == $v) {
                        if (0 < strlen($tmpstr)) {
                            $tmpary[] = $tmpstr;
                        }
                        $tmpstr = '';
                        $i += $slary[$k];
                        break;
                    }
                }
                $cs = 1;
                $asc = ord($tags[$i]);
                if (128 < $asc) {
                    if (!$utf8) {
                        $cs = 2;
                    } else {
                        if (192 <= $asc && $asc <= 223) {
                            $cs = 2;
                        } else {
                            if (224 <= $asc && $asc <= 239) {
                                $cs = 3;
                            } else {
                                if (240 <= $asc && $asc <= 247) {
                                    $cs = 4;
                                }
                            }
                        }
                    }
                }
                $tmpstr .= substr($tags, $i, $cs);
                $i += $cs;
            }
            if (0 < strlen($tmpstr)) {
                $tmpary[] = $tmpstr;
            }
        }
        foreach ($tmpary as $v) {
            $v = trim($v);
            if (0 < strlen($v) && !in_array($v, $tagary)) {
                $tagary[] = $v;
            }
        }
    }
    return $tagary;
}
function jieqi_tag_save($tags, $articleid, $tables)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $articleid = intval($articleid);
    foreach ($tags as $tag) {
        $sql = 'SELECT * FROM ' . $tables['tag'] . ' WHERE tagname = \'' . jieqi_dbslashes($tag) . '\' LIMIT 0,1';
        $upflag = true;
        $query->execute($sql);
        if ($row = $query->getRow()) {
            $tagid = intval($row['tagid']);
            $sql = 'INSERT INTO ' . $tables['taglink'] . ' (`tagid` ,`articleid` ,`linktime`)VALUES (\'' . $tagid . '\', \'' . intval($articleid) . '\', \'' . intval(JIEQI_NOW_TIME) . '\');';
            if ($query->execute($sql)) {
                $sql = 'UPDATE ' . $tables['tag'] . ' SET linknum = linknum + 1 WHERE tagid = ' . $tagid;
                $query->execute($sql);
            } else {
                $upflag = false;
            }
        } else {
            $sql = 'INSERT INTO ' . $tables['tag'] . '(`tagid` ,`tagname` ,`addtime` ,`tagsort` ,`userid` ,`username` ,`linknum` ,`display` )VALUES (0 , \'' . jieqi_dbslashes($tag) . '\', \'' . intval(JIEQI_NOW_TIME) . '\', \'0\', \'' . jieqi_dbslashes($_SESSION['jieqiUserId']) . '\', \'' . jieqi_dbslashes($_SESSION['jieqiUserName']) . '\', \'1\', \'0\');';
            $query->execute($sql);
            $tagid = intval($query->db->getInsertId());
            $sql = 'INSERT INTO ' . $tables['taglink'] . ' (`tagid` ,`articleid` ,`linktime`)VALUES (\'' . $tagid . '\', \'' . intval($articleid) . '\', \'' . intval(JIEQI_NOW_TIME) . '\');';
            $query->execute($sql);
        }
    }
    return true;
}
function jieqi_tag_update($oldtags, $newtags, $articleid, $tables)
{
    global $query;
    $deltags = array();
    $addtags = array();
    foreach ($oldtags as $v) {
        if (!in_array($v, $newtags) && !in_array($v, $deltags)) {
            $deltags[] = $v;
        }
    }
    foreach ($newtags as $v) {
        if (!in_array($v, $oldtags) && !in_array($v, $addtags)) {
            $addtags[] = $v;
        }
    }
    if (!empty($deltags)) {
        jieqi_tag_delete($deltags, $articleid, $tables);
    }
    if (!empty($addtags)) {
        jieqi_tag_save($addtags, $articleid, $tables);
    }
    return true;
}
function jieqi_tag_delete($tags, $articleid, $tables)
{
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    $articleid = intval($articleid);
    foreach ($tags as $tag) {
        $sql = 'SELECT * FROM ' . $tables['tag'] . ' WHERE tagname = \'' . jieqi_dbslashes($tag) . '\' LIMIT 0,1';
        $query->execute($sql);
        if ($row = $query->getRow()) {
            $tagid = intval($row['tagid']);
            $sql = 'DELETE FROM ' . $tables['taglink'] . ' WHERE tagid = ' . $tagid . ' AND articleid = ' . $articleid;
            $query->execute($sql);
            if (0 < $query->db->getAffectedRows()) {
                $uptag = true;
                if ($row['linknum'] <= 1) {
                    $sql = 'SELECT count(*) as cot FROM ' . $tables['taglink'] . ' WHERE tagid = ' . $tagid;
                    $query->execute($sql);
                    if ($row1 = $query->getRow()) {
                        if ($row1['cot'] == 0) {
                            $uptag = false;
                            $sql = 'DELETE FROM ' . $tables['tag'] . ' WHERE tagid = ' . $tagid;
                            $query->execute($sql);
                        }
                    }
                }
                if ($uptag) {
                    $sql = 'UPDATE ' . $tables['tag'] . ' SET linknum = linknum - 1 WHERE tagid = ' . $tagid;
                    $query->execute($sql);
                }
            }
        }
    }
    return true;
}