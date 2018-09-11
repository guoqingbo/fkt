<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <meta content="email=no" name="format-detection" />
    <title>申请按揭贷</title>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/ajd.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo MLS_MOBILE_URL;?>/source/finance/wap/css/zwsPop.css" type="text/css"/>
</head>
<body>
<div class="wrapper  apply_dyd">
    <section class="information recommend">
        <h1>客户信息</h1>
        <div class="forms">
            <div class="mes_div mes_div_epx">
                <label for="">客户姓名</label><input class="lines" type="text" placeholder="请输入客户姓名" name="borrower" id="borrower"/>
                <div class="bt_sex clearfix">
                    <span class="bt_man choose" rel="1">先生</span>
                    <span class="bt_women" rel="2">女士</span>
                </div>
            </div>
            <div class="mes_div ">
                <label for="">客户电话</label><input class="lines" type="tel" placeholder="请输入客户电话" name="phone" id="phone" maxlength = '11'/>
            </div>
            <div class="mes_div ">
                <label for="">小区名称</label><input class="lines" type="text" placeholder="请输入小区名称" name="block_name" id="block_name"/>
            </div>
            <div class="mes_div ">
                <label for="">总价</label><input class="lines" type="tel" placeholder="请输入总价" name="price" id="price"/>万
            </div>
        </div>
    </section>
    <button class="bt_subdyd" id="apply_add">提交</button>


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
    $('.bt_sex span').on('touchend',function(){

        $(this).addClass('choose').siblings().removeClass('choose');
    });

	$("#apply_add").on("click", function () {
		var borrower = $("#borrower").val();
		var sex = $(".choose").attr('rel');
		var phone = $("#phone").val();
		var block_name = $("#block_name").val();
		var price = $("#price").val();

		if(!borrower){
			showError("客户姓名不能为空");
			return false;
		}
		if(!phone){
			showError("客户电话不能为空");
			return false;
		}
		if(!block_name){
			showError("小区名称不能为空");
			return false;
		}
		if(!price){
			showError("房屋价格不能为空");
			return false;
		}

		$.ajax({
			type : 'post',
			url  : '/wap/mortgage/apply/',
			data : {
				borrower : borrower,
				buy_sex : sex,
				borrower_phone : phone,
				block_name : block_name,
				price : price,
				submit_flag : "1"
			},
			dataType :'json',
			success : function(data){
				if(data.result){
					window.location.href="/wap/mortgage/customer/";
				}else{
					showError(data.msg);
				}
			}
		});
	});
</script>
</body>
</html>
