<script>
    window.parent.addNavClass(10);
</script>
<head>
<meta charset="utf-8">
<title>个人中心---个人资料</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/personal_new.css" rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/integral.css" rel="stylesheet" type="text/css">
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/swf/swfupload.js&debug=true"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/uploadimg_codi.js&debug=true"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/personal_center.js,mls/js/v1.0/house.js"></script>

<script type="text/javascript">
  //window.parent.addNavClass(10);
  $(function () {

    var regExp_pic = /(.jpg|.JPG|.png|.PNG)$/;
    //门店二维码弹框
    $('#agency_wei_down').live('click', function () {
      openWin('wei_pic_down');
    });
    //门店二维码下载表单提交
    $('#wei_pic_down_button').live('click', function () {
      $('#pic_deal').submit();
    });
    //门店二维码下载表单提交
    $('#broker_wei_down').live('click', function () {
      $('#broker_wei_down_form').submit();
    });

    $("#headfile").live("change", function () {
      var file = $(this).val();
      if (file != "") {
        var patrn = regExp_pic;
        if (patrn.exec(file)) {
          $("#fileform_head").submit();
        }
        else {
          $("#dialog_do_warnig_tip").html("图片格式不正确,请重新选择");
          $(this).val("");
          openWin('js_pop_do_warning');
          return false;
        }
      }
    });

    $("#headfile_new").live("change", function () {
      var file = $(this).val();
      if (file != "") {
        var patrn = regExp_pic;
        if (patrn.exec(file)) {
          $("#fileform_head_new").submit();
        }
        else {
          $("#dialog_do_warnig_tip").html("图片格式不正确,请重新选择");
          $(this).val("");
          openWin('js_pop_do_warning');
          return false;
        }
      }
    });

    $("#idnofile").live("change", function () {
      var file = $(this).val();
      if (file != "") {
        var patrn = regExp_pic;
        if (patrn.exec(file)) {
          $("#fileform_idno").submit();
        }
        else {
          $("#dialog_do_warnig_tip").html("图片格式不正确,请重新选择");
          $(this).val("");
          openWin('js_pop_do_warning');
          return false;
        }
      }
    });
    $("#idnofile_new").live("change", function () {
      var file = $(this).val();
      if (file != "") {
        var patrn = regExp_pic;
        if (patrn.exec(file)) {
          $("#fileform_idno_new").submit();
        }
        else {
          $("#dialog_do_warnig_tip").html("图片格式不正确,请重新选择");
          openWin('js_pop_do_warning');
          $(this).val("");
          return false;
        }
      }
    });
    $("#cardfile").live("change", function () {
      var file = $(this).val();
      if (file != "") {
        var patrn = regExp_pic;
        if (patrn.exec(file)) {
          $("#fileform_card").submit();
        }
        else {
          $("#dialog_do_warnig_tip").html("图片格式不正确,请重新选择");
          $(this).val("");
          openWin('js_pop_do_warning');
          return false;
        }
      }
    });
    $("#cardfile_new").live("change", function () {
      var file = $(this).val();
      /*	var broker_info_auth = $("#broker_info_auth").val();
       if(broker_info_auth == 0){
       openWin('ident_auth_status_warning');
       return false;
       }*/
      if (file != "") {
        var patrn = regExp_pic;
        if (patrn.exec(file)) {
          $("#fileform_card_new").submit();
        }
        else {
          $("#dialog_do_warnig_tip").html("图片格式不正确,请重新选择");
          $(this).val("");
          openWin('js_pop_do_warning');
          return false;
        }
      }
    });

    $('#new_phone').bind('blur', function () {
      validate_phone();
    });

    $('#new_phone').bind('blur', function () {
      validate_code();
    });

    //更换手机号码
    $('#modify_phone').bind('click', function () {
      openWin('modify_phone_pop');
      $('#old_phone').html($('#modify_phone').attr('value'));
    });

    $('#submit_modify_phone').bind('click', function () {
      if (validate_phone() && validate_code()) {
        $.ajax({
          type: 'get',
          url: '/my_info/modify_phone/',
          data: {phone: $('#new_phone').val(), 'validcode': $('#validcode').val()},
          dataType: 'json',
          success: function (data) {
            if (data.status == 0) {
              $('#phone_error').html(data.msg);
              $('#new_phone').focus();
            }
            else {
              openWin('js_pop_do_modify_success');
              setTimeout(function () {
                external.go2Login();
              }, 2000);
            }
          }
        });
      }
    });

    var getValidcodeKey = 0;
    //获取验证码
    $('#getValidcode').bind('click', function () {
      if (getValidcodeKey > 0) {
        return false;
      }
      if (!validate_phone()) {
        return false
      }
      $.ajax({
        type: 'get',
        url: '/broker_sms/',
        data: {old_phone: $('#old_phone').html(), phone: $('#new_phone').val(), type: 'modify_phone'},
        dataType: 'json',
        success: function (data) {
          if (data.status !== 1) {
            $('#phone_error').html(data.msg);
            $('#new_phone').focus();
          } else {
            getValidcodeKey = 60;
            //$('#getValidcode').addClass('get_code_none');
            $('#getValidcode').html(getValidcodeKey + '秒后重新获取');
            var oTime = setInterval(function () {
              getValidcodeKey--;
              if (getValidcodeKey > 0) {
                $('.btn-hui2').css('color', '#999');
                $('.btn-hui2').css('cursor', 'default');
                $('#getValidcode').html(getValidcodeKey + '秒后重新获取');
              }
              else {
                clearInterval(oTime);
                $('.btn-hui2').css('color', '#535353');
                $('.btn-hui2').css('cursor', 'pointer');
                $('#getValidcode').html('获取验证码');
                //$('#getValidcode').removeClass('get_code_none');
              }
            }, 1000);
          }
        }
      });
    });

    //修改个人资料
    //弹框
    $("#modify_person").bind('click', function () {
      $.ajax({
        type: 'get',
        url: '/my_info/modify_person/',
        data: {type: 'search'},
        dataType: 'json',
        success: function (data) {
          if (data) {
            $("#work_time").val(data.work_time);
            $("#weixin").val(data.weixin);
            $("#businesses").val(data.businesses);
            $("#weixin_error").html(" ");
            $("#businesses_error").html(" ");
            openWin('modify_person_pop');
          }
        }
      });

    })
  });

  function check_num() {
    var weixin = $("#weixin").val();
    var businesses = $("#businesses").val();
    if (weixin.length > 20) {
      $("#weixin_error").html("最多填写20个字！");
      $("input[name='is_submit_w']").val('0');
    } else {
      $("#weixin_error").html(" ");
      $("input[name='is_submit_w']").val('1');
    }
    if (businesses.length > 50) {
      $("#businesses_error").html("最多填写50个字！");
      $("input[name='is_submit_b']").val('0');
    } else {
      $("#businesses_error").html(" ");
      $("input[name='is_submit_b']").val('1');
    }
  }

  //通过参数判断是否可以被提交
  function modify_person() {
    var is_submit_w = $("input[name='is_submit_w']").val();
    var is_submit_b = $("input[name='is_submit_b']").val();
    if (is_submit_w == 1 && is_submit_b == 1) {
      var work_time = $("#work_time").val();
      var weixin = $("#weixin").val();
      var businesses = $("#businesses").val();
      $.ajax({
        type: 'get',
        url: '/my_info/modify_person/',
        data: {work_time: work_time, weixin: weixin, businesses: businesses},
        dataType: 'json',
        success: function (data) {
          if (data.status == 0) {
            $('#js_pop_do_error_person .bold').html(data.msg);
            openWin('js_pop_do_error_person');
          }
          else {
            $("#modify_person_pop").hide();
            $("#GTipsCovermodify_person_pop").hide();
            openWin('js_pop_do_success_person');
          }
        }
      });
    }
  }
  function validate_phone() {
    var rgExp = /^1\d{10}$/;
    if (!rgExp.test($('#new_phone').val())) {
      $('#phone_error').html('请输入联系人正确的手机号');
      $('#new_phone').focus();
      return false;
    }
    if ($('#new_phone').val() == $('#old_phone').html()) {
      $('#phone_error').html('新号码和旧号码相同');
      $('#new_phone').focus();
      return false;
    }
    $('#phone_error').html('');
    return true;
  }

  function validate_code() {
    if ($.trim($("#validcode").val()).length < 0) {
      $('#phone_error').html('请输入收到的短信验证码');
      $('#validcode').focus();
      return false;
    }
    return true;
  }
