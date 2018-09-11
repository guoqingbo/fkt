<style>
     .hide {
       position: absolute !important;
       top: -9999px !important;
       left: -9999px !important;
    }
</style>
<div class="xcc">
		<div class="inner xcc1">
			<a class="a1" href="<?php echo MLS_URL;?>/cooperate_lol/index#qiang" title="抢占先机">抢占先机</a>
			<a class="a2" href="<?php echo MLS_URL;?>/cooperate_lol/index#cheng" title="称霸江湖">称霸江湖</a>
		</div>
		<div class="inner" id='qiang'>
			<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/subject/t3.jpg" >
			<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/subject/t4.jpg" >
		</div>
		<div class="inner xcc5">
			<div id="marquee5">
				<ul>
                    <?php if($group){foreach($group as $key=>$val){?>
					<li><?=$val['broker_name']?>&nbsp&nbsp&nbsp<?=$val['phone']?></li>
					<?php }}else{?>
						<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/subject/qidai.png" style='display:block;margin:30px auto 0;'>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="inner"  id='cheng'>
			<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/subject/t6.jpg" >
		</div>
		<div class="inner xcc7">
			<a href="javascript:void(0);" id="send" title="提交初审资料">提交初审资料</a>
		</div>
		<div class="inner">
			<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/subject/t8.jpg" >
		</div>
		<div class="inner xcc9">
            <div id="flashContent">
            <object width="510" height="510" align="middle" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id="lottery">
            <param value="always" name="allowScriptAccess">
            <param value="/flash/lottery.swf" name="movie">
            <param value="high" name="quality">
            <param value="total_num=10&bg=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/xcc/bg.png&pointer=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/xcc/pointer2.png&btn=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/xcc/btn.png" name="FlashVars">
            <param value="transparent" name="wmode">
            <param value="false" name="menu">
            <embed FlashVars="total_num=10&bg=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/xcc/bg.png&pointer=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/xcc/pointer2.png&btn=<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/xcc/btn.png" width="510" height="510" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="always" wmode="transparent" name="lottery" menu="false" quality="high" src="/flash/lottery.swf">
            </object>
            </div>
			<a class="kong" href="#send" style="display:none;"></a>
		</div>
        <input type="hidden" name="code" id="code" value="<?=$code?>">
		<div class="inner xcc10">
			<div id="marquee"><ul>
				<?php
					if(is_full_array($win_list)){
						$str = '';
						foreach($win_list as $key=>$vo){
							foreach($reward_list as $r){
								$vo['phone'] = substr_replace($vo['phone'],'XXXX',3,4);
								if($vo['reward_type'] == $r['id']){
									if($key % 2 == 0){
										$str .= '<li class="clearfix"><span>'.$vo['broker_name'].' '.$vo['phone'].' 获得'.$r['name'].'</span>';
									}else if($key % 2 == 1){
										$str .= '<span>'.$vo['broker_name'].' '.$vo['phone'].' 获得'.$r['name'].'</span></li>';
									}
								}
							}
						}
						echo $str;
					}else{
				?>
				<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/subject/qidai.png" style='display:block;margin:0px 315px 0px;'>
				<?php } ?>

				</ul>
			</div>
		</div>
	</div>
	<div id="layer">
		<a class="close" href="javascript:void(0);" title="关闭">关闭弹框</a>
		<h3></h3>
		<p></p>
	</div>
	<div id="layer2"><!--英雄，不要总是戳人家嘛！待到下次合作成交，初审资料通过后，再来抽奖，可好?-->
		<a class="close" href="javascript:void(0);" title="关闭">关闭弹框</a>
	</div>
	<!--填写初审资料痰弹窗-->
<div id="js_chushen_pop" class="iframePopBox" style="width:800px; height:430px;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="no" width="816" height="540" class='iframePop' src=""></iframe>
</div>
	<!--提示框-->
<div id="js_pop_do_success"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_success_tip">提交成功！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/cooperate_lol/'">确定</button>
			</div>
		</div>
	</div>
</div>
<div id="js_pop_do_warning"	class="pop_box_g pop_see_inform pop_no_q_up">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop">
			<a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a>
		</div>
	</div>
	<div class="mod">
		<div class="inform_inner">
			<div class="up_inner">
				<p class="text" id="dialog_do_warnig_tip">提交失败！</p>
				<button type="button" class="btn-lv1 btn-mid" onclick="location.href='/cooperate_lol/'">确定</button>
			</div>
		</div>
	</div>
</div>
	<script type="text/javascript" src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/xcc_uploadpic.js,mls/js/v1.0/openWin.js,mls/js/v1.0/Marquee.js"></script>
	<script type="text/javascript">

		$(function() {
			/*$("b.label").on('click',function(){//模拟单选按钮
				var i = $(this);
				if($(this).hasClass("labelon"))
				{
					i.find(".js_checkbox").prop("checked",false);
					i.removeClass("labelon");
				}
				else
				{
					i.find(".js_checkbox").prop("checked",true);
					i.addClass("labelon");
				}
			})*/
			function re_height(){//页面高度-滚动
				$(".xcc").css({
					"height":$(window).height()
				});
			};
			re_height();
			$(window).resize(function(e) {
				re_height();
			});

			$('#marquee5').kxbdSuperMarquee({//中奖名单-滚动
				isMarquee:true,
				isEqual:false,
				scrollDelay:50,
				direction:'up'
			});

			$('#marquee').kxbdSuperMarquee({//中奖名单-滚动
				isMarquee:true,
				isEqual:false,
				scrollDelay:50,
				direction:'up'
			});

			$('#layer .close').click(function(){//关闭中奖
				$('#layer').hide();
				$(".shade").hide();
			});
			$('.xcc7 a').click(function(){//打开中奖
				$.ajax({
					type : 'post',
					url  : '/cooperate_lol/check_time/',
					dataType :'json',
					success : function(data){
						if(data['result']){
							$("#js_chushen_pop").children('iframe').attr('src','/cooperate_lol/lol_chushen/');;
							openWin('js_chushen_pop');
						}else{
							$('#layer p').html(data['reason']);
							openWin('layer');
						}
					}
				});
			});

			$('input').focus(function(){
                            $(this).siblings('p').hide();
                        })
		});
	</script>
