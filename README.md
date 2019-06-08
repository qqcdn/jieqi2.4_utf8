# jieqi2.4_utf8
杰奇2.4,UTF8版本


 * Jieqi2.4 解密开源程序
 * 来源：https://www.hostloc.com

1、服务器环境
php5.3.*- php7.1 + mysql 5.* - MariaDB 10.*

php请加载以下模块
mysql zlib sockets curl iconv mbstring gd

2、更改网站默认编码为uft8，采集GBK自动转为utf8,创建数据库编码为utf8mb4，把sql目录下的jieqi2.4.sql导入数据库，作为网站初始的数据库结构及数据，更改默认存储为INNODB

3、上传网站程序后，以下4个目录必须可写：cache compiled configs files

4、编辑网站目录下 /configs/define.php ，以下数据库参数根据实际填写
@define('JIEQI_DB_HOST','localhost');   //数据库服务器地址，跟网站在同一服务器时候填localhost
@define('JIEQI_DB_USER','root');   //数据库登录账号
@define('JIEQI_DB_PASS','pass');   //数据库登录密码
@define('JIEQI_DB_NAME','jieqicms');  //网站系统使用的数据库名字

5、默认管理员账号密码：
admin
jieqi.com

6、有彩蛋，能支持到PHP7.3

网站后台 http://www.***.com/admin/
进入后台后可具体设置权限、参数等

正式使用时，请在前台会员中心修改默认的管理员密码

小说分类修改不在后台，请直接编辑 /configs/article/sort.php

6、
网站模板修改规范请参考 http://help.jieqi.com/template/index.html
登录充值接口申请，请参考“登录充值接口.txt”
网站授权设置请参考“软件授权.txt”
官方网站及联系方式请访问： http://www.jieqi.com
======================================================================
电脑版和手机版网站同时安装配置方法：

1、电脑版和手机版网站使用两个独立目录，但是共用数据库和数据文件。默认www为电脑版程序目录，建议绑定域名 www.***.com；m为手机版程序目录，建议绑定域名m.***.com
2、编辑手机版网站目录下的  /configs/define.php ，数据库连接设置跟电脑版保持一致
3、如果修改过分类文件 /configs/article/sort.php，请手机版和电脑版保持一致
4、默认程序生成的文件件保存在电脑版网站的 files 目录下，手机版网站也需要读写同一个目录。linux下建议用ls命令建立一个链接把手机站的files目录指向电脑站的files。
windows可以考虑电脑和手机版里面都指定存储目录的绝对路径和访问url，这两个参数在后台 系统管理-系统定义 里面的“数据文件保存路径”和“访问数据文件的URL”。（比如“数据文件保存路径”设置成 E:/web/www/files，“访问数据文件的URL”设置成 http://www.jieqi.com/files）
