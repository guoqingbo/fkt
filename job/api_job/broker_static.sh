#!/bin/bash
source /var/www/fkt/job/common/config.sh
/usr/bin/wget ${API_NAME}/index/crontab/broker --output-document=/dev/null >> /dev/null 2>&1