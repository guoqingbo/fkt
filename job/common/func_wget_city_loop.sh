#!/bin/bash
source /var/www/fkt/job/common/func_wget_loop.sh
function  func_wget_city_loop()

{
    if [ "$3" != "" ]; then
       str_city=$3;
    else
       cd /var/www/fkt/job/common/
       str_city=`cat city_list`;
    fi
    arr_city=(${str_city//,/ });
    for city_spell in ${arr_city[@]}  
    do
	{
        #创建文件句柄
        if [ ! -f "/job/common/$2_$city_spell" ]; then
           /bin/touch "/job/common/$2_$city_spell"
        else
           continue
        fi  
	func_wget_loop_m $1?city=$city_spell 2
        rm -rf index.html*
        rm -rf "/job/common/$2_$city_spell"
	} &
    done
}



function  func_wget_city_loop_1()
{
    if [ "$3" != "" ]; then
       str_city=$3;
    else
       cd /var/www/fkt/job/common/
       str_city=`cat city_list`;
    fi
    arr_city=(${str_city//,/ });
    for city_spell in ${arr_city[@]}
    do
        {
        #创建文件句柄
        if [ ! -f "/job/common/site_out_demon_queue_log/1/$2_$city_spell" ]; then
           /bin/touch "/job/common/site_out_demon_queue_log/1/$2_$city_spell"
        else
           continue
        fi
        func_wget_loop_m $1?city=$city_spell 2
        rm -rf index.html*
        rm -rf "/job/common/site_out_demon_queue_log/1/$2_$city_spell"
        } &
    done
}

function  func_wget_city_loop_2()
{
    if [ "$3" != "" ]; then
       str_city=$3;
    else
       cd /var/www/fkt/job/common/
       str_city=`cat city_list`;
    fi
    arr_city=(${str_city//,/ });
    for city_spell in ${arr_city[@]}
    do
        {
        #创建文件句柄
        if [ ! -f "/job/common/site_out_demon_queue_log/2/$2_$city_spell" ]; then
           /bin/touch "/job/common/site_out_demon_queue_log/2/$2_$city_spell"
        else
           continue
        fi
        func_wget_loop_m $1?city=$city_spell 2
        rm -rf index.html*
        rm -rf "/job/common/site_out_demon_queue_log/2/$2_$city_spell"
        } &
    done
}

function  func_wget_city_loop_3()
{
    if [ "$3" != "" ]; then
       str_city=$3;
    else
       cd /var/www/fkt/job/common/
       str_city=`cat city_list`;
    fi
    arr_city=(${str_city//,/ });
    for city_spell in ${arr_city[@]}
    do
        {
        #创建文件句柄
        if [ ! -f "/job/common/site_out_demon_queue_log/3/$2_$city_spell" ]; then
           /bin/touch "/job/common/site_out_demon_queue_log/3/$2_$city_spell"
        else
           continue
        fi
        func_wget_loop_m $1?city=$city_spell 2
        rm -rf index.html*
        rm -rf "/job/common/site_out_demon_queue_log/3/$2_$city_spell"
        } &
    done
}

#用于并发单次循环城市
function  func_wget_city_loop_once()
{
    if [ "$2" != "" ]; then
       str_city=$2;
    else
       cd /var/www/fkt/job/common/
       str_city=`cat city_list`;
    fi
    arr_city=(${str_city//,/ });

    for city_spell in ${arr_city[@]}
    do
        {
        func_wget_loop_n $1/$city_spell/ 1
        rm -rf index.html*
        } &
    done
}
