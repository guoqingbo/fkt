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
        //手动输入计算
        //商业利率
        $('#lilv_input').live('blur',function(){
            var lilv_input = parseFloat($(this).val()).toFixed(2);
            var lilv_multiple = parseFloat($('#lilv_multiple').val()).toFixed(2);
            var result = lilv_input * lilv_multiple;
            $("#lilv_result").text(result.toFixed(2));
        });
        $('#lilv_multiple').live('blur',function(){
            var lilv_input = parseFloat($('#lilv_input').val()).toFixed(2);
            var lilv_multiple = parseFloat($(this).val()).toFixed(2);
            var result = lilv_input * lilv_multiple;
            $("#lilv_result").text(result.toFixed(2));
        });
        //公积金利率
        $('#lilv_input_2').live('blur',function(){
            var lilv_input_2 = parseFloat($(this).val()).toFixed(2);
            var lilv_multiple_2 = parseFloat($('#lilv_multiple_2').val()).toFixed(2);
            var result = lilv_input_2 * lilv_multiple_2;
            $("#lilv_result_2").text(result.toFixed(2));
        });
        $('#lilv_multiple_2').live('blur',function(){
            var lilv_input_2 = parseFloat($('#lilv_input_2').val()).toFixed(2);
            var lilv_multiple_2 = parseFloat($(this).val()).toFixed(2);
            var result = lilv_input_2 * lilv_multiple_2;
            $("#lilv_result_2").text(result.toFixed(2));
        });

		$('input[name="my-type"]').click(function(){
            //贷款类别
			var my_type = parseInt($(this).attr('value'));
			if(my_type != 3){
				$('#dai-kuan-lei-bie').show();
				$('#zuhe').hide();
                //商业贷款
                if(my_type == 1){
                    $('.daikuan').show();
                    $('.gjj').hide();

                    $("#years,#lilv").change(function(){
                        //手动输入赋值
                        //贷款类别
                        var my_type = $('input[name="my-type"]:checked').val();
                        //按揭年数
                        var years = $('#years').val();
                        //贷款利率
                        var lilv_val = $("#lilv option:selected").val();
                        var lilv = getlilv(lilv_val,my_type,years);//得到利率
                        //小数点处理
                        var lilv_result = lilv * 100;
                        $("#lilv_input").val(lilv_result.toFixed(2));
                        //根据手动输入倍数，计算出结果
                        var lilv_multiple = $('#lilv_multiple').val();
                        var result = (lilv_result.toFixed(2)) * lilv_multiple;
                        $("#lilv_result").text(result.toFixed(2));
                    });
                //公积金贷款
                }else if(my_type == 2){
                    $('.daikuan').hide();
                    $('.gjj').show();

                    $("#years,#lilv_2").change(function(){
                        //手动输入赋值
                        //贷款类别
                        var my_type = $('input[name="my-type"]:checked').val();
                        //按揭年数
                        var years = $('#years').val();
                        //贷款利率
                        var lilv_val = $("#lilv_2 option:selected").val();
                        var lilv = getlilv(lilv_val,my_type,years);//得到利率
                        //小数点处理
                        var lilv_result = lilv * 100;
                        $("#lilv_input_2").val(lilv_result.toFixed(2));
                        //根据手动输入倍数，计算出结果
                        var lilv_multiple = $('#lilv_multiple_2').val();
                        var result = (lilv_result.toFixed(2)) * lilv_multiple;
                        $("#lilv_result_2").text(result.toFixed(2));
                    });
                }
			}else{
                //组合贷款
				$('#dai-kuan-lei-bie').hide();
				$('#zuhe').show();
                $('.daikuan').show();
                $('.gjj').show();

                $("#years_sy,#lilv").change(function(){
                    //手动输入赋值
                    //贷款类别
                    var my_type = $('input[name="my-type"]:checked').val();
                    //按揭年数
                    var years = $('#years_sy').val();
                    //贷款利率
                    var lilv_val = $("#lilv option:selected").val();
                    var lilv = getlilv(lilv_val,my_type,years);//得到利率
                    //小数点处理
                    var lilv_result = lilv * 100;
                    $("#lilv_input").val(lilv_result.toFixed(2));
                    //根据手动输入倍数，计算出结果
                    var lilv_multiple = $('#lilv_multiple').val();
                    var result = (lilv_result.toFixed(2)) * lilv_multiple;
                    $("#lilv_result").text(result.toFixed(2));
                });

                $("#years_gjj,#lilv_2").change(function(){
                    //手动输入赋值
                    //贷款类别
                    var my_type = $('input[name="my-type"]:checked').val();
                    //按揭年数
                    var years = $('#years_gjj').val();
                    //贷款利率
                    var lilv_val = $("#lilv_2 option:selected").val();
                    var lilv = getlilv(lilv_val,my_type,years);//得到利率
                    //小数点处理
                    var lilv_result = lilv * 100;
                    $("#lilv_input_2").val(lilv_result.toFixed(2));
                    //根据手动输入倍数，计算出结果
                    var lilv_multiple = $('#lilv_multiple_2').val();
                    var result = (lilv_result.toFixed(2)) * lilv_multiple;
                    $("#lilv_result_2").text(result.toFixed(2));
                });
			}
			$('#my-hidden-type').val(my_type);

            //手动输入赋值
            //按揭年数
            var years = $('#years').val();
            //贷款利率
            var lilv_val = $("#lilv option:selected").val();
            var lilv = getlilv(lilv_val,my_type,years);//得到利率
            //小数点处理
            var lilv_result = lilv * 100;
            $("#lilv_input").val(lilv_result.toFixed(2));
            //根据手动输入倍数，计算出结果
            var lilv_multiple = $('#lilv_multiple').val();
            var result = (lilv_result.toFixed(2)) * lilv_multiple;
            $("#lilv_result").text(result.toFixed(2));

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
<!--主要内容-->
<div style="padding-top:10px;">
	<div class="cal-cont clearfix"><form name="calc1" id="col-form" autocomplete="off">
		<img class="tip3" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/tip3.png" />
		<div class="fl">
            <h3>请填写数据</h3>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="left_table">
				<tbody><tr>
					<td width="25%" align="right">还款方式：</td>
					<td width="20%"><label><input type="radio" name="fangsih" checked="checked" value="1">等额本息&nbsp;&nbsp;</label></td>
					<td width="20%"><label><input type="radio" name="fangsih" value="2">等额本金</label></td>
					<td></td>
				</tr>
				<tr>
					<td width="25%" align="right">贷款类别：</td>
					<td><label><input type="radio" name="my-type" value="1" checked="checked">商业贷款&nbsp;&nbsp;</label></td>
					<td><label><input type="radio" name="my-type" value="2">公积金贷款&nbsp;&nbsp;</label></td>
					<td><label><input type="radio" name="my-type" value="3">组合贷款<input type="hidden" name="type" id="my-hidden-type" value="1"></td>
				</tr>
			</tbody></table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="left_table" id="zuhe" style="display:none;"><tbody>
				<tr>
					<td width="25%" align="right">商业贷款：</td>
					<td><input class="t-input" name="total_sy" type="text" size="10" maxlength="10" id="my-total-sy">
					元
					<select id="years_sy" name="years_sy">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option selected="" value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					</select>
					年 </td>
				</tr>
				<tr>
					<td width="25%" align="right">公积金贷款：</td>
					<td><input class="t-input" name="total_gjj" type="text" size="10" maxlength="10" id="my-total-gjj">
					元
					<select id="years_gjj" name="years_gjj">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option selected="" value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option selected="" value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					</select>
					年</td>
				</tr></tbody>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="left_table" id="dai-kuan-lei-bie">
				<tbody><tr>
					<td width="25%" align="right">计算方式：</td>
					<td style="color:#666"><label><input type="radio" name="jisuan_radio" id="calc1_radio3" checked="checked">
					<strong>根据面积、单价计算</strong></label></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>单价：
					<input class="t-input" name="price" type="text" size="10" maxlength="10" id="my-price">
					元/平米</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>面积：
					<input class="t-input" name="sqm" type="text" size="10" maxlength="10" id="my-sqm">
					平方米</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>按揭成数：
					<select id="anjie-select" name="anjie-select" style="color:#0877BD;">
					<?php foreach($daikuan['mortgage_percentage'] as $key=>$val){?>
						<option value='<?=$key?>' <?php if($key == 7){?>selected<?php }?>><?=$val?></option>
					<?php }?>
					<!--<option value="2">2成</option>
					<option value="3">3成</option>
					<option value="4">4成</option>
					<option value="5">5成</option>
					<option value="6">6成</option>
					<option selected="" value="7">7成</option>
					<option value="8">8成</option>
					<option value="9">9成</option>
					<option value="0">其他</option>-->
					</select>
					<span id="custom-box" style="display: none;"><input type="text" id="anjie-custom" value="" style="width:25px;">成</span>
					<input type="hidden" id="anjie" name="anjie" value="7">
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="color:#666"><label><input type="radio" name="jisuan_radio" id="calc1_radio4">
					<strong>根据贷款总额计算</strong></label></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>贷款总额：
					<input class="t-input" name="daikuan_total000" type="text" size="10" maxlength="10" id="my-total" disabled="disabled">
					元</td>
				</tr>
				<tr>
					<td width="25%" align="right">按揭年数：</td>
					<td><select id="years" name="years" style="color:#0877BD;">
					<?php foreach($daikuan['mortgage_year'] as $k=>$vo){?>
						<option value='<?=$k?>' <?php if($k == 20){?>selected<?php }?>><?=$vo?></option>
					<?php }?>
					<!--<option value="1">1年（12期）</option>
					<option value="2">2年（24期）</option>
					<option value="3">3年（36期）</option>
					<option value="4">4年（48期）</option>
					<option value="5">5年（60期）</option>
					<option value="6">6年（72期）</option>
					<option value="7">7年（84期）</option>
					<option value="8">8年（96期）</option>
					<option value="9">9年（108期）</option>
					<option value="10">10年（120期）</option>
					<option value="11">11年（132期）</option>
					<option value="12">12年（144期）</option>
					<option value="13">13年（156期）</option>
					<option value="14">14年（168期）</option>
					<option value="15">15年（180期）</option>
					<option value="16">16年（192期）</option>
					<option value="17">17年（204期）</option>
					<option value="18">18年（216期）</option>
					<option value="19">19年（228期）</option>
					<option selected="" value="20">20年（240期）</option>
					<option value="25">25年（300期）</option>
					<option value="30">30年（360期）</option>-->
					</select></td>
				</tr>
				</tbody>
			</table>
			<table width="100%" class="left_table">
				<tbody>
                    <tr class="daikuan">
					<td width="25%" align="right">贷款利率：</td>
					<td>
						<select id="lilv" name="lilv" style="color:#0877BD;">
							<option value="99">2015年10月24日后利率下浮30%</option>
							<option value="98">2015年10月24日后利率下浮20%</option>
							<option value="97">2015年10月24日后利率下浮15%</option>
							<option value="100">2015年10月24日后利率下浮10%</option>
							<option value="101">2015年10月24日后利率下浮5%</option>
                                                        <?php if('22'==$city_id){ ?>
							<option value="104">2015年10月24日后利率上浮30%</option>
							<option value="103">2015年10月24日后利率上浮25%</option>
                                                        <?php } ?>
							<option value="102">2015年10月24日后利率上浮20%</option>
							<option value="96">2015年10月24日后利率上浮10%</option>
							<option value="95" selected="">2015年10月24日后基准利率</option>
							<option value="94">2015年08月26日后利率下浮30%</option>
							<option value="93">2015年08月26日后利率下浮20%</option>
							<option value="92">2015年08月26日后利率下浮15%</option>
							<option value="91">2015年08月26日后利率上浮10%</option>
							<option value="90">2015年08月26日后基准利率</option>
							<option value="89">2015年06月28日后利率下浮30%</option>
							<option value="88">2015年06月28日后利率下浮20%</option>
							<option value="87">2015年06月28日后利率下浮15%</option>
							<option value="86">2015年06月28日后利率上浮10%</option>
							<option value="85">2015年06月28日后基准利率</option>
							<option value="84">2015年05月11日后利率下浮30%</option>
							<option value="83">2015年05月11日后利率下浮20%</option>
							<option value="82">2015年05月11日后利率下浮15%</option>
							<option value="81">2015年05月11日后利率上浮10%</option>
							<option value="80">2015年05月11日后基准利率</option>
							<option value="79">2015年03月01日后利率下浮30%</option>
							<option value="78">2015年03月01日后利率下浮20%</option>
							<option value="77">2015年03月01日后利率下浮15%</option>
							<option value="76">2015年03月01日后利率上浮10%</option>
							<option value="75">2015年03月01日后基准利率</option>
							<option value="74">2014年11月22日后利率下浮30%</option>
							<option value="73">2014年11月22日后利率下浮20%</option>
							<option value="72">2014年11月22日后利率下浮15%</option>
							<option value="71">2014年11月22日后利率上浮10%</option>
							<option value="70">2014年11月22日后基准利率</option>
							<option value="69">2012年7月6日后利率下浮30%</option>
							<option value="68">2012年7月6日后利率下浮20%</option>
							<option value="67">2012年7月6日后利率下浮15%</option>
							<option value="66">2012年7月6日后利率上浮10%</option>
							<option value="65">2012年7月6日后基准利率</option>
							<option value="64">2012年6月8日后利率下浮30%</option>
							<option value="63">2012年6月8日后利率下浮20%</option>
							<option value="62">2012年6月8日后利率下浮15%</option>
							<option value="61">2012年6月8日后利率上浮10%</option>
							<option value="60">2012年6月8日后基准利率</option>
							<option value="59">2011年7月7日后利率上浮10%</option>
							<option value="58">2011年10月24日后基准利率</option>
							<option value="56">2011年7月7日后利率下浮30%</option>
							<option value="57">2011年7月7日后利率下浮15%</option>
							<option value="55">2011年7月7日后利率上浮10%</option>
							<option value="54">2011年7月7日后基准利率</option>
							<option value="53">2011年4月6日后利率下浮30%</option>
							<option value="52">2011年4月6日后利率下浮15%</option>
							<option value="51">2011年4月6日后利率上浮10%</option>
							<option value="50">2011年4月6日后基准利率</option>
							<option value="49">2011年2月9日后利率下浮30%</option>
							<option value="48">2011年2月9日后利率下浮15%</option>
							<option value="47">2011年2月9日后利率上浮10%</option>
							<option value="46">2011年2月9日后基准利率</option>
							<option value="45">2010年12月26日后利率下浮30%</option>
							<option value="44">2010年12月26日后利率下浮15%</option>
							<option value="43">2010年12月26日后利率上浮10%</option>
							<option value="42">2010年12月26日后基准利率</option>
							<option value="41">2010年10月20日后利率下浮30%</option>
							<option value="39">2010年10月20日后利率下浮15%</option>
							<option value="40">2010年10月20日后利率上浮10%</option>
							<option value="38">2010年10月20日后基准利率</option>
							<option value="37">2008年12月23日后利率上浮10%</option>
							<option value="36">2008年12月23日后利率下浮15%</option>
							<option value="35">2008年12月23日后利率下浮30%</option>
							<option value="34">2008年12月23日后基准利率</option>
							<option value="33">2008年11月27日后利率下浮15%</option>
							<option value="32">08年11月27日后利率下浮30%</option>
							<option value="31">08年11月27日后基准利率</option>
							<option value="30">08年10月30日后利率下浮15%</option>
							<option value="29">08年10月30日后利率下浮30%</option>
							<option value="28">08年10月30日后基准利率</option>
							<option value="27">08年10月27日后利率下浮30%</option>
							<option value="26">08年10月27日后基准利率</option>
							<option value="25">08年10月9日后利率上浮10%</option>
							<option value="24">08年10月9日后利率下浮15%</option>
							<option value="23">08年10月9日后基准利率</option>
							<option value="22">08年9月16日后利率上浮10%</option>
							<option value="21">08年9月16日后利率下浮15%</option>
							<option value="20">08年9月16日后基准利率</option>
							<option value="19">07年12月21日后利率上浮10%</option>
							<option value="18">07年12月21日后利率下浮15%</option>
							<option value="17">07年12月21日后基准利率</option>
							<option value="16">07年9月15日后利率下限</option>
							<option value="15">07年9月15日后基准利率</option>
							<option value="14">07年8月22日后利率下限</option>
							<option value="13">07年8月22日后基准利率</option>
							<option value="12">07年7月21日后利率下限</option>
							<option value="11">07年7月21日后基准利率</option>
							<option value="10">07年5月19日后基准利率</option>
							<option value="9">07年5月19日后利率下限</option>
							<option value="8">07年3月18日后基准利率</option>
							<option value="7">07年3月18日后利率下限</option>
							<option value="6">06年8月19日后利率上限</option>
							<option value="5">06年4月28日后利率上限</option>
							<option value="4">05年3月17日后利率上限</option>
							<option value="3">05年3月17日后利率下限</option>
							<option value="2">05年1月1日-3月17日利率</option>
							<option value="1">05年1月1日前利率</option>
						</select>
					</td>
				</tr>
                <tr class="daikuan">
                    <td width="21%" align="right">手动输入：</td>
                    <td width="79%">
                        <input type="text" value="4.9" onkeypress="myNumberic();" size="3" maxlength="10" id="lilv_input" name="lilv_input">%
                      X
                      <input type="text" value="1" onkeypress="myNumberic();" size="3" maxlength="10" id="lilv_multiple" name="lilv_multiple">倍
                      = <span id="lilv_result">4.90</span> %

                    </td>
                </tr>
                <tr class="hidden gjj">
					<td width="25%" align="right">公积金利率：</td>
					<td>
						<select id="lilv_2" name="lilv_2" style="color:#0877BD;">
							<option value="99">2015年10月24日后利率下浮30%</option>
							<option value="98">2015年10月24日后利率下浮20%</option>
							<option value="97">2015年10月24日后利率下浮15%</option>
							<option value="100">2015年10月24日后利率下浮10%</option>
							<option value="101">2015年10月24日后利率下浮5%</option>
							<option value="102">2015年10月24日后利率上浮20%</option>
							<option value="96">2015年10月24日后利率上浮10%</option>
							<option value="95" selected="">2015年10月24日后基准利率</option>
							<option value="94">2015年08月26日后利率下浮30%</option>
							<option value="93">2015年08月26日后利率下浮20%</option>
							<option value="92">2015年08月26日后利率下浮15%</option>
							<option value="91">2015年08月26日后利率上浮10%</option>
							<option value="90">2015年08月26日后基准利率</option>
							<option value="89">2015年06月28日后利率下浮30%</option>
							<option value="88">2015年06月28日后利率下浮20%</option>
							<option value="87">2015年06月28日后利率下浮15%</option>
							<option value="86">2015年06月28日后利率上浮10%</option>
							<option value="85">2015年06月28日后基准利率</option>
							<option value="84">2015年05月11日后利率下浮30%</option>
							<option value="83">2015年05月11日后利率下浮20%</option>
							<option value="82">2015年05月11日后利率下浮15%</option>
							<option value="81">2015年05月11日后利率上浮10%</option>
							<option value="80">2015年05月11日后基准利率</option>
							<option value="79">2015年03月01日后利率下浮30%</option>
							<option value="78">2015年03月01日后利率下浮20%</option>
							<option value="77">2015年03月01日后利率下浮15%</option>
							<option value="76">2015年03月01日后利率上浮10%</option>
							<option value="75">2015年03月01日后基准利率</option>
							<option value="74">2014年11月22日后利率下浮30%</option>
							<option value="73">2014年11月22日后利率下浮20%</option>
							<option value="72">2014年11月22日后利率下浮15%</option>
							<option value="71">2014年11月22日后利率上浮10%</option>
							<option value="70">2014年11月22日后基准利率</option>
							<option value="69">2012年7月6日后利率下浮30%</option>
							<option value="68">2012年7月6日后利率下浮20%</option>
							<option value="67">2012年7月6日后利率下浮15%</option>
							<option value="66">2012年7月6日后利率上浮10%</option>
							<option value="65">2012年7月6日后基准利率</option>
							<option value="64">2012年6月8日后利率下浮30%</option>
							<option value="63">2012年6月8日后利率下浮20%</option>
							<option value="62">2012年6月8日后利率下浮15%</option>
							<option value="61">2012年6月8日后利率上浮10%</option>
							<option value="60">2012年6月8日后基准利率</option>
							<option value="59">2011年7月7日后利率上浮10%</option>
							<option value="58">2011年10月24日后基准利率</option>
							<option value="56">2011年7月7日后利率下浮30%</option>
							<option value="57">2011年7月7日后利率下浮15%</option>
							<option value="55">2011年7月7日后利率上浮10%</option>
							<option value="54">2011年7月7日后基准利率</option>
							<option value="53">2011年4月6日后利率下浮30%</option>
							<option value="52">2011年4月6日后利率下浮15%</option>
							<option value="51">2011年4月6日后利率上浮10%</option>
							<option value="50">2011年4月6日后基准利率</option>
							<option value="49">2011年2月9日后利率下浮30%</option>
							<option value="48">2011年2月9日后利率下浮15%</option>
							<option value="47">2011年2月9日后利率上浮10%</option>
							<option value="46">2011年2月9日后基准利率</option>
							<option value="45">2010年12月26日后利率下浮30%</option>
							<option value="44">2010年12月26日后利率下浮15%</option>
							<option value="43">2010年12月26日后利率上浮10%</option>
							<option value="42">2010年12月26日后基准利率</option>
							<option value="41">2010年10月20日后利率下浮30%</option>
							<option value="39">2010年10月20日后利率下浮15%</option>
							<option value="40">2010年10月20日后利率上浮10%</option>
							<option value="38">2010年10月20日后基准利率</option>
							<option value="37">2008年12月23日后利率上浮10%</option>
							<option value="36">2008年12月23日后利率下浮15%</option>
							<option value="35">2008年12月23日后利率下浮30%</option>
							<option value="34">2008年12月23日后基准利率</option>
							<option value="33">2008年11月27日后利率下浮15%</option>
							<option value="32">08年11月27日后利率下浮30%</option>
							<option value="31">08年11月27日后基准利率</option>
							<option value="30">08年10月30日后利率下浮15%</option>
							<option value="29">08年10月30日后利率下浮30%</option>
							<option value="28">08年10月30日后基准利率</option>
							<option value="27">08年10月27日后利率下浮30%</option>
							<option value="26">08年10月27日后基准利率</option>
							<option value="25">08年10月9日后利率上浮10%</option>
							<option value="24">08年10月9日后利率下浮15%</option>
							<option value="23">08年10月9日后基准利率</option>
							<option value="22">08年9月16日后利率上浮10%</option>
							<option value="21">08年9月16日后利率下浮15%</option>
							<option value="20">08年9月16日后基准利率</option>
							<option value="19">07年12月21日后利率上浮10%</option>
							<option value="18">07年12月21日后利率下浮15%</option>
							<option value="17">07年12月21日后基准利率</option>
							<option value="16">07年9月15日后利率下限</option>
							<option value="15">07年9月15日后基准利率</option>
							<option value="14">07年8月22日后利率下限</option>
							<option value="13">07年8月22日后基准利率</option>
							<option value="12">07年7月21日后利率下限</option>
							<option value="11">07年7月21日后基准利率</option>
							<option value="10">07年5月19日后基准利率</option>
							<option value="9">07年5月19日后利率下限</option>
							<option value="8">07年3月18日后基准利率</option>
							<option value="7">07年3月18日后利率下限</option>
							<option value="6">06年8月19日后利率上限</option>
							<option value="5">06年4月28日后利率上限</option>
							<option value="4">05年3月17日后利率上限</option>
							<option value="3">05年3月17日后利率下限</option>
							<option value="2">05年1月1日-3月17日利率</option>
							<option value="1">05年1月1日前利率</option>
						</select>
					</td>
				</tr>
                <tr class="hide gjj">
                    <td width="21%" align="right">手动输入：</td>
                    <td width="79%">
                      <input type="text" value="4.9" onkeypress="myNumberic();" size="3" maxlength="10" id="lilv_input_2">%
                      X
                      <input type="text" value="1" onkeypress="myNumberic();" size="3" maxlength="10" id="lilv_multiple_2">倍
                      = <span id="lilv_result_2">4.90</span> %
                    </td>
                </tr>
				<tr>
					<td>&nbsp;</td>
					<td style="padding-top:20px;"><a class="btn-lv btn-left" href="javascript:void(0);" onclick="ext_total(document.getElementById('col-form'));"><span>开始计算</span></a><a class="btn-hui1" href="javascript:void(0);" id="my-reset-daikuan" onclick="formReset(document.getElementById('col-form'))"><span>重填</span></a></td>
				</tr></tbody>
			</table>
        </div>

		<div class="fr">
            <h3>查看结果：</h3>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="right_table" id="right-table-1">
				<tbody><tr>
					<td width="28%" align="right">房款总额：</td>
					<td><input class="t-input2" name="fangkuan_total1" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">贷款总额：</td>
					<td><input class="t-input2" name="daikuan_total1" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">还款总额：</td>
					<td><input class="t-input2" name="all_total1" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">支付利息款：</td>
					<td><input class="t-input2" name="accrual1" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">首期付款：</td>
					<td><input class="t-input2" name="money_first1" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">贷款月数：</td>
					<td><input class="t-input2" name="month1" type="text" readonly="readonly">
					月</td>
				</tr>
				<tr>
					<td align="right">月均还款：</td>
					<td><input class="t-input2" name="month_money1" type="text" readonly="readonly">
					元</td>
				</tr>
			</tbody></table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="right_table" id="right-table-2" style="display: none;">
				<tbody><tr>
					<td width="28%" align="right">房款总额：</td>
					<td><input class="t-input2" name="fangkuan_total2" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">贷款总额：</td>
					<td><input class="t-input2" name="daikuan_total2" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">还款总额：</td>
					<td><input class="t-input2" name="all_total2" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">支付利息款：</td>
					<td><input class="t-input2" name="accrual2" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">首期付款：</td>
					<td><input class="t-input2" name="money_first2" type="text" readonly="readonly">
					元</td>
				</tr>
				<tr>
					<td align="right">贷款月数：</td>
					<td><input class="t-input2" name="month2" type="text" readonly="readonly">
					月</td>
				</tr>
				<tr>
					<td valign="top" align="right" style="padding-top:10px;">月均金额：</td>
					<td style="padding-top:4px;"><textarea name="month_money2" cols="" rows="" class="dk_textarea" readonly="readonly"></textarea></td>
				</tr>
			</tbody></table>
		</div>
		<p class="tips1">* 以上查询结果仅供参考，以最终受理结果为准 *</p>
	</form></div>
</div>


















<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
