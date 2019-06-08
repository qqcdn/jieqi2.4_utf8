<?php

function jieqi_splitsqlfile(&$ret, $sql, $release = 32270)
{
    $sql = rtrim($sql, '' . "\n" . '' . "\r" . '');
    $sql_len = strlen($sql);
    $char = '';
    $string_start = '';
    $in_string = false;
    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];
        if ($in_string) {
            for (;;) {
                $i = strpos($sql, $string_start, $i);
                if (!$i) {
                    $ret[] = $sql;
                    return true;
                } else {
                    if ($string_start == '`' || $sql[$i - 1] != '\\') {
                        $string_start = '';
                        $in_string = false;
                        break;
                    } else {
                        $j = 2;
                        $escaped_backslash = false;
                        while (0 < $i - $j && $sql[$i - $j] == '\\') {
                            $escaped_backslash = !$escaped_backslash;
                            $j++;
                        }
                        if ($escaped_backslash) {
                            $string_start = '';
                            $in_string = false;
                            break;
                        } else {
                            $i++;
                        }
                    }
                }
            }
        } else {
            if ($char == ';') {
                $ret[] = substr($sql, 0, $i);
                $sql = ltrim(substr($sql, min($i + 1, $sql_len)));
                $sql_len = strlen($sql);
                if ($sql_len) {
                    $i = -1;
                } else {
                    return true;
                }
            } else {
                if ($char == '"' || $char == '\'' || $char == '`') {
                    $in_string = true;
                    $string_start = $char;
                } else {
                    if ($char == '#' || $char == '-' && 0 < $i && $sql[$i - 1] == '-') {
                        $start_of_comment = $sql[$i] == '#' ? $i : $i - 1;
                        $end_of_comment = strpos(' ' . $sql, "\n", $i + 1) ? strpos(' ' . $sql, "\n", $i + 1) : strpos(' ' . $sql, "\r", $i + 1);
                        if (!$end_of_comment) {
                            if (0 < $start_of_comment) {
                                $ret[] = trim(substr($sql, 0, $start_of_comment));
                            }
                            return true;
                        } else {
                            $sql = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
                            $sql_len = strlen($sql);
                            $i--;
                        }
                    } else {
                        if ($release < 32270 && $char == '!' && 1 < $i && $sql[$i - 2] . $sql[$i - 1] == '/*') {
                            $sql[$i] = ' ';
                        }
                    }
                }
            }
        }
    }
    if (!empty($sql) && preg_match('/[^[:space:]]+/', $sql)) {
        $ret[] = $sql;
    }
    return true;
}
define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
jieqi_checklogin();
jieqi_loadlang('database', JIEQI_MODULE_NAME);
if ($jieqiUsersStatus != JIEQI_GROUP_ADMIN) {
    jieqi_printfail(LANG_NEED_ADMIN);
}
if (empty($_SESSION['jieqiDbLogin'])) {
    header('Location: ' . jieqi_headstr(JIEQI_LOCAL_URL . '/admin/dblogin.php?jumpurl=' . urlencode(jieqi_addurlvars(array()))));
    exit;
}
@set_time_limit(3600);
@session_write_close();
include_once JIEQI_ROOT_PATH . '/admin/header.php';
if (isset($_POST['act']) && $_POST['act'] == 'execute') {
    jieqi_checkpost();
    if (empty($_POST['sqldata'])) {
        jieqi_printfail($jieqiLang['system']['need_sql_data']);
    } else {
        if (preg_match('/(into\\s+outfile|load_file\\s*\\([^\\)]*\\)|load\\s+data)/is', $_POST['sqldata'])) {
            jieqi_printfail($jieqiLang['system']['deny_sql_data']);
        }
    }
    jieqi_includedb();
    $db_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    jieqi_splitsqlfile($sqlary, str_replace(' jieqi_', ' ' . JIEQI_DB_PREFIX . '_', $_POST['sqldata']));
    $sqlerr = array();
    foreach ($sqlary as $v) {
        $v = trim($v);
        if (!empty($v) && 5 < strlen($v)) {
            $retflag = $db_query->execute($v);
            if (!$retflag) {
                $sqlerr[] = array('sql' => $v, 'error' => $db_query->db->error());
                if (!empty($_POST['errorstop'])) {
                    jieqi_printfail(sprintf($jieqiLang['system']['print_sql_error'], jieqi_htmlstr($v), jieqi_htmlstr($db_query->db->error())));
                    break;
                }
            }
        }
    }
    if (!empty($sqlerr) && !empty($_POST['showerror'])) {
        $errorinfo = '';
        foreach ($sqlerr as $v) {
            $errorinfo .= sprintf($jieqiLang['system']['show_error_format'], jieqi_htmlstr($v['sql']), jieqi_htmlstr($v['error']));
        }
        jieqi_msgwin(LANG_DO_SUCCESS, sprintf($jieqiLang['system']['sql_some_error'], $errorinfo));
    } else {
        jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['system']['execute_sql_success']);
    }
} else {
    $jieqiTpl->setCaching(0);
    $jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/dbquery.html';
}
include_once JIEQI_ROOT_PATH . '/admin/footer.php';