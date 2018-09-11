<script>
    window.parent.addNavClass(9);
</script>
<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css,css/v1.0/cal.css " rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/guest_disk.css " rel="stylesheet" type="text/css">
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/cal.js"></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/shuifei.js"></script>
<script type="text/javascript">
var calculate = {
	'+' : function(arg1,arg2){
		var r1,r2,m;
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
		m=Math.pow(10,Math.max(r1,r2));
		return (arg1*m+arg2*m)/m;
	},
	'-' : function(arg1,arg2){
		var r1,r2,m,n;
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
		m=Math.pow(10,Math.max(r1,r2));
		n=(r1>=r2)?r1:r2;
		return ((arg1*m-arg2*m)/m).toFixed(n);
	},
	'*' : function(arg1,arg2){
		var m=0,s1=arg1.toString(),s2=arg2.toString();
		try{m+=s1.split(".")[1].length}catch(e){}
		try{m+=s2.split(".")[1].length}catch(e){}
		return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
	},
	'/' : function(arg1,arg2){
		var t1=0,t2=0,r1,r2;
		try{t1=arg1.toString().split(".")[1].length}catch(e){}
		try{t2=arg2.toString().split(".")[1].length}catch(e){}
		r1=Number(arg1.toString().replace(".",""))
		r2=Number(arg2.toString().replace(".",""))
		return (r1/r2)*Math.pow(10,t2-t1);
	}
};
$(document).ready(function(){
    $("#newhouse").click(function(){
	  $("#newhouse_tab").show();
	  $("#secondhouse_tab").hide();
	  $("#newhouse_p").show();
	  $("#second_p").hide();
	});
	$("#secondhouse").click(function(){
	  $("#secondhouse_tab").show();
	  $("#newhouse_tab").hide();
	  $("#newhouse_p").hide();
	  $("#second_p").show();
	});


	$('input[name="zhuanran"]').click(function(){
		$('#transferyear').val($(this).attr('value'));
	});

	$('input[name="shouci"]').click(function(){
		$('#firstbuy').val($(this).attr('value'));
	});

	var gongshi = {
		'1+1' : 120,
		'1+2' : 75,
		'2+1' : 90,
		'2+2' : 50
	};

	var qiesui = {
		'90+1' : 0.01,
		'144+1' : 0.015,
		'144-2' : 0.03
	};

	$('#xf-submit').click(function(){

		var error = false, val;
		$.each(['xf-danjia', 'xf-mianji'], function(i, n){
			val = $.trim($('#' + n).val());
			if(!val){
				alert('请输入' + (n == 'xf-danjia' ? '单价' : '面积'));
				error = true;
				return false;
			}
		});
		if(error){
			return false;
		}

		var leixing = $('input[name="xf-leixing"]:checked').val(),
			weiyi = $('input[name="xf-weiyi"]:checked').val(),
			dianti = $('input[name="xf-dianti"]:checked').val(),
			danjia = $('#xf-danjia').val(),
			mianji = $('#xf-mianji').val();

		var total = calculate['*'](danjia, mianji);
		$('#xf-zongjia').val(total);
		$('#xf-jijin').val(calculate['*'](gongshi[parseInt(leixing) + '+' + parseInt(dianti)], mianji));

		var type = '';
		if(mianji <= 90 && weiyi == 1){
			type = '90+1';
		}else if(mianji > 90 && mianji <= 144 && weiyi == 1){
			type = '144+1';
		}else if(mianji > 144 || weiyi == 2){
			type = '144-2';
		}
		$('#xf-qisui').val(calculate['*'](qiesui[type], total));
		return false;
	});

	$('#xf-reset').click(function(){
		$('#xf-form').get(0).reset();
		return false;
	});

	$('#xf-danjia, #xf-mianji, #my-averprice, #my-buildarea').blur(function(){
		var val = $.trim($(this).val());
		if(val && isNaN(val)){
			error();
			$(this).focus().val('');
		}
	});

	function error(){
		alert("您输入的格式有误，请重新填写单价\n\n注：输入内容必须为数字、半角格式、最小数值可精确到后两位。");
	}


	$('input,textarea', $('.jsq_tab_table_right')).attr('readonly', 'readonly');

	$('#my-reset-shuifei').click(function(){
		$('.my-tip').html('');
	});
});
</script>
<!--主要内容-->
<div style="padding-top:10px;">
	<div class="cal-cont clearfix">
		<img class="tip3" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/tip3.png" />
		<div class="fl" style="margin-right:400px;">
            <h3>请填写数据</h3>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="left_table" id="shuifei_check">
                <tbody><tr>
                  <td width="126" align="right">购房类型：</td>
                  <td width="323"><input type="radio" name="newl" id="newhouse" value="1" checked="checked" autocomplete="off">
                    新房&nbsp;&nbsp;
                    <input type="radio" name="newl" id="secondhouse" value="2" autocomplete="off">
                    二手房</td>
                </tr>
            </tbody></table>
		</div>
		<form id="xf-form" name="xf-form" autocomplete="off">
			<div id="newhouse_tab" style="display:block">
				<div class="fl">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="left_table">
						<tbody>
						<tr>
							<td width="126" align="right">
								住房类型：
							</td>
							<td width="323">
								<input type="radio" name="xf-leixing" value="1" checked> 商品住宅&nbsp;&nbsp; <input type="radio" name="xf-leixing" value="2"> 政策性住宅
							</td>
						</tr>
						<tr>
							<td align="right">
								购房性质：
							</td>
							<td>
								<input type="radio" name="xf-weiyi" value="1" checked> 家庭唯一一套住宅
							</td>
						</tr>
						<tr>
							<td align="right">
							</td>
							<td>
								<input type="radio" name="xf-weiyi" value="2"> 非家庭唯一一套住宅
							</td>
						</tr>
						<tr>
							<td align="right">
								单价：
							</td>
							<td>
								<input class="t-input2" name="xf-danjia" id="xf-danjia" type="text" size="10" maxlength="10"> 元/平方米
							</td>
						</tr>
						<tr>
							<td align="right">
								面积：
							</td>
							<td>
								<input class="t-input2" name="xf-mianji" id="xf-mianji" type="text" size="10" maxlength="10"> 平方米
							</td>
						</tr>
						<tr>
							<td align="right">
								房屋类别：
							</td>
							<td>
								<input type="radio" name="xf-dianti" value="1" checked> 有电梯&nbsp;&nbsp; <input type="radio" name="xf-dianti" value="2"> 没有电梯
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="padding-top:20px;"><a class="btn-lv btn-left" href="javascript:void(0);" id="xf-submit"><span>开始计算</span></a><a class="btn-hui1" href="javascript:void(0);" id="xf-reset"><span>重填</span></a></td>
						</tr>
						</tbody>
					</table>
					<!--p class="tips2">注：<br>
						1.凡满足以下二条非普通住宅标准中一个条件，就视为非普通住宅：① 住宅容积率≤1.0；② 单套建筑面积≥144平方米<br>
						2.营业税及其它附加费5.55%=营业税+城市维修建设费+教育附加费+地方教育附加费等。<br>
						3.营业税:个人将购买不足2年的住房对外销售的，全额征收营业税；个人将购买2年以上（含2年）的非普通住房对外销售的，按照其销售收入减去购买房屋的价款后的差额征收营业税；个人将购买2年以上（含2年）的普通住房对外销售的，免征营业税。<br>
						4.个人所得税:2010年1月1日起不再发放40%的个税补贴,个人所得税按核定征收1%或查验征收20%。<br>
						5.契税：首套房且面积在90平米以下按1%征收, 首套房且面积在90—144平方米之间按照1.5%征收契税, 首套房且面积在144平方米以上按照3%标准征收契税, 其他所有购房情况，均按3%征收.<br>
						6.首次购房：夫妻双方在南京11区内首次购房。凭夫妻双方身份证、户口本、结婚证、（未婚提供民政局单身证明）前往房地产交易市场首次购房窗口开具证明。</p-->
				</div>
				<div class="fr" style="margin-top:-73px;">
					<h3>查看计算结果</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="right_table shuifei_table">
						<tbody>
						<tr>
							<td width="112" align="right">
								房屋总价：
							</td>
							<td>
								<input class="t-input2" name="xf-zongjia" id="xf-zongjia" type="text" readonly> 元
							</td>
						</tr>
						<tr>
							<td align="right">
								物业维修基金：
							</td>
							<td>
								<input class="t-input2" name="xf-jijin" id="xf-jijin" type="text" readonly> 元
							</td>
						</tr>
						<tr>
							<td align="right">
								契税：
							</td>
							<td>
								<input class="t-input2" name="xf-qisui" id="xf-qisui" type="text" readonly> 元
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</form>
		<form name="resultform" id="resultform" autocomplete="off">
			<div id="secondhouse_tab" style="display:none">
				<div class="fl">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="left_table">
						<tbody>
						<tr>
							<td width="25%" align="right">
								房屋类型：
							</td>
							<td width="75%">
								<select id="housekind" name="housekind">
									<option value="1">住宅</option>
									<option value="2">非住宅</option>
									<option value="3">房改房</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right">
								单价：
							</td>
							<td>
								<input class="t-input2" name="averprice" type="text" size="10" maxlength="10" id="my-averprice"> 元/平方米
							</td>
						</tr>
						<tr>
							<td align="right">
								面积：
							</td>
							<td>
								<input class="t-input2" name="buildarea" type="text" size="10" maxlength="10" id="my-buildarea"> 平方米
							</td>
						</tr>
						<tr>
							<td align="right">
								几年内转让：
							</td>
							<td>
								<input type="radio" name="zhuanran" value="1" checked> 2年内&nbsp;&nbsp; <input type="radio" name="zhuanran" value="2"> 2年后 <input type="hidden" name="transferyear" id="transferyear" value="1">
							</td>
						</tr>
						<tr>
							<td width="28%" align="right">
								是否首次购房：
							</td>
							<td width="72%">
								<input type="radio" name="shouci" value="1" checked> 首次购房&nbsp;&nbsp; <input type="radio" name="shouci" value="2"> 非首次购房 <input type="hidden" name="firstbuy" id="firstbuy" value="1">
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="padding-top:20px;"><a class="btn-lv btn-left" href="javascript:void(0);" onclick="check();return false;"><span>开始计算</span></a><a class="btn-hui1" href="javascript:void(0);"  id="my-reset-shuifei" onclick="document.getElementById(&quot;resultform&quot;).reset()"><span>重填</span></a></td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="fr" style="margin-top:-73px;">
					<h3>查看计算结果</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="right_table shuifei_table">
						<tbody>
						<tr>
							<td width="112" align="right">
								房屋性质：
							</td>
							<td>
								<input class="t-input2" name="housetype" type="text" readonly>
							</td>
						</tr>
						<tr>
							<td align="right">
								税前总价：
							</td>
							<td>
								<input class="t-input2" name="totalprice" type="text" readonly> 万元
							</td>
						</tr>
						<tr style="height:40px; color:#333;font-weight:bold; border-top:1px solid #EFEFEF;">
							<td style="padding-left:50px;">卖方税费</td>
							<td></td>
						</tr>
						<tr>
							<td align="right">
								过户费：
							</td>
							<td>
								<input class="t-input2" name="sell_ghfee" type="text" readonly> 元<span id="lab_s1" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								营业税：
							</td>
							<td>
								<input class="t-input2" name="sell_yyfee" type="text" readonly> 元<span id="lab_s2" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								土地增值税：
							</td>
							<td>
								<input class="t-input2" name="sell_zzfee" type="text" readonly> 元<span id="lab_s3" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								个人所得税：
							</td>
							<td>
								<?php if($city == 'qingdao'){ ?>
								<input type="hidden" id="grsds" value="0.02" /><input type="hidden" id="lab_grsds" value="全额2%" />
								<?php }else{ ?>
								<input type="hidden" id="grsds" value="" /><input type="hidden" id="lab_grsds" value="" />
								<?php } ?>
								<input type="hidden" id="grsds" value="" /><input type="hidden" id="lab_grsds" value="" />
								<input class="t-input2" name="sell_sdfee" type="text" readonly> 元<span id="lab_s4" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								合计：
							</td>
							<td>
								<input class="t-input2" name="sell_sum" type="text" readonly> 元
							</td>
						</tr>
						<tr style="height:40px; color:#333;font-weight:bold; border-top:1px solid #EFEFEF;">
							<td style="padding-left:50px;">买方税费</td>
							<td></td>
						</tr>
						<tr>
							<td align="right">
								过户费：
							</td>
							<td>
								<input class="t-input2" name="buy_ghfee" type="text" readonly> 元<span id="lab_b1" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								契税：
							</td>
							<td>
								<input class="t-input2" name="buy_qfee" type="text" readonly> 元<span id="lab_b2" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								房屋产权登记费：
							</td>
							<td>
								<input class="t-input2" name="buy_djfee" type="text" readonly> 元<span id="lab_b3" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								土地证工本费：
							</td>
							<td>
								<input class="t-input2" name="buy_gbfee" type="text" readonly> 元<span id="lab_b4" class="my-tip"></span>
							</td>
						</tr>
						<tr>
							<td align="right">
								合计：
							</td>
							<td>
								<input class="t-input2" name="buy_sum" type="text" readonly> 元
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</form>
		<p class="tips1">* 以上查询结果仅供参考，以最终受理结果为准 *</p>
    </div>
