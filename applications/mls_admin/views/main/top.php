<!DOCTYPE html>
<html><head>
<title>top</title>
<link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/top/global.css" rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/top/top.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>

<script>
$(function(){
	$('.refresh').bind('click',function(){
		window.parent.frames["rightFrame"].location.reload();
	});
})
</script>
    <style>
        .top-left{
            height: 54px;
            line-height: 54px;
            color: #fff;
            text-indent: .5em;
            font-size: 24px;
            font-weight: bold;
        }
    </style>

</head>

<body>
	<div id="header">
    	<div class="top clearfix">
        	<div class="top-left">
                科地地产运营管理系统
            	<!--<img src="<?/*=MLS_SOURCE_URL */?>/mls_admin/images/admin_header_logo_2.png">-->
            </div>
            <div class="top-right">
            	<a href="javascript:void(0)" class="refresh" title="刷新"></a><a href="###" class="quit" onclick="parent.location.href='<?php echo FRAME_LOGOUT;?>';" title="退出"></a>
            </div>
        </div>
        <div class="top-tool clearfix">
        	<div class="current-location">
                当前所在位置:<a id="city_txt"><?php echo $this_city['cityname'];?>站</a> &gt; <a id="nav_prev">首页</a> &gt; <a id="nav_now">系统消息</a> &nbsp;&nbsp;&nbsp;<a href="../user/index/" target="rightFrame">查看最新功能迭代</a>
            </div>
            <div class="tools">
                <span>用户名：<?php echo $this_user['username'];?></span>&nbsp;<a href="../user/change_pwd/" target="rightFrame">修改密码</a>&nbsp;<span>登录时间：<?php echo date('Y-m-d H:i:s');?></span>
            </div>
        </div>
    </div>


</body></html>
