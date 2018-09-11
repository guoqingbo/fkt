<script>
    window.parent.addNavClass(10);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box_02">
    <div  class="shop_tab_title clearfix">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>
<?=$str?>
<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tbody><tr>
                <td class="c3"><div class="info"></div></td>
                <td class="c5"><div class="info">类型</div></td>
				<td class="c20"><div class="info">提醒内容</div></td>
				<td class="c10"><div class="info">跟进人</div></td>
				<td class="c20"><div class="info">跟进内容</div></td>
                <td class="c10"><div class="info">跟进方式</div></td>
                <td class="c10"><div class="info">提醒时间</div></td>
                <td><div class="info">状态</div></td>
            </tr>
        </tbody></table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
			<tbody>
				<?php if($list){
				foreach($list as $vo){
						foreach($detail as $d){
							if($d['id']==$vo['detail_id']){?>
								<tr <?=($vo[is_look]==2)?"class='tr-click'":"class='tr-click fw'"?> date="<?php echo $vo['id'];?>" id="tr<?php echo $vo['id'];?>">
									<td class="c3"><div class="info"><input type="checkbox" class="checkbox" name="items" value="<?php echo $vo['id'];?>" ></div></td>
									<td class="c5"><div class="info"><?=$vo['title']?></div></td>
									<td class="c20"><div class="info"><?=$vo['contents']?></div></td>
									<td class="c10"><div class="info"><?=$vo['broker_name']?></div></td>
									<td class="c20"><div class="info"><?=$d['text']?></div></td>
									<td class="c10"><div class="info">
									<?php 
										switch($d['follow_way']){
											case "1": echo "勘房";
												break;
											case "3": echo "电话";
												break;
											case "4": echo "磋商";
												break;
											case "5": echo "带看";
												break;
											case "6": echo "其它";
												break;
										}
									?></div></td>
									<td class="c10"><div class="info" style="color:#999;"><?=date("Y-m-d H:i:s",$vo['notice_time'])?></div></td>
									<td><div class="info" id="status<?=$vo['id']?>"><?=($vo['status']==1)?"<font color='green'>已完成</font>":"<font color='red'>未完成</font>"?></div></td>
								</tr>
				<?php }}if(!$vo['detail_id']){?>
								<tr <?=($vo[is_look]==2)?"class='tr-click'":"class='tr-click fw'"?> date="<?php echo $vo['id'];?>" id="tr<?php echo $vo['id'];?>">
									<td class="c3"><div class="info"><input type="checkbox" class="checkbox" name="items" value="<?php echo $vo['id'];?>" ></div></td>
									<td class="c5"><div class="info"><?=$vo['title']?></div></td>
									<td class="c20"><div class="info"><?=$vo['contents']?></div></td>
									<td class="c10"><div class="info"><?=$vo['broker_name']?></div></td>
									<td class="c20"><div class="info"></div></td>
									<td class="c10"><div class="info"></div></td>
									<td class="c10"><div class="info" style="color:#999;"><?=date("Y-m-d H:i:s",$vo['notice_time'])?></div></td>
									<td><div class="info" id="status<?=$vo['id']?>"><?=($vo['status']==1)?"<font color='green'>已完成</font>":"<font color='red'>未完成</font>"?></div></td>
								</tr>
				
				<?php }}}else{ ?>
				<tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
				<?php } ?>
			</tbody>
		</table>
    </div>
</div>

<div id="js_fun_btn" class="fun_btn clearfix">
    <input type="checkbox" id="js_checkbox" style="float:left; margin:3px 10px 0 0;">
	<a class="btn-lan btn-left" href="javascript:void(0);" onclick="mark_complete(1)"><span>标记已完成</span></a>
	<a class="btn-lan btn-left" href="javascript:void(0);" onclick="mark_complete(2)"><span>全部标为已完成</span></a>
    <form action="" name="search_form" method="post" id="subform">
	<div class="get_page">
		<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
	</div>
    </form>
</div>


