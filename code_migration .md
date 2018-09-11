
# 测试环境代码迁移（由内网迁移至外网）

##外网环境(测试环境)
- ip： 118.178.229.226(公)  
       10.27.236.37（内） 
- 用户密码： root/Codi!23
- nginx： /usr/local/nginx
- php： /usr/local/php5416 (memchached扩展，若缺少模块根据提示安装模块)
- memchached:/usr/local/memchached
- 安装包 /root
- 数据库 ：ip：121.40.187.122， port：3307， user：llq_app_test，password：Codi1234
- 项目部署：/var/www/fkt
- 代理服务器 ip:120.55.85.150 /codi/nginx
- CI项目模块日志 /var/www/logs

##外网环境（生产环境）
- ip： 121.199.4.127 (公)  
       10.132.4.88（内） 
- 用户密码： root/Codi0818
- nginx： /usr/local/nginx
- php： /usr/local/php5416
- memchached:memchached (memchached扩展，若缺少模块根据提示安装模块)
- 安装包 /root
- 数据库 ：ip：10.25.9.37， port：3306， user：mlsprd，password：MlsO607!23Sql（跳板机：ip:120.55.85.150 root/Codi!23）
- 项目部署：/var/www/fkt
- 代理服务器 ip:114.55.244.61 /codi/nginx
- CI项目模块日志 /var/www/logs

###负载均衡（生产环境）
- ip：118.178.231.74(公) 
       10.27.235.102(内)
- 用户密码 ：root/Codi$234#
- nginx： /usr/local/nginx
- php： /usr/local/php
- memchached:memchached (memchached扩展，若缺少模块根据提示安装模块)
- 项目部署：/var/www/fkt
- CI项目模块日志 /var/www/logs

###SLB负载均衡（生产环境）
- 域名解析lsb地址：47.97.93.7（house-fileserver.cd121.com 文件服务器地址不变：121.199.4.127）
- upload文件同步:/opt/scripts/inotify_bak.sh 
- slb修改:添加两台主机prd1：10.132.4.88、prd2：10.27.235.102

###RDS数据库
10.142.62.175  rm-bp1yh73uj0279x6dj.mysql.rds.aliyuncs.com
user mlsprd
password MlsO607!23Sql
跳板机 114.55.244.155 admin  r6#K21WJ!K
 
##短信接口
- 接口： http://118.178.230.141:8888/mc-receiver-web/message/sendsms  
- 参数：'templateCode',短信模板编号（SMS_XXX）
         'mobile',手机号
         'content',模板内参数替换，json数据
         'requestIp'请求端ip
- 模板（例） ：
      'code' => 'SMS_003',
      'template' => '尊敬的用户，您操作的验证码为${validcode}，请在收到后3分钟内提交。
      
## php+nginx环境安装
这里首先安装系统常用的支持库
```$xslt
 yum install -y gcc gdb strace gcc-c++ autoconf libjpeg libjpeg-devel libpng libpng-devel freetype freetype-devel libxml2 libxml2-devel zlib zlib-devel glibc glibc-devel glib2 glib2-devel bzip2 bzip2-devel ncurses ncurses-devel curl curl-devel e2fsprogs patch e2fsprogs-devel krb5-devel libidn libidn-devel openldap-devel nss_ldap openldap-clients openldap-servers libevent-devel libevent uuid-devel uuid mysql-devel
```

### 下载压缩包

    1. Nginx http://nginx.org/download/nginx-1.11.9.tar.gz
    
    2. php http://museum.php.net/php5/php-5.4.16.tar.gz
    
    3.pcre ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/pcre2-10.21.tar.gz
     
    4.memchached http://memcached.googlecode.com/files/memcached-1.4.15.tar.gz  
    
    5.libevent http://www.monkey.org/~provos/libevent-1.4.12-stable.tar.gz     

    6.php扩展memchached http://pecl.php.net/get/memcached-2.2.0.tgz

