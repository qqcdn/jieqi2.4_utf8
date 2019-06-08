<?php

define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_NOCONVERT_CHAR', '1');
require_once 'global.php';
if (jieqi_checklogin(true)) {
    jieqi_getconfigs('system', 'configs');
    include_once JIEQI_ROOT_PATH . '/include/funusers.php';
    jieqi_system_avatarset();
    $avatardir = jieqi_uploadpath($jieqiConfigs['system']['avatardir'], 'system');
    $avatardir .= jieqi_getsubdir($_SESSION['jieqiUserId']);
    $file = $avatardir . '/' . $_SESSION['jieqiUserId'] . '_tmp' . $jieqiConfigs['system']['avatardt'];
    if (is_file($file)) {
        $prefix = substr(strrchr(trim(strtolower($file)), '.'), 1);
        switch ($prefix) {
            case 'jpg':
            case 'jpeg':
                header('Content-type: image/jpeg');
                break;
            case 'gif':
                header('Content-type: image/gif');
                break;
            case 'png':
                header('Content-type: image/png');
                break;
            case 'bmp':
                header('Content-type: image/bmp');
                break;
            default:
                exit;
        }
        echo file_get_contents($file);
    }
}