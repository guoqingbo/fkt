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

## 替换job配置文件
cp "$DEV_DIR"/config.sh "$PROJECTDIR"/job/common/config.sh

## 替换文件index.php，crossdomain.xml
cp "$DEV_DIR"/index.php "$PROJECTDIR"/index.php
cp "$DEV_DIR"/crossdomain.xml "$PROJECTDIR"/crossdomain.xml
