
# 开发

## 开发环境

- 操作系统： `Windows` 
- 开发环境： `Nginx` + `MySQL5.0` + `PHP5.6` + `Memcached`+图片系统
- 开发工具： `phpstorm
- 调试工具： `Chrome` + `Xdebug`
- 数据库工具： `Navicat Premium`  `phpmyadmin`

## windows下安装Nginx、MySQL、PHP环境

### 下载压缩包

    1. Nginx
    
    2. Mysql
    
    3. PHP

### 安装ngix

1. 把ngix解压到你要安装nginx的目录下，我是放在d:\mywnmp ,

2. 启动ngix ,双击ngix.exe,或命令行 start nginx (停止nginx -s stop , 重启nginx -s reload， 退出 nginx -s quit)

3. 配置ngix .....

4. 设置nginx php 启动 停止
   
   下载 RunHiddenConsole
   
   创建start_nginx.bat文件
  
   ``````
   @echo off
   REM Windows 下无效
   REM set PHP_FCGI_CHILDREN=5
   
   REM 每个进程处理的最大请求数，或设置为 Windows 环境变量
   set PHP_FCGI_MAX_REQUESTS=1000
    
   echo Starting PHP FastCGI...
   RunHiddenConsole E:/2015/wnmp/php/php-cgi.exe -b 127.0.0.1:9000 -c E:/2015/wnmp/php/php.ini
    
   echo Starting nginx...
   RunHiddenConsole E:/2015/wnmp/nginx/nginx.exe -p E:/2015/wnmp/nginx
   ```
创建stop_nginx.bat脚本，对应的是用来关闭nginx服务

   ```
   @echo off
      echo Stopping nginx...  
      taskkill /F /IM nginx.exe > nul
      echo Stopping PHP FastCGI...
      taskkill /F /IM php-cgi.exe > nul
      exit
   ```
   
   
安装php相关模块（如php7.1-fpm、php7.1-cli、php7.1-mysql、php-memcached等）并配置相应模块生效
```
sudo apt-get install php7.1-fpm php7.1-cli php7.1-mysql php-memcached
```

### 配置PHP环境


### 安装Xdebug


### 初始化数据库



### 配置数据库连接和Memcached


### 配置Minify

项目使用`Minify`压缩合并`js`和`css`文件，`source/min`目录下为`Minify`的工作目录，`Minify`在`source/min/config.php`和`source/min/groupsConfig.php`中配置。

`source/cache`为`Minify`压缩后的缓存目录，这里需要注意，**确保当前服务器的用户具有对该目录的写权限**，请参看[`source/cache/doNotDelete.md`][source-cache-doNotDelete]文件



### 问题总结

 1. mls中config添加数据    
    
 2. 删除运营左侧菜单节点，purview_node
 
 3. 
     
    
    
### mysql远程连接 
  
1. 使用“mysql -uroot -proot”命令可以连接到本地的mysql服务。

2. 使用“use mysql”命令，选择要使用的数据库，修改远程连接的基本信息，保存在mysql数据库中，因此使用mysql数据库。

3. 使用“GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION;”命令可以更改远程连接的设置

4. 使用“flush privileges;”命令刷新刚才修改的权限，使其生效。

5.使用“select host,user from user;”查看修改是否成功。


## 其他

[wnmp环境搭建]: http://www.cnblogs.com/Li-Cheng/p/4399149.html