<script type="text/javascript">
//window.parent.addNavClass(10);
$(function() {
	$("#photofile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
			if (patrn.exec(file))
			{
				$("#fileform_photo").submit();
			}
			else
			{
				$("#dialog_do_warnig_tip").html("图片格式不正确");
        		openWin('js_pop_do_warning');
				return false;
			}
		}
	});

});

function check_num(){
	var telno = $("#telno").val();
	var service_area = $("#service_area").val();
	var partten = /^(((\d{3,4}-)?(\d{7,8}-)?\d{3,4}?)|([1][3578][0-9]{9}))$/;
	var re = new RegExp(partten)
	if(telno.match(re)==null){
		$("#telno_error").html("请填写7-11位的固话或手机！");
		$("input[name='is_submit_t']").val('0');
	}else{
		$("#telno_error").html(" ");
		$("input[name='is_submit_t']").val('1');
	}
	if(service_area.length > 20){
		$("#service_area_error").html("最多填写20个字！");
		$("input[name='is_submit_s']").val('0');
	}else{
		$("#service_area_error").html(" ");
		$("input[name='is_submit_s']").val('1');
	}
}

//通过参数判断是否可以被提交
function modify_department(){
	var is_submit_t = $("input[name='is_submit_t']").val();
	var is_submit_s = $("input[name='is_submit_s']").val();
	if(is_submit_t ==1 && is_submit_s == 1){
		var telno = $("#telno").val();
		var service_area = $("#service_area").val();
		$.ajax({
			type : 'post',
			url  : '/organization/modify_department_edit/',
			data : {telno:telno,service_area:service_area,department_id:"<?php echo $department_info['id']?>"},
			dataType :'json',
			success : function(data){
				if(data.status=="success"){
					$("#dialog_do_success_tip").html(data.msg);
					openWin('js_pop_do_success');
				}else{
					$("#dialog_do_warnig_tip").html(data.msg);
					openWin('js_pop_do_warning');
				}
			}
		});
	}
}
</script>
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
<input type="hidden" name="is_submit_t" value="1">
<input type="hidden" name="is_submit_s" value="1">
<!--门店资料添加-->
<div class="pop_box_g zws_person_add_W450" style="display:block;height:390px;">
    <div class="hd">
        <div class="title">门店资料</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod bg-weight">
		<div class="zws_person_add">
			<dl>
				<dd>门店照片：</dd>
				<dt>
					<div class="my_info" style="margin-bottom: 0;width: 130px;">
						<div class="my_tx" style="margin-bottom:0;">
							<div class="tx_normal" style="background:url(<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/add_img_03.jpg) no-repeat center;">
								<form name='fileform_photo' id='fileform_photo' action='/organization/upload_photo_department' enctype='multipart/form-data' target='filepost_iframe' method='post'>
								<input type="hidden" name="department_id" id="department_id" value="<?=$department_info['id']?>">
									<?php if($department_info['photo']) {?>
									<img class="myself_photo_a" id="photo_flow" src="<?=$department_info['photo']?>" width="130" height="100"/>
									<div class="show_editor_remove" style="width:auto;" >
										<span class="modify"><input name="photofile_modify" id="photofile" type="file" class="file_input">修改</span>
										<span class="remove" style="border-right:none;" onclick="remove_photofile()">删除</span>
									</div>
									<input type='hidden' name='action' value='photofile_modify' />
									<input type='hidden' name='div_id' value='photo_flow' />
									<?php }else{?>
                                                                        <div class="zhanwei" style="width:130px;height: 100px;">
										 <input name="photofile_add" id="photofile" type="file" class="file_input">
									</div>
									<input type='hidden' name='action' value='photofile_add' />
									<?php }?>
								</form>
							</div>
						</div>
					</div>
					<span class="zws_person_add_strong">为达到良好的展示效果，请上传100x100px 的门店形象图</span>
				</dt>
			</dl>
			<dl>
				<dd>联系电话</dd>
				<dt>
					<input type="text" value="<?php echo $department_info['telno']?>" class="zws_person_add_input zws_p_select_w300" id='telno' onkeyup="check_num()">
					<div id='telno_error'></div>
				</dt>
			</dl>
			<dl>
				<dd>服务范围</dd>
				<dt>
					<input type="text" value="<?php echo $department_info['service_area']?>" class="zws_person_add_input zws_p_select_w300" id='service_area' onkeyup="check_num()">
					<div id='service_area_error'></div>
					<span class="zws_person_add_strong">注：门店资料将在门店微店铺中展示</span>
				</dt>

			</dl>

		</div>
		<div class="zws_person_clear"></div>
		<div class="center mt10">
			<button class="btn-lv1 btn-left" type="button" onclick="modify_department();">确定</button>
			<button class="btn-hui1 JS_Close" type="button" onclick="parent.window.location.reload(true)">取消</button>
		</div>
	</div>
</div>
<!--提示框-->
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="parent.window.location.reload(true)">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">操作失败！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="parent.window.location.reload(true)">确定</button>
			</div>
		</div>
	</div>
</div>

<style>
.tx_normal_over .show_editor_remove{display:block}
.tx_normal{height:100px;}
 .my_tx{margin-bottom:0;}
</style>
<script type="text/javascript">
//更改头像
function changePic(fileurl,div_id){
	$("#"+div_id).attr("src",fileurl);
}
//添加头像调用
function changePhoto(fileurl){
	var photo_addHtml = "<img class='myself_photo_a' id='photo_flow' src='"+fileurl+"' width='130' height='100'/>";
	photo_addHtml += "<div class='show_editor_remove'>";
	photo_addHtml += "<span class='modify'><input name='photofile_modify' id='photofile' type='file' class='file_input'>修改</span>";
	photo_addHtml += "<span class='remove' style='border-right:none;' onclick='remove_photofile()'>删除</span></div>";
	photo_addHtml += "<input type='hidden' name='action' value='photofile_modify' /><input type='hidden' name='div_id' value='photo_flow' />";
	$("#fileform_photo").html(photo_addHtml);
}
//去除头像
function remove_photofile()
{
	var photo_modifyHtml = "<div class='zhanwei' width='130' height='100'>";
	photo_modifyHtml += "<input type='file' name='photofile_add' class='file_input' id='photofile'>";
	photo_modifyHtml += "</div><input type='hidden' name='action' value='photofile_add' />";
	$("#fileform_photo").html(photo_modifyHtml);
}



</script>
