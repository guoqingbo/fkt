<script type="text/javascript">
$(function() {
	$("#headfile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
			if (patrn.exec(file))
			{
				$("#fileform_head").submit();
			}
			else
			{
				$("#dialog_do_warnig_tip").html("图片格式不正确");
        		openWin('dialog_do_warnig_tip');
				$("#head_red").css("color","#F00");
				return false;
			}
		}
	});
	$("#idnofile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
			if (patrn.exec(file))
			{
				$("#fileform_idno").submit();
			}
			else
			{
				$("#dialog_do_warnig_tip").html("图片格式不正确");
        		openWin('dialog_do_warnig_tip');
				$("#idno_red").css("color","#F00");
				return false;
			}
		}
	});
	$("#cardfile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
			if (patrn.exec(file))
			{
				$("#fileform_card").submit();
			}
			else
			{
				$("#dialog_do_warnig_tip").html("图片格式不正确");
        		openWin('dialog_do_warnig_tip');
				$("#card_red").css("color","#F00");
				return false;
			}
		}
	});
	$("#agencyfile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
			if (patrn.exec(file))
			{
				$("#fileform_agency").submit();
			}
			else
			{
				$("#dialog_do_warnig_tip").html("图片格式不正确");
        		openWin('dialog_do_warnig_tip');
				$("#agency_red").css("color","#F00");
				return false;
			}
		}
	});
});
</script>

<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>

<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>

