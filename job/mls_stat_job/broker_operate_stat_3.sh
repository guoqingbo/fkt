#!/bin/sh
check=$(cat /job/mls_stat_job/broker_operate_stat.lock)
if [ "$check" == "0" ]; then
	/usr/bin/wget -T 86400 -t 1 ${MLS_JOB_NAME}/stat_broker/new_stat_3/
	rm -f index.html*
fi
