#!/bin/sh
echo 1 > /job/mls_stat_job/broker_operate_stat.lock 
source /var/www/fkt/job/common/func_wget_city.sh
func_wget_city ${MLS_JOB_NAME}/stat_broker/new_stat_1/
