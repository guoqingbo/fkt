<script>
    window.parent.addNavClass(17);
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js,openWin.js"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script>
<script>
var is_follow = true;
$(function(){
	$(".input-num").blur(function(){
		var inputNum=$(this).val();
		var reg =  /^[0-9]*[1-9][0-9]*$/ ;
		if(reg.test(inputNum) == false &&inputNum!==""){
			$("#dialog_do_warnig_tip").html("请输入大于0的整数数字");
			openWin('js_pop_do_warning');
            $(this).val("");
		}
	});
    //最后跟进日天数判断
    $("#sell_house_follow_last_time1,#sell_house_follow_last_time2,#rent_house_follow_last_time1,#rent_house_follow_last_time2,#buy_customer_follow_last_time1,#buy_customer_follow_last_time2,#rent_customer_follow_last_time1,#rent_customer_follow_last_time2").blur(function(){
        var sell_house_follow_last_time1 = $("#sell_house_follow_last_time1").val();
        var sell_house_follow_last_time2 = $("#sell_house_follow_last_time2").val();
        var rent_house_follow_last_time1 = $("#rent_house_follow_last_time1").val();
        var rent_house_follow_last_time2 = $("#rent_house_follow_last_time2").val();
        var buy_customer_follow_last_time1 = $("#buy_customer_follow_last_time1").val();
        var buy_customer_follow_last_time2 = $("#buy_customer_follow_last_time2").val();
        var rent_customer_follow_last_time1 = $("#rent_customer_follow_last_time1").val();
        var rent_customer_follow_last_time2 = $("#rent_customer_follow_last_time2").val();

        if(!(parseInt(sell_house_follow_last_time1) < parseInt(sell_house_follow_last_time2)) || !(parseInt(rent_house_follow_last_time1) < parseInt(rent_house_follow_last_time2))
                || !(parseInt(buy_customer_follow_last_time1) < parseInt(buy_customer_follow_last_time2)) || !(parseInt(rent_customer_follow_last_time1) < parseInt(rent_customer_follow_last_time2))){
			$("#dialog_do_warnig_tip").html("最后跟进日天数紫色必须大于绿色");
			openWin('js_pop_do_warning');
            is_follow = false;
        }else{
            is_follow = true;
        }
    });

})
</script>
<!--导航栏-->
<div id="js_tab_box" class="tab_box">
    <?php echo $user_menu;?>
