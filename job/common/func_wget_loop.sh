#!/bin/bash
source /var/www/fkt/job/common/config.sh
#执行固定次数
function  func_wget_loop_n()
{
    if [ "$2" != "" ]; then
       num=$2
    else
       num=1
    fi

    if [ "$3" != "" ]; then
       rmfile=$3
    else
       rmfile='index.html*'
    fi

    if [ "$1" != "" ]; then
        for (( i = 0; i < num; i=(i+1) )); do  
            /usr/bin/wget -T 86400 -t 1 $1
	    rm -f $rmfile
            #echo `date '+%Y-%m-%d %H:%M:%S'` >> /job/common/testn.log
            sleep 1
        done
    fi
}

#一分钟内循环执行
function  func_wget_loop_m()
{
    if [ "$2" != "" ]; then
       sleeptime=$2
    else
       sleeptime=1
    fi

    if [ "$3" != "" ]; then
       rmfile=$3
    else
       rmfile='index.html*'
    fi

    if [ "$1" != "" ]; then

	dom=$(date +%M) #当前SH执行的分钟数

        while [ true ]; do
	    nowm=$(date +%M); #循环内执行的分钟数
            if [ "$dom" -eq "$nowm" ];then
                /usr/bin/wget -T 86400 -t 1 $1
	        rm -f $rmfile
                #echo `date '+%Y-%m-%d %H:%M:%S'` >> /job/common/testm.log
                sleep $sleeptime
            else
	        break
            fi
        done

    fi
}
