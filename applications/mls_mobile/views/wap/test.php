<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <title>金融</title>
        <link href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/frozen.css" rel="stylesheet" type="text/css">
        <link href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/mainindex2.css" rel="stylesheet" type="text/css">
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
		<header class="finance_mian"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/main_banner_new.jpg" alt="" /></header>
        <div class="finance_mian_con">
            <ul>
                <?php
                if($mortgage){
                ?>
                <li>
                    <span id="clumn">
                        <a href="/wap/mortgage/apply/" class="finance_mian_con_img"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/icon1.png" alt="按揭贷款" /></a>
                        <p id="clumn2" class="zws_front75 zws_line125 zws_text_808"><a href="/wap/mortgage/apply/"><b class="finance_mian_con_b zws_front100 zws_line125">按揭贷款</b>当地最低利率 , 最长30年 , 最高评估价7成，过户后3天放款</a></p>
						<p id="clumn4" class="zws_relative"><b class="finance_apply zws_bold_normal zws_front75 zws_right"><a href="/wap/mortgage/apply/">申请</a></b></p>
                    </span>

                </li>
                <?php
                }
                if($pledge){
                ?>
                <li>
                    <span id="clumn">
                        <a href="/wap/pledge/ad/" class="finance_mian_con_img"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/icon2.png" alt="抵押贷"  /></a>
                        <p id="clumn2" class="zws_front75 zws_line125 zws_text_808"><a href="/wap/pledge/ad/"><b class="finance_mian_con_b zws_front100 zws_line125">抵押贷</b>月息最低仅1.21分,最高可贷500万3-5天可放款,大品牌大保障</a></p>
    					<p id="clumn4" class="zws_relative"><b class="finance_apply zws_bold_normal zws_front75 zws_right"><a href="/wap/pledge/ad/">申请</a></b></p>
                    </span>
                </li>
                <?php
                }
                if($rental){
                ?>
                <li>
                    <span id="clumn">
                        <a href="/wap/rental/ad/" class="finance_mian_con_img"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/icon3.png" alt="租金贷"  /></a>
                        <p id="clumn2" class="zws_front75 zws_line125 zws_text_808"><a href="/wap/rental/ad/"><b class="finance_mian_con_b zws_front100 zws_line125">租金贷</b>改变传统交租方式(季付/半年付/年付)享受按月交租金轻松便捷</a></p>
    					<p id="clumn4" class="zws_relative"><b class="finance_apply zws_bold_normal zws_front75 zws_right"><a href="/wap/rental/ad/">申请</a></b></p>
                    </span>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </body>
</html>
