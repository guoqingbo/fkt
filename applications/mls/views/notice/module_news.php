<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?=$news_msg['title']?></title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
</head>
<body>
<div class="zws_notice_detial">
    <div class="zws_notice_detial_border">
        <div class="zws_notice_detial_border_con">
            <dl class="zws_notice_detial_title">
                <dd><?=$news_msg['title']?></dd>
                <dt>发布时间：<?=date('Y-m-d', $news_msg['createtime'])?> 发布人：平台</dt>
            </dl>
            <!--内容区域-->
            <div class="zws_notice_detial_area_con">
                <?=$news_msg['content']?>
            </div>
            <!--敬语部分-->
            <dl class="zws_notice_detial_end">
                <dd></dd>
                <dt></dt>
            </dl>
        </div>
    </div>
</div>
 <script type="text/javascript">
     $(function () {

         $(".zws_notice_detial").css("height", $(window).height());
     })
     $(window).resize(function () {
         $(".zws_notice_detial").css("height", $(window).height());
     })
 </script>
</body>
</html>
