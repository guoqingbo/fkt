#!/bin/sh

## 生产环境部署脚本
## 需要将域名等替换成生产环境

# resolve links - $0 may be a softlink
PRG="$0"

while [ -h "$PRG" ] ; do
  ls=`ls -ld "$PRG"`
  link=`expr "$ls" : '.*-> \(.*\)$'`
  if expr "$link" : '/.*' > /dev/null; then
    PRG="$link"
  else
    PRG=`dirname "$PRG"`/"$link"
  fi
done

PRGDIR=`dirname "$PRG"`

PROJECTDIR=`cd "$PRGDIR/.." >/dev/null; pwd`

DEV_DIR="$PROJECTDIR"/bin/development

PROD_DIR="$PROJECTDIR"/bin/production

#mkdir "$PROD_DIR"

## 备份文件index.php
cp "$DEV_DIR"/index.php "$PROD_DIR"/index.php

#sed -i "s#define('PROTOCOL', 'http://');#define('PROTOCOL', 'https://');#g" "$PROD_DIR"/index.php
#sed -i "s#define('MLS_TEL_400', '400-018-5180');#define('MLS_TEL_400', '400-018-5180');#g" "$PROD_DIR"/index.php
#sed -i "s#define('SMS_URL', 'http://118.178.230.141:8888/mc-receiver-web/message/sendsms');#define('SMS_URL', 'http://118.178.230.141:8888/mc-receiver-web/message/sendsms');#g" "$PROD_DIR"/index.php
sed -i "s#define('SMS_SEND', false);#define('SMS_SEND', true);#g" "$PROD_DIR"/index.php
sed -i "s#define('ENVIRONMENT', 'development');#define('ENVIRONMENT', 'production');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_NAME', 'fang.cd121.com');#define('MLS_NAME', 'house.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_ADMIN_NAME', 'fang-admin.cd121.com');#define('MLS_ADMIN_NAME', 'house-admin.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_SIGN_NAME', 'fang-sign.cd121.com');#define('MLS_SIGN_NAME', 'house-sign.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_API_NAME', 'fang-api.cd121.com');#define('MLS_API_NAME', 'house-api.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_JOB_NAME', 'fang-job.cd121.com');#define('MLS_JOB_NAME', 'house-job.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_MOBILE_NAME', 'fang-mobile.cd121.com');#define('MLS_MOBILE_NAME', 'house-mobile.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_SOURCE_NAME', 'fang-source.cd121.com');#define('MLS_SOURCE_NAME', 'house-source.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_FILE_SERVER_NAME', 'fang-fileserver.cd121.com');#define('MLS_FILE_SERVER_NAME', 'house-fileserver.cd121.com');#g" "$PROD_DIR"/index.php
sed -i "s#define('MLS_FINANCE_NAME', 'fang-finance-api.cd121.com');#define('MLS_FINANCE_NAME', 'house-finance-api.cd121.com');#g" "$PROD_DIR"/index.php

## 备份文件crossdomain.xml
cp "$DEV_DIR"/crossdomain.xml "$PROD_DIR"/crossdomain.xml

sed -i "s#<allow-access-from domain=\"fang.cd121.com\"/>#<allow-access-from domain=\"house.cd121.com\"/>#g" "$PROD_DIR"/crossdomain.xml
sed -i "s#<allow-access-from domain=\"fang-source.cd121.com\"/>#<allow-access-from domain=\"house-source.cd121.com\"/>#g" "$PROD_DIR"/crossdomain.xml
sed -i "s#<allow-access-from domain=\"fang-admin.cd121.com\"/>#<allow-access-from domain=\"house-admin.cd121.com\"/>#g" "$PROD_DIR"/crossdomain.xml
sed -i "s#<allow-access-from domain=\"fang-job.cd121.com\"/>#<allow-access-from domain=\"house-job.cd121.com\"/>#g" "$PROD_DIR"/crossdomain.xml