</script>
</head>
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
<body>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<script type="text/javascript">
  $('#attendance_app').click(function(){
    var attendance_app_auth = '<?php echo $attendance_app_auth['auth']; ?>';
    if (attendance_app_auth) {
      return true;
    }else{
      permission_none();
      return false;
    }
    
  });
</script>
<div class="personal-out" id="js_inner">
<div class="personal-center" id="js_inner">
	<div class="personal-pane1 clearfix">
		<div class="right">
      <div class="zws_adjust_person">
        <h3 class="clearfix"><span class="fl"><?=$truename ?></span>
				<span class="cur_bg_c" style="float:left;padding:0 10px;font-size: 12px;font-family: Arial;font-weight: bold;color: #FFF;text-align: center;font-style: italic;_background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/IE6_level_bj2_03.jpg) no-repeat center;margin-right:10px;display:inline;cursor:pointer" class="fl" onclick="openWin('my_level')" >Lv<?=$level['level']?></span><?=$trust_level['level']?></h3>
					<!--第一次认证：-->
					<?php if($ident_auth_status == 0 || $ident_auth_status == 4){?>
					<div class="clearfix personal-rz"><span class="sf_rz_code">身份未认证</span><span class="zz_rz_code">资质未认证</span><span class="per-tip">实名认证通过后，您可以使用更多功能！</span></div>
					<?php }elseif($ident_auth_status == 3){ ?>
					<!--需重新认证：-->
					<?php if($ident_remark == ''){ $ident_remark = '认证资料信息不全'; } ?>
					<div class="clearfix personal-rz personal-rz2"><span class="sf_rz_code">身份未认证</span><span class="zz_rz_code">资质未认证</span><span class="per-tip">审核失败原因：<?=$ident_remark?></span></div>
					<?php }elseif($ident_auth_status == 1){ ?>
					<!--审核中-->
					<div class="clearfix personal-rz personal-rz3"><span class="sf_rz_code">身份审核中</span><span class="zz_rz_code">资质审核中</span><span class="per-tip">客服人员将在1-2个工作日内审核完毕并通知您审核结果。</span></div>
					<?php }else{ ?>
					<!--已认证-->
					<div class="clearfix personal-rz personal-rz4"><span class="sf_rz_code">身份已认证</span><span class="zz_rz_code">资质已认证</span><span class="per-tip">恭喜您已认证成功！</span></div>
					<?php }?>

					<p class="per-info">
                        <!--						我的积分：--><? //=$credit?>
                        <!--						<a href="javascript:void(0);" style='margin-left:10px' onclick="openWin('js_pop_protocol')">如何获取积分</a>-->
                        <!--						<br>-->
						绑定手机：<?=$phone ?>
						<a href="javascript:void(0);"  id="modify_phone" value="<?=$phone ?>">更换号码</a> | <a href="javascript:void(0);"  id="modify_person" >个人资料</a>
						<br>
						所属门店：<?=$agency_name ?>　<a href="javascript:void(0);" class="per-hover">如何更换公司和门店？</a><br>
						服务区属：<?=$dist_name.'-'.$street_name ?>
						<br>
						<?php if ($master_id > 0) {?>
							客户经理：<?=$master_name?> <?=$master_telno?>
						<?php } else { ?>
							客服电话：<?=$tel400?>
						<?php } ?>
						<br>
              当前登录IP：<?=$ip?>
						</p>
				</div>
        <div class="zws_adjust_person_ewm">
            <form name="broker_wei_down_form" id="broker_wei_down_form" action="/my_info/broker_wei_down">
                <input type="hidden" value="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/ceshi.png" name="broker_wei_src" />
            </form>
            <?php if(!empty($wximg)){ ?>
            <span>
                <b>经纪人二维码</b>
                <img width="120px" height="120px" src="<?php echo $wximg; ?>" alt="经纪人二维码" />
                <strong>扫一扫查看本人房源</strong>
                <p class="zws_adjust_person_ewm_btn" id="broker_wei_down">下载</p>
            </span>
            <?php } ?>
            <?php if(!empty($wximg2)){ ?>
            <span>
                <b>门店二维码</b>
                <img width="120px" height="120px" src="<?php echo $wximg2; ?>" alt="经纪人二维码" />
                <strong>扫一扫查看门店房源</strong>
                <p class="zws_adjust_person_ewm_btn" id="agency_wei_down">打印</p>
            </span>
            <?php } ?>
        </div>
		</div>
		<?php if($photo) {?>
			<img class="personal-tx"src="<?=$photo?>" width="115" height="150"/>
		<?php }else{?>
			<img class="personal-tx" width="115" height="150" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/default.png">
		<?php }?>
		<div class="r_s_popUP">
				<div class="replace_stores_popUp">
					<i class="upgou"> </i>
					<dl class="r_s_dl clearfix">
						<dt class="r_dt">未认证用户<span class="gray">：</span></dt>
						<dd class="r_dd">可提交认证资料，客服审核后按资料进行修改。若不进行认证，请拨打客服<?=$tel400?>进行修改。</dd>
					</dl>
					<dl class="r_s_dl_1 mt20 clearfix">
						<dt class="r_dt">已认证用户更改公司内门店<span class="gray">：</span></dt>
						<dd class="r_dd">请找公司管理员在人事管理中进行修改，如果没有公司管理权限帐号，请重新提交资质认证材料，客服会第一时间重新审核。</dd>
					</dl>
					<dl class="r_s_dl_1 clearfix">
						<dt class="r_dt">已认证用户更换公司<span class="gray">：</span></dt>
						<dd class="r_dd">请联系公司管理帐号将本帐号在该公司内注销，后重新提交资料认证。若无公司管理权限帐号，请直接提交认证资料，客服会第一时间进行审核。</dd>
					</dl>
				</div>
			</div>
	</div>
	<div class="personal-pane2">
        <!--		<img class="per-img" src="--><?php //echo MLS_SOURCE_URL;?><!--/mls/images/v1.0/per_img2.jpg">-->

		<!-- 未认证 或 注销 -->
		<?php if($ident_auth_status == 0 || $ident_auth_status == 4){?>
		<div class="per-inner">
			<h3>身份认证</h3>
			<table>
				<tr>
					<td class="td1">标准头像：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:81px; height: 113px;">
                <div id="headpic_previewBoxM" style="display:none" ></div>
                <div id="headpic_img" class="headpic-img">
                  <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/per_up1.png" width="81" height="113"/>
                </div>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="headfile" id="headfile_new" type="button" class="file_input mt10">
              </a>
              <img style="margin-top:5px;" title="头像将在外网显示，重新上传需认证通过后显示才会变化。头像认证不影响相关权限操作。" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_head = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "headpic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "headpic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : uploadSuccessNew,
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "headfile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="81" height="113" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/touxiangfl.gif">
						<p class="per-ys">
							提示：<br>
							1.头像标准1寸电子照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
				<tr>
					<td class="td1">身份证照：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div id="idnopic_previewBoxM" style="display:none" ></div>
                <div id="idnopic_img" class="idnopic-img">
                  <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/per_up3.png" width="180" height="115"/>
                </div>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="idnofile" id="idnofile_new" type="button" class="file_input mt10">
              </a>
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_idno = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "idnopic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "idnopic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : uploadSuccessNew,
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "idnofile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/sfrzfl.gif">
						<p class="per-ys">
							提示：<br>
							1.身份证标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>

				<tr>
					<td class="td1">身份证号：</td>
					<td width="238">
						<input class="input-text" id="idno" type="text">
						<p id="errop-tip"></p>
					</td>
					<td></td>
				</tr>
			</table>
			<h3>资质认证</h3>
			<table>
				<tr>
					<td class="td1">个人名片：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div id="cardpic_previewBoxM" style="display:none" ></div>
                <div id="cardpic_img" class="cardpic-img">
                  <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/per_up3.png" width="180" height="115"/>
                </div>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="cardfile" id="cardfile_new" type="button" class="file_input mt10">
              </a>
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_card = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "cardpic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "cardpic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : uploadSuccessNew,
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "cardfile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/grmpfl.gif">
						<p class="per-ys">
							提示：<br>
							1.名片标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
			</table>
		</div>
		<div class="per-btn">
			<a class="btn-lv-big btn-mid ident_save" href="javascript:void(0);" onclick="ident_save()"><span class="btn_inner">提交认证审核</span></a>
		</div>
		<!-- 审核中 -->
		<?php }elseif($ident_auth_status == 1){?>
		<div class="per-inner">
		<h3>身份认证</h3>
			<table>
				<tr>
					<td class="td1">标准头像：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:81px; height: 113px;">
                <div class="headpic-img">
                  <img class="per-up-img" src="<?=$headshots_photo ?>" width="81" height="113"/>
                </div>
                <span class="per-pos-img per-pos-img-shz"></span>
              </div>
            </div>
					</td>
					<td>
						<img class="fl" width="81" height="113" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/touxiangfl.gif">
						<p class="per-ys">
							提示：<br>
							1.头像标准1寸电子照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
				<tr>
					<td class="td1">身份证照：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div class="idnopic-img">
                  <img class="per-up-img" src="<?=$idno_photo ?>" width="180" height="115"/>
                </div>
                <span class="per-pos-img per-pos-img-shz"></span>
              </div>
            </div>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/sfrzfl.gif">
						<p class="per-ys">
							提示：<br>
							1.身份证标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>

				<tr>
					<td class="td1">身份证号：</td>
					<td width="238">
						<p class="input-text"><?=$idno ?></p>
					</td>
					<td></td>
				</tr>
			</table>
			<h3>资质认证</h3>
			<table>
				<tr>
					<td class="td1">个人名片：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div id="cardpic_img" class="cardpic-img">
                  <img class="per-up-img" src="<?=$card_photo ?>" width="180" height="115"/>
                </div>
                <span class="per-pos-img per-pos-img-shz"></span>
              </div>
            </div>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/grmpfl.gif">
						<p class="per-ys">
							提示：<br>
							1.名片标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
			</table>
		</div>
		<!-- 认证成功 -->
		<?php }elseif($ident_auth_status == 2){?>
		<div class="per-inner">
		<h3>身份认证</h3>
			<table>
			<?php if($head_auth_status == 2 || $head_auth_status == 3 || $head_auth_status == 0){?>
				<tr>
					<td class="td1">标准头像：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:81px; height: 113px;">
                <div id="headpic_previewBoxM" style="display:none" ></div>
                <div id="headpic_img" class="headpic-img">
                  <?php if($headshots_photo){?>
                    <img class="img-upload-old" src="<?=$headshots_photo ?>" width="81" height="113"/>
                  <?php }?>
                </div>
                <span class="per-pos-img per-pos-img-yrz" id="headpic_yrz"></span>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="headfile" id="headfile_new" type="button" class="file_input mt10">
              </a>
              <img style="margin-top:5px;" title="头像将在外网显示，重新上传需认证通过后显示才会变化。头像认证不影响相关权限操作。" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_head = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "headpic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "headpic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : function(file, serverData) {
                      uploadSuccessNew.apply(this, arguments);
                    changePic('headpic');
                  },
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "headfile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">修改头像</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="81" height="113" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/touxiangfl.gif">
						<p class="per-ys">
							提示：<br>
							1.头像标准1寸电子照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
			<?php }elseif($head_auth_status == 1){?>
				<tr>
					<td class="td1">标准头像：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:81px; height: 113px;">
                <div id="headpic_previewBoxM" style="display:none" ></div>
                <div id="headpic_img" class="headpic-img">
                  <?php if($head_info_pic){?>
                    <img class="img-upload-old" src="<?=$head_info_pic ?>" width="81" height="113"/>
                  <?php }?>
                </div>
                <span class="per-pos-img per-pos-img-shz" id="headpic_shz"></span>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="headfile" id="headfile_new" type="button" class="file_input mt10">
              </a>
              <img style="margin-top:5px;" title="头像将在外网显示，重新上传需认证通过后显示才会变化。头像认证不影响相关权限操作。" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_head = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "headpic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "headpic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : function(file, serverData) {
                      uploadSuccessNew.apply(this, arguments);
                    changePic('headpic');
                  },
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "headfile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">修改头像</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="81" height="113" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/touxiangfl.gif">
						<p class="per-ys">
							提示：<br>
							1.头像标准1寸电子照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
			<?php } ?>
				<tr>
					<td class="td1">身份证照：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div id="idnopic_previewBoxM" style="display:none" ></div>
                <div id="idnopic_img" class="idnopic-img">
                  <?php if($idno_photo){?>
                    <img class="img-upload-old" src="<?=$idno_photo ?>" width="180" height="115"/>
                  <?php }?>
                </div>
                <span class="per-pos-img per-pos-img-yrz" id="idnopic_yrz"></span>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="idnofile" id="idnofile_new" type="button" class="file_input mt10">
              </a>
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_idno = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "idnopic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "idnopic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : function(file, serverData) {
                      uploadSuccessNew.apply(this, arguments);
                    changePic('idnopic');
                  },
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "idnofile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">重新上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/sfrzfl.gif">
						<p class="per-ys">
							提示：<br>
							1.身份证标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>

				<tr>
					<td class="td1">身份证号：</td>
					<td width="238">
						<input class="input-text" type="text" id="idno" value="<?=$idno ?>">
						<p id="errop-tip"></p>
					</td>
					<td></td>
				</tr>
			</table>
			<h3>资质认证</h3>
			<table>
				<tr>
					<td class="td1">个人名片：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div id="cardpic_previewBoxM" style="display:none" ></div>
                <div id="cardpic_img" class="cardpic-img">
                  <?php if($card_photo){?>
                    <img class="img-upload-old" src="<?=$card_photo ?>" width="180" height="115"/>
                  <?php }?>
                </div>
                <span class="per-pos-img per-pos-img-yrz" id="cardpic_yrz"></span>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="cardfile" id="cardfile_new" type="button" class="file_input mt10">
              </a>
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_card = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "cardpic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "cardpic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : function(file, serverData) {
                      uploadSuccessNew.apply(this, arguments);
                    changePic('cardpic');
                  },
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "cardfile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">重新上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/grmpfl.gif">
						<p class="per-ys">
							提示：<br>
							1.名片标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
			</table>
		</div>
		<!-- 有效 -->
		<div class="per-btn" style="display: none;" id="effective">
			<a class="btn-lv-big btn-mid quali_modify_success" href="javascript:void(0);" onclick="quali_modify()">
			<span class="btn_inner">重新提交审核</span></a>
		</div>
		<!-- 失效 -->
		<div class="per-btn" id="invalid">
			<a class="btn-hui-big btn-mid quali_modify_success" href="javascript:void(0);" onclick="return false">
			<span class="btn_inner">重新提交审核</span></a>
		</div>
		<!-- 审核失败 -->
		<?php }elseif($ident_auth_status == 3){?>
		<div class="per-inner">
		<h3>身份认证</h3>
			<table>
				<tr>
					<td class="td1">标准头像：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:81px; height: 113px;">
                <div id="headpic_previewBoxM" style="display:none" ></div>
                <div id="headpic_img" class="headpic-img">
                  <?php if($headshots_photo){?>
                    <img class="img-upload-old" src="<?=$headshots_photo ?>" width="81" height="113"/>
                  <?php }?>
                </div>
                <span class="per-pos-img per-pos-img-wtg" id="headpic_wtg"></span>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="headfile" id="headfile_new" type="button" class="file_input mt10">
              </a>
              <img style="margin-top:5px;" title="头像将在外网显示，重新上传需认证通过后显示才会变化。头像认证不影响相关权限操作。" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico2.png">
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_head = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "headpic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "headpic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : function(file, serverData) {
                      uploadSuccessNew.apply(this, arguments);
                    changePic('headpic');
                  },
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "headfile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">重新上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="81" height="113" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/touxiangfl.gif">
						<p class="per-ys">
							提示：<br>
							1.头像标准1寸电子照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
				<tr>
					<td class="td1">身份证照：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div id="idnopic_previewBoxM" style="display:none" ></div>
                <div id="idnopic_img" class="idnopic-img">
                  <?php if($idno_photo){?>
                    <img class="img-upload-old" src="<?=$idno_photo ?>" width="180" height="115"/>
                  <?php }?>
                </div>
                <span class="per-pos-img per-pos-img-wtg" id="idnopic_wtg"></span>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="idnofile" id="idnofile_new" type="button" class="file_input mt10">
              </a>
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_idno = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "idnopic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "idnopic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : function(file, serverData) {
                      uploadSuccessNew.apply(this, arguments);
                    changePic('idnopic');
                  },
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "idnofile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">重新上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/sfrzfl.gif">
						<p class="per-ys">
							提示：<br>
							1.身份证标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>

				<tr>
					<td class="td1">身份证号：</td>
					<td width="238">
						<input class="input-text" id="idno" type="text" value="<?=$idno ?>">
						<p id="errop-tip"></p>
					</td>
					<td></td>
				</tr>
			</table>
			<h3>资质认证</h3>
			<table>
				<tr>
					<td class="td1">个人名片：</td>
					<td width="238" height="160">
            <div class="div-pos">
              <div class="per-up-img" style="width:180px; height: 115px;">
                <div id="cardpic_previewBoxM" style="display:none" ></div>
                <div id="cardpic_img" class="cardpic-img">
                  <?php if($card_photo){?>
                    <img class="img-upload-old" src="<?=$card_photo ?>" width="180" height="115"/>
                  <?php }?>
                </div>
                <span class="per-pos-img per-pos-img-wtg" id="cardpic_wtg"></span>
              </div>
              <a class="btn-lan5 fl" style="position: relative; margin-right:10px;" href="javascript:void(0);">
                <input name="cardfile" id="cardfile_new" type="button" class="file_input mt10">
              </a>
            </div>

            <script type="text/javascript">
              $(function() {
                var swfu_card = new SWFUpload({
                  // Backend Settings
                    file_post_name: "file",
                    upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                  //post_params: {"PHPSESSID": "5onmcek5m1qsu5e5nor2tiq325"},
                  //post_params: {"postion" : position},
                  // File Upload Settings
                  file_size_limit : "5 MB",
                  file_types : "*.jpg;*.png",
                  file_types_description : "JPG Images",
                  file_upload_limit : 0,
                  file_queue_limit : 5,

                  custom_settings : {
                    upload_target : "cardpic_previewBoxM",
                    upload_limit  : 1,
                    upload_nail	  : "cardpic_img",
                    upload_infotype : 1
                  },

                  // Event Handler Settings - these functions as defined in Handlers.js
                  //  The handlers are not part of SWFUpload but are part of my website and control how
                  //  my website reacts to the SWFUpload events.
                  swfupload_loaded_handler : swfUploadLoaded,
                  file_queue_error_handler : fileQueueError,
                  file_dialog_start_handler : fileDialogStart,
                  file_dialog_complete_handler : fileDialogComplete,
                  upload_progress_handler : uploadProgress,
                  upload_error_handler : uploadError,
                  upload_success_handler : function(file, serverData) {
                      uploadSuccessNew.apply(this, arguments);
                    changePic('cardpic');
                  },
                  upload_complete_handler : uploadComplete,
                  button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,

                  // Button Settings
                  // button_image_url : "",
                  button_placeholder_id : "cardfile_new",
                  button_width: 86,
                  button_height: 24,
                  button_text_top_padding: 3,
                  button_text_left_padding: 0,
                  button_cursor: SWFUpload.CURSOR.HAND,
                  button_text : '<span class="btn-upload">重新上传</span>',
                  button_text_style : ".btn-upload { color: #ffffff; width: 86pt; font-size: 12pt; line-height: 24pt; text-align: center; vertical-align: middle;}",
                  flash_url : "/swfupload.swf",
                  debug: false
                });

              });
            </script>
					</td>
					<td>
						<img class="fl" width="180" height="115" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/grzx/grmpfl.gif">
						<p class="per-ys">
							提示：<br>
							1.名片标准照，可扫描或拍照<br>
							2.上传图片限JPG、PNG格式 <br>
							3.文件小于10M</p>
					</td>
				</tr>
			</table>
		</div>
		<div class="per-btn">
			<a class="btn-lv-big btn-mid quali_modify_fail" href="javascript:void(0);" onclick="quali_modify()"><span class="btn_inner">重新提交审核</span></a>
		</div>
		<?php }?>
	</div>
