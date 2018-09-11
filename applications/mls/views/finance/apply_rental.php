<script>
    window.parent.addNavClass('24');
</script>
<div id="js_tab_box" class="tab_box">
    <a class="link link_on" href="/finance/index"><span class="iconfont"></span>金融项目</a><a class="link" href="/finance/my_customer"><span class="iconfont"></span>我的客户</a>
</div>
<div class="zws_scroll">
<div class="zws_jr_main">
	<div class="wrapper jr dyds" style="margin: 0 auto;margin-top: 20px;">
		<div class="new_dyd" style="padding-right:2%">

		<div class="zws_repair">
			<div class="jr_ad zws_dy_margin">
				<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd/zws_zj_banner.jpg" alt=""/>
			</div>
			<div class="ydy_detail zws_new_dyW">
				<div class="dyd_block">
					<h1>产品说明</h1>
					<span class="zws_border_dott"><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd/zws_zj_ys_11.jpg" alt=""/></span>
					<div class="zws_jr_main_float"></div>
					<p>贷款额度&nbsp;:&nbsp;住宅租金贷￥4000，公寓租金贷￥6000，家具租金贷￥9000</p>
					<p>贷款期限&nbsp;:&nbsp;14天&sim;1年任意选</p>
					<p>贷款利率&nbsp;:&nbsp;低至4.5%</p>
					<p>还款方式&nbsp;:&nbsp;等额本息或先息后本</p>
					<p>注意事项&nbsp;:&nbsp;按借贷金额6%收取平台服务费</p>
				</div>
				<div class="dyd_block dyd_bbk">
					<h1>申请条件</h1>
					<p>1.申请人至少要有一张&ge;5000元的信用卡&nbsp;;</p>
					<p>2.申请人名下有房产按揭贷款的（商业或者公积金）;</p>
					<p>3.申请人工资卡是浦发银行的，或者在浦发银行有理财产品等优质客户。</p>
					<p>(&nbsp;以上三者符合一条即可&nbsp;)</p>
				</div>
				<div class="dyd_block dyd_bbk dyd_bbk2">
					<h1>申请流程</h1>
					<p>1.经纪人在<?=$title?>金融服务频道点击"租金贷";</p>
					<p>2.经纪人填写租客借款信息、提交订单申请（住宅租金贷￥4000、公寓租金贷￥6000、家具租金贷￥9000）;</p>
					<p>3.订单提交成功，租客即可下载"浦发手机银行"APP并开通电子银行账户&nbsp;;</p>
					<p>4.租客在APP"弘昊点贷"中填写申请信息、提交贷款审批&nbsp;;</p>
					<p>5.审批通过后，租客在APP中完成贷款签约即可放款&nbsp;;</p>
					<p>6.贷款款项扣除6%的平台服务费后直接打给租客。</p>
				</div>

			</div>
		</div>

		<!--右侧-->
		<div class="zws_dy_right">
			<div class="ajd_r_f right_zws_width">
				<span class="zws_product_title2">申请租金贷</span>
				<div class="ajd_form_right" style="padding-bottom:23px">
				    <form action="" method="post" id="form-horizontal">
				    <input type='hidden' name='submit_flag' value='add' />
					<div class="ajd_vvb center_zws">
						<div class="afm quname clearfix">
							<label for="" class="kkk">申请人姓名&nbsp;:</label>
							<div class="input_main">
								<input class="vvinput" type="text" name="tenant_name" id="tenant_name" />
								<span class="ermsg">请填写客户姓名</span>
							</div>
						</div>
						<div class=" ajd_sex clearfix" style="margin-bottom:17px;margin-top:0">
							<span class="kkk">性别&nbsp;:</span>
							<div class="bv">
								<span class="bot checked"><input type="radio" name="tenant_sex" value="2" checked/></span>
							</div>
							<label for="" class="ajd_man">男</label>
							<div class="bv">
								<span class="bot"><input type="radio" name="tenant_sex" value="2"/></span>
							</div>
							<label for="">女</label>
						</div>
						<div class="afm ajd_phone clearfix">
							<label for="" class="kkk">手机号码&nbsp;:</label>
							<div class="input_main">
								<input  class="vvinput" type="text" name="tenant_phone" id="tenant_phone" />
								<span class="ermsg"></span>
							</div>
						</div>
						<div class="ajd_mm clearfix" style="margin-bottom:17px;margin-top:0">
							<span class="kkk">借贷金额&nbsp;:</span>
							<div class="jj">
								<div class="nnn mnm clearfix">
									<div class="bv">
										<span class="bot checked"><input type="radio" name="tenant_price" value="4000" checked/></span>
									</div>
									<label for="" class="ajd_man">住宅租金贷￥4000</label>
								</div>
								<div class="nnn mnm clearfix">
									<div class="bv">
										<span class="bot "><input type="radio" name="tenant_price" value="6000" /></span>
									</div>
									<label for="" >公寓租金贷￥6000</label>
								</div>
								<div class="nnn clearfix">
									<div class="bv">
										<span class="bot "><input type="radio" name="tenant_price" value="9000" /></span>
									</div>
									<label for="" >家居租金贷￥9000</label>
								</div>
							</div>

						</div>
						<div class="afm ajd_names clearfix">
							<label for="" class="kkk">身份证号&nbsp;:</label>
							<div class="input_main">
								<input  class="vvinput" type="text" name="tenant_cart" id="tenant_cart" />
								<span class="ermsg"></span>
							</div>
						</div>
						<div class="afm ajd_names clearfix">
							<label for="" class="kkk">银行卡号&nbsp;:</label>
							<div class="input_main">
								<input  class="vvinput band_card" type="text" name="tenant_bank_id" id="tenant_bank_id" value="" />
								<span class="ermsg"></span>
							</div>
						</div>
						<div class="afm ajd_band clearfix">
							<label for="" class="kkk">银行&nbsp;:</label>
							<div class="input_main">
								<select name="tenant_bank" id="tenant_bank">
    								<option value="中国工商银行">中国工商银行</option>
    								<option value="招商银行">招商银行</option>
    								<option value="中国光大银行">中国光大银行</option>
    								<option value="中信银行">中信银行</option>
    								<option value="浦发银行">浦发银行</option>
    								<option value="广发银行">广发银行</option>
    								<option value="华夏银行">华夏银行</option>
    								<option value="中国建设银行">中国建设银行</option>
    								<option value="交通银行">交通银行</option>
    								<option value="中国银行">中国银行</option>
    								<option value="中国民生银行">中国民生银行</option>
    								<option value="兴业银行">兴业银行</option>
    								<option value="平安银行">平安银行</option>
    								<option value="中国农业银行">中国农业银行</option>
    								<option value="中国邮政储蓄银行">中国邮政储蓄银行</option>
								</select>
							</div>
						</div>
						<div class="afm ajd_money ajd_mam clearfix">
							<label for="" class="kkk">验证码&nbsp;:</label>
							<div class="input_main">
								<input  class="vvinput" type="text" name="validcode" id="validcode" />
								<span class="ermsg"></span>
							</div>
							<span id="getVerify" class="bt_ma">获取验证码</span>
                            <span id='reverify' class="dydk_apply_yzm"  style="display:none" disabled></span>
						</div>

						<button class="afm_bt" type="submit">提交资料</button>
					</div>
                    </form>
				</div>
			</div>
			<!--电话号码-->
			<dl class="zws_product_tel">
				<dd><img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dyd/zws_tel_07.png" alt=""/></dd>
				<dt>
					<b>详情咨询请拨打客服热线</b>
                <p>xxx-xxx-xxx转<?= $tel ?></p>
				</dt>
			</dl>
		</div>
    </div>
