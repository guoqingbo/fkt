<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="format-detection" content="telephone=no">
        <title>贷款提交申请</title>
        <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=kft_jr/css/frozen.css,kft_jr/css/mortgage.css" rel="stylesheet" type="text/css">
        <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/kft_jr/js/zws_rem.js"></script>

    </head>

    <body ontouchstart class="index">
        <div class="body_apply_bg sz_jr_bg">
            <div class="dydk_index">
                <header class="dydk_index_head"><a href="#"><img src="<?php echo MLS_SOURCE_URL;?>/mls/kft_jr/images/<?=$logo?>"/></a></header>
                <!--提交申请-->
                <div class="dydk_apply_tel">
                    <form id="apply">
                        <dl class="dydk_apply_inf zws_fsm_style">
                        	<dd id="clumn">
                                <p id="clumn1" class="sz_jr_W">客户姓名</p>
                                <p id="clumn2"><input value="" name="borrower" class="dydk_apply_inputInput" type="text" placeholder="请输入客户姓名" /></p>
                            </dd>
                            <dd id="clumn">
                                <p id="clumn1" class="sz_jr_W">客户手机</p>
                                <p id="clumn2"><input value="" name="phone" class="dydk_apply_inputTel" type="tel" placeholder="请输入客户手机" maxlength="12" /></p>
                            </dd>

                            <dd id="clumn">
                                <p id="clumn1" class="sz_jr_W">所在城市</p>
                                <p id="clumn2"><input value="<?=$cityname?>" class="dydk_apply_inputTel" type="tel" disabled="true" /></p>
                            </dd>

                        </dl>
                        <input type="hidden" name="type" value="<?=$type?>" />
                        <input type="hidden" name="submit_flg" value="1" />
                        <input type="submit" class="dydk_apply_submit Onhover" value="提交" />
                    </form>
                </div>
                <span class="dydk_apply_zx">咨询热线：<?=$tel400?></span>
            </div>

        </div>
        <!--弹框-->
        <div class="app_Pop" style="display: none;">
        	<div class="popBg"></div>
    	    <!--提交成功-->
        	<div class="inf_sucess">
        		<b class="suce_icon"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/app_jr/fail.png" alt="提交成功"></b>
        		<strong>提交成功</strong>
        	</div>
        	<!--提交失败-->
        	<div class="inf_sucess">
        		<b class="suce_icon"><img src="<?php echo MLS_SOURCE_URL;?>/finance/wap/images/app_jr/sucess.png" alt="提交失败"></b>
        		<strong>提交失败</strong>
        	</div>
        </div>
        <script type="text/javascript" src="<?php echo MLS_SOURCE_URL;?>/min/?f=finance/wap/js/zepto.min.js,fang100/wap/js/frozen.js"></script>
        <script type="text/javascript">
            $(function () {
                //弹框信息
                function popdiog(num){
                	switch(num){
                		//成功
                		case 1:{
                		    $(".app_Pop").show();
                            $(".inf_sucess").eq(0).show();
                            $(".inf_sucess").eq(1).hide();
                			window.setTimeout(function(){
                				$(".app_Pop").hide();
                                $('#apply').find('input[name="borrower"]').val('');
                                $('#apply').find('input[name="phone"]').val('');

                			},3000)
                		}
                        break;

                		//失败
                		case 2:{
          		            $(".app_Pop").show();
                            $(".inf_sucess").eq(1).show();
                            $(".inf_sucess").eq(0).hide();
                			window.setTimeout(function(){
                				$(".app_Pop").hide();
                                $('#apply').find('input[name="borrower"]').val('');
                                $('#apply').find('input[name="phone"]').val('');
                			},3000)
                		}
                        break
                	}
                }

                var form = $('#apply');
                var borrower = form.find('input[name="borrower"]');
                var phone = form.find('input[name="phone"]');
                form.on('submit',function(){
                    if('' == borrower.val()){
                        return false;
                    }
                    if(11 != phone.val().length){
                        phone.val('');
                        return false;
                    }
                    $.ajax({
                        type : 'get',
                        url  : '/wap/finance/apply_business',
                        data : form.serialize(),
                        dataType :'json',
                        success : function(data){
                            if(data.status == 'success'){
                                popdiog(1);
                            }else{
                                popdiog(2);
                            }
                        }
                    });
                    return false;
                });


            })
        </script>
    </body>

</html>
