<?php require APPPATH . 'views/header.php'; ?>

<link href="<?= MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css,css/v1.0/cal.css,css/v1.0/system_set.css" rel="stylesheet" type="text/css">
<link href="<?= MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/guest_disk.css,css/v1.0/house_manage.css,css/v1.0/personal_center.css,css/v1.0/house_new.css" rel="stylesheet" type="text/css">
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>

<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/swf/swfupload.js"></script>
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/upload_logo.js"></script>

<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/cal.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/shuifei.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/personal_center.js,mls/js/v1.0/house.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/openWin.js"></script>
<script src="<?= MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js"></script>

<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>

<body>
<script type="text/javascript">
    //$(function() {
    //	$("#photofile").live("change",function(){
    //		var file = $(this).val();
    //		if(file != "")
    //		{
    //			var patrn=/(.jpg|.JPG|.bmp|.BMP)$/;
    //			if (patrn.exec(file))
    //			{
    //				$("#fileform_photo").submit();
    //			}
    //			else
    //			{
    //				$("#dialog_do_warnig_tip").html("图片格式不正确");
    //        		openWin('js_pop_do_warning');
    //				return false;
    //			}
    //		}
    //	});
    //});
    function createSwf(w, h, text) {
        var swfu_head = new SWFUpload({
            // Backend Settings
            upload_url: "<?=MLS_FILE_SERVER_URL?>/uploadimg/index/",
            //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
            //post_params: {"postion" : position},
            // File Upload Settings
            file_size_limit: "5 MB",
            file_types: "*.jpg;*.png",
            file_types_description: "JPG Images",
            file_upload_limit: 0,
            file_queue_limit: 1,

            custom_settings: {
                upload_target: "headpic_previewBoxM",
                upload_limit: 1,
                upload_nail: "headpic_img",
                upload_infotype: 1
            },

            // Event Handler Settings - these functions as defined in Handlers.js
            //  The handlers are not part of SWFUpload but are part of my website and control how
            //  my website reacts to the SWFUpload events.
            swfupload_loaded_handler: swfUploadLoaded,
            file_queue_error_handler: fileQueueError,
            file_dialog_start_handler: fileDialogStart,
            file_dialog_complete_handler: fileDialogComplete,
            upload_progress_handler: uploadProgress,
            upload_error_handler: uploadError,
            upload_success_handler: uploadSuccess,
            upload_complete_handler: uploadComplete,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,

            // Button Settings
            // button_image_url : "",
            button_placeholder_id: "photofile",
            button_width: w,
            button_height: h,
            button_text_top_padding: 6,
            button_text_left_padding: 2,
            button_cursor: SWFUpload.CURSOR.HAND,
            button_text: '<span class="btn-upload">' + text + '</span>',
            button_text_style: ".btn-upload { color: #ffffff; width: 65pt; font-size: 12pt; line-height: 34pt; text-align: center; vertical-align: middle;}",
            flash_url: "/swfupload.swf",
            debug: false
        });
    }
</script>

<div id="wrapper">
    <div id="page-wrapper">
        <?php if ($modifyResult === '') { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin:10px 0; padding:10px 0;">
                                <!--<form name="add_form" method="post" action="">-->
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    区属<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <select id="district" name="dist_id" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                        <option value="0">请选择</option>
                                                        <?php foreach ($district as $k => $v) { ?>
                                                            <option value="<?php echo $v['id'] ?>"<?php if($v['id']==$company['dist_id']){echo 'selected="selected"';}?>><?php echo $v['district'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>
                                                    <select id="street" name="streetid" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                    &nbsp&nbsp板块:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <?php foreach ($company['street_arr'] as $k => $v) { ?>
                                                        <option value="<?php echo $v['id'] ?>"<?php if($v['id']==$company['street_id']){echo 'selected="selected"';}?>><?php echo $v['streetname'] ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    总店<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" id="name" name="name" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['name']?>">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp总店电话:&nbsp&nbsp&nbsp&nbsp<input type="search" id="telno" name="telno" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['telno']?>">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    联系人:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" id="linkman" name="linkman" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['linkman']?>">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp&nbsp邮编:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" id="zip_code" name="zip_code" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['zip_code']?>">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp&nbsp传真:&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" id="fax" name="fax" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['fax']?>">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    总店地址:&nbsp&nbsp<input type="search" id="address" name="address" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['address']?>" size="62">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    邮箱:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" id="email" name="email" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['email']?>">
                                                </label>
                                                <label>
                                                   &nbsp&nbsp网址:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" id="website" name="website" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$company['website']?>">
                                                </label>
                                            </div>
                                        </div>
										<div class="set_basic_section clearfix">
											<div class="set_basic_section_line clearfix">
												<span class="hr_framework_l_remind fl"><label>上传logo：</label></span>
												<!--上传头像前-->
												<div class="my_info fl" style="margin-bottom: 0;width: 140px;">
													<div class="my_tx">
                            <div class="tx_normal">
                              <form name='fileform_photo' id='fileform_photo' action='/company/upload_logo' enctype='multipart/form-data' target='filepost_iframe' method='post'>
                                <input type="hidden" name="fileurl" id="fileurl" value="">
                                <input type="hidden" name="company_id" id="company_id" value="<?= $company['id'] ?>">
                                <?php if ($company['photo']) { ?>
                                  <img class="myself_photo_a" id="photo_replace" src="<?php echo $company['photo'] ?>" width="130" height="170"/>
                                  <div class="show_editor_remove">
                                    <span class="modify"><input name="photofile_modify" id="photofile" type="button" class="file_input">修改</span>
                                    <span class="remove" style="border-right:none;" onclick="remove_photofile()">删除</span>
                                    <script>createSwf(50, 34, "修改");</script>
                                  </div>
                                  <input type='hidden' name='action' value='photofile_modify'/>
                                  <input type='hidden' name='div_id' value='photo_replace'/>
                                <?php } else { ?>
                                  <div class="zhanwei" width="150" height="150">
                                    <input name="photofile_add" id="photofile" type="" class="file_input">
                                    <script>createSwf(130.170, "");</script>
                                  </div>
                                  <input type='hidden' name='action' value='photofile_add'/>
																<?php }?>
															</form>
														</div>
													</div>
												</div>
												<div style="float:left">
													<p>提示</p>

													<p> 1.图片小于2M</p>

													<p>2.支持JPG和BMPG格式图片</p>

													<p>3.尺寸大小150*150px</p>
												</div>
											</div>

										</div>
                                       <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="button" value="提交" onclick="replace_info()">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit_flag" value="modify">
                                <!--</form>-->
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 == $modifyResult) { ?>
            <div><h1><b>修改失败</b></h1></div>
        <?php } else { ?>
            <div><h1><b>修改成功</b></h1></div>
        <?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>
</div>
<div class="col-lg-4" style="display:none" id="js_note1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            提示框
            <button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="panel-body">
            <p id="warning_text"></p>
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
<style>
    .show_editor_remove {
        display: block
    }
</style>
<script type="text/javascript">
$(function(){
	$(window).resize(function(e) {
		personHeight()
	});
	personHeight();


	function personHeight(){//窗口改变大小的时候  计算高度
		var _height = document.documentElement.clientHeight;
		$("#person_inner").height(_height - 40);
	};
});


$(function(){
    $('#district').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/community/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('#street').empty();
                $('#street').append(str);
            }
        });
    });
});



