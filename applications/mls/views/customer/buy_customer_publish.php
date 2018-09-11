<!--页面部分-->
<body>
<div class="tab_box" id="js_tab_box">
    <a href="#" class="link link_on"><span class="iconfont">&#xe605;</span>录入求购</a>
    <a href="<?php echo MLS_URL;?>/rent_customer/publish" class="link"><span class="iconfont">&#xe604;</span>录入求租</a>
    <a href="/customer/manage" class="btn-lv" style="float:right; margin-right:10px;"><span>&lt;&lt;返回客源列表</span></a>
</div>
<form action='<?php echo MLS_URL;?>/customer/add'  method='post' id = 'jsUpForm'>
    <div class="forms forms_scroll h91" id="js_inner">
        <div class="h_15">&nbsp;</div>
        <div class="forms_details_fg forms_details_fg_bg clearfix">
            <div class="clearfix"><h3 class="h3">客源信息(加密)<span class="tip_text"></span></h3></div>
            <div class="item_fg clearfix">
				<div class="left" >
					<label class="label">
					<span class="text_fg"><b class="red">*</b>客户姓名：</span>
					<div class="y_fg js_fields">
						<input class="input_text input_text_r w80" type="text" name="truename" id="truename" value='' onblur="check_unique_customer('customer','truename')">
						<div class="errorBox clear"></div>
					</div>
					</label>
					<div class="label">
						<?php if(is_array($conf_customer['sex']) && !empty($conf_customer['sex'])) { ?>
						<?php foreach($conf_customer['sex'] as $key => $value){ ?>
						<i class="label">
							<input type="radio" class="input_radio" name="sex" value='<?php echo $key;?>'> <?php if($value == '男'){echo '先生';}else{echo '女士';}?>
						</i>
						<?php } ?>
						<?php } ?>
					</div>
					<label class="label">
					<span class="text_fg">身份证号：</span>
					<div class="y_fg js_fields">
						<input class="input_text w130" name='idno' type='text' maxlength='18'>
						<div class="errorBox clear"></div>
					</div>
					</label>
				</div>
                <div class="left js_fields" >
                    <div class="text_fg">年龄：</div>
                    <?php if(is_array($conf_customer['age_group']) && !empty($conf_customer['age_group'])) { ?>
                    <?php foreach($conf_customer['age_group'] as $key => $value){ ?>
                    <i class="label">
                        <input type="radio" class="input_radio" name="age_group" value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left"> <span class="text_fg"><b class="red">*</b>客户电话：</span>
                    <div class="y_fg js_fields">
                        <input class="input_text input_text_r input_text_r w80" type="text" name="telno[]" id="telno1" onblur="check_unique_customer('customer')">
                        <a href="javascript:void(0)" class="iconfont addTel" id="addTel01">&#xe608;</a>
                        <div class="errorBox clear"></div>
                    </div>
                    <div class=" field-tel02 y_fg js_fields hide">
                        <input class="input_text input_text_r w80" type="text" name="telno[]" id="telno2" onblur="check_unique_customer('customer')">
                        <a href="javascript:void(0)" class="iconfont delTel" id="delTel02">&#xe60c;</a>
                        <div class="errorBox clear"></div>
                    </div>
                    <div class=" field-tel03 y_fg js_fields hide">
                        <input class="input_text input_text_r w80" type="text" name="telno[]" id="telno3" onblur="check_unique_customer('customer')">
                        <a href="javascript:void(0)" class="iconfont delTel"  id="delTel03">&#xe60c;</a>
                        <div class="errorBox clear"></div>
                    </div>
                </div>

                <div class="left" >
                    <div class="text_fg">客源职业：</div>
                    <?php if(is_array($conf_customer['job_type']) && !empty($conf_customer['job_type'])) { ?>
                    <?php foreach($conf_customer['job_type'] as $key => $value){ ?>
                    <i class="label">
                        <input type="radio" class="input_radio" name="job_type" value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left js_fields width_b" >
                <label class="label">
                <span class="text_fg">联系地址：</span>
                <div class="y_fg">
                    <input class="input_text w370" type="text" name='address' value=''>
                </div>
                </label>
                </div>
                <div class="left js_fields width_b" >
                    <div class="text_fg ">客源等级：</div>
                    <?php if(is_array($conf_customer['user_level']) && !empty($conf_customer['user_level'])) { ?>
                    <?php foreach($conf_customer['user_level'] as $key => $value){ ?>
                    <i class="label">
                        <input type="radio"  class="input_radio" name="user_level" value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="forms_details_fg forms_details_fg_bg clearfix">
            <div class="clearfix"><h3 class="h3">客源需求</h3></div>
            <div class="item_fg clearfix">
                <div class="left width_b js_fields" >
                    <div class="text_fg"><b class="red">*</b>状态：</div>
                    <?php if(is_array($conf_customer['status']) && !empty($conf_customer['status'])) { ?>
                    <?php foreach($conf_customer['status'] as $key => $value){ ?>
					<?php if($key==1){?>
					<i class="label labelOn">
					<?php }else{?>
					<i class="label">
					<?php }?>
                        <input type="radio" class="input_radio" name="status"
						<?php if($key == 1){?> checked  <?php } ?> value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                    <div class="errorBox"></div>
                </div>
                <div class="left js_fields">
                    <div class="text_fg"><b class="red">*</b>客源性质：</div>
                    <?php if(is_array($conf_customer['public_type']) && !empty($conf_customer['public_type'])) { ?>
                    <?php
                    if('1'==$is_customer_private){
                        $public_type_checked = 1;
                    }else{
                        $public_type_checked = 2;
                    }

                    $public_type_order = 1;
                    foreach($conf_customer['public_type'] as $key => $value)
                    {
                    ?>
                    <?php if($public_type_order==$public_type_checked){?>
					<i class="label labelOn">
					<?php }else{?>
					<i class="label">
					<?php }?>
                        <input type="radio" class="input_radio" name="public_type" <?php if($public_type_order == 1){?> checked <?php } ?> value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php
                        $public_type_order ++;
                    }?>
                    <?php } ?>
                    <div class="errorBox"></div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg"><b class="red">*</b>物业类型：</div>
                    <div class="left js_fields">
                    <?php if(is_array($conf_customer['property_type']) && !empty($conf_customer['property_type'])) { ?>
                    <?php foreach($conf_customer['property_type'] as $key => $value){ ?>
                    <?php if($key==1){?>
					<i class="label labelOn">
					<?php }else{?>
					<i class="label">
					<?php }?>
                    <input type="radio" class="input_radio" name="property_type"  <?php if($key == 1){?> checked <?php } ?> value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                    </div>
                </div>
                <div class="left" id="huxing">
                    <div class="label "> <span class="text_fg"><b class="red">*</b>户型：</span>
                        <div class="y_fg" >
                            <div class="js_fields left">
                                <input type="text" id="room_min" name='room_min' class="input_text input_text_r w60" value=''>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">一</span>
                            <div class="js_fields left">
                                <input type="text" name="room_max" id="room_max"  class="input_text input_text_r w60" value=''>
                                <div class="errorBox clear"></div>
                            </div>
                        </div>
                        <span class="y_fg y_fg_p_l_5">室</span> </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg"><b class="red">*</b>价格：</div>
                    <div class="y_fg" >
                        <div class="js_fields left">
                            <input type="text" class="input_text input_text_r w60" name="price_min" id="price_min">
                            <div class="errorBox clear"></div>
                        </div>
                        <span class="y_fg y_fg_p5">一</span>
                        <div class="js_fields left">
                            <input type="text"  class="input_text input_text_r w60 " name="price_max" id="price_max" >
                            <div class="errorBox clear"></div>
                        </div>
                    </div>
                    <span class="y_fg y_fg_p_l_5">万元</span>
                </div>
                <div class="left">
                    <div class="label "> <span class="text_fg"><b class="red">*</b>面积：</span>
                        <div class="y_fg" >
                            <div class="js_fields left">
                                <input type="text" name="area_min" class="input_text input_text_r w60" id="mianji01">
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">一</span>
                            <div class="js_fields left ">
                                <input type="text" name="area_max" class="input_text input_text_r w60 ">
                                <div class="errorBox clear"></div>
                            </div>
                        </div>
                        <span class="y_fg y_fg_p_l_5">平方米</span> </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b js_fields">
                    <div class="text_fg">楼层：</div>
                    <div class="y_fg" >
                        <div class="js_fields left">
                            <input type="text" class="input_text w60" name='floor_min'  id='floor_min' value=''>
                            <div class="errorBox clear"></div>
                        </div>
                        <span class="y_fg y_fg_p5">一</span>
                        <div class="js_fields left">
                            <input type="text"  class="input_text w60" name='floor_max' id='floor_max' value=''>
                            <div class="errorBox clear"></div>
                        </div>
                    </div>
                    <span class="y_fg y_fg_p_l_5">层</span>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left ">
                    <div class="label "> <span class="text_fg"><b class="red">*</b>意向区属：</span>
                        <div class="y_fg">
                            <div class="left js_fields" id="QS01">
                                <select name="dist_id[]" id="dist_id" class="select" onchange ="get_street_by_id(this , 'street_id1')">
                                    <option selected="" value="0">请选择区属</option>
                                    <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                    <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>"><?php echo $value['district'];?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">&nbsp;</span>
                            <div class="left js_fields">
                                <select name="street_id[]" class="select" id = 'street_id1'>
                                    <option selected="" value="0">请选择板块</option>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <a href="javascript:void(0)" class="iconfont addTel" id="addQS01">&#xe608;</a> </div>
                        <span class="y_fg y_fg_p5">&nbsp;</span>
                        <div class="y_fg hide" id="QS02">
                            <div class="left js_fields">
                                <select name="dist_id[]" id="dist_id02" class="select" onchange ="get_street_by_id(this , 'street_id2')">
                                    <option selected="" value="0">请选择区属</option>
                                    <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                    <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>"><?php echo $value['district'];?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">&nbsp;</span>
                            <div class="left js_fields">
                                <select name="street_id[]" class="select" id ='street_id2'>
                                    <option selected="" value="0">请选择板块</option>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <a href="javascript:void(0)" class="iconfont delTel delTel02" id="delQS02">&#xe60c;</a> </div>
                        <span class="y_fg y_fg_p5">&nbsp;</span>
                        <div class="y_fg hide" id="QS03">
                            <div class="left js_fields">
                                <select name="dist_id[]" id="dist_id03" class="select" onchange ="get_street_by_id(this , 'street_id3')">
                                    <option selected="" value="0">请选择区属</option>
                                    <?php if( is_array($district_arr) && !empty($district_arr) ){ ?>
                                    <?php foreach($district_arr as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>"><?php echo $value['district'];?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <span class="y_fg y_fg_p5">&nbsp;</span>
                            <div class="left js_fields">
                                <select name="street_id[]" class="select" id ='street_id3'>
                                    <option selected="" value="0">请选择板块</option>
                                </select>
                                <div class="errorBox clear"></div>
                            </div>
                            <a href="javascript:void(0)" class="iconfont delTel delTel02"  id="delQS03">&#xe60c;</a> </div>
                        </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left"> <span class="text_fg">意向楼盘：</span>
                    <div class="y_fg">
                        <input class="js_block input_text w200" type="text" name="cmt_name[]" id="block01" change_tag = '0' value='' placeholder="输入拼音或汉字筛选">
                        <input type="hidden" name="cmt_id[]" class='cmt_id' id = 'cmt_id01' value="">
                        <a href="javascript:void(0)" class="iconfont addTel" id="addBlock01">&#xe608;</a>
                    </div>
                    <div class="y_fg hide">
                        <input class="js_block input_text w200" type="text" name="cmt_name[]" id="block02" change_tag = '0' value='' placeholder="输入拼音或汉字筛选">
                        <input type="hidden" name="cmt_id[]" class='cmt_id' id = 'cmt_id02' value="">
                        <a href="javascript:void(0)" class="iconfont delTel" id="delBlock02">&#xe60c;</a>
                    </div>
                    <div class="y_fg hide">
                        <input class="js_block input_text w200" type="text" name="cmt_name[]" id="block03" change_tag = '0' value='' placeholder="输入拼音或汉字筛选">
                        <input type="hidden" name="cmt_id[]" class='cmt_id' id = 'cmt_id03' value="">
                        <a href="javascript:void(0)" class="iconfont delTel"  id="delBlock03">&#xe60c;</a>
                    </div>
                </div>
			</div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg">朝向：</div>
                    <div class="left js_fields">
                        <select class="select" name="forward" id="forward">
                            <option selected="" value="">请选择</option>
                            <?php if(is_array($conf_customer['forward']) && !empty($conf_customer['forward'])) { ?>
                            <?php foreach($conf_customer['forward'] as $key => $value){ ?>
                                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <div class="text_fg">房龄：</div>
                    <div class="left js_fields">
                    <?php if(is_array($conf_customer['house_age']) && !empty($conf_customer['house_age'])) { ?>
                    <?php foreach($conf_customer['house_age'] as $key => $value){ ?>
                    <i class="label">
                        <input type="radio" class="input_radio" name="house_age" value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                    </div>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left width_b">
                    <div class="text_fg">装修：</div>
                    <div class="left js_fields">
                    <?php if(is_array($conf_customer['fitment']) && !empty($conf_customer['fitment'])) { ?>
                    <?php foreach($conf_customer['fitment'] as $key => $value){ ?>
                    <i class="label">
                        <input type="radio"class="input_radio" name="fitment" value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                    </div>
                </div>
                <div class="left  ">
                    <div class="text_fg">目的：</div>
                    <?php if(is_array($conf_customer['intent']) && !empty($conf_customer['intent'])) { ?>
                    <?php foreach($conf_customer['intent'] as $key => $value){ ?>
                    <i class="label">
                        <input type="radio"class="input_radio" name="intent" value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>

            </div>
            <div class="item_fg clearfix">
                <div class="left  width_b">
                    <div class="text_fg">期限：</div>
                    <?php if(is_array($conf_customer['deadline']) && !empty($conf_customer['deadline'])) { ?>
                    <?php foreach($conf_customer['deadline'] as $key => $value){ ?>
                    <i class="label">
                        <input type="radio" class="input_radio" name="deadline" value='<?php echo $key;?>'> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
                <div class="left">
                    <div class="text_fg">信息来源：</div>
                    <?php if(is_array($conf_customer['infofrom']) && !empty($conf_customer['infofrom'])) { ?>
                    <?php foreach($conf_customer['infofrom'] as $key => $value){ ?>
                    <i class="label <?php if($key==1){echo ' labelOn ';}?>">
                        <input type="radio" class="input_radio" name="infofrom" value='<?php echo $key;?>' <?php if($key==1){echo ' checked ';}?>> <?php echo $value;?>
                    </i>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="item_fg clearfix">
                <div class="left  ">
                    <div class="text_fg">备注：</div>
                    <textarea class="textarea" name='remark'></textarea>
                </div>
            </div>
        </div>

        <div class="forms_details_fg forms_details_fg_bg clearfix">
            <div class="item_fg reset_P clearfix">
                <div class="text_fg"><b class="red">*</b>是否合作：</div>
                <div class="left">
                    <?php if('1'==$open_cooperate){?>
                    <i class="label mod_p" id = "js_gs_01">是
                        <input type="radio"  class="input_radio" name="is_share" value="<?php echo ('1'==$check_cooperate)?'2':'1';?>">
                    </i>
                    <i class="label mod_p labelOn "  id = "js_gs_02">否
                        <input type="radio" checked="true"  class="input_radio" value="0" name="is_share">
                    </i>
                    <?php }else{?>
                    <i class="label-no mod_p">是
                    </i>
                    <i class="label-no2 mod_p labelOn ">否
                    </i>
                    <?php }?>
                </div>
                <span class="info_bc left">
                    <?php if('1'==$open_cooperate){?>
                        <?php if('1'==$check_cooperate){?>
                        需通过合作审核后，进入合作中心
                        <?php }else{?>
                        合作客源将在合作中心展示，帮助您高效合作，快速成交！
                        <?php }?>
                    <?php }else{?>
                        店长已关闭合作功能
                    <?php }?>
                </span>
            </div>
        </div>

   <div style="height:61px;"></div>
    </div>
    <!--操作结果弹出提示框-->
    <div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop">
                <a href="javascript:void(0)" onclick="jump_to_url('/customer/manage/');return false;" title="关闭" class="JS_Close iconfont"></a>
            </div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                      <p class="text" ><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/r_ico.png" style="margin-right:10px;"><span  id='dialog_do_itp'></span></p>
                </div>
            </div>
        </div>
    </div>

    <!--操作结果弹出警告-->
    <div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up" >
        <div class="hd">
            <div class="title">提示</div>
            <div class="close_pop">
                <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
            </div>
        </div>
        <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" ><img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/s_ico.png" style="margin-right:10px;"><span id='dialog_do_warnig_tip'></span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="forms_details_fg forms_details_fg_btn" id="js_forms_details_fg">
		<div class="bg">&nbsp;</div>
		<iframe class="iframe_bg"></iframe>
		<button type="submit" class="submit" id="js_forms_submit">录入客源</button>
    </div>
    <input type = 'hidden' name = 'publish_type' id = 'publish_type' value = 'buy_customer_publish'>
 </form>
<script type='text/javascript'>
$(function(){
    $('input[name="property_type"]').parent().click(function(){
        var property_type_val = $('input[name="property_type"]:checked').val();
        if(property_type_val == '3'|| property_type_val == '4'|| property_type_val == '5'|| property_type_val == '6'|| property_type_val == '7'){
            $('#huxing').attr('style','display:none');
            $('#room_min').val('');
            $('#room_max').val('');
        }else{
            $('#huxing').attr('style','');
        }
    });
});

</script>
<script>

$(function(){//发布页底部按钮 悬浮

    $("#js_forms_submit").hover(function(){
            $(this).addClass("submit_hover")
    },function(){
            $(this).removeClass("submit_hover")
    });
    innerHeightForm();
    $(window).resize(function(){
            innerHeightForm();
    });

	function innerHeightForm()
	{
		//窗口改变大小的时候  计算高度
		if($("#js_inner").length>0)
		{
			var _height = document.documentElement.clientHeight;
			var _height_tab = $("#js_tab_box").outerHeight(true);
			$("#js_inner").css("height", _height - _height_tab );
		}
	};

    $('#telno1,#telno2,#telno3').live('blur',function(){
        var telno = $(this).val();
        $.ajax({
                url: "/customer/check_blacklist/",
                type: "GET",
                data: {telno: telno},
                success:function(data){
                    if('success'==data){
                        $("#dialog_do_warnig_tip").html('该电话号码是黑名单');
                        openWin('js_pop_do_warning');
                    }
                }
        });
    });

})
</script>