### 安装ngix
nginx支持rewrite需要安装这个库
```$xslt cd pcre-2-10.21
 ./configure
make
make install

```
创建用户nginx使用的www用户
```$xslt
groupadd www
useradd -g www www
```
编译安装nginx

```$xslt
cd nginx-1.5.0
./configure --user=www --group=www --prefix=/usr/local/nginx --with-http_stub_status_module --with-http_ssl_module --with-http_realip_module
make
make install
```
配置：
```$xslt
上传配置文件vhost  nginx.conf
user  www  www;          #首行user去掉注释,修改Nginx运行组为www www；必须与/usr/local/php5/etc/php-fpm.conf中的user,group配置相同，否则php运行出错    
```
其他命令
 ```
 
        检查是否安装gcc rpm -qa | grep gcc
        查询nginx主进程号 ps -ef | grep nginx
        #停止进程   kill -QUIT 主进程号 
        #快速停止   kill -TERM 主进程号 
        #强制停止  pkill -9 nginx
        测试端口 netstat -na | grep 80
        检查nginx配置文件 /usr/local/nginx/sbin/nginx -t -c"配置文件"
        启动nginx /usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf
         重启nginx  /usr/local/nginx/sbin/nginx -s reload -c /usr/local/nginx/conf/nginx.conf
```

  
  

### 安装php
php安装需要编译，所以服务器应该保证gcc和g++环境的安装
```tar -xvzf php-5.4.16.tar.gz
   cd php-5.4.16
   ./configure --prefix=/usr/local/php5416 --with-curl --with-freetype-dir --with-gd --with-gettext --with-iconv-dir --with-kerberos --with-libdir=lib64 --with-libxml-dir --with-mysqli --with-openssl --with-pcre-regex --with-pdo-mysql --with-pdo-sqlite --with-pear --with-png-dir --with-jpeg-dir --with-xmlrpc --with-xsl --with-zlib --with-bz2 --with-mhash --enable-fpm --enable-bcmath --enable-libxml --enable-inline-optimization --enable-gd-native-ttf --enable-mbregex --enable-mbstring --enable-opcache --enable-pcntl --enable-shmop --enable-soap --enable-sockets --enable-sysvsem --enable-sysvshm --enable-xml --enable-zip
    make
    make install
```
php的默认安装位置上面已经指定为/usr/local/php5416，接下来配置相应的文件：
```
cp /root/php-5.4.16/php.ini-development /usr/local/php5416/lib/php.ini
cp /usr/local/php5416/etc/php-fpm.conf.default /usr/local/php5416/etc/php-fpm.conf
cp /root/php-5.4.16/sapi/fpm/php-fpm /usr/local/bin
```
设置php.ini，使用： vim /usr/local/php5416/lib/php.ini 打开php配置文件找到cgi.fix_pathinfo配置项，这一项默认被注释并且值为1，根据官方文档的说明，这里为了当文件不存在时，阻止Nginx将请求发送到后端的PHP-FPM模块，从而避免恶意脚本注入的攻击，所以此项应该去掉注释并设置为0
另外注意一个地方就是php.ini配置文件的位置可以在编译前配置参数中设置，编译参数可以写成：--with-config-file-path=/usr/local/php 这样的话php就回去指定的目录下读取php.ini配置文件，如果不加这个参数默认位置就是php安装目录下的lib目录，具体也可以在phpinfo()输出界面查看，如果php.ini放到其他位置，php读取不到，那么所有的配置修改后都是不生效的，这点要注意
创建web用户
``groupadd www
  useradd -g www www
