#!/bin/sh
check=$(cat /job/mls_stat_job/broker_operate_stat.lock)
if [ "$check" == "1" ]; then
    source /var/www/fkt/job/common/func_wget_loop.sh
    func_wget_loop_m ${MLS_JOB_NAME}/stat_broker/new_stat_2/ 1
fi
