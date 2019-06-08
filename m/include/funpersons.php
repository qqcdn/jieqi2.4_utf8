<?php

function jieqi_system_personsvars($persons, $format = 's')
{
    global $jieqiModules;
    global $jieqiLang;
    global $jieqiConfigs;
    global $jieqiOption;
    if (!isset($jieqiLang['system'])) {
        jieqi_loadlang('users', 'shop');
    }
    if (!isset($jieqiOption['system'])) {
        jieqi_getconfigs('system', 'option', 'jieqiOption');
    }
    $ret = array();
    foreach ($persons as $k => $v) {
        if (!isset($ret[$k])) {
            if ($format == 'e') {
                $ret[$k] = jieqi_htmlchars($v, ENT_QUOTES);
            } else {
                $ret[$k] = jieqi_htmlstr($v);
            }
            if (isset($jieqiOption['system'][$k])) {
                $ret[$k . '_n'] = $ret[$k];
                if (isset($jieqiOption['system'][$k]['items'][$v])) {
                    $ret[$k] = $jieqiOption['system'][$k]['items'][$v];
                } else {
                    $ret[$k] = $jieqiOption['system'][$k]['items'][$jieqiOption['system'][$k]['default']];
                }
                $ret[$k . '_v'] = $ret[$k];
            } else {
                if ($k == 'addvars') {
                    $addary = @jieqi_unserialize($v);
                    if (is_array($addary)) {
                        if ($format == 'e') {
                            $ret[$k] = jieqi_funtoarray('jieqi_htmlchars', $addary);
                        } else {
                            $ret[$k] = jieqi_funtoarray('jieqi_htmlstr', $addary);
                        }
                    }
                }
            }
        }
    }
    return $ret;
}