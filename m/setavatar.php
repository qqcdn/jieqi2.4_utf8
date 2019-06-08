<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
jieqi_loadlang('users', JIEQI_MODULE_NAME);
include_once JIEQI_ROOT_PATH . '/class/users.php';
$users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if (!$jieqiUsers) {
    jieqi_printfail(LANG_NO_USER);
}
jieqi_getconfigs('system', 'configs');
include_once JIEQI_ROOT_PATH . '/include/funusers.php';
jieqi_system_avatarset();
$avatardir = jieqi_uploadpath($jieqiConfigs['system']['avatardir'], 'system');
$avatardir .= jieqi_getsubdir($jieqiUsers->getVar('uid', 'n'));
if (!isset($_POST['act']) && isset($_GET['act']) && in_array($_GET['act'], array('cutavatar', 'show'))) {
    $_POST['act'] = $_GET['act'];
}
if (!isset($_POST['act'])) {
    $_POST['act'] = 'show';
}
switch ($_POST['act']) {
    case 'cutsave':
        jieqi_checkpost();
        $old_avatar = $jieqiUsers->getVar('avatar', 'n');
        $newfile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . $jieqiConfigs['system']['avatardt'];
        $smallfile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . 's' . $jieqiConfigs['system']['avatardt'];
        $iconfile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . 'i' . $jieqiConfigs['system']['avatardt'];
        $tmpfile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . '_tmp' . $jieqiConfigs['system']['avatardt'];
        if (is_file($tmpfile)) {
            if (0 < $old_avatar && isset($jieqi_image_type[$old_avatar])) {
                $old_imagefile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . $jieqi_image_type[$old_avatar];
                jieqi_delfile($old_imagefile);
            }
            jieqi_system_avatarresize($jieqiUsers->getVar('uid', 'n'), $tmpfile, $_REQUEST['cut_pos']);
            $image_type = 0;
            $image_postfix = $jieqiConfigs['system']['avatardt'];
            foreach ($jieqi_image_type as $k => $v) {
                if ($image_postfix == $v) {
                    $image_type = $k;
                    break;
                }
            }
            $old_avatar = $jieqiUsers->getVar('avatar', 'n');
            $jieqiUsers->unsetNew();
            $jieqiUsers->setVar('avatar', $image_type);
            if (!$users_handler->insert($jieqiUsers)) {
                jieqi_printfail($jieqiLang['system']['avatar_set_failure']);
            } else {
                jieqi_jumppage(JIEQI_URL . '/setavatar.php', LANG_DO_SUCCESS, $jieqiLang['system']['avatar_set_success']);
            }
        } else {
            jieqi_printfail($jieqiLang['system']['avatar_set_failure']);
        }
        break;
    case 'cutavatar':
        include_once JIEQI_ROOT_PATH . '/header.php';
        $jieqiTpl->assign('url_avatar', JIEQI_URL . '/tmpavatar.php?time=' . JIEQI_NOW_TIME);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/cutavatar.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
    case 'upload':
        jieqi_checkpost();
        $errtext = '';
        if (empty($_FILES['avatarimage']['name'])) {
            $errtext .= $jieqiLang['system']['need_avatar_image'] . '<br />';
        }
        $image_postfix = '';
        if (!empty($_FILES['avatarimage']['name'])) {
            if (0 < $_FILES['avatarimage']['error']) {
                $errtext = $jieqiLang['system']['avatar_upload_failure'];
            } else {
                $image_postfix = strrchr(trim(strtolower($_FILES['avatarimage']['name'])), '.');
                if (preg_match('/\\.(gif|jpg|jpeg|png|bmp)$/i', $_FILES['avatarimage']['name'])) {
                    $typeary = explode(' ', trim($jieqiConfigs['system']['avatartype']));
                    foreach ($typeary as $k => $v) {
                        if (substr($v, 0, 1) != '.') {
                            $typeary[$k] = '.' . $typeary[$k];
                        }
                    }
                    if (!in_array($image_postfix, $typeary)) {
                        $errtext .= sprintf($jieqiLang['system']['avatar_type_error'], $jieqiConfigs['system']['avatartype']) . '<br />';
                    } else {
                        if (function_exists('getimagesize') && getimagesize($_FILES['avatarimage']['tmp_name']) === false) {
                            $errtext .= sprintf($jieqiLang['system']['avatar_not_image'], $_FILES['avatarimage']['name']) . '<br />';
                        }
                    }
                    if (intval($jieqiConfigs['system']['avatarsize']) * 1024 < $_FILES['avatarimage']['size']) {
                        $errtext .= sprintf($jieqiLang['system']['avatar_filesize_toolarge'], intval($jieqiConfigs['system']['avatarsize'])) . '<br />';
                    }
                } else {
                    $errtext .= sprintf($jieqiLang['system']['avatar_not_image'], $_FILES['avatarimage']['name']) . '<br />';
                }
                if (!empty($errtext)) {
                    jieqi_delfile($_FILES['avatarimage']['tmp_name']);
                }
            }
        } else {
            $errtext = $jieqiLang['system']['avatar_need_upload'];
        }
        if (empty($errtext)) {
            if (function_exists('gd_info') && $jieqiConfigs['system']['avatarcut']) {
                if (!empty($_FILES['avatarimage']['name'])) {
                    $imagefile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . '_tmp' . $jieqiConfigs['system']['avatardt'];
                    jieqi_copyfile($_FILES['avatarimage']['tmp_name'], $imagefile, 511, true);
                    include_once JIEQI_ROOT_PATH . '/lib/image/imageresize.php';
                    $imgresize = new ImageResize();
                    $imgresize->load($imagefile);
                    $imgresize->save($imagefile, true, substr(strrchr(trim(strtolower($imagefile)), '.'), 1));
                    @chmod($imagefile, 511);
                }
                header('Location: ' . JIEQI_URL . '/setavatar.php?act=cutavatar');
                exit;
            } else {
                $image_type = 0;
                if (function_exists('gd_info')) {
                    $image_postfix_save = $jieqiConfigs['system']['avatardt'];
                } else {
                    $image_postfix_save = $image_postfix;
                }
                foreach ($jieqi_image_type as $k => $v) {
                    if ($image_postfix_save == $v) {
                        $image_type = $k;
                        break;
                    }
                }
                $old_avatar = $jieqiUsers->getVar('avatar', 'n');
                $jieqiUsers->unsetNew();
                $jieqiUsers->setVar('avatar', $image_type);
                if (!$users_handler->insert($jieqiUsers)) {
                    jieqi_printfail($jieqiLang['system']['avatar_set_failure']);
                } else {
                    if (!empty($_FILES['avatarimage']['name'])) {
                        if (0 < $old_avatar && isset($jieqi_image_type[$old_avatar])) {
                            $old_imagefile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . $jieqi_image_type[$old_avatar];
                            if (is_file($old_imagefile)) {
                                jieqi_delfile($old_imagefile);
                            }
                        }
                        $imagefile = $avatardir . '/' . $jieqiUsers->getVar('uid', 'n') . $image_postfix;
                        jieqi_copyfile($_FILES['avatarimage']['tmp_name'], $imagefile, 511, true);
                        jieqi_system_avatarresize($jieqiUsers->getVar('uid', 'n'), $imagefile);
                    }
                    jieqi_jumppage(JIEQI_URL . '/setavatar.php', LANG_DO_SUCCESS, $jieqiLang['system']['avatar_set_success']);
                }
            }
        } else {
            jieqi_printfail($errtext);
        }
        break;
    case 'show':
    default:
        include_once JIEQI_ROOT_PATH . '/header.php';
        $avatartype = intval($jieqiUsers->getVar('avatar', 'n'));
        $avatarimg = '';
        if (isset($jieqi_image_type[$avatartype])) {
            $urls = jieqi_geturl('system', 'avatar', $jieqiUsers->getVar('uid', 'n'), 'a', $avatartype);
            if (is_array($urls)) {
                $jieqiTpl->assign('base_avatar', $urls['d']);
                $jieqiTpl->assign('url_avatar', $urls['l']);
                $jieqiTpl->assign('url_avatars', $urls['s']);
                $jieqiTpl->assign('url_avatari', $urls['i']);
            }
        }
        $jieqiTpl->assign('avatartype', $avatartype);
        $jieqiTpl->assign('need_imagetype', $jieqiConfigs['system']['avatartype']);
        $jieqiTpl->assign('max_imagesize', $jieqiConfigs['system']['avatarsize']);
        $jieqiTpl->assign('avatartype', $avatartype);
        if (function_exists('gd_info') && $jieqiConfigs['system']['avatarcut']) {
            $jieqiTpl->assign('avatarcut', 1);
        } else {
            $jieqiTpl->assign('avatarcut', 0);
        }
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/setavatar.html';
        include_once JIEQI_ROOT_PATH . '/footer.php';
        break;
}