
<style type="text/css">
.zws_luck{width:100%;height:496px;float:left;display:inline;overflow:hidden;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/luck_bj_03.jpg) no-repeat center #ffd86e;}
.zws_luck_main{width:1253px;margin:0 auto;}
.zws_luck_clear{clear:both;}
.zws_luck_left{width:376px;height:292px;float:left;display:inline;float:left;display:inline;padding:167px 0 0 28px;}
.zws_luck_left_indro{width:100%;height:auto;float:left;display:inline;padding-top:21px;}
.zws_luck_left_indro b{font-size:12px;font-family:"微软雅黑";display:block;float:left;font-weight:bold;line-height:22px;}
.zws_luck_left_indro p{width:100%;float:left;display:inline;font-size:12px;font-family:"微软雅黑";line-height:22px;}

.zws_luck_left_grade{width:327px;height:59px;float:left;display:inline;padding:25px 0 0 25px;}
.zws_luck_left_grade dd{float:left;display:inline;font-size:16px;font-family:"微软雅黑";color:#FFF;line-height:49px;}
.zws_luck_left_grade dd b{font-size:24px;color:#fff001;line-height:59px;float:left;}
.zws_luck_left_grade dt{float:right;display:inline;font-size:16px;font-family:"微软雅黑";color:#FFF;line-height:49px;}
.zws_luck_left_grade strong{line-height:58px;font-weight:normal;float:left;}
.zws_luck_left_grade dt b{font-size:24px;color:#fff001;line-height:59px;float:left;}

.zws_luck_center{width:510px;height:510px;float:left;display:inline;float:left;display:inline;overflow:hidden;}
.zws_luck_right{width:214px;height:337px;float:right;display:inline;overflow:hidden;margin-top:111px;margin-right:58px;}
.zws_luck_right ul{width:214px;height:337px;float:left;display:inline;overflow:hidden;}
.zws_luck_right ul li{width:100%;height:64px;float:left;display:inline;overflow:hidden;color:#000;padding-top:18px;}
.zws_luck_right ul li span{width:62px;height:62px;float:left;display:inline;overflow:hidden;border:1px solid #ffc742; text-align:center;background:#FFF;}
.zws_luck_right ul li span img{width:auto;max-width:100%;_width:62px;}
.zws_luck_right ul li p{width:135px;float:right;display:inline;overflow:hidden;font-size:12px;color:#000;line-height:22px;padding-top:10px;padding-left:10px;}

.zws_luck_remind_bg{width:100%;height:100%;position:fixed;z-index:11;background:#000;filter:alpha(opacity=30);opacity:0.3;display:none;left:0;top:0;}
.zws_luck_remind{width:310px;height:273px;float:left;display:none;background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/luck/close_zj_02.png) no-repeat center;_background:url(<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/luck/zjbg.gif) no-repeat center !important;position:absolute;left:50%;top:50%;margin-left:-155px;margin-top:-137px;z-index:22;}
.zws_luck_remind_close{width:27px;height:28px;float:right;display:inline;overflow:hidden;}
.zws_luck_remind_text1{width:170px;height:auto;float:left;display:inline;padding:88px 0 0 81px;font-size:14px;line-height:24px;color:#333333;text-align:center;font-family:"微软雅黑"}
.zws_luck_remind_text1 b{color:#e43925;display:block;font-weight:normal;padding-bottom:8px;}
.zws_luck_remind_text1 span{color:#e43925;border:1px solid #febf15;font-weight:bold;font-size:16px;}
.zws_luck_remind_text1 strong{color:#c7a42c;display:block;font-size:12px;font-weight:normal;padding-bottom:8px;}
/*转盘样式*/
#flashContent { width:510px; height:510px; }
#layer { /*background:#f5f5f5; border:#000 solid 1px;*/ padding:20px; position:absolute; left:50%; top:50%; display:none; }

</style>

<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>
<div id="js_inner">
    <!--抽奖转盘-->
    <div class="zws_luck_clear"></div>
    <div class="zws_luck">
        <div class="zws_luck_main">
            <!--左侧说明-->
            <div class="zws_luck_left">
                   <span class="zws_luck_left_indro">
                       <b>抽奖规则说明：</b>
                       <p>
                          1、每次抽奖需要消耗500积分，每天不限抽奖次数；<br/>
                          2、中奖后客服人员将在10个工作日内与中奖客户联系；<br/>
                          3、抽奖前请认真核对个人认证信息，如因信息错误而导致无法收到礼品，责任由经纪人承担；<br/>
                          4、由中奖人承担相应所得税，并自行办理相关纳税申报事宜；<br/>
                          5、礼品以实物为准，图片仅供参考；<br/>
                          6、在法律允许的范围内，平台拥有对本抽奖规则的解释权。
                       </p>

                   </span>
                    <!--积分和抽奖次数-->
                    <dl class="zws_luck_left_grade">
                        <dd><strong>我的积分:</strong><b id='credit_total'><?=$credit_total?></b></dd>
                        <dt><strong>剩余抽奖次数:</strong><b id='credit_num'><?=$credit_num?></b></dt>
                    </dl>
            </div>
            <!--中间转盘-->
            <div class="zws_luck_center">
				<div id="flashContent">
					<object width="510" height="510" align="middle" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id="lottery">
					<param value="always" name="allowScriptAccess">
					<param value="/flash/lottery.swf" name="movie">
					<param value="high" name="quality">
					<param value="total_num=10&bg=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/luck/<?=$reward_item?>&pointer=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/luck/pointer2.png&btn=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/luck/btn.png" name="FlashVars">
					<param value="transparent" name="wmode">
					<param value="false" name="menu">
					<embed FlashVars="total_num=10&bg=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/luck/<?=$reward_item?>&pointer=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/luck/pointer2.png&btn=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/luck/btn.png" width="510" height="510" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="always" wmode="transparent" name="lottery" menu="false" quality="high" src="/flash/lottery.swf">
					</object>
				</div>
            </div>

			<input type="hidden" name="code" id="code" value="<?=$code?>">
            <!--中奖名单展示-->
            <div class="zws_luck_right">
                <ul>
				<?php if(is_full_array($gift_raffle_data)){
						foreach($gift_raffle_data as $key=>$vo){
							?>
							<li>
								<span><img src="<?=$vo['product_picture'];?>" /></span>
								<p>
									<strong style="display:block;overflow:hidden;font-weight:normal;white-space: nowrap;text-overflow: ellipsis;"><?=$vo['phone'];?>&nbsp;&nbsp;<?=$vo['truename']?></strong>
									<b style="weight:normal;display:block;">抽中&nbsp;<?=$vo['product_name'];?></b>
								</p>
							</li>
				<?php }} ?>
                </ul>
            </div>
        </div>

    </div>
    <div class="zws_luck_clear"></div>
</div>
<!--中奖提示-->
<div class="zws_luck_remind_bg"></div>
<!--积分不够提示-->
<div class="zws_luck_remind">
       <span class="zws_luck_remind_close"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/luck/close_zj_03.gif" /></span>
        <div class="zws_luck_remind_text1">
            您当前的积分不足！<br />攒够积分下次再来抽奖吧。
        </div>
</div>
<!--中间提示-->
<div class="zws_luck_remind" id='layer' ><!-- style='display:block' -->
    <span class="zws_luck_remind_close" style='cursor:pointer'><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/luck/close_zj_03.gif" /></span>
    <div class="zws_luck_remind_text1" id='layer_text'>
        <!--<b>恭喜! 您已抽中奖品！</b><span>格兰仕电烤箱</span><strong>请等待客服人员与您联系</strong>-->
    </div>
</div>
    <!--未中奖提示-->
<div class="zws_luck_remind" style="display:none;">
    <span class="zws_luck_remind_close"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/luck/close_zj_03.gif" /></span>
    <div class="zws_luck_remind_text1">
        啊哦，很遗憾您未中奖！<br />还有机会，请再接再厉哦。
    </div>
</div>

<script type="text/javascript">
    $(function () {
		if($(".zws_luck_right li").length>4){
			var aLi_H = $(".zws_luck_right li").outerHeight();
			$(".zws_luck_right ul").html($(".zws_luck_right ul").html() + $(".zws_luck_right ul").html());
			var aLi_length = $(".zws_luck_right li").length;
			var aUl_H = aLi_H * aLi_length;
			$(".zws_luck_right ul").css("height", aLi_length * aLi_H+"px");
			var step = 0;
			var timer = "";
			 timer = setInterval(function () {
				var aTop = $(".zws_luck_right ul").css("margin-top");
				aTop = aTop.substring(0, (aTop.length - 2));
				if (Math.abs(aTop) < aUl_H / 2) {
					step += 1;
				}
				else {
					step = 0;
				}
				$(".zws_luck_right ul").css("margin-top", -step + "px");
				//alert(aTop);
			}, 30);

			$(".zws_luck_right ul").mouseover(function () {

				clearInterval(timer);
			});

			$(".zws_luck_right ul").mouseout(function () {

				timer = setInterval(function () {
					var aTop = $(".zws_luck_right ul").css("margin-top");
					aTop = aTop.substring(0, (aTop.length - 2));
					if (Math.abs(aTop) < aUl_H / 2) {
						step += 1;
					}
					else {
						step = 0;
					}
					$(".zws_luck_right ul").css("margin-top", -step + "px");
					//alert(aTop);
				}, 30);
			});
		}
		$(".zws_luck_remind_close").click(function(){
			$(".zws_luck_remind").hide();
			$(".zws_luck_remind_bg").hide();
		})
    })


</script>


<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=house.js,openWin.js,backspace.js"></script>
</body>
</html>
