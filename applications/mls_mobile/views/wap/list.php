<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no" />
        <meta name="format-detection" content="telephone=no" />
        <title>我的金融产品列表页</title>
        <link href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/frozen.css" rel="stylesheet" type="text/css">
        <link href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/mainindex.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            !(function (doc, win) {
                var docEle = doc.documentElement,
                    evt = "onorientationchange" in window ? "orientationchange" : "resize",
                    fn = function () {
                        var width = docEle.clientWidth;
                        width && (docEle.style.fontSize = 20 * (width / 375) + "px");
                    };

                win.addEventListener(evt, fn, false);
                doc.addEventListener("DOMContentLoaded", fn, false);

            }(document, window));
        </script>

    </head>

    <body ontouchstart>
        <section class="finance_list">
			<?php
			if($mortgage){
			?>
            <dl class="touched" href="/wap/mortgage/customer/">
                <dd>按揭贷款客户列表</dd>
                <dt><i class="ui-icon-arrow"></i></dt>
            </dl>
			<?php
			}
			if($pledge){
			?>
            <dl class="touched" href="/wap/pledge/customer/">
                <dd>抵押贷款客户列表</dd>
                <dt><i class="ui-icon-arrow"></i></dt>
            </dl>
			<?php
			}
			if($rental){
			?>
            <dl class="touched" href="/wap/rental/customer/">
                <dd>租金贷款客户列表</dd>
                <dt><i class="ui-icon-arrow"></i></dt>
            </dl>
			<?php
			}
			?>
        </section>
    </body>
<script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
<script>
$('.touched').live('touchend',function(){
    var href = $(this).attr('href');
    location.href = href;
});
</script>
</html>
