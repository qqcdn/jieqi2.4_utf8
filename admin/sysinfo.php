<?php

function jieqi_zendguardloader()
{
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    preg_match('/Zend(\\s|&nbsp;)Guard(\\s|&nbsp;)Loader(\\s|&nbsp;)v([\\.\\d]*),/is', $phpinfo, $matches);
    if (!empty($matches[4])) {
        return $matches[4];
    } else {
        return '';
    }
}
function jieqi_issupport($var)
{
    global $jieqiLang;
    if ($var) {
        return $jieqiLang['system']['sinfo_is_support'];
    } else {
        return $jieqiLang['system']['sinfo_not_support'];
    }
}
define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
jieqi_checklogin();
jieqi_loadlang('sysinfo', JIEQI_MODULE_NAME);
if ($jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail(LANG_NEED_ADMIN);
}
include_once JIEQI_ROOT_PATH . '/admin/header.php';
$sysinfos = array();
if (floatval(PHP_VERSION) < 4.3) {
    $state = 'error';
} else {
    $state = 'ok';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_php_version'], 'value' => PHP_VERSION, 'note' => $jieqiLang['system']['snote_php_version'], 'state' => $state);
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_php_os'], 'value' => PHP_OS, 'note' => $jieqiLang['system']['snote_php_os'], 'state' => 'ok');
if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = JIEQI_ROOT_PATH;
}
$tmpvar = intval(disk_free_space($_SERVER['DOCUMENT_ROOT']) / 1048576);
if (1024 < $tmpvar) {
    $tmpvar = sprintf('%0.1fG', $tmpvar / 1024);
    $state = 'ok';
} else {
    $tmpvar .= 'M';
    $state = 'warning';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_disk_space'], 'value' => $tmpvar, 'note' => $jieqiLang['system']['snote_disk_space'], 'state' => $state);
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_server_name'], 'value' => $_SERVER['SERVER_NAME'], 'note' => $jieqiLang['system']['snote_server_name'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_server_port'], 'value' => getenv('SERVER_PORT'), 'note' => $jieqiLang['system']['snote_server_port'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_server_software'], 'value' => $_SERVER['SERVER_SOFTWARE'], 'note' => $jieqiLang['system']['snote_server_software'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_accept_language'], 'value' => getenv('HTTP_ACCEPT_LANGUAGE'), 'note' => $jieqiLang['system']['snote_accept_language'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_document_root'], 'value' => $_SERVER['DOCUMENT_ROOT'], 'note' => $jieqiLang['system']['snote_document_root'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_server_time'], 'value' => date('Y-m-d H:i:s'), 'note' => $jieqiLang['system']['snote_server_time'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_zend_version'], 'value' => zend_version(), 'note' => $jieqiLang['system']['snote_zend_version'], 'state' => 'ok');
if (floatval(jieqi_zendguardloader()) < 3.3) {
    $state = 'error';
} else {
    $state = 'ok';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_zend_guardloader'], 'value' => jieqi_zendguardloader(), 'note' => $jieqiLang['system']['snote_zend_guardloader'], 'state' => $state);
$tmpvar = get_cfg_var('disable_functions');
if (empty($tmpvar)) {
    $tmpvar = $jieqiLang['system']['sinfo_empty_value'];
    $state = 'ok';
} else {
    $state = 'notice';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_disable_functions'], 'value' => $tmpvar, 'note' => $jieqiLang['system']['snote_disable_functions'], 'state' => $state);
if (get_cfg_var('register_globals')) {
    $state = 'notice';
} else {
    $state = 'ok';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_register_globals'], 'value' => jieqi_issupport(get_cfg_var('register_globals')), 'note' => $jieqiLang['system']['snote_register_globals'], 'state' => $state);
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_magic_quotes'], 'value' => jieqi_issupport(get_cfg_var('magic_quotes_gpc')), 'note' => $jieqiLang['system']['snote_magic_quotes'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_memory_limit'], 'value' => get_cfg_var('memory_limit'), 'note' => $jieqiLang['system']['snote_memory_limit'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_upload_maxsize'], 'value' => get_cfg_var('upload_max_filesize'), 'note' => $jieqiLang['system']['snote_upload_maxsize'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_post_maxsize'], 'value' => get_cfg_var('post_max_size'), 'note' => $jieqiLang['system']['snote_post_maxsize'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_max_exetime'], 'value' => get_cfg_var('max_execution_time'), 'note' => $jieqiLang['system']['snote_max_exetime'], 'state' => 'ok');
if (get_cfg_var('display_errors')) {
    $state = 'notice';
} else {
    $state = 'ok';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_display_errors'], 'value' => jieqi_issupport(get_cfg_var('display_errors')), 'note' => $jieqiLang['system']['snote_display_errors'], 'state' => $state);
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_smtp_support'], 'value' => jieqi_issupport(get_cfg_var('smtp')), 'note' => $jieqiLang['system']['snote_smtp_support'], 'state' => 'ok');
if (get_cfg_var('safe_mode')) {
    $state = 'notice';
} else {
    $state = 'ok';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_safe_mode'], 'value' => jieqi_issupport(get_cfg_var('safe_mode')), 'note' => $jieqiLang['system']['snote_safe_mode'], 'state' => $state);
if (function_exists('xml_parser_set_option')) {
    $state = 'ok';
} else {
    $state = 'error';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_xml_parser'], 'value' => jieqi_issupport(function_exists('xml_parser_set_option')), 'note' => $jieqiLang['system']['snote_xml_parser'], 'state' => $state);
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_xml_support'], 'value' => jieqi_issupport(get_cfg_var('XML Support')), 'note' => $jieqiLang['system']['snote_xml_support'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_ftp_support'], 'value' => jieqi_issupport(get_cfg_var('FTP Support')), 'note' => $jieqiLang['system']['snote_ftp_support'], 'state' => 'ok');
if (get_cfg_var('allow_url_fopen')) {
    $state = 'ok';
} else {
    $state = 'warning';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_url_fopen'], 'value' => jieqi_issupport(get_cfg_var('allow_url_fopen')), 'note' => $jieqiLang['system']['snote_url_fopen'], 'state' => $state);
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_enable_dl'], 'value' => jieqi_issupport(get_cfg_var('enable_dl')), 'note' => $jieqiLang['system']['snote_enable_dl'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_imap_support'], 'value' => jieqi_issupport(function_exists('imap_close')), 'note' => $jieqiLang['system']['snote_imap_support'], 'state' => 'ok');
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_calendar_support'], 'value' => jieqi_issupport(function_exists('JDToGregorian')), 'note' => $jieqiLang['system']['snote_calendar_support'], 'state' => 'ok');
if (function_exists('gzclose')) {
    $state = 'ok';
} else {
    $state = 'warning';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_zlib_support'], 'value' => jieqi_issupport(function_exists('gzclose')), 'note' => $jieqiLang['system']['snote_zlib_support'], 'state' => $state);
if (function_exists('session_start')) {
    $state = 'ok';
} else {
    $state = 'error';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_session_support'], 'value' => jieqi_issupport(function_exists('session_start')), 'note' => $jieqiLang['system']['snote_session_support'], 'state' => $state);
if (function_exists('fsockopen')) {
    $state = 'ok';
} else {
    $state = 'warning';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_socket_support'], 'value' => jieqi_issupport(function_exists('fsockopen')), 'note' => $jieqiLang['system']['snote_socket_support'], 'state' => $state);
if (function_exists('preg_match')) {
    $state = 'ok';
} else {
    $state = 'error';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_preg_support'], 'value' => jieqi_issupport(function_exists('preg_match')), 'note' => $jieqiLang['system']['snote_preg_support'], 'state' => $state);
if (function_exists('imageline')) {
    $state = 'ok';
} else {
    $state = 'notice';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_gd_support'], 'value' => jieqi_issupport(function_exists('gd_info')), 'note' => $jieqiLang['system']['snote_gd_support'], 'state' => $state);
if (function_exists('gd_info')) {
    $tmpary = gd_info();
    $sysinfos[] = array('name' => $jieqiLang['system']['sinfo_gd_version'], 'value' => $tmpary['GD Version'], 'note' => $jieqiLang['system']['snote_gd_version'], 'state' => 'ok');
    if ($tmpary['FreeType Support']) {
        $state = 'ok';
    } else {
        $state = 'warning';
    }
    $sysinfos[] = array('name' => $jieqiLang['system']['sinfo_freetype_support'], 'value' => jieqi_issupport($tmpary['FreeType Support']), 'note' => $jieqiLang['system']['snote_freetype_support'], 'state' => $state);
    if ($tmpary['GIF Read Support']) {
        $state = 'ok';
    } else {
        $state = 'notice';
    }
    $sysinfos[] = array('name' => $jieqiLang['system']['sinfo_gif_read'], 'value' => jieqi_issupport($tmpary['GIF Read Support']), 'note' => $jieqiLang['system']['snote_gif_read'], 'state' => $state);
    if ($tmpary['GIF Create Support']) {
        $state = 'ok';
    } else {
        $state = 'notice';
    }
    $sysinfos[] = array('name' => $jieqiLang['system']['sinfo_gif_create'], 'value' => jieqi_issupport($tmpary['GIF Create Support']), 'note' => $jieqiLang['system']['snote_gif_create'], 'state' => $state);
    if ($tmpary['JPEG Support']) {
        $state = 'ok';
    } else {
        $state = 'notice';
    }
    $sysinfos[] = array('name' => $jieqiLang['system']['sinfo_jpg_support'], 'value' => jieqi_issupport($tmpary['JPEG Support']), 'note' => $jieqiLang['system']['snote_jpg_support'], 'state' => $state);
    if ($tmpary['PNG Support']) {
        $state = 'ok';
    } else {
        $state = 'notice';
    }
    $sysinfos[] = array('name' => $jieqiLang['system']['sinfo_png_support'], 'value' => jieqi_issupport($tmpary['PNG Support']), 'note' => $jieqiLang['system']['snote_png_support'], 'state' => $state);
    if ($tmpary['WBMP Support']) {
        $state = 'ok';
    } else {
        $state = 'notice';
    }
    $sysinfos[] = array('name' => $jieqiLang['system']['sinfo_wbmp_support'], 'value' => jieqi_issupport($tmpary['WBMP Support']), 'note' => $jieqiLang['system']['snote_wbmp_support'], 'state' => $state);
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_iconv_support'], 'value' => jieqi_issupport(function_exists('iconv')), 'note' => $jieqiLang['system']['snote_iconv_support'], 'state' => 'ok');
if (function_exists('mysql_connect') || function_exists('mysqli_connect')) {
    $state = 'ok';
} else {
    $state = 'error';
}
$sysinfos[] = array('name' => $jieqiLang['system']['sinfo_mysql_support'], 'value' => jieqi_issupport(function_exists('mysql_close')), 'note' => $jieqiLang['system']['snote_mysql_support'], 'state' => $state);
$jieqiTpl->assign_by_ref('sysinfos', $sysinfos);
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/sysinfo.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';