</div>
<!--主要内容-->
<form method="post" action="" name="search_form" id="basic_form">
<div style="position: relative; overflow-y: auto; width: 100%; height: 421px; overflow-x:hidden;" id="js_inner">
    <div class="set_basic_bgw">
        <div class="set_basic_wrap">
            <div class="set_basic_content">
                <p class="title">系统设置</p>
				<table>
					<tr>
						<td width="40%">
							<span class="label fl">系统自动防护时间</span>
							<input type="text" class="fl auto_pre" name="guard_time" value="<?php if($company_setting['guard_time']!=='0'){echo $company_setting['guard_time'];}?>">
							<span class="fl">分钟内</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								超过X分钟，系统自动跳转到登录界面，保护您的隐私，最长保持两小时。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">跟进内容不得少于</span>
							<input type="text" class="auto_pre fl input-num" name="follow_text_num" value="<?php echo $company_setting['follow_text_num']>"0"?$company_setting['follow_text_num']:""?>">
							<span class="fl">字</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制跟进内容字数，保证跟进内容质量。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="fl">
								<span class="label fl">信息录入进行黑名单校验</span>
								<input type="radio" class="find_call" name="is_blacklist_check" value="1" <?php echo $company_setting['is_blacklist_check']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_blacklist_check" value="0" <?php echo $company_setting['is_blacklist_check']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								与自由黑名单比对。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">房客源列表默认排序规则</span>
							<select class="auto_pre2 fl" name="house_list_order_field">
								  <option value="1" <?php echo $company_setting['house_list_order_field']=="1"?"selected":""?>>时间</option>
								  <option value="2" <?php echo $company_setting['house_list_order_field']=="2"?"selected":""?>>价格</option>
							</select>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制房源默认排序规则。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="fl">
								<span class="label fl">楼盘名称只能选择录入</span>
								<input type="radio" class="find_call" name="is_property_publish" value="1" <?php echo $company_setting['is_property_publish']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_property_publish" value="0" <?php echo $company_setting['is_property_publish']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制楼盘名称录入规则。</div>
							</span>
						</td>
						<td>
							<div class="fl">
								<span class="label2 fl">登录时有提醒是否自动打开</span>
								<input type="radio" class="find_call" name="is_remind_open" value="1" <?php echo $company_setting['is_remind_open']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_remind_open" value="0" <?php echo $company_setting['is_remind_open']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制登录自动提醒功能。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">保密信息查看次数上限</span>
							<input type="text" class="auto_pre fl input-num" name="secret_view_num" value="<?php echo $company_setting['secret_view_num']>"0"?$company_setting['secret_view_num']:""?>">
							<span class="fl">次/日</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制保密信息查看次数。</div>
							</span>
						</td>
						<td>
							<div class="fl">
								<span class="label2 fl">楼盘字典同步变化</span>
								<input type="radio" class="find_call" name="is_community_modify_house" value="1" <?php echo $company_setting['is_community_modify_house']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_community_modify_house" value="0" <?php echo $company_setting['is_community_modify_house']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制楼盘字典是否同步。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="fl">
								<span class="label fl">是否开启合作中心</span>
								<input type="radio" class="find_call" name="open_cooperate" value="1" <?php echo $company_setting['open_cooperate']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="open_cooperate" value="0" <?php echo $company_setting['open_cooperate']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制是否开启合作中心。</div>
							</span>
						</td>
						<td>
							<div class="fl">
								<span class="label2 fl">是否开启合作审核</span>
								<input type="radio" class="find_call" name="check_cooperate" value="1" <?php echo $company_setting['check_cooperate']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="check_cooperate" value="0" <?php echo $company_setting['check_cooperate']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制是否开启合作审核。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="fl">
                                <span class="label fl">是否早晚打卡</span>
                                <input type="radio" class="find_call" name="is_check_work"
                                       value="1" <?php echo $company_setting['is_check_work'] == "1" ? "checked" : "" ?>>是
                                <input type="radio" class="find_call" name="is_check_work"
                                       value="0" <?php echo $company_setting['is_check_work'] == "0" ? "checked" : "" ?>>否
                            </div>
                            <!--提示部分-->
                            <span class="zws_power_remind_con" id="prompt">
								<div class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img
                                            src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制是否早晚打卡。</div>
							</span>
                        </td>
                        <td>
                            <span class="label2 fl">工作日时间</span>
                            <div class="check_all check_box fl" id="js_check_all02">
                                <b class="label"><input type="checkbox" class="js_checkbox input_checkbox"
                                                        name="work_day"
                                                        value="1" <?php echo in_array('1', $company_setting['work_day']) ? "checked" : "" ?>>
                                    周一</b>
                                <b class="label"><input type="checkbox" class="js_checkbox input_checkbox"
                                                        name="work_day"
                                                        value="2" <?php echo in_array('2', $company_setting['work_day']) ? "checked" : "" ?>>
                                    周二</b>
                                <b class="label"><input type="checkbox" class="js_checkbox input_checkbox"
                                                        name="work_day"
                                                        value="3" <?php echo in_array('3', $company_setting['work_day']) ? "checked" : "" ?>>
                                    周三</b>
                                <b class="label"><input type="checkbox" class="js_checkbox input_checkbox"
                                                        name="work_day"
                                                        value="4" <?php echo in_array('4', $company_setting['work_day']) ? "checked" : "" ?>>
                                    周四</b>
                                <b class="label"><input type="checkbox" class="js_checkbox input_checkbox"
                                                        name="work_day"
                                                        value="5" <?php echo in_array('5', $company_setting['work_day']) ? "checked" : "" ?>>
                                    周五</b>
                                <b class="label"><input type="checkbox" class="js_checkbox input_checkbox"
                                                        name="work_day"
                                                        value="6" <?php echo in_array('6', $company_setting['work_day']) ? "checked" : "" ?>>
                                    周六</b>
                                <b class="label"><input type="checkbox" class="js_checkbox input_checkbox"
                                                        name="work_day"
                                                        value="7" <?php echo in_array('7', $company_setting['work_day']) ? "checked" : "" ?>>
                                    周日</b>

                            </div>
                            <!--提示部分-->
                            <span class="zws_power_remind_con" id="prompt">
								<div class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img
                                            src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制打卡工作日。</div>
							</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fl">
                                <span class="label fl">早上上班时间</span>
                                <input type="text" class="find_call" name="work_day_up_time"
                                       value="<?php echo $company_setting['work_day_up_time']; ?>"
                                       onfocus="WdatePicker({lang:'zh-cn',dateFmt:'HH:mm:ss'})">
                            </div>
                            <!--提示部分-->
                            <span class="zws_power_remind_con" id="prompt">
								<div class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img
                                            src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制早上打卡时间。</div>
							</span>
                        </td>
                        <td>
                            <div class="fl">
                                <span class="label2 fl">晚上下班时间</span>
                                <input type="text" class="find_call" name="work_day_down_time"
                                       value="<?php echo $company_setting['work_day_down_time']; ?>"
                                       onfocus="WdatePicker({lang:'zh-cn',dateFmt:'HH:mm:ss'})">
                            </div>
                            <!--提示部分-->
                            <span class="zws_power_remind_con" id="prompt">
								<div class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img
                                            src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制下午打卡时间。</div>
							</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fl">
                                <span class="label fl">房客源制</span>
                                <input type="radio" class="find_call" name="house_customer_system" value="1" <?php echo $company_setting['house_customer_system']=="1"?"checked":""?>>私盘制
                                <input type="radio" class="find_call" name="house_customer_system" value="2" <?php echo $company_setting['house_customer_system']=="2"?"checked":""?>>公盘私客制
                                <input type="radio" class="find_call" name="house_customer_system" value="3" <?php echo $company_setting['house_customer_system']=="3"?"checked":""?>>公盘制
                            </div>
                            <!--提示部分-->
                            <span class="zws_power_remind_con" id="prompt">
								<div class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								私盘制：只能录入私盘。公盘制：只能录入公盘。公盘私客制：房源只能公盘、客源只能私盘。</div>
							</span>
                        </td>
                        <?php if('37'==$city_id){ ?>
                            <td>
                                <input type="hidden" value="<?php echo $company_setting['is_lock_cmt_wh']; ?>" name="is_lock_cmt_wh" />
                                <span class="label2 fl">是否开启系统楼盘字典</span>
                                <input type="radio" class="find_call" name="is_lock_cmt_wh" value="1" <?php echo $company_setting['is_lock_cmt_wh']=="1"?"checked":""?>>是
                                <input type="radio" class="find_call" name="is_lock_cmt_wh" value="0" <?php echo $company_setting['is_lock_cmt_wh']=="0"?"checked":""?>>否
                            </td>
                        <?php }else{ ?>
                            <td>
                                <input type="hidden" value="<?php echo $company_setting['is_lock_cmt']; ?>" name="is_lock_cmt" />
                                <span class="label2 fl">是否开启自有楼盘字典</span>
                                <input type="radio" class="find_call" name="is_lock_cmt" value="1" <?php echo $company_setting['is_lock_cmt']=="1"?"checked":""?>>是
                                <input type="radio" class="find_call" name="is_lock_cmt" value="0" <?php echo $company_setting['is_lock_cmt']=="0"?"checked":""?>>否
                            </td>
                        <?php } ?>
                    </tr>
				</table>
                <!--是否开启合作中心 旧数据-->
				<input type="hidden" value="<?php echo $company_setting['open_cooperate'];?>" name="old_open_cooperate"/>
				<!--是否开启合作审核 旧数据-->
				<input type="hidden" value="<?php echo $company_setting['check_cooperate'];?>" name="old_check_cooperate"/>
				<!--查看房源密信息必须写跟进 旧数据-->
				<input type="hidden" value="<?php echo $company_setting['is_secret_follow'];?>" name="old_is_secret_follow"/>
            </div>
            <div class="set_basic_content mt15">
				<p class="title">房源设置</p>
				<table>
					<tr>
						<td width="40%">
							<span class="label fl">出租自动变公盘</span>
							<input type="text" class="auto_pre fl input-num" name="rent_house_nature_public" value="<?php echo $company_setting['rent_house_nature_public']>"0"?$company_setting['rent_house_nature_public']:""?>">
							<span class="fl">天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								出租房源超过XX天，自动变成公盘。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">出租信息登记时间</span>
							<input type="text" class="auto_pre fl input-num" name="rent_house_check_time" value="<?php echo $company_setting['rent_house_check_time']>"0"?$company_setting['rent_house_check_time']:""?>">
							<span class="fl">天后未勘察，信息红色警告【默认<?php echo $base_setting['rent_house_check_time'];?>天】 </span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出售勘察时间，提醒勘察。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">出售自动变公盘</span>
							<input type="text" class="auto_pre fl input-num" name="sell_house_nature_public" value="<?php echo $company_setting['sell_house_nature_public']>"0"?$company_setting['sell_house_nature_public']:""?>">
							<span class="fl">天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								出售房源超过XX天，自动变成公盘。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">出售信息登记</span>
							<input type="text" class="auto_pre fl input-num" name="sell_house_check_time" value="<?php echo $company_setting['sell_house_check_time']>"0"?$company_setting['sell_house_check_time']:""?>">
							<span class="fl">天后未勘察，信息红色警告【默认<?php echo $base_setting['sell_house_check_time'];?>天】 </span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出租勘察时间，提醒勘察。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">出租信息默认查询时间</span>
							<select class="auto_pre2 fl" name="rent_house_query_time">
								<option value="1" <?php echo $company_setting['rent_house_query_time']=="1"?"selected":""?>>半年</option>
								<option value="2" <?php echo $company_setting['rent_house_query_time']=="2"?"selected":""?>>一年</option>
								<option value="0" <?php echo $company_setting['rent_house_query_time']=="0"?"selected":""?>>不限</option>
							</select>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出租信息默认查询时间。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">两次房源跟进间隔超过</span>
							<input type="text" class="auto_pre fl input-num" name="house_follow_spacing_time" value="<?php echo $company_setting['house_follow_spacing_time']>"0"?$company_setting['house_follow_spacing_time']:""?>">
							<span class="fl">天，信息变成橙色警告【默认<?php echo $base_setting['house_follow_spacing_time'];?>天】 </span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制两次跟进时间。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">出售信息默认查询时间</span>
							<select class="auto_pre2 fl" name="sell_house_query_time">
								<option value="1" <?php echo $company_setting['sell_house_query_time']=="1"?"selected":""?>>半年</option>
								<option value="2" <?php echo $company_setting['sell_house_query_time']=="2"?"selected":""?>>一年</option>
								<option value="0" <?php echo $company_setting['sell_house_query_time']=="0"?"selected":""?>>不限</option>
							</select>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出售信息默认查询时间。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">出租房源【最后跟进日】超过</span>
							<input id="rent_house_follow_last_time1" type="text" class="auto_pre fl input-num" name="rent_house_follow_last_time1" value="<?php echo $company_setting['rent_house_follow_last_time1']>"0"?$company_setting['rent_house_follow_last_time1']:""?>">
							<span class="fl">天变绿色, 超过</span>
							<input id="rent_house_follow_last_time2" type="text" class="auto_pre fl input-num" name="rent_house_follow_last_time2" value="<?php echo $company_setting['rent_house_follow_last_time2']>"0"?$company_setting['rent_house_follow_last_time2']:""?>">
							<span class="fl">天变紫色</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出租房源最后跟进时间。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="fl">
								<span class="label fl">新增房源默认私盘</span>
								<input type="radio" class="find_call" name="is_house_private" value="1" <?php echo $company_setting['is_house_private']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_house_private" value="0" <?php echo $company_setting['is_house_private']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								新增房源默认公私盘控制。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">出售房源【最后跟进日】超过</span>
							<input id="sell_house_follow_last_time1" type="text" class="auto_pre fl input-num" name="sell_house_follow_last_time1" value="<?php echo $company_setting['sell_house_follow_last_time1']>"0"?$company_setting['sell_house_follow_last_time1']:""?>">
							<span class="fl">天变绿色, 超过</span>
							<input id="sell_house_follow_last_time2" type="text" class="auto_pre fl input-num" name="sell_house_follow_last_time2" value="<?php echo $company_setting['sell_house_follow_last_time2']>"0"?$company_setting['sell_house_follow_last_time2']:""?>">
							<span class="fl">天变紫色</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出售房源最后跟进时间。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">查看出售业主信息</span>
							<input type="text" class="auto_pre fl input-num" name="sell_house_secrecy_time" value="<?php echo $company_setting['sell_house_secrecy_time']>"0"?$company_setting['sell_house_secrecy_time']:""?>">
							<span class="fl">条/天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出售业主每天被查看次数。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">查看出租业主信息</span>
							<input type="text" class="auto_pre fl input-num" name="rent_house_secrecy_time" value="<?php echo $company_setting['rent_house_secrecy_time']>"0"?$company_setting['rent_house_secrecy_time']:""?>">
							<span class="fl">条/天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制出租业主每天被查看次数。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">每人的出售私盘数量</span>
							<input type="text" class="auto_pre fl input-num" name="sell_house_private_num" value="<?php echo $company_setting['sell_house_private_num']>"0"?$company_setting['sell_house_private_num']:""?>">
							<span class="fl">条（仅限纯公盘）</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								与纯公盘制度配合，每人的独有私盘出售数量控制。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">每人的出租私盘数量</span>
							<input type="text" class="auto_pre fl input-num" name="rent_house_private_num" value="<?php echo $company_setting['rent_house_private_num']>"0"?$company_setting['rent_house_private_num']:""?>">
							<span class="fl">条（仅限纯公盘）</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								与纯公盘制度配合，每人的独有私盘出租数量控制。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="fl">
								<span class="label fl">栋座单元门牌归属保密信息</span>
								<input type="radio" class="find_call" name="is_secrecy_information" value="1" <?php echo $company_setting['is_secrecy_information']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_secrecy_information" value="0" <?php echo $company_setting['is_secrecy_information']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制楼栋单元门牌是否属于保密信息。</div>
							</span>
						</td>
						<td>
							<div class="fl">
								<span class="label2 fl">查看房源密信息必须写跟进</span>
								<input type="radio" class="find_call" name="is_secret_follow" value="1" <?php echo $company_setting['is_secret_follow']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_secret_follow" value="0" <?php echo $company_setting['is_secret_follow']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制查看房源是否必须跟进。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
                            <span class="label fl">超过</span><input type="text" class="auto_pre fl input-num" name="house_public_time" value="<?php echo $company_setting['house_public_time']>"0"?$company_setting['house_public_time']:""?>">天未跟进，房源变成没有归属人的公共房源（0天代表不开启）
						</td>
					</tr>
          <tr>
						<td>
							<span class="label fl">房源默认显示范围</span>
							<select class="auto_pre2 fl" name="sell_house_indication_range">
								<option value="1" <?php echo $company_setting['sell_house_indication_range']=="1"?"selected":""?>>公司</option>
								<option value="2" <?php echo $company_setting['sell_house_indication_range']=="2"?"selected":""?>>门店</option>
								<option value="3" <?php echo $company_setting['sell_house_indication_range']=="3"?"selected":""?>>个人</option>
							</select>
            </td>
						<!--<td>
							<div class="fl">
								<span class="label2 fl">房源必须同步</span>
								<input type="radio" class="find_call" name="is_fang100_insert" value="1" <?php /*echo $company_setting['is_fang100_insert']=="1"?"checked":""*/?>>是
								<input type="radio" class="find_call" name="is_fang100_insert" value="0" <?php /*echo $company_setting['is_fang100_insert']=="0"?"checked":""*/?>>否
							</div>
						</td>-->
					</tr>
					<tr>
            <td colspan="2">
							<span class="label fl">出售房源列表页自定义</span>
              <div class="con_w" style="float:left;display: inline;">
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="1" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('1',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">标签</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="2" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('2',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">状态</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="3" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('3',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">性质</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="4" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('4',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">合作</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="5" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('5',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">区属</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="6" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('6',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">板块</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="7" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('7',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">楼盘</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="8" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('8',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">物业类型</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="9" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('9',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">面积</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="10" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('10',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">总价</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="11" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('11',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">栋座</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="12" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('12',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">单元</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="13" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('13',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">门牌</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="14" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('14',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">户型</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="15" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('15',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">楼层</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="16" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('16',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">朝向</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="17" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('17',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">单价</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="18" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('18',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">税费</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="22" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('22',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">车库</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="19" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('19',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">装修</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="23" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('23',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">登记时间</b></p>

                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="20" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('20',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">跟进时间</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="21" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('21',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">经纪人</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="24" class="zws_room_add_box checkon_zws" name="sell_house_field" <?php echo in_array('24',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">编号</b></p>

								<!--提示部分-->
								<span class="zws_power_remind_con" id="prompt">
									<div  class="zws_power_remind"></div>
									<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
									<div class="zws_power_remind_div">控制出售列表页展示字段。</div>
								</span>
              </div>
						</td>
					</tr>
					<tr>
            <td colspan="2">
							<span class="label fl">出租房源列表页自定义</span>
              <div class="con_w" style="float:left;display: inline;">
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="1" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('1',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">标签</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="2" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('2',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">状态</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="3" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('3',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">性质</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="4" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('4',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">合作</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="5" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('5',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">区属</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="6" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('6',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">板块</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="7" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('7',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">楼盘</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="8" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('8',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">物业类型</b></p>
                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="9" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('9',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">面积</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="10" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('10',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">租金</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="11" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('11',$company_setting['sell_house_field'])?"checked":""?>><b style="padding-left: 5px;">栋座</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="12" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('12',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">单元</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="13" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('13',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">门牌</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="14" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('14',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">户型</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="15" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('15',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">楼层</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="16" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('16',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">朝向</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="17" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('17',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">装修</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="18" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('18',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">跟进时间</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="19" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('19',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">经纪人</b></p>
                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="20" class="zws_room_add_box checkon_zws" name="rent_house_field" <?php echo in_array('20',$company_setting['rent_house_field'])?"checked":""?>><b style="padding-left: 5px;">编号</b></p>
								<!--提示部分-->
								<span class="zws_power_remind_con" id="prompt">
									<div  class="zws_power_remind"></div>
									<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
									<div class="zws_power_remind_div">控制出租列表页展示字段。</div>
								</span>
              </div>
						</td>
					</tr>
					<tr>
                        <!--						<td>-->
                        <!--							<span class="label fl">群发他人房源</span>-->
                        <!--							<input type="radio" class="find_call" name="publish_other_house" value="1" -->
                        <?php //echo $company_setting['publish_other_house']=="1"?"checked":""?><!--是-->
                        <!--							<input type="radio" class="find_call" name="publish_other_house" value="0" -->
                        <?php //echo $company_setting['publish_other_house']=="0"?"checked":""?><!--否-->
                        <!--						</td>-->
					</tr>
				</table>
			</div>
        <div class="set_basic_content mt15">
				<p class="title">客源设置</p>
				<table>
					<tr>
						<td width="40%">
							<span class="label fl">求购自动变公客</span>
							<input type="text" class="auto_pre fl input-num" name="buy_customer_nature_public" value="<?php echo $company_setting['buy_customer_nature_public']>"0"?$company_setting['buy_customer_nature_public']:""?>">
							<span class="fl">天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求购自动变公盘的时间。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">求购信息登记时间</span>
							<input type="text" class="auto_pre fl input-num" name="buy_customer_check_time" value="<?php echo $company_setting['buy_customer_check_time']>"0"?$company_setting['buy_customer_check_time']:""?>">
							<span class="fl">天后未勘察，信息红色警告【默认<?php echo $base_setting['buy_customer_check_time'];?>天】 </span>
							<em class="errorBox"></em>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求购勘察提醒时间。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">求租自动变公客</span>
							<input type="text" class="auto_pre fl input-num" name="rent_customer_nature_public" value="<?php echo $company_setting['rent_customer_nature_public']>"0"?$company_setting['rent_customer_nature_public']:""?>">
							<span class="fl">天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求租自动变公盘的时间。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">求租信息登记时间</span>
							<input type="text" class="auto_pre fl input-num" name="rent_customer_check_time" value="<?php echo $company_setting['rent_customer_check_time']>"0"?$company_setting['rent_customer_check_time']:""?>">
							<span class="fl">天后未勘察，信息红色警告【默认<?php echo $base_setting['rent_customer_check_time'];?>天】 </span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求租勘察提醒时间。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">求购信息默认查询时间</span>
							<select class="auto_pre2 fl" name="buy_customer_query_time">
								<option value="1" <?php echo $company_setting['buy_customer_query_time']=="1"?"selected":""?>>半年</option>
								<option value="2" <?php echo $company_setting['buy_customer_query_time']=="2"?"selected":""?>>一年</option>
								<option value="0" <?php echo $company_setting['buy_customer_query_time']=="0"?"selected":""?>>不限</option>
							</select>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求购默认查询时间。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">两次客源跟进间隔超过</span>
							<input type="text" class="auto_pre fl input-num" name="customer_follow_spacing_time" value="<?php echo $company_setting['customer_follow_spacing_time']>"0"?$company_setting['customer_follow_spacing_time']:""?>">
							<span class="fl">天，信息变成橙色警告【默认<?php echo $base_setting['customer_follow_spacing_time'];?>天】 </span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制两次跟进之间提醒。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">求租信息默认查询时间</span>
							<select class="auto_pre2 fl" name="rent_customer_query_time">
								<option value="1" <?php echo $company_setting['rent_customer_query_time']=="1"?"selected":""?>>半年</option>
								<option value="2" <?php echo $company_setting['rent_customer_query_time']=="2"?"selected":""?>>一年</option>
								<option value="0" <?php echo $company_setting['rent_customer_query_time']=="0"?"selected":""?>>不限</option>
							</select>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求租默认查看时间。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">求购客源【最后跟进日】超过</span>
							<input id="buy_customer_follow_last_time1" type="text" class="auto_pre fl input-num" name="buy_customer_follow_last_time1" value="<?php echo $company_setting['buy_customer_follow_last_time1']>"0"?$company_setting['buy_customer_follow_last_time1']:""?>">
							<span class="fl">天变绿色, 超过</span>
							<input id="buy_customer_follow_last_time2" type="text" class="auto_pre fl input-num" name="buy_customer_follow_last_time2" value="<?php echo $company_setting['buy_customer_follow_last_time2']>"0"?$company_setting['buy_customer_follow_last_time2']:""?>">
							<span class="fl">天变紫色</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">控制求购最后跟进时间提醒。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<div class="fl">
								<span class="label fl">新增客源默认私客</span>
								<input type="radio" class="find_call" name="is_customer_private" value="1" <?php echo $company_setting['is_customer_private']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="is_customer_private" value="0" <?php echo $company_setting['is_customer_private']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制新增客源属性。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">求租客源【最后跟进日】超过</span>
							<input id="rent_customer_follow_last_time1" type="text" class="auto_pre fl input-num" name="rent_customer_follow_last_time1"
								   value="<?php echo $company_setting['rent_customer_follow_last_time1']>"0"?$company_setting['rent_customer_follow_last_time1']:""?>">
							<span class="fl">天变绿色, 超过</span>
							<input id="rent_customer_follow_last_time2" type="text" class="auto_pre fl input-num" name="rent_customer_follow_last_time2"
								   value="<?php echo $company_setting['rent_customer_follow_last_time2']>"0"?$company_setting['rent_customer_follow_last_time2']:""?>">
							<span class="fl">天变紫色</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求租最后跟进时间提醒。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">查看求购客源信息</span>
							<input type="text" class="auto_pre fl input-num" name="buy_customer_secrecy_time" value="<?php echo $company_setting['buy_customer_secrecy_time']>"0"?$company_setting['buy_customer_secrecy_time']:""?>">
							<span class="fl">条/天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求购每天被看次数。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">查看求租客源信息</span>
							<input type="text" class="auto_pre fl input-num" name="rent_customer_secrecy_time" value="<?php echo $company_setting['rent_customer_secrecy_time']>"0"?$company_setting['rent_customer_secrecy_time']:""?>">
							<span class="fl">条/天</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求租每日被看次数。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label fl">每人的求购私客数量</span>
							<input type="text" class="auto_pre fl input-num" name="buy_customer_private_num" value="<?php echo $company_setting['buy_customer_private_num']>"0"?$company_setting['buy_customer_private_num']:""?>">
							<span class="fl">条（仅限纯公盘）</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								与纯公盘制配合每人最多求购私盘数量。</div>
							</span>
						</td>
						<td>
							<span class="label2 fl">每人的求租私客数量</span>
							<input type="text" class="auto_pre fl input-num" name="rent_customer_private_num" value="<?php echo $company_setting['rent_customer_private_num']>"0"?$company_setting['rent_customer_private_num']:""?>">
							<span class="fl">条（仅限纯公盘）</span>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								与纯公盘制配合没人最多求租私盘数量。</div>
							</span>
						</td>
					</tr>
                    <tr>
						<td>
							<div class="fl">
								<span class="label fl">求购是否开启去重</span>
								<input type="radio" class="find_call" name="buy_customer_unique" value="1" <?php echo $company_setting['buy_customer_unique']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="buy_customer_unique" value="0" <?php echo $company_setting['buy_customer_unique']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求购是否去重。</div>
							</span>
						</td>
						<td>
							<div class="fl">
								<span class="labe2 fl">求租是否开启去重</span>
								<input type="radio" class="find_call" name="rent_customer_unique" value="1" <?php echo $company_setting['rent_customer_unique']=="1"?"checked":""?>>是
								<input type="radio" class="find_call" name="rent_customer_unique" value="0" <?php echo $company_setting['rent_customer_unique']=="0"?"checked":""?>>否
							</div>
							<!--提示部分-->
							<span class="zws_power_remind_con" id="prompt">
								<div  class="zws_power_remind"></div>
								<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
								<div class="zws_power_remind_div">
								控制求租是否去重。</div>
							</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
                            <span class="label fl">超过</span><input type="text" class="auto_pre fl input-num" name="customer_public_time" value="<?php echo $company_setting['customer_public_time']>"0"?$company_setting['customer_public_time']:""?>">天未跟进，客源变成没有归属人的公共客源（0天代表不开启）
						</td>
					</tr>
                    <tr>
						<td>
							<span class="label fl">客源默认显示范围</span>
							<select class="auto_pre2 fl" name="rent_house_indication_range">
								<option value="1" <?php echo $company_setting['rent_house_indication_range']=="1"?"selected":""?>>公司</option>
								<option value="2" <?php echo $company_setting['rent_house_indication_range']=="2"?"selected":""?>>门店</option>
								<option value="3" <?php echo $company_setting['rent_house_indication_range']=="3"?"selected":""?>>个人</option>
							</select>
                        </td>
					</tr>
					<tr>
                        <td colspan="2">
							<span class="label fl">求购客源列表页自定义</span>
                            <div class="con_w" style="float:left;display: inline;">
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="1" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('1',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">标签</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="2" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('2',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">状态</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="3" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('3',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">性质</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="4" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('4',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">合作</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="5" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('5',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">客户</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="6" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('6',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">意向区属板块</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="7" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('7',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">意向楼盘</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="8" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('8',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">物业类型</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="9" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('9',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">面积</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="10" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('10',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">总价</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="11" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('11',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">户型</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="12" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('12',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">装修</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="13" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('13',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">楼层</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="14" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('14',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">朝向</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="15" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('15',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">房龄</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="16" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('16',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">跟进时间</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="17" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('17',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">经纪人</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="18" class="zws_room_add_box checkon_zws" name="buy_customer_field" <?php echo in_array('18',$company_setting['buy_customer_field'])?"checked":""?>><b style="padding-left: 5px;">编号</b></p>
								<!--提示部分-->
								<span class="zws_power_remind_con" id="prompt">
									<div  class="zws_power_remind"></div>
									<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
									<div class="zws_power_remind_div">
									控制求购列表页自定义。</div>
								</span>
                            </div>
						</td>
					</tr>
					<tr>
                        <td colspan="2">
							<span class="label fl">求租客源列表页自定义</span>
                            <div class="con_w" style="float:left;display: inline;">
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="1" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('1',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">标签</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="2" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('2',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">状态</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="3" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('3',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">性质</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="4" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('4',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">合作</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="5" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('5',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">客户</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="6" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('6',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">意向区属板块</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="7" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('7',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">意向楼盘</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="8" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('8',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">物业类型</b></p>
                                <p style="float:left;display: inline;padding-right: 15px;"><input type="checkbox" value="9" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('9',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;float:left;display: inline;">面积</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="10" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('10',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">租金</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="11" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('11',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">户型</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="12" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('12',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">装修</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="13" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('13',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">楼层</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="14" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('14',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">朝向</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="15" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('15',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">房龄</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="16" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('16',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">跟进时间</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="17" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('17',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">经纪人</b></p>
                                <p style="float: left;padding-right: 15px;"><input type="checkbox" value="18" class="zws_room_add_box checkon_zws" name="rent_customer_field" <?php echo in_array('18',$company_setting['rent_customer_field'])?"checked":""?>><b style="padding-left: 5px;">编号</b></p>
								<!--提示部分-->
								<span class="zws_power_remind_con" id="prompt">
									<div  class="zws_power_remind"></div>
									<span class="zws_power_remind_jt"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/zws-jt_03.jpg"></span>
									<div class="zws_power_remind_div">
									控制求租列表页自定义。</div>
								</span>
                            </div>
						</td>
					</tr>
				</table>
			</div>
		</div>
        <script type="text/javascript">
            $(function(){
                $(".con_w").css("width",($(".set_basic_content").width()-220)+"px")
            })
            $(window).resize(function(){
                $(".con_w").css("width",($(".set_basic_content").width()-220)+"px")
            })
        </script>
		<div class="set_bottom_bar">
			<input type="button" value="保　　存" class="submit_blue" id="save_button">
		</div>
    </div>
        <?php if(0===$setResult){?>
             <script>
                  $(function(){
                      $("#dialog_do_itp").text("保存失败");
                      openWin("js_pop_do_success");
                  });
             </script>
        <?php }else if(1===$setResult){ ?>
             <script>
                  $(function(){
                     $("#dialog_do_itp").text("保存成功");
                     openWin("js_pop_do_success");
                   });
             </script>
        <?php }?>
    </div>
</div>
</form>

<!--权限范围弹框-->
<div class="pop_box_g pop_box_add_shop" id="js_set_per_agency_area" style="width:600px;height:300px;">
    <div class="hd">
        <div class="title">修改范围</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" ></a></div>
    </div>
    <div class="mod">
	<form name="agency_per_area_form" id="agency_per_area_form" method="post">
    <input type="hidden" id="guard_time2" name="guard_time2"><!--系统防护时间-->
    <input type="hidden" id="is_blacklist_check2" name="is_blacklist_check2"><!--黑名单验证-->
    <input type="hidden" id="follow_text_num2" name="follow_text_num2"><!--跟进字数-->
    <input type="hidden" id="is_property_publish2" name="is_property_publish2"><!--楼盘录入-->
    <input type="hidden" id="house_list_order_field2" name="house_list_order_field2"><!--房客源列表排序-->
    <input type="hidden" id="secret_view_num2" name="secret_view_num2"><!--保密信息查看次数上限-->
    <input type="hidden" id="is_remind_open2" name="is_remind_open2"><!--登录时有提醒任务是否自动打开-->
    <input type="hidden" id="open_cooperate2" name="open_cooperate2"><!--是否开启合作中心-->
    <input type="hidden" id="old_open_cooperate2" name="old_open_cooperate2"><!--是否开启合作中心旧数据-->
    <input type="hidden" id="check_cooperate2" name="check_cooperate2"><!--是否开启合作审核-->
    <input type="hidden" id="old_check_cooperate2" name="old_check_cooperate2"><!--是否开启合作审核旧数据-->
    <input type="hidden" id="old_is_secret_follow2" name="old_is_secret_follow2"><!--查看房源密信息必须写跟进-->

    <input type="hidden" id="is_community_modify_house2" name="is_community_modify_house2"><!--楼盘字典同步变化-->
    <input type="hidden" id="rent_house_nature_public2" name="rent_house_nature_public2"><!--出租自动变公盘-->
    <input type="hidden" id="house_public_time2" name="house_public_time2"><!--房源变成没有归属人的公共房源-->
    <input type="hidden" id="customer_public_time2" name="customer_public_time2"><!--客源变成没有归属人的公共客源-->
    <input type="hidden" id="buy_customer_nature_public2" name="buy_customer_nature_public2"><!--求购自动变公客-->
    <input type="hidden" id="sell_house_nature_public2" name="sell_house_nature_public2"><!--出售自动变公盘-->
    <input type="hidden" id="rent_customer_nature_public2" name="rent_customer_nature_public2"><!--求租自动变公客-->
    <input type="hidden" id="sell_house_query_time2" name="sell_house_query_time2"><!--出售信息默认查询时间-->
    <input type="hidden" id="buy_customer_query_time2" name="buy_customer_query_time2"><!--求购信息默认查询时间-->
    <input type="hidden" id="rent_house_query_time2" name="rent_house_query_time2"><!--出租信息默认查询时间-->
    <input type="hidden" id="rent_customer_query_time2" name="rent_customer_query_time2"><!--求租信息默认查询时间-->
    <input type="hidden" id="rent_house_check_time2" name="rent_house_check_time2"><!--出租信息登记时间-->
    <input type="hidden" id="buy_customer_check_time2" name="buy_customer_check_time2"><!--求购信息登记时间-->
    <input type="hidden" id="sell_house_check_time2" name="sell_house_check_time2"><!--出售信息登记-->
    <input type="hidden" id="rent_customer_check_time2" name="rent_customer_check_time2"><!--求租信息登记时间-->
    <input type="hidden" id="customer_follow_spacing_time2" name="customer_follow_spacing_time2"><!--两次客源跟进间隔超过 -->
    <input type="hidden" id="house_follow_spacing_time2" name="house_follow_spacing_time2"><!--两次房源跟进间隔超过-->

    <input type="hidden" id="is_house_private2" name="is_house_private2"><!--新增房源默认私盘-->
    <input type="hidden" id="is_customer_private2" name="is_customer_private2"><!--新增客源默认私盘-->
    <input type="hidden" id="buy_customer_unique2" name="buy_customer_unique2"><!--求购客源是否去重-->
    <input type="hidden" id="rent_customer_unique2" name="rent_customer_unique2"><!--求租客源是否去重-->
    <input type="hidden" id="is_secrecy_information2" name="is_secrecy_information2"><!--栋座单元门牌归属保密信-->
    <input type="hidden" id="is_secret_follow2" name="is_secret_follow2"><!--查看房源密信息必须写跟进-->
    <input type="hidden" id="is_fang100_insert2" name="is_fang100_insert2"><!--房源必须同步-->
    <input type="hidden" id="sell_house_follow_last_time1_form" name="sell_house_follow_last_time1_form"><!--出售房源最后跟进日-->
    <input type="hidden" id="sell_house_follow_last_time2_form" name="sell_house_follow_last_time2_form"><!--出售房源最后跟进日-->
    <input type="hidden" id="rent_house_follow_last_time1_form" name="rent_house_follow_last_time1_form"><!--出租房源最后跟进日-->
    <input type="hidden" id="rent_house_follow_last_time2_form" name="rent_house_follow_last_time2_form"><!--出租房源最后跟进日-->
    <input type="hidden" id="buy_customer_follow_last_time1_form" name="buy_customer_follow_last_time1_form"><!--求购客源最后跟进日-->
    <input type="hidden" id="buy_customer_follow_last_time2_form" name="buy_customer_follow_last_time2_form"><!--求购客源最后跟进日-->
    <input type="hidden" id="rent_customer_follow_last_time1_form" name="rent_customer_follow_last_time1_form"><!--求租客源最后跟进日-->
    <input type="hidden" id="rent_customer_follow_last_time2_form" name="rent_customer_follow_last_time2_form"><!--求租客源最后跟进日-->
    <input type="hidden" id="is_check_work2" name="is_check_work2"><!--是否早晚打卡-->
    <input type="hidden" id="work_day2" name="work_day2"><!--工作日-->
    <input type="hidden" id="sell_house_indication_range2" name="sell_house_indication_range2"><!--出售房源列表页自定义-->
    <input type="hidden" id="rent_house_indication_range2" name="rent_house_indication_range2"><!--出售房源列表页自定义-->
    <input type="hidden" id="sell_house_field2" name="sell_house_field2"><!--出售房源列表页自定义-->
    <input type="hidden" id="rent_house_field2" name="rent_house_field2"><!--出租房源列表页自定义-->
    <input type="hidden" id="buy_customer_field2" name="buy_customer_field2"><!--求购客源列表页自定义-->
    <input type="hidden" id="rent_customer_field2" name="rent_customer_field2"><!--求租客源列表页自定义-->
    <input type="hidden" id="work_day_up_time2" name="work_day_up_time2"><!--上班时间-->
    <input type="hidden" id="work_day_down_time2" name="work_day_down_time2"><!--下班时间-->
    <input type="hidden" id="sell_house_secrecy_time2" name="sell_house_secrecy_time2"><!--出售房源保密信息次数-->
    <input type="hidden" id="rent_house_secrecy_time2" name="rent_house_secrecy_time2"><!--出租房源保密信息次数-->
    <input type="hidden" id="buy_customer_secrecy_time2" name="buy_customer_secrecy_time2"><!--求购客源保密信息次数-->
    <input type="hidden" id="rent_customer_secrecy_time2" name="rent_customer_secrecy_time2"><!--求租客源保密信息次数-->
    <input type="hidden" id="house_customer_system2" name="house_customer_system2"><!--房客源制-->
    <input type="hidden" id="is_lock_cmt2" name="is_lock_cmt2"><!--是否锁盘-->
    <input type="hidden" id="is_lock_cmt_wh2" name="is_lock_cmt_wh2"><!--是否锁盘武汉-->
    <input type="hidden" id="sell_house_private_num2" name="sell_house_private_num2"><!--每人的出售私盘数量-->
    <input type="hidden" id="rent_house_private_num2" name="rent_house_private_num2"><!--每人的出租私盘数量-->
    <input type="hidden" id="buy_customer_private_num2" name="buy_customer_private_num2"><!--每人的求购私客数量-->
    <input type="hidden" id="rent_customer_private_num2" name="rent_customer_private_num2"><!--每人的求租私客数量-->
		<input type="hidden" id="publish_other_house2" name="publish_other_house2"><!--群发他人房源-->


		<div style="overflow-y:scroll;width:560px;height:193px;margin:10px auto;background:#FFF; padding:6px 0 0 5px;">
        <?php if(is_array($all_company_info) && !empty($all_company_info)){
			foreach(array_reverse($all_company_info) as $k=>$v) {?>
			<div onmouseover="this.style.background='#EEE';" onmouseout="this.style.background='#FFF';" style="float:left;width:120px;height:26px;overflow:hidden;line-height:22px;font-weight:14px;padding:5px 0 0 10px;">
				<label style="display:block;width:120px;cursor:pointer;">
					<input type="checkbox" <?php if($now_agency_id == $v['id']){ ?>checked disabled="disabled"<?php }else{ ?> class="agency_access_area" name="agency_access_area[]"<?php } ?> value="<?php echo $v['id']?>">&nbsp;&nbsp;<?php echo $now_agency_id == $v['id'] ? "<span style='color:#999;'>".$v['name']."</span>" : $v['name']?>
				</label>
			</div>
            <?php
                if(isset($v['next_agency_data']) && !empty($v['next_agency_data'])){
                    foreach($v['next_agency_data'] as $key => $value){ ?>
                        <div onmouseover="this.style.background='#EEE';" onmouseout="this.style.background='#FFF';" style="float:left;width:120px;height:26px;overflow:hidden;line-height:22px;font-weight:14px;padding:5px 0 0 10px;">
                            <label style="display:block;width:120px;cursor:pointer;">
                                <input type="checkbox" <?php if($now_agency_id == $value['id']){ ?>checked disabled="disabled"<?php }else{ ?> class="agency_access_area" name="agency_access_area[]"<?php } ?> value="<?php echo $value['id']?>">&nbsp;&nbsp;<?php echo $now_agency_id == $value['id'] ? "<span style='color:#999;'>".$value['name']."</span>" : $value['name']?>
                            </label>
                        </div>
            <?php
                    }
                }
            ?>
		<?php } }?>
		</div>
		<input type="hidden" name="agency_access_area[]" value="<?php echo $now_agency_id; ?>">
		<input type="hidden" name="now_agency_id" value="<?php echo $now_agency_id; ?>">
		<div style="position:relative; text-align:center; width:100%;"><label style="position:absolute; left:13px; top:0;"><input type="checkbox" onclick="checkallagency(this);"> 全选</label><a title="根据勾选设置关联指定部门" class="btn-lv btn-left" style="padding-left: 10px;" href="javascript:void(0)" onclick="submit_agency_per_area_form();"><span class="btn_inner" style="padding-right: 10px;">保存设置</span></a><a class="btn-hui1 JS_Close" href="/base/index/"><span>取消</span></a></div>
	</form>
    </div>
</div>

<script>
$(function(){
	document.onkeydown = function(e){ //enter
		var ev = document.all ? window.event : e;
		if(ev.keyCode==13) {
			submit_agency_per_area_form();return false;
		 }
	}
});
</script>
<div id="js_pop_do_warning" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <p style="text-align:center;padding: 15px 0;"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png" style="margin-right:10px;"><span id="dialog_do_warnig_tip"></span></p>
        </div>
    </div>
</div
    <!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="location=location;return false;" title="关闭" class="JS_Close iconfont"></a>
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
<img id="mainloading" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif"><!--遮罩 loading-->

<script type="text/javascript">
function checkallagency(obj)
{
    var checkall = obj.checked ? 1 : 0;

    $(".agency_access_area").each(function(){
        checkall ? $(this).attr('checked', true) : $(this).attr('checked', false);
    });
}

function submit_agency_per_area_form()
{
    //将上一步操作的基本设置值，放到表单中。
    $('#guard_time2').val($('input[name="guard_time"]').val());
    $('#is_blacklist_check2').val($('input[name="is_blacklist_check"]:checked').val());
    $('#follow_text_num2').val($('input[name="follow_text_num"]').val());
    $('#is_property_publish2').val($('input[name="is_property_publish"]:checked').val());
    $('#house_list_order_field2').val($('select[name="house_list_order_field"] option:selected').val());
    $('#secret_view_num2').val($('input[name="secret_view_num"]').val());
    $('#is_remind_open2').val($('input[name="is_remind_open"]:checked').val());
    $('#open_cooperate2').val($('input[name="open_cooperate"]:checked').val());
    $('#old_open_cooperate2').val($('input[name="old_open_cooperate"]').val());
    $('#check_cooperate2').val($('input[name="check_cooperate"]:checked').val());
    $('#old_check_cooperate2').val($('input[name="old_check_cooperate"]').val());
    $('#old_is_secret_follow2').val($('input[name="old_is_secret_follow"]').val());
    $('#is_community_modify_house2').val($('input[name="is_community_modify_house"]:checked').val());
    $('#rent_house_nature_public2').val($('input[name="rent_house_nature_public"]').val());
    $('#house_public_time2').val($('input[name="house_public_time"]').val());
    $('#customer_public_time2').val($('input[name="customer_public_time"]').val());

    $('#buy_customer_nature_public2').val($('input[name="buy_customer_nature_public"]').val());
    $('#sell_house_nature_public2').val($('input[name="sell_house_nature_public"]').val());
    $('#rent_customer_nature_public2').val($('input[name="rent_customer_nature_public"]').val());
    $('#sell_house_query_time2').val($('select[name="sell_house_query_time"] option:selected').val());
    $('#sell_house_indication_range2').val($('select[name="sell_house_indication_range"] option:selected').val());
    $('#rent_house_indication_range2').val($('select[name="rent_house_indication_range"] option:selected').val());
    $('#buy_customer_query_time2').val($('select[name="buy_customer_query_time"] option:selected').val());
    $('#rent_house_query_time2').val($('select[name="rent_house_query_time"] option:selected').val());
    $('#rent_customer_query_time2').val($('select[name="rent_customer_query_time"] option:selected').val());
    $('#rent_house_check_time2').val($('input[name="rent_house_check_time"]').val());
    $('#buy_customer_check_time2').val($('input[name="buy_customer_check_time"]').val());
    $('#sell_house_check_time2').val($('input[name="sell_house_check_time"]').val());
    $('#rent_customer_check_time2').val($('input[name="rent_customer_check_time"]').val());
    $('#customer_follow_spacing_time2').val($('input[name="customer_follow_spacing_time"]').val());
    $('#house_follow_spacing_time2').val($('input[name="house_follow_spacing_time"]').val());

    $('#is_house_private2').val($('input[name="is_house_private"]:checked').val());
    $('#buy_customer_unique2').val($('input[name="buy_customer_unique"]:checked').val());
    $('#rent_customer_unique2').val($('input[name="rent_customer_unique"]:checked').val());
    $('#is_customer_private2').val($('input[name="is_customer_private"]:checked').val());
    $('#is_secrecy_information2').val($('input[name="is_secrecy_information"]:checked').val());
    $('#is_secret_follow2').val($('input[name="is_secret_follow"]:checked').val());
	$('#is_fang100_insert2').val($('input[name="is_fang100_insert"]:checked').val());
    $('#sell_house_follow_last_time1_form').val($('input[name="sell_house_follow_last_time1"]').val());
    $('#sell_house_follow_last_time2_form').val($('input[name="sell_house_follow_last_time2"]').val());
    $('#rent_house_follow_last_time1_form').val($('input[name="rent_house_follow_last_time1"]').val());
    $('#rent_house_follow_last_time2_form').val($('input[name="rent_house_follow_last_time2"]').val());
    $('#buy_customer_follow_last_time1_form').val($('input[name="buy_customer_follow_last_time1"]').val());
    $('#buy_customer_follow_last_time2_form').val($('input[name="buy_customer_follow_last_time2"]').val());
    $('#rent_customer_follow_last_time1_form').val($('input[name="rent_customer_follow_last_time1"]').val());
    $('#rent_customer_follow_last_time2_form').val($('input[name="rent_customer_follow_last_time2"]').val());

    $('#sell_house_secrecy_time2').val($('input[name="sell_house_secrecy_time"]').val());
    $('#rent_house_secrecy_time2').val($('input[name="rent_house_secrecy_time"]').val());
    $('#buy_customer_secrecy_time2').val($('input[name="buy_customer_secrecy_time"]').val());
    $('#rent_customer_secrecy_time2').val($('input[name="rent_customer_secrecy_time"]').val());

    $('#sell_house_private_num2').val($('input[name="sell_house_private_num"]').val());
    $('#rent_house_private_num2').val($('input[name="rent_house_private_num"]').val());
    $('#buy_customer_private_num2').val($('input[name="buy_customer_private_num"]').val());
    $('#rent_customer_private_num2').val($('input[name="rent_customer_private_num"]').val());

    $('#house_customer_system2').val($('input[name="house_customer_system"]:checked').val());
    $('#is_lock_cmt2').val($('input[name="is_lock_cmt"]:checked').val());
    $('#is_lock_cmt_wh2').val($('input[name="is_lock_cmt_wh"]:checked').val());

    //工作日
	var work_day_id = [];
	$("input[name='work_day']").each(function() {
		if ($(this).attr("checked")) {
			work_day_id.push($(this).val());
		}
	});
    //出售房源字段展示
	var sell_house_field_id = [];
	$("input[name='sell_house_field']").each(function() {
		if ($(this).attr("checked")) {
			sell_house_field_id.push($(this).val());
		}
	});
    //出租房源字段展示
	var rent_house_field_id = [];
	$("input[name='rent_house_field']").each(function() {
		if ($(this).attr("checked")) {
			rent_house_field_id.push($(this).val());
		}
	});
    //求购客源字段展示
	var buy_customer_field_id = [];
	$("input[name='buy_customer_field']").each(function() {
		if ($(this).attr("checked")) {
			buy_customer_field_id.push($(this).val());
		}
	});
    //求租客源字段展示
	var rent_customer_field_id = [];
	$("input[name='rent_customer_field']").each(function() {
		if ($(this).attr("checked")) {
			rent_customer_field_id.push($(this).val());
		}
	});

    $('#is_check_work2').val($('input[name="is_check_work"]:checked').val());
    $('#work_day2').val(work_day_id);
    $('#sell_house_field2').val(sell_house_field_id);
    $('#rent_house_field2').val(rent_house_field_id);
    $('#buy_customer_field2').val(buy_customer_field_id);
    $('#rent_customer_field2').val(rent_customer_field_id);
    $('#work_day_up_time2').val($('input[name="work_day_up_time"]').val());
    $('#work_day_down_time2').val($('input[name="work_day_down_time"]').val());
    $('#publish_other_house2').val($('input[name="publish_other_house"]:checked').val());

    var tt = $("#agency_per_area_form").serializeArray();
    var XBXserializeObject = function(a) {
        var o = {};
        //var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [ o[this.name] ];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    var xbxtest2=XBXserializeObject(tt);
    $.ajax({
        type: "POST",
        url: "/base/save_button_submit/",
       // data:$("#agency_per_area_form").serialize(),
        data: xbxtest2,
        cache:false,
        error:function(){
            $("#dialog_do_warnig_tip").html("系统错误");
            openWin('js_pop_do_warning');
            return false;
        },
        success: function(data){
            if('success'==data){
                $("#dialog_do_itp").text("保存成功");
                openWin("js_pop_do_success");
            }else{
                $("#dialog_do_itp").text("保存成功");
                openWin("js_pop_do_success");
            }
        }
    });
}

$(function(){
    $('#save_button').live('click',function(){
        if(!is_follow){
            $("#dialog_do_warnig_tip").html("最后跟进日天数紫色必须大于绿色");
            openWin('js_pop_do_warning');
            return false;
        }
        var sell_house_field_length = $('input[name="sell_house_field"]:checked').length;
        var rent_house_field_length = $('input[name="rent_house_field"]:checked').length;
        var buy_customer_field_length = $('input[name="buy_customer_field"]:checked').length;
        var rent_customer_field_length = $('input[name="rent_customer_field"]:checked').length;
        if(sell_house_field_length < 2){
            $("#dialog_do_warnig_tip").html("出售房源列表页自定义至少勾选2个");
            openWin('js_pop_do_warning');
            return false;
        }
        if(rent_house_field_length < 2){
            $("#dialog_do_warnig_tip").html("出租房源列表页自定义至少勾选2个");
            openWin('js_pop_do_warning');
            return false;
        }
        if(buy_customer_field_length < 2){
            $("#dialog_do_warnig_tip").html("求购客源列表页自定义至少勾选2个");
            openWin('js_pop_do_warning');
            return false;
        }
        if(rent_customer_field_length < 2){
            $("#dialog_do_warnig_tip").html("求租客源列表页自定义至少勾选2个");
            openWin('js_pop_do_warning');
            return false;
        }
        openWin("js_set_per_agency_area");
    });

	$(".zws_room_add dt").css("width", ($(".zws_room_add").width() - 248) + "px");
	//提示说明
	$(".zws_power_remind_con").find(".zws_power_remind").hover(function(){
		// alert("a");
		$(this).parent(".zws_power_remind_con").find(".zws_power_remind_jt").show();
		$(this).parent(".zws_power_remind_con").find(".zws_power_remind_div").show();
		},function(){
		//alert("aa");
		$(".zws_power_remind_con").find(".zws_power_remind_jt").hide();
		$(".zws_power_remind_con").find(".zws_power_remind_div").hide();
	})
});
</script>