</div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_do_success">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="15%" align="right" style="padding-right:10px;">	<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
							<p class="left" style="font-size:14px;color:#666;" id="dialog_do_success_tip">提交成功</p>
                        </td>
                    </tr>
                </table>
                <button class="btn" type="button" onclick="location.href='/my_info/'">确定</button>
            </div>
         </div>
    </div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_do_success_person">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">	<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
							<p class="left" style="font-size:14px;color:#666;">提交成功!</p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_do_error_person">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>

	<div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
				<div class="text-wrap mb10">
					<table class="mb10">
						<tr>
                <td><div class="img"><img alt="" id="imgg" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png"></div></td>
                <td class="msg"><span class="bold"></span></td>
            </tr>
					</table>
				</div>
                <button class="btn-lv1 btn-left JS_Close" type="button" >确定</button>
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
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_warning">
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
							<p class="left" id="dialog_text" style="font-size:14px;color:#666;"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn-lv1 btn-left" id="dialog_sure" type="button">确定</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
         </div>
    </div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" style="width:400px;" id="ident_auth_status_warning">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
			<div class="up_inner">
                <h3 style="color:#333; font-weight:bold; font-size:14px;"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">　您现在无法申请重新认证！</h3>
				<p style="padding:10px 0 15px; color:#666; line-height:24px; text-align:left;">1.如果您需要更换门店，请联系店长或以上级别帐号在系统管理中对您的门店进行修改。<br>
				2.如果您需要更换公司，请联系店长或以上级别帐号在系统管理中将您从本公司注销后，再重新进行新公司认证。</p>
                <div><button class="btn-lv1 btn-left" type="button" onclick="location.href='/my_info/'">确定</button>
				<!-- <button class="btn-lv1 btn-left" type="button" onclick="seed_message()">确定</button> -->
                <button class="btn-hui1 JS_Close" type="button" onclick="location.href='/my_info/'">取消</button></div>
            </div>
         </div>
    </div>
