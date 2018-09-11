<style>
.upload_photo{margin-bottom:10px;}
.add_item_pic{float:left; position:relative; margin-right:10px; margin-bottom:10px;}
.add_item_pic .fun a{display:block; position:absolute; bottom:0; *bottom:-1px; left:0; width:130px; height:28px; line-height:28px; background:#456A8A; opacity:0.9;  filter:alpha(opacity=90); text-align:center; color:#fff;}
.upload_house{height:330px;}
.upload_btn1{text-align:center;}
</style>
<div class="pop_box_g" id="new_moban" style="width:640px;height:485px;display: block; border:none;">
    <div class="hd">
        <div class="title">上传图片</div>
    </div>
<!--上传图片-->
    <div class="upload_detail">
    	<table class="table">
    		<tr>
    			<td class="upw1">所属小区：</td>
    			<td colspan="3"><?php echo $cmt_data['cmt_name'];?></td>
                <input type="hidden" id="cmt_id" value="<?php echo $cmt_data['id'];?>"/>
    		</tr>
    		<tr>
    			<td class="upw1">图片类型：</td>
    			<td colspan="3">
                    <select class="select" id="img_type" name="img_type">
                        <option value="1">户型图</option>
                        <option value="2">小区正门</option>
                        <option value="3">外景图</option>
                        <option value="4">小区环境</option>
                        <option value="5">内部设施</option>
                        <option value="6">周边配套</option>
                        <option value="7">未分类图片</option>
                    </select>
                </td>
    		</tr>
    		<tr>
    			<td class="upw1">房源图片：</td>
    			<td>
    				<div class="upload_house">
						<div id="jsPicPreviewBoxM1" style="display:none" ></div>
						<div class="picPreviewBoxM ui-sortable clearfix" id="thumbnails1"><div class="upload_photo"><span id="spanButtonPlaceholder1"></span></div>
					</div>
                </td>
    		</tr>
    	</table>
    	<div class="upload_btn1">
            <a href="javascript:void(0);" class="btn-lv1 btn-left" id="img_upload_submit">确定</a>
			<a href="javascript:void(0);" class="btn-hui1">重置</a>
		</div>
    </div>
<!--图片上传弹框-->
<div id="js_upload" class="iframePopBox" style="height:430px;width:550px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="550" height="430" class='iframePop' src=""></iframe>
</div>
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont" id="close_refresh"></a>
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
		upload_limit  : 10,
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

	button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn02.png",
	button_placeholder_id : "spanButtonPlaceholder1",
	button_width: 130,
	button_height: 100,
	button_cursor: SWFUpload.CURSOR.HAND,
    button_text:"",
    flash_url : "/swfupload.swf"
});

//图片提交按钮
$('#img_upload_submit').live('click',function(){
    var cmt_id = $('#cmt_id').val();
    var img_type = $('#img_type option:selected').val();
    var img_src = [];
    var i = 0;

    $('input[name="p_filename1[]"]').each(function(){
        img_src[i] = $(this).val();
        i++;
    });
    $.ajax({
        url: "/community/add_img",
        type: "GET",
        data: {
            cmt_id: cmt_id,
            img_type:img_type,
            img_src:img_src
        },
        success: function(data)
        {
            if('add_success'==data){
                $('#dialog_do_itp').html('图片添加成功');
                openWin('js_pop_do_success');
            }else{
                $('#dialog_do_itp').html('图片添加失败');
                openWin('js_pop_do_success');
            }
        }
    });
});

$('#close_refresh').click(function(){
    location.reload();
});


});
</script>
