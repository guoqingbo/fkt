<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=common/third/swf/swfupload.js"></script>
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/upload_logo.js"></script>
<script type="text/javascript">
//window.parent.addNavClass(10);
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
            file_post_name: "file",
            upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
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
            upload_success_handler: uploadSuccessNew,
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
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
<!--主要内容-->
<div class="pop_box_g" id="infomat" style="width:840px; height:470px; display:block;">
    <div class="hd">
        <div class="title">编辑员工资料</div>
    </div>
    <div class="mod">
		<div class="info-edit clearfix">
			<div class="left pull-left">
				<!--上传头像前-->
				<div class="my_info" style="margin-bottom: 0;width: 140px;">
					<div class="my_tx">
						<div class="tx_normal">
							<form name='fileform_photo' id='fileform_photo' action='/organization/upload_head' enctype='multipart/form-data' target='filepost_iframe' method='post'>
							    <input type="hidden" name="broker_id" id="broker_id" value="<?=$broker_info['broker_id']?>">
                                <input type="hidden" name="fileurl" id="fileurl" value="<?=$broker_info['photo']?>">
								<?php if($broker_info['photo']) {?>
								<img class="myself_photo_a" id="photo_replace" src="<?=$broker_info['photo']?>" width="130" height="170"/>
								<div class="show_editor_remove" style="display: block">
									<span class="modify"><input name="photofile_modify" id="photofile" type="text" class=""></span>
									<span class="remove" style="border-right:none;" onclick="remove_photofile()">删除</span>
                                    <script>createSwf(50, 34, "修改");</script>
								</div>
								<input type='hidden' name='action' value='photofile_modify' />
								<input type='hidden' name='div_id' value='photo_replace' />
								<?php }else{?>
								<div class="zhanwei" width="130" height="170">
									 <input name="photofile_add" id="photofile" type="text" class="file_input">
                                    <script>createSwf(130,170, "");</script>
								</div>
								<input type='hidden' name='action' value='photofile_add' />
								<?php }?>
							</form>
						</div>
					</div>
				</div>
				<p class="c999">提示：</br>
				1.图片小于2M</br>
				2.支持JPG和BMPG格式</br>
				3.尺寸大小130*170px</p>
			</div>
			<table class="fl">
				<tr>
					<td>员工姓名：</td>
					<td width="245"><input type="text" class="hr_framework_text fl" id="truename" value="<?=$broker_info['truename']?>" <?=($broker_info['group_id']==2)?"readonly style='color:#c9c9c9'":''?>></td>
					<td>出生日期：</td>
					<td><input type="text" class="hr_framework_text hr_framework_timebg fl" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id="birthday" value="<?=$broker_info['birthday']?>"></td>
				</tr>
				<tr>
					<td>性别：</td>
					<td>
						<input type="radio" name="sex" value="0" class="find_call" <?=($broker_info['sex']==0)?"checked":""?> >男
						<input type="radio" name="sex" value="1" class="find_call" <?=($broker_info['sex']==1)?"checked":""?> >女
					</td>
					<td>手机号码：</td>
					<td><input type="text" class="hr_framework_text fl" id="phone" value="<?=$broker_info['phone']?>" readonly style="color:#c9c9c9"></td>
				</tr>
				<tr>
					<td>职务：</td>
					<td>
						<?php if($_SESSION['mls_user']['broker_id']==$edit_broker_id && $level==1){?>
							<select class="hr_framework_sel fl" id="system_group_id" disabled style="color:#c9c9c9">
								<?php
								foreach($permission_group as $p){
									if($level<=$p['level']){
								?>
								<option value="<?=$p['id']?>"
								<?=($p['id']==$system_group_id['system_group_id'])?"selected":""?>
								><?=$p['name']?></option>
								<?php }} ?>
							</select>
						<?php }else {?>
							<select class="hr_framework_sel fl" id="system_group_id">
								<?php
								foreach($permission_group as $p){
									if($level<=$p['level'] && $p['level']!= "1"){
								?>
								<option value="<?=$p['id']?>"
								<?=($p['id']==$system_group_id['system_group_id'])?"selected":""?>
								><?=$p['name']?></option>
								<?php }} ?>
							</select>
						<?php }?>
					</td>
					<td>电子邮箱：</td>
					<td><input type="text" class="hr_framework_text fl" id="email" value="<?=$broker_info['email']?>"  ></td>
				</tr>
				<tr>
					<td>所在门店：</td>
					<td>
					<input type="hidden" class="account_agency_id" value="<?=$agency_id?>">
						<select id="account_agency_id" class="hr_framework_sel fl">
						<?php
						if(is_array($company_info_account) && !empty($company_info_account)){
							foreach (array_reverse($company_info_account) as $key =>$value) {
								if($key < count($company_info_account)-1){

						?>
							<option value="<?=$value['id'] ?>" <?=($value['id']==$agency_id)?"selected":""?>>├─<?=$value['name']?></option>
							<?php
								if(isset($value['next_agency_data']) && !empty($value['next_agency_data'])){
									foreach ($value['next_agency_data'] as $k => $v){
							?>
							<option value="<?=$v['id'] ?>" <?=($v['id']==$agency_id)?"selected":""?>>　├─<?=$v['name']?></option>
							<?php
									}
								}
							?>
						<?php
							  }else{
						?>
							<option value="<?=$value['id'] ?>" <?=($value['id']==$agency_id)?"selected":""?>>└─<?=$value['name']?></option>
							<?php
								if(isset($value['next_agency_data']) && !empty($value['next_agency_data'])){
									foreach ($value['next_agency_data'] as $k => $v){
							?>
							<option value="<?=$v['id'] ?>" <?=($v['id']==$agency_id)?"selected":""?>>　├─<?=$v['name']?></option>
							<?php
									}
								}
							?>
						<?php
							  }
							}
						}
						?>
						</select>
					</td>
					<td>邮编：</td>
					<td><input type="text" class="hr_framework_text fl" id="postcode" value="<?=$broker_info['postcode']?>" ></td>
				</tr>
				<tr>
					<td>身份证号：</td>
					<td><input type="text" class="hr_framework_text fl" id="idno" value="<?=$broker_info['idno']?>"></td>
					<td>最高学历：</td>
					<td><input type="text" class="hr_framework_text fl" id="diploma" value="<?=$broker_info['diploma']?>"></td>
				</tr>
				<tr>
					<td>入职时间：</td>
					<td><input type="text" class="hr_framework_text hr_framework_timebg fl" onfocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" id="joinjob" value="<?=$broker_info['joinjob']?>"></td>
					<td >从业时间：</td>
					<td >
						<select class="hr_framework_sel fl" id="work_time">
							<?php
							if(is_full_array($work_time)){
								foreach($work_time as $key=>$vo){
							?>
							<option value="<?=$key?>" <?=($broker_info['work_time'] == $key)?'selected':''?>><?=$vo?></option>
							<?php }}?>
						</select>
					</td>
				</tr>
				<tr>
					<td>业务QQ：</td>
					<td><input type="text" class="hr_framework_text fl" id="qq" value="<?=$broker_info['qq']?>" ></td>
					<td rowspan="2">自我简介：</td>
					<td rowspan="2"><textarea class="hr_framework_text" id='remark'><?=$broker_info['remark']?></textarea></td>
				</tr>
				<tr>
					<td>家庭地址：</td>
					<td><input type="text" class="hr_framework_text fl" id="address" value="<?=$broker_info['address']?>"></td>
				</tr>
				<tr>
					<td>毕业院校：</td>
					<td><input type="text" class="hr_framework_text fl" id="graduate" value="<?=$broker_info['graduate']?>"></td>
					<td>新密码</td>
					<td><input type="text" class="hr_framework_text fl" id="address" value="<?=$broker_info['address']?>"></td>

				</tr>
				<tr>
					<td>外网展示：</td>
					<td>
						<input type="radio" name="is_show_c" value="1" class="find_call" <?=($broker_info['is_show_c']==1)?"checked":""?> >是
						<input type="radio" name="is_show_c" value="2" class="find_call" <?=($broker_info['is_show_c']==2)?"checked":""?> >否
					</td>
					<td>确认密码</td>
					<td><input type="text" class="hr_framework_text fl" id="address" value="<?=$broker_info['address']?>"></td>
				</tr>

			</table>
		</div>
		<div class="center mt20">
			<button class="btn-lv1 btn-left" type="button" onclick="replace_info_pop()">确定</button>
			<button class="btn-hui1 JS_Close" type="button" onclick="parent.window.location.reload(true)">取消</button>
		</div>
    </div>
