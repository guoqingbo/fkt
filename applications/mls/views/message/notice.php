<script>
    window.parent.addNavClass(10);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>

<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tbody><tr>
                <td class="c3"><div class="info"></div></td>
                <td class="c3"><div class="info"></div></td>
                <td class="c5"><div class="info">类型</div></td>
                <td class="c20"><div class="info">标题</div></td>
                <td class="c40"><div class="info">内容</div></td>
                <td><div class="info">来源</div></td>
            </tr>
        </tbody></table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<tbody>
			<?php if($list){
			foreach($list as $vo){?>
				<tr <?=($vo['is_read']==1)?"class=''":"class='fw'"?> onClick="detail_pop(<?php echo $vo['id'];?>)" id="tr<?php echo $vo['id'];?>">
					<td class="c3"><div class="info"><input type="checkbox" class="checkbox" name="items" value="<?=$vo['id']?>"></div></td>
					<td class="c3"><div class="info"><img id="img<?php echo $vo['id'];?>" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/<?=($vo['is_read']==0)?"notice1.png":"notice2.png"?>"></div></td>
					<td class="c5"><div class="info">公司公告
					<!--<?php echo $type[$vo['type']];?>-->
					</div></td>
					<td class="c20"><div class="info <?=$vo['color']?>"><?=$vo['title']?></div></td>
					<td class="c40"><div class="info"><?=$vo['contents']?></div></td>
					<td><div class="info" style="color:#999; font-weight:normal;"><?=$vo['broker_name'];?>&nbsp; <?=date("Y-m-d H:i:s",$vo['createtime'])?></div></td>
				</tr>
			<?php }}else{ ?>
			<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
			<?php } ?>
			</tbody>
		</table>
    </div>
</div>

<div id="js_fun_btn" class="fun_btn clearfix">
    <input type="checkbox" id="js_checkbox" style="float:left; margin:3px 10px 0 0;">
	<a class="btn-lan btn-left" href="javascript:void(0);" onclick="mark_complete(1)"><span>标记为已读</span></a>
	<a class="btn-lan btn-left" href="javascript:void(0);" onclick="mark_complete(2)"><span>全部标记为已读</span></a>
	<!--<a class="grey_btn" href="javascript:void(0);" onclick="del_bulletin('message','<?=$broker_id;?>')">删除</a>
	<a class="grey_btn" href="javascript:void(0);" onclick="read_reminder('message')">设为已读</a>-->
    <form action="" name="search_form" method="post" id="subform">
	<div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
    </form>
</div>

<!--<div class="pop_box_g pop_see_msg_info" id="js_see_msg_info">
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
</div>-->

<div class="pop_box_g" style="width:760px; height:470px; display:none;" id="js_see_msg_info">
    <div class="hd">
        <div class="title">公告详情</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
		<div class="table-notice">
			<table>
				<tr>
					<td class="td-left">标题：</td>
					<td id='d_title'></td>
				</tr>
				<tr>
					<td class="td-left">内容：</td>
					<td><textarea class="input" id="d_bewrite" readonly></textarea></td>
				</tr>
				<tr>
					<td class="td-left">时间：</td>
					<td id="d_ctime">
					</td>
				</tr>
			</table>
		</div>
		<div class="center mt10">
			<button class="btn-lv1 btn-left JS_Close" type="button">确定</button>
		</div>
    </div>
</div>

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
        <div class="title">提示</div>
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
<script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/kindeditor-min.js'></script>
<script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/lang/zh_CN.js'></script>
<script>
innerHeight();
$(window).resize(function(e) {
			innerHeight()
});


//详情操作弹出框
function detail_pop(id){
	$.ajax({
		type: "POST",
		url: "/message/company_notice_detail/",
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
			KindEditor.ready(function(K) {
				K.html('#d_bewrite', '');
			});

			$('#d_title').html(data['title']);
			$('#d_ctime').html(data['createtime']);
			KindEditor.ready(function(K) {
				K.html('#d_bewrite', data['contents']);
			});

			if(data['is_read'] == 1){
				$("#tr"+id).removeClass('fw');
				$("#img"+id).attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/notice2.png");
			}

			openWin('js_see_msg_info');

		}
	});
}
//页面编辑器
var editor;
KindEditor.ready(function(K) {
	editor = K.create('#d_bewrite', {
		readonlyMode : true,
		width: '678px',
		height: '278px',
		resizeType: 0,
		newlineTag: "p",
		allowPreviewEmoticons: false,
		allowImageUpload: false,
		items: [''],
		afterBlur: function() {
			this.sync();
		}
	});
});


//标记为已读
function mark_complete(type){
	var id= [];
	var select_num = 0;
	if(type==1){
		$("input[name='items']").each(function() {
			if ($(this).attr("checked")) {
				id.push($(this).val());
				select_num ++;
			}
		});
	}else if(type==2){
		$("input[name='items']").each(function() {
				id.push($(this).val());
				select_num ++;
		});
	}else{
		return false;
	}
	if(select_num==0){
		$("#dialog_do_warnig_tip").html("请选择要标记的内容！");
		openWin('js_pop_do_warning');
		return false;
	}else{
		if(type==1){
			$("#dialogSaveDiv").html("你确定将所选的提醒标记为已完成吗？");
			openWin('jss_pop_tip');
		}else if(type==2){
			$("#dialogSaveDiv").html("你确定将所有的提醒标记为已完成吗？");
			openWin('jss_pop_tip');
		}
		$("#dialog_share").click(function(){
			$.ajax({
				url:"/message/notice_read",
				type:"post",
				dataType:"json",
				data:{
					id:id,
					type:type
				},
				cache:false,
				error:function(){
					alert("系统错误");
					return false;
				},
				success: function (data) {
					//alert(data);
					if(data<=id.length && data!=0){
						//$("#dialog_do_itp").html("操作成功");
						//openWin('js_pop_do_success');
						window.location.reload();
						//setTimeout(window.location.reload(),2000);
					}else{
						$("#dialog_do_itp").html("已标记");
						openWin('js_pop_do_success');
						//$("#dialog_do_warnig_tip").html("操作失败");
						//openWin('js_pop_do_warning');
					}
				}
			});
		});
	}

};
//阻止checkbox点选触发tr事件
$(':checkbox.checkbox').click(function(evt){
    var is = $(this).attr('checked');
    var xid = $(this).attr('xid');

    if ( is )
    {
        $(':checkbox[xtype="' + xid + '"]').attr('checked','checked');
    }
    else
    {
        $(':checkbox[xtype="' + xid + '"]').removeAttr('checked');
    }
    // 阻止冒泡
    evt.stopPropagation();
});

$("#js_pop_do_success .JS_Close").click(function(){
	//alert(111);
	location.reload();

});
</script>
