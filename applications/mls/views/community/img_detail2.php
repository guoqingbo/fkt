<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<!--客源详情页-弹层-->
<div class="pop_box_g" style="height:420px;width:697px;display: block; border:none; background:#fff;">
    <div class="hd">
        <div class="title">图片详情</div>
    </div>
    <form method="POST" name='form1' action="<?php echo MLS_URL;?>/community/save_picture">
    <div class="photo-cont clearfix">
		<ul class="photo-menu clearfix">
            <li id="1" class="<?php if(2 == $type_id){echo 'on';}?>" onclick="window.location.href='/community/img_detail/<?php echo $id;?>/2'">
				<span class="photo-icon iconfont"></span>
				<span class="photo-title">小区正门</span>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tip_l.png" />
			</li>
			<li id="2" class="<?php if(3 == $type_id){echo 'on';}?>" onclick="window.location.href='/community/img_detail/<?php echo $id;?>/3'">
				<span class="photo-icon iconfont"></span>
				<span class="photo-title">外景图</span>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tip_l.png" />
			</li>
			<li id="3" class="<?php if(1 == $type_id){echo 'on';}?>" onclick="window.location.href='/community/img_detail/<?php echo $id;?>/1'">
				<span class="photo-icon iconfont"></span>
				<span class="photo-title">户型图</span>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tip_l.png" />
			</li>
			<li id="4" class="<?php if(4 == $type_id){echo 'on';}?>" onclick="window.location.href='/community/img_detail/<?php echo $id;?>/4'">
				<span class="photo-icon iconfont"></span>
				<span class="photo-title">小区环境</span>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tip_l.png" />
			</li>
			<li id="5" class="<?php if(5 == $type_id){echo 'on';}?>" onclick="window.location.href='/community/img_detail/<?php echo $id;?>/5'">
				<span class="photo-icon iconfont"></span>
				<span class="photo-title">内部设施</span>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tip_l.png" />
			</li>
			<li id="6" class="<?php if(6 == $type_id){echo 'on';}?>" onclick="window.location.href='/community/img_detail/<?php echo $id;?>/6'">
				<span class="photo-icon iconfont"></span>
				<span class="photo-title">周边配套</span>
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/new/tip_l.png" />
			</li>
            <input type="hidden" name="pic_type" value="<?php echo $type_id;?>">
            <input type="hidden" name="cmt_id" value="<?php echo $id;?>">
		</ul>
		<div class="photo-main clearfix">
			<div class="photo-right right">
				<div class="photo-up">
				<div class="photo-up-inner">
                    <?php
                        if(1 == $type_id){
                            if(!empty($small_img_1)){
                                foreach($small_img_1 as $k=>$v){
                                    echo '<img class="photo-over photo-over1" width="80" height="60" src="'.$v.'">';
                                }
                            }else{
                                echo '<img class="photo-over photo-over1" width="80" height="60" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg">';
                            }
                        }else if(2 == $type_id){
                            if(!empty($small_img_2)){
                                foreach($small_img_2 as $k=>$v){
                                    echo '<img class="photo-over photo-over1" width="80" height="60" src="'.$v.'">';
                                }
                            }else{
                                echo '<img class="photo-over photo-over1" width="80" height="60" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg">';
                            }
                        }else if(3 == $type_id){
                            if(!empty($small_img_3)){
                                foreach($small_img_3 as $k=>$v){
                                    echo '<img class="photo-over photo-over1" width="80" height="60" src="'.$v.'">';
                                }
                            }else{
                                echo '<img class="photo-over photo-over1" width="80" height="60" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg">';
                            }
                        }else if(4 == $type_id){
                            if(!empty($small_img_4)){
                                foreach($small_img_4 as $k=>$v){
                                    echo '<img class="photo-over photo-over1" width="80" height="60" src="'.$v.'">';
                                }
                            }else{
                                echo '<img class="photo-over photo-over1" width="80" height="60" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg">';
                            }
                        }else if(5 == $type_id){
                            if(!empty($small_img_5)){
                                foreach($small_img_5 as $k=>$v){
                                    echo '<img class="photo-over photo-over1" width="80" height="60" src="'.$v.'">';
                                }
                            }else{
                                echo '<img class="photo-over photo-over1" width="80" height="60" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg">';
                            }
                        }else if(6 == $type_id){
                            if(!empty($small_img_6)){
                                foreach($small_img_6 as $k=>$v){
                                    echo '<img class="photo-over photo-over1" width="80" height="60" src="'.$v.'">';
                                }
                            }else{
                                echo '<img class="photo-over photo-over1" width="80" height="60" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg">';
                            }
                        }else{
                            if(!empty($small_img_1)){
                                foreach($small_img_1 as $k=>$v){
                                    echo '<img class="photo-over photo-over1" width="80" height="60" src="'.$v.'">';
                                }
                            }else{
                                echo '<img class="photo-over photo-over1" width="80" height="60" src="'.MLS_SOURCE_URL.'/mls/images/v1.0/no_img.jpg">';
                            }
                        }
                    ?>
					<div id="jsPicPreviewBoxM1" style="display:none" ></div>
					<?php if($is_upload == 1){?>
					<div class="picPreviewBoxM ui-sortable" id="thumbnails1"><div class="upload-photo"><span id="spanButtonPlaceholder1"></span></div></div>
					<?php } ?>
				</div>
				</div>
				<div class="photo-anniu">
					<a href="javascript:void(0);" class="btn-lv1 btn-left"  onclick="javascript:document.form1.submit();">保存</a><a href="javascript:void(0);" class="btn-hui1 JS_Close">取消</a>
				</div>
			</div>
            <img class="photo-big" width="400" height="300" src="<?php if(1 == $type_id){echo strlen($small_img_1[0]) > 0 ? $small_img_1[0] : MLS_SOURCE_URL."/mls/images/v1.0/no_img.jpg" ;}else if(2 == $type_id){echo strlen($small_img_2[0]) > 0 ? $small_img_2[0] : MLS_SOURCE_URL."/mls/images/v1.0/no_img.jpg";}else if(3 == $type_id){echo strlen($small_img_3[0]) > 0 ? $small_img_3[0] : MLS_SOURCE_URL."/mls/images/v1.0/no_img.jpg" ;}else if(4 == $type_id){echo strlen($small_img_4[0]) > 0 ? $small_img_4[0] : MLS_SOURCE_URL."/mls/images/v1.0/no_img.jpg";}else if(5 == $type_id){echo strlen($small_img_5[0]) > 0 ? $small_img_5[0] : MLS_SOURCE_URL."/mls/images/v1.0/no_img.jpg";}else if(6 == $type_id){echo strlen($small_img_6[0]) > 0 ? $small_img_6[0] : MLS_SOURCE_URL."/mls/images/v1.0/no_img.jpg";}?>">
		</div>
    </div>
    </form>

