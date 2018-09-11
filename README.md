# 阿里云线上部署环境


**经纪人**： http://fang.cd121.com  
**测试帐号/密码**：   
~~**南京（暂时不用）** 15951634202 / abcd1234!~~  
**杭州** 15855413165 / 123456

**管理端**： http://fang-admin.cd121.com   
**测试帐号/密码**：  
~~**南京（暂时不用）** ceshiyuan / doucare~~  
**杭州** ceshiyuan / doucare

# 开发

## 开发环境

- 操作系统： `Ubuntu` / `Windows` / `Mac`
- 开发环境： `Nginx` + `MySQL5.7` + `PHP7.1` + `Memcached`
- 开发工具： `IntelliJ Idea 2016.2`
- 调试工具： `Chrome` + `Xdebug`
- 数据库工具： `Navicat Premium`

**以下如无特殊说明，均以`Ubuntu`环境为例，`Windows`环境请自行百度**

## Nginx/MySQL/PHP环境

### 安装Nginx、MySQL、PHP环境

```
sudo apt-get install nginx
sudo apt-get install php7.1
sudo apt-get install mysql-server-5.7
sudo apt-get install memcached
```

**注意 mysql 一定要使用非严格模式**

```
select @@sql_mode;

STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION

```

设置为
```
sql_mode=NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES

```


安装php相关模块（如php7.1-fpm、php7.1-cli、php7.1-mysql、php-memcached等）并配置相应模块生效
```
sudo apt-get install php7.1-fpm php7.1-cli php7.1-mysql php-memcached
```

### 配置PHP环境

修改`/etc/nginx/site-available/default`文件

```
sudo vim /etc/nginx/site-availabel/default
```

开启对php的支持

```
# Add index.php to the list if you are using PHP
index index.html index.htm index.php index.nginx-debian.html; 

server_name _;

location / {
    # First attempt to serve request as file, then
    # as directory, then fall back to displaying a 404.
    try_files $uri $uri/ =404;
}

# pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
#
location ~ \.php$ {
    include snippets/fastcgi-php.conf;
#
#	# With php7.0-cgi alone:
#	fastcgi_pass 127.0.0.1:9000;
#	# With php7.0-fpm:
    fastcgi_pass unix:/run/php/php7.1-fpm.sock;
}
```

### 安装Xdebug

安装采用源码编译安装的方式（源码共github上下载），参考[xdebug安装][xdebug-install]

编译环境需要安装`php7.1-dev`，请预先安装

```
sudo apt-get install php7.1-dev
```

#### 安装和配置

步骤如下：

```
git clone git://github.com/xdebug/xdebug.git
cd xdebug
phpize
```

phpize命令会生成`configure`文件

```
./configure --enable-xdebug
make
sudo make install
```

配置PHP使用xdebug，找到php的配置文件

```
cd /etc/php/7.1
```

配置`xdebug.ini`文件

```
sudo vim /etc/php/7.1/mods-available/xdebug.ini
```
写入如下内容配置xdebug生效（**注意remote_port为xdebug的远程调试端口，默认9000，不能和服务器的端口80相同，否则无法调试了**）
注：80为`nginx`或`apache2`的默认端口，用户可修改
```
 zend_extension=xdebug.so
 ;xdebug.auto_enable=on
 ;xdebug.default_enable=on
 ;xdebug.auto_profile=on
 ;xdebug.collect_params=on
 ;xdebug.collect_return=on
 ;xdebug.profiler_enable=on
 ;xdebug.remote_host=localhost
 xdebug.remote_port=9000
 xdebug.remote_enable=on
 xdebug.remote_connect_back=on
 ;xdebug.trace_output_dir="/usr/share/php/xdebug/"
 ;xdebug.profiler_output_dir="/usr/share/php/xdebug/"
```

配置`php7.1-fpm`、`cli`、`apache2`（如果安装`apache2`的话）中`xdebug`生效（使用软链接）