</div>
<script type="text/javascript">
(function($){
	var calculate = {
		'+' : function(arg1,arg2){
			var r1,r2,m;
			try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
			try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
			m=Math.pow(10,Math.max(r1,r2));
			return (arg1*m+arg2*m)/m;
		},
		'-' : function(arg1,arg2){
			var r1,r2,m,n;
			try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
			try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
			m=Math.pow(10,Math.max(r1,r2));
			n=(r1>=r2)?r1:r2;
			return ((arg1*m-arg2*m)/m).toFixed(n);
		},
		'*' : function(arg1,arg2){
			var m=0,s1=arg1.toString(),s2=arg2.toString();
			try{m+=s1.split(".")[1].length}catch(e){}
			try{m+=s2.split(".")[1].length}catch(e){}
			return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
		},
		'/' : function(arg1,arg2){
			var t1=0,t2=0,r1,r2;
			try{t1=arg1.toString().split(".")[1].length}catch(e){}
			try{t2=arg2.toString().split(".")[1].length}catch(e){}
			r1=Number(arg1.toString().replace(".",""))
			r2=Number(arg2.toString().replace(".",""))
			return (r1/r2)*Math.pow(10,t2-t1);
		}
	};

	$(function(){

		$('input[name="my-type"]').click(function(){
			var val = parseInt($(this).attr('value'));
			if(val != 3){
				$('#dai-kuan-lei-bie').show();
				$('#zuhe').hide();
			}else{
				$('#dai-kuan-lei-bie').hide();
				$('#zuhe').show();
			}
			$('#my-hidden-type').val(val);
			//exc_zuhe($('#col-form')[0], val);
		});

		$('input[name="fangsih"]').click(function(){
			var val = $(this).attr('value'),
				form = $('#col-form');
			form.attr('name', 'calc' + val);
			$('#right-table-1, #right-table-2').hide();
			$('#right-table-' + val).show();
		});

		$('#anjie-select').change(function(){
			var val = parseInt($(this).val());
			$('#custom-box')[val == 0 ? 'show' : 'hide']();
			$('#anjie-custom').val('');
			$('#anjie').val(val);
		});
		$('#anjie-custom').blur(function(){
			var val = $.trim($(this).val());
			if(val && isNaN(val)){
				error();
				$(this).focus().val('');
			}else{
				$('#anjie').val($(this).val());
			}
		});

		$('input[name="jisuan_radio"]').click(function(){
			var id = $(this).attr('id');
			if(id == 'calc1_radio3'){
				$('#my-price, #my-sqm, #anjie-select, #anjie-custom').removeAttr('disabled');
				$('#my-total').attr('disabled', 'disabled');
			}else{
				$('#my-price, #my-sqm, #anjie-select, #anjie-custom').attr('disabled', 'disabled');
				$('#my-total').removeAttr('disabled');
			}
		});

		$('#my-price, #my-sqm, #my-total, #my-total-sy, #my-total-gjj').blur(function(){
			var val = $.trim($(this).val());
			if(val && isNaN(val)){
				error();
				$(this).focus().val('');
			}
		})

		function error(){
			alert("您输入的格式有误，请重新填写单价\n\n注：输入内容必须为数字、半角格式、最小数值可精确到后两位。");
		}


		$('input,textarea', $('.jsq_tab_table_right')).attr('readonly', 'readonly');

		$('#my-reset-daikuan').click(function(){
			$('input[name="my-type"]').eq(0).click();
			$('input[name="jisuan_radio"]').eq(0).click();
			$('input[name="fangsih"]').eq(0).click();
			$('#anjie-select').change();
		})
	});
})(jQuery);
</script>


















<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
