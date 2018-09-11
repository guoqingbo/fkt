<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>专题——女神驾到</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/home.css" rel="stylesheet" type="text/css">
</head>
<body>
	<style>
	.subject_01{overflow-y:scroll; overflow-x:hidden; position:absolute; left:0; top:0; width:100%; background:#FFDDE8;}
	.subject-inner{padding-top:510px; background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/s1.jpg) center top no-repeat;}
	.subject_01 .inner{width:1280px; margin:0 auto;}
	</style>
	<div class="subject_01">
		<div class="subject-inner">
			<div class="inner">
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/s2.jpg" >
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/s3.jpg" >
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/s4.jpg" >
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/s5.jpg" >
			</div>
		</div>
	</div>
	<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js,openWin.js"></script>
	<script>
		$(function () {
			function re_height(){
				$(".subject_01").css({
					"height":$(window).height()
				});
			};
			re_height();
			$(window).resize(function(e) {
				re_height();
			});
		});
	</script>
</body>
</html>
