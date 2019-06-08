<?php

function jieqi_clean_compiled()
{
    jieqi_delfolder(JIEQI_COMPILED_PATH, false);
}
function jieqi_clean_cache()
{
    global $jieqiCache;
    $jieqiCache->clear(JIEQI_CACHE_PATH);
}
function jieqi_clean_blockcache()
{
    global $jieqiCache;
    global $jieqiModules;
    if (is_a($jieqiCache, 'JieqiCacheMemcached')) {
        $jieqiCache->clear(JIEQI_CACHE_PATH);
    } else {
        foreach ($jieqiModules as $mod) {
            $dirname = JIEQI_CACHE_PATH . $mod['dir'] . '/templates';
            $handle = @opendir($dirname);
            while ($handle !== false && ($file = @readdir($handle)) !== false) {
                if ($file == 'blocks' && is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
                    jieqi_delfolder($dirname . DIRECTORY_SEPARATOR . $file, true);
                }
            }
            @closedir($handle);
        }
    }
}
function jieqi_clean_pagecache()
{
    global $jieqiCache;
    global $jieqiModules;
    if (is_a($jieqiCache, 'JieqiCacheMemcached')) {
        $jieqiCache->clear(JIEQI_CACHE_PATH);
    } else {
        foreach ($jieqiModules as $mod) {
            $dirname = JIEQI_CACHE_PATH . $mod['dir'] . '/templates';
            $handle = @opendir($dirname);
            while ($handle !== false && ($file = @readdir($handle)) !== false) {
                if ($file != '.' && $file != '..' && $file != 'blocks') {
                    if (is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
                        jieqi_delfolder($dirname . DIRECTORY_SEPARATOR . $file, true);
                    } else {
                        @unlink($dirname . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
            @closedir($handle);
        }
    }
}
define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
jieqi_checklogin();
@set_time_limit(3600);
@session_write_close();
define('JIEQI_ADMIN_PAGE', 1);
jieqi_loadlang('cache', JIEQI_MODULE_NAME);
if ($jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail(LANG_NEED_ADMIN);
}
if (empty($_REQUEST['target'])) {
    jieqi_printfail(LANG_ERROR_PARAMETER);
} else {
    if (isset($_REQUEST['confirm']) && $_REQUEST['confirm'] != 1) {
        jieqi_msgwin(LANG_NOTICE, sprintf($jieqiLang['system']['cache_clean_notice'], jieqi_addurlvars(array('confirm' => 1))));
    }
}
if ($_REQUEST['target'] == 'all') {
    echo str_repeat(' ', 4096);
    echo $jieqiLang['system']['start_clean_cache'];
    ob_flush();
    flush();
    jieqi_clean_pagecache();
    echo $jieqiLang['system']['start_clean_blockcache'];
    ob_flush();
    flush();
    jieqi_clean_blockcache();
    echo $jieqiLang['system']['start_clean_compiled'];
    ob_flush();
    flush();
    jieqi_clean_compiled();
    jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['cache_clean_success']);
} else {
    if ($_REQUEST['target'] == 'cache') {
        echo str_repeat(' ', 4096);
        echo $jieqiLang['system']['start_clean_cache'];
        ob_flush();
        flush();
        jieqi_clean_pagecache();
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['cache_clean_success']);
    } else {
        if ($_REQUEST['target'] == 'blockcache') {
            echo str_repeat(' ', 4096);
            echo $jieqiLang['system']['start_clean_blockcache'];
            ob_flush();
            flush();
            jieqi_clean_blockcache();
            jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['cache_clean_success']);
        } else {
            if ($_REQUEST['target'] == 'compiled') {
                echo str_repeat(' ', 4096);
                echo $jieqiLang['system']['start_clean_compiled'];
                ob_flush();
                flush();
                jieqi_clean_compiled();
                jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['cache_clean_success']);
            } else {
                jieqi_printfail(LANG_ERROR_PARAMETER);
            }
        }
    }
}