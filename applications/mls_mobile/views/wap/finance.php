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
		<header class="finance_mian"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/main_banner_new2.jpg" alt="" /></header>
        <div class="finance_mian_con">
            <ul>
                <?php
                if($mortgage){
                ?>
                <li>
                    <span id="clumn">
                        <a href="/wap/mortgage/apply/" class="finance_mian_con_img"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/icon1.png" alt="按揭贷款" /></a>
						<div class="zws_repairt_index_new">
                        <p id="clumn2" class="zws_front75 zws_line125 zws_text_808">
							<a href="/wap/mortgage/apply/"><b class="finance_mian_con_b zws_front100 zws_line125">按揭贷款</b>当地最低利率 , 最长30年 , 最高评估价7成，过户后3天放款</a>
						</p>
						<p id="clumn4" class="zws_relative"><b class="finance_apply zws_bold_normal zws_front75 zws_right zws_new_apply"><a href="/wap/mortgage/apply/">立即申请&nbsp;&nbsp;&gt;</a></b></p>
						</div>
                    </span>

                </li>
                <?php
                }
                if($pledge){
                ?>
                <li>
                    <span id="clumn">
                        <a href="/wap/pledge/ad/" class="finance_mian_con_img"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/icon2.png" alt="抵押贷"  /></a>
						<div class="zws_repairt_index_new">
							<p id="clumn2" class="zws_front75 zws_line125 zws_text_808"><a href="/wap/pledge/ad/"><b class="finance_mian_con_b zws_front100 zws_line125">抵押贷</b>月息最低仅1.21分，最高可贷500万最快3天放款，大品牌大保障</a></p>
							<p id="clumn4" class="zws_relative"><b class="finance_apply zws_bold_normal zws_front75 zws_right zws_new_apply"><a href="/wap/pledge/ad/">立即申请&nbsp;&nbsp;&gt;</a></b></p>
						<div>
                    </span>
                </li>
                <?php
                }
                if($rental){
                ?>
                <li>
                    <span id="clumn">
                        <a href="/wap/rental/ad/" class="finance_mian_con_img"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/icon3.png" alt="租金贷"  /></a>
						<div class="zws_repairt_index_new">
							<p id="clumn2" class="zws_front75 zws_line125 zws_text_808"><a href="/wap/rental/ad/"><b class="finance_mian_con_b zws_front100 zws_line125">租金贷</b>纯信用，无抵押，免担保，1-5分钟实时审批急速放款，利率低至4.5%</a></p>
							<p id="clumn4" class="zws_relative"><b class="finance_apply zws_bold_normal zws_front75 zws_right zws_new_apply"><a href="/wap/rental/ad/">立即申请&nbsp;&nbsp;&gt;</a></b></p>
						</div>
                    </span>
                </li>
                <?php
                }
                ?>
 				<li>
                <span id="clumn">
                    <a href="javascript:void();" class="finance_mian_con_img"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/card_icon.png" alt="信用贷" /></a>
					<div class="zws_repairt_index_new">
						<p id="clumn2" class="zws_front75 zws_line125 zws_text_808"><b class="finance_mian_con_b zws_front100 zws_line125 gray_zws">信用贷</b>产品即将上线，敬请关注</p>
						<p id="clumn4" class="zws_relative"><b class="finance_apply zws_bold_normal zws_front75 zws_right gray_zws_sq zws_new_apply"><a href="javascript:void();">即将上线&nbsp;&nbsp;&gt;</a></b></p>
					</div>
                </span>
                </li>
            </ul>
        </div>
        <footer class="footer_finance">
            <b>客服电话 xxx-xxx-xxx</b>
		</footer>
    </body>
</html>
