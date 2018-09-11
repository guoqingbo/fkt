<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>进度详情</title>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/ajd.css" type="text/css"/>
</head>
<body>
<div class="wrapper  fz_progress">
    <section class="progress_head clearfix">
        <span class="icon_head cc"></span>
        <strong><?=$block_name?></strong>
    </section>
    <section class="progress_line">
		<?php
		if(is_full_array($progress['list'])){
			foreach($progress['list'] as $key=>$value){
		?>
		<div class="pro_li <?=$value['is_now']?'':'not_hamdle'?>">
            <div class="line_msg">
                <span class="dot"></span>
                <a class="<?=$value['is_now']?'current':''?>"><i></i></a>
                <strong class="line <?=($key == 0)?'line_first':''?>"></strong>
                <h2 class="clearfix">
					<?=$value['step_str']?>
					<span><?=$value['steptime']?date('Y-m-d H:i:s',$value['steptime']):''?></span>
				</h2>
				<?php
				if($value['is_now']){
				?>
                <p><?=$value['status_str']?></p>
				<?php
				}
				?>
            </div>
        </div>
		<?php
			}
		}
		?>
    </section>
</div>

<script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
<script>
    ;(function (doc, win) {
        var docEl = doc.documentElement,
                resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                recalc = function () {
                    var clientWidth = docEl.clientWidth;
                    if (!clientWidth) return;
                    docEl.style.fontSize = 20 * (clientWidth / 320) + 'px';
                };

        if (!doc.addEventListener) return;
        win.addEventListener(resizeEvt, recalc, false);
        doc.addEventListener('DOMContentLoaded', recalc, false);
    })(document, window);


</script>
</body>
</html>
