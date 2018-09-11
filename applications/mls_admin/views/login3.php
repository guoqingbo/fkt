<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk">
<title><?=$title?></title>
<link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/global.css" rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/mainPage.css" rel="stylesheet" type="text/css">
<script src="<?php echo MLS_SOURCE_URL;?>/mls_admin/js/jquery-1.8.3.min.js"></script>
<script language="javascript">
	$(function(){
		$('.login-input-user').focus(function(){
			$('.login-input-user').val('');
		});
		$('.login-input-pwd').focus(function(){
			$('.login-input-pwd').css("background","none");
		});

	})
	function checkchrm(){
		if($('#user').val()==''||$('#user').val()=='用户名...'){
			alert('用户名不可为空，请输入用户名');
			$('#user').focus();
			return false;
		}
		if($('#password').val()==''){
			alert('密码不可为空，请输入密码');
			$('#password').focus();
			return false;
		}
	}
</script>
<!--[if IE 6]>
<script src="templates/js/DD_belatedPNG_0.0.8a-min.js"></script>
<script>
DD_belatedPNG.fix('.login-head .logo,.login-head .login-title2,.login-con,.login-con .login-btn');
</script>
<![endif]-->
</head>

<body style="background-color:#275680;">
	<div class="login">
        <div class="login-head clearfix" style="width:610px;">
            <div class="logo">
                科地地产运营管理系统
            </div>
        </div>
        <div class="login-con">
		<form onsubmit="return checkchrm();" id="login" method="post" action=""><input name="submit_flag" value="login" type="hidden">
                <input class="login-input-user" name="username" id="user" type="text" value="用户名...">
                <input id="password" name="password" class="login-input-pwd" value="" type="password">
                <input value="" class="login-btn" type="submit"><br>
                &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                <?php if(isset($mess_error)) echo $mess_error;?>
		</form>
        </div>
        <div class="login-ft">科地地产</div>
    </div>


</body></html>
