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
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/ajd.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/dropload/dropload.css" />
</head>
<body>
    <div class="wrapper custorm_wrapper">
	<!--
        <section class="custorm_nav clearfix">
            <a href="/wap/mortgage/customer/"><span class="nav_ajd">按揭贷</span></a>
            <a href="/wap/pledge/customer/"><span class="nav_dyd nav_chooose">抵押贷</span></a>
        </section>
	-->
        <div class="ajd_dyd">
            <section class="my_customer my_customer_ajd">
                <ul class="listview">
                    <?php
                        foreach($list as $value){
                            ?>
                    <li>
						<a href="/wap/pledge/detail/<?=$value['id']?>/">
                            <div class="apply_msg_top apply_msg_dyd clearfix">
                                <h2><?=$value['borrower']?></h2>
                                <p><?=$value['phone']?></p>
                                <span class="show_msg pt_sh"><em class="bar"></em><i><?=$value['status_str']?></i></span>
                            </div>
                            <div class="apply_msg_bot">
                                <span class="apply_money apply_money_all">
                                    <strong class="cc">&nbsp;</strong>总价&nbsp;:&nbsp;
                                    <em><?=round($value['price'])?></em>万
                                </span>
                                <span class="apply_money apply_money_all">
                                    <strong class="cc">&nbsp;</strong>意向额度&nbsp;:&nbsp;
                                    <em><?=round($value['intentional_money'])?></em>万
                                </span>
                            </div>
                        </a>
                    </li>

                            <?php
                        }
                    ?>
                </ul>
            </section>
        </div>

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


    $('.custorm_nav span').on('touchend',function(){

        $(this).addClass('nav_chooose').siblings().removeClass('nav_chooose');
        $('.my_customer').hide().eq($('.custorm_nav span').index(this)).show();
    });

</script>
<script type="text/javascript" charset="utf-8" src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/dropload/dropload.min.js"></script>
<script>
var page = 1;
var pagesize = 15;
$('.ajd_dyd').dropload({
    scrollArea : window,
    loadDownFn : function(me){
        $.ajax({
            type: 'GET',
            url: '/wap/pledge/customer/?page='+page+'&pagesize='+pagesize,
            dataType: 'html',
            success: function(data){
                var listview = $(data).find('.listview');

                if(listview.find('li').length > 0){
                    page++;
                    $('.listview').append(listview.html());
                }else{
                    me.lock();
                    me.noData();
                }

                me.resetload();
            },
            error: function(xhr, type){
                me.resetload();
            }
        });
    }
});
</script>
</html>
