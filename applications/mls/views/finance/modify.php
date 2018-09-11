
<div class="tab_box" id="js_tab_box">

    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
	<a href="/finance/my_customer" class="btn-lv" style="float:right; margin-right:10px;"><span>&lt;&lt;返回我的客户列表</span></a>
</div>
<form class="form-horizontal"  id='jsUpForm_modify' name='jsUpForm_modify' method="post">
<input type='hidden' name='submit_flag' value='modify'/>
<input type='hidden' name='modify_id' id="modify_id" value='<?=$new_info['id']?>'/>
    <div class="wrapper ajd forms_scroll" style="margin:auto">
        <div class="ajd_ad">
            <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/finance/fjr_ad2.jpg" alt=""/>
        </div>
        <div class="ajd_information">
            <h2>房源信息</h2>
            <div class="ajd_form_main clearfix">
                <div class="form_vv form_name clearfix js_fields">
                    <label for="">小区名称&nbsp;&nbsp;:</label>
                    <div class="input_main">
                        <input type="text" name="block_name" id="block_name" value="<?=$new_info['block_name']?>"/>
                    	<div class="errormsg errorBox clear"></div>
                    </div>

                </div>
                <div class="form_vv form_address clearfix js_fields">
                    <label for="">小区地址&nbsp;&nbsp;:</label>
                    <div class="input_main">
                        <input type="text" name="block_address" id="block_address" value="<?=$new_info['block_address']?>"/>
                    	<div class="errormsg errorBox clear"></div>
                    </div>
                </div>
                <div class="form_vv form_block clearfix js_fields" style="margin-left:17px;">
                    <label for="">楼栋单位门牌:</label>
                    <div class="input_main">
                        <input type="text" name="block_num" id="block_num" value="<?=$new_info['block_num']?>" style="margin-left:7px;width:130px"/>
                    	<div class="errormsg errorBox clear"></div>
                    </div>
                </div>
                <div class="form_vv form_all clearfix js_fields">
                    <label for="">总价:</label>
                    <div class="input_main">
                        <input type="text" name="price" id="price" value="<?=strip_end_0($new_info['price'])?>"/>
                    	<div class="errormsg errorBox clear"></div>
                    </div>
                    <label for="">万</label>
                </div>
                <div class="form_vv form_shou clearfix js_fields">
                    <label for="">首付:</label>
                    <div class="input_main">
                        <input type="text" name="first_pay" id="first_pay" value="<?=strip_end_0($new_info['first_pay'])?>"/>
                    	<div class="errormsg errorBox clear"></div>
                    </div>
                    <label for="">万</label>
                </div>
            </div>
        </div>
        <div class="ajd_buy">
            <h2>买方信息</h2>
            <div class="buy_information clearfix">
                <div class="form_vv buy_name clearfix js_fields">
                    <label for="">姓名&nbsp;&nbsp;:</label>
                    <div class="input_main">
                        <input type="text" name="borrower" id="borrower" value="<?=$new_info['borrower']?>"/>
                    	<div class="errormsg errorBox clear"></div>
                    </div>
                </div>
                <div class="form_av buy_sex clearfix">
                    <span>性别&nbsp;&nbsp;:</span>
                    <div class="bv <?php if($new_info['buy_sex'] == 1){echo "checked";}?>">
                        <span class="bot"><input type="radio" name="buy_sex" value='1' checked/></span>
                    </div>
                    <label for="" class="ajd_man">男</label>
                    <div class="bv <?php if($new_info['buy_sex'] == 2){echo "checked";}?>">
                        <span class="bot"><input type="radio" name="buy_sex" value='2'/></span>

                    </div>
                    <label for="">女</label>
                </div>
                <div class="form_vv buy_phone clearfix js_fields">
                    <label for="">联系电话&nbsp;&nbsp;:</label>
                    <div class="input_main">
                        <input type="text" name="borrower_phone" id="borrower_phone" value="<?=$new_info['borrower_phone']?>"/>
                    	<div class="errormsg errorBox clear"></div>
                    </div>
                </div>
            </div>
            <h3>请上传<strong>买方</strong>相关资料<span>(请选择清晰照片，支持JPG/PNG/GIF格式，单项最多可添加10张图片)</span></h3>
            <div class="add_photo clearfix">
			<?php
				foreach($new_borrow_config['buy_photo_info'] as $key=>$value){
					if($key%2 !== 0){
			?>
                <div class="add_lef clearfix">
                    <div class="add_li clearfix">
                        <div class="bt_add">
                            <div class="addBtn radius5">
                                <span id="spanButtonPlaceholder_<?=$key?>"></span>
                            </div>

                            <div id="jsPicPreviewBoxM_<?=$key?>" style="display:none;"></div>
							<div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails_<?=$key?>" style="width:100%;"></div>
                        </div>
						<p class="wz">添加<?=$value?></p>
                        <div class="photo_nums">
                            <span class="go_left gl<?=$key?>" id="gl<?=$key?>"><em>&nbsp;</em></span>
                            <div class="outer_num">
                                <div class="inner_num clearfix" id="m_l<?=$key?>" num="0">
								<?php
									if($new_info['buy_photo_info'][$k]){
										foreach($new_info['buy_photo_info'][$k] as $v){
								?>
                                    <div class="img_list">
                                        <img src="<?=$v?>" alt="" class="llll" tag="1" type="<?=$key?>"/>
                                        <span class="bt_delete">&nbsp;</span>
										<input class="hidden_1" type="hidden" value="<?=$v?>" name="pic_buy<?=$key?>[]">
                                    </div>
								<?php }}?>
                                </div>
                            </div>
                            <span class="go_right gr<?=$key?>" id="gr<?=$key?>"><em>&nbsp;</em></span>
                        </div>
                    </div>
                </div>
				<script>
					var swfu1;
					swfu1 = new SWFUpload({
                        file_post_name: "file",
                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
						file_size_limit : "5 MB",
						file_types : "*.jpg;*.png",
						file_types_description : "JPG Images",
						file_upload_limit : "0",
						file_queue_limit : "5",
						custom_settings : {
							upload_target : "jsPicPreviewBoxM_<?=$key?>",
							upload_limit  : 10,
							upload_nail	  : "m_l<?=$key?>",
							upload_infotype : "buy<?=$key?>"
						},
						swfupload_loaded_handler : swfUploadLoaded,
						file_queue_error_handler : fileQueueError,
						file_dialog_start_handler : fileDialogStart,
						file_dialog_complete_handler : fileDialogComplete,
						upload_progress_handler : uploadProgress,
						upload_error_handler : uploadError,
						upload_success_handler : uploadSuccessNew,
						upload_complete_handler : uploadComplete,

						button_image_url : '<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/finance/uploadimg.png',
						button_placeholder_id : "spanButtonPlaceholder_<?=$key?>",
						button_width: 100,
						button_height: 75,
						button_cursor: SWFUpload.CURSOR.HAND,
						button_text:'',
						flash_url : "/swfupload.swf"
					});
                </script>
			<?php }else{?>
                <div class="add_right clearfix">
                    <div class="add_li clearfix">
                        <div class="bt_add">
                            <div class="addBtn radius5">
                                <span id="spanButtonPlaceholder_<?=$key?>"></span>
                            </div>

                            <div id="jsPicPreviewBoxM_<?=$key?>" style="display:none;"></div>
							<div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails_<?=$key?>" style="width:100%;"></div>
                        </div>
						<p class="wz">添加<?=$value?></p>
                        <div class="photo_nums">
                            <span class="go_left gl<?=$key?>" id="gl<?=$key?>"><em>&nbsp;</em></span>
                            <div class="outer_num">
                                <div class="inner_num clearfix" id="m_l<?=$key?>" num="0">
                                    <?php
									if($new_info['buy_photo_info'][$key]){
										foreach($new_info['buy_photo_info'][$key] as $v){
									?>
                                    <div class="img_list">
                                        <img src="<?=$v?>" alt="" class="llll" tag="1" type="<?=$key?>"/>
                                        <span class="bt_delete">&nbsp;</span>
										<input class="hidden_1" type="hidden" value="<?=$v?>" name="pic_buy<?=$key?>[]">
                                    </div>
								<?php }}?>
                                </div>
                            </div>
                            <span class="go_right gr<?=$key?>" id="gr<?=$key?>"><em>&nbsp;</em></span>
                        </div>
                    </div>
                </div>
				<script>
					var swfu1;
					swfu1 = new SWFUpload({
                        file_post_name: "file",
                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
						file_size_limit : "5 MB",
						file_types : "*.jpg;*.png",
						file_types_description : "JPG Images",
						file_upload_limit : "0",
						file_queue_limit : "5",
						custom_settings : {
							upload_target : "jsPicPreviewBoxM_<?=$key?>",
							upload_limit  : 10,
							upload_nail	  : "m_l<?=$key?>",
							upload_infotype : "buy<?=$key?>"
						},
						swfupload_loaded_handler : swfUploadLoaded,
						file_queue_error_handler : fileQueueError,
						file_dialog_start_handler : fileDialogStart,
						file_dialog_complete_handler : fileDialogComplete,
						upload_progress_handler : uploadProgress,
						upload_error_handler : uploadError,
						upload_success_handler : uploadSuccessNew,
						upload_complete_handler : uploadComplete,

						button_image_url : '<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/finance/uploadimg.png',
						button_placeholder_id : "spanButtonPlaceholder_<?=$key?>",
						button_width: 100,
						button_height: 75,
						button_cursor: SWFUpload.CURSOR.HAND,
						button_text:'',
						flash_url : "/swfupload.swf"
					});
                </script>
			<?php }}?>
            </div>
        </div>
        <div class="ajd_buy ajd_mai">
            <h2>卖方信息</h2>
            <div class="buy_information clearfix">
            <div class="form_vv buy_name clearfix js_fields">
                <label for="">姓名&nbsp;&nbsp;:</label>
                <div class="input_main">
                    <input type="text" name="sell_name" id="sell_name" value="<?=$new_info['sell_name']?>"/>
                	<div class="errormsg errorBox clear"></div>
                </div>
            </div>
            <div class="form_av buy_sex clearfix">
                <span>性别&nbsp;&nbsp;:</span>
                <div class="bv <?php if($new_info['sell_sex'] == 1){echo "checked";}?>">
                    <span class="bot"><input type="radio" name="sell_sex" value="1" checked/></span>

                </div>
                <label for="" class="ajd_man">男</label>
                <div class="bv <?php if($new_info['sell_sex'] == 2){echo "checked";}?>">
                    <span class="bot"><input type="radio" name="sell_sex" value="2" /></span>

                </div>
                <label for="">女</label>
            </div>
            <div class="form_vv buy_phone clearfix js_fields">
                <label for="">联系电话&nbsp;&nbsp;:</label>
                <div class="input_main">
                    <input type="text" name="sell_phone" id="sell_phone" value="<?=$new_info['sell_phone']?>"/>
                	<div class="errormsg errorBox clear"></div>
                </div>
            </div>
        </div>
        <h3>请上传<strong>卖方</strong>相关资料<span>(请选择清晰照片，支持JPG/PNG/GIF格式，单项最多可添加10张图片)</span></h3>
        <div class="add_photo clearfix">
		<?php
				foreach($new_borrow_config['sell_photo_info'] as $key=>$value){
					if($key%2 == 0){
		?>
			<div class="add_lef clearfix">
				<div class="add_li clearfix">
					<div class="bt_add">
						<div class="addBtn radius5">
                                <span id="spanButtonPlaceholder_<?=$key?>"></span>
                        </div>

						<div id="jsPicPreviewBoxM_<?=$key?>" style="display:none;"></div>
						<div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails_<?=$key?>" style="width:100%;"></div>
					</div>
					<p class="wz">添加<?=$value?></p>
					<div class="photo_nums">
						<span class="go_left" id="ml<?=$key?>"><em>&nbsp;</em></span>
						<div class="outer_num">
							<div class="inner_num clearfix" id="b_l<?=$key?>" num="0">
								<?php
									if($new_info['sell_photo_info'][$key]){
										foreach($new_info['sell_photo_info'][$key] as $v){
								?>
                                    <div class="img_list">
                                        <img src="<?=$v?>" alt="" class="llll" tag="2" type="<?=$key?>"/>
                                        <span class="bt_delete">&nbsp;</span>
										<input class="hidden_1" type="hidden" value="<?=$v?>" name="pic_sell<?=$key?>[]">
                                    </div>
								<?php }}?>
							</div>
						</div>
						<span class="go_right" id="mr<?=$key?>"><em>&nbsp;</em></span>
					</div>
				</div>
			</div>
			<script>
					var swfu1;
					swfu1 = new SWFUpload({
                        file_post_name: "file",
                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
						file_size_limit : "5 MB",
						file_types : "*.jpg;*.png",
						file_types_description : "JPG Images",
						file_upload_limit : "0",
						file_queue_limit : "5",
						custom_settings : {
							upload_target : "jsPicPreviewBoxM_<?=$key?>",
							upload_limit  : 10,
							upload_nail	  : "b_l<?=$key?>",
							upload_infotype : "sell<?=$key?>"
						},
						swfupload_loaded_handler : swfUploadLoaded,
						file_queue_error_handler : fileQueueError,
						file_dialog_start_handler : fileDialogStart,
						file_dialog_complete_handler : fileDialogComplete,
						upload_progress_handler : uploadProgress,
						upload_error_handler : uploadError,
						upload_success_handler : uploadSuccessNew,
						upload_complete_handler : uploadComplete,

						button_image_url : '<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/finance/uploadimg.png',
						button_placeholder_id : "spanButtonPlaceholder_<?=$key?>",
						button_width: 100,
						button_height: 75,
						button_cursor: SWFUpload.CURSOR.HAND,
						button_text:'',
						flash_url : "/swfupload.swf"
					});
                </script>
		<?php }else{?>
			<div class="add_right clearfix">
				<div class="add_li clearfix">
					<div class="bt_add">
						<div class="addBtn radius5">
                                <span id="spanButtonPlaceholder_<?=$key?>"></span>
						</div>

						<div id="jsPicPreviewBoxM_<?=$key?>" style="display:none;"></div>
						<div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails_<?=$key?>" style="width:100%;"></div>
					</div>
					<p class="wz">添加<?=$value?></p>
					<div class="photo_nums">
						<span class="go_left" id="ml<?=$key?>"><em>&nbsp;</em></span>
						<div class="outer_num">
							<div class="inner_num clearfix" id="b_l<?=$key?>" num="0">
								<?php
									if($new_info['sell_photo_info'][$key]){
										foreach($new_info['sell_photo_info'][$key] as $v){
								?>
                                    <div class="img_list">
                                        <img src="<?=$v?>" alt="" class="llll" tag="2" type="<?=$key?>"/>
                                        <span class="bt_delete">&nbsp;</span>
										<input class="hidden_1" type="hidden" value="<?=$v?>" name="pic_sell<?=$key?>[]">
                                    </div>
								<?php }}?>
							</div>
						</div>
						<span class="go_right" id="mr<?=$key?>"><em>&nbsp;</em></span>
					</div>
				</div>
			</div>
			<script>
					var swfu1;
					swfu1 = new SWFUpload({
                        file_post_name: "file",
                        upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
						file_size_limit : "5 MB",
						file_types : "*.jpg;*.png",
						file_types_description : "JPG Images",
						file_upload_limit : "0",
						file_queue_limit : "5",
						custom_settings : {
							upload_target : "jsPicPreviewBoxM_<?=$key?>",
							upload_limit  : 10,
							upload_nail	  : "b_l<?=$key?>",
							upload_infotype : "sell<?=$key?>"
						},
						swfupload_loaded_handler : swfUploadLoaded,
						file_queue_error_handler : fileQueueError,
						file_dialog_start_handler : fileDialogStart,
						file_dialog_complete_handler : fileDialogComplete,
						upload_progress_handler : uploadProgress,
						upload_error_handler : uploadError,
						upload_success_handler : uploadSuccessNew,
						upload_complete_handler : uploadComplete,

						button_image_url : '<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/finance/uploadimg.png',
						button_placeholder_id : "spanButtonPlaceholder_<?=$key?>",
						button_width: 100,
						button_height: 75,
						button_cursor: SWFUpload.CURSOR.HAND,
						button_text:'',
						flash_url : "/swfupload.swf"
					});
                </script>
		<?php }}?>
		</div>
        <button class="ajd_submit">提交资料</button>
    </div>