修改/usr/local/php5416/etc/php-fpm.conf添加以上创建的用户和组(与nginx.conf user保持一致)
``启动php-fpm服务：
```$xslt
/usr/local/bin/php-fpm -c /usr/local/php5416/lib/php.ini
```
``关闭php-fpm服务：
```$xslt
pkill php-fpm
```
### 安装配置memchached
方法1：
memcached 依赖于libevent 库,因此我们需要先安装libevent.
```$xslt
cd libevent-1.2/  
./configure -prefix=/usr/local
make  
make install  
cd /root/memcached-1.2.0/  
./configure -with-libevent=/usr/local/libevent/ -prefix=/usr/local/memcached  
make  
make install  
/usr/local/memcached/bin/memcached -d -m 10m -p 11211 -u root  
启动参数介绍如下：和上面的命令不对应

-d选项是启动一个守护进程，

-m是分配给Memcache使用的内存数量，单位是MB，这里是10MB，

-u是运行Memcache的用户，这里是root，

-l是监听的服务器IP地址，如果有多个地址的话，这里指定了服务器的IP地址192.168.0.200，

-p是设置Memcache监听的端口，这里设置了12000，最好是1024以上的端口，

-c选项是最大运行的并发连接数，默认是1024，这里设置了256，按照服务器的负载量来设定，

-P是设置保存Memcache的pid文件，我这里是保存在 /tmp/memcached.pid，也可以启动多个守护进程，不过端口不能重复。

```
方法2：
```$xslt
 yum -y install memcached
 /usr/bin/memcached -l 127.0.0.1 -p 11211 -m 150 -u root
```
安装php-fpm的memcached扩展
```$xslt
 tar zxvf memcache-2.2.7.tgz
 cd memcache-2.2.7
 /usr/local/php/bin/phpize
 ./configure –enable-memcache --with-php-config=/usr/local/php/bin/php-config --with-zlib-dir
  make && make install
  --with-php-config 指定 php-config，该文件与 phpize 所在目录相同， 
  
  --with-libmemcached-dir 指定 libmemcached 安装目录，就刚才我们 --prefix 那个目录 ,
  
  --disable-memcached-sasl 说明我们系统不支持sasl.h
  
  如果安装成功，会提示：Installing shared extension:/usr/local/php/lib/extensions/no-debug-non-zts-20160524/ 等类信息
/usr/local/php5416/lib/php/extensions/no-debug-non-zts-20100525/

/usr/local/php/lib/php/extensions/no-debug-non-zts-20100525/

```
编辑php配置文件php.ini
```$xslt
extension_dir = " /usr/local/php/lib/php/extensions/no-debug-non-zts-20100525/ "
extension=memcached.so
```

### 配置Minify

项目使用`Minify`压缩合并`js`和`css`文件，`source/min`目录下为`Minify`的工作目录，`Minify`在`source/min/config.php`和`source/min/groupsConfig.php`中配置。

`source/cache`为`Minify`压缩后的缓存目录，这里需要注意，**确保当前服务器的用户具有对该目录的写权限**，请参看[`source/cache/doNotDelete.md`][source-cache-doNotDelete]文件
cached一定要设置权限可读写
chmod o+w cache

### hosts配置（不必须）
房产应用都是通过域名解析

### 问题总结
phph或nginx配置文件改变注意重启及设置-c参数

 
     
    
    
### mysql远程连接 
  
1. 使用“mysql -uroot -proot”命令可以连接到本地的mysql服务。

2. 使用“use mysql”命令，选择要使用的数据库，修改远程连接的基本信息，保存在mysql数据库中，因此使用mysql数据库。

3. 使用“GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION;”命令可以更改远程连接的设置

4. 使用“flush privileges;”命令刷新刚才修改的权限，使其生效。

5.使用“select host,user from user;”查看修改是否成功。


## 其他

[lnp环境搭建1]: http://www.linuxidc.com/Linux/2016-08/134110.htm
[lnp环境搭建2]:http://www.cnblogs.com/freeweb/p/5425554.html
[memcached配置1]: http://www.cnblogs.com/flywind/p/6021568.html
[memcached配置2]:http://blog.csdn.net/woshihaiyong168/article/details/54288708
[memcached配置3]:http://blog.csdn.net/mevicky/article/details/49717765
[memcached配置4]:http://www.linuxidc.com/Linux/2015-05/117170.htm