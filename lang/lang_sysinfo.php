<?php
/**
 * 语言包-系统信息
 *
 * 语言包-系统信息
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: lang_sysinfo.php 193 2008-11-25 02:52:44Z juny $
 */

$jieqiLang['system']['sysinfo']=1; //表示本语言包已经包含
//amin/sysinfo.php
$jieqiLang['system']['sinfo_is_support']='支持';
$jieqiLang['system']['sinfo_not_support']='不支持';
$jieqiLang['system']['sinfo_empty_value']='无';

$jieqiLang['system']['sinfo_php_version']='PHP版本';
$jieqiLang['system']['snote_php_version']='PHP版本需要 4.30 以上，推荐官方稳定版';

$jieqiLang['system']['sinfo_php_os']='操作系统';
$jieqiLang['system']['snote_php_os']='Windows及Linux系统皆可运行，推荐Linxu系列操作系统';

$jieqiLang['system']['sinfo_disk_space']='当前硬盘剩余空间';
$jieqiLang['system']['snote_disk_space']='至少不少于1G空间';

$jieqiLang['system']['sinfo_server_name']='当前域名';
$jieqiLang['system']['snote_server_name']='';

$jieqiLang['system']['sinfo_server_port']='端口';
$jieqiLang['system']['snote_server_port']='一般默认80即可';

$jieqiLang['system']['sinfo_server_software']='服务软件';
$jieqiLang['system']['snote_server_software']='windows下一般用IIS，linux下一般用Apache，对性能要求高的可以用Zeus、lighttpd等';

$jieqiLang['system']['sinfo_accept_language']='服务器语种';
$jieqiLang['system']['snote_accept_language']='';

$jieqiLang['system']['sinfo_document_root']='网站根目录';
$jieqiLang['system']['snote_document_root']='';

$jieqiLang['system']['sinfo_server_time']='服务器时间';
$jieqiLang['system']['snote_server_time']='PHP5需要设置时区，默认时区与中国时区差8个小时，这种情况需要在php.ini中这么设置 date.timezone = PRC';

$jieqiLang['system']['sinfo_zend_version']='Zend Engine版本';
$jieqiLang['system']['snote_zend_version']='Zend Engine是PHP自带的，不用特别安装';

$jieqiLang['system']['sinfo_zend_guardloader']='Zend Guard Loader版本';
$jieqiLang['system']['snote_zend_guardloader']='Zend Guard Loader版本需要3.3以上';

$jieqiLang['system']['sinfo_disable_functions']='禁止函数';
$jieqiLang['system']['snote_disable_functions']='禁止函数有可能导致程序执行错误，请谨慎设置';

$jieqiLang['system']['sinfo_register_globals']='自定义全局变量';
$jieqiLang['system']['snote_register_globals']='即 php.ini 中的 register_globals 设置，建议设置成 Off';

$jieqiLang['system']['sinfo_memory_limit']='脚本运行可占最大内存';
$jieqiLang['system']['snote_memory_limit']='一般脚本程序按默认 8M 设置即可，如需要处理文件打包等比较占资源的操作，可设置成 32M 或更多';

$jieqiLang['system']['sinfo_upload_maxsize']='脚本上传文件大小限制';
$jieqiLang['system']['snote_upload_maxsize']='请根据实际需要设置，一般用默认即可';

$jieqiLang['system']['sinfo_post_maxsize']='POST提交最大档案限制';
$jieqiLang['system']['snote_post_maxsize']='请根据实际需要设置，一般用默认即可';

$jieqiLang['system']['sinfo_max_exetime']='脚本执行超时时间';
$jieqiLang['system']['snote_max_exetime']='默认 30 秒，特殊程序主要执行时间比较长的可放宽这个限制';

$jieqiLang['system']['sinfo_display_errors']='显示错误消息';
$jieqiLang['system']['snote_display_errors']='即 php.ini 中的 display_errors 设置，网站测试或者出错时可以显示，以便于发现问题，正常运行期间建议不显示';

