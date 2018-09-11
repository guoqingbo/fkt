<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>专题——按揭服务</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/css/v1.0/base.css" rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&amp;f=css/v1.0/home.css" rel="stylesheet" type="text/css">
</head>
<body>
	<style>
	.subject_01{overflow-y:scroll; overflow-x:hidden; position:absolute; left:0; top:0; width:100%; background:#EB7714;}
	.subject_01 .inner{position:relative; width:1004px; margin:0 auto;}
	.s1{display:block; margin:0 auto;}
	.a{position:absolute; display:block; width:110px; height:40px; background:#fff; filter:alpha(opacity=0); opacity:0;}
	.a1{ top:291px; right:238px; width:160px; height:60px;}
	.a2{ top:1150px; right:275px;}
	.a3{ top:1410px; right:275px;}
	.a4{ top:1799px; right:351px;}
	.a5{ top:1919px; right:432px;}
	.a6{ width:169px; bottom:20px; right:392px;}
	</style>
	<div class="subject_01">
		<div class="inner">
			<a class="a a1" href="#a5"></a>
			<a class="a a2" href="<?php echo MLS_SOURCE_URL;?>/word/按揭服务确认单.doc"></a>
			<a class="a a3" href="<?php echo MLS_SOURCE_URL;?>/word/按揭服务确认单.doc"></a>
			<a class="a a4" href="<?php echo MLS_SOURCE_URL;?>/word/附件2：个人单身声明（售房人版）.docx"></a>
			<a class="a a5" href="<?php echo MLS_SOURCE_URL;?>/word/职业及收入证明.doc"></a>
			<a class="a a6" href="/workbench/index/"></a>
			<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/a1.jpg" >
			<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/a2-4.jpg" >
			<img id="a5" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/a3.jpg">
			<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/subject/a6-4.jpg" >
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
