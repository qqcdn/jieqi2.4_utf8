<?php

function jieqi_system_usersvars($users, $format = 's')
{
    global $jieqiModules;
    global $jieqiConfigs;
    global $jieqiLang;
    global $jieqiOption;
    global $jieqiHonors;
    include_once JIEQI_ROOT_PATH . '/class/users.php';
    if (!isset($jieqiHonors)) {
        jieqi_getconfigs('system', 'honors');
    }
    $ret = jieqi_query_rowvars($users, $format, 'system');
    if (strlen($ret['name']) == 0) {
        $ret['name'] = $ret['uname'];
    }
    $ret['setting'] = is_object($users) ? jieqi_funtoarray('jieqi_htmlstr', jieqi_unserialize($users->getVar('setting', 'n'))) : jieqi_funtoarray('jieqi_htmlstr', jieqi_unserialize($users['setting']));
    $ret['verify_n'] = $ret['verify'];
    $ret['verify'] = JieqiUsersHandler::extractUserset($ret['verify'], 'verify');
    $ret['showset_n'] = $ret['showset'];
    $ret['showset'] = JieqiUsersHandler::extractUserset($ret['showset'], 'showset');
    $ret['acceptset_n'] = $ret['acceptset'];
    $ret['acceptset'] = JieqiUsersHandler::extractUserset($ret['acceptset'], 'acceptset');
    $ret['group'] = $users->getGroup();
    $ret['viptype'] = $users->getViptype();
    $honorid = jieqi_gethonorid($users->getVar('score'), $jieqiHonors);
    $ret['honorid'] = $honorid;
    $ret['honor'] = $jieqiHonors[$honorid]['name'][intval($users->getVar('workid', 'n'))];
    if (0 < $ret['overtime'] && JIEQI_NOW_TIME < $ret['overtime']) {
        $ret['monthly'] = 1;
    } else {
        $ret['monthly'] = 0;
    }
    $ret['remainemoney'] = $ret['sumemoney'] - $ret['paidemoney'];
    $ret['remainmoney'] = $ret['summoney'] - $ret['paidmoney'];
    $ret['url_avatar'] = jieqi_geturl('system', 'avatar', $users->getVar('uid', 'n'), 's', $users->getVar('avatar', 'n'));
    if (!empty($jieqiModules['badge']['publish']) && is_file($jieqiModules['badge']['path'] . '/include/badgefunction.php')) {
        include_once $jieqiModules['badge']['path'] . '/include/badgefunction.php';
        $ret['url_group'] = getbadgeurl(1, $users->getVar('groupid'), 0, true);
        $ret['url_honor'] = getbadgeurl(2, $honorid, 0, true);
        $jieqi_badgerows = array();
        $badgeary = jieqi_unserialize($users->getVar('badges', 'n'));
        if (is_array($badgeary) && 0 < count($badgeary)) {
            $m = 0;
            foreach ($badgeary as $badge) {
                $jieqi_badgerows[$m]['imageurl'] = getbadgeurl($badge['btypeid'], $badge['linkid'], $badge['imagetype']);
                $jieqi_badgerows[$m]['caption'] = jieqi_htmlstr($badge['caption']);
                $m++;
            }
        }
        $ret['badgerows'] = $jieqi_badgerows;
        $ret['use_badge'] = 1;
    } else {
        $ret['use_badge'] = 0;
    }
    return $ret;
}
function jieqi_system_avatarset()
{
    global $jieqiConfigs;
    if (!isset($jieqiConfigs['system'])) {
        jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
    }
    if (!isset($jieqiConfigs['system']['avatardt'])) {
        $jieqiConfigs['system']['avatardt'] = '.jpg';
    }
    if (!isset($jieqiConfigs['system']['avatardw'])) {
        $jieqiConfigs['system']['avatardw'] = '120';
    }
    if (!isset($jieqiConfigs['system']['avatardh'])) {
        $jieqiConfigs['system']['avatardh'] = $jieqiConfigs['system']['avatardw'];
    }
    if (!isset($jieqiConfigs['system']['avatarsw'])) {
        $jieqiConfigs['system']['avatarsw'] = '48';
    }
    if (!isset($jieqiConfigs['system']['avatarsh'])) {
        $jieqiConfigs['system']['avatarsh'] = $jieqiConfigs['system']['avatarsw'];
    }
    if (!isset($jieqiConfigs['system']['avatariw'])) {
        $jieqiConfigs['system']['avatariw'] = '16';
    }
    if (!isset($jieqiConfigs['system']['avatarih'])) {
        $jieqiConfigs['system']['avatarih'] = $jieqiConfigs['system']['avatariw'];
    }
    return $jieqiConfigs;
}
function jieqi_system_avatarresize($uid, $imagefile, $cutary = '')
{
    global $jieqiConfigs;
    if (function_exists('gd_info')) {
        if (!isset($jieqiConfigs['system']['avatardw'])) {
            jieqi_system_avatarset();
        }
        $avatardir = jieqi_uploadpath($jieqiConfigs['system']['avatardir'], 'system');
        $avatardir .= jieqi_getsubdir($uid);
        include_once JIEQI_ROOT_PATH . '/lib/image/imageresize.php';
        $imgresize = new ImageResize();
        $imgresize->load($imagefile);
        if (!empty($cutary)) {
            if (!is_array($cutary)) {
                $cutary = explode(',', $cutary);
            }
            foreach ($cutary as $k => $v) {
                $cutary[$k] = intval($v);
            }
            if (0 < $cutary[2] && 0 < $cutary[3]) {
                $imgresize->resize($cutary[2], $cutary[3]);
            }
            $imgresize->cut($jieqiConfigs['system']['avatardw'], $jieqiConfigs['system']['avatardh'], intval($cutary[0]), intval($cutary[1]));
        }
        $imgresize->resize($jieqiConfigs['system']['avatardw'], $jieqiConfigs['system']['avatardh']);
        $imagefile_l = $avatardir . '/' . $uid . $jieqiConfigs['system']['avatardt'];
        $imgresize->save($imagefile_l, false, substr(strrchr(trim(strtolower($imagefile_l)), '.'), 1));
        @chmod($imagefile, 511);
        $imgresize->resize($jieqiConfigs['system']['avatarsw'], $jieqiConfigs['system']['avatarsh']);
        $imagefile_s = $avatardir . '/' . $uid . 's' . $jieqiConfigs['system']['avatardt'];
        $imgresize->save($imagefile_s, false, substr(strrchr(trim(strtolower($imagefile_s)), '.'), 1));
        @chmod($imagefile, 511);
        $imgresize->resize($jieqiConfigs['system']['avatariw'], $jieqiConfigs['system']['avatarih']);
        $imagefile_i = $avatardir . '/' . $uid . 'i' . $jieqiConfigs['system']['avatardt'];
        $imgresize->save($imagefile_i, true, substr(strrchr(trim(strtolower($imagefile_i)), '.'), 1));
        @chmod($imagefile, 511);
        if ($imagefile != $imagefile_l) {
            jieqi_delfile($imagefile);
        }
    }
    return true;
}