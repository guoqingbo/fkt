<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>登录</title>
  <link href="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/css/v1.0/login.css" rel="stylesheet" type="text/css">
  <script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
</head>
<body class="page-bg">
<div class="page-wrap">
  <div class="page-logo">
    <img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/codi/logo/codi-white.png" alt="">
  </div>
  <div class="page-login">
    <div class="login-form">
      <form action="/login/signin/" method="post">
        <div class="login-title">
          做更懂你的房产ERP！
        </div>
        <div class="login-inp-item">
          <span>手机号码：</span>
          <input type="text" name="phone" id="textfield"/>
        </div>
        <div class="login-inp-item">
          <span>密　　码：</span>
          <input type="password" name="password" id="textfield2"/>
        </div>
        <div class="fond-password-item">
          <a href="/login/findpw/">忘记密码？</a>
            <a href="/register">注册账号</a>
        </div>
        <div class="login-btn-item">
          <span></span>
          <input type="submit" name="button" id="button" value="登录系统"/>
        </div>
        <input type="hidden" name="action" value="signin">
      </form>
    </div>
  </div>
</div>

<script>

</script>
</body>
</html>
