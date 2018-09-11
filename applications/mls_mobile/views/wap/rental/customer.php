<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>我的客户</title>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zjd.css" type="text/css"/>
</head>
<body>
    <div class="wrapper custorm_wrapper">
        <section class="custorm_search clearfix">

            <form action="" method="get">
            <span class="search_bar cc"></span>
            <input type="text" name="keywords" placeholder="请输入姓名/手机号" value="<?=$keywords?>" />
            </form>
        </section>
        <section class="my_customer">
            <ul>
                <?php
                    foreach($list as $value){
                        ?>
                        <li>
                            <div class="apply_msg_top">
                                <h2><?=$value['tenant_name']?></h2>
                                <p><?=$value['tenant_phone']?></p>
                                <span class="show_msg wait_up<?php if('4' == $value['step'] && '4' == $value['status']){?> wait_up<?php }?>"><?=$value['status_str']?></span>
                            </div>
                            <div class="apply_msg_bot clearfix">
                                <span class="apply_money"><strong class="cc">&nbsp;</strong>借款金额&nbsp;:&nbsp;&nbsp;
                                    <em><?=round($value['tenant_price'])?></em>元</span>

                                    <?php
                                    if('4' == $value['step'] && '1' == $value['status']){
                                        ?>
                                        <button class="bt_msg">扫二维码下载浦发APP</button>
                                        <?php
                                    }else{
                                        ?>
                                        <span class="show_msg"><?=str_replace(array('[false]','[true]'),'',$value['reason']);?></span>
                                        <?php
                                    }
                                    ?>
                            </div>
                        </li>
                        <?php
                    }
                ?>
            </ul>
        </section>
    </div>
</body>
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

    $('.bt_msg').on('touchend',function(){

        $('.pf_app').show();
    });
    $('.pf_app').on('touchend',function(){

        $(this).hide();
    });

</script>
</html>
