<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="format-detection" content="telephone=no">
  <title>积分抽奖</title>
  <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,css/v1.0/app_cj.css" rel="stylesheet" type="text/css">
  <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
  <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/awardRotate.js"></script>
</head>
<body>
	<input type='hidden' id='scode' value='<?=$param['scode']?>'>
	<input type='hidden' id='api_key' value='<?=$param['api_key']?>'>
	<input type='hidden' id='version' value='<?=$param['version']?>'>
	<input type='hidden' id='deviceid' value='<?=$param['deviceid']?>'>
	<input type='hidden' id='app_channel' value='<?=$param['app_channel']?>'>
    <div class="app_cj_con">
        <!--<span class="app_cj_con_title">
            <a href="javascript:history.go(-1);"><img src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/app_cj/cj_left_02.jpg" /></a>
            积分抽奖
        </span>-->
        <!--中奖信息滚动-->
        <div class="app_cj_con_mess">
            <ul>
                <?php if(is_full_array($gift_raffle_data)){
						foreach($gift_raffle_data as $key=>$vo){?>
							<li><?=$vo['phone'];?> <?=$vo['truename']?> 抽中<?=$vo['product_name'];?></li>
				<?php }} ?>
            </ul>

        </div>
        <span class="app_cj_con_gift"><img src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/app_cj/cj_1_05.png" /></span>
        <!--抽奖区域-->
		<div class="app_cj_con_area">
            <div class="banner">
                <div class="turnplate" style="background-image:url(<?=MLS_SOURCE_URL?>/mls/images/v1.0/app_cj/turnplate-bg2.png);background-size:100% 100%;">
                    <canvas class="item" id="wheelcanvas" width="422" height="422"></canvas>
                    <img class="pointer" src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/app_cj/turnplate-pointer2.png" />
                </div>
            </div>
        </div>
        <dl class="app_cj_con_time">
            <dd>剩余抽奖次数<b id='credit_num'><?=$credit_num?></b></dd>
            <dt>
                <!--<a href="<?php echo MLS_MOBILE_URL;?>/gift_exchange/record?scode=<?=$param['scode']?>&api_key=<?=$param['api_key']?>&version=<?=$param['version']?>&deviceid=<?=$param['deviceid']?>&app_channel=<?=$param['app_channel']?>" >中奖查询</a>|--><a href="<?php echo MLS_MOBILE_URL; ?>/gift_exchange/raffle_rule?scode=<?=$param['scode']?>&api_key=<?=$param['api_key']?>&version=<?=$param['version']?>&deviceid=<?=$param['deviceid']?>&app_channel=<?=$param['app_channel']?>">抽奖规则</a>
            </dt>
        </dl>
    </div>

<!--中间提示-->
<div class="app_cj_con_bj_remind" style="display:none;"></div>
<!--积分不够-->
<div class="app_cj_con_zj" style="display:none;">
    <span class="app_cj_con_zj_close"><img src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/app_cj/close_06.png" /></span>
    <div class="app_cj_con_zj_inf">
          <span class="app_cj_con_zj_inf_lose">您当前的积分不足！<br/>攒够积分下次再来抽奖吧。</span>
    </div>
</div>
<!--未中奖-->
<div class="app_cj_con_zj" style="display:none;" >
    <span class="app_cj_con_zj_close"><img src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/app_cj/close_06.png" /></span>
    <div class="app_cj_con_zj_inf">
        <span class="app_cj_con_zj_inf_lose">啊哦，很遗憾您未中奖！<br />还有机会，请再接再厉哦。</span>
    </div>
</div>