</div>

<div class="pop_box_g pop_see_info_deal" id="wei_pic_down" style="width:641px; height:525px;">
    <div class="hd">
        <div class="title">打印二维码</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod integral" style="margin:15px;">
        <form name="pic_deal" id="pic_deal" action="/my_info/pic_deal">
        <div class="inner clearfix" style="padding:0;">
			<div class="zws_adjust_person_pop_bg">
                <b>A4不干胶贴打印纸</b>
                <span class="zws_adjust_person_pop_bg_inf"><img src="<?php echo $wximg2; ?>" alt="" /></span>
                <span class="zws_adjust_person_pop_bg_inf"><img src="<?php echo $wximg2; ?>" alt="" /></span>
                <input type="hidden" value="<?php echo $wximg2; ?>" name="agency_scode_img"/>
            </div>
		</div>
        <span class="zws_adjust_person_pop_down">
            <b>提示：上图为打印预览图，建议1张A4纸上打印2张</b>
            <strong id="wei_pic_down_button">下载到本地打印</strong>
        </span>
        </form>
    </div>
</div>

<!--上班提示-->
<div class="pop_box_g" style="width:375px; height:255px; background:#fff;" id="modify_phone_pop">
    <div class="hd">
      <div class="title">帐号管理</div>
      <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
		<div class="table-phone">
			<table>
				<tr>
					<td class="td-left">原手机号：</td>
					<td id="old_phone"></td>
				</tr>
				<tr>
					<td class="td-left">新手机号：</td>
          <td><input type="text" name='phonenum' id='new_phone' class="input" value='' maxlength="11"></td>
				</tr>
				<tr>
					<td class="td-left">短信验证码：</td>
					<td>
              <input style="width:120px; margin-right:5px;" type="text" name='validcode'  id='validcode'  class="input" value=''>
              <button class="btn-hui2" id="getValidcode" >获取验证码</button>
              <p class="error" id="phone_error"></p>
          </td>
				</tr>
			</table>
		</div>
		<div class="center mt10">
			<button class="btn-lv1 btn-left" type="button" id="submit_modify_phone">确定</button>
			<button class="btn-hui1 JS_Close" type="button">取消</button>
		</div>
    </div>