```
sudo ln -snf /etc/php/7.1/mods-available/xdebug.ini /etc/php/7.1/fpm/conf.d/20-xdebug.ini
sudo ln -snf /etc/php/7.1/mods-available/xdebug.ini /etc/php/7.1/cli/conf.d/20-xdebug.ini
sudo ln -snf /etc/php/7.1/mods-available/xdebug.ini /etc/php/7.1/apache2/conf.d/20-xdebug.ini
```

重启服务

```
service php7.1-fpm restart
service apache2 restart
```

#### 检测xdebug是否生效

我们有很多方式来确认Xdebug已经正常工作了：

 * 在Terminal执行php -m，在输出结果最后的`Zend Modules`部分，可以看到有Xdebug；
 * 执行php -i | grep xdebug，在输出的结果中，可以看到有xdebug support => enabled；
 * 访问我们之前的`http://localhost/index.php`，可以找到Xdebug的配置（注：80为`nginx`或`apache2`默认端口）

## Idea环境准备

安装`IntelliJ Idea 2016.2`， 并配置，可参考[Apache2.4-php7.1环境配置][apache2-php7-idea]相关部分

### 关于Deployment

在`Deployment->Options`中将`.svn;.cvs;.idea;.DS_Store;.git;.hg;init-config`加入到`Exclude`项中

# 项目配置

## 本机配置

### Nginx配置

参考`init-config/nginx/`下的`nginx`配置项

### 配置hosts

将域名映射到本地

```
127.0.0.1 fang-job.cd121.com		#科地地产job域名
127.0.0.1 fang.cd121.com		    #科地地产PC域名 http://fang.cd121.com
127.0.0.1 fang-source.cd121.com	#静态资源域名
127.0.0.1 fang-admin.cd121.com		#科地地产后台域名
127.0.0.1 fang-mobile.cd121.com	#科地地产APP接口域名
127.0.0.1 fang-fileserver.cd121.com   #科地地产图片系统域名
127.0.0.1 fang.mysql.com        #数据库域名
```

### 初始化数据库

新建`mls`、`mls_hz`两个数据库，分别使用`init-config/sqldata`下的`mls.sql`、`mls_hz.sql`脚本初始化数据库

### 配置数据库连接和Memcached

`applications/mls/config`目录下是`mls`的配置（`mls_admin`、`mls_api`等类同）

`development`目录对应了开发本地的配置，修改`database.php`和`memcached.php`中的`ip`、`端口`、`用户名`、`密码`等

### 配置Minify

项目使用`Minify`压缩合并`js`和`css`文件，`source/min`目录下为`Minify`的工作目录，`Minify`在`source/min/config.php`和`source/min/groupsConfig.php`中配置。

`source/cache`为`Minify`压缩后的缓存目录，这里需要注意，**确保当前服务器的用户具有对该目录的写权限**，请参看[`source/cache/doNotDelete.md`][source-cache-doNotDelete]文件

### 其他配置（待完成）

- 短信通道
- IM
- 友盟推送
- 图片系统

## 登录

### 科地地产PC（经纪人端）

>```
>地址：http://fang.cd121.com/login/index/1
>用户名：15951634202
>密码：abcd123!
>```

### 科地地产后台管理

>```
>地址：http://fang-admin.cd121.com
>超级管理员：admin
>密码：doucare
>
>普通管理员：ceshiyuan
>密码：doucare
>```


## 说明

php配置需要注意的问题
本项目依赖php的模块
- php-pecl-memcached # memcache相关
- php-mysql     # mysql数据库支持
- php-mbstring  # 字符串处理相关
- php-gb  # 图片压缩相关
- libcurl # curl库
- php-curl # curl模块
请务必确保安装

### MySQL数据库连接

mysql数据库编码使用`utf-8`，请确保，否则会出现乱码
mysql连接使用fang.mysql.com域名，请在hosts中配置域名映射到相应IP地址

### 打包发布

使用tar命令打包文件，排除调不需要打包的文件

```
cd $WORKSPACE
tar zcvf fkt.tar.gz fkt/* -X ./fkt/exclude.md
```

### 上传的文件