<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    <a href="javascript:void(0)" onClick="openWin('js_add_shop')" class="btn-lan right"><span>添加经纪人</span></a>
</div>
<div class="table_all">
    <div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c4"><div class="info"><input type="checkbox" id="js_checkbox"></div></td>
                <td class="c12"><div class="info">姓名</div></td>
                <td class="c12"><div class="info">电话</div></td>
                <td class="c12"><div class="info">分店名称</div></td>
                <td class="c12"><div class="info">角色</div></td>
                <!--<td class="c12"><div class="info">资料认证</div></td> -->
                <td class="c12"><div class="info">身份认证</div></td>
                <td class="c12"><div class="info">资质认证</div></td>
                <td ><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div class="inner shop_inner" id="js_inner">
        <table class="table">
            <?php
            if(is_array($agency) && !empty($agency)){
                foreach ($agency as $key =>$value) {
            ?>
            <tr>
                <td class="c4"><div class="info"><input type="checkbox" class="checkbox" name="brokerId" value="<?=$value['broker_id'];?>" ></div></td>
                <td class="c12"><div class="info"><?=$value['truename'] ?></div></td>
                <td class="c12"><div class="info"><?=$value['phone'] ?></div></td>
                <td class="c12"><div class="info"><?=$value['agency_name'] ?></div></td>
                <td width="11%"><div class="info"><?=$value['role_name'] ?></div></td>
            <!--<td class="c12">
                    <div class="info">
                    <//?php
                    if($value['status'] == 1){
                        echo '<span class="s">已认证</span>';
                    }else{
                        echo '未认证';
                    }
                    ?>
                    </div>
                </td> -->
                <td class="c12">
                    <div class="info">
                    <?php
                    if($value['auth_ident_status'] == 2){
                        echo '<span class="s">已认证</span>';
                    }else{
                        echo '未认证';
                    }
                    ?>
                    </div>
                </td>
                <td class="c12">
                    <div class="info">
                    <?php
                    if($value['auth_quali_status'] == 2){
                        echo '<span class="s">已认证</span>';
                    }else{
                        echo '未认证';
                    }
                    ?>
                    </div>
                </td>
                <td>
                    <div class="info">
                        <a href="javascript:void(0)" onClick="modify_broker(<?=$value['broker_id'] ?>,'get_info');" class="fun_link">修改</a>|
                        <?php
                        if($value['broker_id'] == $broker_id){
                        ?>
                        <a class="fun_link" style="color:#ccc">删除</a>
                        <?php
                        }else{
                        ?>
                        <a href="javascript:void(0)" class="fun_link" onclick="delete_broker(<?=$value['broker_id'] ?>)">删除</a>
                        <?php
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <?php
                }
            }else{
           ?>
            <tr><td colspan="9" style="height: 340px;">抱歉，暂无店面部门信息，请添加</td></tr>
            <?php }?>
        </table>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn">
    <!--<label class="btn btn_del"><input type="checkbox" id="js_checkbox">全选</label>-->
    <form action="" name="search_form" method="post" id="subform">
        <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
        </div>
    </form>
    <a href="#" class="grey_btn" onclick="delete_all()">删除</a>
</div>
<div class="pop_box_g pop_box_add_shop" id="js_add_shop">
    <div class="hd">
        <div class="title">添加经纪人</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <?php
        if($auth == 1 && $area == 3){
        ?>
        <label class="label clearfix"><span class="text">门店：</span>
            <select id="agency_id">
            <?php
            if(is_array($agencys) && !empty($agencys)){
                foreach ($agencys as $key =>$value) {
            ?>
                <option value="<?=$value['id'] ?>"><?=$value['name']?></option>
            <?php
                }
            }
            ?>
            </select>
        </label>
        <?php
        }
        ?>
        <label class="label clearfix"><span class="text">手机号码：</span>
            <input class="text_input" type="text" id="phone">
        </label>
        <div class="label clearfix"><span class="text">验证码：</span>
            <input class="text_input input_code" type="text" id="code">
            <input type="button" class="get_code" value="获取验证码" onclick="get_code()">
        </div>
        <label class="label clearfix"><span class="text">登录密码：</span>
            <input class="text_input"  type="password" id="password">
        </label>
        <button class="btn-lv1 btn-mid" style="margin-top:10px;" type="button" onclick="add_broker()">注册</button>
    </div>
</div>
<div class="pop_box_g pop_box_add_shop pop_box_r_shop" id="js_r_shop">
    <div class="hd">
        <div class="title">修改资料</div>
        <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <dl id="js_tab_t01" class="clearfix">
                <dd title="资料认证" class="js_t item itemOn">资料修改</dd>
                <dd title="照片认证" class="js_t item">身份认证</dd>
                <dd title="身份认证" class="js_t item">资质认证</dd>

            </dl>
        </div>
        <div id="js_tab_b01" class="tab_pop_mod tab_pop_mod_shop clear">
            <div style="display:block;" class="js_d inner">
           	    <div id="base_info_modify" style="display:none">
                    <label class="label clearfix"><span class="text">员工姓名：</span>
                        <input class="text_input" id="input_truename" type="text">
                    </label>
                    <label class="label clearfix"><span class="text">出生日期：</span>
                        <input class="text_input" id="input_birthday" type="text">
                    </label>
                    <label class="label clearfix"><span class="text">服务区属：</span>
                        <input class="text_input" id="input_dist_name" type="text" readonly>
                    </label>
                    <label class="label clearfix"><span class="text">手机号码：</span>
                        <input class="text_input" id="input_phone" type="text" readonly>
                    </label>
                    <label class="label clearfix"><span class="text">身份证：</span>
                        <input class="text_input" id="input_idno" type="text">
                    </label>
                    <label class="label clearfix"><span class="text">所属门店：</span>
                        <select id="select_agency_name"></select>
                        <!-- <input class="text_input" id="input_agency_name" type="text" readonly>-->
                    </label>
                    <label class="label clearfix"><span class="text">业务QQ：</span>
                        <input class="text_input" id="input_qq" type="text">
                    </label>
                    <label class="label clearfix"><span class="text">角色：</span>
                        <select id="select_role"></select>
                    </label>
                    <button class="btn" type="button" onClick="modify('base_add')">立即提交</button>
           	    </div>
             	<div id="base_info">
                    <label class="label clearfix"><span class="text">员工姓名：</span>
                       <strong class="input_info" id="info_truename"></strong>
                    </label>
                    <label class="label clearfix"><span class="text">出生日期：</span>
                        <strong class="input_info" id="info_birthday"></strong>
                    </label>
                    <label class="label clearfix"><span class="text">服务区属：</span>
                        <strong class="input_info" id="info_dist_name"></strong>
                    </label>
                    <label class="label clearfix"><span class="text">手机号码：</span>
                        <strong class="input_info" id="info_phone"></strong>
                    </label>
                    <label class="label clearfix"><span class="text">身份证：</span>
                       <strong class="input_info" id="info_idno"></strong>
                    </label>
                    <label class="label clearfix"><span class="text">所属门店：</span>
                        <strong class="input_info" id="info_agency_name"></strong>
                    </label>
                    <label class="label clearfix"><span class="text">业务QQ：</span>
                        <strong class="input_info" id="info_qq"></strong>
                    </label>
                    <label class="label clearfix"><span class="text">角色：</span>
                        <strong class="input_info" id="info_role"></strong>
                    </label>
                    <label id="bb">
                    <button class="btn btn_none" id="bb_none" type="button">审核中</button>
                    <button class="btn-lv1 btn-mid" id="bb_block" type="button" onclick="base_again()">重新认证</button>
                    </label>
                </div>
            </div>

            <div class="js_d inner">
                <div class="personal_photo_text clearfix">
                    <div class="title clear">标准照片</div>
                    <div class="personal_photo">
                        <div class="inner_p">
                            <form name="fileform_head" id="fileform_head" action="/my_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                <div id="headpic_replace">
                                    <p class="iconfont">&#xe608;</p>
                                    <p class="tex">上传标准照片</p>
                                    <input name="headfile" type="file" class="file_input" id="headfile">
                                </div>
                                <input type="hidden" name="action" value="headfile" />
                                <input type="hidden" name="div_id" value="headpic_replace" />
                            </form>
                        </div>
                    </div>
                    <div class="photo_pit_text">
                        <p class="t">1：标准照</p>
                        <p class="t" id="head_red">2：限JPG、BMP格式</p>
                        <p class="t">3：文件小于10M</p>
                    </div>
                    <div class="title clear">身份照片</div>
                    <div class="personal_photo">
                        <div class="inner_p">
                            <form name="fileform_idno" id="fileform_idno" action="/my_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                <div id="idnopic_replace">
                                    <p class="iconfont">&#xe608;</p>
                                    <p class="tex">上传身份证照片</p>
                                    <input type="file" name="idnofile" class="file_input" id="idnofile">
                                </div>
                                <input type="hidden" name="action" value="idnofile" />
                                <input type="hidden" name="div_id" value="idnopic_replace" />
                            </form>
                        </div>
                    </div>
                    <div class="photo_pit_text">
                        <p class="t">1：标准照</p>
                        <p class="t" id="idno_red">2：限JPG、BMP格式</p>
                        <p class="t">3：文件小于10M</p>
                    </div>
                    <div class="clear"></div>
                    <div id="bi">
                     <button class="btn" type="button" onClick="modify('ident_add')">立即提交</button>
                     <button class="btn btn_none" type="button">审核中</button>
                    </div>
                </div>
            </div>

            <div class="js_d inner">
			    <div class="personal_photo_text clearfix">
			        <div class="title clear">个人名片</div>
                    <div class="personal_photo">
                        <div class="inner_p">
                            <form name="fileform_card" id="fileform_card" action="/my_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                <div id="cardpic_replace">
                                    <p class="iconfont">&#xe608;</p>
                                    <p class="tex">上传个人名片</p>
                                    <input type="file" name="cardfile" class="file_input" id="cardfile">
                                </div>
                                <input type="hidden" name="action" value="cardfile" />
                                <input type="hidden" name="div_id" value="cardpic_replace" />
                            </form>
                   		</div>
                    </div>
                    <div class="photo_pit_text">
                        <p class="t">1：标准照</p>
                        <p class="t" id="card_red">2：限JPG、BMP格式</p>
                        <p class="t">3：文件小于10M</p>
                    </div>
                    <div class="title clear">门店照片</div>
                    <div class="personal_photo">
                        <div class="inner_p">
                            <form name="fileform_agency" id="fileform_agency" action="/my_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                                <div id="agencypic_replace">
                                    <p class="iconfont">&#xe608;</p>
                                    <p class="tex">上传门店照片</p>
                                     <input type="file" name="agencyfile" class="file_input" id="agencyfile">
                                </div>
                                <input type="hidden" name="action" value="agencyfile" />
                                <input type="hidden" name="div_id" value="agencypic_replace" />
                            </form>
                   		</div>
                    </div>
                    <div class="photo_pit_text">
                        <p class="t">1：标准照</p>
                        <p class="t" id="agency_red">2：限JPG、BMP格式</p>
                        <p class="t">3：文件小于10M</p>
                    </div>
                    <div class="clear"></div>
                    <div id="bq">
                    <button class="btn" type="button" onClick="modify('quali_add')">立即提交</button>
                    <button class="btn btn_none" type="button">审核中</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input id="broker_id" type="hidden" value="">
</div>

<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">操作成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/broker/'">确定</button>
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
<div id="js_pop_do_delete"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_delete_tip">确定要删除选定的记录吗？</p>
				<button type="button" id="dialog_btn" class="btn-lv1 btn-left">确定</button>
				<button type="button" class="btn-hui1 JS_Close">取消</button>
			</div>
		</div>
	</div>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/loading.gif" id="mainloading">
<!--遮罩 loading-->

<script type="text/javascript">
//获取验证码
    var InterValObj; //timer变量，控制时间
    var count = 60; //间隔函数，1秒执行
    var curCount;//当前剩余秒数

    function get_code(){
    	curCount = count;
    	var phone = $("#phone").val();
    	if(!phone){
            $("#dialog_do_warnig_tip").html("请输入手机号码");
    		openWin('js_pop_do_warning');
            return false;
        }
    	$.ajax({
        type : 'get',
    		url: '/broker_sms/',
        data : {phone : phone, type : 'register'},
        dataType :'json',
    		cache:false,
    		error:function(){
    			$("#dialog_do_warnig_tip").html("系统错误");
        		openWin('js_pop_do_warning');
    			return false;
    		},
    		success: function(data){
    			if(data.status == 1){
    				$(".get_code").attr("disabled", "true");
    				$(".get_code").removeAttr("onclick");
                    InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
    			}else{
    				$("#dialog_do_warnig_tip").html(data.msg);
    	    		openWin('js_pop_do_warning');
    	            return false;
    			}
    		}
    	});
    }

    //timer处理函数
    function SetRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);//停止计时器
            $(".get_code").removeAttr("disabled");//启用按钮
            $(".get_code").attr("onclick","get_code()");
            $(".get_code").val("重新获取验证码");
        }
        else {
        	$(".get_code").val(curCount + "s");
            curCount--;
        }
    }
