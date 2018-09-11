<!DOCTYPE html>
<html>
<head>
<title><?=$title?></title>
<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/js/html5.js"></script>
<![endif]-->

<body style="text-align:center;">
	<div class="dialog radius5" style="text-align:left;display:block;margin:150px auto 0;width: 500px;">
		<div class="hd">
			<h3 class="h3">页面跳转中...</h3>
		</div>
		<div class="textMod">
			<div class="text" style="font-size: 16px; margin:20px 0 0 60px;">
                <img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/okBg.png"
                     alt=""> <?= $msg === 'login_index' ? '登录。。。' : $msg; ?>
      </div>
			<div style="margin:20px 0 20px 60px;">如页面无法自动跳转，可点击<a href="<?=$url?>" style="color: #17BAB0;">手动跳转。</a></div>
		</div>
	</div>
    <?php if ($msg === 'login_index') { ?>
        <script>setTimeout(function () {
                window.parent.location.href = '<?=$url?>';
            }, <?=$time?>);</script>
    <?php }else{ ?>
        <script>setTimeout(function () {
                window.location.href = '<?=$url?>';
            }, <?=$time?>);</script>
    <?php }; ?>
</body>
</html>
<?php exit;?>