上传的文件统一存放在index.php中指定的/uploads目录中

```
define('UPLOADS', str_replace("\\", "/", dirname(DOCROOT) . "/uploads"));
```

### 短信通道配置

#### 短信API通道

短信通道使用`科地短信平台API`

短信通道类为`fkt > applications > libraries > Sms_codi.php`

短信通道的配置项在`fkt > applications > mls > config > development > config.php`中，客户400电话也在此配置
```

//客户电话
$config['tel400'] = '400-018-5180';

// 短信服务 验证码 通知 广告
$config['sms_url_yzm'] = SMS_URL;
$config['sms_url_notice'] = SMS_URL;
$config['sms_url_ad'] = SMS_URL;
```

#### 短信模板

短信模板根据`科地短信平台API`要求，以如下格式存在，`科地短信平台API`只要求使用`code`进行接口调用，`template`内容实际上需要在`科地短信平台设置`，在`Sms_codi.php`中只作为代码完整性展示使用，便于查看

```
  //短信模板
  private $_module = array(
    '1' => array(
      'register' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。' //经纪人注册
      ),
      'findpw' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。' //找回密码
      ),
      'modify_phone' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。' //修改手机号
      ),
      'check_for_updates' => array(
        'code' => 'SMS_004',
        'template' => '亲，采集中断个数：${num}' //检测采集房源
      )
    ),
    '2' => array(
      'cooperate_lol_pass' => array(
        'code' => 'SMS_005',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})已经审核通过！'
      ),
      'cooperate_lol_fail' => array(
        'code' => 'SMS_006',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})没有审核通过，请您按照活动规则重新提交相应资料。'
      ),
      'cooperate_activity_pass' => array(
        'code' => 'SMS_007',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})已经通过${type}${score}。'
      ),
      'cooperate_activity_fail' => array(
        'code' => 'SMS_008',
        'template' => '您好！您的合作成交资料(合同编号:${order_sn})没有通过${type}，请您按照活动规则重新提交相应资料。'
      ),
      'send_accepet_message_to_broker_a' => array(
        'code' => 'SMS_009',
        'template' => '您有新的合作申请尚未处理，请及时处理以免逾期。'
      ),
      'first_login' => array(
        'code' => 'SMS_010',
        'template' => '欢迎您使用${platform}，进入个人中心提交认证资料，有房源、客源、合作等更多功能！详询：${tel400}。'
      ),
      'auth_review_pass' => array(
        'code' => 'SMS_011',
        'template' => '恭喜您已成为${platform}认证经纪人，赶快登录${platform}使用吧。如有问题，请咨询：${tel400}。' //无人站经纪人审核通过发送短信
      ),
      'auth_review_fail' => array(
        'code' => 'SMS_012',
        'template' => '${name}您好，您的认证信息未通过审核，请登录${platform}重新提交。如有问题，请咨询：${tel400}。' //无人站经纪人审核通过发送短信
      )
    ),
    '3' => array(
      'rent_finance' => array(
        'code' => 'SMS_003',
        'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。'//租房分期
      )
    )
  );

```

## 生产环境配置

测试环境 生产环境 均采用jenkins部署

jenkins部署生产环境时，需要将环境变量开关切换为production，同时，如果需要切换短信通道，域名，

