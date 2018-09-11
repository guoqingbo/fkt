#!/bin/bash
source /var/www/fkt/job/common/config.sh
function  func_wget_city()
{
    if [ "$2" != "" ]; then
       str_city=$2;
    else
       cd /var/www/fkt/job/common/
       str_city=`cat city_list`;
    fi
    arr_city=(${str_city//,/ });
    #cd $1
    for city_spell in ${arr_city[@]}  
    do  
        /usr/bin/wget -T 86400 -t 1 $1?city=$city_spell
        sleep 5
        rm -rf $1*
        rm -rf index.html*
    done
}