</div>
<div id="js_pop_do_modify_success" class="pop_box_g pop_see_inform pop_no_q_up" style="position: absolute; z-index: 299706; left: 50%; margin-left: -151px; margin-top: -63px; top: 50%;">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);"  title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id="dialog_do_itp">修改成功，请重新登录！</p>
            </div>
        </div>
    </div>
</div>
<!--个人资料弹窗-->
<div class="pop_box_g zws_person_add_W450" style="display:none;" id="modify_person_pop">
	<input type="hidden" name="is_submit_w" value="1">
	<input type="hidden" name="is_submit_b" value="1">
    <div class="hd">
        <div class="title">个人资料</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod bg-weight">
		<div class="zws_person_add">
			<dl>
				<dd>从业时间：</dd>
				<dt>
					<select class="hr_framework_sel fl" id="work_time">
						<?php
						if(is_full_array($work_time)){
							foreach($work_time as $key=>$vo){
						?>
						<option value="<?=$key?>" ><?=$vo?></option>
						<?php }}?>
					</select>
				</dt>
			</dl>
			<dl>
				<dd>微信号：</dd>
				<dt>
					<input type="text" value="<?=$broker_info['weixin']?>" class="zws_person_add_input zws_p_select_w200" id='weixin' onkeyup="check_num()">
					<b>如不填写则不展示</b>
					<div id='weixin_error'></div>
				</dt>
			</dl>
			<dl>
				<dd>擅长领域：</dd>
				<dt>
					<textarea class="zws_person_add_textarea" id='businesses' onkeyup="check_num()"><?=$broker_info['businesses']?></textarea>
					<div id='businesses_error'></div>
					<span class="zws_person_add_strong">注：个人资料将在门店微店铺中展示，请如实填写。</span>
				</dt>

			</dl>

		</div>
		<div class="zws_person_clear"></div>
		<div class="center mt10">
			<button class="btn-lv1 btn-left" type="button" onclick="modify_person()">确定</button>
			<button class="btn-hui1 JS_Close" type="button">取消</button>
		</div>
	</div>