```

#sed -i "s#define('PROTOCOL', 'http://');#define('PROTOCOL', 'https://');#g" index.php

#sed -i "s#define('MLS_TEL_400', '400-018-5180');#define('MLS_TEL_400', '400-018-5180');#g" index.php

#sed -i "s#define('SMS_URL', 'http://118.178.230.141:8888/mc-receiver-web/message/sendsms');#define('SMS_URL', 'http://118.178.230.141:8888/mc-receiver-web/message/sendsms');#g" index.php

sed -i "s#define('SMS_SEND', false);#define('SMS_SEND', true);#g" index.php

sed -i "s#define('ENVIRONMENT', 'development');#define('ENVIRONMENT', 'production');#g" index.php

sed -i "s#define('MLS_NAME', 'fang.cd121.com');#define('MLS_NAME', 'house.cd121.com');#g" index.php

sed -i "s#define('MLS_ADMIN_NAME', 'fang-admin.cd121.com');#define('MLS_ADMIN_NAME', 'house-admin.cd121.com');#g" index.php

sed -i "s#define('MLS_SIGN_NAME', 'fang-sign.cd121.com');#define('MLS_SIGN_NAME', 'house-sign.cd121.com');#g" index.php

sed -i "s#define('MLS_API_NAME', 'fang-api.cd121.com');#define('MLS_API_NAME', 'house-api.cd121.com');#g" index.php

sed -i "s#define('MLS_JOB_NAME', 'fang-job.cd121.com');#define('MLS_JOB_NAME', 'house-job.cd121.com');#g" index.php

sed -i "s#define('MLS_MOBILE_NAME', 'fang-mobile.cd121.com');#define('MLS_MOBILE_NAME', 'house-mobile.cd121.com');#g" index.php

sed -i "s#define('MLS_SOURCE_NAME', 'fang-source.cd121.com');#define('MLS_SOURCE_NAME', 'house-source.cd121.com');#g" index.php

sed -i "s#define('MLS_FILE_SERVER_NAME', 'fang-fileserver.cd121.com');#define('MLS_FILE_SERVER_NAME', 'house-fileserver.cd121.com');#g" index.php

sed -i "s#define('MLS_FINANCE_NAME', 'fang-finance-api.cd121.com');#define('MLS_FINANCE_NAME', 'house-finance-api.cd121.com');#g" index.php
```
`s`后的`/`和`#`表示分隔符，可根据需求使用

```
define('SMS_SEND', false);

define('MLS_TEL_400', '400-018-5180');

define('SMS_URL', 'http://118.178.230.141:8888/mc-receiver-web/message/sendsms');

define('PROTOCOL', 'http://');

//经纪人平台 PC端URL
define('MLS_NAME', 'fang.cd121.com');

//管理平台 PC端URL
define('MLS_ADMIN_NAME', 'fang-admin.cd121.com');

//签约平台 PC端URL
define('MLS_SIGN_NAME', 'fang-sign.cd121.com');

//对外接口API URL
define('MLS_API_NAME', 'fang-api.cd121.com');

//JOBAPI URL
define('MLS_JOB_NAME', 'fang-job.cd121.com');

//移动端API URL
define('MLS_MOBILE_NAME', 'fang-mobile.cd121.com');

//静态资源URL
define('MLS_SOURCE_NAME', 'fang-source.cd121.com');

//文件上传URL
define('MLS_FILE_SERVER_NAME', 'fang-fileserver.cd121.com');

//金融端URL
define('MLS_FINANCE_NAME', 'fang-finance-api.cd121.com');

```

生产环境部署时 执行`bin/prod.sh`脚本，域名等请在`bin/production`文件夹中修改

- `sed.sh`： 根据`模板`将`development`中的配置替换，生成并放到`production`下，慎用
- `dev.sh`： 将`development`文件夹中的配置复制到工程，生效
- `prod.sh`： 将`production`文件夹中的配置复制到工程，生效

ps: `sed`命令替换，需要保证格式匹配，替换前后请检查

# 备注JOB（待完成）

linux中配置crontab

```
crontab -e          # 编辑当前用户的crontab文件
crontab -u alphabeta -e   # 编辑制定用户的crontab文件
crontab -l          # 查看当前用户的crontab配置
service cron restart     # 重启cron服务
service cron status      # 查看cron服务状态
```

测试crontab任务可使用mail命令发送邮件测试

## 安装mail

```
# 安装mail
sudo apt-get install heirloom-mailx
```

## 配置mail

```
vi /etc/nail.rc

//此时如果打印没有权限则使用sudo命令，并且在有些版本下是s-nail.rc文件
//在nail.rc文件末尾添加以下两行代码

set from=user@163.com smtp=smtp.163.com
//此处以163邮箱举例，也可以使用qq邮箱，此时smtp=smtp.exmail.qq.com
//其他企业邮箱以自己公司邮箱服务器为准
set smtp-auth-user="邮箱名" smtp-auth-password="邮箱密码" smtp-auth=login

```