<!--消息详情-->
<div class="pop_box_g pop_see_msg_info" id="js_see_msg_info" style="width:480px; height:390px;">
    <div class="hd">
        <div class="title">消息详情</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);">&#xe60c;</a></div>
    </div>
    <div class="mod" style="height:334px;">
    	<div class="inform_inner">			
				<table class="table-info">
				<tbody>
					<input type="hidden" id="d_id">
					<tr>
                        <td width="50%" >跟进方式：<span id="d_way"></span></td>
                        <td width="50%">状态：<span id="d_status"></span></td>
                    </tr>
					<tr>
                        <td width="50%">房客源编号：<span id="d_code"></span></td>
                        <td width="50%">跟进人：<span id="d_broker_name"></span></td>
                    </tr>
					<tr>
                        <td width="50%">跟进时间：<span id="d_date"></span></td>
                        <td width="50%">发布时间：<span id="d_create"></span></td>
                    </tr>
					<tr style="height:100px;">
                        <td colspan="2" width="50%">提醒内容：<textarea class="textarea2" id="d_contents"  readonly></textarea></td>
                    </tr>
					<tr>
                        <td colspan="2" width="50%">跟进内容：<textarea class="textarea2" id="d_text"  readonly></textarea></td>
                    </tr>
                </tbody></table>
		       <div class="clearfix m_bd">
	       	 		<button class="btn-lv1 btn-mid JS_Close" id="complete" type="button" onClick="complete()">完成</button>
		       </div>
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
                    <p class="text" id="dialogSaveDiv"></p>
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
<script>
$(".tr-click").live('click',function(){
	var date = $(this).attr("date");
	detail_pop(date);
});
//详情操作弹出框
function detail_pop(id){
	$.ajax({ 
		type: "POST", 
		url: "/message/smessage_detail/", 
		data: "id="+id,
		dataType:"json",
		cache:false, 
		error:function(){ 
			alert("系统错误");  
			return false; 
		}, 
		success: function(data){
			if(data['status']==0){
				$('#d_status').html("");
				$("#d_way").html("");
				$('#d_date').html("");
				$('#d_code').html("");
				$(".m_bd").show();
				$('#d_id').val(data['id']);
				$('#d_broker_name').html(data['broker_name']);
				$('#d_create').html(data['create_time']);
				$('#d_contents').val(data['contents']);
				$('#d_text').val(data['text']);
				$('#d_date').html(data['date']);
				switch(data['status']){
					case "0" : $('#d_status').html("<font color='red'>未完成</font>");
					break;
					case "1" : $('#d_status').html("<font color='green'>已完成</font>");
					break
				}
				switch(data['follow_way']){
					case "1" : $("#d_way").html("勘房");
					break;
					case "3" : $("#d_way").html("电话");
					break;
					case "4" : $("#d_way").html("磋商");
					break;
					case "5" : $("#d_way").html("带看");
					break;
					case "6" : $("#d_way").html("其它");
					break;
				}
				switch(data['type']){
					case "1" : $("#d_code").html("CS"+data['house_id']);
					break;
					case "2" : $("#d_code").html("CZ"+data['house_id']);
					break;
					case "3" : $("#d_code").html("QG"+data['customer_id']);
					break;
					case "4" : $("#d_code").html("QZ"+data['customer_id']);
					break;
				}
				if(data['num'] == 1){
					$("#tr"+id).removeClass('fw');
				}
			
				openWin('js_see_msg_info');
			}else if(data['status']==1){
				$(".m_bd").hide();
				$('#d_status').html("");
				$("#d_way").html("");
				$('#d_date').html("");
				$('#d_code').html("");
				$('#d_id').val(data['id']);
				$('#d_broker_name').html(data['broker_name']);
				$('#d_create').html(data['create_time']);
				$('#d_contents').val(data['contents']);
				$('#d_text').val(data['text']);
				$('#d_date').html(data['date']);
				switch(data['status']){
					case "0" : $('#d_status').html("<font color='red'>未完成</font>");
					break;
					case "1" : $('#d_status').html("<font color='green'>已完成</font>");
					break
				}
				switch(data['follow_way']){
					case "1" : $("#d_way").html("勘房");
					break;
					case "3" : $("#d_way").html("电话");
					break;
					case "4" : $("#d_way").html("磋商");
					break;
					case "5" : $("#d_way").html("带看");
					break;
					case "6" : $("#d_way").html("其它");
					break;
			
				}
				switch(data['type']){
					case "1" : $("#d_code").html("CS"+data['house_id']);
					break;
					case "2" : $("#d_code").html("CZ"+data['house_id']);
					break;
					case "3" : $("#d_code").html("QG"+data['customer_id']);
					break;
					case "4" : $("#d_code").html("QZ"+data['customer_id']);
					break;
				}
				if(data['num'] == 1){
					$("#tr"+id).removeClass('fw');
				}
				openWin('js_see_msg_info');
			}
		} 
	});
	
}	


//完成按钮
function complete(){
	var id = $('#d_id').val();
	$.ajax({ 
		type: "POST", 
		url: "/message/complete/", 
		data: "id="+id,
		dataType:"json",
		cache:false, 
		error:function(){ 
			alert("系统错误");  
			return false; 
		}, 
		success: function(data){
			//alert(data);
			if(data==1){
				$('#status'+id).html("<font color='green'>已完成</font>");
				$("#dialog_do_itp").html("操作成功！");
				//openWin('js_pop_do_success');
			}else{
				$("#dialog_do_warnig_tip").html("操作失败！");
				openWin('js_pop_do_warning');
			}
		} 
	});
}




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
				url:"/message/complete",
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