</div>

<!--载入如何获取积分页面-->
<?php $this->view('my_credit/credit');?>

<!--载入等级分值(成长值)页面-->
<?php $this->view('my_level/level');?>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,backspace.js,personal_center.js "></script>
<script>
$(function(){
	$(".zhanwei").hover(function(){
		$(this).toggleClass("zhanwei-on");
	});
	$(".zhanwei2").hover(function(){
		$(this).toggleClass("zhanwei2-on");
	});
	$(".per-hover").hover(function(){
		$(".r_s_popUP").toggle();
	});
});
</script>
</body>

<style>
.tx_normal_over .show_editor_remove{display:block}
</style>
<script type="text/javascript">
	//更改头像及其身份资质认证
  function changePic(div_id){
    $("#"+div_id+"_yrz").css("display","none");
    $("#effective").show();
    $("#invalid").hide();
  }
	//身份证确认
	$("#idno").bind("blur",function(){
		var idno = $(this).val();
		if(idno.length<15 || idno.length>18){
			if(idno){
				$("#errop-tip").html("请填写正确身份证号");
				$("#errop-tip").css("color","red");
			}else{
				$("#errop-tip").html("请填写身份证号");
				$("#errop-tip").css("color","red");
			}
		}else{
			$("#errop-tip").html("");
		}
	})

	//提交认证按钮计时
  var InterValObj; //timer变量，控制时间
  var count = 20; //间隔函数，1秒执行
  var curCount;//当前剩余秒数

  //提交认证审核
  function ident_save() {
    curCount = count;
    var idno = $("#idno").val();
    var photo = $("#headpic_img>.img-upload").attr("src");
    var photo2 = $("#idnopic_img>.img-upload").attr("src");
    var photo3 = $("#cardpic_img>.img-upload").attr("src");
    if (idno.length > 18 || idno.length < 15) {
      if (idno) {
        $("#dialog_do_warnig_tip").html("请填写正确身份证号");
        openWin('js_pop_do_warning');
        return false;
      } else {
        $("#dialog_do_warnig_tip").html("请填写身份证号");
        openWin('js_pop_do_warning');
        return false;
      }
    }
    if (!photo || photo == "") {
      $("#dialog_do_warnig_tip").html("请上传标准照片");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!photo2 || photo2 == "") {
      $("#dialog_do_warnig_tip").html("请上传身份证照片");
      openWin('js_pop_do_warning');
      return false;
    }
    if (!photo3 || photo3 == "") {
      $("#dialog_do_warnig_tip").html("请上传个人名片");
      openWin('js_pop_do_warning');
      return false;
    }
    $(".ident_save").removeAttr("onclick");
    InterValObj = window.setInterval(SetRemainTime, 1000);

    var data = {photo: photo, photo2: photo2, photo3: photo3, idno: idno};
    $.ajax({
      type: "POST",
      url: "/my_info/ident_auth",
      data: data,
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (data) {
        $("#dialog_do_success_tip").html(data);
        openWin('js_pop_do_success');
      }
    });
  }

  function getNoEmptyPic(img_old, img_new) {
    if(img_new && img_new != "")
      return img_new;
    else if(img_old && img_old != "")
      return img_old
  }

  //重新提交审核
  function quali_modify() {
    curCount = count;
//    var headfile_new = $("#headfile_new").val();//头像
//    var cardfile_new = $("#cardfile_new").val();//名片
    var idno = $("#idno").val();
    var headfile_old = $("#headpic_img>.img-upload-old").attr("src");
    var idnofile_old = $("#idnopic_img>.img-upload-old").attr("src");
    var cardfile_old = $("#cardpic_img>.img-upload-old").attr("src");
    var photo = $("#headpic_img>.img-upload").attr("src");
    var photo2 = $("#idnopic_img>.img-upload").attr("src");
    var photo3 = $("#cardpic_img>.img-upload").attr("src");
    var ident_auth_status = <?php echo $ident_auth_status;?>;

    if (idno.length < 15 || idno.length > 18) {
      if (idno) {
        $("#dialog_do_warnig_tip").html("请填写正确身份证号");
        openWin('js_pop_do_warning');
        return false;
      } else {
        $("#dialog_do_warnig_tip").html("请填写身份证号");
        openWin('js_pop_do_warning');
        return false;
      }
    }
    if ((!headfile_old || headfile_old == "") && (!photo || photo == "")) {
      $("#dialog_do_warnig_tip").html("请上传标准照片");
      openWin('js_pop_do_warning');
      return false;
    }
    if ((!idnofile_old || idnofile_old == "") && (!photo2 || photo2 == "")) {
      $("#dialog_do_warnig_tip").html("请上传身份证照片");
      openWin('js_pop_do_warning');
      return false;
    }
    if ((!cardfile_old || cardfile_old == "") && (!photo3 || photo3 == "")) {
      $("#dialog_do_warnig_tip").html("请上传个人名片");
      openWin('js_pop_do_warning');
      return false;
    }

    $(".quali_modify_success").removeAttr("onclick");
    $(".quali_modify_fail").removeAttr("onclick");
    InterValObj = window.setInterval(SetRemainTime, 1000);

    if (ident_auth_status == 2 && (photo && photo != "") && (idnofile_old && idnofile_old != "") && (cardfile_old && cardfile_old != "")/* && headfile_new != "" && cardfile_new == ""*/) {
      var data = {headpic: photo};
      $.ajax({
        type: "POST",
        url: "/my_info/head_modify_auth",
        data: data,
        cache: false,
        error: function () {
          $("#dialog_do_warnig_tip").html("系统错误");
          openWin('js_pop_do_warning');
          return false;
        },
        success: function (data) {
          $("#dialog_do_success_tip").html(data);
          openWin('js_pop_do_success');
        }
      });
    } else {
      var data = {photo: getNoEmptyPic(headfile_old,photo), photo2: getNoEmptyPic(idnofile_old,photo2), photo3: getNoEmptyPic(cardfile_old,photo3), idno: idno};
      $.ajax({
        type: "POST",
        url: "/my_info/quali_modify_auth",
        data: data,
        cache: false,
        error: function () {
          $("#dialog_do_warnig_tip").html("系统错误");
          openWin('js_pop_do_warning');
          return false;
        },
        success: function (data) {
          $("#dialog_do_success_tip").html(data);
          openWin('js_pop_do_success');
        }
      });
    }


  }

  function seed_message() {
    $.ajax({
      type: "POST",
      url: "/my_info/seed_message",
      //data:data,
      cache: false,
      error: function () {
        $("#dialog_do_warnig_tip").html("系统错误");
        openWin('js_pop_do_warning');
        return false;
      },
      success: function (data) {
        window.location.href = '/my_info/';
      }
    });
  }

  //timer处理函数
  function SetRemainTime() {
    if (curCount == 0) {
      window.clearInterval(InterValObj);//停止计时器
      $(".ident_save").attr("onclick", "ident_save()");
      $(".quali_modify_success").attr("onclick", "quali_modify()");
      $(".quali_modify_fail").attr("onclick", "quali_modify()");
      $(".ident_save .btn_inner").html("提交认证审核");
      $(".quali_modify_success .btn_inner").html("重新提交审核");
      $(".quali_modify_fail .btn_inner").html("重新提交审核");
    }
    else {
      $(".ident_save .btn_inner").html(curCount + "s");
      $(".quali_modify_success .btn_inner").html(curCount + "s");
      $(".quali_modify_fail .btn_inner").html(curCount + "s");
      curCount--;
    }
  }

	//提交成功关闭按钮关闭后刷新
	$("#js_pop_do_success .JS_Close").click(function(){
		window.location.reload();
	});
	$("#ident_auth_status_warning .JS_Close").click(function(){
		window.location.reload();
	});
</script>