例如

```
set from=dqmmpb@163.com smtp=smtp.163.com
set smtp-auth-user="dqmmpb@163.com" smtp-auth-password="dqmmpb" smtp-auth=login
```

测试mail邮件

```
方法1:交互式邮件发送：
    mail "收件人邮箱"
    填写主题
    填写内容
    ctrl + d 结束输入
    cc代表抄送
    回车完成发送
方法2: 通道发送：
    echo "内容" | mail -s "主题" "收件人邮箱"
方法3:读取文件法：
    mail -s "主题" "收件人邮箱" < "文件名"
```

例如

```
方法1:交互式邮件发送：
    mail dengqiming@cd121.com
    hello world
    this is a test
    ctrl + d 结束输入
    cc代表抄送
    回车完成发送
方法2: 通道发送：
    echo "this is a test" | mail -s "hello world" dengqiming@cd121.com
方法3:读取文件法：
    mail -s "hello world" dengqiming@cd121.com < /home/alphabeta/mail/maildata
    
# maildata文件内容为
this is a test
```

## 配置发送邮件的crontab任务

```
crontab -e
# 编辑定时任务，添加如下内容
*/1 * * * * mail -s "hello" dengqiming@cd121.com < /home/alphabeta/mail/maildata
```

## crontab定时任务配置说明

```
crontab文件的格式：m h  dom mon dow   command  
m: 分钟（0-59）。   
h: 小时（0-23）。   
dom: 天（1-31）。 （day of month）
mon: 月（1-12）。   
dow: 一星期内的天（0~6，0为星期天）。（day of week）   
cmd: 要运行的程序，程序被送入sh执行，这个shell只有USER,HOME,SHELL这三个环境变量
```

### 配置示例

```
30 21 * * * /usr/local/etc/rc.d/lighttpd restart #每晚的21:30重启apache。   
45 4 1,10,22 * * /usr/local/etc/rc.d/lighttpd restart #每月1、10、22日的4 : 45重启apache。   
10 1 * * 6,0 /usr/local/etc/rc.d/lighttpd restart #每周六、周日的1 : 10重启apache。   
0,30 18-23 * * * /usr/local/etc/rc.d/lighttpd restart #每天18 : 00至23 : 00之间每隔30分钟重启apache。   
0 23 * * 6 /usr/local/etc/rc.d/lighttpd restart #每星期六的11 : 00 pm重启apache。   
0 */1 * * * /usr/local/etc/rc.d/lighttpd restart #每一小时重启apache   
0 23-7/1 * * * /usr/local/etc/rc.d/lighttpd restart #晚上11点到早上7点之间，每隔一小时重启apache   
0 11 4 * mon-wed /usr/local/etc/rc.d/lighttpd restart #每月的4号与每周一到周三的11点重启apache   
0 4 1 jan * /usr/local/etc/rc.d/lighttpd restart #一月一号的4点重启apache
```


## 房产项目定时任务

这里为项目的JOB（待完成）

