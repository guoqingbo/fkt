<!--我要举报-->
<div class="pop_box_g report_box pop_box_g_border_none" id="js_woyaojubao" style="display:block">
	<div class="hd">
        <div class="title">我要举报</div>
      <!--  <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>-->
    </div>

    <div class="report_box">
    	<div class="report_tips">
    		<p class="left" style="line-height:20px;">为了共同打造真实可靠的合作平台，举报经核实后将奖励您一定的积分</p>
    	</div>
    	<form name="fileform_head" id="fileform_head"  enctype="multipart/form-data">
    		<table class="table report_table">
    			<tr class="retrbg">
    				<td class="retdname" >举报类型：</td>
    				<td>
    					<select name="report_type" id="report_obj" name="report_type">
    						<option value="2">客源虚假</option>
							<option value="3">已成交</option>
							<option value="4">其它</option>
    					</select>
    				</td>
					<input type="hidden" value="<?php echo $customer_id ?>" name="customer_id"/>
						<input type="hidden" value="3" name="style"/>
    			</tr>
    			<tr class="retrbg">
    				<td class="retdname"><span style="color:red">*</span>举报原因：</td>
    				<td>
    					<textarea name="report_text" id="text_uid" placeholder="请详细说明举报理由不少于十个字"
						onkeyup="textCounter()"></textarea>
						<p id="p_id_text"></p>
                        <p style="line-height:18px; color:#1dc681; padding-top:5px;">请上传相关证据图片<!--，如IM沟通截图等-->。</p>
						<p style="line-height:18px; color:#1dc681;">支持PNG、JPG文件上传，最大一张图片5M，最多可上传5张。</p>
    				</td>
    			</tr>
      <tr class="retrbg">
    				<td class="retdname" valign="top">上传证据：</td>
    				<td >
<script type="text/javascript">
var swfu1;
$(function() {
swfu1 = new SWFUpload({
    file_post_name: "file",
    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
	file_size_limit : "5 MB",
	file_types : "*.jpg;*.png",
	file_types_description : "JPG Images",
	file_upload_limit : "0",
	file_queue_limit : "5",

	custom_settings : {
		upload_target : "jsPicPreviewBoxM1",
		upload_limit  : 5,
		upload_nail	  : "thumbnails1",
		upload_infotype : 1
    },
	swfupload_loaded_handler : swfUploadLoaded,
	file_queue_error_handler : fileQueueError,
	file_dialog_start_handler : fileDialogStart,
	file_dialog_complete_handler : fileDialogComplete,
	upload_progress_handler : uploadProgress,
	upload_error_handler : uploadError,
	upload_success_handler : uploadSuccessNew,
	upload_complete_handler : uploadComplete,

	button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/uploadImg.png",
	button_placeholder_id : "spanButtonPlaceholder1",
	button_width: 66,
	button_height: 26,
	button_cursor: SWFUpload.CURSOR.HAND,
    button_text:"",
    flash_url : "/swfupload.swf"
});
});
</script>
                     <div style="width:66px; height:26px; padding-top:10px;">
                    	 <span id="spanButtonPlaceholder1"></span>
    				</div>
                    <div  id="jsPicPreviewBoxM1" style="display:none" ></div>
                    <div class="picPreviewBoxM clearfix ui-sortable left" style=" height:75px;padding:0 5px 10px 0;" id="thumbnails1"></div>

                    </td>

    		</tr>

    		</table>
    		<input  class="report_btn"   value="举报" onclick="add_report()" id="repot"  />
    	</form>
    </div>
</div>

<script>
function add_report(){
	var text_uid=$("#text_uid").val();
		if(text_uid==''){
		$("#dialog_do_warnig_tip").html("必须填写原因");
				 openWin('js_pop_do_warning');
				 return false;
	}
	if(text_uid.length<10){

	 return false;
	}
    /***
	var photo_name=$("#thumbnails1").html();
	if(photo_name==''){
		$("#dialog_do_warnig_tip").html("必须上传证据");
				 openWin('js_pop_do_warning');
				 return false;
	}***/
	$.ajax({
		type: 'POST',
        url: '/rent_customer/add_report/',
        data:$("#fileform_head").serialize(),
        dataType:'json',
		success:(function(data){
			if(data['select_num']>0){
				 $("#dialog_do_warnig_tip").html("该客源的类型你已经举报过了");
				 openWin('js_pop_do_warning');
				 return false;
			}
			if(data['brokerinfo_id']==data['broker_id']){
				 $("#dialog_do_warnig_tip").html("不能对自己的客源举报");
				 openWin('js_pop_do_warning');
				 return false;
			}
			if(data['return_id']>0){
				 $("#imgg").attr("src",'<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png');
				 $("#dialog_do_warnig_tip").html("举报成功");
				 openWin('js_pop_do_warning');
				 $("#sure_yes").click(function(){
					 $(window.parent.document).find('#GTipsCoverjs_woyaojubao').remove();
					 $(window.parent.document).find('#js_woyaojubao').hide();
				 });
			}else{
				 $("#dialog_do_warnig_tip").html("举报失败");
				 openWin('js_pop_do_warning');
			}
		})

	})

}
function textCounter(){
	var text_uid=$("#text_uid").val();
	var text_num=10-text_uid.length;
	if(text_uid.length<10){
		$('#p_id_text').html('<span style="color:red;">您至少还需要输入'+text_num+'字</span>');
	}else{
		$('#p_id_text').html('');
	}

}
</script>
<!--操作结果弹出警告-->
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
			<div class="text-wrap">
                    <table>
                        <tr>
                            <td><div class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                            <td class="msg" ><span class="bold" id="dialog_do_warnig_tip"></span></td>
                        </tr>
                    </table>
                </div>
				<button type="button" id="sure_yes" class="btn-lv1 btn-mid JS_Close">确定</button>
            </div>

        </div>
    </div>
</div>
