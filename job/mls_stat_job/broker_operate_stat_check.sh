#!/bin/sh
check=$(cat /job/mls_stat_job/broker_operate_stat.lock)
if [ "$check" == "1" ]; then
    /usr/bin/wget -T 86400 -t 1 ${MLS_JOB_NAME}/stat_broker/new_stat_2/?check=1
    check=$(cat index.html?check=1)
    if [ "$check" == "0" ];then
        echo 0 > /job/mls_stat_job/broker_operate_stat.lock
    fi
    rm -f index.html?check=1*
fi
