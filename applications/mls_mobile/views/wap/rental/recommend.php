<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>推荐客户</title>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zjd.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zwsPop.css" type="text/css"/>
</head>
<body>
<div class="wrapper">
    <form action="" method="post" enctype="multipart/form-data" id="recommend">
    <input type="hidden" name="submit_flag" value="1" />
    <section class="information recommend">
        <h1>推荐客户</h1>
        <div class="forms">
            <div class="mes_div mes_div_epx">
                <label for="">租客姓名</label><input class="lines" type="text" placeholder="请输入租客姓名" id="tenant_name" name="tenant_name" />
                <div class="bt_sex clearfix">
                    <span class="bt_man choose">先生</span>
                    <span class="bt_women">女士</span>
                </div>
            </div>
            <div class="mes_div mes_div_epx">
                <label for="">手机号码</label><input class="lines" type="tel" placeholder="请输入手机号码" id="tenant_phone" name="tenant_phone" maxlength = '11'/>
                <!--<span class="phone_msg cc"></span>-->
            </div>
            <div class="mes_div mes_div_exp mes_div_epx">
                <label for="">借款金额</label><input class="lines" type="tel" placeholder="请输入借款金额" id="tenant_price" name="tenant_price" />
                <span class="doll_bar">元</span>
            </div>
        </div>
    </section>
    <section class="procuct_ma">
        <div class="ma_kuang cc">
            <!--<img src="images/show.jpg" alt=""/>-->
        </div>
        <span class="bt_procuct cc"></span>
    </section>
    <input type="hidden" name="tenant_sex" value="1" />
    </form>
</div>

<script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zepto.min.js"></script>
<script src="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/js/zwsPop.js"></script>
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

    //choose_sex
    $('.bt_sex span').on('click',function(){
        if($(this).hasClass('bt_man')) $("input[name='tenant_sex']").val('1');
        if($(this).hasClass('bt_women')) $("input[name='tenant_sex']").val('2');
        $(this).addClass('choose').siblings().removeClass('choose');
    });

    //product_photo
    var form = $('#recommend');
    var phone_reg= /^(1[0-9]{10})$/;
    var price_reg = /^[0-9]+.[0-9]+|[0-9]+$/

    $('.bt_procuct').on('click',function(){
        if(!$('#tenant_name').val()){
            showError('请输入租客姓名');
            return false;
        }
        if(!$('#tenant_phone').val()){
            showError('请输入手机号码');
            return false;
        }
        if(!$('#tenant_price').val()){
            showError('请输入借款金额');
            return false;
        }
        if(!phone_reg.test($('#tenant_phone').val())){
            showError('请输入正确的手机号码');
            return false;
        }
        if(!price_reg.test($('#tenant_price').val())){
            showError('请输入正确的金额');
            return false;
        }
		$.ajax({
			type: 'post',
			url: '/wap/rental/recommend/',
			data:form.serialize(),
			dataType: 'json',
			success: function(data){
				if(data['result'] == 'ok'){
				    $('.ma_kuang').html('');
                    var imgs = $('<img src="" alt="" />');
                    var imgSrc= data.data.img;
                    imgs.attr('src',imgSrc)
                    $('.ma_kuang').append(imgs);
				}else if(data['result'] == 'no'){
                    showError(data.msg);
				}
			}
		});
    });
</script>
</body>
</html>
