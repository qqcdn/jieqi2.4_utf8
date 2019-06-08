<?php
/**
 * 语言包-数据库管理
 *
 * 语言包-数据库管理
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_database.php 344 2009-06-23 03:06:07Z juny $
 */

$jieqiLang['system']['database']=1; //表示本语言包已经包含
//tools/database
$jieqiLang['system']['need_select_table']='请先选择要处理的数据表！';
$jieqiLang['system']['optimize_table_action']='优化';
$jieqiLang['system']['repair_table_action']='修复';
$jieqiLang['system']['optrep_table_success']='恭喜您，数据表%s成功，系统将重新返回数据表操作页面！';
$jieqiLang['system']['optrep_no_table']='对不起，没发现需要%s的数据表，请返回重新选择！';
$jieqiLang['system']['need_sql_data']='请先输入SQL语句！';
$jieqiLang['system']['deny_sql_data']='对不起，您提交的SQL语句有禁止执行的代码！';
$jieqiLang['system']['execute_sql_success']='恭喜您，全部SQL执行成功，您可以进行其他操作！';
$jieqiLang['system']['print_sql_error']='SQL执行失败：<br />SQL语句：%s<br />错误提示：%s';
$jieqiLang['system']['sql_some_error']='SQL执行完成，其中有错误提示如下：<br />%s';
$jieqiLang['system']['show_error_format']='<hr />SQL语句：%s<br />错误提示：%s';

$jieqiLang['system']['db_export']='数据库备份';
$jieqiLang['system']['export_type']='数据备份类型';
$jieqiLang['system']['export_all_table']='全部数据';
$jieqiLang['system']['export_select_table']='自定义数据';
$jieqiLang['system']['export_talbe_list']='选择要备份的表格';
$jieqiLang['system']['export_mode']='数据备份方式';
$jieqiLang['system']['export_dump']='系统MySQL Dump (Shell)备份';
$jieqiLang['system']['export_partition']='JIEQI CMS 分卷备份';
$jieqiLang['system']['export_size_limit']='分卷文件长度限制';
$jieqiLang['system']['export_file_unit']='单位（KB），设置不要小于100';
$jieqiLang['system']['export_extend_insert']='使用扩展插入(Extended Insert)方式';
$jieqiLang['system']['radio_checked_yes']='是';
$jieqiLang['system']['radio_checked_no']='否';
$jieqiLang['system']['export_version']='建表语句格式';
$jieqiLang['system']['export_mysql_default']='默认';
$jieqiLang['system']['export_mysql_low']='MySQL 3.23/4.0.x';
$jieqiLang['system']['export_mysql_high']='MySQL 4.1.x/5.x';
$jieqiLang['system']['export_charset']='强制字符集';
$jieqiLang['system']['export_charset_default']='默认';
//$jieqiLang['system']['export_charset_gbk']='GBK';
//$jieqiLang['system']['export_charset_utf8']='UTF-8';
$jieqiLang['system']['export_hexcode']='十六进制方式';
$jieqiLang['system']['export_compress']='压缩备份文件';
$jieqiLang['system']['export_zip_one']='多分卷压缩成一个文件';
$jieqiLang['system']['export_zip_all']='每个分卷压缩成单独文件';
$jieqiLang['system']['export_zip_none']='不压缩';
$jieqiLang['system']['export_file']='备份文件名';
$jieqiLang['system']['export_file_format']='最多允许20个字母数字下划线组合，不带扩展名';
$jieqiLang['system']['need_size_limit']='分卷文件尺寸错误，必须大于100的数字！';
$jieqiLang['system']['need_file_name']='备份SQL文件名错误！';
$jieqiLang['system']['need_export_table']='未选择任何数据表！';
$jieqiLang['system']['create_file_failure']='生成SQL文件失败！';
$jieqiLang['system']['write_file_failure']='写入SQL文件失败！';
$jieqiLang['system']['export_mysql_success']='数据已成功备份！';
$jieqiLang['system']['export_file_start']='开始备份数据，请耐心等待！';
$jieqiLang['system']['export_file_name']='正在生成SQL文件： %s';
$jieqiLang['system']['create_zip_failure']='生成压缩文件失败！';
$jieqiLang['system']['log_add_failure']='写入数据库备份日志错误！';
$jieqiLang['system']['log_del_failure']='删除数据库备份日志错误！';
$jieqiLang['system']['log_del_success']='删除数据库备份日志成功！';
$jieqiLang['system']['export_all_data']='全部数据';
$jieqiLang['system']['export_custom_data']='自定义数据';
$jieqiLang['system']['export_multivol']='分卷';
$jieqiLang['system']['export_mysqldump']='dump';

$jieqiLang['system']['db_import']='数据库恢复';
$jieqiLang['system']['import_file']='备份文件名';
$jieqiLang['system']['log_query_error']='读取历史备份纪录错误！';
$jieqiLang['system']['import_file_format']='输入文件名(不带扩展名)，多卷文件会自动导入全部数据';
$jieqiLang['system']['import_mysql_success']='数据已成功恢复！';
$jieqiLang['system']['import_file_error']='读取备份文件失败！';

$jieqiLang['system']['db_error_logincheck']='对不起，验证码错误！';
$jieqiLang['system']['db_error_userpass']='数据库账号或密码错误！';

?>