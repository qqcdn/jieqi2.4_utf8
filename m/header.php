<?php

function jieqi_get_block($blockconfig, $retflag = 0)
{
    global $jieqiUsersStatus;
    global $jieqiModules;
    global $jieqiTpl;
    global $jieqiCache;
    if (!is_a($jieqiTpl, 'JieqiTpl')) {
        $jieqiTpl = JieqiTpl::getInstance();
    }
    $blockret = array();
    if ($jieqiUsersStatus == JIEQI_GROUP_GUEST && 0 < ($blockconfig['publish'] & 1) || $jieqiUsersStatus != JIEQI_GROUP_GUEST && 0 < ($blockconfig['publish'] & 2)) {
        $blockpath = $blockconfig['module'] == 'system' ? JIEQI_ROOT_PATH : $jieqiModules[$blockconfig['module']]['path'];
        if (0 < $blockconfig['custom']) {
            $blockfile = JIEQI_ROOT_PATH . '/blocks/block_custom.php';
        } else {
            $blockfile = $blockpath . '/blocks/' . trim($blockconfig['filename']) . '.php';
        }
        $usecache = false;
        if ($blockconfig['contenttype'] != JIEQI_CONTENT_PHP && empty($blockconfig['hasvars'])) {
            if (0 < $blockconfig['custom']) {
                $templatefile = empty($blockconfig['bid']) ? empty($blockconfig['template']) ? $blockconfig['filename'] . '.html' : $blockconfig['template'] : 'block_custom' . $blockconfig['bid'] . '.html';
            } else {
                $templatefile = empty($blockconfig['template']) ? $blockconfig['filename'] . '.html' : $blockconfig['template'];
            }
            $templatefile = $blockpath . '/templates/blocks/' . $templatefile;
            if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $templatefile))) {
                $templatefile = str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $templatefile);
            }
            $cachefile = str_replace(JIEQI_ROOT_PATH, JIEQI_CACHE_PATH, $templatefile);
            if ($jieqiCache->iscached($cachefile)) {
                $usecache = true;
            }
        }
        if ($usecache) {
            $blockret = array('side' => intval($blockconfig['side']), 'title' => $blockconfig['title'], 'content' => $jieqiCache->get($cachefile));
        } else {
            $blockfile = @realpath($blockfile);
            if (is_file($blockfile) && preg_match('/blocks[\\/\\\\]block_\\w+\\.php$/i', $blockfile)) {
                $tpl_bak_vars = $jieqiTpl->get_all_assign();
                $tpl_bak_caching = $jieqiTpl->getCaching();
                $tpl_bak_cachetype = $jieqiTpl->getCachType();
                $tpl_bak_cachetime = $jieqiTpl->getCacheTime();
                $tpl_bak_overtime = $jieqiTpl->getOverTime();
                include_once $blockfile;
                $jieqiBlock = new $blockconfig['classname']($blockconfig);
                $blockret = array('side' => intval($blockconfig['side']), 'title' => $jieqiBlock->getTitle(), 'content' => $jieqiBlock->getContent());
                $jieqiTpl->set_all_assign($tpl_bak_vars);
                $jieqiTpl->setCaching($tpl_bak_caching);
                $jieqiTpl->setCachType($tpl_bak_cachetype);
                $jieqiTpl->setCacheTime($tpl_bak_cachetime);
                $jieqiTpl->setOverTime($tpl_bak_overtime);
            } else {
                return false;
            }
        }
    }
    if ($retflag == 1) {
        return $blockret['content'];
    } else {
        if ($retflag == 2) {
            return $blockret['title'];
        } else {
            if ($retflag == 3) {
                return $blockret['side'];
            } else {
                return $blockret;
            }
        }
    }
}
function jieqi_get_pageset($vars = NULL)
{
    global $jieqiTset;
    if (empty($vars) || !is_array($vars)) {
        $vars = array();
    }
    if (!isset($vars['var'])) {
        if (isset($jieqiTset['jieqi_page_var']) && preg_match('/^\\w+$/', $jieqiTset['jieqi_page_var'])) {
            $vars['var'] = $jieqiTset['jieqi_page_var'];
        } else {
            $vars['var'] = 'page';
        }
    }
    if (!isset($vars['page'])) {
        if (isset($_REQUEST[$vars['var']])) {
            $_REQUEST[$vars['var']] = intval($_REQUEST[$vars['var']]);
            if ($_REQUEST[$vars['var']] < 1) {
                $_REQUEST[$vars['var']] = 1;
            }
        } else {
            $_REQUEST[$vars['var']] = 1;
        }
        $vars['page'] = $_REQUEST[$vars['var']];
    } else {
        if (!is_numeric($vars['page']) || $vars['page'] <= 0) {
            $vars['page'] = 1;
        }
    }
    $vars['rows'] = 0;
    if (isset($jieqiTset['jieqi_page_rows']) && is_numeric($jieqiTset['jieqi_page_rows'])) {
        $vars['rows'] = intval($jieqiTset['jieqi_page_rows']);
    } else {
        if (defined('JIEQI_PAGE_ROWS')) {
            $vars['rows'] = intval(JIEQI_PAGE_ROWS);
        }
    }
    if ($vars['rows'] <= 0) {
        $vars['rows'] = 30;
    }
    $vars['start'] = ($vars['page'] - 1) * $vars['rows'];
    if (!isset($vars['links'])) {
        if (isset($jieqiTset['jieqi_page_links']) && is_numeric($jieqiTset['jieqi_page_links'])) {
            $vars['links'] = 0 < intval($jieqiTset['jieqi_page_links']) ? intval($jieqiTset['jieqi_page_links']) : 10;
        } else {
            $vars['links'] = 10;
        }
    }
    if (!isset($vars['ajax'])) {
        if (isset($jieqiTset['jieqi_page_ajax']) && !empty($jieqiTset['jieqi_page_ajax'])) {
            $vars['ajax'] = 1;
        } else {
            $vars['ajax'] = 0;
        }
    }
    if (!isset($vars['style'])) {
        if (isset($jieqiTset['jieqi_page_style']) && !empty($jieqiTset['jieqi_page_style'])) {
            $vars['style'] = $jieqiTset['jieqi_page_style'];
        }
    }
    if (!isset($vars['contents'])) {
        if (isset($jieqiTset['jieqi_page_contents']) && !empty($jieqiTset['jieqi_page_contents'])) {
            $vars['contents'] = $jieqiTset['jieqi_page_contents'];
        }
    }
    return $vars;
}
function jieqi_query_rowvars($row, $format = 's', $module = '')
{
    global $jieqiModules;
    global $jieqiOption;
    if (empty($module) && defined('JIEQI_MODULE_NAME')) {
        $module = JIEQI_MODULE_NAME;
    }
    $options = array();
    if (!empty($module)) {
        if (!isset($jieqiOption[$module])) {
            jieqi_getconfigs($module, 'option', 'jieqiOption');
        }
        if (!empty($jieqiOption[$module])) {
            $options = $jieqiOption[$module];
        }
    }
    $ret = array();
    if (is_object($row)) {
        $ret = $row->getVars($format);
    } else {
        if (is_array($row)) {
            switch ($format) {
                case 's':
                    foreach ($row as $k => $v) {
                        $ret[$k] = jieqi_htmlstr($v);
                    }
                    break;
                case 'e':
                    foreach ($row as $k => $v) {
                        $ret[$k] = jieqi_htmlchars($v, ENT_QUOTES);
                    }
                    break;
                case 'n':
                default:
                    $ret = $row;
                    break;
            }
        }
    }
    if (!empty($options)) {
        foreach ($ret as $k => $v) {
            if (isset($options[$k])) {
                $v = htmlspecialchars_decode($v);
                $ret[$k . '_n'] = $v;
                if (isset($options[$k]['items'][$v])) {
                    $ret[$k] = $options[$k]['items'][$v];
                } else {
                    if (!is_numeric($v)) {
                        $tmpary = jieqi_unserialize($v);
                        if (is_array($tmpary)) {
                            $ret[$k] = array();
                            foreach ($tmpary as $t) {
                                if (isset($options[$k]['items'][$t])) {
                                    $ret[$k][$t] = $options[$k]['items'][$t];
                                }
                            }
                        } else {
                            $ret[$k] = '';
                        }
                    } else {
                        $ret[$k] = '';
                    }
                }
            }
        }
    }
    return $ret;
}
define('JIEQI_INCLUDE_HEADER', 1);
if (defined('JIEQI_SUPPORT_MOB') && JIEQI_SUPPORT_MOB == 1 && (defined('JIEQI_DEVICE_CHECK') && 0 < intval(JIEQI_DEVICE_CHECK) || !defined('JIEQI_DEVICE_CHECK') && defined('JIEQI_MOBILE_LOCATION') && 0 < strlen(trim(JIEQI_MOBILE_LOCATION)) || !defined('JIEQI_DEVICE_CHECK') && defined('JIEQI_PC_LOCATION') && 0 < strlen(trim(JIEQI_PC_LOCATION)))) {
    jieqi_getconfigs('system', 'device');
    if (function_exists('jieqi_device_check')) {
        jieqi_device_check();
    }
}
if (!defined('JIEQI_ADMIN_PAGE')) {
    $etagcheck = isset($_SESSION['jieqiUserId']) ? intval($_SESSION['jieqiUserId']) : '';
    if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
        $etagary = explode('|', $_SERVER['HTTP_IF_NONE_MATCH']);
        if (count(1 < $etagary) && is_numeric($etagary[0]) && $etagcheck == $etagary[1]) {
            if (JIEQI_NOW_TIME - $etagary[0] < 3 || defined('JIEQI_LAST_MODIFYED') && JIEQI_LAST_MODIFYED < $etagary[0]) {
                header('HTTP/1.1 304 Not Modified');
                jieqi_freeresource();
                exit;
            }
        }
    }
    @header('ETag:' . jieqi_headstr(JIEQI_NOW_TIME . '|' . $etagcheck));
}
include_once JIEQI_ROOT_PATH . '/lib/template/template.php';
$jieqiTpl = JieqiTpl::getInstance();
if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
} else {
    if (isset($_SERVER['HTTP_REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
    }
}
if (isset($_SERVER['REQUEST_URI'])) {
    $jieqi_thisurl = $_SERVER['REQUEST_URI'];
} else {
    $jieqi_thisurl = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    if (isset($_SERVER['QUERY_STRING']) && 0 < strlen($_SERVER['QUERY_STRING'])) {
        $jieqi_thisurl .= '?' . $_SERVER['QUERY_STRING'];
    } else {
        if (isset($_SERVER['argv'][0]) && 0 < strlen($_SERVER['argv'][0])) {
            $jieqi_thisurl .= '?' . $_SERVER['argv'][0];
        }
    }
}
$jieqiTpl->assign('jieqi_thisurl', $jieqi_thisurl);
$jieqi_shareurl = strpos($jieqi_thisurl, '?') === false ? $jieqi_thisurl . '?fromuid=' . @intval($_SESSION['jieqiUserId']) : $jieqi_thisurl . '&fromuid=' . @intval($_SESSION['jieqiUserId']);
$jieqiTpl->assign('jieqi_shareurl', $jieqi_shareurl);
if (!empty($_SERVER['PHP_SELF'])) {
    $jieqi_thisfile = $_SERVER['PHP_SELF'];
} else {
    if (!empty($_SERVER['SCRIPT_NAME']) && substr($_SERVER['SCRIPT_NAME'], -4) == '.php') {
        $jieqi_thisfile = $_SERVER['SCRIPT_NAME'];
    } else {
        $jieqi_thisfile = '';
    }
}
$jieqiTpl->assign('jieqi_thisfile', $jieqi_thisfile);
global $jieqiUsersStatus;
global $jieqiUsersGroup;
if ($jieqiUsersStatus == JIEQI_GROUP_GUEST) {
    $jieqiTpl->assign('jieqi_newmessage', 0);
    $jieqiTpl->assign('jieqi_userid', 0);
    $jieqiTpl->assign('jieqi_username', '');
    $jieqiTpl->assign('jieqi_useruname', '');
    $jieqiTpl->assign('jieqi_group', JIEQI_GROUP_GUEST);
    $jieqiTpl->assign('jieqi_groupname', $jieqiGroups[JIEQI_GROUP_GUEST]);
    $jieqiTpl->assign('jieqi_score', 0);
    $jieqiTpl->assign('jieqi_experience', 0);
    $jieqiTpl->assign('jieqi_honor', '');
    $jieqiTpl->assign('jieqi_vip', 0);
    $jieqiTpl->assign('jieqi_egold', 0);
    $jieqiTpl->assign('jieqi_overtime', 0);
    $jieqiTpl->assign('jieqi_avatar', 0);
    $jieqiTpl->assign('jieqi_setting', array());
    $jieqiTpl->assign('jieqi_token', '');
    $jieqiTpl->assign('jieqi_token_name', JIEQI_TOKEN_NAME);
    $jieqiTpl->assign('jieqi_token_input', '');
    $jieqiTpl->assign('jieqi_token_url', '');
} else {
    $jieqiTpl->assign('jieqi_userid', $_SESSION['jieqiUserId']);
    $jieqiTpl->assign('jieqi_username', jieqi_htmlstr($_SESSION['jieqiUserName']));
    $jieqiTpl->assign('jieqi_useruname', jieqi_htmlstr($_SESSION['jieqiUserUname']));
    $jieqiTpl->assign('jieqi_group', $_SESSION['jieqiUserGroup']);
    $jieqiTpl->assign('jieqi_groupname', $jieqiGroups[$_SESSION['jieqiUserGroup']]);
    $jieqiTpl->assign('jieqi_score', $_SESSION['jieqiUserScore']);
    $jieqiTpl->assign('jieqi_experience', $_SESSION['jieqiUserExperience']);
    $jieqiTpl->assign('jieqi_honor', $_SESSION['jieqiUserHonor']);
    $jieqiTpl->assign('jieqi_vip', $_SESSION['jieqiUserVip']);
    $jieqiTpl->assign('jieqi_egold', $_SESSION['jieqiUserEgold']);
    $jieqiTpl->assign('jieqi_overtime', $_SESSION['jieqiUserOvertime']);
    $jieqiTpl->assign('jieqi_avatar', $_SESSION['jieqiUserAvatar']);
    $jieqiTpl->assign('jieqi_setting', $_SESSION['jieqiUserSet']);
    if (isset($_SESSION['jieqiNewMessage']) && 0 < $_SESSION['jieqiNewMessage']) {
        $jieqiTpl->assign('jieqi_newmessage', $_SESSION['jieqiNewMessage']);
    } else {
        $jieqiTpl->assign('jieqi_newmessage', 0);
    }
    $jieqiTpl->assign('jieqi_token', $_SESSION['jieqiUserToken']);
    $jieqiTpl->assign('jieqi_token_name', JIEQI_TOKEN_NAME);
    $jieqiTpl->assign('jieqi_token_input', '<input type="hidden" name="' . JIEQI_TOKEN_NAME . '" value="' . jieqi_htmlchars($_SESSION['jieqiUserToken'], ENT_QUOTES) . '" />');
    $jieqiTpl->assign('jieqi_token_url', '&' . JIEQI_TOKEN_NAME . '=' . urlencode($_SESSION['jieqiUserToken']));
}
$jieqiTpl->assign('jieqi_userstatus', $jieqiUsersStatus);
$jieqi_register_checkcode = defined('JIEQI_REGISTER_CHECKCODE') && !defined('JIEQI_NO_CHECKCODE') ? JIEQI_REGISTER_CHECKCODE : 0;
$jieqi_login_checkcode = defined('JIEQI_LOGIN_CHECKCODE') && !defined('JIEQI_NO_CHECKCODE') ? JIEQI_LOGIN_CHECKCODE : 0;
$jieqiTpl->assign('jieqi_register_checkcode', $jieqi_register_checkcode);
$jieqiTpl->assign('jieqi_login_checkcode', $jieqi_login_checkcode);
$jieqi_api_oauth = defined('JIEQI_API_OAUTH') ? JIEQI_API_OAUTH : 0;
$jieqiTpl->assign('jieqi_api_oauth', $jieqi_api_oauth);
$jieqi_api_sites = array('weixin' => array('apiorder' => 2, 'apititle' => LANG_APITITLE_WEIXIN, 'publish' => 0), 'qq' => array('apiorder' => 1, 'apititle' => LANG_APITITLE_QQ, 'publish' => 0), 'weibo' => array('apiorder' => 4, 'apititle' => LANG_APITITLE_WEIBO, 'publish' => 0));
foreach ($jieqi_api_sites as $ak => $av) {
    $jieqi_api_sites[$ak]['apiname'] = $ak;
    if (0 < $jieqi_api_oauth && 0 < ($jieqi_api_oauth & pow(2, $av['apiorder'] - 1))) {
        $jieqi_api_sites[$ak]['publish'] = 1;
    }
}
$jieqiTpl->assign('jieqi_api_sites', $jieqi_api_sites);
$langurl = jieqi_addurlvars(array('charset' => ''));
$jieqiTpl->assign('url_big5', $langurl . 'big5');
$jieqiTpl->assign('url_gb2312', $langurl . 'gbk');
$jieqiTpl->assign('url_gbk', $langurl . 'gbk');
$jieqiTpl->assign('url_utf8', $langurl . 'utf8');
unset($langurl);
if (empty($jieqi_pagetitle)) {
    $jieqi_pagetitle = JIEQI_SITE_NAME;
}
$jieqiTpl->assign_by_ref('jieqi_pagetitle', $jieqi_pagetitle);
$jieqiTpl->assign('jieqi_banner', JIEQI_BANNER);
if (!empty($jieqi_pagehead)) {
    $jieqiTpl->assign('jieqi_head', $jieqi_pagehead);
} else {
    $jieqiTpl->assign('jieqi_head', '');
}
$jieqiTpl->assign('jieqi_top_bar', JIEQI_TOP_BAR);
$jieqiTpl->assign('jieqi_bottom_bar', JIEQI_BOTTOM_BAR);
if (function_exists('jieqi_hooks_header')) {
    jieqi_hooks_header();
}
if (!empty($jieqiTset['jieqi_contents_template'])) {
    define('JIEQI_INCLUDE_COMPILED_INC', 1);
    if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $jieqiTset['jieqi_contents_template']))) {
        $jieqiTset['jieqi_contents_template'] = str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $jieqiTset['jieqi_contents_template']);
    }
    if (!isset($jieqiTset['jieqi_contents_cacheid'])) {
        $jieqiTset['jieqi_contents_cacheid'] = NULL;
    }
    if (!isset($jieqiTset['jieqi_contents_compileid'])) {
        $jieqiTset['jieqi_contents_compileid'] = NULL;
    }
    $jieqiTpl->include_compiled_inc($jieqiTset['jieqi_contents_template'], $jieqiTset['jieqi_contents_compileid'], true);
}
$jieqi_channel_vname = defined('JIEQI_CHANNEL_VNAME') && 0 < strlen(JIEQI_CHANNEL_VNAME) ? JIEQI_CHANNEL_VNAME : 'fromchannel';
if (!isset($_GET[$jieqi_channel_vname]) && !empty($_GET['fromuid'])) {
    $_GET[$jieqi_channel_vname] = $_GET['fromuid'];
}
if (isset($_GET[$jieqi_channel_vname])) {
    @setcookie('jieqiChannel', substr($_GET[$jieqi_channel_vname], 0, 100), 0, '/', JIEQI_COOKIE_DOMAIN, 0);
}