</div>
<!--有房源用户修改门店弹窗-->
<input type="hidden" id="house_num" value="<?=$house_num?$house_num:0?>">
<input type="hidden" id="customer_num" value="<?=$customer_num?$customer_num:0?>">
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_edit1">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14" valign="top"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
							<p class="left" style="color:#666;">员工 <strong><?=$broker_info['truename']?></strong> 名下仍有<strong class="f00" ><?=$house_num?$house_num:0?></strong>套房源、<strong class="f00" ><?=$customer_num?$customer_num:0?></strong>个客源信息，若更换门店后将会跟随其一起转移，是否仍然更换门店？</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left" type="button" onclick="replace_info();">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_edit2">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td class="c14">	<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></td>
                        <td>
							<p class="left" style="font-size:14px;color:#666;">您确定修改吗?</p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left" type="button" onclick="replace_info();">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
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


    //更改头像及其身份资质认证
    function changePic(fileurl,div_id){
        $("#"+div_id).attr("src",fileurl);
    }
    //添加头像调用
    function changePhoto(fileurl){

        var photo_addHtml = "<input type='hidden' name='broker_id' id='broker_id' value='<?=$broker_info['broker_id']?>'>";
        photo_addHtml += "<input type='hidden' name='fileurl' id='fileurl' value='"+fileurl+"'>";
    	photo_addHtml += "<img class='myself_photo_a' id='photo_replace' src='"+fileurl+"' width='130' height='170'/>";
    	photo_addHtml += "<div class='show_editor_remove' style='display: block'>";
    	photo_addHtml += "<span class='modify'><input name='photofile_modify' id='photofile' type='text' class='file_input'></span>";
    	photo_addHtml += "<span class='remove' style='border-right:none;' onclick='remove_photofile()'>删除</span></div>";
    	photo_addHtml += "<input type='hidden' name='action' value='photofile_modify' /><input type='hidden' name='div_id' value='photo_replace' />";
    	$("#fileform_photo").html(photo_addHtml);
        createSwf(50, 34, "修改");
    }
    //去除头像
    function remove_photofile()
    {
        var photo_modifyHtml = "<input type='hidden' name='broker_id' id='broker_id' value='<?=$broker_info['broker_id']?>'>";
        photo_modifyHtml += "<input type='hidden' name='fileurl' id='fileurl' value=''>";
    	photo_modifyHtml += "<div class='zhanwei' width='130' height='170'>";
    	photo_modifyHtml += "<input type='text' name='photofile_add' class='file_input' id='photofile'>";
    	photo_modifyHtml += "</div><input type='hidden' name='action' value='photofile_add' />";
    	$("#fileform_photo").html(photo_modifyHtml);
       createSwf(130,170, "");
    }

	//提交修改资料信息弹窗
	function replace_info_pop(){
		var agency_id_old = $(".account_agency_id").val();
		var agency_id_new = $("#account_agency_id").val();
		var house_num = $("#house_num").val();
		var customer_num = $("#customer_num").val();
		//alert(sell_house_num);
		//alert(rent_house_num);
		if(agency_id_old != agency_id_new){
			if(house_num!=0 || customer_num!=0){
				openWin('js_edit1');
			}else{
				openWin('js_edit2');
			}
		}else{
			openWin('js_edit2');
		}

	}


	//提交修改资料信息
	function replace_info()
	{
        var photo = $("#fileurl").val();
		var truename = $("#truename").val();
		var sex = $("input[name='sex']:checked").val();
		var is_show_c = $("input[name='is_show_c']:checked").val();
		var system_group_id = $("#system_group_id").val();
		if(system_group_id == null || system_group_id == undefined || system_group_id == ''){
			system_group_id = <?php echo $level ?>;
		}
		var idno = $("#idno").val();
		var birthday = $("#birthday").val();
		var joinjob = $("#joinjob").val();
		var address = $("#address").val();
		var postcode = $("#postcode").val();
		var graduate = $("#graduate").val();
		var diploma = $("#diploma").val();
		var phone = $("#phone").val();
		var broker_id = $("#broker_id").val();
		var qq = $("#qq").val();
		var work_time = $("#work_time").val();
		var email = $("#email").val();
		var remark = $("#remark").val();
		var agency_id_old = $(".account_agency_id").val();
		var agency_id = $("#account_agency_id").val();
		var house_num = $("#house_num").val();
		var customer_num = $("#customer_num").val();
        var data = {photo:photo,truename:truename,sex:sex,system_group_id:system_group_id,idno:idno,birthday:birthday,joinjob:joinjob,address:address,postcode:postcode,graduate:graduate,diploma:diploma,phone:phone,broker_id:broker_id,agency_id:agency_id,agency_id_old:agency_id_old,house_num:house_num,customer_num:customer_num,qq:qq,email:email,remark:remark,is_show_c:is_show_c,work_time:work_time};
    	$.ajax({
    		type: "POST",
    		url: "/organization/update_detail",
    		dataType:"json",
    		data:data,
    		success: function(data){
				//alert(data);
    			if(data['errorCode'] == '401')
                {
                    login_out();
                    $("#jss_pop_tip").hide();
                }
                else if(data['errorCode'] == '403')
                {
                    /*permission_none();
                    $("#jss_pop_tip").hide();*/
                	closeWindowWin('js_add_shop');
                	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                    openWin('js_pop_do_warning');return false;
                }else{
                	if(data.status=="success"){
            			$("#dialog_do_success_tip").html(data.msg);
						$("#js_edit1").hide();
						$("#js_edit2").hide();
                		openWin('js_pop_do_success');
            		}else{
            			$("#dialog_do_warnig_tip").html(data.msg);
						$("#js_edit1").hide();
						$("#js_edit2").hide();
                		openWin('js_pop_do_warning');
            		}
                }
    		}
    	});

    }

</script>
</body>
</html>