//添加经纪人
    function add_broker(){
    	var agency_id = $("#agency_id").val();
    	var phone = $("#phone").val();
    	var password = $("#password").val();
        var code = $("#code").val();
        if(!agency_id){
        	agency_id = 0;
        }
        if(!phone){
            $("#dialog_do_warnig_tip").html("请输入手机号码");
    		openWin('js_pop_do_warning');
            return false;
        }
        if(!password){
            $("#dialog_do_warnig_tip").html("请输入注册密码");
    		openWin('js_pop_do_warning');
            return false;
        }
        if(!code){
            $("#dialog_do_warnig_tip").html("请输入验证码");
    		openWin('js_pop_do_warning');
            return false;
        }
        var data = {agency_id:agency_id,phone:phone,password:password,code:code};
    	$.ajax({
    		type: "POST",
    		url: "/broker/add",
    		dataType:"json",
    		data:data,
    		cache:false,
    		error:function(){
    			$("#dialog_do_warnig_tip").html("系统错误");
        		openWin('js_pop_do_warning');
    			return false;
    		},
    		success:function(data){
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
                		openWin('js_pop_do_success');
            		}else{
            			$("#dialog_do_warnig_tip").html(data.msg);
                		openWin('js_pop_do_warning');
            		}
                }
    		}
    	});

    }
