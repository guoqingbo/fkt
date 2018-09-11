<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui, maximum-scale=1, user-scalable=no">
<meta name="alexaVerifyID" content="LkzCRJ7rPEUwt6fVey2vhxiw1vQ">
<title>经纪人注册页面</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/web_reseat.css,css/v1.0/register_login_password2.css" rel="stylesheet" type="text/css">
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js,jquery.validate.min.js"></script>
<script>
$(function() {
	$('.ewm1').hover(function(){
		$('.ewm-img').toggle();
	});

    var getValidcodeKey = 0;
    //获取验证码
    $('#getValidcode').bind('click', function() {
        if(getValidcodeKey > 0)
        {
            return false;
        }

        var rgExp = /^1\d{10}$/;
        if(!rgExp.test($('#phone').val()))
        {
            $('#phone').addClass('error');
        }
        else
        {
            getValidcodeKey = 60;
            $('#getValidcode').addClass('get_code_none');
            $('#getValidcode').html(getValidcodeKey + '秒后重新获取');
            var oTime = setInterval(function(){
                getValidcodeKey--;
                if(getValidcodeKey > 0)
                {
                    $('#getValidcode').html(getValidcodeKey + '秒后重新获取');
                }
                else
                {
                    $('#getValidcode').html('获取手机验证码');
                    $('#getValidcode').removeClass('get_code_none');
                }
            },1000);
        }

        $.ajax({
            type : 'get',
            url  : '/broker_sms/',
            data : {phone : $('#phone').val(), type : 'register', city_id : $('#city_id').val()},
            dataType :'json',
            success : function(data){
                if (data.status !== 1) {
                    $('#phone').addClass('error');
                    $('#phone_error').html('<label class="error" for="phone">'+data.msg+'</label>');
                }
            }
        });
    });


    $('#province').bind('change', function() {
        $.ajax({
            type: 'get',
            url : '/city/get_city/',
            data: { province : $('#province').val()},
            dataType:'json',
            success: function(data){
                $('#city_id').html('');
                $('#city_id').append('<option value="">请选择城市</option>');
                if (data) {
                    for(var i in data) {
                        $('#city_id').append('<option value="' + data[i].id
                                +'">' + data[i].cityname + '</option>');
                    }
                }
            }
        });
    });
});
function InvokeLoginFunc()
{
    external.go2Login();
}
</script>
</head>
<body>
    <div class="header">
        <div class="header-inner">
            <a class="logo fl" href="#"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/codi/logo/codi.png" title="<?=$title?>"></a>
            <a class="try-btn fr" href="/">返回登陆页面</a>
        </div>
    </div>
    <div id="register_ok_div" class="find_password" style="display:none; height:425px; padding-top:200px;">
        <div class="s_text">帐号已注册成功！请等待客服人员开通！</div>
    </div>
    <div id="register_form_div" style="display: block;" class="find_password">
        <form action="/register/signup/" method="post" id="js_register_form">
			<h1>注册新帐号</h1>
            <dl class="list">
				<dd class="list_item clearfix">
					<label class="label">所在城市</label>
					<?php if (isset($province) && $province) { ?>
					<select name="province" id="province" class="select">
					<option value="" style="color:#999;">请选择省份</option>
					<?php foreach($province as $v) { ?>
					<option value="<?=$v?>"><?=$v?></option>
					<?php } ?>
					</select>
					<?php } ?>
					<select name="city_id" id="city_id" class="select">
					<option value="" style="color:#999;">请选择城市</option>
					</select>
					<div class="error_add"></div>
				</dd>
				<dd class="list_item clearfix">
					<label class="label">中介公司</label>
					<input name="corpName" id="corpName" type="text" class="input_t" />
					<label class="placeholder_for" for="corpName">请填写中介公司</label>
					<div class="error_add"></div>
				</dd>
				<dd class="list_item clearfix">
					<label class="label">所属门店</label>
					<input name="name2" id="name2" type="text" class="input_t" />
					<label class="placeholder_for" for="name2">请填写所属门店</label>
					<div class="error_add"></div>
				</dd>
				<dd class="list_item clearfix">
					<label class="label">姓名</label>
					<input name="userName" id="userName" type="text" class="input_t" />
					<label class="placeholder_for" for="userName">请填写姓名</label>
					<div class="error_add"></div>
				</dd>
				<dd class="list_item clearfix">
					<label class="label">手机号码</label>
					<input id="phone" maxlength="11" name="phone" id="phone" type="text" class="input_t" />
					<label class="placeholder_for" for="phone">请输入11位手机号码</label>
					<div id="phone_error" class="error_add"></div>
				</dd>
				<dd class="list_item clearfix">
					<label class="label">验证码</label>
					<input type="text" name="validcode" id="validcode" class="input_t w128" />
					<label class="placeholder_for" for="validcode">请填写验证码</label>
					<button type="button" id="getValidcode" class="get_code">获取短信验证码</button>
					<div id="validcode_error" class="error_add"></div>
				</dd>
				<dd class="list_item clearfix">
					<label class="label">设置密码</label>
					<input type="password" id="password"  name="password" class="input_t">
					<label class="placeholder_for" for="password">请输入密码</label>
					<div class="error_add"></div>
				</dd>
				<dd class="list_item clearfix">
					<label class="label">&nbsp;</label>
					<button type="submit" class="btn_submit">免费注册</button>
				</dd>
			</dl>
       </form>
   </div>
    <div class="footer" style="margin-top:120px;">
        <div class="footer-inner">
            <div class="clearfix">
                <ul class="fl">
                    <li><h2>服务与帮助</h2></li>
                    <li><a href="/register">注册帐号</a></li>
                    <li><a href="/login/findpw/">找回密码</a></li>
                </ul>
                <ul class="fr">
                    <li><h2 style="position:relative;">关注我们</h2></li>
                    <li class="tel">电话：<?=$tel400?></li>
                    <li class="adr">地址：浙江省杭州市下城区上塘路15号武林时代20楼</li>
                </ul>
            </div>
            <p class="company-name">科地地产 <a href="http://www.miibeian.gov.cn/" target="view_window" >浙ICP备</a></p>
        </div>
    </div>
   <script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=backspace.js,jquery.validate.min.js,register_login_password2.js"></script>
   <script src="<?php echo MLS_SOURCE_URL;?>/min/?b=common/js&f=jquery.form.js"></script>
</body>
</html>