<!--中奖-->
<div class="app_cj_con_zj" style="display:none;"  id='layer' >
    <span class="app_cj_con_zj_close"><img src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/app_cj/close_06.png" /></span>
    <div class="app_cj_con_zj_inf" id='layer_text'>
        <div class="app_cj_con_zj_inf_sucess">
            <b>恭喜! 您已抽中奖品</b>
            <span><strong>小米移动电源</strong></span>
            <p>请等待客服人员与您联系</p>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {

        $(".app_cj_con_bj_remind").css("height", ($(window).height()) + "px");


        var speed = 0;
        var aul_W = 0;

        $(".app_cj_con_mess").find("ul").html($(".app_cj_con_mess").find("ul").html() + $(".app_cj_con_mess").find("ul").html());
        for (var i = 0; i < $(".app_cj_con_mess").find("li").length; i++) {
            aul_W = aul_W + $(".app_cj_con_mess").find("li").eq(i).outerWidth();

        }
       // alert(aul_W/2);
        $(".app_cj_con_mess").find("ul").css("width", aul_W + "px");
        setInterval(function () {
            speed++;
            if (speed < aul_W/2) {
                speed = speed;
            }
            else {
                speed = 0;
            }
            $(".app_cj_con_mess").find("ul").animate({ "margin-left": -speed + "px" }, 30)

        })

		$(".app_cj_con_zj_close").click(function(){
			$(".app_cj_con_bj_remind").hide();
			$(".app_cj_con_zj").hide();
		})
    })
</script>


<script type="text/javascript">
var turnplate={
	restaraunts:[],				//大转盘奖品名称
	colors:[],					//大转盘奖品区块对应背景颜色
	outsideRadius:179,			//大转盘外圆的半径
	textRadius:135,				//大转盘奖品位置距离圆心的距离
	insideRadius:60,			//大转盘内圆的半径
	startAngle:0,				//开始角度
	bRotate:false				//false:停止;ture:旋转
};

var award_writer = '';//得到奖品描述
var credit_total = '';//抽奖者剩余积分
var credit_num = '';//抽奖者剩余抽奖次数

$(document).ready(function(){
	var scode = $('#scode').val();
	var api_key = $('#api_key').val();
	var version = $('#version').val();
	var deviceid = $('#deviceid').val();
	var app_channel = $('#app_channel').val();
	//动态添加大转盘的奖品与奖品区域背景颜色
    turnplate.restaraunts = ["<?=$reward[1]['name']?>", "<?=$reward[2]['name']?>", "<?=$reward[3]['name']?>", "<?=$reward[4]['name']?>", "<?=$reward[5]['name']?>", "<?=$reward[6]['name']?>", "<?=$reward[7]['name']?>", "<?=$reward[8]['name']?>", "<?=$reward[9]['name']?>", "<?=$reward[10]['name']?>"];
    turnplate.colors = ["#fbdb00", "#faca00", "#fbdb00", "#faca00", "#fbdb00", "#faca00", "#fbdb00", "#faca00", "#fbdb00", "#faca00"];


	var rotateTimeOut = function (){
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:2160,
			duration:8000,
			callback:function (){
				alert('网络超时，请检查您的网络设置！');
			}
		});
	};

	//旋转转盘 item:奖品位置; txt：提示语;
	var rotateFn = function (item, txt){
		var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
		if(angles<270){
			angles = 270 - angles;
		}else{
			angles = 360 - angles + 270;
		}
		$('#wheelcanvas').stopRotate();
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:angles+1800,
			duration:8000,
			callback:function (){
			    // alert(txt);
			    //回调函数
                //中奖提示
				if(credit_total != ''){
					$("#credit_total").html(credit_total);
					$("#credit_num").html(credit_num);
				}
				$("#layer #layer_text").html(award_writer);
				$("#layer").show();
				$(".app_cj_con_bj_remind").show();
			    //console.log(txt);
				turnplate.bRotate = !turnplate.bRotate;
			}
		});
	};

	$('.pointer').click(function (){
		$.ajax({
			url: "/gift_exchange/lottery?scode="+scode+"&api_key="+api_key+"&version="+version+"&deviceid="+deviceid+"&app_channel="+app_channel,
			type: "get",
			data:{code : $('#code').val()},
			dataType: "json",
			cache: false,
			beforeSend: function(){// 提交之前
			},
			error: function(){//出错
				alert('网络异常，请重试！');
			},
			success: function(res){//成功
				if (res.result == 1)
				{
					if(typeof(res.award_id)!='undefined' && res.award_id != ''){
						award_writer = res.award_writer;
						if(turnplate.bRotate)return;
						turnplate.bRotate = !turnplate.bRotate;
						//获取随机数(奖品个数范围内)
						var item = res.award_id;
						//奖品数量等于10,指针落在对应奖品区域的中心角度[252, 216, 180, 144, 108, 72, 36, 360, 324, 288]
						rotateFn(item, turnplate.restaraunts[item-1]);
						/* switch (item) {
							case 1:
								rotateFn(252, turnplate.restaraunts[0]);
								break;
							case 2:
								rotateFn(216, turnplate.restaraunts[1]);
								break;
							case 3:
								rotateFn(180, turnplate.restaraunts[2]);
								break;
							case 4:
								rotateFn(144, turnplate.restaraunts[3]);
								break;
							case 5:
								rotateFn(108, turnplate.restaraunts[4]);
								break;
							case 6:
								rotateFn(72, turnplate.restaraunts[5]);
								break;
							case 7:
								rotateFn(36, turnplate.restaraunts[6]);
								break;
							case 8:
								rotateFn(360, turnplate.restaraunts[7]);
								break;
							case 9:
								rotateFn(324, turnplate.restaraunts[8]);
								break;
							case 10:
								rotateFn(288, turnplate.restaraunts[9]);
								break;
						} */
						//console.log(item);
					}else{
						award_writer = res.award_writer;
					}
					credit_total = res.credit_total;//抽奖者剩余积分
					credit_num = res.credit_num;//抽奖者剩余抽奖次数
				}
				else
				{
					alert(res.reason);
				}
			}
		});

	});
});