//触发修改事件
    function modify_broker(broker_id,purpose){
        	var data = {broker_id:broker_id,purpose:purpose};
        	$.ajax({
        		type: "POST",
        		url: "/broker/modify/",
        		dataType:"json",
        		data:data,
        		cache:false,
        		error:function(){
        			$("#dialog_do_warnig_tip").html("系统错误");
            		openWin('js_pop_do_warning');
        			return false;
        		},
        		success: function(dataMsg){
        			if(dataMsg['errorCode'] == '401')
                    {
                        login_out();
                        $("#jss_pop_tip").hide();
                    }
                    else if(dataMsg['errorCode'] == '403')
                    {
                        /*permission_none();
                        $("#jss_pop_tip").hide();*/
                    	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                        openWin('js_pop_do_warning');return false;
                    }else{
                    	openWin('js_r_shop');
                    	$("#base_info").css('display','block');
                        $("#base_info_modify").css('display','none');

            		    if(dataMsg.status == 1){
            		        $("#bb").html('<button class="btn-lv1 btn-mid" type="button" onclick="base_again()">重新认证</button>');
            		    }else{
            		    	$("#bb").html('<button class="btn-hui1 btn-mid" type="button">审核中</button>');
            		    }
            		    if(dataMsg.auth_ident_status == 2){
            		        $("#bi").html('');
            		        $("#headpic_replace").html("<img src='"+dataMsg.headshots_photo+"' width='90' height='120'>");
            		        $("#idnopic_replace").html("<img src='"+dataMsg.idno_photo+"' width='90' height='120'>");
            		    }else if(dataMsg.auth_ident_status == 1){
            		        $("#bi").html('<button class="btn-hui1 btn-mid" type="button">审核中</button>');
            		        $("#headpic_replace").html("<img src='"+dataMsg.headshots_photo+"' width='90' height='120'>");
            		        $("#idnopic_replace").html("<img src='"+dataMsg.idno_photo+"' width='90' height='120'>");
                		}else{
                			$("#bi").html('<button class="btn-lv1 btn-mid" type="button" onClick=modify("ident_add")>立即提交</button>');
                		}

            		    if(dataMsg.auth_quali_status == 2){
            		        $("#bq").html('<button class="btn-lv1 btn-mid" id="bq_block" type="button" onClick=modify("quali_add")>立即提交</button>');
            		        $("#cardpic_replace").html("<img src='"+dataMsg.headshots_photo+"' width='90' height='120'><div class='del_amend'><a href='javascript:void(0)'><input name='cardfile' type='file' class='file_input' id='cardfile'>修改</a><a href='javascript:void(0)' onclick='remove_cardfile()'>删除</a></div>");
            		        $("#agencypic_replace").html("<img src='"+dataMsg.idno_photo+"' width='90' height='120'><div class='del_amend'><a href='javascript:void(0)'><input name='agencyfile' type='file' class='file_input' id='agencyfile'>修改</a><a href='javascript:void(0)' onclick='remove_agencyfile()'>删除</a></div>");
            		    }else if(dataMsg.auth_quali_status == 1){
            		        $("#bq").html('<button class="btn-hui1 btn-mid" id="bq_none" type="button">审核中</button>');
            		        $("#cardpic_replace").html("<img src='"+dataMsg.headshots_photo+"' width='90' height='120'><div class='del_amend'><a href='javascript:void(0)'><input name='cardfile' type='file' class='file_input' id='cardfile'>修改</a><a href='javascript:void(0)' onclick='remove_cardfile()'>删除</a></div>");
            		        $("#agencypic_replace").html("<img src='"+dataMsg.idno_photo+"' width='90' height='120'><div class='del_amend'><a href='javascript:void(0)'><input name='agencyfile' type='file' class='file_input' id='agencyfile'>修改</a><a href='javascript:void(0)' onclick='remove_agencyfile()'>删除</a></div>");
                		}else{
                			$("#bq").html('<button class="btn-lv1 btn-mid" id="bq_block" type="button" onClick=modify("quali_add")>立即提交</button>');
                		}

            		    $("#broker_id").val(dataMsg.broker_id);
            			$("#input_truename").val(dataMsg.truename);
            			$("#info_truename").html(dataMsg.truename);
            			$("#input_birthday").val(dataMsg.birthday);
            			$("#info_birthday").html(dataMsg.birthday);
            			$("#input_dist_name").val(dataMsg.dist_name);
            			$("#info_dist_name").html(dataMsg.dist_name);
            			$("#input_phone").val(dataMsg.phone);
            			$("#info_phone").html(dataMsg.phone);
            			$("#input_idno").val(dataMsg.idno);
            			$("#info_idno").html(dataMsg.idno);
            			//$("#input_agency_name").val(dataMsg.agency_name);
            			var agencys = dataMsg.agencys;
            			var option_info = "";
            			var selected = "";
            		    if(agencys){
                		   for(var i=0;i<agencys.length;i++){
                    		   if(agencys[i].id === dataMsg.agency_id){
                    			   selected = "selected='selected'";
                    		   }
                			   option_info += "<option "+selected+" value='"+agencys[i].id+"'>"+agencys[i].name+"</option>";
                			   selected = "";
                		   }
                		   $("#select_agency_name").html(option_info);
            		    }
            			$("#info_agency_name").html(dataMsg.agency_name);
            			$("#input_qq").val(dataMsg.qq);
            			$("#info_qq").html(dataMsg.qq);
            			$("#info_role").html(dataMsg.role_name);

            			var company_roles = dataMsg.company_roles;
            			var option_info1 = "";
            			var selected1 = "";
            		    if(company_roles){
                		   for(var i=0;i<company_roles.length;i++){
                    		   if(company_roles[i].id === dataMsg.role_id){
                    			   selected1 = "selected='selected'";
                    		   }
                			   option_info1 += "<option "+selected1+" value='"+company_roles[i].id+"'>"+company_roles[i].name+"</option>";
                			   selected1 = "";
                		   }
                		   $("#select_role").html(option_info1);
            		    }
                    }
        		}
        	});
    }
