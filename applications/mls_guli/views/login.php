<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui, maximum-scale=1, user-scalable=no">
<meta name="alexaVerifyID" content="LkzCRJ7rPEUwt6fVey2vhxiw1vQ">
<title>登录</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls_guli&f=third/iconfont/iconfont.css,css/v1.0/web_reseat.css,css/v1.0/login.css,css/v1.0/house_manage.css" rel="stylesheet" type="text/css">
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls_guli/js/v1.0&f=jquery-1.8.3.min.js,jquery.validate.min.js,openWin.js"></script>
<script>
  if(window!=top){
    top.location.href = "/login/index";
  }
</script>
</head>
<body class="login_body">
<div class="header">
  <div class="header-inner">
    <a class="logo fl" href="/"><img src="<?php echo MLS_SOURCE_URL;?>/mls_guli/images/v1.0/codi/logo/codi.png" title="<?=$title?>"></a>
  </div>
</div>
<div id="findpw_form_div" class="login">
  <form action="/login/signin/" id="js_login" name="js_login" method="post">
    <h1>签约平台-登录</h1>
    <dl class="list">
      <dd class="list_item clearfix">
        <label class="label">手机号码</label>
        <input id="phone" maxlength="11" name="phone" id="phone" type="text" class="input_t" />
        <label class="placeholder_for" for="phone">请输入11位手机号码</label>
        <div id="phone_error" class="error_add"></div>
      </dd>
      <dd class="list_item clearfix">
        <label class="label">密码</label>
        <input type="password" id="password"  name="password" class="input_t">
        <label class="placeholder_for" for="password">请输入密码</label>
        <div class="error_add"></div>
      </dd>
      <dd class="list_item list_item_find_password clearfix">
        <label class="label">&nbsp;</label>
        <div class="find_password"><a href="/login/findpw/">忘记密码？</a></div>

      </dd>
      <dd class="list_item clearfix">
        <label class="label">&nbsp;</label>
        <input type="hidden" name="action" value="findpw">
        <button type="submit" class="btn_submit">登录系统</button>
        <div id="error_submit" class="error_add"></div>
      </dd>
    </dl>
  </form>
</div>
<!--<div class="footer" style="margin-top:120px;">
  <div class="footer-inner">
    <div class="clearfix">
      <ul class="fl">
        <li><h2>服务与帮助</h2></li>
        <li><a href="/login/findpw/">找回密码</a></li>
      </ul>
      <ul class="fr">
        <li><h2 style="position:relative;">关注我们</h2></li>
        <li class="tel">电话：<?/*=$tel400*/?></li>
        <li class="adr">地址：浙江省杭州市下城区上塘路15号武林时代20楼</li>
      </ul>
    </div>
    <p class="company-name">科地地产 <a href="http://www.miibeian.gov.cn/" target="view_window" >浙ICP备</a></p>
  </div>
</div>-->
<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
  <div class="hd">
    <div class="title">提示</div>
    <div class="close_pop">
      <a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
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
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls_guli/js/v1.0&f=backspace.js,jquery.validate.min.js,login.js"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=common/js&f=jquery.form.js"></script>
</body>
</html>