function rnd(n, m){
	var random = Math.floor(Math.random()*(m-n+1)+n);
	return random;

}


//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
window.onload=function(){
	drawRouletteWheel();
};

function drawRouletteWheel() {
	var canvas = document.getElementById("wheelcanvas");
	if (canvas.getContext) {
		//根据奖品个数计算圆周角度
		var arc = Math.PI / (turnplate.restaraunts.length/2);
		var ctx = canvas.getContext("2d");
		//在给定矩形内清空一个矩形
		ctx.clearRect(0,0,422,422);
		//strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式
		ctx.strokeStyle = "#FFBE04";
		//font 属性设置或返回画布上文本内容的当前字体属性
		ctx.font = '16px Microsoft YaHei';
		for(var i = 0; i < turnplate.restaraunts.length; i++) {
			var angle = turnplate.startAngle + i * arc;
			ctx.fillStyle = turnplate.colors[i];
			ctx.beginPath();
			//arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）
			ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);
			ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
			ctx.stroke();
			ctx.fill();
			//锁画布(为了保存之前的画布状态)
			ctx.save();

			//----绘制奖品开始----
			ctx.fillStyle = "#E5302F";
			var text = turnplate.restaraunts[i];
			var line_height = 17;
			//translate方法重新映射画布上的 (0,0) 位置
			ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);

			//rotate方法旋转当前的绘图
			ctx.rotate(angle + arc / 2 + Math.PI / 2);

			/** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
			if(text.indexOf("M")>0){//流量包
				var texts = text.split("M");
				for(var j = 0; j<texts.length; j++){
					ctx.font = j == 0?'bold 20px Microsoft YaHei':'16px Microsoft YaHei';
					if(j == 0){
						ctx.fillText(texts[j]+"M", -ctx.measureText(texts[j]+"M").width / 2, j * line_height);
					}else{
						ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
					}
				}
			}else if(text.indexOf("M") == -1 && text.length>6){//奖品名称长度超过一定范围
				text = text.substring(0,6)+"||"+text.substring(6);
				var texts = text.split("||");
				for(var j = 0; j<texts.length; j++){
					ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
				}
			}else{
				//在画布上绘制填色的文本。文本的默认颜色是黑色
				//measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
				ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
			}

			//添加对应图标
			/* if(text.indexOf("闪币")>0){
				var img= document.getElementById("shan-img");
				img.onload=function(){
					ctx.drawImage(img,-15,10);
				};
				ctx.drawImage(img,-15,10);
			}else if(text.indexOf("谢谢参与")>=0){
				var img= document.getElementById("sorry-img");
				img.onload=function(){
					ctx.drawImage(img,-15,10);
				};
				ctx.drawImage(img,-15,10);
			}*/
			//把当前画布返回（调整）到上一个save()状态之前
			ctx.restore();
			//----绘制奖品结束----
		}
	}
}

</script>
</body>
</html>