//修改提交
     function modify(purpose){
         var broker_id = $("#broker_id").val();
         if(purpose == "base_add"){
             var truename = $("#input_truename").val();
             var birthday = $("#input_birthday").val();
             /*var phone = $("#input_phone").val();*/
             var idno = $("#input_idno").val();
             var qq = $("#input_qq").val();
             var agency_id = $("#select_agency_name").val();
             var role = $("#select_role").val();

             if(!truename){
      		   $("#dialog_do_warnig_tip").html("姓名不能为空");
      		   openWin('js_pop_do_warning');
     		   return false;
        	}

             var data = {broker_id:broker_id,purpose:purpose,truename:truename,birthday:birthday,idno:idno,qq:qq,agency_id:agency_id,role:role};
         }else if(purpose == "ident_add"){
        	 var photo = "";
             if($("#headpic_replace").find("img").length){
         	     photo = $("#headpic_replace img").attr("src");
             }else{
              	 $("#dialog_do_warnig_tip").html("请上传标准照片");
                 openWin('js_pop_do_warning');
           	     return false;
             }
             var photo2 = "";
             if($("#idnopic_replace").find("img").length){
           	     photo2 = $("#idnopic_replace img").attr("src");
             }else{
              	  $("#dialog_do_warnig_tip").html("请上传身份证照片");
                  openWin('js_pop_do_warning');
           	     return false;
       	     }
             var data = {broker_id:broker_id,purpose:purpose,photo:photo,photo2:photo2};
         }else if(purpose == "quali_add"){
        	 var photo = "";
             if($("#cardpic_replace").find("img").length){
                 photo = $("#cardpic_replace img").attr("src");
           	 }else{
              	 $("#dialog_do_warnig_tip").html("请上传个人名片");
                 openWin('js_pop_do_warning');
           	     return false;
             }
             var photo2 = "";
         	 if($("#agencypic_replace").find("img").length){
          	     photo2 = $("#agencypic_replace img").attr("src");
             }else{
              	 $("#dialog_do_warnig_tip").html("请上传门店照片");
                 openWin('js_pop_do_warning');
           	     return false;
             }
         	 var data = {broker_id:broker_id,purpose:purpose,photo:photo,photo2:photo2};
         }

         $.ajax({
      		type: "POST",
      		url: "/broker/modify/",
      		data:data,
      		cache:false,
      		error:function(){
      			$("#dialog_do_warnig_tip").html("系统错误");
        		openWin('js_pop_do_warning');
      			return false;
      		},
      		success: function(data){
      			$("#dialog_do_success_tip").html(data);
        		openWin('js_pop_do_success');
      		}
         });

     }
