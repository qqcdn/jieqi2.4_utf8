<?php

function jieqi_article_tipvars($row, $format = 's')
{
    global $jieqiModules;
    global $jieqiConfigs;
    global $jieqiOption;
    global $jieqiLang;
    $ret = array();
    foreach ($row as $k => $v) {
        if (!isset($ret[$k])) {
            if ($format == 'e') {
                $ret[$k] = jieqi_htmlchars($v, ENT_QUOTES);
            } else {
                $ret[$k] = jieqi_htmlstr($v);
            }
        }
    }
    return $ret;
}