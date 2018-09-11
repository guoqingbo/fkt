<script>
    window.parent.addNavClass(10);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
		<a class="wh fr" href="javascript:void(0);" onclick="openWin('js_pop_protocol')">如何获得积分</a>
    </div>
</div>
<div class="condition_box"  id="js_search_box_02">
	<span class="iconfont" style="color:#F5AD00; vertical-align:middle;">&#xe65b;</span> 我的积分：<span style="color:#ff7800"><?php echo $credit_total;?></span>
</div>
<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c20"><div class="info">时间</div></td>
              	<td class="c20"><div class="info">使用/获取情况</div></td>
                <td class="c20"><div class="info">积分</div></td>
                <td><div class="info">备注</div></td>
            </tr>
        </table>
    </div>
	 <div class="inner shop_inner" id="js_inner">
        <table class="table">
		<?php
			if($credit_info){
				foreach($credit_info as $k=>$v){
					if($k%2 == 0 ){
		?>
			<tr>
                <td class="c20"><div class="info"><?php echo date("Y-m-d H:i:s",$v['create_time']);?></div></td>
              	<td class="c20"><div class="info"><?php echo $v['credit_way']['action']?></div></td>
                <td class="c20">
					<?php if($v['score'] > 0){?>
					<div class="info f12ab5b"><?php echo '+'.$v['score']?></div>
					<?php }else{?>
					<div class="info f60"><?php echo $v['score']?></div>
					<?php }?>
				</td>
                <td class="align-left"><div class="info"><?php echo $v['remark'];?></div></td>
            </tr>
		<?php }else{?>
			<tr class="bg">
              	<td class="c20"><div class="info"><?php echo date("Y-m-d H:i:s",$v['create_time']);?></div></td>
              	<td class="c20"><div class="info"><?php echo $v['credit_way']['action']?></div></td>
                <td class="c20">
				<?php if($v['score'] > 0){?>
					<div class="info f12ab5b"><?php echo '+'.$v['score']?></div>
					<?php }else{?>
					<div class="info f60"><?php echo $v['score']?></div>
					<?php }?>
				</td>
                <td class="align-left"><div class="info"><?php echo $v['remark'];?></div></td>
            </tr>
		<?php }}}else{?>
			<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
		<?php }?>
        </table>
    </div>
</div>

<div id="js_fun_btn" class="fun_btn clearfix">
    <input type="hidden" class="input" name="page" value="1">
    <form action="" name="search_form" method="post" id="subform">
	<div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
    </form>
</div>

<div class="pop_box_g pop_see_msg_info" id="js_see_msg_info">
    <div class="hd">
        <div class="title">消息详情</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);">&#xe60c;</a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
				<h3 class="h3" id="d_title"></h3>
				<p class="time" id="d_ctime"></p>
				<p class="text index-text" id="d_message"></p>
				<div class="m_bd">
	       	 		<button class="btn-lv1 btn-mid JS_Close" type="button">确定</button>
				</div>
         </div>

    </div>
</div>
<!--载入如何获取积分页面-->
<?php $this->view('my_credit/credit');?>


<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text" id='dialog_do_warnig_tip'></p><br />
                <button type="button" class="btn-lv1 btn-left JS_Close" >确定</button>
                <!--<button type="button" class="btn-hui1 JS_Close">取消</button>-->
            </div>
        </div>
    </div>
</div>

<!--询问操作确定弹窗-->
<div id="jss_pop_tip" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" id="dialogSaveDiv"></p><br />
                    <button type="button" id = 'dialog_share' class="btn-lv1 btn-left JS_Close" >确定</button>
                    <button type="button" class="btn-hui1 JS_Close">取消</button>
                    <input type ="hidden" name='ci_id' id = 'rowid' value = ''>
                    <input type ="hidden" name='secret_key' id = 'secret_key' value = ''>
                    <input type ="hidden" name='atction_type' id = 'atction_type' value = ''>
                    <input type ="hidden" name='do_type' id = 'do_type' value = ''>
                </div>
            </div>
    </div>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">关于获取积分</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>
<script>
$(window).resize(function(e) {
			innerHeight()
});

//详情操作弹出框
function detail_pop(id){
	$.ajax({
		type: "POST",
		url: "/message/details/",
		data: "id="+id,
		dataType:"json",
		cache:false,
		error:function(){
			alert("系统错误");
			return false;
		},
		success: function(data){
			//alert(data['message']);
			$('#d_title').html("");
			$("#d_ctime").html("");
			$('#d_message').html("");

			$('#d_title').html(data['title']);
			$('#d_ctime').html(data['createtime']);
			$('#d_message').html(data['message']);
			switch(data['from']){
				case "0" : $("#d_ctime").html(data['createtime']+" 科地地产");
				break;
				case "1" : $("#d_ctime").html(data['createtime']+" 其他平台");
				break;
			}
			if(data['is_read'] == 1){
				$("#tr"+id).removeClass('fw');
				$("#img"+id).attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/notice2.png");
			}

			openWin('js_see_msg_info');

		}
	});
}

</script>
