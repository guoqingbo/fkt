<!--详情页弹框-->
<div id="js_pop_box_g pop_box_g_border_none" class="iframePopBox" style=" width:816px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>

<!--匹配详情页弹框-->
<div id="js_pop_box_g_match" class="iframePopBox" style=" width:1200px; height:540px; ">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="1200" height="540" class='iframePop' src=""></iframe>
</div>

<!--页面处理中弹层-->
<div style="display:none; text-align: center;" id ='docation_loading'>
    <img src ="<?php echo MLS_SOURCE_URL; ?>/common/images/loading_6.gif">
    <p style="font-size: 16px; font-family:'微软雅黑'; line-height: 30px; color: #fff;">正在处理</p>
</div>

<!--引入公用对话框-->
<?php $this->view('common/common_dialog_box.php');?>