```
JOB crontab 
###########################mls_stat_job##############################
1 1 * * * /job/mls_stat_job/collect_stat.sh > /dev/null 2>&1
3 1 * * * /job/mls_stat_job/collect_view_stat.sh > /dev/null 2>&1
6 1 * * * /job/mls_stat_job/group_publish_stat.sh > /dev/null 2>&1
9 1 * * * /job/mls_stat_job/login_stat.sh > /dev/null 2>&1
12 1 * * * /job/mls_stat_job/publish_stat.sh > /dev/null 2>&1
15 1 * * * /job/mls_stat_job/broker_app_count_stat.sh > /dev/null 2>&1
17 1 * * * /job/mls_stat_job/broker_stat.sh > /dev/null 2>&1
25 1 * * * /job/mls_stat_job/dist_stat.sh > /dev/null 2>&1

####################################################################
30 1 * * * /job/mls_stat_job/broker_operate_stat.sh > /dev/null 2>&1
*/1 2-9 * * * /job/mls_stat_job/broker_operate_stat_2.sh > /dev/null 2>&1
*/1 2-9 * * * sleep 1; /job/mls_stat_job/broker_operate_stat_2.sh > /dev/null 2>&1
*/1 2-9 * * * sleep 2; /job/mls_stat_job/broker_operate_stat_2.sh > /dev/null 2>&1
*/1 2-9 * * * sleep 3; /job/mls_stat_job/broker_operate_stat_2.sh > /dev/null 2>&1
*/1 2-9 * * * slepp 4; /job/mls_stat_job/broker_operate_stat_2.sh > /dev/null 2>&1
*/5 8,9 * * * /job/mls_stat_job/broker_operate_stat_check.sh > /dev/null 2>&1
1 10 * * * /job/mls_stat_job/broker_operate_stat_3.sh > /dev/null 2>&1
*/1 14,15 * * * /job/mls_stat_job/broker_operate_stat_2.sh > /dev/null 2>&1
*/1 14,15 * * * sleep 2; job/mls_stat_job/broker_operate_stat_2.sh > /dev/null 2>&1

#######################mls_push_job###############################
1 9,20 * * * /job/mls_push_job/new_add_collect.sh > /dev/null 2>&1
*/5 * * * * /job/mls_push_job/new_add_coop_house.sh > /dev/null 2>&1
0 9 * * * /job/mls_push_job/event_remind.sh > /dev/null 2>&1

#######################check_my_task###################################
* */1 * * * /job/mls_task_job/check_is_near_overdate.sh > /dev/null 2>&1
25 5 * * * /job/mls_task_job/check_is_over_date.sh > /dev/null 2>&1

######################mls_cooperate_job###################################
1 5 * * * /job/mls_cooperate_job/send_accepet_message_to_broker_a.sh > /dev/null 2>&1
5 5 * * * /job/mls_cooperate_job/send_confirm_commission_message_to_broker_b.sh > /dev/null 2>&1
10 5 * * * /job/mls_cooperate_job/update_cooperate_to_failed_unaccepet.sh > /dev/null 2>&1
15 5 * * * /job/mls_cooperate_job/update_cooperate_to_failed_uncofirm.sh > /dev/null 2>&1
20 5 * * * /job/mls_cooperate_job/update_cooperate_to_overdue_unsub.sh > /dev/null 2>&1
#1 */1 * * * /job/curl_broker_info.sh > /dev/null 2>&1

######################mls_cityprice_job###################################
* * 1 * * /job/mls_cityprice_job/cityprice.sh > /dev/null 2>&1     # 房价走势图

######################mls_monitor#########################################
1 9-20 * * * /job/mls_monitor/collect_monitor.sh > /dev/null 2>&1

######################public turn private house#########################################
1 2 * * * /job/mls_pubic_turn_private/sell_house.sh > /dev/null 2>&1
5 2 * * * /job/mls_pubic_turn_private/rent_house.sh > /dev/null 2>&1
10 2 * * * /job/mls_pubic_turn_private/buy_customer.sh > /dev/null 2>&1
15 2 * * * /job/mls_pubic_turn_private/rent_customer.sh > /dev/null 2>&1

######################mls_group_refresh#########################################
*/1 * * * * /job/mls_group_refresh/group_refresh.sh > /dev/null 2>&1

######################cooperate_friends_apply#########################################
1 3 * * * /job/cooperate_friends_apply/cooperate_friends_apply.sh > /dev/null 2>&1

######################cooperate_friends_apply#########################################
10 1 * * * /job/mls_data_public_job/data_public_change.sh > /dev/null 2>&1
```

[xdebug-install]: https://xdebug.org/docs/install
[apache2-php7-idea]: https://gitlab.codiruifu.com/realestate/doc/blob/master/src/spec/php-development-ubuntu.md
[source-cache-doNotDelete]: source/cache/doNotDelete.md


