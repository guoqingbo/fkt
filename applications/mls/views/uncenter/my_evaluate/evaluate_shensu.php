<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn" id="sure_yes">确定</button>
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
			</div>
		</div>
	</div>
</div>

<div class="pop_box_g report_box" id="js_woyaoshensu" style="display:block;border:0;">
	<div class="hd">
        <div class="title">我要申诉</div>
    </div>

    <div class="report_box report_box02">
    	<div class="report_tips report_tips02">
    	   为了共同打造真实可靠的合作平台，杜绝恶意评价，您的申诉将在三个工作日内处理完毕。如申诉成功，该评价失效，不计入动态评分中。
    	</div>

		<table class="table report_table">
			<tr class="retrbg">
				<td class="retdname">申诉说明：</td>
				<td>
					<textarea name="report_text" id="content" placeholder="请详细说明申诉理由不少于20个字"
						onkeyup="textCounter()"></textarea>
					<p id="p_id_text"></p>
				</td>
			</tr>
			<tr>
                <td colspan="2">
                    <p class="text_tip_bottom">如有证据证明为恶意评价，请上传相关文件。支持PNG、JPG文件上传，最大5M，最多上传五张。</p>
                </td>
            </tr>
   	        <tr class="retrbg">
    			<td class="retdname" valign="top">上传证据：</td>
    			<td>
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
		<input type="button" class="report_btn" value="提交" onclick="modify()" attr-id="<?=$id?>" tran-id="<?=$transaction_id ?>"  />
    </div>
</div>

<script type="text/javascript">
function textCounter(){
	var text_uid=$("#content").val();
	var text_num=20-text_uid.length;
	if(text_uid.length<20){
		$('#p_id_text').html('<span style="color:red;">您至少还需要输入'+text_num+'字</span>');
	}else{
		$('#p_id_text').html('');
	}

}

function modify(){
	var id = $('.report_btn').attr('attr-id');
	var transaction_id = $('.report_btn').attr('tran-id');
	var content = $("#content").val();
	var photo_url = "";

    $("input[name='img_name1[]']").each(function(index,item){
        photo_url += $(this).val()+',';
    });

	var data = {id:id,transaction_id:transaction_id,content:content,photo_url:photo_url};
	$.ajax({
		type: "POST",
		url: "/my_evaluate/modify/",
		dataType:"json",
		data:data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
  		    openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			if(data.status=="success"){
    			$("#dialog_do_success_tip").html(data.msg);
        		openWin('js_pop_do_success');
        		$("#sure_yes").click(function(){
        			parent.location.reload();
				 });
    		}else{
    			$("#dialog_do_warnig_tip").html(data.msg);
        		openWin('js_pop_do_warning');
    		}
		}
	});
}
</script>