</form>
<!--操作成功弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;" id="js_prompt"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
<!--操作失败弹窗-->
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop_false">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
     <div class="mod">
    	<div class="inform_inner">
	    <div class="up_inner">
                <table class="del_table_pop">
                    <tr>
                        <td width="25%" align="right" style="padding-right:10px;">
			    <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
                        <td>
			    <p class="left" style="font-size:14px;color:#666;"  id="js_prompt2"></p>
                        </td>
                    </tr>
                </table>
                <button class="btn JS_Close" type="button">确定</button>
            </div>
         </div>
    </div>
</div>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/openWin.js" type="text/javascript"></script>

<script>
    window.onload  = function(){

        var winHeight = $(window).height()-43;
        $('.wrapper').css('height',winHeight);
        function getId(id){
            return document.getElementById(id);
        }

		function ff(bbc){
            var length = bbc.siblings('.outer_num').find('.img_list').length;
            var _index = bbc.parent().find('.inner_num').attr('num');
            if(bbc.hasClass('go_right')){

                if(length > 4){
                    _index++;
                    if( _index>Math.abs(length - 4)){
                        _index = length - 4;
                    }
                }

            }
            else{
                _index--;
                if( _index<0){
                    _index = 0;
                }
            }
            bbc.parent().find('.inner_num').attr('num',_index);
            miaovStartMove(bbc.parent().find('.inner_num')[0], {left: -(_index)*110}, MIAOV_MOVE_TYPE.BUFFER);
        }


		  function ccc(){

            var vv =$('.inner_num');
            var len = vv.length;
            for(var i = 0;i < len;i++){
                if($(vv[i]).children().length > 4){
                    $(vv[i]).parents('.photo_nums').find('.go_left').show();
                    $(vv[i]).parents('.photo_nums').find('.go_right').show();
                }
            }

        }

        ccc()

        $(document).delegate('.bt_delete','click',function(){
            var vv = $(this).parents('.inner_num').children().length;
            if(vv-1 <=4 ){
                $(this).parents('.outer_num').siblings('span').hide();
            }
            var _index2 = $(this).parents('.inner_num').attr('num');
            if(_index2>=1){
                _index2--;
                $(this).parents('.inner_num').attr('num',_index2);
            }
            miaovStartMove($(this).parents('.inner_num')[0], {left: -(_index2)*110}, MIAOV_MOVE_TYPE.BUFFER);
            $(this).parent('.img_list').remove();


        })

        $(document).delegate('.go_left','click',function(){

            ff($(this));
        })
        $(document).delegate('.go_right','click',function(){

            ff($(this));
        })


        $(document).delegate('.img_list','mouseenter',function(){
            $(this).find('.bt_delete').show();
        }).delegate('.img_list','mouseleave',function(){
            $(this).find('.bt_delete').hide();
        })


        $('.go_left,.go_right').hover(function(){
            $(this).addClass('go_hovers');
        },function(){
            $(this).removeClass('go_hovers');
        })

        $('.bt_add').hover(function(){
            $(this).find('.add_plus').addClass('add_plus_hover');
            $(this).addClass('bt_add_hover');
        },function(){
            $(this).find('.add_plus').removeClass('add_plus_hover');
            $(this).removeClass('bt_add_hover');
        })

       $('.buy_sex input').click(function(){
                   //$(this).attr('checked','true');
                   $(this).parents('.bv').siblings().find('.bot').removeClass('checked');
                   $(this).parent('.bot').addClass('checked');
               })

        $(document).delegate('.bt_delete','click',function(){
            var vv = $(this).parents('.inner_num').children().length;
            if(vv-1 <=4 ){
                $(this).parents('.outer_num').siblings('span').hide();
            }
            $(this).parent('.img_list').remove();


        })

          $(document).delegate('.llll','click',function(){

                            var imgList = $(this).parents('.inner_num').find('img');
                            var len = imgList.length;
                            var arr = [];
                            for(var i = 0;i < len;i++){
                                arr.push($(imgList[i]).attr('src'));
                            }
							var tag = $(this).attr('tag');//1:买方；2：卖方
							var type = $(this).attr('type');
                            var srcs = $(this).attr('src').replace('/thumb','');
                            var indexs = arr.indexOf($(this).attr('src'));
                            bbb();
                            $('.inner_show img').attr('src',srcs);
                            $('.check_big_photo .pp2 em').html(len);
                            $('.check_big_photo .pp1 em').html(indexs+1);

							$.ajax({
								type: 'POST',
								url: '/finance/get_config/',
								data:{},
								dataType: 'json',
								success: function(data){
									if(data['result'] == 'ok'){
									   var config = data['data'];
									   if(tag == 1){
											$(".now_name").html(config['buy_photo_info'][type]+'图片');
									   }else if(tag == 2){
											$(".now_name").html(config['sell_photo_info'][type]+'图片');
									   }
									}
								}
							});

                            $('.choose_prev').click(function(){
                                indexs--;
                                if(indexs < 0){
                                    indexs = arr.length-1;
                                }
                                $('.inner_show img').attr('src',arr[indexs].replace('/thumb',''));
                                $('.check_big_photo .pp1 em').html(indexs+1);
                            });

                            $('.choose_next').click(function(){
                                indexs++;
                                if(indexs > arr.length-1){
                                    indexs = 0;
                                }
                                $('.inner_show img').attr('src',arr[indexs].replace('/thumb',''));
                                $('.check_big_photo .pp1 em').html(indexs+1);
                            });

                            $('.check_close').click(function(){
                                $('.check_big_photo').hide();
                            });
                        })

                        var getSigon = function(fn){

                            var result;
                            return function(){
                                if(!result){
                                    result = fn.apply(this,arguments);
                                }
                                result[0].style.display = 'block';
                            }
                        }

                        function mycreatDiv(){

                            var photoDom = $(
                            '<div class="check_big_photo">' +
                                '<h2>查看大图<span class="check_close"></span></h2>'+
                                    '<div class="inner_show">'+
                                        '<img src="" alt=""/>'+
                                    '</div>'+
                                    '<div class="check_mesg clearfix">'+
                                        '<span class="now_name">户口本图片</span>'+
                                        '<div class="choose_mains">'+
                                            '<span class="pp1">当前第<em></em>张</span>'+
                                            '<span class="pp2">共<em></em>张</span>'+
                                            '<span class="choose_prev">上一张</span>'+
                                            '<span class="choose_next">下一张</span>'+
                                        '</div>'+
                                    '</div>'+
                            '</div>' );
                            $('.wrapper').append(photoDom);
                            return photoDom;
                        }
                        var bbb = getSigon(mycreatDiv);

    }


</script>
</html>
