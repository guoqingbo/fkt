<body >
<!--导航栏-->
<div class="tab_box" id="js_tab_box">
<?php
    echo $user_menu;
?>
</div>
<!--主要内容-->
<div class="table_all tableallborder" style="margin-top:10px;">
    <div id="js_inner" class="inner">
		<iframe src="<?php echo MLS_SOURCE_URL;?>/mls/draw/draw.html" frameBorder="0" scrolling="no" width="100%" height="100%"></iframe>
	</div>
</div>

</body>
