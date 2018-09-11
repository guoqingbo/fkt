<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <title>租金分期申请</title>
        <link href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/rentstage_ios.css?t=1" rel="stylesheet" type="text/css" />
        <link href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zwsPop.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/frozen.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
        <script type="text/javascript" src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zwsPop.js"></script>

    </head>

    <body ontouchstart class="index">
        <section  class="zwe_rentstage_apply">

                <!--banner-->
                <figure class="zwe_rentstage_apply_banner"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/rentstage/zws1_02.jpg" alt="租金分期贷款" /></figure>
                <!--信息填写-->
                <form action="" method="post" id="apply_form">
                    <div class="zwe_rentstage_apply_info">
                        <span class="zwe_rentstage_apply_info_title"  id="demo-2">申请人信息</span>
                        <dl class="dydk_apply_inf">
                            <dd id="clumn">
                                <p id="clumn4">姓名：</p>
                                <p id="clumn2"><input value="" name="tenant_name" id="tenant_name" class="dydk_apply_inputInput" type="text" placeholder="请输入姓名" /></p>
								<p id="clumn3"><b class="sex_border sexCur">先生</b><b class="sex_border last_child">女士</b></p>
                            </dd>
                            <dd id="clumn">
                                <p id="clumn4">手机号：</p>
                                <p id="clumn2"><input value="" name="tenant_phone" id="tenant_phone" class="dydk_apply_inputTel" type="tel" placeholder="请输入手机号" /></p>
                            </dd>
							<dd id="clumn" class="border_none">
                                <p id="clumn4">借款金额：</p>
                                <p id="clumn2">
									<b class="apply_money_b"><input value="4000" name="tenant_price" type="radio" class="apply_money_style" checked />&nbsp;&nbsp;住宅租金贷 ¥4000</b>
									<b class="apply_money_b"><input value="6000" name="tenant_price" type="radio" class="apply_money_style" />&nbsp;&nbsp;住宅租金贷 ¥6000</b>
									<b class="apply_money_b"><input value="9000" name="tenant_price" type="radio" class="apply_money_style" />&nbsp;&nbsp;住宅租金贷 ¥9000</b>
								</p>
                            </dd>
							<dd class="remindColorT">提示：每次只可选择一个申请，申请成功后可再次申请</dd>
							<dd id="clumn">
                                <p id="clumn4">身份证</p>
                                <p id="clumn2"><input value="" name="tenant_cart" id="tenant_cart" class="dydk_apply_inputInput" type="text" placeholder="请输入身份证" /></p>
                            </dd>
							<dd id="clumn">
                                <p id="clumn4">银行卡号：</p>
                                <p id="clumn2"><input value="" name="tenant_bank_id" id="tenant_bank_id" class="dydk_apply_inputTel" type="num" placeholder="请输入银行卡号" /></p>
                            </dd>
							<dd id="clumn" class="border_none">
                                <p id="clumn4">银行：</p>
                                <p id="clumn2">
									<select name="tenant_bank" id="tenant_bank" class="apply_bank_select">
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
								</p>
                            </dd>
							<dd class="remindColorT">提示：申请成功后，款项将转入该银行卡账户，户名与申请人须一致</dd>
                            <dt id="clumn">
                                <p id="clumn4">验证码</p>
                                <p id="clumn2"><input class="dydk_apply_inputInput" id="validcode" name="validcode" type="text" placeholder="" value="" /></p>
                                <p id="getVerify" class="dydk_apply_yzm colorStyleR">获取验证码</p>
                                <p id='reverify' class="dydk_apply_yzm"  style="display:none" disabled></p>
                            </dt>
                        </dl>
        				<input id="tenant_sex" name="tenant_sex" type="hidden" value="1" />
                        <input type="submit" class="apply_bank_submit" value="提交" />
                    </div>
                </form>

        </section>

        <footer class="rentstage_footer"></footer>

        <script type="text/javascript">
            $(function () {
				//性别切换
				$(".sex_border").on("click",function(){
					$(".sex_border").removeClass("sexCur");
					$(this).addClass("sexCur");

                    if($(this).hasClass('last_child')){
                        $("input[name='tenant_sex']").val('2');
                    }else{
                        $("input[name='tenant_sex']").val('1');
                    }
				})

    			//获取验证码
                $('#getVerify').on('click',function(){
                    var phone = $("#tenant_phone").val();
    				var phone_reg= /^(1[0-9]{10})$/;
                    if(phone == ''){showError('号码为空');return false;}

                    if(phone_reg.test(phone)){
                        //判断手机号码是否注册过 当点击获取验证码时
                        $.ajax({
                            type : 'post',
                            url  : '/wap/finance/ajaxValid/',
                            data : {
                                name : "order",
                                phone : phone
                            },
                            dataType :'json',
                            success : function(data){
                                if(data.result != '1'){
                                    showError(data.msg);
                                }else if(data.result == '1'){
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
                        showError('请输入正确的手机号码');
                        return false;
                    }
                });
    			$("#apply_form").on("submit", function () {
    				var phone_reg= /^(1[0-9]{10})$/;
    	            var cart_reg= /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/;

    				var tenant_name = $("#tenant_name").val();
                    var tenant_sex = $('#tenant_sex').val();
    				var tenant_phone = $("#tenant_phone").val();
    				var tenant_price = $("input[name='tenant_price']:checked").val();
                    var tenant_cart = $('#tenant_cart').val();
                    var tenant_bank_id = $('#tenant_bank_id').val();
                    var tenant_bank = $('#tenant_bank').val();
    				var validcode = $("#validcode").val();

    				if(!tenant_name){
    					showError("申请人姓名不能为空");
    					return false;
    				}
    				if(!tenant_phone){
    					showError("申请人电话不能为空");
    					return false;
    				}else if(!phone_reg.test(tenant_phone)){
    					showError('请输入正确的申请人电话');
                        return false;
                    }
    				if(!tenant_cart){
    					showError("身份证号不能为空");
    					return false;
    				}else if(!cart_reg.test(tenant_cart)){
    					showError('请输入正确的身份证');
                        return false;
                    }
    				if(!tenant_bank_id){
    					showError("银行卡号不能为空");
    					return false;
    				}

    				if(!validcode){
    					showError("验证码不能为空");
    					return false;
    				}

    				$.ajax({
    					type : 'post',
    					url  : '/wap/rental/apply/?action=apply_add',
    					data : {
    						tenant_name : tenant_name,
    						tenant_phone : tenant_phone,
    						tenant_cart : tenant_cart,
    						tenant_bank_id : tenant_bank_id,
    						validcode : validcode,
    						tenant_bank : tenant_bank,
    						tenant_price : tenant_price,
    						tenant_sex : tenant_sex
    					},
    					dataType :'json',
    					success : function(data){
    						if(data.result == '1'){
    							window.location.href="/wap/rental/customer/";
    						}else{
    							showError(data.msg);
                                return false;
    						}
    					}
    				});
                    return false;
    			});

                //弹框
                //showSuccess("成功");
                //showError("失败");
            })
        </script>
    </body>
</html>