</div>
<!--图片上传弹框-->
<div id="js_upload" class="iframePopBox" style="width:697px;height:420px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="697" height="420" class='iframePop' src=""></iframe>
</div>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js "></script>
<script>
$(function(){
	$(".photo-menu li").hover(function(){
		$(this).toggleClass("hover");
	});
	$(".photo-menu li").click(function(){
		$(".photo-menu li").removeClass("on");
		$(".photo-menu li").find("img").hide();
		$(this).addClass("on");
		$(this).find("img").show();
	});
	$(".photo-over").live('click',function(){
		$(".photo-right .photo-over").css("border","1px #fff solid");
		$(this).css("border","1px #5598df solid");
		src = $(this).attr('src');
		$(".photo-big").attr('src',src);
	});
	$(".photo-anniu .btn-lv1").live('click',function(){
		$(".photo-anniu").hide();
	});
	$(".photo-anniu .btn-hui1").live('click',function(){
		$(window.parent.document).find("#GTipsCoverjs_tupian").remove();
		$(window.parent.document).find("#js_tupian").hide();
	});


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

		button_image_url : "<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/flash_btn04.png",
		button_placeholder_id : "spanButtonPlaceholder1",
		button_width: 80,
		button_height: 60,
		button_cursor: SWFUpload.CURSOR.HAND,
		button_text:"",
		flash_url : "/swfupload.swf"
	});

});
</script>