//删除经济人
    function delete_broker(broker_id){
    	openWin('js_pop_do_delete');
    	$('#dialog_btn').bind('click', function() {
            var data = {broker_id:broker_id};
            $.ajax({
            	type: "POST",
            	url: "/broker/delete",
            	dataType:"json",
            	data:data,
            	cache:false,
            	error:function(){
            		$("#dialog_do_warnig_tip").html("系统错误");
            		openWin('js_pop_do_warning');
            		return false;
            	},
            	success: function(data){
            		if(data['errorCode'] == '401')
                    {
                        login_out();
                        $("#jss_pop_tip").hide();
                    }
                    else if(data['errorCode'] == '403')
                    {
                        /*permission_none();
                        $("#jss_pop_tip").hide();*/
                    	closeWindowWin('js_pop_do_delete');
                    	$("#dialog_do_warnig_tip").html('对不起，您没有访问权限！');
                        openWin('js_pop_do_warning');
                    }else{
                    	if(data.status=="success"){
                			$("#dialog_do_success_tip").html(data.msg);
                    		openWin('js_pop_do_success');
                		}else{
                			$("#dialog_do_warnig_tip").html(data.msg);
                    		openWin('js_pop_do_warning');
                		}
                    }
            	}
            });
    	});
    }
//删除多个经济人
    function delete_all(){
    	var broker_id= [];
        $("input[name=brokerId]").each(function() {
            if ($(this).attr("checked")) {
            	broker_id.push($(this).val());
            }
        });
        if(broker_id.length==0){
        	$("#dialog_do_warnig_tip").html("未勾选，请选择！");
    		openWin('js_pop_do_warning');
        }else{
        	delete_broker(broker_id);
        }
     }
