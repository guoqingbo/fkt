<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
        <meta content="yes" name="apple-mobile-web-app-capable" />
        <meta content="black" name="apple-mobile-web-app-status-bar-style" />
        <meta content="telephone=no" name="format-detection" />
        <meta content="email=no" name="format-detection" />
        <title>房屋资料</title>
        <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zjd.css" type="text/css"/>
        <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zwsPop.css" type="text/css"/>
    </head>
    <body>
        <div class="wrapper">
            <form id="form">
            <input type="hidden" name="submit_flag" value="1" />
            <section class="information">
                <h1>房东信息</h1>
                <div class="forms">
                    <div class="mes_div">
                        <label for="">房东姓名</label><input class="lines" type="text" placeholder="请输入姓名" name="house_name" id="house_name" />
                    </div>
                    <div class="mes_div">
                        <label for="">手机号码</label><input class="lines" type="tel" placeholder="请输入手机号码" name="house_phone" id="house_phone" />
                    </div>
                    <div class="mes_div">
                        <label for="">身份证</label><input class="lines" type="tel" placeholder="请输入身份证号" name="house_cart" id="house_cart" />
                    </div>
                    <div class="mes_div mes_div_exp">
                        <label for="">银行卡</label><input class="lines" type="tel" placeholder="请输入银行卡号" name="house_bank" id="house_bank" />
                    </div>
                </div>
            </section>
            <section class="up_information">
                <h1>上传资料</h1>
                <div class="up_main">
                    <section class="up_li">
                        <p>身份证:&nbsp;&nbsp;<span>最多上传1张</span></p>
                        <div class="load_show clearfix">
                            <iframe src="/wap/rental/upload?inputid=house_cart_photo&limit=1" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                        </div>
                    </section>
                    <section class="up_li">
                        <p>房产证:&nbsp;&nbsp;<span>最多上传1张</span></p>
                         <div class="load_show clearfix">
                            <iframe src="/wap/rental/upload?inputid=house_property_photo&limit=1" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                        </div>
                    </section>
                     <section class="up_li">
                        <p>租赁合同:&nbsp;&nbsp;<span>最多上传3张</span></p>
                        <div class="load_show clearfix">
                            <iframe src="/wap/rental/upload?inputid=house_contract_photo&limit=3" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
                        </div>
                    </section>
                </div>
            </section>
            </form>
            <section class="bt_main">
                <button class="bt_sub">提交</button>
            </section>
        </div>
        <script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
        <script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zwsPop.js"></script>
        <script>
            function hidden(id,value){
                var input = '<input type="hidden" class="'+id+'" name="'+id+'[]" value="'+value+'" />';
                $('#form').append($(input));
            }
        </script>
        <script>
            ;(function (doc, win) {
                var docEl = doc.documentElement,
                        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                        recalc = function () {
                            var clientWidth = docEl.clientWidth;
                            if (!clientWidth) return;
                            docEl.style.fontSize = 20 * (clientWidth / 320) + 'px';
                        };

                if (!doc.addEventListener) return;
                win.addEventListener(resizeEvt, recalc, false);
                doc.addEventListener('DOMContentLoaded', recalc, false);
            })(document, window);


            //验证表单
            var phone_reg= /^(1[0-9]{10})$/;
            var cart_reg= /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/;
            var bank_reg= /^\d{16,19}$/;
            $('.bt_sub').on('click',function(){
                var house_name = $('#house_name').val();
                var house_phone = $('#house_phone').val();
                var house_cart = $('#house_cart').val();
                var house_bank = $('#house_bank').val();
                var house_cart_photo = $(".house_cart_photo");
                var house_property_photo = $(".house_property_photo");
                var house_contract_photo = $(".house_contract_photo");

                if(!house_name){
                    showError("请输入房东姓名");
                    return false;
                }

                if(!phone_reg.test(house_phone)){
                    showError('请输入正确的手机号');
                    return false;
                }

                if(!cart_reg.test(house_cart)){
                    showError('请输入正确的身份证');
                    return false;
                }

                if(!bank_reg.test(house_bank)){
                    showError('请输入正确的银行卡');
                    return false;
                }

                if(!house_cart_photo || house_cart_photo.length == 0){
                    showError('请上传身份证');
                    return false;
                }

                if(!house_property_photo || house_property_photo.length == 0){
                    showError('请上传房产证');
                    return false;
                }

                if(!house_contract_photo || house_contract_photo.length == 0){
                    showError('请上传租赁合同');
                    return false;
                }
            	$.ajax({
            		type : 'post',
            		url  : '/wap/rental/house/<?=$id?>',
            		data : $("#form").serialize(),
            		dataType :'json',
            		success : function(data){
            			if(data.result){
            				window.location.href="/wap/rental/customer/";
            			}else{
            				showError(data.msg);
            			}
            		}
            	});
            });
        </script>
    </body>
</html>
