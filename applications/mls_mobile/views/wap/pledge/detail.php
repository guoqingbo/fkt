<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>申请抵押贷</title>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/ajd.css" type="text/css"/>
</head>
<body>
<div class="wrapper  custorm_detail">

    <section class="custorm_head">
        <div class="custorm_information clearfix">
            <span><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/zjd/<?php if($info['sex']=='2'){?>pic_woman.png<?php }else{ ?>head.png<?php } ?>" alt=""/></span>
            <strong><?=$info['borrower']?></strong>
            <em><?=$info['sex']=='2'?'女士':'先生'?></em>
        </div>
        <div class="sustorm_phone">
            <span>手机</span>
            <strong><?=$info['phone']?></strong>
            <a class="cc" href="tel:<?=$info['phone']?>"></a>
        </div>
    </section>
    <section class="dk_progress">
        <a class="dk_tits" href="/wap/pledge/progress?id=<?=$info['id']?>&block_name=<?=$info['block_name']?>">贷款进度<span class="cc">&nbsp;</span></a>
        <div class="dk_fz">
            <h3><?=$info['block_name']?></h3>
            <div class="progress_bar"><em class="bar_show" style="width:<?=$info['score']?>%"></em><span><?=$info['score']?>%</span></div>
            <div class="progress_date clearfix">
                <div class="pro_msg">
                    <p><?=$info['status_str']?></p>
                    <span></span>
                </div>
                <span class="pro_date"><?=date('Y.m.d H：i',$info['create_dateline'])?></span>
            </div>
        </div>
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
