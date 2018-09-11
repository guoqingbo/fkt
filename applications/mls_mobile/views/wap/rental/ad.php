<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
        <meta content="yes" name="apple-mobile-web-app-capable" />
        <meta content="black" name="apple-mobile-web-app-status-bar-style" />
        <meta content="telephone=no" name="format-detection" />
        <meta content="email=no" name="format-detection" />
        <title>租金贷</title>
        <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zjd.css" type="text/css"/>
    </head>
    <body>
        <div class="wrapper index_zj">
            <img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/zjd/bj.jpg" alt=""/>
            <span class="zj_p1"></span>
            <span class="zj_p2"></span>
            <span class="zj_p3"></span>
            <span class="zj_p4"></span>
            <span class="zj_p5"></span>
            <span class="zj_p6">咨询热线: <?=$tel400?></span>
            <a href="/wap/rental/apply/" class="goto_zj" group_id="<?=$group_id;?>"></a>
        </div>

        <script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
        <script>
            ;(function (doc, win) {
                var docEl = doc.documentElement,
                        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                        recalc = function () {
                            var clientWidth = docEl.clientWidth;
                            if (!clientWidth) return;
                            docEl.style.fontSize = 20 * (clientWidth / 375) + 'px';
                        };

                if (!doc.addEventListener) return;
                win.addEventListener(resizeEvt, recalc, false);
                doc.addEventListener('DOMContentLoaded', recalc, false);
            })(document, window);

			$(document).ready(function(){
				$('.goto_zj').on('click',function(){
					var group_id = $(this).attr('group_id');
					if('2' != group_id){
						alert('未认证帐号无法申请 ');
						return false;
					}
				});
			});
        </script>
    </body>
</html>