</div>
</div>
</div>
<div class="pop_box_g pop_see_inform pop_no_q_up" style="display:none;" id="js_pop">
	<div class="hd">
		<div class="title">提示</div>
		<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
	</div>
	 <div class="mod">
		<div class="inform_inner">
		<div class="up_inner">
				<table class="del_table_pop">
					<tr>
						<td width="25%" align="right" style="padding-right:10px;">
				<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></td>
						<td>
				<p class="left" style="font-size:14px;color:#666;" id="js_prompt"></p>
						</td>
					</tr>
				</table>
				<button class="btn JS_Close" type="button">确定</button>
			</div>
		 </div>
	</div>
</div>
<style>
label.error{color:red;}
</style>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/openWin.js" type="text/javascript"></script>
<script>
    window.onload = function(){

        setInterval(function(){

            var winHeight = $(window).height()-43;
            $('.zws_scroll').css('height',winHeight);
        },30);
        //性别
        $('.ajd_sex input').click(function(){

            $(this).parents('.bv').siblings().find('.bot').removeClass('checked');
            $(this).parent('.bot').addClass('checked');
        })

        //金额
        $('.ajd_mm input').click(function(){

            $(this).parents('.nnn').siblings().find('.bot').removeClass('checked');
            $(this).parent('.bot').addClass('checked');
        })

        $('.band_card').focus(function(){

            if( $(this).val() == '请输入储蓄卡号'){
                $(this).css('color','#000');
                $(this).val('');
            }
        });
        $('.band_card').blur(function(){

            if($.trim($(this).val()) == ''){
                $(this).css('color','#aaa');
                $(this).val('请输入储蓄卡号');
            }
        });
    }

	$(document).ready(function(){
		//获取验证码
		$('#getVerify').live('click',function(){
			var phone = $("#tenant_phone").val();
			var phone_reg= /^(1[0-9]{10})$/;
			if(phone == ''){
				$('#js_prompt').html('请输入手机号码');
				openWin('js_pop');
				return false;
			}

			if(phone_reg.test(phone)){
				//判断手机号码是否注册过 当点击获取验证码时
				$.ajax({
					type : 'post',
					url  : '/finance/ajaxValid/',
					data : {
						name : "order",
						phone : phone
					},
					dataType :'json',
					success : function(data){
						if(data.result != 'ok'){
							showError(data.msg);
							$('#js_prompt').html(data.msg);
							openWin('js_pop');
						}else if(data.result == 'ok'){
							var num = 60;
							$('#getVerify').hide();
							$('#reverify').html(num + 's后重新获取');
							//alert(123);return false;
							$('#reverify').show();
							var _interval   =   setInterval(function(){
								num--;
								var html = num + 's后重新获取';
								$('#reverify').html(html);
								if(num <= 0){
									clearInterval(_interval);
									$('#reverify').hide();
									$('#getVerify').show();
								}
							},1000);
						}
					}
				});
			}else{
				$('#js_prompt').html('请输入正确的手机号码');
				openWin('js_pop');
				return false;
			}
		});

        $("#form-horizontal").validate({
            focusInvalid: true, //当为false时，验证无效时，没有焦点响应
            onkeyup: false,
            submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form
    			$.ajax({
    				type: 'POST',
    				url: '/finance/apply_rental/',
    				data:$(form).serialize(),
    				dataType: 'json',
    				success: function(data){
    				    $('#js_prompt').html(data.msg);
    				    openWin('js_pop',function(){
    				        if(data.result == 'ok'){
    				            $('#js_pop').find('.btn').bind('click',function(){
    				                location.href = '/finance/my_customer';
    				            });
    				        }
    				    });
    				}
    			});
                return false;
            },
            rules: {
              tenant_name: "required",
              tenant_phone: "required",
              tenant_cart: "required",
              tenant_bank_id: "required",
              validcode: "required"
            },
            messages: {
              tenant_name: "请输入您的名字",
              tenant_phone: "请输入手机号码",
              tenant_cart: "请输入身份证号",
              tenant_bank_id: "请输入银行卡号",
              validcode: "请输入验证码"
            }
        });
	});
</script>