$jieqiLang['system']['sinfo_smtp_support']='SMTP支持';
$jieqiLang['system']['snote_smtp_support']='需要SMTP发邮件时候用到';

$jieqiLang['system']['sinfo_safe_mode']='安全模式';
$jieqiLang['system']['snote_safe_mode']='安全模式下可以增加系统安全性，但是也限制了一些功能，默认不开放';

$jieqiLang['system']['sinfo_xml_parser']='XML语法解析';
$jieqiLang['system']['snote_xml_parser']='必须支持';

$jieqiLang['system']['sinfo_xml_support']='XML函数库';
$jieqiLang['system']['snote_xml_support']='目前可以不用';

$jieqiLang['system']['sinfo_ftp_support']='FTP文件传输支持';
$jieqiLang['system']['snote_ftp_support']='目前可以不用';

$jieqiLang['system']['sinfo_url_fopen']='允许使用URL打开文件';
$jieqiLang['system']['snote_url_fopen']='即 php.ini 中的 allow_url_fopen 设置，如果需要采集，这个必须开放';

$jieqiLang['system']['sinfo_enable_dl']='动态链接库调用';
$jieqiLang['system']['snote_enable_dl']='目前不用';

$jieqiLang['system']['sinfo_imap_support']='IMAP 电子邮件系统函数库';
$jieqiLang['system']['snote_imap_support']='目前不用';

$jieqiLang['system']['sinfo_calendar_support']='日历函数库';
$jieqiLang['system']['snote_calendar_support']='目前不用';

$jieqiLang['system']['sinfo_zlib_support']='压缩文件函数库(Zlib)';
$jieqiLang['system']['snote_zlib_support']='必须支持';

$jieqiLang['system']['sinfo_session_support']='Session支持';
$jieqiLang['system']['snote_session_support']='必须支持';

$jieqiLang['system']['sinfo_socket_support']='Socket支持';
$jieqiLang['system']['snote_socket_support']='必须支持';

$jieqiLang['system']['sinfo_preg_support']='正则表达式函数库';
$jieqiLang['system']['snote_preg_support']='必须支持';

$jieqiLang['system']['sinfo_gd_support']='图像函数库(GD)';
$jieqiLang['system']['snote_gd_support']='如需要生成图片，图片加水印及图片验证码等就必须支持';
	
$jieqiLang['system']['sinfo_gd_version']='GD图像库版本';
$jieqiLang['system']['snote_gd_version']='';

$jieqiLang['system']['sinfo_freetype_support']='FreeTyte字体支持';
$jieqiLang['system']['snote_freetype_support']='图片中需要写中文就必须支持';

$jieqiLang['system']['sinfo_gif_read']='GIF图片读取支持';
$jieqiLang['system']['snote_gif_read']='';

$jieqiLang['system']['sinfo_gif_create']='GIF图片创建支持';
$jieqiLang['system']['snote_gif_create']='';

$jieqiLang['system']['sinfo_jpg_support']='JPG图片处理支持';
$jieqiLang['system']['snote_jpg_support']='';

$jieqiLang['system']['sinfo_png_support']='PNG图片处理支持';
$jieqiLang['system']['snote_png_support']='';

$jieqiLang['system']['sinfo_wbmp_support']='BMP图片处理支持';
$jieqiLang['system']['snote_wbmp_support']='';

$jieqiLang['system']['sinfo_iconv_support']='Iconv编码转换';
$jieqiLang['system']['snote_iconv_support']='用于utf8编码和其他编码之间的转换，目前不是必要的，但是需要的时候用系统函数库效率较高';

$jieqiLang['system']['sinfo_mysql_support']='MySQL数据库支持';
$jieqiLang['system']['snote_mysql_support']='必须支持';

$jieqiLang['system']['sinfo_magic_quotes']='魔术引用';
$jieqiLang['system']['snote_magic_quotes']='即 php.ini 中的 magic_quotes_gpc 设置，本系统可设置成 Off';

?>