#!/bin/sh
source /var/www/fkt/job/common/func_wget_city.sh
func_wget_city ${MLS_JOB_NAME}/data_public_change/sell_rent_house/
func_wget_city ${MLS_JOB_NAME}/data_public_change/buy_rent_customer/
func_wget_city ${MLS_JOB_NAME}/district_public_change/sell_rent_house/
