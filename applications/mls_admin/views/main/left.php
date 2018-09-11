<!DOCTYPE html>
<html>
<head>
<title>left</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls_admin/css/top/global.css" type="text/css" rel="stylesheet">
<link href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls_admin/css/left/left.css" type="text/css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<script>
(function($){
var defaults={
selectClassName:'.select-val',
selectList:'.select-list'
};
var cache = [];
$.fn.selectBox=function(options){
options = $.extend({},defaults,options);
this.each(function(){
var self = $(this);
var _select = self.find(options['selectClassName']);
var list = self.find(options['selectList']);
cache.push(list);
var func = function(){
list.hide();
$(document).unbind('click', func);
};
_select.bind('click', function(event){
$.each(cache, function(i, n){
n.hide();
});
list.show();
$(document).bind('click', func);
event.stopPropagation();  //防止事件流冒泡
});
list.find('a').bind('click', function(event){
    var city=$(this).attr("rel");
    var city_txt=$(this).text();
    $.ajax({
        url:'../user/change_city/'+city,
        type:'get',
        success:function(result){
            window.parent.frames["topFrame"].$("#city_txt").text(city_txt);
            window.parent.frames["rightFrame"].location.reload(true);
            window.parent.frames["leftFrame"].location.reload(true);
        }
    });
    _select.text($(this).text());

});
list.bind('mouseout',function(){
  list.hide();
});
list.bind('mouseover',function(){
  list.show();
});
})
}
})(jQuery);
$(function(){
$('.select-city').selectBox();
$('.nav-list').find("a").bind('click',function(){
var nav_prev = $(this).parents('.nav-list').siblings().find('a').text();
var nav_now = $(this).text();
window.parent.frames["topFrame"].$("#nav_prev").text(nav_prev);
window.parent.frames["topFrame"].$("#nav_now").text(nav_now);
});
})
</script>
<script>
$(function(){
	$('.nav-title').next().hide()
	$('.nav-title').bind('click',function(){
		var self = $(this);
		self.next().toggle();
		self.parent().siblings().find('.nav-list').hide()
		//$(self.parents().next()).toggle();
	});
	//点击导航名称变色
	$('.nav-list').find('li').bind('click',function(){
		if($(this).hasClass('on')) return;
		$('.on').removeClass('on');
		$(this).addClass('on')
	})
})
function switch_tab(liid){
	$('.on').removeClass('on');
	$("#"+liid).addClass('on');
	$("#"+liid).parent().css('display','block');
	$("#"+liid).parent().parent().siblings().find('.nav-list').hide();
}
</script>
<style>
a:focus{
-moz-outline:none;
outline:none
}
</style>
</head>

<body>
    <div class="main-left">
        <div class="all-data">
            <div class="select-city">
                <div class="select-val"><?php echo (isset($_SESSION[WEB_AUTH]['is_admin']) && $_SESSION[WEB_AUTH]['is_admin']==1)?'系统管理':$this_city['cityname'].'站';?></div>
                <div class="select-list clearfix" style="overflow-x: hidden;overflow-y: scroll; max-height: 557px;">
                	<span><?php echo $this_city['cityname'];?>站</span>
                    <div class="select-list-con">
                        <?php if($role=='3' || $role=='4'){?>
                        <a href="javascript:void(0)" rel="admin">系统管理</a>
                        <?php }?>
                        <?php if(is_full_array($city_list_new)){ foreach($city_list_new as $k => $v){?>
                        <a href="javascript:void(0)" rel="<?php echo $v['spell'];?>"><?php echo $v['cityname'];?>站</a>
                        <?php }} ?>
                    </div>
                    <div class="select-list-ft"></div>
                </div>
            </div>
        </div>
        <ul class="nav">
            <?php
              $this_user = $_SESSION[WEB_AUTH];
              if($this_user['role']==1){
            ?>
            <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">超级管理</a></h3>
                <ul style="display: none;" class="nav-list">
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/purview_father_node/index" target="rightFrame">权限根节点设置</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/purview_node/index" target="rightFrame">权限节点设置</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/manager/index"target="rightFrame">管理员角色管理</a></li>
                </ul>
            </li>
            <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">系统管理</a></h3>
                <ul style="display: none;" class="nav-list">
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/user_group/index" target="rightFrame">用户组管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/user/data_list" target="rightFrame">用户信息管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/city/index" target="rightFrame">城市管理</a></li>
                </ul>
            </li>
            <?php
              }else if(isset($this_user['is_admin']) && $this_user['is_admin']==1 && $this_user['role'] == 3){
            ?>
            <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">系统管理</a></h3>
                <ul style="display: none;" class="nav-list">
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/user_group/index" target="rightFrame">用户组管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/user/data_list" target="rightFrame">用户信息管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/city/index" target="rightFrame">城市管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/broker_sms/index" target="rightFrame">经纪人验证码管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/phone_info_400/index" target="rightFrame">400电话管理</a></li>
                </ul>
            </li>
            <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">帮助中心管理</a></h3>
                <ul style="display: none;" class="nav-list">
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/help_center" target="rightFrame">帮助中心管理</a></li>
                </ul>
            </li>
            <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">意见反馈管理</a></h3>
                <ul style="display: none;" class="nav-list">
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/feedback" target="rightFrame">意见反馈管理</a></li>
                </ul>
            </li>
			      <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">应用管理</a></h3>
                <ul style="display: none;" class="nav-list">
                  <li><a href="<?php echo MLS_ADMIN_URL;?>/mls_apply/index" target="rightFrame">经纪人系统管理</a></li>
                </ul>
            </li>
			      <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">功能迭代管理</a></h3>
                <ul style="display: none;" class="nav-list">
                    <li><a href="<?php echo MLS_ADMIN_URL; ?>/features_notice/index" ? target="rightFrame">功能迭代管理</a></li>
                </ul>
            </li>
            <?php
              } else if(isset($this_user['is_admin']) && $this_user['is_admin']==1 && $this_user['role'] == 4){
            ?>
            <li>
                <h3 class="nav-title"><span class="personal-settings"></span><a href="#" target="">系统管理</a></h3>
                <ul style="display: none;" class="nav-list">
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/user/data_list_city_manage" target="rightFrame">用户信息管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/broker_sms/index" target="rightFrame">经纪人验证码管理</a></li>
                    <li><a href="<?php echo MLS_ADMIN_URL;?>/phone_info_400/index2" target="rightFrame">400电话管理</a></li>
                </ul>
            </li>
            <?php
              }else{
                  if(!isset($left_menu['result'])){
                  foreach($left_menu as $k => $v){
                      $module_id = $k;
            ?>
            <li>
                <h3 class="nav-title"><span class="<?php echo $v['class_str'];?>"></span><a href="#" target=""><?php echo $v['p_name'];?></a></h3>
                <ul style="display: none;" class="nav-list">
                    <?php foreach($v['purview_node_data'] as $k => $v){?>
                    <li id="liid<?php echo $v['id'];?>"><a href="<?php echo '../'.$v['path'];?>" target="<?php if ($module_id == 0) {echo '_blank';} else {echo 'rightFrame';}?>"><?php echo $v['name'];?></a></li>
                    <?php }?>
                </ul>
            </li>
            <?php
              }
            }
            else{
                echo '<code color="red">您尚未开通任何菜单权限<br>(请联系管理员)</code>';
            }
          }?>
        </ul>
    </div>

</body></html>