//触发资料认证-重新认证
    function base_again(){
    	$("#base_info").css('display','none');
        $("#base_info_modify").css('display','block');
    }
//上传更换图片
    function changePic(fileurl,div_id,filename){
    	$("#"+div_id).html("<img src='"+fileurl+"' width='90' height='120'><div class='del_amend'><a href='javascript:void(0)'><input name='"+filename+"' type='file' class='file_input' id='"+filename+"'>修改</a><a href='javascript:void(0)' onclick='remove_"+filename+"()'>删除</a></div>");
    }
//删除图片操作
    function remove_headfile()
    {
    	var head_addHtml = "<p class='iconfont'>&#xe608;</p>";
    	head_addHtml += "<p class='tex'>上传标准照片</p>";
    	head_addHtml += "<input type='file' name='headfile' class='file_input' id='headfile'>";
    	$("#headpic_replace").html(head_addHtml);
    }

    function remove_idnofile()
    {
    	var idno_addHtml = "<p class='iconfont'>&#xe608;</p>";
    	idno_addHtml += "<p class='tex'>上传身份证照片</p>";
    	idno_addHtml += "<input type='file' name='idnofile' class='file_input' id='idnofile'>";
    	$("#idnopic_replace").html(idno_addHtml);
    }

    function remove_cardfile()
    {
    	var card_addHtml = "<p class='iconfont'>&#xe608;</p>";
    	card_addHtml += "<p class='tex'>上传个人名片</p>";
    	card_addHtml += "<input type='file' name='cardfile' class='file_input' id='cardfile'>";
    	$("#cardpic_replace").html(card_addHtml);
    }

    function remove_agencyfile()
    {
    	var agency_addHtml = "<p class='iconfont'>&#xe608;</p>";
    	agency_addHtml += "<p class='tex'>上传门店照片</p>";
    	agency_addHtml += "<input type='file' name='agencyfile' class='file_input' id='agencyfile'>";
    	$("#agencypic_replace").html(agency_addHtml);
    }
 </script>