//更改头像
function changePic(fileurl,div_id){
//  $("#"+div_id).attr("src",fileurl);
    if (fileurl == '') {
        delete_photofile()
    } else {
        changePhoto(fileurl);
    }
}
//添加头像调用
function changePhoto(fileurl){
    var photo_addHtml = "<input type='hidden' name='fileurl' id='fileurl' value='" + fileurl + "'>";
    photo_addHtml += "<input type='hidden' name='company_id' id='company_id' value='<?=$company['id']?>'>";
    photo_addHtml += "<img class='myself_photo_a' id='photo_replace' src='" + fileurl + "' width='130' height='170'/>";
    photo_addHtml += "<div class='show_editor_remove'>";
    photo_addHtml += "<span class='modify'><input name='photofile_modify' id='photofile' type='' class='file_input'>修改</span>";
    photo_addHtml += "<span class='remove' style='border-right:none;' onclick='remove_photofile()'>删除</span></div>";
    photo_addHtml += "<input type='hidden' name='action' value='photofile_modify' /><input type='hidden' name='div_id' value='photo_replace' />";
    $("#fileform_photo").html(photo_addHtml);
    createSwf(50, 34, "修改");
}

//去除头像
function remove_photofile()
{
    $('#fileurl').val('');
    $("#fileform_photo").submit();
}
//去除头像
function delete_photofile() {
    var photo_modifyHtml = "<input type='hidden' name='fileurl' id='fileurl' value=''>";
    photo_modifyHtml += "<input type='hidden' name='company_id' id='company_id' value='<?=$company['id']?>'>";
    photo_modifyHtml += "<div class='zhanwei' width='130' height='170'>";
    photo_modifyHtml += "<input type='' name='photofile_add' class='file_input' id='photofile'>";
    photo_modifyHtml += "</div><input type='hidden' name='action' value='photofile_add' />";
    $("#fileform_photo").html(photo_modifyHtml);
    createSwf(130.170, "");
}

//提交修改资料信息
function replace_info()
{
	var dist_id = $("#district").val();
	var streetid = $("#street").val();
	var name = $("#name").val();
	var telno = $("#telno").val();
	var linkman = $("#linkman").val();
	var zip_code = $("#zip_code").val();
	var fax = $("#fax").val();
	var address = $("#address").val();
	var email = $("#email").val();
	var website = $("#website").val();
	var company_id = $("#company_id").val();


	var data = {dist_id:dist_id,streetid:streetid,name:name,telno:telno,linkman:linkman,zip_code:zip_code,fax:fax,address:address,email:email,website:website,company_id:company_id};
	$.ajax({
		type: "POST",
		url: "/company/edit",
		dataType:"json",
		data:data,
		cache:false,
		error:function(){
			$("#dialog_do_warnig_tip").html("系统错误");
			openWin('js_pop_do_warning');
			return false;
		},
		success: function(data){
			//alert(data);
			if(data['errorCode'] == '401')
			{
				login_out();
				$("#jss_pop_tip").hide();
			}
			else{
				if(data.status=="repeat"){
					$("#dialog_do_warnig_tip").html(data.msg);
					openWin('js_pop_do_warning');
				}else if(data.status=="missing"){
					$("#dialog_do_success_tip").html(data.msg);
					openWin('js_pop_do_success');
				}else if(data.status=="success"){
					$("#dialog_do_success_tip").html(data.msg);
					openWin('js_pop_do_success');
				}else{
					$("#dialog_do_warnig_tip").html(data.msg);
					openWin('js_pop_do_warning');
				}
			}
		}
	});

}

</script>
<?php require APPPATH . 'views/footer.php'; ?>
</body>
