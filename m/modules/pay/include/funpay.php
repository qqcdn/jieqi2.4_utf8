<?php

function jieqi_pay_start()
{
    global $jieqiModules;
    global $jieqiLang;
    global $jieqiPayset;
    global $jieqiPayAction;
    global $jieqiUsersGroup;
    global $jieqiTset;
    if (!isset($jieqiLang['pay']['pay'])) {
        jieqi_loadlang('pay', 'pay');
    }
    if (!isset($jieqiPayset)) {
        jieqi_getconfigs('pay', JIEQI_PAY_TYPE, 'jieqiPayset');
    }
    $acttype = 0;
    $actid = 0;
    $actname = 0;
    $actlog = '';
    if (isset($_REQUEST['payaction']) && is_numeric($_REQUEST['payaction'])) {
        if (!isset($jieqiPayAction[$_REQUEST['payaction']])) {
            jieqi_printfail($jieqiLang['pay']['payaction_type_error']);
        }
        if (!empty($jieqiPayAction[$_REQUEST['payaction']]['denygroup']) && in_array($jieqiUsersGroup, $jieqiPayAction[$_REQUEST['payaction']]['denygroup'])) {
            jieqi_printfail($jieqiLang['pay']['payaction_deny_group']);
        }
        if (!empty($jieqiPayAction[$_REQUEST['payaction']]['expiretime']) && strtotime($jieqiPayAction[$_REQUEST['payaction']]['expiretime']) < JIEQI_NOW_TIME) {
            jieqi_printfail(sprintf($jieqiLang['pay']['payaction_expire_time'], $jieqiPayAction[$_REQUEST['payaction']]['expiretime']));
        }
        $_REQUEST['egold'] = 0;
        $_REQUEST['money'] = ceil($jieqiPayAction[$_REQUEST['payaction']]['amount'] * 100);
        $acttype = 1;
        $actid = $_REQUEST['payaction'];
        $actname = $jieqiPayAction[$_REQUEST['payaction']]['caption'];
    } else {
        if (isset($_REQUEST['money']) && is_numeric($_REQUEST['money']) && 0 < $_REQUEST['money']) {
            $_REQUEST['egold'] = 0;
            $paylimit = isset($jieqiPayset[JIEQI_PAY_TYPE]['paylimit']) ? array_flip($jieqiPayset[JIEQI_PAY_TYPE]['paylimit']) : array();
            if (isset($paylimit[$_REQUEST['money']])) {
                $_REQUEST['egold'] = intval($paylimit[$_REQUEST['money']]);
            } else {
                if (is_numeric($jieqiPayset[JIEQI_PAY_TYPE]['paycustom']) && 0 < $jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] || is_array($jieqiPayset[JIEQI_PAY_TYPE]['paycustom']) && 0 < $jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['open']) {
                    if (is_numeric($jieqiPayset[JIEQI_PAY_TYPE]['paycustom'])) {
                        $jieqiPayset[JIEQI_PAY_TYPE]['paycustom'] = array('open' => 1);
                    }
                    if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['min'])) {
                        $jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['min'] = 0;
                    }
                    if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['dec']) || !in_array($jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['dec'], array(0, 1, 2))) {
                        $jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['dec'] = 0;
                    }
                    $_REQUEST['money'] = round($_REQUEST['money'], $jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['dec']);
                    if ($_REQUEST['money'] <= 0) {
                        jieqi_printfail($jieqiLang['pay']['money_over_zero']);
                    } else {
                        if ($_REQUEST['money'] < $jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['min']) {
                            jieqi_printfail(sprintf($jieqiLang['pay']['money_over_min'], $jieqiPayset[JIEQI_PAY_TYPE]['paycustom']['min']));
                        }
                    }
                    $payrate = 100;
                    if (is_numeric($jieqiPayset[JIEQI_PAY_TYPE]['payrate']) && 0 < intval($jieqiPayset[JIEQI_PAY_TYPE]['payrate'])) {
                        $payrate = intval($jieqiPayset[JIEQI_PAY_TYPE]['payrate']);
                    } else {
                        if (is_array($jieqiPayset[JIEQI_PAY_TYPE]['payrate'])) {
                            ksort($jieqiPayset[JIEQI_PAY_TYPE]['payrate']);
                            foreach ($jieqiPayset[JIEQI_PAY_TYPE]['payrate'] as $k => $v) {
                                $v = intval($v);
                                if ($k <= $_REQUEST['money'] && 0 < $v) {
                                    $payrate = $v;
                                }
                            }
                        }
                    }
                    $_REQUEST['egold'] = floor($_REQUEST['money'] * $payrate);
                } else {
                    jieqi_printfail($jieqiLang['pay']['buy_type_error']);
                }
            }
            $_REQUEST['money'] = ceil($_REQUEST['money'] * 100);
            $actname = $_REQUEST['egold'] . JIEQI_EGOLD_NAME;
        } else {
            if (isset($_REQUEST['egold']) && is_numeric($_REQUEST['egold']) && 0 < $_REQUEST['egold']) {
                $_REQUEST['egold'] = intval($_REQUEST['egold']);
                $_REQUEST['money'] = 0;
                if (isset($jieqiPayset[JIEQI_PAY_TYPE]['paylimit'][$_REQUEST['egold']])) {
                    $_REQUEST['money'] = intval($jieqiPayset[JIEQI_PAY_TYPE]['paylimit'][$_REQUEST['egold']] * 100);
                } else {
                    jieqi_printfail($jieqiLang['pay']['buy_type_error']);
                }
                $actname = $_REQUEST['egold'] . JIEQI_EGOLD_NAME;
            } else {
                if (isset($_REQUEST['payaction']) || isset($_REQUEST['money']) || isset($_REQUEST['egold'])) {
                    jieqi_printfail($jieqiLang['pay']['buy_type_error']);
                } else {
                    if (!empty($_REQUEST['type']) && preg_match('/^\\w+$/', $_REQUEST['type']) && strlen($_REQUEST['type']) < 64) {
                        $paytype = $_REQUEST['type'];
                    } else {
                        $paytype = JIEQI_PAY_TYPE;
                    }
                    jieqi_getconfigs('pay', $paytype, 'jieqiPayset');
                    if (empty($jieqiPayset[$paytype])) {
                        jieqi_printfail(LANG_ERROR_PARAMETER);
                    }
                    $jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'] . '/templates/' . $paytype . 'pay.html';
                    if (!is_file($jieqiTset['jieqi_contents_template'])) {
                        jieqi_printfail(LANG_ERROR_PARAMETER);
                    }
                    include_once JIEQI_ROOT_PATH . '/header.php';
                    $jieqiTpl->assign('paytype', jieqi_htmlstr($paytype));
                    $paytype_name = isset($jieqiPayset[$paytype]['paytype']) ? $jieqiPayset[$paytype]['paytype'] : '';
                    $jieqiTpl->assign('paytype_name', jieqi_htmlstr($paytype_name));
                    $subtype_name = isset($_REQUEST['subtype']) && isset($jieqiPayset[$paytype]['subtype'][$_REQUEST['subtype']]) ? $jieqiPayset[$paytype]['subtype'][$_REQUEST['subtype']] : (isset($jieqiLang['pay']['pay_subtype_title']) ? $jieqiLang['pay']['pay_subtype_title'] : '');
                    $jieqiTpl->assign('subtype_name', jieqi_htmlstr($subtype_name));
                    $fromtype_name = isset($_REQUEST['fromtype']) && isset($jieqiPayset[$paytype]['fromtype'][$_REQUEST['fromtype']]) ? $jieqiPayset[$paytype]['fromtype'][$_REQUEST['fromtype']] : '';
                    $jieqiTpl->assign('fromtype_name', jieqi_htmlstr($fromtype_name));
                    if (!empty($_REQUEST['a']) && preg_match('/^\\w+$/', $_REQUEST['a']) && strlen($_REQUEST['a']) < 64) {
                        $payaction = $_REQUEST['a'];
                    } else {
                        $payaction = 'payaction';
                    }
                    if (!empty($payaction)) {
                        jieqi_getconfigs('pay', $payaction, 'jieqiPayaction');
                    }
                    if (!empty($jieqiPayset[$paytype])) {
                        if (empty($jieqiPayset[$paytype]['paydefault']) && is_array($jieqiPayset[$paytype]['paylimit'])) {
                            reset($jieqiPayset[$paytype]['paylimit']);
                            $jieqiPayset[$paytype]['paydefault'] = key($jieqiPayset[$paytype]['paylimit']);
                        }
                        $jieqiTpl->assign('paytype', $paytype);
                        $jieqiTpl->assign('payset', jieqi_funtoarray('jieqi_htmlstr', $jieqiPayset[$paytype]));
                    } else {
                        $jieqiTpl->assign('paytype', '');
                        $jieqiTpl->assign('payset', '');
                    }
                    if (!empty($jieqiPayAction)) {
                        foreach ($jieqiPayAction as $k => $v) {
                            if (!empty($v['expiretime']) && strtotime($v['expiretime']) < JIEQI_NOW_TIME) {
                                $jieqiPayAction[$k]['isexpire'] = 1;
                            } else {
                                $jieqiPayAction[$k]['isexpire'] = 0;
                            }
                            if (empty($v['expiretime'])) {
                                $v['expiretime'] = '';
                            }
                        }
                        $jieqiTpl->assign('payaction', jieqi_funtoarray('jieqi_htmlstr', $jieqiPayAction));
                    } else {
                        $jieqiTpl->assign('payaction', array());
                    }
                    if (!empty($_REQUEST['jumpurl']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
                        $jumpurl = $_REQUEST['jumpurl'];
                    } else {
                        if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_SERVER['HTTP_REFERER']) && !preg_match('/(\\/pay\\/\\w+\\.php)/i', $_SERVER['HTTP_REFERER'])) {
                            $jumpurl = $_SERVER['HTTP_REFERER'];
                        } else {
                            $jumpurl = JIEQI_USER_URL . '/userdetail.php';
                        }
                    }
                    $jieqiTpl->assign('jumpurl', jieqi_htmlstr($jumpurl));
                    $jieqiTpl->assign('jumpurl_n', $jumpurl);
                    $jieqiTpl->assign('egoldname', JIEQI_EGOLD_NAME);
                    $jieqiTpl->assign('_request', jieqi_funtoarray('jieqi_htmlstr', $_REQUEST));
                    $jieqiTpl->setCaching(0);
                    include_once JIEQI_ROOT_PATH . '/footer.php';
                    exit;
                }
            }
        }
    }
    $_REQUEST['amount'] = $_REQUEST['money'] / 100;
    include_once $jieqiModules['pay']['path'] . '/class/paylog.php';
    $paylog_handler = JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
    $paylog = $paylog_handler->create();
    $paylog->setVar('siteid', JIEQI_SITE_ID);
    $paylog->setVar('buytime', JIEQI_NOW_TIME);
    $paylog->setVar('buydate', date('Ymd', JIEQI_NOW_TIME));
    $paylog->setVar('buymonth', date('Ym', JIEQI_NOW_TIME));
    $paylog->setVar('rettime', 0);
    $paylog->setVar('buyid', $_SESSION['jieqiUserId']);
    $paylog->setVar('buyname', $_SESSION['jieqiUserName']);
    $buyinfo = isset($_REQUEST['buyinfo']) ? $_REQUEST['buyinfo'] : '';
    $paylog->setVar('buyinfo', $buyinfo);
    $moneytype = empty($jieqiPayset[JIEQI_PAY_TYPE]['moneytype']) ? 0 : intval($jieqiPayset[JIEQI_PAY_TYPE]['moneytype']);
    $paylog->setVar('moneytype', $moneytype);
    $paylog->setVar('money', $_REQUEST['money']);
    $paylog->setVar('egoldtype', intval($jieqiPayset[JIEQI_PAY_TYPE]['paysilver']));
    $paylog->setVar('egold', $_REQUEST['egold']);
    $paylog->setVar('paytype', JIEQI_PAY_TYPE);
    if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['fromtype'])) {
        $jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array();
    }
    if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'])) {
        $jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = '';
    }
    $subtype = isset($_REQUEST['subtype']) && isset($jieqiPayset[JIEQI_PAY_TYPE]['subtype'][$_REQUEST['subtype']]) ? $_REQUEST['subtype'] : $jieqiPayset[JIEQI_PAY_TYPE]['subtypeid'];
    $paylog->setVar('subtype', $subtype);
    if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['fromtype'])) {
        $jieqiPayset[JIEQI_PAY_TYPE]['fromtype'] = array();
    }
    if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'])) {
        $jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'] = '';
    }
    $fromtype = isset($_REQUEST['fromtype']) && isset($jieqiPayset[JIEQI_PAY_TYPE]['fromtype'][$_REQUEST['fromtype']]) ? $_REQUEST['fromtype'] : $jieqiPayset[JIEQI_PAY_TYPE]['fromtypeid'];
    $paylog->setVar('fromtype', $fromtype);
    $typename = isset($jieqiPayset[JIEQI_PAY_TYPE]['paytype']) ? $jieqiPayset[JIEQI_PAY_TYPE]['paytype'] : '';
    $typename .= isset($jieqiPayset[JIEQI_PAY_TYPE]['subtype'][$subtype]) ? ' ' . $jieqiPayset[JIEQI_PAY_TYPE]['subtype'][$subtype] : '';
    $typename .= isset($jieqiPayset[JIEQI_PAY_TYPE]['fromtype'][$fromtype]) ? ' ' . $jieqiPayset[JIEQI_PAY_TYPE]['fromtype'][$fromtype] : '';
    $paylog->setVar('typename', $typename);
    $paylog->setVar('acttype', $acttype);
    $paylog->setVar('actid', $actid);
    $paylog->setVar('actname', $actname);
    $paylog->setVar('actlog', $actlog);
    $paylog->setVar('channel', $_SESSION['jieqiUserChannel']);
    if (defined('JIEQI_DEVICE_FOR')) {
        $paylog->setVar('device', JIEQI_DEVICE_FOR);
    }
    $paylog->setVar('retserialno', '');
    $paylog->setVar('retaccount', '');
    $paylog->setVar('retinfo', '');
    $paylog->setVar('masterid', 0);
    $paylog->setVar('mastername', '');
    $paylog->setVar('masterinfo', '');
    $paylog->setVar('note', '');
    $paylog->setVar('payflag', 0);
    if (!$paylog_handler->insert($paylog)) {
        jieqi_printfail($jieqiLang['pay']['add_paylog_error']);
    }
    $jieqiPayset[JIEQI_PAY_TYPE]['subject'] = $actname;
    if (!empty($_REQUEST['jumpurl']) || !preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_REQUEST['jumpurl'])) {
        $_REQUEST['jumpurl'] = JIEQI_USER_URL . '/userdetail.php';
    }
    $_SESSION['jieqiJumpUrl'] = $_REQUEST['jumpurl'];
    return $paylog;
}
function jieqi_pay_return($params)
{
    global $jieqiModules;
    global $jieqiLang;
    global $jieqiPayset;
    global $jieqiPayAction;
    global $paylog_handler;
    global $users_handler;
    if (!isset($jieqiLang['pay']['pay'])) {
        jieqi_loadlang('pay', 'pay');
    }
    if (!isset($jieqiPayset)) {
        jieqi_getconfigs('pay', JIEQI_PAY_TYPE, 'jieqiPayset');
    }
    if (!isset($paylog_handler) || !is_a($paylog_handler, 'JieqiPaylogHandler')) {
        include_once $jieqiModules['pay']['path'] . '/class/paylog.php';
        $paylog_handler = JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
    }
    $params['orderid'] = intval($params['orderid']);
    $paylog = $paylog_handler->get($params['orderid']);
    $orderinfo = array('orderid' => $params['orderid']);
    if (is_object($paylog)) {
        $orderinfo = array('orderid' => $params['orderid'], 'buyid' => $paylog->getVar('buyid'), 'buyname' => $paylog->getVar('buyname'), 'egold' => $paylog->getVar('egold'), 'money' => $paylog->getVar('money'), 'moneytype' => $paylog->getVar('moneytype'), 'acttype' => $paylog->getVar('acttype'), 'actid' => $paylog->getVar('actid'), 'payflag' => $paylog->getVar('payflag'));
        if ($orderinfo['payflag'] == 0) {
            if (!isset($users_handler) || !is_a($users_handler, 'JieqiUsersHandler')) {
                include_once JIEQI_ROOT_PATH . '/class/users.php';
                $users_handler = JieqiUsersHandler::getInstance('JieqiUsersHandler');
            }
            $extras = array();
            $extras['money'] = $orderinfo['money'];
            $extras['moneytype'] = $orderinfo['moneytype'];
            if (!isset($jieqiPayset[JIEQI_PAY_TYPE]['payscore'])) {
                $jieqiPayset[JIEQI_PAY_TYPE]['payscore'] = 1;
            }
            $extras['addscore'] = empty($jieqiPayset[JIEQI_PAY_TYPE]['payscore']) ? 0 : floor($orderinfo['money'] * $jieqiPayset[JIEQI_PAY_TYPE]['payscore'] / 100);
            $extras['updatevip'] = isset($jieqiPayset[JIEQI_PAY_TYPE]['payupvip']) ? intval($jieqiPayset[JIEQI_PAY_TYPE]['payupvip']) : 1;
            $acttype = intval($paylog->getVar('acttype', 'n'));
            $actid = intval($paylog->getVar('actid', 'n'));
            if ($acttype == 1 && isset($jieqiPayAction[$actid])) {
                if (isset($jieqiPayAction[$actid]['updategroup'])) {
                    $extras['updategroup'] = $jieqiPayAction[$actid]['updategroup'];
                }
                if (isset($jieqiPayAction[$actid]['addmonthly']) && is_numeric($jieqiPayAction[$actid]['addmonthly']) && 0 < $jieqiPayAction[$actid]['addmonthly']) {
                    $extras['addmonthly'] = intval($jieqiPayAction[$actid]['addmonthly']);
                }
                if (isset($jieqiPayAction[$actid]['earnvipvote']) && is_numeric($jieqiPayAction[$actid]['earnvipvote']) && 0 < $jieqiPayAction[$actid]['earnvipvote']) {
                    $extras['earnvipvote'] = intval($jieqiPayAction[$actid]['earnvipvote']);
                }
            }
            $ret = $users_handler->income($orderinfo['buyid'], $orderinfo['egold'], $extras);
            if ($ret) {
                $note = sprintf($jieqiLang['pay']['add_egold_success'], $orderinfo['buyname'], $paylog->getVar('actname', 'n'));
            } else {
                $note = sprintf($jieqiLang['pay']['add_egold_failure'], $orderinfo['buyname'], $paylog->getVar('actname', 'n'));
            }
            $paylog->setVar('rettime', JIEQI_NOW_TIME);
            if (isset($params['retserialno'])) {
                $paylog->setVar('retserialno', $params['retserialno']);
            }
            if (isset($params['retaccount'])) {
                $paylog->setVar('retaccount', $params['retaccount']);
            }
            if (isset($params['retinfo'])) {
                $paylog->setVar('retinfo', $params['retinfo']);
            }
            if (isset($params['subtype'])) {
                $paylog->setVar('subtype', $params['subtype']);
            }
            if (isset($params['fromtype'])) {
                $paylog->setVar('fromtype', $params['fromtype']);
            }
            if (isset($params['typename'])) {
                $paylog->setVar('typename', $params['typename']);
            } else {
                $typename = isset($jieqiPayset[JIEQI_PAY_TYPE]['paytype']) ? $jieqiPayset[JIEQI_PAY_TYPE]['paytype'] : '';
                $subtype = $paylog->getVar('subtype', 'n');
                $typename .= isset($jieqiPayset[JIEQI_PAY_TYPE]['subtype'][$subtype]) ? ' ' . $jieqiPayset[JIEQI_PAY_TYPE]['subtype'][$subtype] : '';
                $fromtype = $paylog->getVar('fromtype', 'n');
                $typename .= isset($jieqiPayset[JIEQI_PAY_TYPE]['fromtype'][$fromtype]) ? ' ' . $jieqiPayset[JIEQI_PAY_TYPE]['fromtype'][$fromtype] : '';
                $paylog->setVar('typename', $typename);
            }
            $paylog->setVar('note', $note);
            if (empty($params['manual'])) {
                $paylog->setVar('payflag', 1);
            } else {
                if (!empty($_SESSION['jieqiUserId'])) {
                    $paylog->setVar('masterid', $_SESSION['jieqiUserId']);
                    $paylog->setVar('mastername', $_SESSION['jieqiUserName']);
                }
                $paylog->setVar('payflag', 2);
            }
            if (!$paylog_handler->insert($paylog)) {
                if ($params['return']) {
                    return -2;
                } else {
                    $orderinfo['message'] = $jieqiLang['pay']['save_paylog_failure'];
                    jieqi_pay_failure($orderinfo);
                }
            } else {
                if ($params['return']) {
                    return 1;
                } else {
                    $orderinfo['message'] = sprintf($jieqiLang['pay']['buy_egold_success'], $orderinfo['egold'] . JIEQI_EGOLD_NAME);
                    jieqi_pay_success($orderinfo);
                }
            }
        } else {
            if ($params['return']) {
                return 2;
            } else {
                $orderinfo['message'] = sprintf($jieqiLang['pay']['buy_already_success'], $orderinfo['egold'] . JIEQI_EGOLD_NAME);
                jieqi_pay_success($orderinfo);
            }
        }
    } else {
        if ($params['return']) {
            return -1;
        } else {
            $orderinfo['message'] = $jieqiLang['pay']['no_buy_record'];
            jieqi_pay_failure($orderinfo);
        }
    }
}
function jieqi_pay_success($params)
{
    if (!empty($_SESSION['jieqiJumpUrl']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_SESSION['jieqiJumpUrl'])) {
        $jumpurl = $_SESSION['jieqiJumpUrl'];
        unset($_SESSION['jieqiJumpUrl']);
    } else {
        $jumpurl = JIEQI_USER_URL . '/userdetail.php';
    }
    jieqi_jumppage($jumpurl, LANG_DO_SUCCESS, $params['message'], false);
}
function jieqi_pay_failure($params)
{
    jieqi_printfail($params['message']);
}
function jieqi_pay_submitted()
{
    global $jieqiLang;
    if (!isset($jieqiLang['pay']['pay'])) {
        jieqi_loadlang('pay', 'pay');
    }
    if (!empty($_SESSION['jieqiJumpUrl']) && preg_match('/^(\\/\\w+|' . preg_quote(JIEQI_LOCAL_URL, '/') . ')/i', $_SESSION['jieqiJumpUrl'])) {
        $jumpurl = $_SESSION['jieqiJumpUrl'];
        unset($_SESSION['jieqiJumpUrl']);
        jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['pay']['buy_return_jumpurl'], $jumpurl));
    } else {
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['pay']['buy_return_success']);
    }
}
function jieqi_pay_makequery($params, $ue = true, $sort = true)
{
    if ($sort) {
        ksort($params);
        reset($params);
    }
    $query_string = '';
    foreach ($params as $k => $v) {
        if (0 < strlen($v)) {
            if (0 < strlen($query_string)) {
                $query_string .= '&';
            }
            $query_string .= $ue == true ? urlencode($k) . '=' . urlencode($v) : $k . '=' . $v;
        }
    }
    return $query_string;
}
function jieqi_pay_getsign($params, $options)
{
    if (is_string($options)) {
        $options = array('key' => $options);
    }
    if (!is_array($options) || !isset($options['key'])) {
        return false;
    }
    if (is_array($options['sort'])) {
        $fields = array();
        foreach ($options['sort'] as $k) {
            if (isset($params[$k])) {
                $fields[$k] = $params[$k];
            }
        }
    } else {
        ksort($params);
        $fields = $params;
        if (is_array($options['filter'])) {
            foreach ($options['filter'] as $k) {
                if (isset($fields[$k])) {
                    unset($fields[$k]);
                }
            }
        }
    }
    $query_string = '';
    foreach ($fields as $k => $v) {
        if (0 < strlen($query_string)) {
            $query_string .= '&';
        }
        if (!empty($options['urlencode'])) {
            $k = urlencode($k);
            $v = urlencode($v);
        }
        $query_string .= $k . '=' . $v;
    }
    if (!empty($options['kname'])) {
        if (0 < strlen($query_string)) {
            $query_string .= '&';
        }
        $query_string .= $options['kname'] . '=';
    }
    $query_string .= $options['key'];
    $sign = md5($query_string);
    if (!empty($options['case'])) {
        if ($options['case'] == 'upper') {
            $sign = strtoupper($sign);
        } else {
            if ($options['case'] == 'lower') {
                $sign = strtolower($sign);
            }
        }
    }
    return $sign;
}
function jieqi_pay_getpaylog($payid)
{
    global $paylog_handler;
    if (!is_a($paylog_handler, 'JieqiPaylogHandler')) {
        include_once $jieqiModules['pay']['path'] . '/class/paylog.php';
        $paylog_handler = JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
    }
    return $paylog_handler->get($payid);
}