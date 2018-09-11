<body>
<form id="jsUpForm" name = 'jsUpForm' method="post" action =''>
	<div class="pop_see_inform pop_no_q_up pop_box_g" id="infomat" style="width:800px; height:500px;">
		<div class="hd">
			<div class="title">提交初审资料</div>
		</div>
        <!--		<div class="mod infomat" style="height:18px;">-->
        <!--		    <p style="color:blue;">恭喜你房源成交，提交初审资料且审核通过后就能获得<span style="color:red">500积分</span>，积分可以在积分商城兑换实物礼品哦</p>-->
        <!--		</div>-->
        <div class="mod infomat" style="height:18px;">
            <p style="color:blue;">恭喜你房源成交!</p>
        </div>
		<div class="mod infomat">
			<div class="pane-out clearfix">
				<div class="pane pane2">合同编号：
					<select class="select" name="s_id" id="s_id">
					    <?php if(is_full_array($list)){?>
						    <?php if(count($list)>1){?>
								<option value="">请选择</option>
							<?php }?>
							<?php foreach($list as $key=>$val){?>
							<option value="<?=$val['id'];?>"><?=$val['order_sn'];?></option>
							<?php }?>
						<?php }else{?>
							<option value="">暂无合同编号可以申请</option>
						<?php }?>
					</select>
					<div class="errorBox"></div>
				</div>
				<span>*若无合同编号，请确认是否已在合作中心确认成交，否则不能提交初审资料</span>
			</div>
			<div class="pane-out clearfix">
				<div class="pane">业主姓名：<input type="text" value="" class="txt" name="seller_owner" autocomplete="off"><div class="errorBox"></div></div>
                <div class="pane pane2">身份证号：<input type="text" value="" class="txt" name="seller_idcard" autocomplete="off"><div class="errorBox"></div></div>
				<div class="pane">联系方式：<input type="text" value="" class="txt" name="seller_telno" autocomplete="off"><div class="errorBox"></div></div>
			</div>
			<div class="pane-out clearfix">
				<div class="pane">买方姓名：<input type="text" value="" class="txt" name="buyer_owner" autocomplete="off"><div class="errorBox"></div></div>
                <div class="pane pane2">身份证号：<input type="text" value="" class="txt" name="buyer_idcard" autocomplete="off"><div class="errorBox"></div></div>
                <div class="pane">联系方式：<input type="text" value="" class="txt" name="buyer_telno" autocomplete="off"><div class="errorBox"></div></div>
			</div>
			<h3>二手房买卖契约完整版照片：</h3>
			<div class="upload clearfix">
				<div id="jsPicPreviewBoxM1" style="display:none" ></div>
				<div class="picPreviewBoxM ui-sortable" id="thumbnails1">
                    <div class="upload-photo">
                        <span id="spanButtonPlaceholder1">
                        </span>
                    </div>
                </div>
			</div>
            <div class="errorBox" id="photo" style="display: none"><p class="wrong">请选择一张照片上传</p></div>
			<div class="center">
				<button type="submit" class="submit" id="add_submit">提交资料</button>
			</div>
		</div>
	</div>
</form>
<!--提示框-->
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up" style="width:300px;height:140px;">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">提交成功！</p>
				<button type="button" class="btn-lv1 btn-mid" id="dialog_share">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up" style="width:300px;height:140px;">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">提交失败！</p>
				<button type="button" class="btn-lv1 btn-mid JS_Close">确定</button>
			</div>
		</div>
	</div>
</div>
<div class="shade"></div>
<script type="text/javascript" src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/swf/swfupload.js,mls/js/v1.0/xcc_uploadpic.js,mls/js/v1.0/openWin.js"></script>
<script  type="text/javascript">
	var swfu1 = new SWFUpload({
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
</script></body>
