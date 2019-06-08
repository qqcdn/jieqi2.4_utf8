<?php

function jieqi_fetchtablelist($tablepre = '')
{
    global $query_handler;
    $arr = explode('.', $tablepre);
    $dbname = !empty($arr[1]) ? $arr[0] : '';
    $sqladd = $dbname ? ' FROM ' . $dbname . ' LIKE \'' . $arr[1] . '%\'' : 'LIKE \'' . $tablepre . '%\'';
    !$tablepre && ($tablepre = '*');
    $tables = $table = array();
    $query = $query_handler->execute('SHOW TABLE STATUS ' . $sqladd);
    while ($table = $query_handler->db->fetchArray($query)) {
        $table['Name'] = ($dbname ? $dbname . '.' : '') . $table['Name'];
        $tables[] = $table;
    }
    return $tables;
}
function jieqi_syntablestruct($sql, $version, $dbcharset)
{
    if (strpos(trim(substr($sql, 0, 18)), 'CREATE TABLE') === false) {
        return $sql;
    }
    $sqlversion = strpos($sql, 'ENGINE=') === false ? false : true;
    if ($sqlversion === $version) {
        return $sqlversion && $dbcharset ? preg_replace(array('/ character set \\w+/i', '/ collate \\w+/i', '/DEFAULT CHARSET=\\w+/is'), array('', '', 'DEFAULT CHARSET=' . $dbcharset), $sql) : $sql;
    }
    if ($version) {
        return preg_replace(array('/TYPE=HEAP/i', '/TYPE=(\\w+)/is'), array('ENGINE=MEMORY DEFAULT CHARSET=' . $dbcharset, 'ENGINE=\\1 DEFAULT CHARSET=' . $dbcharset), $sql);
    } else {
        return preg_replace(array('/character set \\w+/i', '/collate \\w+/i', '/ENGINE=MEMORY/i', '/\\s*DEFAULT CHARSET=\\w+/is', '/\\s*COLLATE=\\w+/is', '/ENGINE=(\\w+)(.*)/is'), array('', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'), $sql);
    }
}
function jieqi_sqldumptable($table, $startfrom = 0, $currsize = 0)
{
    global $query_handler;
    global $sizelimit;
    global $startrow;
    global $extendins;
    global $sqlcompat;
    global $sqlcharset;
    global $dumpcharset;
    global $usehex;
    global $complete;
    $offset = 300;
    $tabledump = '';
    $tablefields = array();
    $query = $query_handler->execute('SHOW FULL COLUMNS FROM ' . $table);
    if (!$query) {
        $usehex = false;
    } else {
        while ($fieldrow = $query_handler->db->fetchArray($query)) {
            $tablefields[] = $fieldrow;
        }
    }
    if (!$startfrom) {
        $createtable = $query_handler->execute('SHOW CREATE TABLE ' . $table);
        if ($createtable) {
            $tabledump = 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n" . '';
        } else {
            return '';
        }
        $create = $query_handler->db->fetchArray($createtable);
        if (strpos($table, '.') !== false) {
            $tablename = substr($table, strpos($table, '.') + 1);
            $create['Create Table'] = str_replace('CREATE TABLE `' . $tablename . '`', 'CREATE TABLE `' . $table . '`', $create['Create Table']);
        }
        $tabledump .= $create['Create Table'];
        if (MYSQL_SERVER_INFO < '4.1' && $sqlcompat == 'MYSQL41') {
            $tabledump = preg_replace('/TYPE\\=(.+)/', 'ENGINE=\\1 DEFAULT CHARSET=' . $dumpcharset, $tabledump);
        }
        if ('4.1' < MYSQL_SERVER_INFO && $sqlcharset) {
            $tabledump = preg_replace('/(DEFAULT)*\\s*CHARSET=.+/', 'DEFAULT CHARSET=' . $sqlcharset, $tabledump);
        }
        $tablestatus = $query_handler->execute('SHOW TABLE STATUS LIKE \'' . $table . '\'');
        $tablestatus = $query_handler->db->fetchArray($tablestatus);
        $tabledump .= ';' . "\n" . '' . "\n" . '';
        if ($sqlcompat == 'MYSQL40' && '4.1' <= MYSQL_SERVER_INFO && MYSQL_SERVER_INFO < '5.1') {
            if ($tablestatus['Engine'] == 'MEMORY') {
                $tabledump = str_replace('TYPE=MEMORY', 'TYPE=HEAP', $tabledump);
            }
        }
    }
    $tabledumped = 0;
    $numrows = $offset;
    $firstfield = $tablefields[0];
    if ($extendins == '0') {
        while ($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset) {
            if ($firstfield['Extra'] == 'auto_increment') {
                $selectsql = 'SELECT * FROM ' . $table . ' WHERE ' . $firstfield['Field'] . '>' . $startfrom . ' LIMIT ' . $offset;
            } else {
                $selectsql = 'SELECT * FROM ' . $table . ' LIMIT ' . $startfrom . ', ' . $offset;
            }
            $tabledumped = 1;
            $rows = $query_handler->execute($selectsql);
            $numfields = mysql_num_fields($rows);
            $numrows = $query_handler->db->getRowsNum($rows);
            while ($row = $query_handler->db->fetchRow($rows)) {
                $comma = $t = '';
                for ($i = 0; $i < $numfields; $i++) {
                    $t .= $comma . ($usehex && !empty($row[$i]) && (jieqi_strexists($tablefields[$i]['Type'], 'char') || jieqi_strexists($tablefields[$i]['Type'], 'text')) ? '0x' . bin2hex($row[$i]) : '\'' . mysql_real_escape_string($row[$i]) . '\'');
                    $comma = ',';
                }
                if (strlen($t) + $currsize + strlen($tabledump) < $sizelimit * 1000) {
                    if ($firstfield['Extra'] == 'auto_increment') {
                        $startfrom = $row[0];
                    } else {
                        $startfrom++;
                    }
                    $tabledump .= 'INSERT INTO ' . $table . ' VALUES (' . $t . ');' . "\n" . '';
                } else {
                    $complete = false;
                    break 2;
                }
            }
        }
    } else {
        while ($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset) {
            if ($firstfield['Extra'] == 'auto_increment') {
                $selectsql = 'SELECT * FROM ' . $table . ' WHERE ' . $firstfield['Field'] . '>' . $startfrom . ' LIMIT ' . $offset;
            } else {
                $selectsql = 'SELECT * FROM ' . $table . ' LIMIT ' . $startfrom . ', ' . $offset;
            }
            $tabledumped = 1;
            $rows = $query_handler->execute($selectsql);
            $numfields = mysql_num_fields($rows);
            if ($numrows = $query_handler->db->getRowsNum($rows)) {
                $t1 = $comma1 = '';
                while ($row = $query_handler->db->fetchRow($rows)) {
                    $t2 = $comma2 = '';
                    for ($i = 0; $i < $numfields; $i++) {
                        $t2 .= $comma2 . ($usehex && !empty($row[$i]) && (jieqi_strexists($tablefields[$i]['Type'], 'char') || jieqi_strexists($tablefields[$i]['Type'], 'text')) ? '0x' . bin2hex($row[$i]) : '\'' . mysql_real_escape_string($row[$i]) . '\'');
                        $comma2 = ',';
                    }
                    if (strlen($t1) + $currsize + strlen($tabledump) < $sizelimit * 1000) {
                        if ($firstfield['Extra'] == 'auto_increment') {
                            $startfrom = $row[0];
                        } else {
                            $startfrom++;
                        }
                        $t1 .= $comma1 . ' (' . $t2 . ')';
                        $comma1 = ',';
                    } else {
                        $tabledump .= 'INSERT INTO ' . $table . ' VALUES ' . $t1 . ';' . "\n" . '';
                        $complete = false;
                        break 2;
                    }
                }
                $tabledump .= 'INSERT INTO ' . $table . ' VALUES ' . $t1 . ';' . "\n" . '';
            }
        }
    }
    $startrow = $startfrom;
    $tabledump .= "\n";
    return $tabledump;
}
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
function jieqi_strexists($haystack, $needle)
{
    return !strpos($haystack, $needle) === false;
}
function jieqi_random($length, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double) microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}
function jieqi_arraykeys2($array, $key2)
{
    $return = array();
    foreach ($array as $val) {
        $return[] = $val[$key2];
    }
    return $return;
}
function jieqi_makezip($filename, $volume, $type)
{
    if (@function_exists('gzcompress')) {
        include_once JIEQI_ROOT_PATH . '/lib/compress/zip.php';
        $zip = new JieqiZip();
        if ($type == 1) {
            $zipfilename = MYSQL_BACKUP_PATH . '/' . $filename . '-1' . '.zip';
            if (!$zip->zipstart($zipfilename)) {
                return false;
            }
            for ($i = 1; $i < $volume; $i++) {
                $sqlfilename = MYSQL_BACKUP_PATH . '/' . $filename . '-' . $i . '.sql';
                if (@is_file($sqlfilename)) {
                    $content = jieqi_readfile($sqlfilename);
                    $zip->zipadd(basename($sqlfilename), $content);
                    jieqi_delfile($sqlfilename);
                }
            }
            if ($zip->zipend()) {
                @chmod($zipfilename, 511);
            }
            return true;
        } else {
            if ($type == 2) {
                for ($i = 1; $i < $volume; $i++) {
                    $zipfilename = MYSQL_BACKUP_PATH . '/' . $filename . '-' . $i . '.zip';
                    if (!$zip->zipstart($zipfilename)) {
                        return false;
                    }
                    $sqlfilename = MYSQL_BACKUP_PATH . '/' . $filename . '-' . $i . '.sql';
                    if (@is_file($sqlfilename)) {
                        $content = jieqi_readfile($sqlfilename);
                        $zip->zipadd(basename($sqlfilename), $content);
                        if ($zip->zipend()) {
                            @chmod($zipfilename, 511);
                        }
                        jieqi_delfile($sqlfilename);
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
}
function jieqi_unzip($filename, $type)
{
    if (@function_exists('gzcompress')) {
        include_once JIEQI_ROOT_PATH . '/lib/compress/zip.php';
        $zip = new JieqiZip();
        if ($type == 1) {
        } else {
            if ($type == 2) {
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    return true;
}
function jieqi_getfilesarray($basename)
{
    $filearray = array();
    $handle = dir(MYSQL_BACKUP_PATH);
    while (false !== ($file = $handle->read())) {
        $subfile = substr(basename($file), 0, strpos(basename($file), '.'));
        $subfile = substr($subfile, 0, strpos($subfile, '-'));
        if ($basename == $subfile) {
            $filearray[] = $file;
        }
    }
    $handle->close();
    if (is_array($filearray) && 0 < count($filearray)) {
        for ($i = 0; $i < count($filearray); $i++) {
            if (!file_exists(MYSQL_BACKUP_PATH . '/' . $basename . '-' . ($i + 1) . substr($filearray[$i], strrpos($filearray[$i], '.')))) {
                return false;
            }
        }
        return $filearray;
    } else {
        return false;
    }
}
function jieqi_getbackuplog()
{
    $tmplogs = array();
    $handle = opendir(MYSQL_BACKUP_PATH);
    while ($handle !== false && ($file = @readdir($handle)) !== false) {
        if (substr($file, -4) == '.sql') {
            $tmplogs = $file;
        }
    }
    sort($tmplogs);
    $logary = array();
    $logname = '';
    $k = 0;
    foreach ($tmplogs as $v) {
        $tmpary = explode('-', $v);
        $tmpname = $tmpary[0];
        if ($tmpname != $logname) {
            $logname = $tmpname;
            $logary[$k]['name'] = $logname;
            $logary[$k]['time'] = filemtime(MYSQL_BACKUP_PATH . '/' . $v);
            if (1 < count($tmpary)) {
                $logary[$k]['num'] = 1;
            } else {
                $logary[$k]['num'] = 0;
            }
            $k++;
        } else {
            $logary[$k]['num']++;
        }
    }
    return $logary;
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
@set_time_limit(0);
@session_write_close();
jieqi_includedb();
$query_handler = JieqiQueryHandler::getInstance('JieqiQueryHandler');
include_once JIEQI_ROOT_PATH . '/admin/header.php';
include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
if (!defined('MYSQL_BACKUP_PATH')) {
    define('MYSQL_BACKUP_PATH', JIEQI_ROOT_PATH . '/files/system/dbbackup');
}
if (!jieqi_checkdir(MYSQL_BACKUP_PATH, true)) {
    jieqi_createdir(MYSQL_BACKUP_PATH, 511, true);
}
$query_handler->execute('SHOW CHARACTER SET LIKE \'gb%\'' . "\n" . '' . "\n" . '');
define('MYSQL_SERVER_INFO', mysql_get_server_info());
if ($_REQUEST['option'] == 'export') {
    if (isset($_POST['act']) && $_POST['act'] == 'backup') {
        jieqi_checkpost();
        $exporttype = $_REQUEST['exporttype'] == 'select' ? 'select' : 'all';
        $exporttables = $_REQUEST['tablearray'];
        $exportmode = $_REQUEST['exportmode'] == 'mysqldump' ? 'mysqldump' : 'multivol';
        $sqlcompat = $_REQUEST['exportversion'] ? $_REQUEST['exportversion'] == 'MYSQL40' ? 'MYSQL40' : 'MYSQL41' : '';
        $sqlcharset = in_array($_REQUEST['exportcharset'], array('gbk', 'big5', 'utf8')) ? $_REQUEST['exportcharset'] : '';
        $dumpcharset = $sqlcharset ? $sqlcharset : str_replace('-', '', JIEQI_CHAR_SET);
        $extendins = $_REQUEST['exportinsert'] == 1 ? 1 : 0;
        $sizelimit = intval(trim($_REQUEST['exportsize']));
        if ($sizelimit < 100) {
            $sizelimit = 100;
        }
        $usehex = $_REQUEST['exporthexcode'] == 1 ? 1 : '';
        $filename = trim($_REQUEST['exportfile']);
        $errtext = '';
        if (empty($sizelimit) || intval($sizelimit) < 100) {
            $errtext .= $jieqiLang[JIEQI_MODULE_NAME]['need_size_limit'] . '<br />';
        }
        if (empty($filename) || !preg_match('/[A-Za-z0-9_]+$/', $filename)) {
            $errtext .= $jieqiLang[JIEQI_MODULE_NAME]['need_file_name'] . '<br />';
        }
        $tables = array();
        if ($exporttype == 'all') {
            $tables = jieqi_arraykeys2(jieqi_fetchtablelist(JIEQI_DB_PREFIX), 'Name');
        } else {
            if ($exporttype == 'select') {
                if (is_array($exporttables) && 0 < count($exporttables)) {
                    foreach ($exporttables as $value) {
                        $tables[] = $value;
                    }
                }
            }
        }
        if (!is_array($tables) || empty($tables)) {
            $errtext .= $jieqiLang[JIEQI_MODULE_NAME]['need_export_table'] . '<br />';
        }
        $exporttime = gmdate(JIEQI_DATE_FORMAT . ' ' . JIEQI_TIME_FORMAT, JIEQI_NOW_TIME);
        if (empty($errtext)) {
            $idstring = '# Identify: ' . base64_encode($exporttime . ', ' . $exporttype . ', ' . $exportmode) . "\n";
            $setnames = $sqlcharset && '4.1' < MYSQL_SERVER_INFO && (!$sqlcompat || $sqlcompat == 'MYSQL41') ? 'SET NAMES \'' . $dumpcharset . '\';' . "\n" . '' . "\n" . '' : '';
            if ('4.1' < MYSQL_SERVER_INFO) {
                if ($sqlcharset) {
                    $query_handler->execute('SET NAMES \'' . $sqlcharset . '\';' . "\n" . '' . "\n" . '');
                }
                if ($sqlcompat == 'MYSQL40') {
                    $query_handler->execute('SET SQL_MODE=\'MYSQL40\'');
                } else {
                    if ($sqlcompat == 'MYSQL41') {
                        $query_handler->execute('SET SQL_MODE=\'\'');
                    }
                }
            }
            $backupfilename = MYSQL_BACKUP_PATH . '/' . str_replace(array('/', '\\', '.'), '', $filename);
            if ($exportmode == 'multivol') {
                header('Content-type: text/html; charset=' . JIEQI_SYSTEM_CHARSET);
                echo str_repeat(' ', 4096);
                echo $jieqiLang['system']['export_file_start'] . '<br />';
                ob_flush();
                flush();
                while (1) {
                    $sqldump = '';
                    $complete = true;
                    $volume = intval($volume) + 1;
                    $tableid = intval($tableid);
                    for ($startfrom = intval($startrow); strlen($sqldump) < $sizelimit * 1000; $tableid++) {
                        $sqldump .= jieqi_sqldumptable($tables[$tableid], $startfrom, strlen($sqldump));
                        if ($complete) {
                            $startfrom = 0;
                        }
                    }
                    $dumpfile = $backupfilename . '-%s' . '.sql';
                    !$complete && $tableid--;
                    if (trim($sqldump)) {
                        $sqldump = $idstring . '# <?php exit();?>' . "\n" . '' . '# JIEQI CMS Multi-Volume Data Dump Vol.' . $volume . "\n" . '# Version: JIEQI CMS ' . JIEQI_VERSION . ' ' . JIEQI_VERSION_TYPE . "\n" . '# Time: ' . $exporttime . "\n" . '# Type: ' . $exportmode . "\n" . '# Table Prefix: ' . JIEQI_DB_PREFIX . "\n" . '#' . "\n" . '' . '# JIEQI CMS Homepage: http://www.jieqi.com' . "\n" . '' . '# Please visit our website for newest infomation about JIEQI CMS' . "\n" . '' . '# --------------------------------------------------------' . "\n" . '' . "\n" . '' . "\n" . '' . $setnames . $sqldump;
                        $dumpfilename = sprintf($dumpfile, $volume);
                        $fp = @fopen($dumpfilename, 'wb');
                        @flock($fp, 2);
                        if (@(!fwrite($fp, $sqldump))) {
                            @fclose($fp);
                            jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['write_file_failure']);
                        } else {
                            @fclose($fp);
                            unset($sqldump);
                            echo sprintf($jieqiLang[JIEQI_MODULE_NAME]['export_file_name'], basename($dumpfilename)) . '<br />';
                            ob_flush();
                            flush();
                        }
                    } else {
                        break;
                    }
                }
                $jieqiTpl->assign('option', 3);
                $jieqiTpl->assign('backup_info', $jieqiLang['system']['export_mysql_success']);
                jieqi_getconfigs(JIEQI_MODULE_NAME, 'backuplog');
                if (@file_exists(MYSQL_BACKUP_PATH)) {
                    $handle = @opendir(MYSQL_BACKUP_PATH);
                    while ($handle !== false && ($files = @readdir($handle)) !== false) {
                        if (strpos($files, $filename) === 0) {
                            $jieqiBackuplog[] = array('name' => $files, 'version' => $sqlcompat ? $sqlcompat : MYSQL_SERVER_INFO, 'time' => filemtime(MYSQL_BACKUP_PATH . '/' . $files), 'mode' => $jieqiLang[JIEQI_MODULE_NAME]['export_multivol'], 'size' => filesize(MYSQL_BACKUP_PATH . '/' . $files), 'type' => $exporttype == 'all' ? $jieqiLang[JIEQI_MODULE_NAME]['export_all_data'] : $jieqiLang[JIEQI_MODULE_NAME]['export_custom_data'], 'volume' => intval(substr(basename($files), strrpos(basename($files), '-') + 1)));
                        }
                    }
                    @closedir($handle);
                }
                jieqi_setconfigs('backuplog', 'jieqiBackuplog', $jieqiBackuplog, JIEQI_MODULE_NAME);
            } else {
                if ($exportmode == 'mysqldump') {
                    $volume = 1;
                    $tablesstr = '';
                    $filestring = '<li>' . $jieqiLang[JIEQI_MODULE_NAME]['export_status_title'] . '</li>';
                    foreach ($tables as $t) {
                        $tablesstr .= '"' . $t . '" ';
                    }
                    list($dbhost, $dbport) = explode(':', JIEQI_DB_HOST);
                    $result = $query_handler->execute('SHOW VARIABLES LIKE \'basedir\'');
                    $mysql_base = @mysql_fetch_array($result, MYSQL_NUM)[1];
                    $dumpfile = $backupfilename . '-' . $volume . '.sql';
                    jieqi_delfile($dumpfile);
                    $mysqlbin = $mysql_base == '/' ? '' : jieqi_setslashes($mysql_base) . 'bin/';
                    @shell_exec($mysqlbin . 'mysqldump --force --quick ' . ('4.1' < MYSQL_SERVER_INFO ? '--skip-opt --create-options' : '-all') . ' --add-drop-table' . (JIEQI_DB_CHARSET ? ' --default-character-set="' . JIEQI_DB_CHARSET . '"' : '') . ($extendins == 1 ? ' --extended-insert' : '') . '' . ('4.1' < MYSQL_SERVER_INFO && $sqlcompat == 'MYSQL40' ? ' --compatible=mysql40' : '') . ' --host="' . $dbhost . '"' . ($dbport ? is_numeric($dbport) ? ' --port="' . $dbport . '"' : ' --socket="' . $dbport . '"' : '') . ' --user="' . JIEQI_DB_USER . '" --password="' . JIEQI_DB_PASS . '" "' . JIEQI_DB_NAME . '" ' . $tablesstr . ' > ' . $dumpfile);
                    if (@file_exists($dumpfile)) {
                        if (@is_writeable($dumpfile)) {
                            $fp = @fopen($dumpfile, 'rb+');
                            @fwrite($fp, $idstring . '# <?php exit();?>' . "\n" . ' ' . $setnames . '' . "\n" . ' #');
                            @fclose($fp);
                        }
                        $jieqiTpl->assign('option', 3);
                        $filestring .= '<li>-' . sprintf($jieqiLang[JIEQI_MODULE_NAME]['export_file_name'], basename($dumpfile)) . '</li>';
                        $filestring .= '<li>' . $jieqiLang['system']['export_mysql_success'] . '</li>';
                        $jieqiTpl->assign('backup_info', $filestring);
                        unset($filestring);
                        jieqi_getconfigs(JIEQI_MODULE_NAME, 'backuplog');
                        if (@file_exists(MYSQL_BACKUP_PATH)) {
                            $handle = @opendir(MYSQL_BACKUP_PATH);
                            while ($handle !== false && ($files = @readdir($handle)) !== false) {
                                if (strpos($files, $filename) === 0) {
                                    $jieqiBackuplog[] = array('name' => $files, 'version' => $sqlcompat ? $sqlcompat : MYSQL_SERVER_INFO, 'time' => filemtime(MYSQL_BACKUP_PATH . '/' . $files), 'mode' => $jieqiLang[JIEQI_MODULE_NAME]['export_mysqldump'], 'size' => filesize(MYSQL_BACKUP_PATH . '/' . $files), 'type' => $exporttype == 'all' ? $jieqiLang[JIEQI_MODULE_NAME]['export_all_data'] : $jieqiLang[JIEQI_MODULE_NAME]['export_custom_data'], 'volume' => 0);
                                }
                            }
                            @closedir($handle);
                        }
                        jieqi_setconfigs('backuplog', 'jieqiBackuplog', $jieqiBackuplog, JIEQI_MODULE_NAME);
                    } else {
                        jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['create_file_failure']);
                    }
                }
            }
        } else {
            jieqi_printfail($errtext);
        }
    } else {
        $jieqiTpl->assign('option', 1);
        $shelldisabled = function_exists('shell_exec') ? '' : 'disabled';
        $defaultfilename = date('ymd') . '_' . jieqi_random(8);
        $num = 0;
        $tablestring = '<div id="tablelist" style="display:none;"><table border="0"><tr>';
        foreach (jieqi_fetchtablelist(JIEQI_DB_PREFIX) as $table) {
            $tablestring .= $num % 3 == 0 ? '</tr><tr><td style="text-align:left;font-size:12px;font-weight:normal;"><input type="checkbox" name="tablearray[]" id="tablearray[]" value="' . $table['Name'] . '" />' . $table['Name'] . '</td>' : '<td style="text-align:left;font-size:12px;font-weight:normal;"><input type="checkbox" name="tablearray[]" id="tablearray[]" value="' . $table['Name'] . '" />' . $table['Name'] . '</td>';
            $num++;
        }
        $tablestring .= '</tr></table></div>';
        $export_form = new JieqiThemeForm($jieqiLang[JIEQI_MODULE_NAME]['db_export'], 'dbexport', $jieqiModules[JIEQI_MODULE_NAME]['url'] . '/admin/dbmanage.php');
        $export_type = new JieqiFormRadio($jieqiLang[JIEQI_MODULE_NAME]['export_type'], 'exporttype', 'all');
        $export_type->setExtra('onClick=\'javascript:if(this.value=="select"){document.getElementById("tablelist").style.display="block";}else{document.getElementById("tablelist").style.display="none";}\'');
        $export_type->addOption('all', $jieqiLang[JIEQI_MODULE_NAME]['export_all_table']);
        $export_type->addOption('select', $jieqiLang[JIEQI_MODULE_NAME]['export_select_table']);
        $export_form->addElement($export_type);
        $export_form->addElement(new JieqiFormLabel($jieqiLang[JIEQI_MODULE_NAME]['export_talbe_list'], $tablestring));
        $export_mode = new JieqiFormRadio($jieqiLang[JIEQI_MODULE_NAME]['export_mode'], 'exportmode', 'multivol');
        $export_mode->setExtra($shelldisabled);
        $export_mode->addOption('multivol', $jieqiLang[JIEQI_MODULE_NAME]['export_partition']);
        $export_mode->addOption('mysqldump', $jieqiLang[JIEQI_MODULE_NAME]['export_dump']);
        $export_form->addElement($export_mode);
        $export_size = new JieqiFormText($jieqiLang[JIEQI_MODULE_NAME]['export_size_limit'], 'exportsize', 6, 4, '2048');
        $export_size->setDescription($jieqiLang[JIEQI_MODULE_NAME]['export_file_unit']);
        $export_form->addElement($export_size, true);
        $export_extend = new JieqiFormRadio($jieqiLang[JIEQI_MODULE_NAME]['export_extend_insert'], 'exportinsert', '');
        $export_extend->setExtra('onClick=\'\'');
        $export_extend->addOption('1', $jieqiLang[JIEQI_MODULE_NAME]['radio_checked_yes']);
        $export_extend->addOption('0', $jieqiLang[JIEQI_MODULE_NAME]['radio_checked_no']);
        $export_form->addElement($export_extend);
        $export_version = new JieqiFormRadio($jieqiLang[JIEQI_MODULE_NAME]['export_version'], 'exportversion', '');
        $export_version->setExtra('onClick=\'\'');
        $export_version->addOption('', $jieqiLang[JIEQI_MODULE_NAME]['export_mysql_default']);
        $export_version->addOption('MYSQL40', $jieqiLang[JIEQI_MODULE_NAME]['export_mysql_low']);
        $export_version->addOption('MYSQL41', $jieqiLang[JIEQI_MODULE_NAME]['export_mysql_high']);
        $export_form->addElement($export_version);
        $export_charset = new JieqiFormRadio($jieqiLang[JIEQI_MODULE_NAME]['export_charset'], 'exportcharset', '');
        $export_charset->setExtra('onClick=\'\'');
        $export_charset->addOption('', $jieqiLang[JIEQI_MODULE_NAME]['export_charset_default']);
        JIEQI_DB_CHARSET && '4.1' < MYSQL_SERVER_INFO ? $export_charset->addOption(JIEQI_DB_CHARSET, strtoupper(JIEQI_DB_CHARSET)) : '';
        JIEQI_DB_CHARSET != 'utf8' && '4.1' < MYSQL_SERVER_INFO ? $export_charset->addOption('utf8', 'UTF-8') : '';
        $export_form->addElement($export_charset);
        $export_hexcode = new JieqiFormRadio($jieqiLang[JIEQI_MODULE_NAME]['export_hexcode'], 'exporthexcode', '1');
        $export_hexcode->setExtra('onClick=\'\'');
        $export_hexcode->addOption('1', $jieqiLang[JIEQI_MODULE_NAME]['radio_checked_yes']);
        $export_hexcode->addOption('', $jieqiLang[JIEQI_MODULE_NAME]['radio_checked_no']);
        $export_form->addElement($export_hexcode);
        $export_file = new JieqiFormText($jieqiLang[JIEQI_MODULE_NAME]['export_file'], 'exportfile', 20, 250, $defaultfilename);
        $export_file->setDescription($jieqiLang[JIEQI_MODULE_NAME]['export_file_format']);
        $export_form->addElement($export_file, true);
        $export_form->addElement(new JieqiFormHidden('act', 'backup'));
        $export_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $export_form->addElement(new JieqiFormHidden('option', 'export'));
        $on_submit = new JieqiFormButton('&nbsp;', 'submit', LANG_SUBMIT, 'submit');
        $on_submit->setExtra('onclick=""');
        $export_form->addElement($on_submit);
        $jieqiTpl->assign('dbmanage_form', $export_form->render(JIEQI_FORM_MAX));
    }
} else {
    if ($_REQUEST['option'] == 'import') {
        if (isset($_POST['act']) && $_POST['act'] == 'recover') {
            jieqi_checkpost();
            $filename = $_REQUEST['importfile'];
            $errtext = '';
            if (!empty($filename)) {
                $filename = trim($filename);
                $filename = strpos($filename, '.') ? substr($filename, 0, strpos($filename, '.')) : $filename;
                $filename = strpos($filename, '-') ? substr($filename, 0, strpos($filename, '-')) : $filename;
                if (!preg_match('/[A-Za-z0-9_]+$/', $filename)) {
                    $errtext .= $jieqiLang[JIEQI_MODULE_NAME]['need_file_name'] . '<br />';
                }
            } else {
                $errtext .= $jieqiLang[JIEQI_MODULE_NAME]['need_file_name'] . '<br />';
            }
            if (empty($errtext)) {
                $db_query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
                $sqlfilearray = jieqi_getfilesarray($filename);
                if (is_array($sqlfilearray) && 0 < count($sqlfilearray)) {
                    foreach ($sqlfilearray as $v) {
                        $sqlfilecontent = jieqi_readfile(MYSQL_BACKUP_PATH . '/' . $v);
                        $sqlary = array();
                        $sqlerr = array();
                        jieqi_splitsqlfile($sqlary, str_replace(' jieqi', ' ' . JIEQI_DB_PREFIX, $sqlfilecontent));
                        foreach ($sqlary as $s) {
                            $s = trim($s);
                            if (!empty($s) && 5 < strlen($s)) {
                                $retflag = $db_query->execute(jieqi_syntablestruct($s, '4.1' < MYSQL_SERVER_INFO, JIEQI_DB_CHARSET));
                                if (!$retflag) {
                                    $sqlerr[] = array('sql' => $s, 'error' => $db_query->db->error());
                                    jieqi_printfail(sprintf($jieqiLang[JIEQI_MODULE_NAME]['print_sql_error'], jieqi_htmlstr($s), jieqi_htmlstr($db_query->db->error())));
                                    break;
                                }
                            }
                        }
                    }
                    jieqi_jumppage(JIEQI_URL . '/admin/dbmanage.php?option=import', LANG_DO_SUCCESS, $jieqiLang[JIEQI_MODULE_NAME]['import_mysql_success']);
                } else {
                    jieqi_printfail($jieqiLang[JIEQI_MODULE_NAME]['import_file_error']);
                }
            } else {
                jieqi_printfail($errtext);
            }
        } else {
            if (isset($_POST['act']) && $_POST['act'] == 'delete' && is_array($_POST['checkid']) && 0 < count($_POST['checkid'])) {
                jieqi_checkpost();
                jieqi_getconfigs(JIEQI_MODULE_NAME, 'backuplog');
                foreach ($_POST['checkid'] as $v) {
                    $backfile = MYSQL_BACKUP_PATH . '/' . $jieqiBackuplog[$v]['name'];
                    if (@file_exists($backfile)) {
                        jieqi_delfile($backfile);
                    }
                    unset($jieqiBackuplog[$v]);
                }
                jieqi_setconfigs('backuplog', 'jieqiBackuplog', $jieqiBackuplog, JIEQI_MODULE_NAME);
                jieqi_jumppage(JIEQI_URL . '/admin/dbmanage.php?option=import', LANG_DO_SUCCESS, $jieqiLang[JIEQI_MODULE_NAME]['log_del_success']);
            }
            $jieqiTpl->assign('option', 2);
            $import_form = new JieqiThemeForm($jieqiLang[JIEQI_MODULE_NAME]['db_import'], 'dbimport', $jieqiModules[JIEQI_MODULE_NAME]['url'] . '/admin/dbmanage.php');
            $import_file = new JieqiFormText($jieqiLang[JIEQI_MODULE_NAME]['import_file'], 'importfile', 20, 250);
            $import_file->setDescription($jieqiLang[JIEQI_MODULE_NAME]['import_file_format']);
            $import_form->addElement($import_file, true);
            $import_form->addElement(new JieqiFormHidden('act', 'recover'));
            $import_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
            $import_form->addElement(new JieqiFormHidden('option', 'import'));
            $on_submit = new JieqiFormButton('&nbsp;', 'submit', LANG_SUBMIT, 'submit');
            $on_submit->setExtra('onclick=""');
            $import_form->addElement($on_submit);
            $jieqiTpl->assign('dbmanage_form', $import_form->render(JIEQI_FORM_MAX));
            $logfileisarray = false;
            jieqi_getconfigs(JIEQI_MODULE_NAME, 'backuplog');
            if (is_array($jieqiBackuplog) && 0 < count($jieqiBackuplog)) {
                foreach ($jieqiBackuplog as $k => $v) {
                    if (!@file_exists(MYSQL_BACKUP_PATH . '/' . $v['name'])) {
                        unset($jieqiBackuplog[$k]);
                    }
                }
                $logfileisarray = true;
            }
            jieqi_setconfigs('backuplog', 'jieqiBackuplog', $jieqiBackuplog, JIEQI_MODULE_NAME);
            if ($logfileisarray) {
                $log_array = array();
                $i = 0;
                foreach ($jieqiBackuplog as $k => $v) {
                    $log_array[$i]['id'] = $k;
                    $log_array[$i]['name'] = $v['name'];
                    $log_array[$i]['version'] = $v['version'];
                    $log_array[$i]['time'] = date(JIEQI_DATE_FORMAT . ' ' . JIEQI_TIME_FORMAT, $v['time']);
                    $log_array[$i]['mode'] = $v['mode'];
                    $log_array[$i]['size'] = round($v['size'] / 1024, 2) . 'K';
                    $log_array[$i]['type'] = $v['type'];
                    $log_array[$i]['volume'] = $v['volume'];
                    $log_array[$i]['checkbox'] = '<input type="checkbox" id="checkid[]" name="checkid[]" value="' . $k . '" />';
                    $log_array[$i]['importurl'] = substr($v['name'], strpos($v['name'], '.')) == '.sql' ? './dbmanage.php?option=import&acr=recover&importfile=' . substr(basename($v['name']), 0, strpos(basename($v['name']), '-')) : '';
                    $i++;
                }
                $jieqiTpl->assign('log_list', $log_array);
            }
        }
    } else {
        jieqi_printfail(LANG_ERROR_PARAMETER);
    }
}
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH . '/templates/admin/dbmanage.html';
include_once JIEQI_ROOT_PATH . '/admin/footer.php';