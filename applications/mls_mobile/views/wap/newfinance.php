<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <title>金融</title>
       <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=kft_jr/css/frozen.css,kft_jr/css/app_finance.css" rel="stylesheet" type="text/css">
        <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/kft_jr/js/zws_rem.js"></script>

    </head>

    <body ontouchstart class="index app_finance_bg">
        <div class="body_apply_bg sz_jr_bg">
            <div class="dydk_index">
                <header class="dydk_index_head"><a href="#"><img src="<?php echo MLS_SOURCE_URL;?>/mls/kft_jr/images/<?=$banner?>"/></a></header>
                <!--金融列表-->
                <div class="app_finance_list">
                    <?php
                        if($city_spell == 'cd'){
                            ?>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon dyd"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/business_info?type=3">
	                				<b>抵押宝</b>
	                				<strong>额度高，速度快，先息后本无压力</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/business_info?type=3">申请</a></dt>
                		</dl>
                	</div>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon mmb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/business_info?type=1">
	                				<b>买卖宝</b>
	                				<strong>签约当天收全款，买房首付就收房</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/business_info?type=1">申请</a></dt>
                		</dl>
                	</div>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon mfb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/business_info?type=4">
	                				<b>卖房宝</b>
	                				<strong>挂牌当天就成交，当天即可收全款</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/business_info?type=4">申请</a></dt>
                		</dl>
                	</div>

                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon txb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/business_info?type=2">
	                				<b>提现宝</b>
	                				<strong>不用着急低价卖，安心卖个好价钱</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/business_info?type=2">申请</a></dt>
                		</dl>
                	</div>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon ajb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/business_info?type=5">
	                				<b>按揭宝</b>
	                				<strong>无抵押无担保个人贷款</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/business_info?type=5">申请</a></dt>
                		</dl>
                	</div>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon slb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/business_info?type=6">
	                				<b>赎楼贷</b>
	                				<strong>垫资赎楼，尾款垫付，房产交易不求人</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/business_info?type=6">申请</a></dt>
                		</dl>
                	</div>
                            <?php
                        }else if($city_spell == 'sz'){
                            ?>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon dyd"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/loan_info?type=1">
	                				<b>抵押宝</b>
	                				<strong>额度高，速度快，先息后本无压力</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/loan_info?type=1">申请</a></dt>
                		</dl>
                	</div>

                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon ajb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/loan_info?type=3">
	                				<b>消费贷</b>
	                				<strong>无抵押无担保个人贷款</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/loan_info?type=3">申请</a></dt>
                		</dl>
                	</div>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon slb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/loan_info?type=2">
	                				<b>赎楼贷</b>
	                				<strong>垫资赎楼，尾款垫付，房产交易不求人</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/loan_info?type=2">申请</a></dt>
                		</dl>
                	</div>
                            <?php
                        }else if($city_spell == 'km'){
                            ?>
                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon dyd"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/loan_info?type=1">
	                				<b>抵押宝</b>
	                				<strong>额度高，速度快，先息后本无压力</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/loan_info?type=1">申请</a></dt>
                		</dl>
                	</div>

                	<div class="app_prodect">
                		<!--icon-->
                		<span class="prodect_icon slb"></span>
                		<!--介绍-->
                		<dl class="app_prodect_introduce">
                			<dd>
                				<a href="/wap/finance/loan_info?type=2">
	                				<b>赎楼贷</b>
	                				<strong>垫资赎楼，尾款垫付，房产交易不求人</strong>
                				</a>
                			</dd>
                			<dt><a href="/wap/finance/loan_info?type=2">申请</a></dt>
                		</dl>
                	</div>
                            <?php
                        }
                    ?>

                </div>
            </div>

        </div>
        <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/kft_jr/js/zepto.min.js,mls/kft_jr/js/zfrozen.js"></script>

    </body>

</html>
